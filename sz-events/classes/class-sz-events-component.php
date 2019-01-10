<?php
/**
 * SportsZone Events Component Class.
 *
 * @package SportsZone
 * @subpackage EventsLoader
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Creates our Events component.
 *
 * @since 1.5.0
 */
class SZ_Events_Component extends SZ_Component {

	/**
	 * Auto-join event when non event member performs event activity.
	 *
	 * @since 1.5.0
	 * @var bool
	 */
	public $auto_join;

	/**
	 * The event being currently accessed.
	 *
	 * @since 1.5.0
	 * @var SZ_Events_Event
	 */
	public $current_event;

	/**
	 * Default event extension.
	 *
	 * @since 1.6.0
	 * @todo Is this used anywhere? Is this a duplicate of $default_extension?
	 * @var string
	 */
	var $default_component;

	/**
	 * Default event extension.
	 *
	 * @since 1.6.0
	 * @var string
	 */
	public $default_extension;

	/**
	 * Illegal event names/slugs.
	 *
	 * @since 1.5.0
	 * @var array
	 */
	public $forbidden_names;

	/**
	 * Event creation/edit steps (e.g. Details, Settings, Avatar, Invites).
	 *
	 * @since 1.5.0
	 * @var array
	 */
	public $event_creation_steps;

	/**
	 * Types of event statuses (Public, Private, Hidden).
	 *
	 * @since 1.5.0
	 * @var array
	 */
	public $valid_status;

	/**
	 * Event types.
	 *
	 * @see sz_events_register_event_type()
	 *
	 * @since 2.6.0
	 * @var array
	 */
	public $types = array();

	/**
	 * Current directory event type.
	 *
	 * @see events_directory_events_setup()
	 *
	 * @since 2.7.0
	 * @var string
	 */
	public $current_directory_type = '';

	/**
	 * Start the events component creation process.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		parent::start(
			'events',
			_x( 'User Events', 'Event screen page <title>', 'sportszone' ),
			sportszone()->plugin_dir,
			array(
				'adminbar_myaccount_order' => 70,
				'search_query_arg' => 'events_search',
			)
		);
	}

	/**
	 * Include Events component files.
	 *
	 * @since 1.5.0
	 *
	 * @see SZ_Component::includes() for a description of arguments.
	 *
	 * @param array $includes See SZ_Component::includes() for a description.
	 */
	public function includes( $includes = array() ) {
		$includes = array(
			'cache',
			'filters',
			'widgets',
			'template',
			'adminbar',
			'functions',
			'notifications',
			'hierarchy',
			'types',
			'match'
		);

		// Conditional includes.
		if ( sz_is_active( 'activity' ) ) {
			$includes[] = 'activity';
		}
		if ( is_admin() ) {
			$includes[] = 'admin';
		}

		parent::includes( $includes );
	}

	/**
	 * Late includes method.
	 *
	 * Only load up certain code when on specific pages.
	 *
	 * @since 3.0.0
	 */
	public function late_includes() {
		// Bail if PHPUnit is running.
		if ( defined( 'SZ_TESTS_DIR' ) ) {
			return;
		}

		if ( sz_is_events_component() ) {
			// Authenticated actions.
			if ( is_user_logged_in() &&
				in_array( sz_current_action(), array( 'create', 'join', 'leave-event' ), true )
			) {
				require $this->path . 'sz-events/actions/' . sz_current_action() . '.php';
			}

			// Actions - RSS feed handler.
			if ( sz_is_active( 'activity' ) && sz_is_current_action( 'feed' ) ) {
				require $this->path . 'sz-events/actions/feed.php';
			}

			// Actions - Random event handler.
			if ( isset( $_GET['random-event'] ) ) {
				require $this->path . 'sz-events/actions/random.php';
			}

			// Screens - Directory.
			if ( sz_is_events_directory() ) {
				require $this->path . 'sz-events/screens/directory.php';
			}

			// Screens - User profile integration.
			if ( sz_is_user() ) {
				require $this->path . 'sz-events/screens/user/my-events.php';

				if ( sz_is_current_action( 'invites' ) ) {
					require $this->path . 'sz-events/screens/user/invites.php';
				}
			}

			// Single event.
			if ( sz_is_event() ) {
				// Actions - Access protection.
				require $this->path . 'sz-events/actions/access.php';

				// Public nav items.
				if ( in_array( sz_current_action(), array( 'home', 'request-membership', 'activity', 'members', 'send-invites' ), true ) ) {
					require $this->path . 'sz-events/screens/single/' . sz_current_action() . '.php';
				}

				// Admin nav items.
				if ( sz_is_item_admin() && is_user_logged_in() ) {
					require $this->path . 'sz-events/screens/single/admin.php';

					if ( in_array( sz_get_event_current_admin_tab(), array( 'edit-details', 'event-settings', 'event-avatar', 'event-cover-image', 'manage-members', 'membership-requests', 'delete-event' ), true ) ) {
						require $this->path . 'sz-events/screens/single/admin/' . sz_get_event_current_admin_tab() . '.php';
					}
				}
			}
			
			

			// Theme compatibility.
			new SZ_Events_Theme_Compat();
		}
	}

	/**
	 * Set up component global data.
	 *
	 * The SZ_EVENTS_SLUG constant is deprecated, and only used here for
	 * backwards compatibility.
	 *
	 * @since 1.5.0
	 *
	 * @see SZ_Component::setup_globals() for a description of arguments.
	 *
	 * @param array $args See SZ_Component::setup_globals() for a description.
	 */
	public function setup_globals( $args = array() ) {
		$sz = sportszone();

		// Define a slug, if necessary.
		if ( ! defined( 'SZ_EVENTS_SLUG' ) ) {
			define( 'SZ_EVENTS_SLUG', $this->id );
		}

		// Global tables for events component.
		$global_tables = array(
			'table_name'           => $sz->table_prefix . 'sz_events',
			'table_name_members'   => $sz->table_prefix . 'sz_events_members',
			'table_name_eventmeta' => $sz->table_prefix . 'sz_events_eventmeta'
		);

		// Metadata tables for events component.
		$meta_tables = array(
			'event' => $sz->table_prefix . 'sz_events_eventmeta',
		);

		// Fetch the default directory title.
		$default_directory_titles = sz_core_get_directory_page_default_titles();
		$default_directory_title  = $default_directory_titles[$this->id];

		// All globals for events component.
		// Note that global_tables is included in this array.
		$args = array(
			'slug'                  => SZ_EVENTS_SLUG,
			'root_slug'             => isset( $sz->pages->events->slug ) ? $sz->pages->events->slug : SZ_EVENTS_SLUG,
			'has_directory'         => true,
			'directory_title'       => isset( $sz->pages->events->title ) ? $sz->pages->events->title : $default_directory_title,
			'notification_callback' => 'events_format_notifications',
			'search_string'         => _x( 'Search Events...', 'Component directory search', 'sportszone' ),
			'global_tables'         => $global_tables,
			'meta_tables'           => $meta_tables,
		);

		parent::setup_globals( $args );

		/* Single Event Globals **********************************************/

		// Are we viewing a single event?
		if ( sz_is_events_component()
			&& ( ( $event_id = SZ_Events_Event::event_exists( sz_current_action() ) )
				|| ( $event_id = SZ_Events_Event::get_id_by_previous_slug( sz_current_action() ) ) )
			) {
			$sz->is_single_item  = true;

			/**
			 * Filters the current PHP Class being used.
			 *
			 * @since 1.5.0
			 *
			 * @param string $value Name of the class being used.
			 */
			$current_event_class = apply_filters( 'sz_events_current_event_class', 'SZ_Events_Event' );

			if ( $current_event_class == 'SZ_Events_Event' ) {
				$this->current_event = events_get_event( $event_id );

			} else {

				/**
				 * Filters the current event object being instantiated from previous filter.
				 *
				 * @since 1.5.0
				 *
				 * @param object $value Newly instantiated object for the event.
				 */
				$this->current_event = apply_filters( 'sz_events_current_event_object', new $current_event_class( $event_id ) );
			}

			// When in a single event, the first action is bumped down one because of the
			// event name, so we need to adjust this and set the event name to current_item.
			$sz->current_item   = sz_current_action();
			$sz->current_action = sz_action_variable( 0 );
			array_shift( $sz->action_variables );

			// Using "item" not "event" for generic support in other components.
			if ( sz_current_user_can( 'sz_moderate' ) ) {
				sz_update_is_item_admin( true, 'events' );
			} else {
				sz_update_is_item_admin( events_is_user_admin( sz_loggedin_user_id(), $this->current_event->id ), 'events' );
			}

			// If the user is not an admin, check if they are a moderator.
			if ( ! sz_is_item_admin() ) {
				sz_update_is_item_mod  ( events_is_user_mod  ( sz_loggedin_user_id(), $this->current_event->id ), 'events' );
			}

			// Check once if the current event has a custom front template.
			$this->current_event->front_template = sz_events_get_front_template( $this->current_event );

			// Initialize the nav for the events component.
			$this->nav = new SZ_Core_Nav( $this->current_event->id );

		// Set current_event to 0 to prevent debug errors.
		} else {
			$this->current_event = 0;
		}

		// Set event type if available.
		if ( sz_is_events_directory() && sz_is_current_action( sz_get_events_event_type_base() ) && sz_action_variable() ) {
			$matched_types = sz_events_get_event_types( array(
				'has_directory'  => true,
				'directory_slug' => sz_action_variable(),
			) );

			// Set 404 if we do not have a valid event type.
			if ( empty( $matched_types ) ) {
				sz_do_404();
				return;
			}

			// Set our directory type marker.
			$this->current_directory_type = reset( $matched_types );
		}

		// Set up variables specific to the event creation process.
		if ( sz_is_events_component() && sz_is_current_action( 'create' ) && sz_user_can_create_events() && isset( $_COOKIE['sz_new_event_id'] ) ) {
			$sz->events->new_event_id = (int) $_COOKIE['sz_new_event_id'];
		}

		/**
		 * Filters the list of illegal events names/slugs.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of illegal event names/slugs.
		 */
		$this->forbidden_names = apply_filters( 'events_forbidden_names', array(
			'my-events',
			'create',
			'invites',
			'send-invites',
			'forum',
			'delete',
			'add',
			'admin',
			'request-membership',
			'members',
			'settings',
			'avatar',
			$this->slug,
			$this->root_slug,
		) );

		// If the user was attempting to access a event, but no event by that name was found, 404.
		if ( sz_is_events_component() && empty( $this->current_event ) && empty( $this->current_directory_type ) && sz_current_action() && ! in_array( sz_current_action(), $this->forbidden_names ) ) {
			sz_do_404();
			return;
		}

		/**
		 * Filters the preconfigured events creation steps.
		 *
		 * @since 1.1.0
		 *
		 * @param array $value Array of preconfigured event creation steps.
		 */
		$this->event_creation_steps = apply_filters( 'events_create_event_steps', array(
			'event-details'  => array(
				'name'       => _x( 'Details', 'Event screen nav', 'sportszone' ),
				'position'   => 0
			),
			'event-settings' => array(
				'name'       => _x( 'Settings', 'Event screen nav', 'sportszone' ),
				'position'   => 10
			)
		) );

		if ( sz_event_use_cover_image_header() ) {
			$this->event_creation_steps['event-cover-image'] = array(
				'name'     => _x( 'Cover Image', 'Event screen nav', 'sportszone' ),
				'position' => 25
			);
		}

		// If friends component is active, add invitations.
		if ( sz_is_active( 'friends' ) ) {
			$this->event_creation_steps['event-invites'] = array(
				'name'     => _x( 'Invites',  'Event screen nav', 'sportszone' ),
				'position' => 30
			);
		}

		/**
		 * Filters the list of valid events statuses.
		 *
		 * @since 1.1.0
		 *
		 * @param array $value Array of valid event statuses.
		 */
		$this->valid_status = apply_filters( 'events_valid_status', array(
			'public',
			'private',
			'paid',
			'hidden'
		) );

		// Auto join event when non event member performs event activity.
		$this->auto_join = defined( 'SZ_DISABLE_AUTO_EVENT_JOIN' ) && SZ_DISABLE_AUTO_EVENT_JOIN ? false : true;
	}

	/**
	 * Set up canonical stack for this component.
	 *
	 * @since 2.1.0
	 */
	public function setup_canonical_stack() {
		if ( ! sz_is_events_component() ) {
			return;
		}

		if ( empty( $this->current_event ) ) {
			return;
		}

		/**
		 * Filters the default events extension.
		 *
		 * @since 1.6.0
		 *
		 * @param string $value SZ_EVENTS_DEFAULT_EXTENSION constant if defined,
		 *                      else 'home'.
		 */
		$this->default_extension = apply_filters( 'sz_events_default_extension', defined( 'SZ_EVENTS_DEFAULT_EXTENSION' ) ? SZ_EVENTS_DEFAULT_EXTENSION : 'home' );

		$sz = sportszone();

		// If the activity component is not active and the current event has no custom front, members are displayed in the home nav.
		if ( 'members' === $this->default_extension && ! sz_is_active( 'activity' ) && ! $this->current_event->front_template ) {
			$this->default_extension = 'home';
		}

		if ( ! sz_current_action() ) {
			$sz->current_action = $this->default_extension;
		}

		// Prepare for a redirect to the canonical URL.
		$sz->canonical_stack['base_url'] = sz_get_event_permalink( $this->current_event );

		if ( sz_current_action() ) {
			$sz->canonical_stack['action'] = sz_current_action();
		}

		/**
		 * If there's no custom front.php template for the event, we need to make sure the canonical stack action
		 * is set to 'home' in these 2 cases:
		 *
		 * - the current action is 'activity' (eg: site.url/events/single/activity) and the Activity component is active
		 * - the current action is 'members' (eg: site.url/events/single/members) and the Activity component is *not* active.
		 */
		if ( ! $this->current_event->front_template && ( sz_is_current_action( 'activity' ) || ( ! sz_is_active( 'activity' ) && sz_is_current_action( 'members' ) ) ) ) {
			$sz->canonical_stack['action'] = 'home';
		}

		if ( ! empty( $sz->action_variables ) ) {
			$sz->canonical_stack['action_variables'] = sz_action_variables();
		}

		// When viewing the default extension, the canonical URL should not have
		// that extension's slug, unless more has been tacked onto the URL via
		// action variables.
		if ( sz_is_current_action( $this->default_extension ) && empty( $sz->action_variables ) )  {
			unset( $sz->canonical_stack['action'] );
		}
	}

	/**
	 * Set up component navigation.
	 *
	 * @since 1.5.0
	 *
	 * @see SZ_Component::setup_nav() for a description of arguments.
	 *
	 * @param array $main_nav Optional. See SZ_Component::setup_nav() for description.
	 * @param array $sub_nav  Optional. See SZ_Component::setup_nav() for description.
	 */
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {

		// Determine user to use.
		if ( sz_displayed_user_domain() ) {
			$user_domain = sz_displayed_user_domain();
		} elseif ( sz_loggedin_user_domain() ) {
			$user_domain = sz_loggedin_user_domain();
		} else {
			$user_domain = false;
		}

		// Only grab count if we're on a user page.
		if ( sz_is_user() ) {
			$class = ( 0 === events_total_events_for_user( sz_displayed_user_id() ) ) ? 'no-count' : 'count';

			$nav_name = sprintf(
				/* translators: %s: Event count for the current user */
				_x( 'Events %s', 'Event screen nav with counter', 'sportszone' ),
				sprintf(
					'<span class="%s">%s</span>',
					esc_attr( $class ),
					sz_get_total_event_count_for_user()
				)
			);
		} else {
			$nav_name = _x( 'Events', 'Event screen nav without counter', 'sportszone' );
		}

		$slug = sz_get_events_slug();

		// Add 'Events' to the main navigation.
		$main_nav = array(
			'name'                => $nav_name,
			'slug'                => $slug,
			'position'            => 70,
			'screen_function'     => 'events_screen_my_events',
			'default_subnav_slug' => 'my-events',
			'item_css_id'         => $this->id
		);

		if ( ! empty( $user_domain ) ) {
			$access      = sz_core_can_edit_settings();
			$events_link = trailingslashit( $user_domain . $slug );

			// Add the My Events nav item.
			$sub_nav[] = array(
				'name'            => __( 'My Events', 'sportszone' ),
				'slug'            => 'my-events',
				'parent_url'      => $events_link,
				'parent_slug'     => $slug,
				'screen_function' => 'events_screen_my_events',
				'position'        => 10,
				'item_css_id'     => 'events-my-events'
			);

			// Add the Event Invites nav item.
			$sub_nav[] = array(
				'name'            => __( 'Invitations', 'sportszone' ),
				'slug'            => 'invites',
				'parent_url'      => $events_link,
				'parent_slug'     => $slug,
				'screen_function' => 'events_screen_event_invites',
				'user_has_access' => $access,
				'position'        => 30
			);

			parent::setup_nav( $main_nav, $sub_nav );
		}

		if ( sz_is_events_component() && sz_is_single_item() ) {

			// Reset sub nav.
			$sub_nav = array();

			/*
			 * The top-level Events item is called 'Memberships' for legacy reasons.
			 * It does not appear in the interface.
			 */
			sz_core_new_nav_item( array(
				'name'                => __( 'Memberships', 'sportszone' ),
				'slug'                => $this->current_event->slug,
				'position'            => -1, // Do not show in BuddyBar.
				'screen_function'     => 'events_screen_event_home',
				'default_subnav_slug' => $this->default_extension,
				'item_css_id'         => $this->id
			), 'events' );

			$event_link = sz_get_event_permalink( $this->current_event );

			// Add the "Home" subnav item, as this will always be present.
			$sub_nav[] = array(
				'name'            =>  _x( 'Event Activity', 'Event screen navigation title', 'sportszone' ),
				'slug'            => 'home',
				'parent_url'      => $event_link,
				'parent_slug'     => $this->current_event->slug,
				'screen_function' => 'events_screen_event_home',
				'position'        => 10,
				'item_css_id'     => 'home'
			);

			// If this is a private event, and the user is not a
			// member and does not have an outstanding invitation,
			// show a "Request Membership" nav item.
			if ( sz_current_user_can( 'events_request_membership', array( 'event_id' => $this->current_event->id ) ) ) {

				$sub_nav[] = array(
					'name'            => _x( 'Request Membership','Event screen nav', 'sportszone' ),
					'slug'            => 'request-membership',
					'parent_url'      => $event_link,
					'parent_slug'     => $this->current_event->slug,
					'screen_function' => 'events_screen_event_request_membership',
					'position'        => 30
				);
			}

			if ( $this->current_event->front_template || sz_is_active( 'activity' ) ) {
				/**
				 * If the theme is using a custom front, create activity subnav.
				 */
				if ( $this->current_event->front_template && sz_is_active( 'activity' ) ) {
					$sub_nav[] = array(
						'name'            => _x( 'Activity', 'My Event screen nav', 'sportszone' ),
						'slug'            => 'activity',
						'parent_url'      => $event_link,
						'parent_slug'     => $this->current_event->slug,
						'screen_function' => 'events_screen_event_activity',
						'position'        => 11,
						'user_has_access' => $this->current_event->user_has_access,
						'item_css_id'     => 'activity',
						'no_access_url'   => $event_link,
					);
				}

				/**
				 * Only add the members subnav if it's not the home's nav.
				 */
				$sub_nav[] = array(
					'name'            => sprintf( _x( 'Members %s', 'My Event screen nav', 'sportszone' ), '<span>' . number_format( $this->current_event->total_member_count ) . '</span>' ),
					'slug'            => 'members',
					'parent_url'      => $event_link,
					'parent_slug'     => $this->current_event->slug,
					'screen_function' => 'events_screen_event_members',
					'position'        => 60,
					'user_has_access' => $this->current_event->user_has_access,
					'item_css_id'     => 'members',
					'no_access_url'   => $event_link,
				);
			}

			if ( sz_is_active( 'friends' ) && sz_events_user_can_send_invites() ) {
				$sub_nav[] = array(
					'name'            => _x( 'Send Invites', 'My Event screen nav', 'sportszone' ),
					'slug'            => 'send-invites',
					'parent_url'      => $event_link,
					'parent_slug'     => $this->current_event->slug,
					'screen_function' => 'events_screen_event_invite',
					'item_css_id'     => 'invite',
					'position'        => 70,
					'user_has_access' => $this->current_event->user_has_access,
					'no_access_url'   => $event_link,
				);
			}

			// If the user is a event admin, then show the event admin nav item.
			if ( sz_is_item_admin() ) {
				$sub_nav[] = array(
					'name'            => _x( 'Manage', 'My Event screen nav', 'sportszone' ),
					'slug'            => 'admin',
					'parent_url'      => $event_link,
					'parent_slug'     => $this->current_event->slug,
					'screen_function' => 'events_screen_event_admin',
					'position'        => 1000,
					'user_has_access' => true,
					'item_css_id'     => 'admin',
					'no_access_url'   => $event_link,
				);

				$admin_link = trailingslashit( $event_link . 'admin' );

				// Common params to all nav items.
				$default_params = array(
					'parent_url'        => $admin_link,
					'parent_slug'       => $this->current_event->slug . '_manage',
					'screen_function'   => 'events_screen_event_admin',
					'user_has_access'   => sz_is_item_admin(),
					'show_in_admin_bar' => true,
				);

				$sub_nav[] = array_merge( array(
					'name'     => __( 'Details', 'sportszone' ),
					'slug'     => 'edit-details',
					'position' => 0,
				), $default_params );

				$sub_nav[] = array_merge( array(
					'name'     => __( 'Settings', 'sportszone' ),
					'slug'     => 'event-settings',
					'position' => 10,
				), $default_params );


				if ( sz_event_use_cover_image_header() ) {
					$sub_nav[] = array_merge( array(
						'name'     => __( 'Cover Image', 'sportszone' ),
						'slug'     => 'event-cover-image',
						'position' => 25,
					), $default_params );
				}

				$sub_nav[] = array_merge( array(
					'name'     => __( 'Members', 'sportszone' ),
					'slug'     => 'manage-members',
					'position' => 30,
				), $default_params );

				if ( 'private' == $this->current_event->status ) {
					$sub_nav[] = array_merge( array(
						'name'     => __( 'Requests', 'sportszone' ),
						'slug'     => 'membership-requests',
						'position' => 40,
					), $default_params );
				}

				$sub_nav[] = array_merge( array(
					'name'     => __( 'Delete', 'sportszone' ),
					'slug'     => 'delete-event',
					'position' => 1000,
				), $default_params );
			}

			foreach ( $sub_nav as $nav ) {
				sz_core_new_subnav_item( $nav, 'events' );
			}
		}

		if ( isset( $this->current_event->user_has_access ) ) {

			/**
			 * Fires at the end of the events navigation setup if user has access.
			 *
			 * @since 1.0.2
			 *
			 * @param bool $user_has_access Whether or not user has access.
			 */
			do_action( 'events_setup_nav', $this->current_event->user_has_access );
		} else {

			/** This action is documented in sz-events/sz-events-loader.php */
			do_action( 'events_setup_nav');
		}
	}

	/**
	 * Set up the component entries in the WordPress Admin Bar.
	 *
	 * @since 1.5.0
	 *
	 * @see SZ_Component::setup_nav() for a description of the $wp_admin_nav
	 *      parameter array.
	 *
	 * @param array $wp_admin_nav See SZ_Component::setup_admin_bar() for a description.
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {

		// Menus for logged in user.
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables.
			$events_link = trailingslashit( sz_loggedin_user_domain() . sz_get_events_slug() );

			// Pending event invites.
			$count   = events_get_invite_count_for_user();
			$title   = _x( 'Events', 'My Account Events', 'sportszone' );
			$pending = _x( 'No Pending Invites', 'My Account Events sub nav', 'sportszone' );

			if ( ! empty( $count['total'] ) ) {
				$title = sprintf(
					/* translators: %s: Event invitation count for the current user */
					_x( 'Events %s', 'My Account Events nav', 'sportszone' ),
					'<span class="count badge badge-primary badge-pill">' . sz_core_number_format( $count ) . '</span>'
				);

				$pending = sprintf(
					/* translators: %s: Event invitation count for the current user */
					_x( 'Pending Invites %s', 'My Account Events sub nav', 'sportszone' ),
					'<span class="count">' . sz_core_number_format( $count ) . '</span>'
				);
			}

			// Add the "My Account" sub menus.
			$wp_admin_nav[] = array(
				'parent' => sportszone()->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => $title,
				'href'   => $events_link
			);

			// My Events.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-memberships',
				'title'    => _x( 'Memberships', 'My Account Events sub nav', 'sportszone' ),
				'href'     => $events_link,
				'position' => 10
			);

			// Invitations.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-invites',
				'title'    => $pending,
				'href'     => trailingslashit( $events_link . 'invites' ),
				'position' => 30
			);

			// Create a Event.
			if ( sz_user_can_create_events() ) {
				$wp_admin_nav[] = array(
					'parent'   => 'my-account-' . $this->id,
					'id'       => 'my-account-' . $this->id . '-create',
					'title'    => _x( 'Create a Event', 'My Account Events sub nav', 'sportszone' ),
					'href'     => trailingslashit( sz_get_events_directory_permalink() . 'create' ),
					'position' => 90
				);
			}
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Set up the title for pages and <title>.
	 *
	 * @since 1.5.0
	 */
	public function setup_title() {

		if ( sz_is_events_component() ) {
			$sz = sportszone();

			if ( sz_is_my_profile() && !sz_is_single_item() ) {
				$sz->sz_options_title = _x( 'Memberships', 'My Events page <title>', 'sportszone' );

			} elseif ( !sz_is_my_profile() && !sz_is_single_item() ) {
				$sz->sz_options_avatar = sz_core_fetch_avatar( array(
					'item_id' => sz_displayed_user_id(),
					'type'    => 'thumb',
					'alt'     => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_get_displayed_user_fullname() )
				) );
				$sz->sz_options_title = sz_get_displayed_user_fullname();

			// We are viewing a single event, so set up the
			// event navigation menu using the $this->current_event global.
			} elseif ( sz_is_single_item() ) {
				$sz->sz_options_title  = $this->current_event->name;
				$sz->sz_options_avatar = sz_core_fetch_avatar( array(
					'item_id'    => $this->current_event->id,
					'object'     => 'event',
					'type'       => 'thumb',
					'avatar_dir' => 'event-avatars',
					'alt'        => __( 'Event Profile Photo', 'sportszone' )
				) );

				if ( empty( $sz->sz_options_avatar ) ) {
					$sz->sz_options_avatar = '<img src="' . esc_url( sz_core_avatar_default_thumb() ) . '" alt="' . esc_attr__( 'No Event Profile Photo', 'sportszone' ) . '" class="avatar" />';
				}
			}
		}

		parent::setup_title();
	}

	/**
	 * Setup cache events
	 *
	 * @since 2.2.0
	 */
	public function setup_cache_events() {

		// Global events.
		wp_cache_add_global_groups( array(
			'sz_events',
			'sz_event_admins',
			'sz_event_invite_count',
			'event_meta',
			'sz_events_memberships',
			'sz_events_memberships_for_user',
		) );

		parent::setup_cache_events();
	}

	/**
	 * Set up taxonomies.
	 *
	 * @since 2.6.0
	 */
	public function register_taxonomies() {
		// Event Type.
		register_taxonomy( 'sz_event_type', 'sz_event', array(
			'public' => false,
		) );
	}
}
