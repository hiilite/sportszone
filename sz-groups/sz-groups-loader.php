<?php
/**
 * SportsZone Groups Loader.
 *
 * A groups component, for users to group themselves together. Includes a
 * robust sub-component API that allows Groups to be extended.
 * Comes preconfigured with an activity stream, discussion forums, and settings.
 *
 * @package SportsZone
 * @subpackage GroupsLoader
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the sz-groups component.
 *
 * @since 1.5.0
 */
function sz_setup_groups() {
	sportszone()->groups = new SZ_Groups_Component();
}
add_action( 'sz_setup_components', 'sz_setup_groups', 6 );
