<?php
/**
 * SportsZone Friends Streams Loader.
 *
 * The friends component is for users to create relationships with each other.
 *
 * @package SportsZone
 * @subpackage Friends
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the sz-friends component.
 *
 * @since 1.6.0
 */
function sz_setup_friends() {
	sportszone()->friends = new SZ_Friends_Component();
}
add_action( 'sz_setup_components', 'sz_setup_friends', 6 );
