<?php
/**
 * Activity: Directory screen handler
 *
 * @package SportsZone
 * @subpackage ActivityScreens
 * @since 3.0.0
 */

/**
 * Load the Activity directory.
 *
 * @since 1.5.0
 *
 */
function sz_activity_screen_index() {
	if ( sz_is_activity_directory() ) {
		sz_update_is_directory( true, 'activity' );

		/**
		 * Fires right before the loading of the Activity directory screen template file.
		 *
		 * @since 1.5.0
		 */
		do_action( 'sz_activity_screen_index' );

		/**
		 * Filters the template to load for the Activity directory screen.
		 *
		 * @since 1.5.0
		 *
		 * @param string $template Path to the activity template to load.
		 */
		sz_core_load_template( apply_filters( 'sz_activity_screen_index', 'activity/index' ) );
	}
}
add_action( 'sz_screens', 'sz_activity_screen_index' );