<?php
/**
 * SportsZone Blogs Widgets.
 *
 * @package SportsZone
 * @subpackage BlogsWidgets
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register the widgets for the Blogs component.
 */
function sz_blogs_register_widgets() {
	global $wpdb;

	if ( sz_is_active( 'activity' ) && sz_is_root_blog( $wpdb->blogid ) ) {
		add_action( 'widgets_init', function() { register_widget( 'SZ_Blogs_Recent_Posts_Widget' ); } );
	}
}
add_action( 'sz_register_widgets', 'sz_blogs_register_widgets' );
