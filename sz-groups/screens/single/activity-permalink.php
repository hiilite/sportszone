<?php
/**
 * Groups: Single group activity permalink screen handler
 *
 * Note - This has never worked.
 * See {@link https://sportszone.trac.wordpress.org/ticket/2579}
 *
 * @package SportsZone
 * @subpackage GroupsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a single group activity item.
 *
 * @since 1.2.0
 */
function groups_screen_group_activity_permalink() {
	if ( !sz_is_groups_component() || !sz_is_active( 'activity' ) || ( sz_is_active( 'activity' ) && !sz_is_current_action( sz_get_activity_slug() ) ) || !sz_action_variable( 0 ) )
		return false;

	sportszone()->is_single_item = true;

	/** This filter is documented in sz-groups/sz-groups-screens.php */
	sz_core_load_template( apply_filters( 'groups_template_group_home', 'groups/single/home' ) );
}
add_action( 'sz_screens', 'groups_screen_group_activity_permalink' );