<?php
/**
 * Blogs: Directory screen handler
 *
 * @package SportsZone
 * @subpackage BlogsScreens
 * @since 3.0.0
 */

/**
 * Load the top-level Blogs directory.
 *
 * @since 1.5-beta-1
 */
function sz_blogs_screen_index() {
	if ( sz_is_blogs_directory() ) {
		sz_update_is_directory( true, 'blogs' );

		/**
		 * Fires right before the loading of the top-level Blogs screen template file.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sz_blogs_screen_index' );

		sz_core_load_template( apply_filters( 'sz_blogs_screen_index', 'blogs/index' ) );
	}
}
add_action( 'sz_screens', 'sz_blogs_screen_index', 2 );