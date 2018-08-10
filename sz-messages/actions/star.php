<?php
/**
 * Messages: Star action handler
 *
 * @package SportsZone
 * @subpackage MessageActions
 * @since 3.0.0
 */

/**
 * Action handler to set a message's star status for those not using JS.
 *
 * @since 2.3.0
 */
function sz_messages_star_action_handler() {
	if ( ! sz_is_user_messages() ) {
		return;
	}

	if ( false === ( sz_is_current_action( 'unstar' ) || sz_is_current_action( 'star' ) ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sz_action_variable( 1 ), 'sz-messages-star-' . sz_action_variable( 0 ) ) ) {
		wp_die( "Oops!  That's a no-no!" );
	}

	// Check capability.
	if ( ! is_user_logged_in() || ! sz_core_can_edit_settings() ) {
		return;
	}

	// Mark the star.
	sz_messages_star_set_action( array(
		'action'     => sz_current_action(),
		'message_id' => sz_action_variable(),
		'bulk'       => (bool) sz_action_variable( 2 )
	) );

	// Redirect back to previous screen.
	$redirect = wp_get_referer() ? wp_get_referer() : sz_displayed_user_domain() . sz_get_messages_slug();
	sz_core_redirect( $redirect );
	die();
}
add_action( 'sz_actions', 'sz_messages_star_action_handler' );