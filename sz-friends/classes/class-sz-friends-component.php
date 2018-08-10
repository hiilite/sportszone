<?php
/**
 * SportsZone Friends Streams Loader.
 *
 * The friends component is for users to create relationships with each other.
 *
 * @package SportsZone
 * @subpackage Friends
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Defines the SportsZone Friends Component.
 *
 * @since 1.5.0
 */
class SZ_Friends_Component extends SZ_Component {

	/**
	 * Start the friends component creation process.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		parent::start(
			'friends',
			_x( 'Friend Connections', 'Friends screen page <title>', 'sportszone' ),
			sportszone()->plugin_dir,
			array(
				'adminbar_myaccount_order' => 60
			)
		);
	}

	/**
	 * Include sz-friends files.
	 *
	 * @since 1.5.0
	 *
	 * @see SZ_Component::includes() for description of parameters.
	 *
	 * @param array $includes See {@link SZ_Component::includes()}.
	 */
	public function includes( $includes = array() ) {
		$includes = array(
			'cache',
			'filters',
			'template',
			'functions',
			'widgets',
		);

		// Conditional includes.
		if ( sz_is_active( 'activity' ) ) {
			$includes[] = 'activity';
		}
		if ( sz_is_active( 'notifications' ) ) {
			$includes[] = 'notifications';
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

		// Friends.
		if ( sz_is_user_friends() ) {
			// Authenticated actions.
			if ( is_user_logged_in() &&
				in_array( sz_current_action(), array( 'add-friend', 'remove-friend' ), true )
			) {
				require $this->path . 'sz-friends/actions/' . sz_current_action() . '.php';
			}

			// User nav.
			require $this->path . 'sz-friends/screens/my-friends.php';
			if ( is_user_logged_in() && sz_is_user_friend_requests() ) {
				require $this->path . 'sz-friends/screens/requests.php';
			}
		}
	}

	/**
	 * Set up sz-friends global settings.
	 *
	 * The SZ_FRIENDS_SLUG constant is deprecated, and only used here for
	 * backwards compatibility.
	 *
	 * @since 1.5.0
	 *
	 * @see SZ_Component::setup_globals() for description of parameters.
	 *
	 * @param array $args See {@link SZ_Component::setup_globals()}.
	 */
	public function setup_globals( $args = array() ) {
		$sz = sportszone();

		// Deprecated. Do not use.
		// Defined conditionally to support unit tests.
		if ( ! defined( 'SZ_FRIENDS_DB_VERSION' ) ) {
			define( 'SZ_FRIENDS_DB_VERSION', '1800' );
		}

		// Define a slug, if necessary.
		if ( ! defined( 'SZ_FRIENDS_SLUG' ) ) {
			define( 'SZ_FRIENDS_SLUG', $this->id );
		}

		// Global tables for the friends component.
		$global_tables = array(
			'table_name'      => $sz->table_prefix . 'sz_friends',
			'table_name_meta' => $sz->table_prefix . 'sz_friends_meta',
		);

		// All globals for the friends component.
		// Note that global_tables is included in this array.
		$args = array(
			'slug'                  => SZ_FRIENDS_SLUG,
			'has_directory'         => false,
			'search_string'         => __( 'Search Friends...', 'sportszone' ),
			'notification_callback' => 'friends_format_notifications',
			'global_tables'         => $global_tables
		);

		parent::setup_globals( $args );
	}

	/**
	 * Set up component navigation.
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

		// Determine user to use.
		if ( sz_displayed_user_domain() ) {
			$user_domain = sz_displayed_user_domain();
		} elseif ( sz_loggedin_user_domain() ) {
			$user_domain = sz_loggedin_user_domain();
		} else {
			return;
		}

		$access       = sz_core_can_edit_settings();
		$slug         = sz_get_friends_slug();
		$friends_link = trailingslashit( $user_domain . $slug );

		// Add 'Friends' to the main navigation.
		$count = friends_get_total_friend_count();
		$class = ( 0 === $count ) ? 'no-count' : 'count';

		$main_nav_name = sprintf(
			/* translators: %s: Friend count for the current user */
			__( 'Friends %s', 'sportszone' ),
			sprintf(
				'<span class="%s">%s</span>',
				esc_attr( $class ),
				sz_core_number_format( $count )
			)
		);

		$main_nav = array(
			'name'                => $main_nav_name,
			'slug'                => $slug,
			'position'            => 60,
			'screen_function'     => 'friends_screen_my_friends',
			'default_subnav_slug' => 'my-friends',
			'item_css_id'         => $this->id
		);

		// Add the subnav items to the friends nav item.
		$sub_nav[] = array(
			'name'            => _x( 'Friendships', 'Friends screen sub nav', 'sportszone' ),
			'slug'            => 'my-friends',
			'parent_url'      => $friends_link,
			'parent_slug'     => $slug,
			'screen_function' => 'friends_screen_my_friends',
			'position'        => 10,
			'item_css_id'     => 'friends-my-friends'
		);

		$sub_nav[] = array(
			'name'            => _x( 'Requests', 'Friends screen sub nav', 'sportszone' ),
			'slug'            => 'requests',
			'parent_url'      => $friends_link,
			'parent_slug'     => $slug,
			'screen_function' => 'friends_screen_requests',
			'position'        => 20,
			'user_has_access' => $access
		);

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up sz-friends integration with the WordPress admin bar.
	 *
	 * @since 1.5.0
	 *
	 * @see SZ_Component::setup_admin_bar() for a description of arguments.
	 *
	 * @param array $wp_admin_nav See SZ_Component::setup_admin_bar()
	 *                            for description.
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {

		// Menus for logged in user.
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables.
			$friends_link = trailingslashit( sz_loggedin_user_domain() . sz_get_friends_slug() );

			// Pending friend requests.
			$count = count( friends_get_friendship_request_user_ids( sz_loggedin_user_id() ) );
			if ( !empty( $count ) ) {
				$title = sprintf(
					/* translators: %s: Pending friend request count for the current user */
					_x( 'Friends %s', 'My Account Friends menu', 'sportszone' ),
					'<span class="count">' . sz_core_number_format( $count ) . '</span>'
				);
				$pending = sprintf(
					/* translators: %s: Pending friend request count for the current user */
					_x( 'Pending Requests %s', 'My Account Friends menu sub nav', 'sportszone' ),
					'<span class="count">' . sz_core_number_format( $count ) . '</span>'
				);
			} else {
				$title   = _x( 'Friends',            'My Account Friends menu',         'sportszone' );
				$pending = _x( 'No Pending Requests','My Account Friends menu sub nav', 'sportszone' );
			}

			// Add the "My Account" sub menus.
			$wp_admin_nav[] = array(
				'parent' => sportszone()->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => $title,
				'href'   => $friends_link
			);

			// My Friends.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-friendships',
				'title'    => _x( 'Friendships', 'My Account Friends menu sub nav', 'sportszone' ),
				'href'     => $friends_link,
				'position' => 10
			);

			// Requests.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-requests',
				'title'    => $pending,
				'href'     => trailingslashit( $friends_link . 'requests' ),
				'position' => 20
			);
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Set up the title for pages and <title>.
	 *
	 * @since 1.5.0
	 */
	public function setup_title() {

		// Adjust title.
		if ( sz_is_friends_component() ) {
			$sz = sportszone();

			if ( sz_is_my_profile() ) {
				$sz->sz_options_title = __( 'Friendships', 'sportszone' );
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
			'sz_friends_requests',
			'sz_friends_friendships', // Individual friendship objects are cached here by ID.
			'sz_friends_friendships_for_user' // All friendship IDs for a single user.
		) );

		parent::setup_cache_groups();
	}
}
