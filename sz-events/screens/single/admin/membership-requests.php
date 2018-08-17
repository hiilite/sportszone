<?php
/**
 * Events: Single event "Manage > Requests" screen handler
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of Admin > Membership Requests.
 *
 * @since 1.0.0
 */
function events_screen_event_admin_requests() {
	$sz = sportszone();

	if ( 'membership-requests' != sz_get_event_current_admin_tab() ) {
		return false;
	}

	if ( ! sz_is_item_admin() || ( 'public' == $sz->events->current_event->status ) ) {
		return false;
	}

	$request_action = (string) sz_action_variable( 1 );
	$membership_id  = (int) sz_action_variable( 2 );

	if ( !empty( $request_action ) && !empty( $membership_id ) ) {
		if ( 'accept' == $request_action && is_numeric( $membership_id ) ) {

			// Check the nonce first.
			if ( !check_admin_referer( 'events_accept_membership_request' ) )
				return false;

			// Accept the membership request.
			if ( !events_accept_membership_request( $membership_id ) )
				sz_core_add_message( __( 'There was an error accepting the membership request. Please try again.', 'sportszone' ), 'error' );
			else
				sz_core_add_message( __( 'Event membership request accepted', 'sportszone' ) );

		} elseif ( 'reject' == $request_action && is_numeric( $membership_id ) ) {
			/* Check the nonce first. */
			if ( !check_admin_referer( 'events_reject_membership_request' ) )
				return false;

			// Reject the membership request.
			if ( !events_reject_membership_request( $membership_id ) )
				sz_core_add_message( __( 'There was an error rejecting the membership request. Please try again.', 'sportszone' ), 'error' );
			else
				sz_core_add_message( __( 'Event membership request rejected', 'sportszone' ) );
		}

		/**
		 * Fires before the redirect if a event membership request has been handled.
		 *
		 * @since 1.0.0
		 *
		 * @param int    $id             ID of the event that was edited.
		 * @param string $request_action Membership request action being performed.
		 * @param int    $membership_id  The key of the action_variables array that you want.
		 */
		do_action( 'events_event_request_managed', $sz->events->current_event->id, $request_action, $membership_id );
		sz_core_redirect( sz_get_event_permalink( events_get_current_event() ) . 'admin/membership-requests/' );
	}

	/**
	 * Fires before the loading of the event membership request page template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the event that is being displayed.
	 */
	do_action( 'events_screen_event_admin_requests', $sz->events->current_event->id );

	/**
	 * Filters the template to load for a event's membership request page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a event's membership request template.
	 */
	sz_core_load_template( apply_filters( 'events_template_event_admin_requests', 'events/single/home' ) );
}
add_action( 'sz_screens', 'events_screen_event_admin_requests' );