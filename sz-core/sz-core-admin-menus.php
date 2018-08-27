<?php
/**
 * Main SportsZone Admin Menus Class.
 *
 * @package SportsZone
 * @subpackage CoreAdministration
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup SportsZone Admin.
 *
 * @since 1.6.0
 *
 */
function sz_admin_menus() {
	sportszone()->admin_menus = new SZ_Admin_Menus();
	return;
}
