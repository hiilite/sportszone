<?php
/**
 * Groups: Random group action handler
 *
 * @package SportsZone
 * @subpackage GroupActions
 * @since 3.0.0
 */

/**
 * Catch requests for a random group page (example.com/groups/?random-group) and redirect.
 *
 * @since 1.2.0
 */
function groups_action_redirect_to_random_group() {

	if ( sz_is_groups_component() && isset( $_GET['random-group'] ) ) {
		$group = SZ_Groups_Group::get_random( 1, 1 );

		sz_core_redirect( trailingslashit( sz_get_groups_directory_permalink() . $group['groups'][0]->slug ) );
	}
}
add_action( 'sz_actions', 'groups_action_redirect_to_random_group' );