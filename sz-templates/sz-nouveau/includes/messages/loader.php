<?php
/**
 * BP Nouveau Messages
 *
 * @since 3.0.0
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Messages Loader class
 *
 * @since 3.0.0
 */
class SZ_Nouveau_Messages {
	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
		$this->setup_filters();
	}

	/**
	 * Globals
	 *
	 * @since 3.0.0
	 */
	protected function setup_globals() {
		$this->dir = trailingslashit( dirname( __FILE__ ) );
	}

	/**
	 * Include needed files
	 *
	 * @since 3.0.0
	 */
	protected function includes() {
		require $this->dir . 'functions.php';
		require $this->dir . 'template-tags.php';

		// Test suite requires the AJAX functions early.
		if ( function_exists( 'tests_add_filter' ) ) {
			require $this->dir . 'ajax.php';

		// Load AJAX code only on AJAX requests.
		} else {
			add_action( 'admin_init', function() {
				if ( defined( 'DOING_AJAX' ) && true === DOING_AJAX && 0 === strpos( $_REQUEST['action'], 'messages_' ) ) {
					require $this->dir . 'ajax.php';
				}
			} );
		}
	}

	/**
	 * Register do_action() hooks
	 *
	 * @since 3.0.0
	 */
	protected function setup_actions() {
		// Notices
		add_action( 'widgets_init', 'sz_nouveau_unregister_notices_widget' );
		add_action( 'sz_init', 'sz_nouveau_push_sitewide_notices', 99 );

		// Messages
		add_action( 'sz_messages_setup_nav', 'sz_nouveau_messages_adjust_nav' );

		// Remove deprecated scripts
		remove_action( 'sz_enqueue_scripts', 'messages_add_autocomplete_js' );

		// Enqueue the scripts for the new UI
		add_action( 'sz_nouveau_enqueue_scripts', 'sz_nouveau_messages_enqueue_scripts' );

		// Register the Messages Notifications filters
		add_action( 'sz_nouveau_notifications_init_filters', 'sz_nouveau_messages_notification_filters' );
	}

	/**
	 * Register add_filter() hooks
	 *
	 * @since 3.0.0
	 */
	protected function setup_filters() {
		// Enqueue specific styles
		add_filter( 'sz_nouveau_enqueue_styles', 'sz_nouveau_messages_enqueue_styles', 10, 1 );

		// Register messages scripts
		add_filter( 'sz_nouveau_register_scripts', 'sz_nouveau_messages_register_scripts', 10, 1 );

		// Localize Scripts
		add_filter( 'sz_core_get_js_strings', 'sz_nouveau_messages_localize_scripts', 10, 1 );

		// Notices
		add_filter( 'sz_messages_single_new_message_notification', 'sz_nouveau_format_notice_notification_for_user', 10, 1 );
		add_filter( 'sz_notifications_get_all_notifications_for_user', 'sz_nouveau_add_notice_notification_for_user', 10, 2 );

		// Messages
		add_filter( 'sz_messages_admin_nav', 'sz_nouveau_messages_adjust_admin_nav', 10, 1 );
	}
}

/**
 * Launch the Messages loader class.
 *
 * @since 3.0.0
 */
function sz_nouveau_messages( $sz_nouveau = null ) {
	if ( is_null( $sz_nouveau ) ) {
		return;
	}

	$sz_nouveau->messages = new SZ_Nouveau_Messages();
}
add_action( 'sz_nouveau_includes', 'sz_nouveau_messages', 10, 1 );
