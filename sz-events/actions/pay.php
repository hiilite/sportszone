<?php
/**
 * Events: Pay action
 *
 * @package SportsZone
 * @subpackage EventActions
 * @since 3.0.0
 */

/**
 * Catch and process "Pay Event" button clicks.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function events_action_pay_event() {
	print_r($_REQUEST);
	exit;
	if ( !sz_is_single_item() || !sz_is_events_component() || !sz_is_current_action( 'join' ) )
		return false;

	// Nonce check.
	if ( !check_admin_referer( 'events_pay_event' ) )
		return false;

	$sz = sportszone();

	// Skip if banned or already a member.
	if ( !events_is_user_member( sz_loggedin_user_id(), $sz->events->current_event->id ) && !events_is_user_banned( sz_loggedin_user_id(), $sz->events->current_event->id ) ) {

		// User wants to join a event that requires an invitation to join.
		if ( ! sz_current_user_can( 'events_pay_event', array( 'event_id' => $sz->events->current_event->id ) ) ) {
			if ( !events_check_user_has_invite( sz_loggedin_user_id(), $sz->events->current_event->id ) ) {
				sz_core_add_message( __( 'There was an error paying for the event.', 'sportszone' ), 'error' );
				sz_core_redirect( sz_get_event_permalink( $sz->events->current_event ) );
			}
		}

		// User wants to join any event.
		if ( !events_join_event( $sz->events->current_event->id ) )
			sz_core_add_message( __( 'There was an error paying for the event.', 'sportszone' ), 'error' );
		else
			sz_core_add_message( __( 'You joined the event!', 'sportszone' ) );

		sz_core_redirect( sz_get_event_permalink( $sz->events->current_event ) );
	}

	/**
	 * Filters the template to load for the single event screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to the single event template to load.
	 */
	sz_core_load_template( apply_filters( 'events_template_event_home', 'events/single/home' ) );
}
add_action( 'sz_actions', 'events_action_pay_event' );
