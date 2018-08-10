<?php
/**
 * Messages: Bulk-delete action handler
 *
 * @package SportsZone
 * @subpackage MessageActions
 * @since 3.0.0
 */

/**
 * Process a request to bulk delete messages.
 *
 * @return bool False on failure.
 */
function messages_action_bulk_delete() {

	if ( ! sz_is_messages_component() || ! sz_is_action_variable( 'bulk-delete', 0 ) ) {
		return false;
	}

	$thread_ids = $_POST['thread_ids'];

	if ( !$thread_ids || !messages_check_thread_access( $thread_ids ) ) {
		sz_core_redirect( trailingslashit( sz_displayed_user_domain() . sz_get_messages_slug() . '/' . sz_current_action() ) );
	} else {
		if ( !check_admin_referer( 'messages_delete_thread' ) ) {
			return false;
		}

		if ( !messages_delete_thread( $thread_ids ) ) {
			sz_core_add_message( __('There was an error deleting messages.', 'sportszone'), 'error' );
		} else {
			sz_core_add_message( __('Messages deleted.', 'sportszone') );
		}

		sz_core_redirect( trailingslashit( sz_displayed_user_domain() . sz_get_messages_slug() . '/' . sz_current_action() ) );
	}
}
add_action( 'sz_actions', 'messages_action_bulk_delete' );