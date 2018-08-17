<?php
/**
 * Events: User's "Events" screen handler
 *
 * @package SportsZone
 * @subpackage EventScreens
 * @since 3.0.0
 */

/**
 * Handle the loading of the My Events page.
 *
 * @since 1.0.0
 */
function events_screen_my_events() {

	/**
	 * Fires before the loading of the My Events page.
	 *
	 * @since 1.1.0
	 */
	do_action( 'events_screen_my_events' );

	/**
	 * Filters the template to load for the My Events page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to the My Events page template to load.
	 */
	sz_core_load_template( apply_filters( 'events_template_my_events', 'members/single/home' ) );
}