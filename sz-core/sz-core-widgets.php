<?php
/**
 * SportsZone Core Component Widgets.
 *
 * @package SportsZone
 * @subpackage Core
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register sz-core widgets.
 *
 * @since 1.0.0
 */
function sz_core_register_widgets() {
	add_action( 'widgets_init', function() { register_widget( 'SZ_Core_Login_Widget' ); } );
}
add_action( 'sz_register_widgets', 'sz_core_register_widgets' );
