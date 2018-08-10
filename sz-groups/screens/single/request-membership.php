<?php
/**
 * Groups: Single group "Request Membership" screen handler
 *
 * @package SportsZone
 * @subpackage GroupsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a group's Request Membership page.
 *
 * @since 1.0.0
 */
function groups_screen_group_request_membership() {

	if ( !is_user_logged_in() )
		return false;

	$sz = sportszone();

	if ( 'private' != $sz->groups->current_group->status )
		return false;

	// If the user is already invited, accept invitation.
	if ( groups_check_user_has_invite( sz_loggedin_user_id(), $sz->groups->current_group->id ) ) {
		if ( groups_accept_invite( sz_loggedin_user_id(), $sz->groups->current_group->id ) )
			sz_core_add_message( __( 'Group invite accepted', 'sportszone' ) );
		else
			sz_core_add_message( __( 'There was an error accepting the group invitation. Please try again.', 'sportszone' ), 'error' );
		sz_core_redirect( sz_get_group_permalink( $sz->groups->current_group ) );
	}

	// If the user has submitted a request, send it.
	if ( isset( $_POST['group-request-send']) ) {

		// Check the nonce.
		if ( !check_admin_referer( 'groups_request_membership' ) )
			return false;

		if ( !groups_send_membership_request( sz_loggedin_user_id(), $sz->groups->current_group->id ) ) {
			sz_core_add_message( __( 'There was an error sending your group membership request. Please try again.', 'sportszone' ), 'error' );
		} else {
			sz_core_add_message( __( 'Your membership request was sent to the group administrator successfully. You will be notified when the group administrator responds to your request.', 'sportszone' ) );
		}
		sz_core_redirect( sz_get_group_permalink( $sz->groups->current_group ) );
	}

	/**
	 * Fires before the loading of a group's Request Memebership page.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the group currently being displayed.
	 */
	do_action( 'groups_screen_group_request_membership', $sz->groups->current_group->id );

	/**
	 * Filters the template to load for a group's Request Membership page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a group's Request Membership template.
	 */
	sz_core_load_template( apply_filters( 'groups_template_group_request_membership', 'groups/single/home' ) );
}