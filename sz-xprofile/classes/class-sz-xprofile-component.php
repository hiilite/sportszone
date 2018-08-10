<?php
/**
 * SportsZone XProfile Loader.
 *
 * An extended profile component for users. This allows site admins to create
 * groups of fields for users to enter information about themselves.
 *
 * @package SportsZone
 * @subpackage XProfileLoader
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Creates our XProfile component.
 *
 * @since 1.5.0
 */
class SZ_XProfile_Component extends SZ_Component {

	/**
	 * Profile field types.
	 *
	 * @since 1.5.0
	 * @var array
	 */
	public $field_types;

	/**
	 * The acceptable visibility levels for xprofile fields.
	 *
	 * @see sz_xprofile_get_visibility_levels()
	 *
	 * @since 1.6.0
	 * @var array
	 */
	public $visibility_levels = array();

	/**
	 * Start the xprofile component creation process.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		parent::start(
			'xprofile',
			_x( 'Extended Profiles', 'Component page <title>', 'sportszone' ),
			sportszone()->plugin_dir,
			array(
				'adminbar_myaccount_order' => 20
			)
		);

		$this->setup_hooks();
	}

	/**
	 * Include files.
	 *
	 * @since 1.5.0
	 *
	 * @param array $includes Array of files to include.
	 */
	public function includes( $includes = array() ) {
		$includes = array(
			'cssjs',
			'cache',
			'caps',
			'filters',
			'template',
			'functions',
		);

		// Conditional includes.
		if ( sz_is_active( 'activity' ) ) {
			$includes[] = 'activity';
		}
		if ( sz_is_active( 'notifications' ) ) {
			$includes[] = 'notifications';
		}
		if ( sz_is_active( 'settings' ) ) {
			$includes[] = 'settings';
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

		// Bail if not on a user page.
		if ( ! sz_is_user() ) {
			return;
		}

		// User nav.
		if ( sz_is_profile_component() ) {
			require $this->path . 'sz-xprofile/screens/public.php';

			// Action - Delete avatar.
			if ( is_user_logged_in()&& sz_is_user_change_avatar() && sz_is_action_variable( 'delete-avatar', 0 ) ) {
				require $this->path . 'sz-xprofile/actions/delete-avatar.php';
			}

			// Sub-nav items.
			if ( is_user_logged_in() &&
				in_array( sz_current_action(), array( 'edit', 'change-avatar', 'change-cover-image' ), true )
			) {
				require $this->path . 'sz-xprofile/screens/' . sz_current_action() . '.php';
			}
		}

		// Settings.
		if ( is_user_logged_in() && sz_is_user_settings_profile() ) {
			require $this->path . 'sz-xprofile/screens/settings-profile.php';
		}
	}

	/**
	 * Setup globals.
	 *
	 * The SZ_XPROFILE_SLUG constant is deprecated, and only used here for
	 * backwards compatibility.
	 *
	 * @since 1.5.0
	 *
	 * @param array $args Array of globals to set up.
	 */
	public function setup_globals( $args = array() ) {
		$sz = sportszone();

		// Define a slug, if necessary.
		if ( !defined( 'SZ_XPROFILE_SLUG' ) ) {
			define( 'SZ_XPROFILE_SLUG', 'profile' );
		}

		// Assign the base group and fullname field names to constants
		// to use in SQL statements.
		// Defined conditionally to accommodate unit tests.
		if ( ! defined( 'SZ_XPROFILE_BASE_GROUP_NAME' ) ) {
			define( 'SZ_XPROFILE_BASE_GROUP_NAME', stripslashes( sz_core_get_root_option( 'avatar_default' ) ) );
		}

		if ( ! defined( 'SZ_XPROFILE_FULLNAME_FIELD_NAME' ) ) {
			define( 'SZ_XPROFILE_FULLNAME_FIELD_NAME', stripslashes( sz_core_get_root_option( 'sz-xprofile-fullname-field-name' ) ) );
		}

		/**
		 * Filters the supported field type IDs.
		 *
		 * @since 1.1.0
		 *
		 * @param array $value Array of IDs for the supported field types.
		 */
		$this->field_types = apply_filters( 'xprofile_field_types', array_keys( sz_xprofile_get_field_types() ) );

		// 'option' is a special case. It is not a top-level field, so
		// does not have an associated SZ_XProfile_Field_Type class,
		// but it must be whitelisted.
		$this->field_types[] = 'option';

		// Register the visibility levels. See sz_xprofile_get_visibility_levels() to filter.
		$this->visibility_levels = array(
			'public' => array(
				'id'	  => 'public',
				'label' => _x( 'Everyone', 'Visibility level setting', 'sportszone' )
			),
			'adminsonly' => array(
				'id'	  => 'adminsonly',
				'label' => _x( 'Only Me', 'Visibility level setting', 'sportszone' )
			),
			'loggedin' => array(
				'id'	  => 'loggedin',
				'label' => _x( 'All Members', 'Visibility level setting', 'sportszone' )
			)
		);

		if ( sz_is_active( 'friends' ) ) {
			$this->visibility_levels['friends'] = array(
				'id'	=> 'friends',
				'label'	=> _x( 'My Friends', 'Visibility level setting', 'sportszone' )
			);
		}

		// Tables.
		$global_tables = array(
			'table_name_data'   => $sz->table_prefix . 'sz_xprofile_data',
			'table_name_groups' => $sz->table_prefix . 'sz_xprofile_groups',
			'table_name_fields' => $sz->table_prefix . 'sz_xprofile_fields',
			'table_name_meta'   => $sz->table_prefix . 'sz_xprofile_meta',
		);

		$meta_tables = array(
			'xprofile_group' => $sz->table_prefix . 'sz_xprofile_meta',
			'xprofile_field' => $sz->table_prefix . 'sz_xprofile_meta',
			'xprofile_data'  => $sz->table_prefix . 'sz_xprofile_meta',
		);

		$globals = array(
			'slug'                  => SZ_XPROFILE_SLUG,
			'has_directory'         => false,
			'notification_callback' => 'xprofile_format_notifications',
			'global_tables'         => $global_tables,
			'meta_tables'           => $meta_tables,
		);

		parent::setup_globals( $globals );
	}

	/**
	 * Set up navigation.
	 *
	 * @since 1.5.0
	 *
	 * @global SportsZone $sz The one true SportsZone instance
	 *
	 * @param array $main_nav Array of main nav items to set up.
	 * @param array $sub_nav  Array of sub nav items to set up.
	 */
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {

		// Determine user to use.
		if ( sz_displayed_user_domain() ) {
			$user_domain = sz_displayed_user_domain();
		} elseif ( sz_loggedin_user_domain() ) {
			$user_domain = sz_loggedin_user_domain();
		} else {
			return;
		}

		$access       = sz_core_can_edit_settings();
		$slug         = sz_get_profile_slug();
		$profile_link = trailingslashit( $user_domain . $slug );

		// Add 'Profile' to the main navigation.
		$main_nav = array(
			'name'                => _x( 'Profile', 'Profile header menu', 'sportszone' ),
			'slug'                => $slug,
			'position'            => 20,
			'screen_function'     => 'xprofile_screen_display_profile',
			'default_subnav_slug' => 'public',
			'item_css_id'         => $this->id
		);

		// Add the subnav items to the profile.
		$sub_nav[] = array(
			'name'            => _x( 'View', 'Profile header sub menu', 'sportszone' ),
			'slug'            => 'public',
			'parent_url'      => $profile_link,
			'parent_slug'     => $slug,
			'screen_function' => 'xprofile_screen_display_profile',
			'position'        => 10
		);

		// Edit Profile.
		$sub_nav[] = array(
			'name'            => _x( 'Edit','Profile header sub menu', 'sportszone' ),
			'slug'            => 'edit',
			'parent_url'      => $profile_link,
			'parent_slug'     => $slug,
			'screen_function' => 'xprofile_screen_edit_profile',
			'position'        => 20,
			'user_has_access' => $access
		);

		// Change Avatar.
		if ( sportszone()->avatar->show_avatars ) {
			$sub_nav[] = array(
				'name'            => _x( 'Change Profile Photo', 'Profile header sub menu', 'sportszone' ),
				'slug'            => 'change-avatar',
				'parent_url'      => $profile_link,
				'parent_slug'     => $slug,
				'screen_function' => 'xprofile_screen_change_avatar',
				'position'        => 30,
				'user_has_access' => $access
			);
		}

		// Change Cover image.
		if ( sz_displayed_user_use_cover_image_header() ) {
			$sub_nav[] = array(
				'name'            => _x( 'Change Cover Image', 'Profile header sub menu', 'sportszone' ),
				'slug'            => 'change-cover-image',
				'parent_url'      => $profile_link,
				'parent_slug'     => $slug,
				'screen_function' => 'xprofile_screen_change_cover_image',
				'position'        => 40,
				'user_has_access' => $access
			);
		}

		// The Settings > Profile nav item can only be set up after
		// the Settings component has run its own nav routine.
		add_action( 'sz_settings_setup_nav', array( $this, 'setup_settings_nav' ) );

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up the Settings > Profile nav item.
	 *
	 * Loaded in a separate method because the Settings component may not
	 * be loaded in time for SZ_XProfile_Component::setup_nav().
	 *
	 * @since 2.1.0
	 */
	public function setup_settings_nav() {
		if ( ! sz_is_active( 'settings' ) ) {
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

		// Get the settings slug.
		$settings_slug = sz_get_settings_slug();

		sz_core_new_subnav_item( array(
			'name'            => _x( 'Profile Visibility', 'Profile settings sub nav', 'sportszone' ),
			'slug'            => 'profile',
			'parent_url'      => trailingslashit( $user_domain . $settings_slug ),
			'parent_slug'     => $settings_slug,
			'screen_function' => 'sz_xprofile_screen_settings',
			'position'        => 30,
			'user_has_access' => sz_core_can_edit_settings()
		), 'members' );
	}

	/**
	 * Set up the Admin Bar.
	 *
	 * @since 1.5.0
	 *
	 * @param array $wp_admin_nav Admin Bar items.
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {

		// Menus for logged in user.
		if ( is_user_logged_in() ) {

			// Profile link.
			$profile_link = trailingslashit( sz_loggedin_user_domain() . sz_get_profile_slug() );

			// Add the "Profile" sub menu.
			$wp_admin_nav[] = array(
				'parent' => sportszone()->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => _x( 'Profile', 'My Account Profile', 'sportszone' ),
				'href'   => $profile_link
			);

			// View Profile.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-public',
				'title'    => _x( 'View', 'My Account Profile sub nav', 'sportszone' ),
				'href'     => $profile_link,
				'position' => 10
			);

			// Edit Profile.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-edit',
				'title'    => _x( 'Edit', 'My Account Profile sub nav', 'sportszone' ),
				'href'     => trailingslashit( $profile_link . 'edit' ),
				'position' => 20
			);

			// Edit Avatar.
			if ( sportszone()->avatar->show_avatars ) {
				$wp_admin_nav[] = array(
					'parent'   => 'my-account-' . $this->id,
					'id'       => 'my-account-' . $this->id . '-change-avatar',
					'title'    => _x( 'Change Profile Photo', 'My Account Profile sub nav', 'sportszone' ),
					'href'     => trailingslashit( $profile_link . 'change-avatar' ),
					'position' => 30
				);
			}

			if ( sz_displayed_user_use_cover_image_header() ) {
				$wp_admin_nav[] = array(
					'parent'   => 'my-account-' . $this->id,
					'id'       => 'my-account-' . $this->id . '-change-cover-image',
					'title'    => _x( 'Change Cover Image', 'My Account Profile sub nav', 'sportszone' ),
					'href'     => trailingslashit( $profile_link . 'change-cover-image' ),
					'position' => 40
				);
			}
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Add custom hooks.
	 *
	 * @since 2.0.0
	 */
	public function setup_hooks() {
		add_filter( 'sz_settings_admin_nav', array( $this, 'setup_settings_admin_nav' ), 2 );
	}

	/**
	 * Sets up the title for pages and <title>.
	 *
	 * @since 1.5.0
	 */
	public function setup_title() {

		if ( sz_is_profile_component() ) {
			$sz = sportszone();

			if ( sz_is_my_profile() ) {
				$sz->sz_options_title = _x( 'My Profile', 'Page title', 'sportszone' );
			} else {
				$sz->sz_options_avatar = sz_core_fetch_avatar( array(
					'item_id' => sz_displayed_user_id(),
					'type'    => 'thumb',
					'alt'	  => sprintf( _x( 'Profile picture of %s', 'Avatar alt', 'sportszone' ), sz_get_displayed_user_fullname() )
				) );
				$sz->sz_options_title = sz_get_displayed_user_fullname();
			}
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
			'sz_xprofile',
			'sz_xprofile_data',
			'sz_xprofile_fields',
			'sz_xprofile_groups',
			'xprofile_meta'
		) );

		parent::setup_cache_groups();
	}

	/**
	 * Adds "Settings > Profile" subnav item under the "Settings" adminbar menu.
	 *
	 * @since 2.0.0
	 *
	 * @param array $wp_admin_nav The settings adminbar nav array.
	 * @return array
	 */
	public function setup_settings_admin_nav( $wp_admin_nav ) {

		// Setup the logged in user variables.
		$settings_link = trailingslashit( sz_loggedin_user_domain() . sz_get_settings_slug() );

		// Add the "Profile" subnav item.
		$wp_admin_nav[] = array(
			'parent' => 'my-account-' . sportszone()->settings->id,
			'id'     => 'my-account-' . sportszone()->settings->id . '-profile',
			'title'  => _x( 'Profile', 'My Account Settings sub nav', 'sportszone' ),
			'href'   => trailingslashit( $settings_link . 'profile' )
		);

		return $wp_admin_nav;
	}
}
