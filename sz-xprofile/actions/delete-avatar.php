<?php
/**
 * XProfile: Avatar deletion action handler
 *
 * @package SportsZone
 * @subpackage XProfileActions
 * @since 3.0.0
 */

/**
 * This function runs when an action is set for a screen:
 * example.com/members/andy/profile/change-avatar/ [delete-avatar]
 *
 * The function will delete the active avatar for a user.
 *
 * @since 1.0.0
 *
 */
function xprofile_action_delete_avatar() {

	if ( ! sz_is_user_change_avatar() || ! sz_is_action_variable( 'delete-avatar', 0 ) ) {
		return false;
	}

	// Check the nonce.
	check_admin_referer( 'sz_delete_avatar_link' );

	if ( ! sz_is_my_profile() && ! sz_current_user_can( 'sz_moderate' ) ) {
		return false;
	}

	if ( sz_core_delete_existing_avatar( array( 'item_id' => sz_displayed_user_id() ) ) ) {
		sz_core_add_message( __( 'Your profile photo was deleted successfully!', 'sportszone' ) );
	} else {
		sz_core_add_message( __( 'There was a problem deleting your profile photo. Please try again.', 'sportszone' ), 'error' );
	}

	sz_core_redirect( wp_get_referer() );
}
add_action( 'sz_actions', 'xprofile_action_delete_avatar' );