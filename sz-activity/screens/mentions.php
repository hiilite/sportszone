<?php
/**
 * Activity: User's "Activity > Mentions" screen handler
 *
 * @package SportsZone
 * @subpackage ActivityScreens
 * @since 3.0.0
 */

/**
 * Load the 'Mentions' activity page.
 *
 * @since 1.2.0
 */
function sz_activity_screen_mentions() {
	sz_update_is_item_admin( sz_current_user_can( 'sz_moderate' ), 'activity' );

	/**
	 * Fires right before the loading of the "Mentions" screen template file.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_activity_screen_mentions' );

	/**
	 * Filters the template to load for the "Mentions" screen.
	 *
	 * @since 1.2.0
	 *
	 * @param string $template Path to the activity template to load.
	 */
	sz_core_load_template( apply_filters( 'sz_activity_template_mention_activity', 'members/single/home' ) );
}

/**
 * Reset the logged-in user's new mentions data when he visits his mentions screen.
 *
 * @since 1.5.0
 *
 */
function sz_activity_reset_my_new_mentions() {
	if ( sz_is_my_profile() )
		sz_activity_clear_new_mentions( sz_loggedin_user_id() );
}
add_action( 'sz_activity_screen_mentions', 'sz_activity_reset_my_new_mentions' );