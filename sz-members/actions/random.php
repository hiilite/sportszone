<?php
/**
 * Members: Random member action handler
 *
 * @package SportsZone
 * @subpackage MembersActions
 * @since 3.0.0
 */

/**
 * Redirect to a random member page when visiting a ?random-member URL.
 *
 * @since 1.0.0
 */
function sz_core_get_random_member() {
	if ( ! isset( $_GET['random-member'] ) )
		return;

	$user = sz_core_get_users( array( 'type' => 'random', 'per_page' => 1 ) );
	sz_core_redirect( sz_core_get_user_domain( $user['users'][0]->id ) );
}
add_action( 'sz_actions', 'sz_core_get_random_member' );