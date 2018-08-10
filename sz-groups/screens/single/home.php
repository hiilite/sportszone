<?php
/**
 * Groups: Single group "Home" screen handler
 *
 * @package SportsZone
 * @subpackage GroupsScreens
 * @since 3.0.0
 */

/**
 * Handle the loading of a single group's page.
 *
 * @since 1.0.0
 */
function groups_screen_group_home() {

	if ( ! sz_is_single_item() ) {
		return false;
	}

	/**
	 * Fires before the loading of a single group's page.
	 *
	 * @since 1.0.0
	 */
	do_action( 'groups_screen_group_home' );

	/**
	 * Filters the template to load for a single group's page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a single group's template to load.
	 */
	sz_core_load_template( apply_filters( 'groups_template_group_home', 'groups/single/home' ) );
}