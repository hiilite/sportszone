<?php
/**
 * Events: Directory screen handler
 *
 * @package SportsZone
 * @subpackage EventScreens
 * @since 3.0.0
 */

/**
 * Handle the display of the Events directory index.
 *
 * @since 1.0.0
 */
function events_directory_events_setup() {
	if ( sz_is_events_directory() ) {
		sz_update_is_directory( true, 'events' );

		/**
		 * Fires before the loading of the Events directory index.
		 *
		 * @since 1.1.0
		 */
		do_action( 'events_directory_events_setup' );

		/**
		 * Filters the template to load for the Events directory index.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Path to the events directory index template to load.
		 */
		sz_core_load_template( apply_filters( 'events_template_directory_events', 'events/index' ) );
	}
}
add_action( 'sz_screens', 'events_directory_events_setup', 2 );