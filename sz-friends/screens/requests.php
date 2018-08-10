<?php
/**
 * Friends: User's "Friends > Requests" screen handler
 *
 * @package SportsZone
 * @subpackage FriendsScreens
 * @since 3.0.0
 */

/**
 * Catch and process the Requests page.
 *
 * @since 1.0.0
 */
function friends_screen_requests() {
	if ( sz_is_action_variable( 'accept', 0 ) && is_numeric( sz_action_variable( 1 ) ) ) {
		// Check the nonce.
		check_admin_referer( 'friends_accept_friendship' );

		if ( friends_accept_friendship( sz_action_variable( 1 ) ) )
			sz_core_add_message( __( 'Friendship accepted', 'sportszone' ) );
		else
			sz_core_add_message( __( 'Friendship could not be accepted', 'sportszone' ), 'error' );

		sz_core_redirect( trailingslashit( sz_loggedin_user_domain() . sz_current_component() . '/' . sz_current_action() ) );

	} elseif ( sz_is_action_variable( 'reject', 0 ) && is_numeric( sz_action_variable( 1 ) ) ) {
		// Check the nonce.
		check_admin_referer( 'friends_reject_friendship' );

		if ( friends_reject_friendship( sz_action_variable( 1 ) ) )
			sz_core_add_message( __( 'Friendship rejected', 'sportszone' ) );
		else
			sz_core_add_message( __( 'Friendship could not be rejected', 'sportszone' ), 'error' );

		sz_core_redirect( trailingslashit( sz_loggedin_user_domain() . sz_current_component() . '/' . sz_current_action() ) );

	} elseif ( sz_is_action_variable( 'cancel', 0 ) && is_numeric( sz_action_variable( 1 ) ) ) {
		// Check the nonce.
		check_admin_referer( 'friends_withdraw_friendship' );

		if ( friends_withdraw_friendship( sz_loggedin_user_id(), sz_action_variable( 1 ) ) )
			sz_core_add_message( __( 'Friendship request withdrawn', 'sportszone' ) );
		else
			sz_core_add_message( __( 'Friendship request could not be withdrawn', 'sportszone' ), 'error' );

		sz_core_redirect( trailingslashit( sz_loggedin_user_domain() . sz_current_component() . '/' . sz_current_action() ) );
	}

	/**
	 * Fires before the loading of template for the friends requests page.
	 *
	 * @since 1.0.0
	 */
	do_action( 'friends_screen_requests' );

	/**
	 * Filters the template used to display the My Friends page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Path to the friends request template to load.
	 */
	sz_core_load_template( apply_filters( 'friends_template_requests', 'members/single/home' ) );
}