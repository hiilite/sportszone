<?php
/**
 * SportsZone Activity Streams Loader.
 *
 * An activity stream component, for users, groups, and site tracking.
 *
 * @package SportsZone
 * @subpackage ActivityCore
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the sz-activity component.
 *
 * @since 1.6.0
 */
function sz_setup_activity() {
	sportszone()->activity = new SZ_Activity_Component();
}
add_action( 'sz_setup_components', 'sz_setup_activity', 6 );
