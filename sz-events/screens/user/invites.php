<?php
/**
 * Groups: User's "Groups > Invites" screen handler
 *
 * @package SportsZone
 * @subpackage GroupScreens
 * @since 3.0.0
 */

/**
 * Handle the loading of a user's Groups > Invites page.
 *
 * @since 1.0.0
 */
function groups_screen_group_invites() {
	$group_id = (int)sz_action_variable( 1 );

	if ( sz_is_action_variable( 'accept' ) && is_numeric( $group_id ) ) {
		// Check the nonce.
		if ( !check_admin_referer( 'groups_accept_invite' ) )
			return false;

		if ( !groups_accept_invite( sz_loggedin_user_id(), $group_id ) ) {
			sz_core_add_message( __('Group invite could not be accepted', 'sportszone'), 'error' );
		} else {
			// Record this in activity streams.
			$group = groups_get_group( $group_id );

			sz_core_add_message( sprintf( __( 'Group invite accepted. Visit %s.', 'sportszone' ), sz_get_group_link( $group ) ) );

			if ( sz_is_active( 'activity' ) ) {
				groups_record_activity( array(
					'type'    => 'joined_group',
					'item_id' => $group->id
				) );
			}
		}

		if ( isset( $_GET['redirect_to'] ) ) {
			$redirect_to = urldecode( $_GET['redirect_to'] );
		} else {
			$redirect_to = trailingslashit( sz_loggedin_user_domain() . sz_get_groups_slug() . '/' . sz_current_action() );
		}

		sz_core_redirect( $redirect_to );

	} elseif ( sz_is_action_variable( 'reject' ) && is_numeric( $group_id ) ) {
		// Check the nonce.
		if ( !check_admin_referer( 'groups_reject_invite' ) )
			return false;

		if ( !groups_reject_invite( sz_loggedin_user_id(), $group_id ) ) {
			sz_core_add_message( __( 'Group invite could not be rejected', 'sportszone' ), 'error' );
		} else {
			sz_core_add_message( __( 'Group invite rejected', 'sportszone' ) );
		}

		if ( isset( $_GET['redirect_to'] ) ) {
			$redirect_to = urldecode( $_GET['redirect_to'] );
		} else {
			$redirect_to = trailingslashit( sz_loggedin_user_domain() . sz_get_groups_slug() . '/' . sz_current_action() );
		}

		sz_core_redirect( $redirect_to );
	}

	/**
	 * Fires before the loading of a users Groups > Invites template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $group_id ID of the group being displayed
	 */
	do_action( 'groups_screen_group_invites', $group_id );

	/**
	 * Filters the template to load for a users Groups > Invites page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a users Groups > Invites page template.
	 */
	sz_core_load_template( apply_filters( 'groups_template_group_invites', 'members/single/home' ) );
}