<?php
/**
 * SportsZone Blogs Caching.
 *
 * Caching functions handle the clearing of cached objects and pages on specific
 * actions throughout SportsZone.
 *
 * @package SportsZone
 * @subpackage BlogsCache
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Slurp up blogmeta for a specified set of blogs.
 *
 * It grabs all blogmeta associated with all of the blogs passed
 * in $blog_ids and adds it to the WP cache. This improves efficiency when
 * using querying blogmeta inline.
 *
 * @param int|string|array|bool $blog_ids Accepts a single blog ID, or a comma-
 *                                        separated list or array of blog IDs.
 */
function sz_blogs_update_meta_cache( $blog_ids = false ) {
	$cache_args = array(
		'object_ids'    => $blog_ids,
		'object_type'   => sportszone()->blogs->id,
		'object_column' => 'blog_id',
		'cache_group'   => 'blog_meta',
		'meta_table'    => sportszone()->blogs->table_name_blogmeta,
	);

	sz_update_meta_cache( $cache_args );
}
/**
 * Clear the blog object cache.
 *
 * @since 1.0.0
 *
 * @param int $blog_id ID of the current blog.
 * @param int $user_id ID of the user whose blog cache should be cleared.
 */
function sz_blogs_clear_blog_object_cache( $blog_id = 0, $user_id = 0 ) {
	if ( ! empty( $user_id ) ) {
		wp_cache_delete( 'sz_blogs_of_user_'        . $user_id, 'sz' );
		wp_cache_delete( 'sz_total_blogs_for_user_' . $user_id, 'sz' );
	}

	wp_cache_delete( 'sz_total_blogs', 'sz' );
}

// List actions to clear object caches on.
add_action( 'sz_blogs_remove_blog_for_user', 'sz_blogs_clear_blog_object_cache', 10, 2 );
add_action( 'wpmu_new_blog',                 'sz_blogs_clear_blog_object_cache', 10, 2 );
add_action( 'sz_blogs_remove_blog',          'sz_blogs_clear_blog_object_cache' );

// List actions to clear super cached pages on, if super cache is installed.
add_action( 'sz_blogs_remove_data_for_blog', 'sz_core_clear_cache' );
add_action( 'sz_blogs_remove_comment',       'sz_core_clear_cache' );
add_action( 'sz_blogs_remove_post',          'sz_core_clear_cache' );
add_action( 'sz_blogs_remove_blog_for_user', 'sz_core_clear_cache' );
add_action( 'sz_blogs_remove_blog',          'sz_core_clear_cache' );
add_action( 'sz_blogs_new_blog_comment',     'sz_core_clear_cache' );
add_action( 'sz_blogs_new_blog_post',        'sz_core_clear_cache' );
add_action( 'sz_blogs_new_blog',             'sz_core_clear_cache' );
add_action( 'sz_blogs_remove_data',          'sz_core_clear_cache' );
