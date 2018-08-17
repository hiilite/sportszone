<?php
/**
 * Events: Single event "Request Membership" screen handler
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a event's Request Membership page.
 *
 * @since 1.0.0
 */
function events_screen_event_request_membership() {

	if ( !is_user_logged_in() )
		return false;

	$sz = sportszone();

	if ( 'private' != $sz->events->current_event->status )
		return false;

	// If the user is already invited, accept invitation.
	if ( events_check_user_has_invite( sz_loggedin_user_id(), $sz->events->current_event->id ) ) {
		if ( events_accept_invite( sz_loggedin_user_id(), $sz->events->current_event->id ) )
			sz_core_add_message( __( 'Event invite accepted', 'sportszone' ) );
		else
			sz_core_add_message( __( 'There was an error accepting the event invitation. Please try again.', 'sportszone' ), 'error' );
		sz_core_redirect( sz_get_event_permalink( $sz->events->current_event ) );
	}

	// If the user has submitted a request, send it.
	if ( isset( $_POST['event-request-send']) ) {

		// Check the nonce.
		if ( !check_admin_referer( 'events_request_membership' ) )
			return false;

		if ( !events_send_membership_request( sz_loggedin_user_id(), $sz->events->current_event->id ) ) {
			sz_core_add_message( __( 'There was an error sending your event membership request. Please try again.', 'sportszone' ), 'error' );
		} else {
			sz_core_add_message( __( 'Your membership request was sent to the event administrator successfully. You will be notified when the event administrator responds to your request.', 'sportszone' ) );
		}
		sz_core_redirect( sz_get_event_permalink( $sz->events->current_event ) );
	}

	/**
	 * Fires before the loading of a event's Request Memebership page.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the event currently being displayed.
	 */
	do_action( 'events_screen_event_request_membership', $sz->events->current_event->id );

	/**
	 * Filters the template to load for a event's Request Membership page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a event's Request Membership template.
	 */
	sz_core_load_template( apply_filters( 'events_template_event_request_membership', 'events/single/home' ) );
}