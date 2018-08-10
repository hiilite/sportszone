<?php
/**
 * SportsZone Settings Loader.
 *
 * @package SportsZone
 * @subpackage SettingsLoader
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Creates our Settings component.
 *
 * @since 1.5.0
 */
class SZ_Settings_Component extends SZ_Component {

	/**
	 * Start the settings component creation process.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		parent::start(
			'settings',
			__( 'Settings', 'sportszone' ),
			sportszone()->plugin_dir,
			array(
				'adminbar_myaccount_order' => 100
			)
		);
	}

	/**
	 * Include files.
	 *
	 * @since 1.5.0
	 *
	 * @param array $includes Array of values to include. Not used.
	 */
	public function includes( $includes = array() ) {
		parent::includes( array(
			'template',
			'functions',
		) );
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

		// Bail if not on Settings component.
		if ( ! sz_is_settings_component() ) {
			return;
		}

		$actions = array( 'notifications', 'capabilities', 'delete-account' );

		// Authenticated actions.
		if ( is_user_logged_in() ) {
			if ( ! sz_current_action() || sz_is_current_action( 'general' ) ) {
				require $this->path . 'sz-settings/actions/general.php';

			// Specific to post requests.
			} elseif ( sz_is_post_request() && in_array( sz_current_action(), $actions, true ) ) {
				require $this->path . 'sz-settings/actions/' . sz_current_action() . '.php';
			}
		}

		// Screens - User profile integration.
		if ( sz_is_user() ) {
			require $this->path . 'sz-settings/screens/general.php';

			// Sub-nav items.
			if ( in_array( sz_current_action(), $actions, true ) ) {
				require $this->path . 'sz-settings/screens/' . sz_current_action() . '.php';
			}
		}
	}

	/**
	 * Setup globals.
	 *
	 * The SZ_SETTINGS_SLUG constant is deprecated, and only used here for
	 * backwards compatibility.
	 *
	 * @since 1.5.0
	 *
	 * @param array $args Array of arguments.
	 */
	public function setup_globals( $args = array() ) {

		// Define a slug, if necessary.
		if ( ! defined( 'SZ_SETTINGS_SLUG' ) ) {
			define( 'SZ_SETTINGS_SLUG', $this->id );
		}

		// All globals for settings component.
		parent::setup_globals( array(
			'slug'          => SZ_SETTINGS_SLUG,
			'has_directory' => false,
		) );
	}

	/**
	 * Set up navigation.
	 *
	 * @since 1.5.0
	 *
	 * @param array $main_nav Array of main nav items.
	 * @param array $sub_nav  Array of sub nav items.
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

		$access        = sz_core_can_edit_settings();
		$slug          = sz_get_settings_slug();
		$settings_link = trailingslashit( $user_domain . $slug );

		// Add the settings navigation item.
		$main_nav = array(
			'name'                    => __( 'Settings', 'sportszone' ),
			'slug'                    => $slug,
			'position'                => 100,
			'show_for_displayed_user' => $access,
			'screen_function'         => 'sz_settings_screen_general',
			'default_subnav_slug'     => 'general'
		);

		// Add General Settings nav item.
		$sub_nav[] = array(
			'name'            => __( 'General', 'sportszone' ),
			'slug'            => 'general',
			'parent_url'      => $settings_link,
			'parent_slug'     => $slug,
			'screen_function' => 'sz_settings_screen_general',
			'position'        => 10,
			'user_has_access' => $access
		);

		// Add Email nav item. Formerly called 'Notifications', we
		// retain the old slug and function names for backward compat.
		$sub_nav[] = array(
			'name'            => __( 'Email', 'sportszone' ),
			'slug'            => 'notifications',
			'parent_url'      => $settings_link,
			'parent_slug'     => $slug,
			'screen_function' => 'sz_settings_screen_notification',
			'position'        => 20,
			'user_has_access' => $access
		);

		// Add Spam Account nav item.
		if ( sz_current_user_can( 'sz_moderate' ) ) {
			$sub_nav[] = array(
				'name'            => __( 'Capabilities', 'sportszone' ),
				'slug'            => 'capabilities',
				'parent_url'      => $settings_link,
				'parent_slug'     => $slug,
				'screen_function' => 'sz_settings_screen_capabilities',
				'position'        => 80,
				'user_has_access' => ! sz_is_my_profile()
			);
		}

		// Add Delete Account nav item.
		if ( ( ! sz_disable_account_deletion() && sz_is_my_profile() ) || sz_current_user_can( 'delete_users' ) ) {
			$sub_nav[] = array(
				'name'            => __( 'Delete Account', 'sportszone' ),
				'slug'            => 'delete-account',
				'parent_url'      => $settings_link,
				'parent_slug'     => $slug,
				'screen_function' => 'sz_settings_screen_delete_account',
				'position'        => 90,
				'user_has_access' => ! is_super_admin( sz_displayed_user_id() )
			);
		}

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up the Toolbar.
	 *
	 * @since 1.5.0
	 *
	 * @param array $wp_admin_nav Array of Admin Bar items.
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {

		// Menus for logged in user.
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables.
			$settings_link = trailingslashit( sz_loggedin_user_domain() . sz_get_settings_slug() );

			// Add main Settings menu.
			$wp_admin_nav[] = array(
				'parent' => sportszone()->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => __( 'Settings', 'sportszone' ),
				'href'   => $settings_link
			);

			// General Account.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-general',
				'title'    => __( 'General', 'sportszone' ),
				'href'     => $settings_link,
				'position' => 10
			);

			// Notifications - only add the tab when there is something to display there.
			if ( has_action( 'sz_notification_settings' ) ) {
				$wp_admin_nav[] = array(
					'parent'   => 'my-account-' . $this->id,
					'id'       => 'my-account-' . $this->id . '-notifications',
					'title'    => __( 'Email', 'sportszone' ),
					'href'     => trailingslashit( $settings_link . 'notifications' ),
					'position' => 20
				);
			}

			// Delete Account
			if ( !sz_current_user_can( 'sz_moderate' ) && ! sz_core_get_root_option( 'sz-disable-account-deletion' ) ) {
				$wp_admin_nav[] = array(
					'parent'   => 'my-account-' . $this->id,
					'id'       => 'my-account-' . $this->id . '-delete-account',
					'title'    => __( 'Delete Account', 'sportszone' ),
					'href'     => trailingslashit( $settings_link . 'delete-account' ),
					'position' => 90
				);
			}
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}
}
