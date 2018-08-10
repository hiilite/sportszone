<?php
/**
 * Activity: Favorite action
 *
 * @package SportsZone
 * @subpackage ActivityActions
 * @since 3.0.0
 */

/**
 * Mark activity as favorite.
 *
 * @since 1.2.0
 *
 * @return bool False on failure.
 */
function sz_activity_action_mark_favorite() {
	if ( !is_user_logged_in() || !sz_is_activity_component() || !sz_is_current_action( 'favorite' ) )
		return false;

	// Check the nonce.
	check_admin_referer( 'mark_favorite' );

	if ( sz_activity_add_user_favorite( sz_action_variable( 0 ) ) )
		sz_core_add_message( __( 'Activity marked as favorite.', 'sportszone' ) );
	else
		sz_core_add_message( __( 'There was an error marking that activity as a favorite. Please try again.', 'sportszone' ), 'error' );

	sz_core_redirect( wp_get_referer() . '#activity-' . sz_action_variable( 0 ) );
}
add_action( 'sz_actions', 'sz_activity_action_mark_favorite' );