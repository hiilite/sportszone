<?php
/**
 * Messages template tags
 *
 * @since 3.0.0
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Fire specific hooks into the private messages template.
 *
 * @since 3.0.0
 *
 * @param string $when   Optional. Either 'before' or 'after'.
 * @param string $suffix Optional. Use it to add terms at the end of the hook name.
 */
function sz_nouveau_messages_hook( $when = '', $suffix = '' ) {
	$hook = array( 'sz' );

	if ( $when ) {
		$hook[] = $when;
	}

	// It's a message hook
	$hook[] = 'message';

	if ( $suffix ) {
		if ( 'compose_content' === $suffix ) {
			$hook[2] = 'messages';
		}

		$hook[] = $suffix;
	}

	sz_nouveau_hook( $hook );
}

/**
 * Load the new Messages User Interface
 *
 * @since 3.0.0
 */
function sz_nouveau_messages_member_interface() {
	/**
	 * Fires before the member messages content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_member_messages_content' );

	// Load the Private messages UI
	sz_get_template_part( 'common/js-templates/messages/index' );

	/**
	 * Fires after the member messages content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_member_messages_content' );
}
