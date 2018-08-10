<?php
/**
 * Groups: Join action
 *
 * @package SportsZone
 * @subpackage GroupActions
 * @since 3.0.0
 */

/**
 * Catch and process "Join Group" button clicks.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function groups_action_join_group() {

	if ( !sz_is_single_item() || !sz_is_groups_component() || !sz_is_current_action( 'join' ) )
		return false;

	// Nonce check.
	if ( !check_admin_referer( 'groups_join_group' ) )
		return false;

	$sz = sportszone();

	// Skip if banned or already a member.
	if ( !groups_is_user_member( sz_loggedin_user_id(), $sz->groups->current_group->id ) && !groups_is_user_banned( sz_loggedin_user_id(), $sz->groups->current_group->id ) ) {

		// User wants to join a group that requires an invitation to join.
		if ( ! sz_current_user_can( 'groups_join_group', array( 'group_id' => $sz->groups->current_group->id ) ) ) {
			if ( !groups_check_user_has_invite( sz_loggedin_user_id(), $sz->groups->current_group->id ) ) {
				sz_core_add_message( __( 'There was an error joining the group.', 'sportszone' ), 'error' );
				sz_core_redirect( sz_get_group_permalink( $sz->groups->current_group ) );
			}
		}

		// User wants to join any group.
		if ( !groups_join_group( $sz->groups->current_group->id ) )
			sz_core_add_message( __( 'There was an error joining the group.', 'sportszone' ), 'error' );
		else
			sz_core_add_message( __( 'You joined the group!', 'sportszone' ) );

		sz_core_redirect( sz_get_group_permalink( $sz->groups->current_group ) );
	}

	/**
	 * Filters the template to load for the single group screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to the single group template to load.
	 */
	sz_core_load_template( apply_filters( 'groups_template_group_home', 'groups/single/home' ) );
}
add_action( 'sz_actions', 'groups_action_join_group' );
