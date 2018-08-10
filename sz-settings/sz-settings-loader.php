<?php
/**
 * SportsZone Settings Loader.
 *
 * @package SportsZone
 * @subpackage SettingsLoader
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the sz-settings component.
 *
 * @since 1.6.0
 */
function sz_setup_settings() {
	sportszone()->settings = new SZ_Settings_Component();
}
add_action( 'sz_setup_components', 'sz_setup_settings', 6 );
