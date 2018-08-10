<?php
/**
 * SportsZone Members Admin
 *
 * @package SportsZone
 * @subpackage MembersAdmin
 * @since 2.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Load the BP Members admin.
add_action( 'sz_init', array( 'SZ_Members_Admin', 'register_members_admin' ) );
