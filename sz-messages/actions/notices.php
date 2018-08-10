<?php
/**
 * Messages: Edit notice action handler
 *
 * @package SportsZone
 * @subpackage MessageActions
 * @since 3.0.0
 */

/**
 * Handle editing of sitewide notices.
 *
 * @since 2.4.0 This function was split from messages_screen_notices(). See #6505.
 *
 * @return boolean
 */
function sz_messages_action_edit_notice() {

	// Bail if not viewing a single notice URL.
	if ( ! sz_is_messages_component() || ! sz_is_current_action( 'notices' ) ) {
		return false;
	}

	// Get the notice ID (1|2|3).
	$notice_id = sz_action_variable( 1 );

	// Bail if notice ID is not numeric.
	if ( empty( $notice_id ) || ! is_numeric( $notice_id ) ) {
		return false;
	}

	// Bail if the current user doesn't have administrator privileges.
	if ( ! sz_current_user_can( 'sz_moderate' ) ) {
		return false;
	}

	// Get the action (deactivate|activate|delete).
	$action = sanitize_key( sz_action_variable( 0 ) );

	// Check the nonce.
	check_admin_referer( "messages_{$action}_notice" );

	// Get the notice from database.
	$notice   = new SZ_Messages_Notice( $notice_id );
	$success  = false;
	$feedback = '';

	// Take action.
	switch ( $action ) {

		// Deactivate.
		case 'deactivate' :
			$success  = $notice->deactivate();
			$feedback = true === $success
				? __( 'Notice deactivated successfully.',              'sportszone' )
				: __( 'There was a problem deactivating that notice.', 'sportszone' );
			break;

		// Activate.
		case 'activate' :
			$success  = $notice->activate();
			$feedback = true === $success
				? __( 'Notice activated successfully.',              'sportszone' )
				: __( 'There was a problem activating that notice.', 'sportszone' );
			break;

		// Delete.
		case 'delete' :
			$success  = $notice->delete();
			$feedback = true === $success
				? __( 'Notice deleted successfully.',              'sportszone' )
				: __( 'There was a problem deleting that notice.', 'sportszone' );
			break;
	}

	// Feedback.
	if ( ! empty( $feedback ) ) {

		// Determine message type.
		$type = ( true === $success )
			? 'success'
			: 'error';

		// Add feedback message.
		sz_core_add_message( $feedback, $type );
	}

	// Redirect.
	$member_notices = trailingslashit( sz_loggedin_user_domain() . sz_get_messages_slug() );
	$redirect_to    = trailingslashit( $member_notices . 'notices' );

	sz_core_redirect( $redirect_to );
}
add_action( 'sz_actions', 'sz_messages_action_edit_notice' );