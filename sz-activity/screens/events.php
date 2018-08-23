<?php
/**
 * Activity: User's "Activity > Events" screen handler
 *
 * @package SportsZone
 * @subpackage ActivityScreens
 * @since 3.0.0
 */

/**
 * Load the 'My Events' activity page.
 *
 * @since 1.2.0
 */
function sz_activity_screen_events() {
	if ( !sz_is_active( 'events' ) )
		return false;

	sz_update_is_item_admin( sz_current_user_can( 'sz_moderate' ), 'activity' );

	/**
	 * Fires right before the loading of the "My Events" screen template file.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_activity_screen_events' );

	/**
	 * Filters the template to load for the "My Events" screen.
	 *
	 * @since 1.2.0
	 *
	 * @param string $template Path to the activity template to load.
	 */
	sz_core_load_template( apply_filters( 'sz_activity_template_events_activity', 'members/single/home' ) );
}