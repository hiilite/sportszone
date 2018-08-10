<?php
/**
 * SportsZone Friend Filters.
 *
 * @package SportsZone
 * @subpackage FriendsFilters
 * @since 1.7.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Filter SZ_User_Query::populate_extras to add confirmed friendship status.
 *
 * Each member in the user query is checked for confirmed friendship status
 * against the logged-in user.
 *
 * @since 1.7.0
 *
 * @global WPDB $wpdb WordPress database access object.
 *
 * @param SZ_User_Query $user_query   The SZ_User_Query object.
 * @param string        $user_ids_sql Comma-separated list of user IDs to fetch extra
 *                                    data for, as determined by SZ_User_Query.
 */
function sz_friends_filter_user_query_populate_extras( SZ_User_Query $user_query, $user_ids_sql ) {
	global $wpdb;

	// Stop if user isn't logged in.
	if ( ! $user_id = sz_loggedin_user_id() ) {
		return;
	}

	$maybe_friend_ids = wp_parse_id_list( $user_ids_sql );

	// Bulk prepare the friendship cache.
	SZ_Friends_Friendship::update_sz_friends_cache( $user_id, $maybe_friend_ids );

	foreach ( $maybe_friend_ids as $friend_id ) {
		$status = SZ_Friends_Friendship::check_is_friend( $user_id, $friend_id );
		$user_query->results[ $friend_id ]->friendship_status = $status;
		if ( 'is_friend' == $status ) {
			$user_query->results[ $friend_id ]->is_friend = 1;
		}
	}

}
add_filter( 'sz_user_query_populate_extras', 'sz_friends_filter_user_query_populate_extras', 4, 2 );
