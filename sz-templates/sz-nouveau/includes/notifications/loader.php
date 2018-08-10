<?php
/**
 * BP Nouveau Notifications
 *
 * @since 3.0.0
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Notifications Loader class
 *
 * @since 3.0.0
 */
class SZ_Nouveau_Notifications {
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
		$this->dir = dirname( __FILE__ );
	}

	/**
	 * Include needed files
	 *
	 * @since 3.0.0
	 */
	protected function includes() {
		$dir = trailingslashit( $this->dir );

		require "{$dir}functions.php";
		require "{$dir}template-tags.php";
	}

	/**
	 * Register do_action() hooks
	 *
	 * @since 3.0.0
	 */
	protected function setup_actions() {
		add_action( 'sz_init', 'sz_nouveau_notifications_init_filters', 20 );
		add_action( 'sz_nouveau_enqueue_scripts', 'sz_nouveau_notifications_enqueue_scripts' );

		$ajax_actions = array(
			array(
				'notifications_filter' => array(
					'function' => 'sz_nouveau_ajax_object_template_loader',
					'nopriv'   => false,
				),
			),
		);

		foreach ( $ajax_actions as $ajax_action ) {
			$action = key( $ajax_action );

			add_action( 'wp_ajax_' . $action, $ajax_action[ $action ]['function'] );

			if ( ! empty( $ajax_action[ $action ]['nopriv'] ) ) {
				add_action( 'wp_ajax_nopriv_' . $action, $ajax_action[ $action ]['function'] );
			}
		}
	}

	/**
	 * Register add_filter() hooks
	 *
	 * @since 3.0.0
	 */
	protected function setup_filters() {
		add_filter( 'sz_nouveau_register_scripts', 'sz_nouveau_notifications_register_scripts', 10, 1 );
		add_filter( 'sz_get_the_notification_mark_unread_link', 'sz_nouveau_notifications_mark_unread_link', 10, 1 );
		add_filter( 'sz_get_the_notification_mark_read_link', 'sz_nouveau_notifications_mark_read_link', 10, 1 );
		add_filter( 'sz_get_the_notification_delete_link', 'sz_nouveau_notifications_delete_link', 10, 1 );
	}
}

/**
 * Launch the Notifications loader class.
 *
 * @since 3.0.0
 */
function sz_nouveau_notifications( $sz_nouveau = null ) {
	if ( is_null( $sz_nouveau ) ) {
		return;
	}

	$sz_nouveau->notifications = new SZ_Nouveau_Notifications();
}
add_action( 'sz_nouveau_includes', 'sz_nouveau_notifications', 10, 1 );
