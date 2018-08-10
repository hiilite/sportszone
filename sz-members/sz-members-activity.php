<?php
/**
 * SportsZone Member Activity
 *
 * @package SportsZone
 * @subpackage MembersActivity
 * @since 2.2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register the 'new member' activity type.
 *
 * @since 2.2.0
 *
 */
function sz_members_register_activity_actions() {

	sz_activity_set_action(
		sportszone()->members->id,
		'new_member',
		__( 'New member registered', 'sportszone' ),
		'sz_members_format_activity_action_new_member',
		__( 'New Members', 'sportszone' ),
		array( 'activity' )
	);

	/**
	 * Fires after the default 'new member' activity types are registered.
	 *
	 * @since 2.2.0
	 */
	do_action( 'sz_members_register_activity_actions' );
}
add_action( 'sz_register_activity_actions', 'sz_members_register_activity_actions' );

/**
 * Format 'new_member' activity actions.
 *
 * @since 2.2.0
 *
 * @param string $action   Static activity action.
 * @param object $activity Activity object.
 * @return string $action
 */
function sz_members_format_activity_action_new_member( $action, $activity ) {
	$userlink = sz_core_get_userlink( $activity->user_id );
	$action   = sprintf( __( '%s became a registered member', 'sportszone' ), $userlink );

	// Legacy filter - pass $user_id instead of $activity.
	if ( has_filter( 'sz_core_activity_registered_member_action' ) ) {
		$action = apply_filters( 'sz_core_activity_registered_member_action', $action, $activity->user_id );
	}

	/**
	 * Filters the formatted 'new member' activity actions.
	 *
	 * @since 2.2.0
	 *
	 * @param string $action   Static activity action.
	 * @param object $activity Activity object.
	 */
	return apply_filters( 'sz_members_format_activity_action_new_member', $action, $activity );
}

/**
 * Create a "became a registered user" activity item when a user activates his account.
 *
 * @since 1.2.2
 *
 * @param array $user Array of userdata passed to sz_core_activated_user hook.
 * @return bool
 */
function sz_core_new_user_activity( $user ) {
	if ( empty( $user ) ) {
		return false;
	}

	if ( is_array( $user ) ) {
		$user_id = $user['user_id'];
	} else {
		$user_id = $user;
	}

	if ( empty( $user_id ) ) {
		return false;
	}

	sz_activity_add( array(
		'user_id'   => $user_id,
		'component' => sportszone()->members->id,
		'type'      => 'new_member'
	) );
}
add_action( 'sz_core_activated_user', 'sz_core_new_user_activity' );
