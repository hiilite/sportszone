<?php
/**
 * Notifications: User's "Notifications" screen handler
 *
 * @package SportsZone
 * @subpackage NotificationsScreens
 * @since 3.0.0
 */

/**
 * Catch and route the 'unread' notifications screen.
 *
 * @since 1.9.0
 */
function sz_notifications_screen_unread() {

	/**
	 * Fires right before the loading of the notifications unread screen template file.
	 *
	 * @since 1.9.0
	 */
	do_action( 'sz_notifications_screen_unread' );

	/**
	 * Filters the template to load for the notifications unread screen.
	 *
	 * @since 1.9.0
	 *
	 * @param string $template Path to the notifications unread template to load.
	 */
	sz_core_load_template( apply_filters( 'sz_notifications_template_unread', 'members/single/home' ) );
}

/**
 * Handle marking single notifications as read.
 *
 * @since 1.9.0
 *
 * @return bool
 */
function sz_notifications_action_mark_read() {

	// Bail if not the unread screen.
	if ( ! sz_is_notifications_component() || ! sz_is_current_action( 'unread' ) ) {
		return false;
	}

	// Get the action.
	$action = !empty( $_GET['action']          ) ? $_GET['action']          : '';
	$nonce  = !empty( $_GET['_wpnonce']        ) ? $_GET['_wpnonce']        : '';
	$id     = !empty( $_GET['notification_id'] ) ? $_GET['notification_id'] : '';

	// Bail if no action or no ID.
	if ( ( 'read' !== $action ) || empty( $id ) || empty( $nonce ) ) {
		return false;
	}

	// Check the nonce and mark the notification.
	if ( sz_verify_nonce_request( 'sz_notification_mark_read_' . $id ) && sz_notifications_mark_notification( $id, false ) ) {
		sz_core_add_message( __( 'Notification successfully marked read.',         'sportszone' )          );
	} else {
		sz_core_add_message( __( 'There was a problem marking that notification.', 'sportszone' ), 'error' );
	}

	// Redirect.
	sz_core_redirect( sz_displayed_user_domain() . sz_get_notifications_slug() . '/unread/' );
}
add_action( 'sz_actions', 'sz_notifications_action_mark_read' );