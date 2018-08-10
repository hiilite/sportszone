<?php
/**
 * Members: Directory screen handler
 *
 * @package SportsZone
 * @subpackage MembersScreens
 * @since 3.0.0
 */

/**
 * Handle the display of the members directory index.
 *
 * @since 1.5.0
 */
function sz_members_screen_index() {
	if ( sz_is_members_directory() ) {
		sz_update_is_directory( true, 'members' );

		/**
		 * Fires right before the loading of the Member directory index screen template file.
		 *
		 * @since 1.5.0
		 */
		do_action( 'sz_members_screen_index' );

		/**
		 * Filters the template to load for the Member directory page screen.
		 *
		 * @since 1.5.0
		 *
		 * @param string $value Path to the member directory template to load.
		 */
		sz_core_load_template( apply_filters( 'sz_members_screen_index', 'members/index' ) );
	}
}
add_action( 'sz_screens', 'sz_members_screen_index' );