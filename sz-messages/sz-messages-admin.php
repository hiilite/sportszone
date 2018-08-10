<?php
/**
 * SportsZone Members component admin screens.
 *
 * @package SportsZone
 * @subpackage Messages
 * @since 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Load the Sitewide Notices Admin
add_action( sz_core_admin_hook(), array( 'SZ_Messages_Notices_Admin', 'register_notices_admin' ), 9 );
