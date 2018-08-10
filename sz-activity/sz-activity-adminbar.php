<?php
/**
 * SportsZone Activity Toolbar.
 *
 * Handles the activity functions related to the WordPress Toolbar.
 *
 * @package SportsZone
 * @subpackage Activity
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Add the Activity top-level menu link when viewing single activity item.
 *
 * @since 2.6.0
 *
 * @return null Null if user does not have access to editing functionality.
 */
function sz_activity_admin_menu() {
	global $wp_admin_bar;

	// Only show if viewing a single activity item.
	if ( ! sz_is_single_activity() ) {
		return;
	}

	// Only show this menu to super admins
	if ( ! sz_current_user_can( 'sz_moderate' ) ) {
		return;
	}

	$activity_edit_link = add_query_arg( array(
		'page' => 'sz-activity',
		'aid' => sz_current_action(),
		'action' => 'edit'
	), sz_get_admin_url( 'admin.php' ) );

	// Add the top-level Edit Activity button.
	$wp_admin_bar->add_menu( array(
		'id'    => 'activity-admin',
		'title' => __( 'Edit Activity', 'sportszone' ),
		'href'  => esc_url( $activity_edit_link ),
	) );
}
add_action( 'admin_bar_menu', 'sz_activity_admin_menu', 99 );
