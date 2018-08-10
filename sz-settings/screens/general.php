<?php
/**
 * Settings: User's "Settings" screen handler
 *
 * @package SportsZone
 * @subpackage SettingsScreens
 * @since 3.0.0
 */

/**
 * Show the general settings template.
 *
 * @since 1.5.0
 */
function sz_settings_screen_general() {

	if ( sz_action_variables() ) {
		sz_do_404();
		return;
	}

	/**
	 * Filters the template file path to use for the general settings screen.
	 *
	 * @since 1.6.0
	 *
	 * @param string $value Directory path to look in for the template file.
	 */
	sz_core_load_template( apply_filters( 'sz_settings_screen_general_settings', 'members/single/settings/general' ) );
}

/**
 * Removes 'Email' sub nav, if no component has registered options there.
 *
 * @since 2.2.0
 */
function sz_settings_remove_email_subnav() {
	if ( ! has_action( 'sz_notification_settings' ) ) {
		sz_core_remove_subnav_item( SZ_SETTINGS_SLUG, 'notifications' );
	}
}
add_action( 'sz_actions', 'sz_settings_remove_email_subnav' );