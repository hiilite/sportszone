<?php
/**
 * XProfile: User's "Profile > Change Cover Image" screen handler
 *
 * @package SportsZone
 * @subpackage XProfileScreens
 * @since 3.0.0
 */

/**
 * Displays the change cover image page.
 *
 * @since 2.4.0
 */
function xprofile_screen_change_cover_image() {

	// Bail if not the correct screen.
	if ( ! sz_is_my_profile() && ! sz_current_user_can( 'sz_moderate' ) ) {
		return false;
	}

	/**
	 * Fires right before the loading of the XProfile change cover image screen template file.
	 *
	 * @since 2.4.0
	 */
	do_action( 'xprofile_screen_change_cover_image' );

	/**
	 * Filters the template to load for the XProfile cover image screen.
	 *
	 * @since 2.4.0
	 *
	 * @param string $template Path to the XProfile cover image template to load.
	 */
	sz_core_load_template( apply_filters( 'xprofile_template_cover_image', 'members/single/home' ) );
}