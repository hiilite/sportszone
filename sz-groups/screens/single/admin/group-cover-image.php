<?php
/**
 * Groups: Single group "Manage > Cover Image" screen handler
 *
 * @package SportsZone
 * @subpackage GroupsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a group's Change cover image page.
 *
 * @since 2.4.0
 */
function groups_screen_group_admin_cover_image() {
	if ( 'group-cover-image' != sz_get_group_current_admin_tab() ) {
		return false;
	}

	// If the logged-in user doesn't have permission or if cover image uploads are disabled, then stop here.
	if ( ! sz_is_item_admin() || ! sz_group_use_cover_image_header() ) {
		return false;
	}

	/**
	 * Fires before the loading of the group Change cover image page template.
	 *
	 * @since 2.4.0
	 *
	 * @param int $id ID of the group that is being displayed.
	 */
	do_action( 'groups_screen_group_admin_cover_image', sz_get_current_group_id() );

	/**
	 * Filters the template to load for a group's Change cover image page.
	 *
	 * @since 2.4.0
	 *
	 * @param string $value Path to a group's Change cover image template.
	 */
	sz_core_load_template( apply_filters( 'groups_template_group_admin_cover_image', 'groups/single/home' ) );
}
add_action( 'sz_screens', 'groups_screen_group_admin_cover_image' );