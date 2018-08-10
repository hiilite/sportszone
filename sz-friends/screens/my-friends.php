<?php
/**
 * Friends: User's "Friends" screen handler
 *
 * @package SportsZone
 * @subpackage FriendsScreens
 * @since 3.0.0
 */

/**
 * Catch and process the My Friends page.
 *
 * @since 1.0.0
 */
function friends_screen_my_friends() {

	/**
	 * Fires before the loading of template for the My Friends page.
	 *
	 * @since 1.0.0
	 */
	do_action( 'friends_screen_my_friends' );

	/**
	 * Filters the template used to display the My Friends page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Path to the my friends template to load.
	 */
	sz_core_load_template( apply_filters( 'friends_template_my_friends', 'members/single/home' ) );
}