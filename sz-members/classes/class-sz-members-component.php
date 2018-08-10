<?php
/**
 * SportsZone Member Loader.
 *
 * @package SportsZone
 * @subpackage Members
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Defines the SportsZone Members Component.
 *
 * @since 1.5.0
 */
class SZ_Members_Component extends SZ_Component {

	/**
	 * Member types.
	 *
	 * @see sz_register_member_type()
	 *
	 * @since 2.2.0
	 * @var array
	 */
	public $types = array();

	/**
	 * Start the members component creation process.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		parent::start(
			'members',
			__( 'Members', 'sportszone' ),
			sportszone()->plugin_dir,
			array(
				'adminbar_myaccount_order' => 20,
				'search_query_arg' => 'members_search',
			)
		);
	}

	/**
	 * Include sz-members files.
	 *
	 * @since 1.5.0
	 *
	 * @see SZ_Component::includes() for description of parameters.
	 *
	 * @param array $includes See {@link SZ_Component::includes()}.
	 */
	public function includes( $includes = array() ) {

		// Always include these files.
		$includes = array(
			'filters',
			'template',
			'adminbar',
			'functions',
			'widgets',
			'cache',
		);

		if ( sz_is_active( 'activity' ) ) {
			$includes[] = 'activity';
		}

		// Include these only if in admin.
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

		// Members.
		if ( sz_is_members_component() ) {
			// Actions - Random member handler.
			if ( isset( $_GET['random-member'] ) ) {
				require $this->path . 'sz-members/actions/random.php';
			}

			// Screens - Directory.
			if ( sz_is_members_directory() ) {
				require $this->path . 'sz-members/screens/directory.php';
			}
		}

		// Members - User main nav screen.
		if ( sz_is_user() ) {
			require $this->path . 'sz-members/screens/profile.php';
		}

		// Members - Theme compatibility.
		if ( sz_is_members_component() || sz_is_user() ) {
			new SZ_Members_Theme_Compat();
		}

		// Registration / Activation.
		if ( sz_is_register_page() || sz_is_activation_page() ) {
			if ( sz_is_register_page() ) {
				require $this->path . 'sz-members/screens/register.php';
			} else {
				require $this->path . 'sz-members/screens/activate.php';
			}

			// Theme compatibility.
			new SZ_Registration_Theme_Compat();
		}
	}

	/**
	 * Set up sz-members global settings.
	 *
	 * The SZ_MEMBERS_SLUG constant is deprecated, and only used here for
	 * backwards compatibility.
	 *
	 * @since 1.5.0
	 *
	 * @see SZ_Component::setup_globals() for description of parameters.
	 *
	 * @param array $args See {@link SZ_Component::setup_globals()}.
	 */
	public function setup_globals( $args = array() ) {
		global $wpdb;

		$sz = sportszone();

		/** Component Globals ************************************************
		 */

		// Define a slug, as a fallback for backpat.
		if ( !defined( 'SZ_MEMBERS_SLUG' ) ) {
			define( 'SZ_MEMBERS_SLUG', $this->id );
		}

		// Fetch the default directory title.
		$default_directory_titles = sz_core_get_directory_page_default_titles();
		$default_directory_title  = $default_directory_titles[$this->id];

		// Override any passed args.
		$args = array(
			'slug'            => SZ_MEMBERS_SLUG,
			'root_slug'       => isset( $sz->pages->members->slug ) ? $sz->pages->members->slug : SZ_MEMBERS_SLUG,
			'has_directory'   => true,
			'directory_title' => isset( $sz->pages->members->title ) ? $sz->pages->members->title : $default_directory_title,
			'search_string'   => __( 'Search Members...', 'sportszone' ),
			'global_tables'   => array(
				'table_name_last_activity' => sz_core_get_table_prefix() . 'sz_activity',
				'table_name_signups'       => $wpdb->base_prefix . 'signups', // Signups is a global WordPress table.
			)
		);

		parent::setup_globals( $args );

		/** Logged in user ***************************************************
		 */

		// The core userdata of the user who is currently logged in.
		$sz->loggedin_user->userdata       = sz_core_get_core_userdata( sz_loggedin_user_id() );

		// Fetch the full name for the logged in user.
		$sz->loggedin_user->fullname       = isset( $sz->loggedin_user->userdata->display_name ) ? $sz->loggedin_user->userdata->display_name : '';

		// Hits the DB on single WP installs so get this separately.
		$sz->loggedin_user->is_super_admin = $sz->loggedin_user->is_site_admin = is_super_admin( sz_loggedin_user_id() );

		// The domain for the user currently logged in. eg: http://example.com/members/andy.
		$sz->loggedin_user->domain         = sz_core_get_user_domain( sz_loggedin_user_id() );

		/** Displayed user ***************************************************
		 */

		// The core userdata of the user who is currently being displayed.
		$sz->displayed_user->userdata = sz_core_get_core_userdata( sz_displayed_user_id() );

		// Fetch the full name displayed user.
		$sz->displayed_user->fullname = isset( $sz->displayed_user->userdata->display_name ) ? $sz->displayed_user->userdata->display_name : '';

		// The domain for the user currently being displayed.
		$sz->displayed_user->domain   = sz_core_get_user_domain( sz_displayed_user_id() );

		// Initialize the nav for the members component.
		$this->nav = new SZ_Core_Nav();

		// If A user is displayed, check if there is a front template
		if ( sz_get_displayed_user() ) {
			$sz->displayed_user->front_template = sz_displayed_user_get_front_template();
		}

		/** Signup ***********************************************************
		 */

		$sz->signup = new stdClass;

		/** Profiles Fallback ************************************************
		 */

		if ( ! sz_is_active( 'xprofile' ) ) {
			$sz->profile       = new stdClass;
			$sz->profile->slug = 'profile';
			$sz->profile->id   = 'profile';
		}
	}

	/**
	 * Set up canonical stack for this component.
	 *
	 * @since 2.1.0
	 */
	public function setup_canonical_stack() {
		$sz = sportszone();

		/** Default Profile Component ****************************************
		 */
		if ( sz_displayed_user_has_front_template() ) {
			$sz->default_component = 'front';
		} elseif ( defined( 'SZ_DEFAULT_COMPONENT' ) && SZ_DEFAULT_COMPONENT ) {
			$sz->default_component = SZ_DEFAULT_COMPONENT;
		} elseif ( sz_is_active( 'activity' ) && isset( $sz->pages->activity ) ) {
			$sz->default_component = sz_get_activity_slug();
		} else {
			$sz->default_component = ( 'xprofile' === $sz->profile->id ) ? 'profile' : $sz->profile->id;
		}

		/** Canonical Component Stack ****************************************
		 */

		if ( sz_displayed_user_id() ) {
			$sz->canonical_stack['base_url'] = sz_displayed_user_domain();

			if ( sz_current_component() ) {
				$sz->canonical_stack['component'] = sz_current_component();
			}

			if ( sz_current_action() ) {
				$sz->canonical_stack['action'] = sz_current_action();
			}

			if ( !empty( $sz->action_variables ) ) {
				$sz->canonical_stack['action_variables'] = sz_action_variables();
			}

			// Looking at the single member root/home, so assume the default.
			if ( ! sz_current_component() ) {
				$sz->current_component = $sz->default_component;

			// The canonical URL will not contain the default component.
			} elseif ( sz_is_current_component( $sz->default_component ) && ! sz_current_action() ) {
				unset( $sz->canonical_stack['component'] );
			}

			// If we're on a spammer's profile page, only users with the 'sz_moderate' cap
			// can view subpages on the spammer's profile.
			//
			// users without the cap trying to access a spammer's subnav page will get
			// redirected to the root of the spammer's profile page.  this occurs by
			// by removing the component in the canonical stack.
			if ( sz_is_user_spammer( sz_displayed_user_id() ) && ! sz_current_user_can( 'sz_moderate' ) ) {
				unset( $sz->canonical_stack['component'] );
			}
		}
	}

	/**
	 * Set up fall-back component navigation if XProfile is inactive.
	 *
	 * @since 1.5.0
	 *
	 * @see SZ_Component::setup_nav() for a description of arguments.
	 *
	 * @param array $main_nav Optional. See SZ_Component::setup_nav() for
	 *                        description.
	 * @param array $sub_nav  Optional. See SZ_Component::setup_nav() for
	 *                        description.
	 */
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {

		// Don't set up navigation if there's no member.
		if ( ! is_user_logged_in() && ! sz_is_user() ) {
			return;
		}

		$is_xprofile_active = sz_is_active( 'xprofile' );

		// Bail if XProfile component is active and there's no custom front page for the user.
		if ( ! sz_displayed_user_has_front_template() && $is_xprofile_active ) {
			return;
		}

		// Determine user to use.
		if ( sz_displayed_user_domain() ) {
			$user_domain = sz_displayed_user_domain();
		} elseif ( sz_loggedin_user_domain() ) {
			$user_domain = sz_loggedin_user_domain();
		} else {
			return;
		}

		// Set slug to profile in case the xProfile component is not active
		$slug = sz_get_profile_slug();

		// Defaults to empty navs
		$this->main_nav = array();
		$this->sub_nav  = array();

		if ( ! $is_xprofile_active ) {
			$this->main_nav = array(
				'name'                => _x( 'Profile', 'Member profile main navigation', 'sportszone' ),
				'slug'                => $slug,
				'position'            => 20,
				'screen_function'     => 'sz_members_screen_display_profile',
				'default_subnav_slug' => 'public',
				'item_css_id'         => sportszone()->profile->id
			);
		}

		/**
		 * Setup the subnav items for the member profile.
		 *
		 * This is required in case there's a custom front or in case the xprofile component
		 * is not active.
		 */
		$this->sub_nav = array(
			'name'            => _x( 'View', 'Member profile view', 'sportszone' ),
			'slug'            => 'public',
			'parent_url'      => trailingslashit( $user_domain . $slug ),
			'parent_slug'     => $slug,
			'screen_function' => 'sz_members_screen_display_profile',
			'position'        => 10
		);

		/**
		 * If there's a front template the members component nav
		 * will be there to display the user's front page.
		 */
		if ( sz_displayed_user_has_front_template() ) {
			$main_nav = array(
				'name'                => _x( 'Home', 'Member Home page', 'sportszone' ),
				'slug'                => 'front',
				'position'            => 5,
				'screen_function'     => 'sz_members_screen_display_profile',
				'default_subnav_slug' => 'public',
			);

			// We need a dummy subnav for the front page to load.
			$front_subnav = $this->sub_nav;
			$front_subnav['parent_slug'] = 'front';

			// In case the subnav is displayed in the front template
			$front_subnav['parent_url'] = trailingslashit( $user_domain . 'front' );

			// Set the subnav
			$sub_nav[] = $front_subnav;

			/**
			 * If the profile component is not active, we need to create a new
			 * nav to display the WordPress profile.
			 */
			if ( ! $is_xprofile_active ) {
				add_action( 'sz_members_setup_nav', array( $this, 'setup_profile_nav' ) );
			}

		/**
		 * If there's no front template and xProfile is not active, the members
		 * component nav will be there to display the WordPress profile
		 */
		} else {
			$main_nav  = $this->main_nav;
			$sub_nav[] = $this->sub_nav;
		}


		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up a profile nav in case the xProfile
	 * component is not active and a front template is
	 * used.
	 *
	 * @since 2.6.0
	 */
	public function setup_profile_nav() {
		if ( empty( $this->main_nav ) || empty( $this->sub_nav ) ) {
			return;
		}

		// Add the main nav
		sz_core_new_nav_item( $this->main_nav, 'members' );

		// Add the sub nav item.
		sz_core_new_subnav_item( $this->sub_nav, 'members' );
	}

	/**
	 * Set up the title for pages and <title>.
	 *
	 * @since 1.5.0
	 */
	public function setup_title() {
		$sz = sportszone();

		if ( sz_is_my_profile() ) {
			$sz->sz_options_title = __( 'You', 'sportszone' );
		} elseif ( sz_is_user() ) {
			$sz->sz_options_title  = sz_get_displayed_user_fullname();
			$sz->sz_options_avatar = sz_core_fetch_avatar( array(
				'item_id' => sz_displayed_user_id(),
				'type'    => 'thumb',
				'alt'     => sprintf( __( 'Profile picture of %s', 'sportszone' ), $sz->sz_options_title )
			) );
		}

		parent::setup_title();
	}

	/**
	 * Setup cache groups.
	 *
	 * @since 2.2.0
	 */
	public function setup_cache_groups() {

		// Global groups.
		wp_cache_add_global_groups( array(
			'sz_last_activity',
			'sz_member_type'
		) );

		parent::setup_cache_groups();
	}
}
