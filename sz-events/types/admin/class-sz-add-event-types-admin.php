<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    SZ_Add_Event_Types
 * @subpackage SZ_Add_Event_Types/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    SZ_Add_Event_Types
 * @subpackage SZ_Add_Event_Types/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class SZ_Add_Event_Types_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->szet_save_general_settings();
		$this->szet_save_event_types();
		$this->szet_save_type_display_settings();
		$this->szet_save_event_type_search_settings();

	}

	/**
	 * Register the stylesheets for the admin area.
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
		if ( strpos( filter_input( INPUT_SERVER, 'REQUEST_URI' ), 'sz-add-event-types' ) !== false ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sz-add-event-types-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
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
		if ( strpos( filter_input( INPUT_SERVER, 'REQUEST_URI' ), 'sz-add-event-types' ) !== false ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sz-add-event-types-admin.js', array( 'jquery' ), $this->version, false );
		}
	}

	/**
	 * Register a submenu to handle event types
	 *
	 * @since    1.0.0
	 */
	public function szet_add_submenu_page() {
		add_submenu_page( 'sz-events', __( 'Event Types Settings', 'sportszone' ), __( 'Types Settings', 'sportszone' ), 'manage_options', $this->plugin_name, array( $this, 'szet_admin_settings_page' ));
	}
	/**
	 * Actions performed to create a submenu page content
	 */
	public function szet_admin_settings_page() {
		$tab = ( filter_input( INPUT_GET, 'tab' ) !== null ) ? filter_input( INPUT_GET, 'tab' ) : $this->plugin_name;
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Event Types - SportsZone', 'sz-add-event-types' ); ?></h2>
			<?php $this->szet_plugin_settings_tabs(); ?>
			<?php do_settings_sections( $tab ); ?>
		</div>
		<?php
	}

	/**
	 * Actions performed to create tabs on the sub menu page
	 */
	public function szet_plugin_settings_tabs() {
		$current_tab = ( filter_input( INPUT_GET, 'tab' ) !== null ) ? filter_input( INPUT_GET, 'tab' ) : $this->plugin_name;
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab === $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . esc_attr( $active ) . '" id="' . esc_attr( $tab_key ) . '-tab" href="?page=' . esc_attr( $this->plugin_name ) . '&tab=' . esc_attr( $tab_key ) . '">' . esc_attr( $tab_caption ) . '</a>';
		}
		echo '</h2>';
	}

	/**
	 * General Tab
	 */
	public function szet_register_general_settings() {
		$this->plugin_settings_tabs['szgt-general'] = __( 'General', 'sz-add-event-types' );
		register_setting( 'szgt-general', 'szgt-general' );
		add_settings_section( 'szgt-general-section', ' ', array( &$this, 'szet_general_settings_content' ), 'szgt-general' );
	}

	/**
	 * General Tab Content
	 */
	public function szet_general_settings_content() {
		if ( file_exists( dirname( __FILE__ ) . '/settings/szgt-general-settings.php' ) ) {
			require_once dirname( __FILE__ ) . '/settings/szgt-general-settings.php';
		}
	}

	/**
	 * Event Types Listing Tab
	 */
	public function szet_register_event_types_listing_settings() {
		$this->plugin_settings_tabs['sz-add-event-types'] = __( 'Event Types', 'sz-add-event-types' );
		register_setting( 'sz-add-event-types', 'sz-add-event-types' );
		add_settings_section( 'sz-add-event-types-section', ' ', array( &$this, 'szet_event_types_listing_settings_content' ), 'sz-add-event-types' );
	}

	/**
	 * Event Types Listing Tab Content
	 */
	public function szet_event_types_listing_settings_content() {
		if ( file_exists( dirname( __FILE__ ) . '/settings/szgt-event-types-listing-settings.php' ) ) {
			require_once dirname( __FILE__ ) . '/settings/szgt-event-types-listing-settings.php';
		}
	}

	/**
	 * Event Types Search Tab
	 */
	public function szet_register_event_type_search_settings() {
		$this->plugin_settings_tabs['szgt-search'] = __( 'Event Type Search', 'sz-add-event-types' );
		register_setting( 'szgt-search', 'szgt-search' );
		add_settings_section( 'szgt-search-enabled-section', ' ', array( &$this, 'szet_event_type_search_settings_content' ), 'szgt-search' );
	}

	/**
	 * Event Types Search Tab Content
	 */
	public function szet_event_type_search_settings_content() {
		if ( file_exists( dirname( __FILE__ ) . '/settings/szgt-event-type-search-settings.php' ) ) {
			require_once dirname( __FILE__ ) . '/settings/szgt-event-type-search-settings.php';
		}
	}

	/**
	 * Support Tab
	 */
	public function szet_register_support_settings() {
		$this->plugin_settings_tabs['szgt-support'] = __( 'Support', 'sz-add-event-types' );
		register_setting( 'szgt-support', 'szgt-support' );
		add_settings_section( 'szgt-support-section', ' ', array( &$this, 'szet_support_settings_content' ), 'szgt-support' );
	}

	/**
	 * Support Tab Content
	 */
	public function szet_support_settings_content() {
		if ( file_exists( dirname( __FILE__ ) . '/settings/szgt-support-settings.php' ) ) {
			require_once dirname( __FILE__ ) . '/settings/szgt-support-settings.php';
		}
	}

	/**
	 * Event Type Display Setting Tab
	 */
	public function szet_register_type_display_settings() {
		$this->plugin_settings_tabs['szgt-type-display'] = __( 'Display Event Types', 'sz-add-event-types' );
		register_setting( 'szgt-type-display', 'szgt-type-display' );
		add_settings_section( 'szgt-type-display-section', ' ', array( &$this, 'szet_type_display_settings_content' ), 'szgt-type-display' );
	}

	/**
	 * Event Type Display Setting Tab Content
	 */
	public function szet_type_display_settings_content() {
		if ( file_exists( dirname( __FILE__ ) . '/settings/szgt-event-type-display-settings.php' ) ) {
			require_once dirname( __FILE__ ) . '/settings/szgt-event-type-display-settings.php';
		}
	}

	/**
	 * Save Plugin General Settings
	 */
	public function szet_save_general_settings() {
		global $allowedposttags;
		if ( ( filter_input( INPUT_POST, 'szet_submit_general_settings' ) !== null ) && wp_verify_nonce( filter_input( INPUT_POST, 'szgt-general-settings-nonce' ), 'szgt' ) ) {

			$event_types_pre_selected = 'no';
			if ( null !== filter_input( INPUT_POST, 'szgt-event-types-pre-selected' ) ) {
				$event_types_pre_selected = 'yes';
			}

			$event_type_search_enabled = 'no';
			if ( null !== filter_input( INPUT_POST, 'szgt-event-types-search-enabled' ) ) {
				$event_type_search_enabled = 'yes';
			}

			$admin_settings = array(
				'event_types_pre_selected'  => $event_types_pre_selected,
				'event_type_search_enabled' => $event_type_search_enabled,
			);

			update_site_option( 'szet_general_settings', $admin_settings );
			$success_msg  = "<div class='notice updated is-dismissible' id='message'>";
			$success_msg .= '<p>' . __( 'Settings Saved.', 'sz-add-event-types' ) . '</p>';
			$success_msg .= '</div>';
			wp_kses( $success_msg, $allowedposttags );
		}
	}

	/**
	 * Save Plugin General Settings
	 */
	public function szet_save_type_display_settings() {
		global $allowedposttags;
		$type_arr = array();
		if ( null !== filter_input( INPUT_POST, 'szet_submit_event_type_display_settings' ) ) {
			if ( isset( $_POST['szet_event_type_display'] ) ) {
				$type_arr = array_map( 'sanitize_text_field', wp_unslash( $_POST['szet_event_type_display'] ) );
			}
			update_site_option( 'szet_type_display_settings', $type_arr );
			$success_msg  = "<div class='notice updated is-dismissible' id='message'>";
			$success_msg .= '<p>' . __( 'Settings Saved.', 'sz-add-event-types' ) . '</p>';
			$success_msg .= '</div>';
			wp_kses( $success_msg, $allowedposttags );
		}
	}

	/**
	 * Save Event Types that are added
	 */
	public function szet_save_event_types() {
		global $allowedposttags;
		if ( ( filter_input( INPUT_POST, 'szgt-add-event-type' ) !== null ) && wp_verify_nonce( filter_input( INPUT_POST, 'szgt-add-event-types-nonce' ), 'szgt-event-types' ) ) {
			$event_type_name = sanitize_text_field( filter_input( INPUT_POST, 'event-type-name' ) );

			if ( filter_input( INPUT_POST, 'event-type-slug' ) !== null ) {
				$event_type_slug = sanitize_text_field( filter_input( INPUT_POST, 'event-type-slug' ) );
			} else {
				$event_type_slug = str_replace( ' ', '', strtolower( $event_type_name ) );
			}

			$event_type_desc = '';
			if ( filter_input( INPUT_POST, 'event-type-desc' ) !== null ) {
				$event_type_desc = sanitize_text_field( filter_input( INPUT_POST, 'event-type-desc' ) );
			}

			$event_types = get_site_option( 'szet_event_types' );
			if ( ! is_array( $event_types ) ) {
				$event_types = array();
			}

			$flag = 0;
			if ( ! empty( $event_types ) ) {
				foreach ( $event_types as $key => $event_type ) {
					if ( $event_type_slug === $event_type['slug'] ) {
						$flag = 1;
					}
				}
			}

			if ( 0 === $flag ) {
				$event_types[] = array(
					'name' => $event_type_name,
					'slug' => $event_type_slug,
					'desc' => $event_type_desc,
				);
				update_site_option( 'szet_event_types', $event_types );
				$success_msg  = "<div class='notice updated is-dismissible' id='message'>";
				$success_msg .= '<p>' . __( 'Event Type Added!', 'sz-add-event-types' ) . '</p>';
				$success_msg .= '</div>';
				wp_kses( $success_msg, $allowedposttags );
			} else {
				$error_msg  = "<div class='notice notice-error is-dismissible' id='message'>";
				$error_msg .= '<p>' . __( 'Event Type With This Name/Slug Already Exists!', 'sz-add-event-types' ) . '</p>';
				$error_msg .= '</div>';
				wp_kses( $error_msg, $allowedposttags );
			}
		}
	}

	/**
	 * Ajax served to delete the event type
	 */
	public function szet_delete_event_type() {
		if ( ( filter_input( INPUT_POST, 'action' ) !== null ) && 'szet_delete_event_type' === filter_input( INPUT_POST, 'action' ) ) {
			$slug = sanitize_text_field( filter_input( INPUT_POST, 'slug' ) );

			$event_types = get_site_option( 'szet_event_types' );
			foreach ( $event_types as $key => $event_type ) {
				if ( $slug === $event_type['slug'] ) {
					$key_to_unset = $key;
					break;
				}
			}
			unset( $event_types[ $key_to_unset ] );
			if ( empty( $event_types ) ) {
				delete_option( 'szet_event_types' );
			} else {
				update_site_option( 'szet_event_types', $event_types );
			}

			$response = array(
				'message' => __( 'Event Type Deleted.', 'sz-add-event-types' ),
			);
			wp_send_json_success( $response );
			die;
		}
	}

	/**
	 * Ajax served to update the event type
	 */
	public function szet_update_event_type() {
		if ( ( filter_input( INPUT_POST, 'action' ) !== null ) && 'szet_update_event_type' === filter_input( INPUT_POST, 'action' ) ) {
			$new_name = sanitize_text_field( filter_input( INPUT_POST, 'new_name' ) );
			$old_slug = sanitize_text_field( filter_input( INPUT_POST, 'old_slug' ) );

			$event_types = get_site_option( 'szet_event_types' );
			foreach ( $event_types as $key => $event_type ) {
				if ( $old_slug === $event_type['slug'] ) {
					$key_to_update = $key;
					break;
				}
			}

			$new_event_type = array(
				'name' => $new_name,
				'slug' => sanitize_text_field( filter_input( INPUT_POST, 'new_slug' ) ),
				'desc' => sanitize_text_field( filter_input( INPUT_POST, 'new_desc' ) ),
			);

			$event_types[ $key_to_update ] = $new_event_type;
			update_site_option( 'szet_event_types', $event_types );

			$response = array(
				'message' => __( 'Event Type Updated.', 'sz-add-event-types' ),
			);
			wp_send_json_success( $response );
			die;
		}
	}

	/**
	 * Register all saved event types
	 */
	public function szet_register_event_types() {
		global $sz_evt_types;
		$create_screen_checked = false;
		if ( isset( $sz_evt_types->event_types_pre_selected ) ) {
			if ( 'yes' === $sz_evt_types->event_types_pre_selected ) {
				$create_screen_checked = true;
			}
		}
		$saved_event_types = $sz_evt_types->event_types;
		$event_types       = sz_events_get_event_types();
		if ( ! empty( $saved_event_types ) ) {
			foreach ( $saved_event_types as $key => $saved_event_type ) {
				$slug = $saved_event_type['slug'];
				$name = $saved_event_type['name'];
				$desc = $saved_event_type['desc'];
				if ( ! in_array( $slug, $event_types, true ) ) {
					$temp = array(
						'labels'                => array(
							'name'          => $name,
							'singular_name' => $name,
						),
						'has_directory'         => strtolower( $name ),
						'show_in_create_screen' => true,
						'show_in_list'          => true,
						'description'           => $desc,
						'create_screen_checked' => $create_screen_checked,
					);
					sz_events_register_event_type( $name, $temp );
				}
			}
		}
	}

	/**
	 * Save Plugin Event Type Search Settings
	 */
	public function szet_save_event_type_search_settings() {
		global $allowedposttags;
		if ( ( filter_input( INPUT_POST, 'szet_submit_event_type_search_settings' ) !== null ) && wp_verify_nonce( filter_input( INPUT_POST, 'szgt-event-type-search-settings-nonce' ), 'szgt-search-settings' ) ) {
			$event_type_search_template = 'both';
			if ( null !== filter_input( INPUT_POST, 'szgt-event-type-search-template' ) ) {
				$event_type_search_template = sanitize_text_field( filter_input( INPUT_POST, 'szgt-event-type-search-template' ) );
			}

			$admin_settings = array(
				'event_type_search_template' => $event_type_search_template,
			);

			update_site_option( 'szet_event_type_search_settings', $admin_settings );
			$success_msg  = "<div class='notice updated is-dismissible' id='message'>";
			$success_msg .= '<p>' . __( 'Settings Saved.', 'sz-add-event-types' ) . '</p>';
			$success_msg .= '</div>';
			echo wp_kses( $success_msg, $allowedposttags );
		}
	}
}
