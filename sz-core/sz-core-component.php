<?php
/**
 * Component classes.
 *
 * @package SportsZone
 * @subpackage Core
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SZ_Component' ) ) {
	require dirname( __FILE__ ) . '/classes/class-sz-component.php';
}
