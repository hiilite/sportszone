<?php
/**
 * Activity: Unfavorite action
 *
 * @package SportsZone
 * @subpackage ActivityActions
 * @since 3.0.0
 */

/**
 * Remove activity from favorites.
 *
 * @since 1.2.0
 *
 * @return bool False on failure.
 */
function sz_activity_action_remove_favorite() {
	if ( ! is_user_logged_in() || ! sz_is_activity_component() || ! sz_is_current_action( 'unfavorite' ) )
		return false;

	// Check the nonce.
	check_admin_referer( 'unmark_favorite' );

	if ( sz_activity_remove_user_favorite( sz_action_variable( 0 ) ) )
		sz_core_add_message( __( 'Activity removed as favorite.', 'sportszone' ) );
	else
		sz_core_add_message( __( 'There was an error removing that activity as a favorite. Please try again.', 'sportszone' ), 'error' );

	sz_core_redirect( wp_get_referer() . '#activity-' . sz_action_variable( 0 ) );
}
add_action( 'sz_actions', 'sz_activity_action_remove_favorite' );