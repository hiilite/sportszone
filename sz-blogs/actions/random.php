<?php
/**
 * Blogs: Random blog action handler
 *
 * @package SportsZone
 * @subpackage BlogsActions
 * @since 3.0.0
 */

/**
 * Redirect to a random blog in the multisite network.
 *
 * @since 1.0.0
 */
function sz_blogs_redirect_to_random_blog() {

	// Bail if not looking for a random blog.
	if ( ! sz_is_blogs_component() || ! isset( $_GET['random-blog'] ) )
		return;

	// Multisite is active so find a random blog.
	if ( is_multisite() ) {
		$blog = sz_blogs_get_random_blogs( 1, 1 );
		sz_core_redirect( get_home_url( $blog['blogs'][0]->blog_id ) );

	// No multisite and still called, always redirect to root.
	} else {
		sz_core_redirect( sz_core_get_root_domain() );
	}
}
add_action( 'sz_actions', 'sz_blogs_redirect_to_random_blog' );