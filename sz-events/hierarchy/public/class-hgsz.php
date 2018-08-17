<?php
/**
 * The public class.
 *
 * @package   HierarchicalEventsForSZ
 * @author    dcavins
 * @license   GPL-2.0+
 * @copyright 2016 David Cavins
 */

/**
 * Plugin class for public functionality.
 *
 * @package   HierarchicalEventsForSZ_Public_Class
 * @author    dcavins
 * @license   GPL-2.0+
 * @copyright 2016 David Cavins
 */
class HGSZ_Public {

	/**
	 *
	 * The current version of the plugin.
	 *
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $version = '1.0.0';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'hierarchical-events-for-sz';

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
		$this->version = hgsz_get_plugin_version();
	}

	/**
	 * Add actions and filters to WordPress/SportsZone hooks.
	 *
	 * @since    1.0.0
	 */
	public function add_action_hooks() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );


		/* Changes to the events directory view. ******************************/
		// Add our templates to SportsZone' template stack.
		add_filter( 'sz_get_template_stack', array( $this, 'add_template_stack'), 10, 1 );

		// Potentially override the events loop template.
		add_filter( 'sz_get_template_part', array( $this, 'filter_events_loop_template'), 10, 3 );

		/*
		 * Adds toggle allowing user to choose whether to restrict events list to top-level events
		 * (working top down), or whether to intermingle.
		 */
		add_action( 'sz_events_directory_event_types', array( $this, 'output_enable_tree_checkbox' ) );

		// Hook sz_has_events filters right before a event directory is rendered.
		add_action( 'sz_before_events_loop', array( $this, 'add_has_event_parse_arg_filters' ) );

		// Unhook sz_has_events filters right after a event directory is rendered.
		add_action( 'sz_after_events_loop', array( $this, 'remove_has_event_parse_arg_filters' ) );

		// Add pagination blocks to the events-loop-tree directory.
		add_action( 'hgsz_before_directory_events_list_tree', 'hgsz_events_loop_pagination_top' );
		add_action( 'hgsz_after_directory_events_list_tree', 'hgsz_events_loop_pagination_bottom' );

		// Add the hierarchy breadcrumb links to a single event's hierarchy screen.
		add_action( 'hgsz_before_events_loop', 'hgsz_single_event_hierarchy_screen_list_header' );

		// Add the "has-children" class to a event item that has children.
		add_filter( 'sz_get_event_class', array( $this, 'filter_event_classes' ) );

		// Handle AJAX requests for subevents.
		add_action( 'wp_ajax_hgsz_get_child_events', array( $this, 'ajax_subevents_response_cb' ) );
		add_action( 'wp_ajax_nopriv_hgsz_get_child_events', array( $this, 'ajax_subevents_response_cb' ) );


		/* Changes to single event behavior. **********************************/
		// Modify event permalinks to reflect hierarchy
		add_filter( 'sz_get_event_permalink', array( $this, 'make_permalink_hierarchical' ), 10, 2 );

		/*
		 * Update the current action and action variables, after the table name is set,
		 * but before SZ Events Component sets the current event, action and variables.
		 * This change allows the URLs to be hierarchically written, but for
		 * SportsZone to know which event is really the current event.
		 */
		add_action( 'sz_events_setup_globals', array( $this, 'reset_action_variables' ) );

		// Add hierarchically related activity to event activity streams.
		add_filter( 'sz_after_has_activities_parse_args', array( $this, 'add_activity_aggregation' ) );


		/* Add user capability checks. ****************************************/
		// Filter user capabilities.
		add_filter( 'sz_user_can', array( $this, 'check_user_caps' ), 10, 5 );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return   string Plugin slug.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		global $wp_version;

		/*
		 * WordPress 4.6 and newer automatically loads language files found at
		 * wp-content/languages/plugins/hierarchical-events-for-sz-LOCALE.mo
		 * This is for older installations of WordPress.
		 */

		if ( version_compare( $wp_version, '4.6', '<' ) ) {
			$domain = $this->plugin_slug;
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
			load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo' );
		}
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles_scripts() {
		if ( sz_is_active( 'events' ) ) {
			// Styles
			if ( is_rtl() ) {
				wp_enqueue_style( $this->plugin_slug . '-plugin-styles-rtl', plugins_url( 'css/public-rtl.css', __FILE__ ), array(), $this->version );
			} else {
				wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
			}

			// Scripts
			wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.min.js', __FILE__ ), array( 'jquery' ), $this->version );
		}
	}


	/* Changes to the events directory view. **********************************/
	/**
	 * Add our templates to SportsZone' template stack.
	 *
	 * @since    1.0.0
	 */
	public function add_template_stack( $templates ) {
		if ( sz_is_current_component( 'events' ) ) {
			$templates[] = plugin_dir_path( __FILE__ ) . 'views/templates';
		}
		return $templates;
	}

	/**
	 * Potentially override the events loop template.
	 *
	 * @since    1.0.0
	 *
	 * @param array  $templates Array of templates located.
	 * @param string $slug      Template part slug requested.
	 * @param string $name      Template part name requested.
	 *
	 * @return array $templates
	 */
	public function filter_events_loop_template( $templates, $slug, $name ) {
		if ( 'events/events-loop' == $slug && hgsz_get_directory_as_tree_setting() ) {
			/*
			 * Add our setting to the front of the array, for the main events
			 * directory and a single event's hierarchy screen.
			 * Make sure this isn't the "my events" view on the main directory
			 * or a user's events screen--those directories must be flat.
			 */
			if ( ! hgsz_is_my_events_view() ) {
				array_unshift( $templates, 'events/events-loop-tree.php' );
			}
		}
		return $templates;
	}

	/**
	 * Add sz_has_events filters right before the directory is rendered.
	 * This helps avoid modifying the "single-event" use of sz_has_event() used
	 * to render the event wrapper.
	 *
	 * @since 1.0.0
 	 */
	public function add_has_event_parse_arg_filters() {
		add_filter( 'sz_after_has_events_parse_args', array( $this, 'filter_has_events_args' ) );
	}

	/**
	 * Remove sz_has_events filters right before the directory is rendered.
	 * This helps avoid modifying the other use of sz_has_event() like
	 * widgets that might appear on a page with a event directory.
	 *
	 * @since 1.0.0
 	 */
	public function remove_has_event_parse_arg_filters() {
		remove_filter( 'sz_after_has_events_parse_args', array( $this, 'filter_has_events_args' ) );
	}

	/**
	 * Adds toggle allowing user to choose whether to restrict events list
	 * to top-level events (working top down), or whether to intermingle.
	 *
 	 * @since 1.0.0
	 *
	 * @return 	string html markup
	 */
	public function output_enable_tree_checkbox() {
		if ( ! hgsz_get_directory_as_tree_setting() ) {
			return;
		}

		// Calculate the checkbox status, based on the cookie value.
		$checked = true;
		if ( isset( $_COOKIE['sz-events-use-tree-view'] ) && 0 == $_COOKIE['sz-events-use-tree-view'] ) {
			$checked = false;
		}

		// Set the label. Check for a saved option for this string first.
		$label = sz_get_option( 'hgsz-directory-enable-tree-view-label' );
		// Next, allow translations to be applied.
		if ( empty( $label ) ) {
			$label = __( 'Include top-level events only.', 'hierarchical-events-for-sz' );
		}

		/**
		 * Filters the "enable tree view" toggle label.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Label to use.
		 */
		$label = apply_filters( 'hgsz_directory_enable_tree_view_label', $label );
		?>
		<li class="hgsz-enable-tree-view-container no-ajax" id="hgsz-enable-tree-view-container" style="float:left;">
			<input id="hgsz-enable-tree-view" name="hgsz-enable-tree-view" type="checkbox" <?php checked( $checked ); ?> class="no-ajax" /> <label for="hgsz-enable-tree-view" class="no-ajax"><?php echo $label; ?></label>
		</li>
		<?php
	}

	/**
	 * Filter has_events parameters to change results on the main directory
	 * and on a single event's hierarchy screen.
	 *
	 * @since 1.0.0
	 *
	 * @param $args Array of parsed arguments.
	 *
	 * @return array
 	 */
	public function filter_has_events_args( $args ) {
		/*
		 * Should we filter this events loop at all?
		 * We only want to filter if adding the hierarchy makes sense.
		 * For instance, if a user searches for events matching "oboes",
		 * they probably want the results, not only events that match "oboes"
		 * AND have a parent_id of 0.
		 * Adding the toggle means that if a user choosing an orderby, we let
		 * them decide whether they want hierarchical results or not.
		 * We never apply hierarchy to a "my events" view, because a user
		 * would have to belong to all ancestor events of a child event they
		 * belong to in order to see that child event.
		 * This is a guess.
		 * Feel free to customize the guess for your site using the
		 * 'hgsz_enable_has_event_args_filter' filter.
		 */
		$use_tree = hgsz_get_directory_as_tree_setting();

		// If the tree view is allowed, has the user set a preference?
		if ( $use_tree && isset( $_COOKIE['sz-events-use-tree-view'] ) ) {
			$use_tree = (bool) $_COOKIE['sz-events-use-tree-view'];
		}

		$force_parent_id = false;
		if ( $use_tree ) {
			// Check that the incoming args are basically defaults.
			if (
					( empty( $args['slug'] ) )
					&& ( empty( $args['include'] ) )
					&& ( empty( $args['parent_id'] ) )
					&& ( empty( $args['scope'] ) || 'personal' != $args['scope'] )
				) {
				$force_parent_id = true;
			}
		}

		/**
		 * Filters whether or not to apply a parent_id to a events loop.
		 *
		 * @since 1.0.0
		 *
		 * @param bool  $force_parent_id Whether to apply a parent_id to a events loop.
		 * @param array $args            Incoming sz_has_events() args.
		 */
		$force_parent_id = apply_filters( 'hgsz_enable_has_event_args_force_parent_id', $force_parent_id, $args );

		// Maybe set the parent_id on the main events directory.
		if ( sz_is_events_directory() && ! hgsz_is_my_events_view() ) {
			if ( $force_parent_id ) {
				$args['parent_id'] = isset( $_REQUEST['parent_id'] ) ? (int) $_REQUEST['parent_id'] : 0;
			} elseif ( empty( $args['parent_id'] ) && isset( $_REQUEST['parent_id'] ) )  {
				/*
				 * Even if a parent ID is not forced, requests may still come
				 * in for subevent loops. Respect a passed parent ID, though.
				 */
				$args['parent_id'] = (int) $_REQUEST['parent_id'];
			}
		}

		// We do have to filter some args on the single event 'hierarchy' screen.
		if ( hgsz_is_hierarchy_screen() ) {
			/*
			 * Change some of the default args to generate a directory-style loop.
			 *
			 * Use the current event id as the parent ID on a single event's
			 * hierarchy screen. (Don't override passed parent IDs, though.)
			 */
			if ( empty( $args['parent_id'] ) ) {
				$args['parent_id'] = isset( $_REQUEST['parent_id'] ) ? (int) $_REQUEST['parent_id'] : sz_get_current_event_id();
			}
			// Unset the type and slug set in sz_has_events() when in a single event.
			$args['type'] = $args['slug'] = null;
			// Set update_admin_cache to true, because this is actually a directory.
			$args['update_admin_cache'] = true;
		}

		return $args;
	}

	/**
	 * Add the "has-children" class to items that have children.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Array of determined classes for the event.
	 *
	 * @return array
 	 */
	public function filter_event_classes( $classes ) {
		if ( $has_children = hgsz_event_has_children( sz_get_event_id(), sz_loggedin_user_id(), 'directory' ) ) {
			$classes[] = 'has-children';
		}
		return $classes;
	}

	/**
	 * Generate the response for the AJAX hgsz_get_child_events action.
	 *
	 * @since 1.0.0
	 *
	 * @return html
	 */
	public function ajax_subevents_response_cb() {
		// Within a single event, prefer the subevents loop template.
		if ( hgsz_is_hierarchy_screen() ) {
			sz_get_template_part( 'events/single/subevents-loop' );
		} else {
			sz_get_template_part( 'events/events-loop' );
		}

		exit;
	}


	/* Changes to single event behavior. **************************************/
	/**
	 * Filter a child event's permalink to take the form
	 * /events/parent-event/child-event.
	 *
	 * @since 1.0.0
	 *
	 * @param string $permalink Permalink for the current event in the loop.
	 * @param object $event     Event object.
	 *
	 * @return string Filtered permalink for the event.
	 */
	public function make_permalink_hierarchical( $permalink, $event = null ) {

 		if ( is_null( $event ) ) {
 			return $permalink;
 		}

		// We only need to filter if this not a top-level event.
		if ( $event->parent_id != 0 ) {
			$event_path = hgsz_build_hierarchical_slug( $event->id );
			$permalink  = trailingslashit( sz_get_events_directory_permalink() . $event_path );
		}
		return $permalink;
	}

	/**
	 * Filter $sz->current_action and $sz->action_variables before the single
	 * event details are set up in the Single Event Globals section of
	 * SZ_Events_Component::setup_globals() to ignore the hierarchical
	 * piece of the URL for child events.
	 *
	 * @since 1.0.0
	 *
	 */
	public function reset_action_variables() {
		if ( sz_is_events_component() ) {
			$sz = sportszone();

			// We're looking for event slugs masquerading as action variables.
			$action_variables = sz_action_variables();
			if ( ! $action_variables || ! is_array( $action_variables ) ) {
				return;
			}

			// The current event slug is the 'sz_current_action'.
			$parent_id = events_get_id( sz_current_action() );

			/*
			 * The Single Event Globals section of SZ_Events_Component::setup_globals()
			 * uses the current action to set up the current event. Pull found
			 * event slugs out of the action variables array.
			 */
			foreach ( $action_variables as $maybe_slug ) {
				if ( $parent_id = hgsz_child_event_exists( $maybe_slug, $parent_id ) ) {
					$sz->current_action = array_shift( $sz->action_variables );
				} else {
					// If we've gotten into real action variables, stop.
					break;
				}
			}
		}
	}

	/**
	 * Filter has_activities parameters to add hierarchically related events of
	 * the current event that user has access to.
	 *
	 * @since 1.0.0
	 *
	 * @param $args Array of parsed arguments.
	 *
	 * @return array
	 */
	public function add_activity_aggregation( $args ) {
		// Only fire on event activity streams.
		if ( $args['object'] != 'events' ) {
			return $args;
		}

		$event_id = sz_get_current_event_id();

		// Check if this event is set to aggregate child event activity.
		$include_activity = hgsz_event_include_hierarchical_activity( $event_id );

		switch ( $include_activity ) {
			case 'include-from-both':
				$parents = hgsz_get_ancestor_event_ids( $event_id, sz_loggedin_user_id(), 'activity' );
				$children  = hgsz_get_descendent_events( $event_id, sz_loggedin_user_id(), 'activity' );
				$child_ids = wp_list_pluck( $children, 'id' );
				$include   = array_merge( array( $event_id ), $parents, $child_ids );
				break;
			case 'include-from-parents':
				$parents = hgsz_get_ancestor_event_ids( $event_id, sz_loggedin_user_id(), 'activity' );
				// Add the parent IDs to the main event ID.
				$include = array_merge( array( $event_id ), $parents );
				break;
			case 'include-from-children':
				$children  = hgsz_get_descendent_events( $event_id, sz_loggedin_user_id(), 'activity' );
				$child_ids = wp_list_pluck( $children, 'id' );
				// Add the child IDs to the main event ID.
				$include   = array_merge( array( $event_id ), $child_ids );
				break;
			case 'include-from-none':
			default:
				// Do nothing.
				$include = false;
				break;
		}

		if ( ! empty( $include ) ) {
			$args['primary_id'] = $include;
		}

		return $args;
	}


	/* Add user capability checks. ********************************************/
	/**
	 * Check for user capabilities specific to this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $retval     Whether or not the current user has the capability.
	 * @param int    $user_id
	 * @param string $capability The capability being checked for.
	 * @param int    $site_id    Site ID. Defaults to the SZ root blog.
	 * @param array  $args       Array of extra arguments passed.
	 *
	 * @return bool
	 */
	public function check_user_caps( $retval, $user_id, $capability, $site_id, $args ) {
		if ( 'hgsz_change_include_activity' == $capability ) {

			$global_setting = hgsz_get_global_activity_enforce_setting();

			$retval = false;
			switch ( $global_setting ) {
				case 'site-admins':
					if ( sz_user_can( $user_id, 'sz_moderate' ) ) {
						$retval = true;
					}
					break;
				case 'event-admins':
					if ( sz_user_can( $user_id, 'sz_moderate' )
						 || events_is_user_admin( $user_id, sz_get_current_event_id() ) ) {
						$retval = true;
					}
					break;
				case 'strict':
				default:
					$retval = false;
					break;
			}

		}

		if ( 'create_subevents' == $capability ) {
			// We need to know which event is in question.
			if ( empty( $args['event_id'] ) ) {
				return false;
			}

			// Site admins can do the hokey pokey.
			if ( sz_user_can( $user_id, 'sz_moderate' ) ) {
				$retval = true;
			} else {
				$event_id = (int) $args['event_id'];

				// Possible settings for the event meta setting 'allowed_subevent_creators'
				$creator_setting = hgsz_get_allowed_subevent_creators( $event_id );
				switch ( $creator_setting ) {
					case 'admin' :
						$retval = events_is_user_admin( $user_id, $event_id );
						break;

					case 'mod' :
						$retval = ( events_is_user_mod( $user_id, $event_id )
									|| events_is_user_admin( $user_id, $event_id ) );
						break;

					case 'member' :
						$retval = events_is_user_member( $user_id, $event_id );
						break;

					case 'loggedin' :
						$retval = is_user_logged_in();
						break;

					case 'noone' :
					default :
						$retval = false;
						break;
				}
			}
		}

		return $retval;
	}

}
