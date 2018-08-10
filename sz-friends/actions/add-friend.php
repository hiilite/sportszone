<?php
/**
 * Friends: Add action
 *
 * @package SportsZone
 * @subpackage FriendsActions
 * @since 3.0.0
 */

/**
 * Catch and process friendship requests.
 *
 * @since 1.0.1
 */
function friends_action_add_friend() {
	if ( !sz_is_friends_component() || !sz_is_current_action( 'add-friend' ) )
		return false;

	if ( !$potential_friend_id = (int)sz_action_variable( 0 ) )
		return false;

	if ( $potential_friend_id == sz_loggedin_user_id() )
		return false;

	$friendship_status = SZ_Friends_Friendship::check_is_friend( sz_loggedin_user_id(), $potential_friend_id );

	if ( 'not_friends' == $friendship_status ) {

		if ( !check_admin_referer( 'friends_add_friend' ) )
			return false;

		if ( !friends_add_friend( sz_loggedin_user_id(), $potential_friend_id ) ) {
			sz_core_add_message( __( 'Friendship could not be requested.', 'sportszone' ), 'error' );
		} else {
			sz_core_add_message( __( 'Friendship requested', 'sportszone' ) );
		}

	} elseif ( 'is_friend' == $friendship_status ) {
		sz_core_add_message( __( 'You are already friends with this user', 'sportszone' ), 'error' );
	} else {
		sz_core_add_message( __( 'You already have a pending friendship request with this user', 'sportszone' ), 'error' );
	}

	sz_core_redirect( wp_get_referer() );

	return false;
}
add_action( 'sz_actions', 'friends_action_add_friend' );