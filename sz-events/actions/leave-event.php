<?php
/**
 * Events: Leave action
 *
 * @package SportsZone
 * @subpackage EventActions
 * @since 3.0.0
 */

/**
 * Catch and process "Leave Event" button clicks.
 *
 * When a event member clicks on the "Leave Event" button from a event's page,
 * this function is run.
 *
 * Note: When leaving a event from the event directory, AJAX is used and
 * another function handles this. See {@link sz_legacy_theme_ajax_joinleave_event()}.
 *
 * @since 1.2.4
 *
 * @return bool
 */
function events_action_leave_event() {
	if ( ! sz_is_single_item() || ! sz_is_events_component() || ! sz_is_current_action( 'leave-event' ) ) {
		return false;
	}

	// Nonce check.
	if ( ! check_admin_referer( 'events_leave_event' ) ) {
		return false;
	}

	// User wants to leave any event.
	if ( events_is_user_member( sz_loggedin_user_id(), sz_get_current_event_id() ) ) {
		$sz = sportszone();

		// Stop sole admins from abandoning their event.
		$event_admins = events_get_event_admins( sz_get_current_event_id() );

		if ( 1 == count( $event_admins ) && $event_admins[0]->user_id == sz_loggedin_user_id() ) {
			sz_core_add_message( __( 'This event must have at least one admin', 'sportszone' ), 'error' );
		} elseif ( ! events_leave_event( $sz->events->current_event->id ) ) {
			sz_core_add_message( __( 'There was an error leaving the event.', 'sportszone' ), 'error' );
		} else {
			sz_core_add_message( __( 'You successfully left the event.', 'sportszone' ) );
		}

		$event = events_get_current_event();
		$redirect = sz_get_event_permalink( $event );

		if ( ! $event->is_visible ) {
			$redirect = trailingslashit( sz_loggedin_user_domain() . sz_get_events_slug() );
		}

		sz_core_redirect( $redirect );
	}

	/** This filter is documented in sz-events/sz-events-actions.php */
	sz_core_load_template( apply_filters( 'events_template_event_home', 'events/single/home' ) );
}
add_action( 'sz_actions', 'events_action_leave_event' );