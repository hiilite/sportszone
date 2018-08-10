<?php
/**
 * SportsZone Core Loader.
 *
 * Core contains the commonly used functions, classes, and APIs.
 *
 * @package SportsZone
 * @subpackage Core
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the sz-core component.
 *
 * @since 1.6.0
 */
function sz_setup_core() {
	sportszone()->core = new SZ_Core();
}
add_action( 'sz_loaded', 'sz_setup_core', 0 );
