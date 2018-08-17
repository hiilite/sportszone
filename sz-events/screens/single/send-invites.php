<?php
/**
 * Events: Single event "Send Invites" screen handler
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a event's Send Invites page.
 *
 * @since 1.0.0
 */
function events_screen_event_invite() {

	if ( !sz_is_single_item() )
		return false;

	$sz = sportszone();

	if ( sz_is_action_variable( 'send', 0 ) ) {

		if ( !check_admin_referer( 'events_send_invites', '_wpnonce_send_invites' ) )
			return false;

		if ( !empty( $_POST['friends'] ) ) {
			foreach( (array) $_POST['friends'] as $friend ) {
				events_invite_user( array( 'user_id' => $friend, 'event_id' => $sz->events->current_event->id ) );
			}
		}

		// Send the invites.
		events_send_invites( sz_loggedin_user_id(), $sz->events->current_event->id );
		sz_core_add_message( __('Event invites sent.', 'sportszone') );

		/**
		 * Fires after the sending of a event invite inside the event's Send Invites page.
		 *
		 * @since 1.0.0
		 *
		 * @param int $id ID of the event whose members are being displayed.
		 */
		do_action( 'events_screen_event_invite', $sz->events->current_event->id );
		sz_core_redirect( sz_get_event_permalink( $sz->events->current_event ) );

	} elseif ( !sz_action_variable( 0 ) ) {

		/**
		 * Filters the template to load for a event's Send Invites page.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Path to a event's Send Invites template.
		 */
		sz_core_load_template( apply_filters( 'events_template_event_invite', 'events/single/home' ) );

	} else {
		sz_do_404();
	}
}

/**
 * Process event invitation removal requests.
 *
 * Note that this function is only used when JS is disabled. Normally, clicking
 * Remove Invite removes the invitation via AJAX.
 *
 * @since 2.0.0
 */
function events_remove_event_invite() {
	if ( ! sz_is_event_invites() ) {
		return;
	}

	if ( ! sz_is_action_variable( 'remove', 0 ) || ! is_numeric( sz_action_variable( 1 ) ) ) {
		return;
	}

	if ( ! check_admin_referer( 'events_invite_uninvite_user' ) ) {
		return false;
	}

	$friend_id = intval( sz_action_variable( 1 ) );
	$event_id  = sz_get_current_event_id();
	$message   = __( 'Invite successfully removed', 'sportszone' );
	$redirect  = wp_get_referer();
	$error     = false;

	if ( ! sz_events_user_can_send_invites( $event_id ) ) {
		$message = __( 'You are not allowed to send or remove invites', 'sportszone' );
		$error = 'error';
	} elseif ( events_check_for_membership_request( $friend_id, $event_id ) ) {
		$message = __( 'The member requested to join the event', 'sportszone' );
		$error = 'error';
	} elseif ( ! events_uninvite_user( $friend_id, $event_id ) ) {
		$message = __( 'There was an error removing the invite', 'sportszone' );
		$error = 'error';
	}

	sz_core_add_message( $message, $error );
	sz_core_redirect( $redirect );
}
add_action( 'sz_screens', 'events_remove_event_invite' );