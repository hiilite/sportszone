<?php
/**
 * Messages: Unread action handler
 *
 * @package SportsZone
 * @subpackage MessageActions
 * @since 3.0.0
 */

/**
 * Handle marking a single message thread as unread.
 *
 * @since 2.2.0
 *
 * @return false|null Returns false on failure. Otherwise redirects back to the
 *                   message box URL.
 */
function sz_messages_action_mark_unread() {

	if ( ! sz_is_messages_component() || sz_is_current_action( 'notices' ) || ! sz_is_action_variable( 'unread', 0 ) ) {
		return false;
	}

	$action = ! empty( $_GET['action'] ) ? $_GET['action'] : '';
	$nonce  = ! empty( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '';
	$id     = ! empty( $_GET['message_id'] ) ? intval( $_GET['message_id'] ) : '';

	// Bail if no action or no ID.
	if ( 'unread' !== $action || empty( $id ) || empty( $nonce ) ) {
		return false;
	}

	// Check the nonce.
	if ( ! sz_verify_nonce_request( 'sz_message_thread_mark_unread_' . $id ) ) {
		return false;
	}

	// Check access to the message and mark unread.
	if ( messages_check_thread_access( $id ) || sz_current_user_can( 'sz_moderate' ) ) {
		messages_mark_thread_unread( $id );
		sz_core_add_message( __( 'Message marked unread.', 'sportszone' ) );
	} else {
		sz_core_add_message( __( 'There was a problem marking that message.', 'sportszone' ), 'error' );
	}

	// Redirect back to the message box URL.
	sz_core_redirect( sz_displayed_user_domain() . sz_get_messages_slug() . '/' . sz_current_action() );
}
add_action( 'sz_actions', 'sz_messages_action_mark_unread' );