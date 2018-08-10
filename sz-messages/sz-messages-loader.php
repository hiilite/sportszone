<?php
/**
 * SportsZone Messages Loader.
 *
 * A private messages component, for users to send messages to each other.
 *
 * @package SportsZone
 * @subpackage MessagesLoader
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the sz-messages component.
 *
 * @since 1.5.0
 */
function sz_setup_messages() {
	sportszone()->messages = new SZ_Messages_Component();
}
add_action( 'sz_setup_components', 'sz_setup_messages', 6 );
