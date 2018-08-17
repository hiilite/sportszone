<?php
/**
 * Events: User's "Events > Invites" screen handler
 *
 * @package SportsZone
 * @subpackage EventScreens
 * @since 3.0.0
 */

/**
 * Handle the loading of a user's Events > Invites page.
 *
 * @since 1.0.0
 */
function events_screen_event_invites() {
	$event_id = (int)sz_action_variable( 1 );

	if ( sz_is_action_variable( 'accept' ) && is_numeric( $event_id ) ) {
		// Check the nonce.
		if ( !check_admin_referer( 'events_accept_invite' ) )
			return false;

		if ( !events_accept_invite( sz_loggedin_user_id(), $event_id ) ) {
			sz_core_add_message( __('Event invite could not be accepted', 'sportszone'), 'error' );
		} else {
			// Record this in activity streams.
			$event = events_get_event( $event_id );

			sz_core_add_message( sprintf( __( 'Event invite accepted. Visit %s.', 'sportszone' ), sz_get_event_link( $event ) ) );

			if ( sz_is_active( 'activity' ) ) {
				events_record_activity( array(
					'type'    => 'joined_event',
					'item_id' => $event->id
				) );
			}
		}

		if ( isset( $_GET['redirect_to'] ) ) {
			$redirect_to = urldecode( $_GET['redirect_to'] );
		} else {
			$redirect_to = trailingslashit( sz_loggedin_user_domain() . sz_get_events_slug() . '/' . sz_current_action() );
		}

		sz_core_redirect( $redirect_to );

	} elseif ( sz_is_action_variable( 'reject' ) && is_numeric( $event_id ) ) {
		// Check the nonce.
		if ( !check_admin_referer( 'events_reject_invite' ) )
			return false;

		if ( !events_reject_invite( sz_loggedin_user_id(), $event_id ) ) {
			sz_core_add_message( __( 'Event invite could not be rejected', 'sportszone' ), 'error' );
		} else {
			sz_core_add_message( __( 'Event invite rejected', 'sportszone' ) );
		}

		if ( isset( $_GET['redirect_to'] ) ) {
			$redirect_to = urldecode( $_GET['redirect_to'] );
		} else {
			$redirect_to = trailingslashit( sz_loggedin_user_domain() . sz_get_events_slug() . '/' . sz_current_action() );
		}

		sz_core_redirect( $redirect_to );
	}

	/**
	 * Fires before the loading of a users Events > Invites template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $event_id ID of the event being displayed
	 */
	do_action( 'events_screen_event_invites', $event_id );

	/**
	 * Filters the template to load for a users Events > Invites page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a users Events > Invites page template.
	 */
	sz_core_load_template( apply_filters( 'events_template_event_invites', 'members/single/home' ) );
}