<?php
/**
 * SportsZone Messages Loader.
 *
 * A private messages component, for users to send messages to each other.
 *
 * @package SportsZone
 * @subpackage MessagesLoader
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Implementation of SZ_Component for the Messages component.
 *
 * @since 1.5.0
 */
class SZ_Messages_Component extends SZ_Component {

	/**
	 * If this is true, the Message autocomplete will return friends only, unless
	 * this is set to false, in which any matching users will be returned.
	 *
	 * @since 1.5.0
	 * @var bool
	 */
	public $autocomplete_all;

	/**
	 * Start the messages component creation process.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		parent::start(
			'messages',
			__( 'Private Messages', 'sportszone' ),
			sportszone()->plugin_dir,
			array(
				'adminbar_myaccount_order' => 50,
				'features'                 => array( 'star' )
			)
		);
	}

	/**
	 * Include files.
	 *
	 * @since 1.5.0
	 *
	 * @param array $includes See {SZ_Component::includes()} for details.
	 */
	public function includes( $includes = array() ) {

		// Files to include.
		$includes = array(
			'cssjs',
			'cache',
			'filters',
			'template',
			'functions',
			'widgets',
		);

		// Conditional includes.
		if ( sz_is_active( 'notifications' ) ) {
			$includes[] = 'notifications';
		}
		if ( sz_is_active( $this->id, 'star' ) ) {
			$includes[] = 'star';
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

		if ( sz_is_messages_component() ) {
			// Authenticated actions.
			if ( is_user_logged_in() &&
				in_array( sz_current_action(), array( 'compose', 'notices', 'view' ), true )
			) {
				require $this->path . 'sz-messages/actions/' . sz_current_action() . '.php';
			}

			// Authenticated action variables.
			if ( is_user_logged_in() && sz_action_variable( 0 ) &&
				in_array( sz_action_variable( 0 ), array( 'delete', 'read', 'unread', 'bulk-manage', 'bulk-delete' ), true )
			) {
				require $this->path . 'sz-messages/actions/' . sz_action_variable( 0 ) . '.php';
			}

			// Authenticated actions - Star.
			if ( is_user_logged_in() && sz_is_active( $this->id, 'star' ) ) {
				// Single action.
				if ( in_array( sz_current_action(), array( 'star', 'unstar' ), true ) ) {
					require $this->path . 'sz-messages/actions/star.php';
				}

				// Bulk-manage.
				if ( sz_is_action_variable( 'bulk-manage' ) ) {
					require $this->path . 'sz-messages/actions/bulk-manage-star.php';
				}
			}

			// Screens - User profile integration.
			if ( sz_is_user() ) {
				require $this->path . 'sz-messages/screens/inbox.php';

				/*
				 * Nav items.
				 *
				 * 'view' is not a registered nav item, but we add a screen handler manually.
				 */
				if ( sz_is_user_messages() && in_array( sz_current_action(), array( 'sentbox', 'compose', 'notices', 'view' ), true ) ) {
					require $this->path . 'sz-messages/screens/' . sz_current_action() . '.php';
				}

				// Nav item - Starred.
				if ( sz_is_active( $this->id, 'star' ) && sz_is_current_action( sz_get_messages_starred_slug() ) ) {
					require $this->path . 'sz-messages/screens/starred.php';
				}
			}
		}
	}

	/**
	 * Set up globals for the Messages component.
	 *
	 * The SZ_MESSAGES_SLUG constant is deprecated, and only used here for
	 * backwards compatibility.
	 *
	 * @since 1.5.0
	 *
	 * @param array $args Not used.
	 */
	public function setup_globals( $args = array() ) {
		$sz = sportszone();

		// Define a slug, if necessary.
		if ( ! defined( 'SZ_MESSAGES_SLUG' ) ) {
			define( 'SZ_MESSAGES_SLUG', $this->id );
		}

		// Global tables for messaging component.
		$global_tables = array(
			'table_name_notices'    => $sz->table_prefix . 'sz_messages_notices',
			'table_name_messages'   => $sz->table_prefix . 'sz_messages_messages',
			'table_name_recipients' => $sz->table_prefix . 'sz_messages_recipients',
			'table_name_meta'       => $sz->table_prefix . 'sz_messages_meta',
		);

		// Metadata tables for messaging component.
		$meta_tables = array(
			'message' => $sz->table_prefix . 'sz_messages_meta',
		);

		$this->autocomplete_all = defined( 'SZ_MESSAGES_AUTOCOMPLETE_ALL' );

		// All globals for messaging component.
		// Note that global_tables is included in this array.
		parent::setup_globals( array(
			'slug'                  => SZ_MESSAGES_SLUG,
			'has_directory'         => false,
			'notification_callback' => 'messages_format_notifications',
			'search_string'         => __( 'Search Messages...', 'sportszone' ),
			'global_tables'         => $global_tables,
			'meta_tables'           => $meta_tables
		) );
	}

	/**
	 * Set up navigation for user pages.
	 *
	 * @param array $main_nav See {SZ_Component::setup_nav()} for details.
	 * @param array $sub_nav  See {SZ_Component::setup_nav()} for details.
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
		$slug          = sz_get_messages_slug();
		$messages_link = trailingslashit( $user_domain . $slug );

		// Only grab count if we're on a user page and current user has access.
		if ( sz_is_user() && sz_user_has_access() ) {
			$count    = sz_get_total_unread_messages_count( sz_displayed_user_id() );
			$class    = ( 0 === $count ) ? 'no-count' : 'count';
			$nav_name = sprintf(
				/* translators: %s: Unread message count for the current user */
				__( 'Messages %s', 'sportszone' ),
				sprintf(
					'<span class="%s">%s</span>',
					esc_attr( $class ),
					sz_core_number_format( $count )
				)
			);
		} else {
			$nav_name = __( 'Messages', 'sportszone' );
		}

		// Add 'Messages' to the main navigation.
		$main_nav = array(
			'name'                    => $nav_name,
			'slug'                    => $slug,
			'position'                => 50,
			'show_for_displayed_user' => $access,
			'screen_function'         => 'messages_screen_inbox',
			'default_subnav_slug'     => 'inbox',
			'item_css_id'             => $this->id
		);

		// Add the subnav items to the profile.
		$sub_nav[] = array(
			'name'            => __( 'Inbox', 'sportszone' ),
			'slug'            => 'inbox',
			'parent_url'      => $messages_link,
			'parent_slug'     => $slug,
			'screen_function' => 'messages_screen_inbox',
			'position'        => 10,
			'user_has_access' => $access
		);

		if ( sz_is_active( $this->id, 'star' ) ) {
			$sub_nav[] = array(
				'name'            => __( 'Starred', 'sportszone' ),
				'slug'            => sz_get_messages_starred_slug(),
				'parent_url'      => $messages_link,
				'parent_slug'     => $slug,
				'screen_function' => 'sz_messages_star_screen',
				'position'        => 11,
				'user_has_access' => $access
			);
		}

		$sub_nav[] = array(
			'name'            => __( 'Sent', 'sportszone' ),
			'slug'            => 'sentbox',
			'parent_url'      => $messages_link,
			'parent_slug'     => $slug,
			'screen_function' => 'messages_screen_sentbox',
			'position'        => 20,
			'user_has_access' => $access
		);

		// Show certain screens only if the current user is the displayed user.
		if ( sz_is_my_profile() ) {

			// Show "Compose" on the logged-in user's profile only.
			$sub_nav[] = array(
				'name'            => __( 'Compose', 'sportszone' ),
				'slug'            => 'compose',
				'parent_url'      => $messages_link,
				'parent_slug'     => $slug,
				'screen_function' => 'messages_screen_compose',
				'position'        => 30,
				'user_has_access' => $access
			);

			/*
			 * Show "Notices" on the logged-in user's profile only
			 * and then only if the user can create notices.
			 */
			if ( sz_current_user_can( 'sz_moderate' ) ) {
				$sub_nav[] = array(
					'name'            => __( 'Notices', 'sportszone' ),
					'slug'            => 'notices',
					'parent_url'      => $messages_link,
					'parent_slug'     => $slug,
					'screen_function' => 'messages_screen_notices',
					'position'        => 90,
					'user_has_access' => true
				);
			}
		}

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up the Toolbar.
	 *
	 * @param array $wp_admin_nav See {SZ_Component::setup_admin_bar()} for details.
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {

		// Menus for logged in user.
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables.
			$messages_link = trailingslashit( sz_loggedin_user_domain() . sz_get_messages_slug() );

			// Unread message count.
			$count = messages_get_unread_count( sz_loggedin_user_id() );
			if ( !empty( $count ) ) {
				$title = sprintf(
					/* translators: %s: Unread message count for the current user */
					__( 'Messages %s', 'sportszone' ),
					'<span class="count">' . sz_core_number_format( $count ) . '</span>'
				);
				$inbox = sprintf(
					/* translators: %s: Unread message count for the current user */
					__( 'Inbox %s', 'sportszone' ),
					'<span class="count">' . sz_core_number_format( $count ) . '</span>'
				);
			} else {
				$title = __( 'Messages', 'sportszone' );
				$inbox = __( 'Inbox',    'sportszone' );
			}

			// Add main Messages menu.
			$wp_admin_nav[] = array(
				'parent' => sportszone()->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => $title,
				'href'   => $messages_link
			);

			// Inbox.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-inbox',
				'title'    => $inbox,
				'href'     => $messages_link,
				'position' => 10
			);

			// Starred.
			if ( sz_is_active( $this->id, 'star' ) ) {
				$wp_admin_nav[] = array(
					'parent'   => 'my-account-' . $this->id,
					'id'       => 'my-account-' . $this->id . '-starred',
					'title'    => __( 'Starred', 'sportszone' ),
					'href'     => trailingslashit( $messages_link . sz_get_messages_starred_slug() ),
					'position' => 11
				);
			}

			// Sent Messages.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-sentbox',
				'title'    => __( 'Sent', 'sportszone' ),
				'href'     => trailingslashit( $messages_link . 'sentbox' ),
				'position' => 20
			);

			// Compose Message.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-compose',
				'title'    => __( 'Compose', 'sportszone' ),
				'href'     => trailingslashit( $messages_link . 'compose' ),
				'position' => 30
			);

			// Site Wide Notices.
			if ( sz_current_user_can( 'sz_moderate' ) ) {
				$wp_admin_nav[] = array(
					'parent'   => 'my-account-' . $this->id,
					'id'       => 'my-account-' . $this->id . '-notices',
					'title'    => __( 'Site Notices', 'sportszone' ),
					'href'     => trailingslashit( $messages_link . 'notices' ),
					'position' => 90
				);
			}
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Set up the title for pages and <title>.
	 */
	public function setup_title() {

		if ( sz_is_messages_component() ) {
			$sz = sportszone();

			if ( sz_is_my_profile() ) {
				$sz->sz_options_title = __( 'My Messages', 'sportszone' );
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
	 * Setup cache groups
	 *
	 * @since 2.2.0
	 */
	public function setup_cache_groups() {

		// Global groups.
		wp_cache_add_global_groups( array(
			'sz_messages',
			'sz_messages_threads',
			'sz_messages_unread_count',
			'message_meta'
		) );

		parent::setup_cache_groups();
	}
}
