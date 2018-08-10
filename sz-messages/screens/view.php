<?php
/**
 * Messages: Conversation thread screen handler
 *
 * @package SportsZone
 * @subpackage MessageScreens
 * @since 3.0.0
 */

/**
 * Load an individual conversation screen.
 *
 * @since 1.0.0
 *
 * @return false|null False on failure.
 */
function messages_screen_conversation() {

	// Bail if not viewing a single message.
	if ( ! sz_is_messages_component() || ! sz_is_current_action( 'view' ) ) {
		return false;
	}

	$thread_id = (int) sz_action_variable( 0 );

	if ( empty( $thread_id ) || ! messages_is_valid_thread( $thread_id ) ) {
		if ( is_user_logged_in() ) {
			sz_core_add_message( __( 'The conversation you tried to access is no longer available', 'sportszone' ), 'error' );
		}

		sz_core_redirect( trailingslashit( sz_displayed_user_domain() . sz_get_messages_slug() ) );
	}

	// No access.
	if ( ( ! messages_check_thread_access( $thread_id ) || ! sz_is_my_profile() ) && ! sz_current_user_can( 'sz_moderate' ) ) {
		// If not logged in, prompt for login.
		if ( ! is_user_logged_in() ) {
			sz_core_no_access();
			return;

		// Redirect away.
		} else {
			sz_core_add_message( __( 'You do not have access to that conversation.', 'sportszone' ), 'error' );
			sz_core_redirect( trailingslashit( sz_loggedin_user_domain() . sz_get_messages_slug() ) );
		}
	}

	// Load up SportsZone one time.
	$sz = sportszone();

	// Decrease the unread count in the nav before it's rendered.
	$count    = sz_get_total_unread_messages_count();
	$class    = ( 0 === $count ) ? 'no-count' : 'count';
	$nav_name = sprintf( __( 'Messages <span class="%s">%s</span>', 'sportszone' ), esc_attr( $class ), sz_core_number_format( $count ) );

	// Edit the Navigation name.
	$sz->members->nav->edit_nav( array(
		'name' => $nav_name,
	), $sz->messages->slug );

	/**
	 * Fires right before the loading of the Messages view screen template file.
	 *
	 * @since 1.7.0
	 */
	do_action( 'messages_screen_conversation' );

	/**
	 * Filters the template to load for the Messages view screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Path to the messages template to load.
	 */
	sz_core_load_template( apply_filters( 'messages_template_view_message', 'members/single/home' ) );
}
add_action( 'sz_screens', 'messages_screen_conversation' );