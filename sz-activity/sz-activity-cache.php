<?php
/**
 * Functions related to the SportsZone Activity component and the WP Cache.
 *
 * @package SportsZone
 * @subpackage ActivityCache
 * @since 1.6.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Slurp up activitymeta for a specified set of activity items.
 *
 * It grabs all activitymeta associated with all of the activity items passed
 * in $activity_ids and adds it to the WP cache. This improves efficiency when
 * using querying activitymeta inline.
 *
 * @since 1.6.0
 *
 * @param int|string|array|bool $activity_ids Accepts a single activity ID, or a comma-
 *                                            separated list or array of activity ids.
 */
function sz_activity_update_meta_cache( $activity_ids = false ) {
	$sz = sportszone();

	$cache_args = array(
		'object_ids' 	   => $activity_ids,
		'object_type' 	   => $sz->activity->id,
		'object_column'    => 'activity_id',
		'cache_group'      => 'activity_meta',
		'meta_table' 	   => $sz->activity->table_name_meta,
		'cache_key_prefix' => 'sz_activity_meta'
	);

	sz_update_meta_cache( $cache_args );
}

/**
 * Clear a cached activity item when that item is updated.
 *
 * @since 2.0.0
 *
 * @param SZ_Activity_Activity $activity Activity object.
 */
function sz_activity_clear_cache_for_activity( $activity ) {
	wp_cache_delete( $activity->id, 'sz_activity' );
	wp_cache_delete( 'sz_activity_sitewide_front', 'sz' );
}
add_action( 'sz_activity_after_save', 'sz_activity_clear_cache_for_activity' );

/**
 * Clear cached data for deleted activity items.
 *
 * @since 2.0.0
 *
 * @param array $deleted_ids IDs of deleted activity items.
 */
function sz_activity_clear_cache_for_deleted_activity( $deleted_ids ) {
	foreach ( (array) $deleted_ids as $deleted_id ) {
		wp_cache_delete( $deleted_id, 'sz_activity' );
	}
}
add_action( 'sz_activity_deleted_activities', 'sz_activity_clear_cache_for_deleted_activity' );

/**
 * Reset cache incrementor for the Activity component.
 *
 * Called whenever an activity item is created, updated, or deleted, this
 * function effectively invalidates all cached results of activity queries.
 *
 * @since 2.7.0
 *
 * @return bool True on success, false on failure.
 */
function sz_activity_reset_cache_incrementor() {
	$without_last_activity = sz_core_reset_incrementor( 'sz_activity' );
	$with_last_activity    = sz_core_reset_incrementor( 'sz_activity_with_last_activity' );
	return $without_last_activity && $with_last_activity;
}
add_action( 'sz_activity_delete',    'sz_activity_reset_cache_incrementor' );
add_action( 'sz_activity_add',       'sz_activity_reset_cache_incrementor' );
add_action( 'added_activity_meta',   'sz_activity_reset_cache_incrementor' );
add_action( 'updated_activity_meta', 'sz_activity_reset_cache_incrementor' );
add_action( 'deleted_activity_meta', 'sz_activity_reset_cache_incrementor' );
