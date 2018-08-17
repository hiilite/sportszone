<?php
/**
 * Events: Single event activity permalink screen handler
 *
 * Note - This has never worked.
 * See {@link https://sportszone.trac.wordpress.org/ticket/2579}
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a single event activity item.
 *
 * @since 1.2.0
 */
function events_screen_event_activity_permalink() {
	if ( !sz_is_events_component() || !sz_is_active( 'activity' ) || ( sz_is_active( 'activity' ) && !sz_is_current_action( sz_get_activity_slug() ) ) || !sz_action_variable( 0 ) )
		return false;

	sportszone()->is_single_item = true;

	/** This filter is documented in sz-events/sz-events-screens.php */
	sz_core_load_template( apply_filters( 'events_template_event_home', 'events/single/home' ) );
}
add_action( 'sz_screens', 'events_screen_event_activity_permalink' );