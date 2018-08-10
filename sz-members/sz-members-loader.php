<?php
/**
 * SportsZone Member Loader.
 *
 * @package SportsZone
 * @subpackage Members
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the sz-members component.
 *
 * @since 1.6.0
 */
function sz_setup_members() {
	sportszone()->members = new SZ_Members_Component();
}
add_action( 'sz_setup_components', 'sz_setup_members', 1 );
