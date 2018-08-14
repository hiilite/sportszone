<?php
/**
 * Add hierarchical group functionality to your SportsZone-powered community site.
 *
 * @package   HierarchicalGroupsForSZ
 * @author    dcavins
 * @license   GPL-2.0+
 * @copyright 2016 David Cavins
 *
 * @wordpress-plugin
 * Based on Plugin:       Hierarchical Groups for SZ
 * Plugin URI:        https://github.com/dcavins/hierarchical-groups-for-bp
 * Description:       Add hierarchical group functionality to your SportsZone-powered community site.
 * Version:           1.0.0
 * Author:            dcavins
 * Text Domain:       hierarchical-groups-for-bp
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/dcavins/hierarchical-groups-for-bp
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

function hierarchical_groups_for_sz_init() {

	// Take an early out if the groups component isn't activated.
	if ( ! sz_is_active( 'groups' ) ) {
		return;
	}

	// This plugin requires SportsZone 2.7 or greater.
	if ( ! function_exists( 'sz_get_version' ) || version_compare( sz_get_version(), '2.7', '<' ) ) {
		sz_core_add_message( __( 'Hierarchical Groups for SportsZone requires SportsZone 2.7 or newer.', 'hierarchical-groups-for-sz' ), 'error' );
		return;
	}

	// Helper functions
	require_once( plugin_dir_path( __FILE__ ) . 'hierarchy/includes/hgsz-internal-functions.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'hierarchy/includes/hgsz-functions.php' );

	// Template output functions
	require_once( plugin_dir_path( __FILE__ ) . 'hierarchy/public/views/template-tags.php' );

	// The SZ_Group_Extension class
	require_once( plugin_dir_path( __FILE__ ) . 'hierarchy/includes/class-sz-group-extension.php' );

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
add_action( 'sz_loaded', 'hierarchical_groups_for_sz_init' );

/**
 * Helper function.
 *
 * @return Fully-qualified URI to the root of the plugin.
 */
function hgsz_get_plugin_base_uri(){
	return plugin_dir_url( __FILE__ );
}

/**
 * Helper function.
 *
 * @return Fully-qualified URI to the root of the plugin.
 */
function hgsz_get_plugin_base_name(){
	return plugin_basename( __FILE__ );
}

/**
 * Helper function to return the current version of the plugin.
 *
 * @return string Current version of plugin.
 */
function hgsz_get_plugin_version(){
	return '1.0.0';
}
