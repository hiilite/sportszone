<?php
/**
 * Messages: User's "Messages > Notices" screen handler
 *
 * @package SportsZone
 * @subpackage MessageScreens
 * @since 3.0.0
 */

/**
 * Load the Messages > Notices screen.
 *
 * @since 1.0.0
 *
 * @return false|null False on failure.
 */
function messages_screen_notices() {

	if ( sz_action_variables() ) {
		sz_do_404();
		return;
	}

	/**
	 * Fires right before the loading of the Messages notices screen template file.
	 *
	 * @since 1.0.0
	 */
	do_action( 'messages_screen_notices' );

	/**
	 * Filters the template to load for the Messages notices screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Path to the messages template to load.
	 */
	sz_core_load_template( apply_filters( 'messages_template_notices', 'members/single/home' ) );
}