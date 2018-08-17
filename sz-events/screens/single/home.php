<?php
/**
 * Events: Single event "Home" screen handler
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * Handle the loading of a single event's page.
 *
 * @since 1.0.0
 */
function events_screen_event_home() {

	if ( ! sz_is_single_item() ) {
		return false;
	}

	/**
	 * Fires before the loading of a single event's page.
	 *
	 * @since 1.0.0
	 */
	do_action( 'events_screen_event_home' );

	/**
	 * Filters the template to load for a single event's page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a single event's template to load.
	 */
	sz_core_load_template( apply_filters( 'events_template_event_home', 'events/single/home' ) );
}