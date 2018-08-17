<?php
/**
 * Events: Random event action handler
 *
 * @package SportsZone
 * @subpackage EventActions
 * @since 3.0.0
 */

/**
 * Catch requests for a random event page (example.com/events/?random-event) and redirect.
 *
 * @since 1.2.0
 */
function events_action_redirect_to_random_event() {

	if ( sz_is_events_component() && isset( $_GET['random-event'] ) ) {
		$event = SZ_Events_Event::get_random( 1, 1 );

		sz_core_redirect( trailingslashit( sz_get_events_directory_permalink() . $event['events'][0]->slug ) );
	}
}
add_action( 'sz_actions', 'events_action_redirect_to_random_event' );