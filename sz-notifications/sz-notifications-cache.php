<?php
/**
 * Functions related to notifications caching.
 *
 * @package SportsZone
 * @subpackage NotificationsCache
 * @since 2.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Slurp up metadata for a set of notifications.
 *
 * It grabs all notification meta associated with all of the notifications
 * passed in $notification_ids and adds it to WP cache. This improves efficiency
 * when using notification meta within a loop context.
 *
 * @since 2.3.0
 *
 * @param int|string|array|bool $notification_ids Accepts a single notification_id, or a
 *                                                comma-separated list or array of
 *                                                notification ids.
 */
function sz_notifications_update_meta_cache( $notification_ids = false ) {
	sz_update_meta_cache( array(
		'object_ids'       => $notification_ids,
		'object_type'      => sportszone()->notifications->id,
		'cache_group'      => 'notification_meta',
		'object_column'    => 'notification_id',
		'meta_table'       => sportszone()->notifications->table_name_meta,
		'cache_key_prefix' => 'sz_notifications_meta'
	) );
}

/**
 * Clear all notifications cache for a given user ID.
 *
 * @since 2.3.0
 *
 * @param int $user_id The user ID's cache to clear.
 */
function sz_notifications_clear_all_for_user_cache( $user_id = 0 ) {
	wp_cache_delete( 'all_for_user_' . $user_id, 'sz_notifications' );
	wp_cache_delete( $user_id, 'sz_notifications_unread_count' );
	wp_cache_delete( $user_id, 'sz_notifications_grouped_notifications' );
}

/**
 * Invalidate 'all_for_user_' cache when saving.
 *
 * @since 2.0.0
 *
 * @param SZ_Notifications_Notification $n Notification object.
 */
function sz_notifications_clear_all_for_user_cache_after_save( SZ_Notifications_Notification $n ) {
	sz_notifications_clear_all_for_user_cache( $n->user_id );
}
add_action( 'sz_notification_after_save', 'sz_notifications_clear_all_for_user_cache_after_save' );

/**
 * Invalidate the 'all_for_user_' cache when deleting.
 *
 * @since 2.0.0
 *
 * @param int $args Notification deletion arguments.
 */
function sz_notifications_clear_all_for_user_cache_before_delete( $args ) {

	// Pull up a list of items matching the args (those about te be deleted).
	$ns = SZ_Notifications_Notification::get( $args );

	$user_ids = array();
	foreach ( $ns as $n ) {
		$user_ids[] = $n->user_id;
	}

	$user_ids = array_unique( $user_ids );
	foreach ( $user_ids as $user_id ) {
		sz_notifications_clear_all_for_user_cache( $user_id );
	}
}
add_action( 'sz_notification_before_delete', 'sz_notifications_clear_all_for_user_cache_before_delete' );

/**
 * Invalidates 'all_for_user_' cache when updating.
 *
 * @since 2.3.0
 *
 * @param array $update_args See SZ_Notifications_Notification::update() for description.
 * @param array $where_args  See SZ_Notifications_Notification::update() for description.
 */
function sz_notifications_clear_all_for_user_cache_before_update( $update_args, $where_args ) {

	// User ID is passed in where arugments.
	if ( ! empty( $where_args['user_id'] ) ) {
		sz_notifications_clear_all_for_user_cache( $where_args['user_id'] );

	// Get user ID from Notification ID.
	} elseif ( ! empty( $where_args['id'] ) ) {
		$n = sz_notifications_get_notification( $where_args['id'] );
		sz_notifications_clear_all_for_user_cache( $n->user_id );
	}
}
add_action( 'sz_notification_before_update', 'sz_notifications_clear_all_for_user_cache_before_update', 10, 2 );
