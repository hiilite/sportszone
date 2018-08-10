<?php
/**
 * Messages: User's "Messages > Starred" screen handler
 *
 * @package SportsZone
 * @subpackage MessageScreens
 * @since 3.0.0
 */

/**
 * Screen handler to display a user's "Starred" private messages page.
 *
 * @since 2.3.0
 */
function sz_messages_star_screen() {
	add_action( 'sz_template_content', 'sz_messages_star_content' );

	/**
	 * Fires right before the loading of the "Starred" messages box.
	 *
	 * @since 2.3.0
	 */
	do_action( 'sz_messages_screen_star' );

	sz_core_load_template( 'members/single/plugins' );
}

/**
 * Screen content callback to display a user's "Starred" messages page.
 *
 * @since 2.3.0
 */
function sz_messages_star_content() {
	// Add our message thread filter.
	add_filter( 'sz_after_has_message_threads_parse_args', 'sz_messages_filter_starred_message_threads' );

	// Load the message loop template part.
	sz_get_template_part( 'members/single/messages/messages-loop' );

	// Remove our filter.
	remove_filter( 'sz_after_has_message_threads_parse_args', 'sz_messages_filter_starred_message_threads' );
}
