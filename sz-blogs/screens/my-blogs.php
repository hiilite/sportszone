<?php
/**
 * Blogs: User's "Sites" screen handler
 *
 * @package SportsZone
 * @subpackage BlogsScreens
 * @since 3.0.0
 */

/**
 * Load the "My Blogs" screen.
 *
 * @since 1.0.0
 */
function sz_blogs_screen_my_blogs() {
	if ( !is_multisite() )
		return false;

	/**
	 * Fires right before the loading of the My Blogs screen template file.
	 *
	 * @since 1.0.0
	 */
	do_action( 'sz_blogs_screen_my_blogs' );

	sz_core_load_template( apply_filters( 'sz_blogs_template_my_blogs', 'members/single/home' ) );
}