<?php
/**
 * Events: Single event "Activity" screen handler
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * Handle the loading of a single event's activity.
 *
 * @since 2.4.0
 */
function events_screen_event_activity() {

	if ( ! sz_is_single_item() ) {
		return false;
	}

	/**
	 * Fires before the loading of a single event's activity page.
	 *
	 * @since 2.4.0
	 */
	do_action( 'events_screen_event_activity' );

	/**
	 * Filters the template to load for a single event's activity page.
	 *
	 * @since 2.4.0
	 *
	 * @param string $value Path to a single event's template to load.
	 */
	sz_core_load_template( apply_filters( 'events_screen_event_activity', 'events/single/activity' ) );
}