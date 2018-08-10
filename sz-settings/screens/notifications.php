<?php
/**
 * Settings: User's "Settings > Email" screen handler
 *
 * @package SportsZone
 * @subpackage SettingsScreens
 * @since 3.0.0
 */

/**
 * Show the notifications settings template.
 *
 * @since 1.5.0
 */
function sz_settings_screen_notification() {

	if ( sz_action_variables() ) {
		sz_do_404();
		return;
	}

	/**
	 * Filters the template file path to use for the notification settings screen.
	 *
	 * @since 1.6.0
	 *
	 * @param string $value Directory path to look in for the template file.
	 */
	sz_core_load_template( apply_filters( 'sz_settings_screen_notification_settings', 'members/single/settings/notifications' ) );
}