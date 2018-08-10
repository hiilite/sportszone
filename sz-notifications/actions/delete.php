<?php
/**
 * Notifications: Delete action handler
 *
 * @package SportsZone
 * @subpackage NotificationsActions
 * @since 3.0.0
 */

/**
 * Handle deleting single notifications.
 *
 * @since 1.9.0
 *
 * @return bool
 */
function sz_notifications_action_delete() {

	// Bail if not the read or unread screen.
	if ( ! sz_is_notifications_component() || ! ( sz_is_current_action( 'read' ) || sz_is_current_action( 'unread' ) ) ) {
		return false;
	}

	// Get the action.
	$action = !empty( $_GET['action']          ) ? $_GET['action']          : '';
	$nonce  = !empty( $_GET['_wpnonce']        ) ? $_GET['_wpnonce']        : '';
	$id     = !empty( $_GET['notification_id'] ) ? $_GET['notification_id'] : '';

	// Bail if no action or no ID.
	if ( ( 'delete' !== $action ) || empty( $id ) || empty( $nonce ) ) {
		return false;
	}

	// Check the nonce and delete the notification.
	if ( sz_verify_nonce_request( 'sz_notification_delete_' . $id ) && sz_notifications_delete_notification( $id ) ) {
		sz_core_add_message( __( 'Notification successfully deleted.',              'sportszone' )          );
	} else {
		sz_core_add_message( __( 'There was a problem deleting that notification.', 'sportszone' ), 'error' );
	}

	// Redirect.
	sz_core_redirect( sz_displayed_user_domain() . sz_get_notifications_slug() . '/' . sz_current_action() . '/' );
}
add_action( 'sz_actions', 'sz_notifications_action_delete' );