<?php
/**
 * Groups: Single group "Manage > Requests" screen handler
 *
 * @package SportsZone
 * @subpackage GroupsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of Admin > Membership Requests.
 *
 * @since 1.0.0
 */
function groups_screen_group_admin_requests() {
	$sz = sportszone();

	if ( 'membership-requests' != sz_get_group_current_admin_tab() ) {
		return false;
	}

	if ( ! sz_is_item_admin() || ( 'public' == $sz->groups->current_group->status ) ) {
		return false;
	}

	$request_action = (string) sz_action_variable( 1 );
	$membership_id  = (int) sz_action_variable( 2 );

	if ( !empty( $request_action ) && !empty( $membership_id ) ) {
		if ( 'accept' == $request_action && is_numeric( $membership_id ) ) {

			// Check the nonce first.
			if ( !check_admin_referer( 'groups_accept_membership_request' ) )
				return false;

			// Accept the membership request.
			if ( !groups_accept_membership_request( $membership_id ) )
				sz_core_add_message( __( 'There was an error accepting the membership request. Please try again.', 'sportszone' ), 'error' );
			else
				sz_core_add_message( __( 'Group membership request accepted', 'sportszone' ) );

		} elseif ( 'reject' == $request_action && is_numeric( $membership_id ) ) {
			/* Check the nonce first. */
			if ( !check_admin_referer( 'groups_reject_membership_request' ) )
				return false;

			// Reject the membership request.
			if ( !groups_reject_membership_request( $membership_id ) )
				sz_core_add_message( __( 'There was an error rejecting the membership request. Please try again.', 'sportszone' ), 'error' );
			else
				sz_core_add_message( __( 'Group membership request rejected', 'sportszone' ) );
		}

		/**
		 * Fires before the redirect if a group membership request has been handled.
		 *
		 * @since 1.0.0
		 *
		 * @param int    $id             ID of the group that was edited.
		 * @param string $request_action Membership request action being performed.
		 * @param int    $membership_id  The key of the action_variables array that you want.
		 */
		do_action( 'groups_group_request_managed', $sz->groups->current_group->id, $request_action, $membership_id );
		sz_core_redirect( sz_get_group_permalink( groups_get_current_group() ) . 'admin/membership-requests/' );
	}

	/**
	 * Fires before the loading of the group membership request page template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the group that is being displayed.
	 */
	do_action( 'groups_screen_group_admin_requests', $sz->groups->current_group->id );

	/**
	 * Filters the template to load for a group's membership request page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a group's membership request template.
	 */
	sz_core_load_template( apply_filters( 'groups_template_group_admin_requests', 'groups/single/home' ) );
}
add_action( 'sz_screens', 'groups_screen_group_admin_requests' );