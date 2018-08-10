<?php
/**
 * SportsZone Member Notifications Loader.
 *
 * Initializes the Notifications component.
 *
 * @package SportsZone
 * @subpackage NotificationsLoader
 * @since 1.9.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the sz-notifications component.
 *
 * @since 1.9.0
 */
function sz_setup_notifications() {
	sportszone()->notifications = new SZ_Notifications_Component();
}
add_action( 'sz_setup_components', 'sz_setup_notifications', 6 );
