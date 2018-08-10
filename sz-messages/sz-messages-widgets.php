<?php
/**
 * SportsZone Messages Widgets.
 *
 * @package SportsZone
 * @subpackage Messages
 * @since 1.9.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register widgets for the Messages component.
 *
 * @since 1.9.0
 */
function sz_messages_register_widgets() {
	add_action( 'widgets_init', function() { register_widget( 'SZ_Messages_Sitewide_Notices_Widget' ); } );
}
add_action( 'sz_register_widgets', 'sz_messages_register_widgets' );
