<?php
/**
 * Messages: Delete action handler
 *
 * @package SportsZone
 * @subpackage MessageActions
 * @since 3.0.0
 */

/**
 * Process a request to delete a message.
 *
 * @return bool False on failure.
 */
function messages_action_delete_message() {

	if ( ! sz_is_messages_component() || sz_is_current_action( 'notices' ) || ! sz_is_action_variable( 'delete', 0 ) ) {
		return false;
	}

	$thread_id = sz_action_variable( 1 );

	if ( !$thread_id || !is_numeric( $thread_id ) || !messages_check_thread_access( $thread_id ) ) {
		sz_core_redirect( trailingslashit( sz_displayed_user_domain() . sz_get_messages_slug() . '/' . sz_current_action() ) );
	} else {
		if ( ! check_admin_referer( 'messages_delete_thread' ) ) {
			return false;
		}

		// Delete message.
		if ( !messages_delete_thread( $thread_id ) ) {
			sz_core_add_message( __('There was an error deleting that message.', 'sportszone'), 'error' );
		} else {
			sz_core_add_message( __('Message deleted.', 'sportszone') );
		}
		sz_core_redirect( trailingslashit( sz_displayed_user_domain() . sz_get_messages_slug() . '/' . sz_current_action() ) );
	}
}
add_action( 'sz_actions', 'messages_action_delete_message' );