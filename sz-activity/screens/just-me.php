<?php
/**
 * Activity: User's "Activity" screen handler
 *
 * @package SportsZone
 * @subpackage ActivityScreens
 * @since 3.0.0
 */

/**
 * Load the 'My Activity' page.
 *
 * @since 1.0.0
 */
function sz_activity_screen_my_activity() {

	/**
	 * Fires right before the loading of the "My Activity" screen template file.
	 *
	 * @since 1.0.0
	 */
	do_action( 'sz_activity_screen_my_activity' );

	/**
	 * Filters the template to load for the "My Activity" screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Path to the activity template to load.
	 */
	sz_core_load_template( apply_filters( 'sz_activity_template_my_activity', 'members/single/home' ) );
}