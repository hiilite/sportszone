<?php
/**
 * Events: Access protection action handler
 *
 * @package SportsZone
 * @subpackage EventActions
 * @since 3.0.0
 */

/**
 * Protect access to single events.
 *
 * @since 2.1.0
 */
function sz_events_event_access_protection() {
	if ( ! sz_is_event() ) {
		return;
	}

	$current_event   = events_get_current_event();
	$user_has_access = $current_event->user_has_access;
	$is_visible      = $current_event->is_visible;
	$no_access_args  = array();

	// The user can know about the event but doesn't have full access.
	if ( ! $user_has_access && $is_visible ) {
		// Always allow access to home and request-membership.
		if ( sz_is_current_action( 'home' ) || sz_is_current_action( 'request-membership' ) ) {
			$user_has_access = true;

		// User doesn't have access, so set up redirect args.
		} elseif ( is_user_logged_in() ) {
			$no_access_args = array(
				'message'  => __( 'You do not have access to this event.', 'sportszone' ),
				'root'     => sz_get_event_permalink( $current_event ) . 'home/',
				'redirect' => false
			);
		}
	}

	// Protect the admin tab from non-admins.
	if ( sz_is_current_action( 'admin' ) && ! sz_is_item_admin() ) {
		$user_has_access = false;
		$no_access_args  = array(
			'message'  => __( 'You are not an admin of this event.', 'sportszone' ),
			'root'     => sz_get_event_permalink( $current_event ),
			'redirect' => false
		);
	}

	/**
	 * Allow plugins to filter whether the current user has access to this event content.
	 *
	 * Note that if a plugin sets $user_has_access to false, it may also
	 * want to change the $no_access_args, to avoid problems such as
	 * logged-in users being redirected to wp-login.php.
	 *
	 * @since 2.1.0
	 *
	 * @param bool  $user_has_access True if the user has access to the
	 *                               content, otherwise false.
	 * @param array $no_access_args  Arguments to be passed to sz_core_no_access() in case
	 *                               of no access. Note that this value is passed by reference,
	 *                               so it can be modified by the filter callback.
	 */
	$user_has_access = apply_filters_ref_array( 'sz_event_user_has_access', array( $user_has_access, &$no_access_args ) );

	// If user has access, we return rather than redirect.
	if ( $user_has_access ) {
		return;
	}

	// Events that the user cannot know about should return a 404 for non-members.
	// Unset the current event so that you're not redirected
	// to the default event tab.
	if ( ! $is_visible ) {
		sportszone()->events->current_event = 0;
		sportszone()->is_single_item        = false;
		sz_do_404();
		return;
	} else {
		sz_core_no_access( $no_access_args );
	}

}
add_action( 'sz_actions', 'sz_events_event_access_protection' );