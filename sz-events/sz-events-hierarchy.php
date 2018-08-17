<?php
/**
 * Add hierarchical event functionality to your SportsZone-powered community site.
 *
 * @package   HierarchicalEventsForSZ
 * @author    dcavins
 * @license   GPL-2.0+
 * @copyright 2016 David Cavins
 *
 * @wordpress-plugin
 * Based on Plugin:       Hierarchical Events for SZ
 * Plugin URI:        https://github.com/dcavins/hierarchical-events-for-bp
 * Description:       Add hierarchical event functionality to your SportsZone-powered community site.
 * Version:           1.0.0
 * Author:            dcavins
 * Text Domain:       hierarchical-events-for-bp
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/dcavins/hierarchical-events-for-bp
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

function hierarchical_events_for_sz_init() {

	// Take an early out if the events component isn't activated.
	if ( ! sz_is_active( 'events' ) ) {
		return;
	}

	// This plugin requires SportsZone 2.7 or greater.
	if ( ! function_exists( 'sz_get_version' ) || version_compare( sz_get_version(), '2.7', '<' ) ) {
		sz_core_add_message( __( 'Hierarchical Events for SportsZone requires SportsZone 2.7 or newer.', 'hierarchical-events-for-sz' ), 'error' );
		return;
	}

	// Helper functions
	require_once( plugin_dir_path( __FILE__ ) . 'hierarchy/includes/hgsz-internal-functions.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'hierarchy/includes/hgsz-functions.php' );

	// Template output functions
	require_once( plugin_dir_path( __FILE__ ) . 'hierarchy/public/views/template-tags.php' );

	// The SZ_Event_Extension class
	require_once( plugin_dir_path( __FILE__ ) . 'hierarchy/includes/class-sz-event-extension.php' );

	// The main class
	require_once( plugin_dir_path( __FILE__ ) . 'hierarchy/public/class-hgsz.php' );
	$hgsz_public = new HGSZ_Public();
	$hgsz_public->add_action_hooks();

	// Admin and dashboard functionality
	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'hierarchy/admin/class-hgsz-admin.php' );
		$hgsz_admin = new HGSZ_Admin();
		$hgsz_admin->add_action_hooks();
	}

}
//add_action( 'sz_loaded', 'hierarchical_events_for_sz_init' );
