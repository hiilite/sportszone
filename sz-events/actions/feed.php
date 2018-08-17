<?php
/**
 * Events: RSS feed action
 *
 * @package SportsZone
 * @subpackage EventActions
 * @since 3.0.0
 */

/**
 * Load the activity feed for the current event.
 *
 * @since 1.2.0
 *
 * @return false|null False on failure.
 */
function events_action_event_feed() {

	// Get current event.
	$event = events_get_current_event();

	if ( ! sz_is_active( 'activity' ) || ! sz_is_events_component() || ! $event || ! sz_is_current_action( 'feed' ) )
		return false;

	// If event isn't public or if logged-in user is not a member of the event, do
	// not output the event activity feed.
	if ( ! sz_event_is_visible( $event ) ) {
		return false;
	}

	// Set up the feed.
	sportszone()->activity->feed = new SZ_Activity_Feed( array(
		'id'            => 'event',

		/* translators: Event activity RSS title - "[Site Name] | [Event Name] | Activity" */
		'title'         => sprintf( __( '%1$s | %2$s | Activity', 'sportszone' ), sz_get_site_name(), sz_get_current_event_name() ),

		'link'          => sz_get_event_permalink( $event ),
		'description'   => sprintf( __( "Activity feed for the event, %s.", 'sportszone' ), sz_get_current_event_name() ),
		'activity_args' => array(
			'object'           => sportszone()->events->id,
			'primary_id'       => sz_get_current_event_id(),
			'display_comments' => 'threaded'
		)
	) );
}
add_action( 'sz_actions', 'events_action_event_feed' );