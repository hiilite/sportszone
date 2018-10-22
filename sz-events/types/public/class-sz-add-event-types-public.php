<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    SZ_Add_Event_Types
 * @subpackage SZ_Add_Event_Types/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    SZ_Add_Event_Types
 * @subpackage SZ_Add_Event_Types/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class SZ_Add_Event_Types_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SZ_Add_Event_Types_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SZ_Add_Event_Types_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sz-add-event-types-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SZ_Add_Event_Types_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SZ_Add_Event_Types_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sz-add-event-types-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			$this->plugin_name,
			'szet_front_js_object',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Change the event search template.
	 *
	 * @param string $search_form_html The seach form html.
	 * @since    1.0.0
	 */
	public function szet_modified_event_search_form( $search_form_html ) {
		global $sz_evt_types;
		if ( isset( $sz_evt_types->event_type_search_template ) && 'textbox' === $sz_evt_types->event_type_search_template ) {
			$search_form_html = $search_form_html;
		} else {
			$event_types       = sz_events_get_event_types( array(), 'objects' );
			$event_select_html = '';
			if ( ! empty( $event_types ) && is_array( $event_types ) ) {
				$event_select_html .= '<div class="szgt-events-search-event-type"><select class="szgt-events-search-event-type">';
				$event_select_html .= '<option value="">' . __( 'All Types', 'sz-add-event-types' ) . '</option>';
				foreach ( $event_types as $event_type_slug => $event_type ) {
					$event_select_html .= '<option value="' . $event_type_slug . '">' . $event_type->labels['name'] . '</option>';
				}
				$event_select_html .= '</select></div>';
			}

			if ( isset( $sz_evt_types->event_type_search_template ) && 'both' === $sz_evt_types->event_type_search_template ) {
				$search_html       = $search_form_html;
				$search_form_html  = '';
				$search_form_html .= $event_select_html;
				$search_form_html .= $search_html;
			} else {
				$search_form_html = $event_select_html;
			}
		}
		return $search_form_html;
	}

	/**
	 * Change the event search template.
	 *
	 * @param string $sz_ajax_querystring The seach form html.
	 * @param string $object The seach form html.
	 * @since    1.0.0
	 */
	public function szet_alter_sz_ajax_querystring( $sz_ajax_querystring, $object ) {
		global $sz;
		$object       = filter_input( INPUT_POST, 'object' );
		$query_extras = filter_input( INPUT_POST, 'extras' );
		$scope        = filter_input( INPUT_POST, 'scope' );
		if ( ( null !== $object ) && ( 'events' === $object ) && ( null !== $query_extras ) && ! empty( $query_extras ) ) {
			parse_str( $query_extras, $extras );
			if ( ! empty( $extras ) && is_array( $extras ) ) {
				if ( ! empty( $extras['event_type'] ) ) {
					$sz_ajax_querystring = add_query_arg( 'event_type', $extras['event_type'], $sz_ajax_querystring );
					if ( ! empty( $scope ) && 'all' !== $scope ) {
						if ( 'all' !== $extras['event_type'] && ! empty( $extras['event_type'] ) ) {
							$allevents = events_get_events();
							if ( ! empty( $allevents ) && array_key_exists( 'events', $allevents ) ) {
								$include_events = array();
								$exclude_events = array();
								foreach ( $allevents['events'] as $event ) {
									$event_type = (array) sz_events_get_event_type( $event->id, false );
									if ( ! empty( $event_type ) && is_array( $event_type ) ) {
										if ( in_array( $extras['event_type'], $event_type, true ) && in_array( $scope, $event_type, true ) ) {
											array_push( $include_events, $event->id );
										}
									}
									array_push( $exclude_events, $event->id );
								}

								if ( ! empty( $include_events ) ) {
									$include_events      = implode( ',', $include_events );
									$sz_ajax_querystring = add_query_arg( 'include', $include_events, $sz_ajax_querystring );
								} elseif ( ! empty( $exclude_events ) ) {
									$exclude_events      = implode( ',', $exclude_events );
									$sz_ajax_querystring = add_query_arg( 'exclude', $exclude_events, $sz_ajax_querystring );
								}
							}
						}
					}
				}
			}
		}
		return $sz_ajax_querystring;
	}


	/**
	 * Ajax served to search events
	 */
	public function szet_search_events() {
		if ( ( null !== filter_input( INPUT_POST, 'action' ) ) && 'szet_search_events' === filter_input( INPUT_POST, 'action' ) ) {
			$_POST['object'] = 'events';
			sz_legacy_theme_object_template_loader();
			die;
		}
	}

	/**
	 * Add event type tabs.
	 */
	public function bb_display_directory_tabs() {
		$display_event_types = get_site_option( 'szet_type_display_settings' );
		$event_types         = sz_events_get_event_types( array(), 'objects' );
		// Loop in event types to build the tabs.
		if ( ! empty( $display_event_types ) && is_array( $display_event_types ) ) {
			foreach ( $event_types as $key => $event_type ) :
				if ( in_array( $key, $display_event_types, true ) ) {
					?>
			<li id="events-<?php echo esc_attr( $event_type->name ); ?>" class="szgt-type-tab">
				<a href="<?php sz_events_directory_permalink(); ?>"><?php printf( '%s <span>%d</span>', esc_attr( $event_type->labels['name'] ), esc_attr( $this->bb_count_event_types( $event_type->name ) ) ); ?></a>
			</li>
				<?php
				}
			endforeach;
		}
	}
	
	/**
	 * Add event type tabs.
	 */
	public function sz_display_directory_select() {
		$display_event_types = get_site_option( 'szet_type_display_settings' );
		$event_types         = sz_events_get_event_types( array(), 'objects' );
		
		$page = explode("/",$_SERVER['REQUEST_URI']);
		
		if($page[2] == 'type') {
			$type = $page[3];
		}
		else {
			$type = 'all';
		}
		
		?>
		<!--form id="events-types-filter" method="post" action=""-->
			<div class="select-wrap">
				<select id="events-types-select" name="type">
					<option value="<?php echo _('all'); ?>" <?php echo ($type == 'all' ? 'selected' : ''); ?>><?php echo _('All Events'); ?></option>
		<?php
		if ( ! empty( $display_event_types ) && is_array( $display_event_types ) ) {
			foreach ( $event_types as $key => $event_type ) :
				if ( in_array( $key, $display_event_types, true ) ) {
					?>
			<option value="<?php echo esc_attr( $event_type->name ); ?>" <?php echo ($type == esc_attr( $event_type->name ) ? 'selected' : ''); ?>>
				<?php printf( '%s', esc_attr( $event_type->labels['name'] ) ); ?>
			</option>
				<?php
				}
			endforeach;
		}
		?>
		</select>
		<span class="select-arrow" aria-hidden="true"></span>
		</div>
		<!--/form-->
		<?php
	}

	/**
	 * Get event count of event type tabs events.
	 *
	 * @param string $event_type The event type.
	 * @param string $taxonomy The event taxonomy.
	 */
	public function bb_count_event_types( $event_type = '', $taxonomy = 'sz_event_type' ) {
		global $wpdb;
		$event_types = sz_events_get_event_types();
		if ( empty( $event_type ) || empty( $event_types[ $event_type ] ) ) {
			return false;
		}
		$count_types = wp_cache_get( 'bpex_count_event_types', 'using_gt_sz_event_type' );
		if ( ! $count_types ) {
			if ( ! sz_is_root_blog() ) {
				switch_to_blog( sz_get_root_blog_id() );
			}
			$sql         = array(
				'select' => "SELECT t.slug, tt.count FROM {$wpdb->term_taxonomy} tt LEFT JOIN {$wpdb->terms} t",
				'on'     => 'ON tt.term_id = t.term_id',
				'where'  => $wpdb->prepare( 'WHERE tt.taxonomy = %s', $taxonomy ),
			);
			$count_types = $wpdb->get_results( join( ' ', $sql ) );
			wp_cache_set( 'bpex_count_event_types', $count_types, 'using_gt_sz_event_type' );
			restore_current_blog();
		}
		$type_count = wp_filter_object_list( $count_types, array( 'slug' => $event_type ), 'and', 'count' );
		$type_count = array_values( $type_count );
		if ( empty( $type_count ) ) {
			return 0;
		}
		return (int) $type_count[0];
	}

	/**
	 * Get event type args.
	 *
	 * @param array $args The event type.
	 */
	public function bb_set_has_events_type_arg( $args = array() ) {
		$display_event_types = get_site_option( 'szet_type_display_settings' );
		if ( ! empty( $display_event_types ) && is_array( $display_event_types ) ) {
			// Get event types to check scope.
			$event_types = sz_events_get_event_types();
			// Set the event type arg if scope match one of the registered event type.
			if ( ! empty( $args['scope'] ) && ! empty( $event_types[ $args['scope'] ] ) ) {
				$args['event_type'] = $args['scope'];
			}
		}
		return $args;
	}

	/**
	 * Display event type.
	 *
	 * @param string $event_id The event id.
	 */
	public function bb_event_directory_show_event_type( $event_id = null ) {
		if ( empty( $event_id ) ) {
			$event_id = sz_get_event_id();
		}
		// Event directory.
		if ( sz_is_active( 'events' ) && sz_is_events_directory() ) {
			// Passing false means supporting multiple event types.
			$event_type = (array) sz_events_get_event_type( $event_id, false );
			$sep        = '&ndash;';
			foreach ( $event_type as $type ) {
				$obj = sz_events_get_event_type_object( $type );
				// Event type name/description.
				if ( ! empty( $obj->description ) ) {
					printf( '<div class="dir-desc-' . esc_attr( $obj->labels['singular_name'] ) . '"><span class="dir-desc-span-name">%1$s</span><span class="dir-desc-span-sep">%2$s</span><span class="dir-desc-span-desc">%3$s</span>.', esc_attr( $obj->labels['singular_name'] ), esc_attr( $sep ), esc_html( $obj->description ) . '</div>' );
				}
			}
		}
	}
}
