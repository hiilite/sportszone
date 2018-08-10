<?php
/**
 * SportsZone Blogs Loader
 *
 * The blogs component tracks posts and comments to member activity streams,
 * shows blogs the member can post to in their profiles, and caches useful
 * information from those blogs to make querying blogs in bulk more performant.
 *
 * @package SportsZone
 * @subpackage BlogsCore
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the sz-blogs component.
 *
 * @since 1.5.0
 */
function sz_setup_blogs() {
	sportszone()->blogs = new SZ_Blogs_Component();
}
add_action( 'sz_setup_components', 'sz_setup_blogs', 6 );
