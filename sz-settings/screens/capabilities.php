<?php
/**
 * Settings: User's "Settings > Capabilities" screen handler
 *
 * @package SportsZone
 * @subpackage SettingsScreens
 * @since 3.0.0
 */

/**
 * Show the capabilities settings template.
 *
 * @since 1.6.0
 */
function sz_settings_screen_capabilities() {

	if ( sz_action_variables() ) {
		sz_do_404();
		return;
	}

	/**
	 * Filters the template file path to use for the capabilities settings screen.
	 *
	 * @since 1.6.0
	 *
	 * @param string $value Directory path to look in for the template file.
	 */
	sz_core_load_template( apply_filters( 'sz_settings_screen_capabilities', 'members/single/settings/capabilities' ) );
}