<?php
/**
 * SportsZone Member Notifications Loader.
 *
 * Initializes the Notifications component.
 *
 * @package SportsZone
 * @subpackage NotificationsLoader
 * @since 1.9.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Extends the component class to set up the Notifications component.
 */
class SZ_Notifications_Component extends SZ_Component {

	/**
	 * Start the notifications component creation process.
	 *
	 * @since 1.9.0
	 */
	public function __construct() {
		parent::start(
			'notifications',
			_x( 'Notifications', 'Page <title>', 'sportszone' ),
			sportszone()->plugin_dir,
			array(
				'adminbar_myaccount_order' => 30
			)
		);
	}

	/**
	 * Include notifications component files.
	 *
	 * @since 1.9.0
	 *
	 * @see SZ_Component::includes() for a description of arguments.
	 *
	 * @param array $includes See SZ_Component::includes() for a description.
	 */
	public function includes( $includes = array() ) {
		$includes = array(
			'adminbar',
			'template',
			'functions',
			'cache',
		);

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

		// Bail if not on a notifications page or logged in.
		if ( ! sz_is_user_notifications() || ! is_user_logged_in() ) {
			return;
		}

		// Actions.
		if ( sz_is_post_request() ) {
			require $this->path . 'sz-notifications/actions/bulk-manage.php';
		} elseif ( sz_is_get_request() ) {
			require $this->path . 'sz-notifications/actions/delete.php';
		}

		// Screens.
		require $this->path . 'sz-notifications/screens/unread.php';
		if ( sz_is_current_action( 'read' ) ) {
			require $this->path . 'sz-notifications/screens/read.php';
		}
	}

	/**
	 * Set up component global data.
	 *
	 * @since 1.9.0
	 *
	 * @see SZ_Component::setup_globals() for a description of arguments.
	 *
	 * @param array $args See SZ_Component::setup_globals() for a description.
	 */
	public function setup_globals( $args = array() ) {
		$sz = sportszone();

		// Define a slug, if necessary.
		if ( ! defined( 'SZ_NOTIFICATIONS_SLUG' ) ) {
			define( 'SZ_NOTIFICATIONS_SLUG', $this->id );
		}

		// Global tables for the notifications component.
		$global_tables = array(
			'table_name'      => $sz->table_prefix . 'sz_notifications',
			'table_name_meta' => $sz->table_prefix . 'sz_notifications_meta',
		);

		// Metadata tables for notifications component.
		$meta_tables = array(
			'notification' => $sz->table_prefix . 'sz_notifications_meta',
		);

		// All globals for the notifications component.
		// Note that global_tables is included in this array.
		$args = array(
			'slug'          => SZ_NOTIFICATIONS_SLUG,
			'has_directory' => false,
			'search_string' => __( 'Search Notifications...', 'sportszone' ),
			'global_tables' => $global_tables,
			'meta_tables'   => $meta_tables
		);

		parent::setup_globals( $args );
	}

	/**
	 * Set up component navigation.
	 *
	 * @since 1.9.0
	 *
	 * @see SZ_Component::setup_nav() for a description of arguments.
	 *
	 * @param array $main_nav Optional. See SZ_Component::setup_nav() for
	 *                        description.
	 * @param array $sub_nav  Optional. See SZ_Component::setup_nav() for
	 *                        description.
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

		$access             = sz_core_can_edit_settings();
		$slug               = sz_get_notifications_slug();
		$notifications_link = trailingslashit( $user_domain . $slug );

		// Only grab count if we're on a user page and current user has access.
		if ( sz_is_user() && sz_user_has_access() ) {
			$count    = sz_notifications_get_unread_notification_count( sz_displayed_user_id() );
			$class    = ( 0 === $count ) ? 'no-count' : 'count';
			$nav_name = sprintf(
				/* translators: %s: Unread notification count for the current user */
				_x( 'Notifications %s', 'Profile screen nav', 'sportszone' ),
				sprintf(
					'<span class="%s">%s</span>',
					esc_attr( $class ),
					sz_core_number_format( $count )
				)
			);
		} else {
			$nav_name = _x( 'Notifications', 'Profile screen nav', 'sportszone' );
		}

		// Add 'Notifications' to the main navigation.
		$main_nav = array(
			'name'                    => $nav_name,
			'slug'                    => $slug,
			'position'                => 30,
			'show_for_displayed_user' => $access,
			'screen_function'         => 'sz_notifications_screen_unread',
			'default_subnav_slug'     => 'unread',
			'item_css_id'             => $this->id,
		);

		// Add the subnav items to the notifications nav item.
		$sub_nav[] = array(
			'name'            => _x( 'Unread', 'Notification screen nav', 'sportszone' ),
			'slug'            => 'unread',
			'parent_url'      => $notifications_link,
			'parent_slug'     => $slug,
			'screen_function' => 'sz_notifications_screen_unread',
			'position'        => 10,
			'item_css_id'     => 'notifications-my-notifications',
			'user_has_access' => $access,
		);

		$sub_nav[] = array(
			'name'            => _x( 'Read', 'Notification screen nav', 'sportszone' ),
			'slug'            => 'read',
			'parent_url'      => $notifications_link,
			'parent_slug'     => $slug,
			'screen_function' => 'sz_notifications_screen_read',
			'position'        => 20,
			'user_has_access' => $access,
		);

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up the component entries in the WordPress Admin Bar.
	 *
	 * @since 1.9.0
	 *
	 * @see SZ_Component::setup_nav() for a description of the $wp_admin_nav
	 *      parameter array.
	 *
	 * @param array $wp_admin_nav See SZ_Component::setup_admin_bar() for a
	 *                            description.
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {

		// Menus for logged in user.
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables.
			$notifications_link = trailingslashit( sz_loggedin_user_domain() . sz_get_notifications_slug() );

			// Pending notification requests.
			$count = sz_notifications_get_unread_notification_count( sz_loggedin_user_id() );
			if ( ! empty( $count ) ) {
				$title = sprintf(
					/* translators: %s: Unread notification count for the current user */
					_x( 'Notifications %s', 'My Account Notification pending', 'sportszone' ),
					'<span class="count">' . sz_core_number_format( $count ) . '</span>'
				);
				$unread = sprintf(
					/* translators: %s: Unread notification count for the current user */
					_x( 'Unread %s', 'My Account Notification pending', 'sportszone' ),
					'<span class="count">' . sz_core_number_format( $count ) . '</span>'
				);
			} else {
				$title  = _x( 'Notifications', 'My Account Notification',         'sportszone' );
				$unread = _x( 'Unread',        'My Account Notification sub nav', 'sportszone' );
			}

			// Add the "My Account" sub menus.
			$wp_admin_nav[] = array(
				'parent' => sportszone()->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => $title,
				'href'   => $notifications_link
			);

			// Unread.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-unread',
				'title'    => $unread,
				'href'     => $notifications_link,
				'position' => 10
			);

			// Read.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-read',
				'title'    => _x( 'Read', 'My Account Notification sub nav', 'sportszone' ),
				'href'     => trailingslashit( $notifications_link . 'read' ),
				'position' => 20
			);
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Set up the title for pages and <title>.
	 *
	 * @since 1.9.0
	 */
	public function setup_title() {

		// Adjust title.
		if ( sz_is_notifications_component() ) {
			$sz = sportszone();

			if ( sz_is_my_profile() ) {
				$sz->sz_options_title = __( 'Notifications', 'sportszone' );
			} else {
				$sz->sz_options_avatar = sz_core_fetch_avatar( array(
					'item_id' => sz_displayed_user_id(),
					'type'    => 'thumb',
					'alt'     => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_get_displayed_user_fullname() )
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
			'sz_notifications',
			'notification_meta'
		) );

		parent::setup_cache_groups();
	}
}
