<?php
/**
 * SportsZone XProfile Loader.
 *
 * An extended profile component for users. This allows site admins to create
 * groups of fields for users to enter information about themselves.
 *
 * @package SportsZone
 * @subpackage XProfileLoader
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the sz-xprofile component.
 *
 * @since 1.6.0
 */
function sz_setup_xprofile() {
	$sz = sportszone();

	if ( ! isset( $sz->profile->id ) ) {
		$sz->profile = new SZ_XProfile_Component();
	}
}
add_action( 'sz_setup_components', 'sz_setup_xprofile', 2 );
