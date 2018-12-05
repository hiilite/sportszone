<?php
/**
 * SportsZone Groups Component Class.
 *
 * @package SportsZone
 * @subpackage GroupsLoader
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Creates our Groups component.
 *
 * @since 1.5.0
 */
class SZ_Groups_Component extends SZ_Component {

	/**
	 * Auto-join group when non group member performs group activity.
	 *
	 * @since 1.5.0
	 * @var bool
	 */
	public $auto_join;

	/**
	 * The group being currently accessed.
	 *
	 * @since 1.5.0
	 * @var SZ_Groups_Group
	 */
	public $current_group;

	/**
	 * Default group extension.
	 *
	 * @since 1.6.0
	 * @todo Is this used anywhere? Is this a duplicate of $default_extension?
	 * @var string
	 */
	var $default_component;

	/**
	 * Default group extension.
	 *
	 * @since 1.6.0
	 * @var string
	 */
	public $default_extension;

	/**
	 * Illegal group names/slugs.
	 *
	 * @since 1.5.0
	 * @var array
	 */
	public $forbidden_names;

	/**
	 * Group creation/edit steps (e.g. Details, Settings, Avatar, Invites).
	 *
	 * @since 1.5.0
	 * @var array
	 */
	public $group_creation_steps;

	/**
	 * Types of group statuses (Public, Private, Hidden).
	 *
	 * @since 1.5.0
	 * @var array
	 */
	public $valid_status;

	/**
	 * Group types.
	 *
	 * @see sz_groups_register_group_type()
	 *
	 * @since 2.6.0
	 * @var array
	 */
	public $types = array();

	/**
	 * Current directory group type.
	 *
	 * @see groups_directory_groups_setup()
	 *
	 * @since 2.7.0
	 * @var string
	 */
	public $current_directory_type = '';

	/**
	 * Start the groups component creation process.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		parent::start(
			'groups',
			_x( 'User Groups', 'Group screen page <title>', 'sportszone' ),
			sportszone()->plugin_dir,
			array(
				'adminbar_myaccount_order' => 70,
				'search_query_arg' => 'groups_search',
			)
		);
	}

	/**
	 * Include Groups component files.
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
			'types'
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

		if ( sz_is_groups_component() ) {
			// Authenticated actions.
			if ( is_user_logged_in() &&
				in_array( sz_current_action(), array( 'create', 'join', 'leave-group' ), true )
			) {
				require $this->path . 'sz-groups/actions/' . sz_current_action() . '.php';
			}

			// Actions - RSS feed handler.
			if ( sz_is_active( 'activity' ) && sz_is_current_action( 'feed' ) ) {
				require $this->path . 'sz-groups/actions/feed.php';
			}

			// Actions - Random group handler.
			if ( isset( $_GET['random-group'] ) ) {
				require $this->path . 'sz-groups/actions/random.php';
			}

			// Screens - Directory.
			if ( sz_is_groups_directory() ) {
				require $this->path . 'sz-groups/screens/directory.php';
			}

			// Screens - User profile integration.
			if ( sz_is_user() ) {
				require $this->path . 'sz-groups/screens/user/my-groups.php';

				if ( sz_is_current_action( 'invites' ) ) {
					require $this->path . 'sz-groups/screens/user/invites.php';
				}
			}

			// Single group.
			if ( sz_is_group() ) {
				// Actions - Access protection.
				require $this->path . 'sz-groups/actions/access.php';

				// Public nav items.
				if ( in_array( sz_current_action(), array( 'home', 'request-membership', 'activity', 'members', 'send-invites' ), true ) ) {
					require $this->path . 'sz-groups/screens/single/' . sz_current_action() . '.php';
				}

				// Admin nav items.
				if ( sz_is_item_admin() && is_user_logged_in() ) {
					require $this->path . 'sz-groups/screens/single/admin.php';

					if ( in_array( sz_get_group_current_admin_tab(), array( 'edit-details', 'group-settings', 'group-avatar', 'group-cover-image', 'manage-members', 'membership-requests', 'delete-group' ), true ) ) {
						require $this->path . 'sz-groups/screens/single/admin/' . sz_get_group_current_admin_tab() . '.php';
					}
				}
			}
			
			

			// Theme compatibility.
			new SZ_Groups_Theme_Compat();
		}
	}

	/**
	 * Set up component global data.
	 *
	 * The SZ_GROUPS_SLUG constant is deprecated, and only used here for
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
		if ( ! defined( 'SZ_GROUPS_SLUG' ) ) {
			define( 'SZ_GROUPS_SLUG', $this->id );
		}

		// Global tables for groups component.
		$global_tables = array(
			'table_name'           => $sz->table_prefix . 'sz_groups',
			'table_name_members'   => $sz->table_prefix . 'sz_groups_members',
			'table_name_groupmeta' => $sz->table_prefix . 'sz_groups_groupmeta'
		);

		// Metadata tables for groups component.
		$meta_tables = array(
			'group' => $sz->table_prefix . 'sz_groups_groupmeta',
		);

		// Fetch the default directory title.
		$default_directory_titles = sz_core_get_directory_page_default_titles();
		$default_directory_title  = $default_directory_titles[$this->id];

		// All globals for groups component.
		// Note that global_tables is included in this array.
		$args = array(
			'slug'                  => SZ_GROUPS_SLUG,
			'root_slug'             => isset( $sz->pages->groups->slug ) ? $sz->pages->groups->slug : SZ_GROUPS_SLUG,
			'has_directory'         => true,
			'directory_title'       => isset( $sz->pages->groups->title ) ? $sz->pages->groups->title : $default_directory_title,
			'notification_callback' => 'groups_format_notifications',
			'search_string'         => _x( 'Search Groups...', 'Component directory search', 'sportszone' ),
			'global_tables'         => $global_tables,
			'meta_tables'           => $meta_tables,
		);

		parent::setup_globals( $args );

		/* Single Group Globals **********************************************/

		// Are we viewing a single group?
		if ( sz_is_groups_component()
			&& ( ( $group_id = SZ_Groups_Group::group_exists( sz_current_action() ) )
				|| ( $group_id = SZ_Groups_Group::get_id_by_previous_slug( sz_current_action() ) ) )
			) {
			$sz->is_single_item  = true;

			/**
			 * Filters the current PHP Class being used.
			 *
			 * @since 1.5.0
			 *
			 * @param string $value Name of the class being used.
			 */
			$current_group_class = apply_filters( 'sz_groups_current_group_class', 'SZ_Groups_Group' );

			if ( $current_group_class == 'SZ_Groups_Group' ) {
				$this->current_group = groups_get_group( $group_id );

			} else {

				/**
				 * Filters the current group object being instantiated from previous filter.
				 *
				 * @since 1.5.0
				 *
				 * @param object $value Newly instantiated object for the group.
				 */
				$this->current_group = apply_filters( 'sz_groups_current_group_object', new $current_group_class( $group_id ) );
			}

			// When in a single group, the first action is bumped down one because of the
			// group name, so we need to adjust this and set the group name to current_item.
			$sz->current_item   = sz_current_action();
			$sz->current_action = sz_action_variable( 0 );
			array_shift( $sz->action_variables );

			// Using "item" not "group" for generic support in other components.
			if ( sz_current_user_can( 'sz_moderate' ) ) {
				sz_update_is_item_admin( true, 'groups' );
			} else {
				sz_update_is_item_admin( groups_is_user_admin( sz_loggedin_user_id(), $this->current_group->id ), 'groups' );
			}

			// If the user is not an admin, check if they are a moderator.
			if ( ! sz_is_item_admin() ) {
				sz_update_is_item_mod  ( groups_is_user_mod  ( sz_loggedin_user_id(), $this->current_group->id ), 'groups' );
			}

			// Check once if the current group has a custom front template.
			$this->current_group->front_template = sz_groups_get_front_template( $this->current_group );

			// Initialize the nav for the groups component.
			$this->nav = new SZ_Core_Nav( $this->current_group->id );

		// Set current_group to 0 to prevent debug errors.
		} else {
			$this->current_group = 0;
		}

		// Set group type if available.
		if ( sz_is_groups_directory() && sz_is_current_action( sz_get_groups_group_type_base() ) && sz_action_variable() ) {
			$matched_types = sz_groups_get_group_types( array(
				'has_directory'  => true,
				'directory_slug' => sz_action_variable(),
			) );

			// Set 404 if we do not have a valid group type.
			if ( empty( $matched_types ) ) {
				sz_do_404();
				return;
			}

			// Set our directory type marker.
			$this->current_directory_type = reset( $matched_types );
		}

		// Set up variables specific to the group creation process.
		if ( sz_is_groups_component() && sz_is_current_action( 'create' ) && sz_user_can_create_groups() && isset( $_COOKIE['sz_new_group_id'] ) ) {
			$sz->groups->new_group_id = (int) $_COOKIE['sz_new_group_id'];
		}

		/**
		 * Filters the list of illegal groups names/slugs.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of illegal group names/slugs.
		 */
		$this->forbidden_names = apply_filters( 'groups_forbidden_names', array(
			'my-groups',
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

		// If the user was attempting to access a group, but no group by that name was found, 404.
		if ( sz_is_groups_component() && empty( $this->current_group ) && empty( $this->current_directory_type ) && sz_current_action() && ! in_array( sz_current_action(), $this->forbidden_names ) ) {
			sz_do_404();
			return;
		}

		/**
		 * Filters the preconfigured groups creation steps.
		 *
		 * @since 1.1.0
		 *
		 * @param array $value Array of preconfigured group creation steps.
		 */
		$this->group_creation_steps = apply_filters( 'groups_create_group_steps', array(
			'group-details'  => array(
				'name'       => _x( 'Details', 'Group screen nav', 'sportszone' ),
				'position'   => 0
			),
			'group-settings' => array(
				'name'       => _x( 'Settings', 'Group screen nav', 'sportszone' ),
				'position'   => 10
			)
		) );

		// If avatar uploads are not disabled, add avatar option.
		$disabled_avatar_uploads = (int) sz_disable_group_avatar_uploads();
		if ( ! $disabled_avatar_uploads && $sz->avatar->show_avatars ) {
			$this->group_creation_steps['group-avatar'] = array(
				'name'     => _x( 'Photo', 'Group screen nav', 'sportszone' ),
				'position' => 20
			);
		}

		if ( sz_group_use_cover_image_header() ) {
			$this->group_creation_steps['group-cover-image'] = array(
				'name'     => _x( 'Cover Image', 'Group screen nav', 'sportszone' ),
				'position' => 25
			);
		}

		// If friends component is active, add invitations.
		if ( sz_is_active( 'friends' ) ) {
			$this->group_creation_steps['group-invites'] = array(
				'name'     => _x( 'Invites',  'Group screen nav', 'sportszone' ),
				'position' => 30
			);
		}

		/**
		 * Filters the list of valid groups statuses.
		 *
		 * @since 1.1.0
		 *
		 * @param array $value Array of valid group statuses.
		 */
		$this->valid_status = apply_filters( 'groups_valid_status', array(
			'public',
			'private',
			'hidden'
		) );

		// Auto join group when non group member performs group activity.
		$this->auto_join = defined( 'SZ_DISABLE_AUTO_GROUP_JOIN' ) && SZ_DISABLE_AUTO_GROUP_JOIN ? false : true;
	}

	/**
	 * Set up canonical stack for this component.
	 *
	 * @since 2.1.0
	 */
	public function setup_canonical_stack() {
		if ( ! sz_is_groups_component() ) {
			return;
		}

		if ( empty( $this->current_group ) ) {
			return;
		}

		/**
		 * Filters the default groups extension.
		 *
		 * @since 1.6.0
		 *
		 * @param string $value SZ_GROUPS_DEFAULT_EXTENSION constant if defined,
		 *                      else 'home'.
		 */
		$this->default_extension = apply_filters( 'sz_groups_default_extension', defined( 'SZ_GROUPS_DEFAULT_EXTENSION' ) ? SZ_GROUPS_DEFAULT_EXTENSION : 'home' );

		$sz = sportszone();

		// If the activity component is not active and the current group has no custom front, members are displayed in the home nav.
		if ( 'members' === $this->default_extension && ! sz_is_active( 'activity' ) && ! $this->current_group->front_template ) {
			$this->default_extension = 'home';
		}

		if ( ! sz_current_action() ) {
			$sz->current_action = $this->default_extension;
		}

		// Prepare for a redirect to the canonical URL.
		$sz->canonical_stack['base_url'] = sz_get_group_permalink( $this->current_group );

		if ( sz_current_action() ) {
			$sz->canonical_stack['action'] = sz_current_action();
		}

		/**
		 * If there's no custom front.php template for the group, we need to make sure the canonical stack action
		 * is set to 'home' in these 2 cases:
		 *
		 * - the current action is 'activity' (eg: site.url/groups/single/activity) and the Activity component is active
		 * - the current action is 'members' (eg: site.url/groups/single/members) and the Activity component is *not* active.
		 */
		if ( ! $this->current_group->front_template && ( sz_is_current_action( 'activity' ) || ( ! sz_is_active( 'activity' ) && sz_is_current_action( 'members' ) ) ) ) {
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
			$class = ( 0 === groups_total_groups_for_user( sz_displayed_user_id() ) ) ? 'no-count' : 'count';

			$nav_name = sprintf(
				/* translators: %s: Group count for the current user */
				_x( 'Groups %s', 'Group screen nav with counter', 'sportszone' ),
				sprintf(
					'<span class="%s">%s</span>',
					esc_attr( $class ),
					sz_get_total_group_count_for_user()
				)
			);
		} else {
			$nav_name = _x( 'Groups', 'Group screen nav without counter', 'sportszone' );
		}

		$slug = sz_get_groups_slug();

		// Add 'Groups' to the main navigation.
		$main_nav = array(
			'name'                => $nav_name,
			'slug'                => $slug,
			'position'            => 70,
			'screen_function'     => 'groups_screen_my_groups',
			'default_subnav_slug' => 'my-groups',
			'item_css_id'         => $this->id
		);

		if ( ! empty( $user_domain ) ) {
			$access      = sz_core_can_edit_settings();
			$groups_link = trailingslashit( $user_domain . $slug );

			// Add the My Groups nav item.
			$sub_nav[] = array(
				'name'            => __( 'Memberships', 'sportszone' ),
				'slug'            => 'my-groups',
				'parent_url'      => $groups_link,
				'parent_slug'     => $slug,
				'screen_function' => 'groups_screen_my_groups',
				'position'        => 10,
				'item_css_id'     => 'groups-my-groups'
			);

			// Add the Group Invites nav item.
			$sub_nav[] = array(
				'name'            => __( 'Invitations', 'sportszone' ),
				'slug'            => 'invites',
				'parent_url'      => $groups_link,
				'parent_slug'     => $slug,
				'screen_function' => 'groups_screen_group_invites',
				'user_has_access' => $access,
				'position'        => 30
			);

			parent::setup_nav( $main_nav, $sub_nav );
		}

		if ( sz_is_groups_component() && sz_is_single_item() ) {

			// Reset sub nav.
			$sub_nav = array();

			/*
			 * The top-level Groups item is called 'Memberships' for legacy reasons.
			 * It does not appear in the interface.
			 */
			sz_core_new_nav_item( array(
				'name'                => __( 'Memberships', 'sportszone' ),
				'slug'                => $this->current_group->slug,
				'position'            => -1, // Do not show in BuddyBar.
				'screen_function'     => 'groups_screen_group_home',
				'default_subnav_slug' => $this->default_extension,
				'item_css_id'         => $this->id
			), 'groups' );

			$group_link = sz_get_group_permalink( $this->current_group );

			// Add the "Homepage" subnav item, as this will always be present.
			/*$sub_nav[] = array(
				'name'            =>  _x( 'About', 'Group screen navigation title', 'sportszone' ),
				'slug'            => 'about',
				'parent_url'      => $group_link,
				'parent_slug'     => $this->current_group->slug,
				'screen_function' => 'groups_screen_group_about',
				'position'        => 1,
				'item_css_id'     => 'about'
			);*/

			// Add the "Home" subnav item, as this will always be present.
			$sub_nav[] = array(
				'name'            =>  _x( 'About', 'Group screen navigation title', 'sportszone' ),
				'slug'            => 'home',
				'parent_url'      => $group_link,
				'parent_slug'     => $this->current_group->slug,
				'screen_function' => 'groups_screen_group_home',
				'position'        => 10,
				'item_css_id'     => 'home'
			);

			// If this is a private group, and the user is not a
			// member and does not have an outstanding invitation,
			// show a "Request Membership" nav item.
			if ( sz_current_user_can( 'groups_request_membership', array( 'group_id' => $this->current_group->id ) ) ) {

				$sub_nav[] = array(
					'name'            => _x( 'Request Membership','Group screen nav', 'sportszone' ),
					'slug'            => 'request-membership',
					'parent_url'      => $group_link,
					'parent_slug'     => $this->current_group->slug,
					'screen_function' => 'groups_screen_group_request_membership',
					'position'        => 1
				);
			}

			if ( $this->current_group->front_template || sz_is_active( 'activity' ) ) {
				/**
				 * If the theme is using a custom front, create activity subnav.
				 */
				if ( $this->current_group->front_template && sz_is_active( 'activity' ) ) {
					$sub_nav[] = array(
						'name'            => _x( 'Group Activity', 'My Group screen nav', 'sportszone' ),
						'slug'            => 'activity',
						'parent_url'      => $group_link,
						'parent_slug'     => $this->current_group->slug,
						'screen_function' => 'groups_screen_group_activity',
						'position'        => 11,
						'user_has_access' => $this->current_group->user_has_access,
						'item_css_id'     => 'activity',
						'no_access_url'   => $group_link,
					);
				}

				/**
				 * Only add the members subnav if it's not the home's nav.
				 */
				$sub_nav[] = array(
					'name'            => sprintf( _x( 'Members %s', 'My Group screen nav', 'sportszone' ), '<span>' . number_format( $this->current_group->total_member_count ) . '</span>' ),
					'slug'            => 'members',
					'parent_url'      => $group_link,
					'parent_slug'     => $this->current_group->slug,
					'screen_function' => 'groups_screen_group_members',
					'position'        => 60,
					'user_has_access' => $this->current_group->user_has_access,
					'item_css_id'     => 'members',
					'no_access_url'   => $group_link,
				);
			}

			if ( sz_is_active( 'friends' ) && sz_groups_user_can_send_invites() ) {
				$sub_nav[] = array(
					'name'            => _x( 'Send Invites', 'My Group screen nav', 'sportszone' ),
					'slug'            => 'send-invites',
					'parent_url'      => $group_link,
					'parent_slug'     => $this->current_group->slug,
					'screen_function' => 'groups_screen_group_invite',
					'item_css_id'     => 'invite',
					'position'        => 70,
					'user_has_access' => $this->current_group->user_has_access,
					'no_access_url'   => $group_link,
				);
			}

			// If the user is a group admin, then show the group admin nav item.
			if ( sz_is_item_admin() ) {
				$sub_nav[] = array(
					'name'            => _x( 'Manage', 'My Group screen nav', 'sportszone' ),
					'slug'            => 'admin',
					'parent_url'      => $group_link,
					'parent_slug'     => $this->current_group->slug,
					'screen_function' => 'groups_screen_group_admin',
					'position'        => 1000,
					'user_has_access' => true,
					'item_css_id'     => 'admin',
					'no_access_url'   => $group_link,
				);

				$admin_link = trailingslashit( $group_link . 'admin' );

				// Common params to all nav items.
				$default_params = array(
					'parent_url'        => $admin_link,
					'parent_slug'       => $this->current_group->slug . '_manage',
					'screen_function'   => 'groups_screen_group_admin',
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
					'slug'     => 'group-settings',
					'position' => 10,
				), $default_params );

				if ( ! sz_disable_group_avatar_uploads() && sportszone()->avatar->show_avatars ) {
					$sub_nav[] = array_merge( array(
						'name'     => __( 'Photo', 'sportszone' ),
						'slug'     => 'group-avatar',
						'position' => 20,
					), $default_params );
				}

				if ( sz_group_use_cover_image_header() ) {
					$sub_nav[] = array_merge( array(
						'name'     => __( 'Cover Image', 'sportszone' ),
						'slug'     => 'group-cover-image',
						'position' => 25,
					), $default_params );
				}

				$sub_nav[] = array_merge( array(
					'name'     => __( 'Members', 'sportszone' ),
					'slug'     => 'manage-members',
					'position' => 30,
				), $default_params );

				if ( 'private' == $this->current_group->status ) {
					$sub_nav[] = array_merge( array(
						'name'     => __( 'Requests', 'sportszone' ),
						'slug'     => 'membership-requests',
						'position' => 40,
					), $default_params );
				}

				$sub_nav[] = array_merge( array(
					'name'     => __( 'Delete', 'sportszone' ),
					'slug'     => 'delete-group',
					'position' => 1000,
				), $default_params );
			}

			foreach ( $sub_nav as $nav ) {
				sz_core_new_subnav_item( $nav, 'groups' );
			}
		}

		if ( isset( $this->current_group->user_has_access ) ) {

			/**
			 * Fires at the end of the groups navigation setup if user has access.
			 *
			 * @since 1.0.2
			 *
			 * @param bool $user_has_access Whether or not user has access.
			 */
			do_action( 'groups_setup_nav', $this->current_group->user_has_access );
		} else {

			/** This action is documented in sz-groups/sz-groups-loader.php */
			do_action( 'groups_setup_nav');
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
			$groups_link = trailingslashit( sz_loggedin_user_domain() . sz_get_groups_slug() );

			// Pending group invites.
			$count   = groups_get_invite_count_for_user();
			$title   = _x( 'Groups', 'My Account Groups', 'sportszone' );
			$pending = _x( 'No Pending Invites', 'My Account Groups sub nav', 'sportszone' );

			if ( ! empty( $count['total'] ) ) {
				$title = sprintf(
					/* translators: %s: Group invitation count for the current user */
					_x( 'Groups %s', 'My Account Groups nav', 'sportszone' ),
					'<span class="count">' . sz_core_number_format( $count ) . '</span>'
				);

				$pending = sprintf(
					/* translators: %s: Group invitation count for the current user */
					_x( 'Pending Invites %s', 'My Account Groups sub nav', 'sportszone' ),
					'<span class="count">' . sz_core_number_format( $count ) . '</span>'
				);
			}

			// Add the "My Account" sub menus.
			$wp_admin_nav[] = array(
				'parent' => sportszone()->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => $title,
				'href'   => $groups_link
			);

			// My Groups.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-memberships',
				'title'    => _x( 'Memberships', 'My Account Groups sub nav', 'sportszone' ),
				'href'     => $groups_link,
				'position' => 10
			);

			// Invitations.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-invites',
				'title'    => $pending,
				'href'     => trailingslashit( $groups_link . 'invites' ),
				'position' => 30
			);

			// Create a Group.
			if ( sz_user_can_create_groups() ) {
				$wp_admin_nav[] = array(
					'parent'   => 'my-account-' . $this->id,
					'id'       => 'my-account-' . $this->id . '-create',
					'title'    => _x( 'Create a Group', 'My Account Groups sub nav', 'sportszone' ),
					'href'     => trailingslashit( sz_get_groups_directory_permalink() . 'create' ),
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

		if ( sz_is_groups_component() ) {
			$sz = sportszone();

			if ( sz_is_my_profile() && !sz_is_single_item() ) {
				$sz->sz_options_title = _x( 'Memberships', 'My Groups page <title>', 'sportszone' );

			} elseif ( !sz_is_my_profile() && !sz_is_single_item() ) {
				$sz->sz_options_avatar = sz_core_fetch_avatar( array(
					'item_id' => sz_displayed_user_id(),
					'type'    => 'thumb',
					'alt'     => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_get_displayed_user_fullname() )
				) );
				$sz->sz_options_title = sz_get_displayed_user_fullname();

			// We are viewing a single group, so set up the
			// group navigation menu using the $this->current_group global.
			} elseif ( sz_is_single_item() ) {
				$sz->sz_options_title  = $this->current_group->name;
				$sz->sz_options_avatar = sz_core_fetch_avatar( array(
					'item_id'    => $this->current_group->id,
					'object'     => 'group',
					'type'       => 'thumb',
					'avatar_dir' => 'group-avatars',
					'alt'        => __( 'Group Profile Photo', 'sportszone' )
				) );

				if ( empty( $sz->sz_options_avatar ) ) {
					$sz->sz_options_avatar = '<img src="' . esc_url( sz_core_avatar_default_thumb() ) . '" alt="' . esc_attr__( 'No Group Profile Photo', 'sportszone' ) . '" class="avatar" />';
				}
			}
		}

		parent::setup_title();
	}

	/**
	 * Setup cache groups
	 *
	 * @since 2.2.0
	 */
	public function setup_cache_groups() {

		// Global groups.
		wp_cache_add_global_groups( array(
			'sz_groups',
			'sz_group_admins',
			'sz_group_invite_count',
			'group_meta',
			'sz_groups_memberships',
			'sz_groups_memberships_for_user',
		) );

		parent::setup_cache_groups();
	}

	/**
	 * Set up taxonomies.
	 *
	 * @since 2.6.0
	 */
	public function register_taxonomies() {
		// Group Type.
		register_taxonomy( 'sz_group_type', 'sz_group', array(
			'public' => false,
		) );
	}
}
