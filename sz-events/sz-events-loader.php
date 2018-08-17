<?php
/**
 * SportsZone Events Loader.
 *
 * A events component, for users to event themselves together. Includes a
 * robust sub-component API that allows Events to be extended.
 * Comes preconfigured with an activity stream, discussion forums, and settings.
 *
 * @package SportsZone
 * @subpackage EventsLoader
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the sz-events component.
 *
 * @since 1.5.0
 */
function sz_setup_events() {
	sportszone()->events = new SZ_Events_Component();
}
add_action( 'sz_setup_components', 'sz_setup_events', 6 );
