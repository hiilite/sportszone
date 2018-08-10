<?php
/**
 * Blogs: Create screen handler
 *
 * @package SportsZone
 * @subpackage BlogsScreens
 * @since 3.0.0
 */

/**
 * Load the "Create a Blog" screen.
 *
 * @since 1.0.0
 */
function sz_blogs_screen_create_a_blog() {

	if ( !is_multisite() ||  !sz_is_blogs_component() || !sz_is_current_action( 'create' ) )
		return false;

	if ( !is_user_logged_in() || !sz_blog_signup_enabled() )
		return false;

	/**
	 * Fires right before the loading of the Create A Blog screen template file.
	 *
	 * @since 1.0.0
	 */
	do_action( 'sz_blogs_screen_create_a_blog' );

	sz_core_load_template( apply_filters( 'sz_blogs_template_create_a_blog', 'blogs/create' ) );
}
add_action( 'sz_screens', 'sz_blogs_screen_create_a_blog', 3 );