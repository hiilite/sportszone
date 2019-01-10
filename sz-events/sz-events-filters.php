<?php
/**
 * SportsZone Events Filters.
 *
 * @package SportsZone
 * @subpackage EventsFilters
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Filter SportsZone template locations.
add_filter( 'sz_events_get_directory_template', 'sz_add_template_locations' );
add_filter( 'sz_get_single_event_template',    'sz_add_template_locations' );

/* Apply WordPress defined filters */
add_filter( 'sz_get_event_description',         'wptexturize' );
add_filter( 'sz_get_event_description_excerpt', 'wptexturize' );
add_filter( 'sz_get_event_name',                'wptexturize' );

add_filter( 'sz_get_event_description',         'convert_smilies' );
add_filter( 'sz_get_event_description_excerpt', 'convert_smilies' );

add_filter( 'sz_get_event_description',         'convert_chars' );
add_filter( 'sz_get_event_description_excerpt', 'convert_chars' );
add_filter( 'sz_get_event_name',                'convert_chars' );

add_filter( 'sz_get_event_description',         'wpautop' );
add_filter( 'sz_get_event_description_excerpt', 'wpautop' );

add_filter( 'sz_get_event_description',         'make_clickable', 9 );
add_filter( 'sz_get_event_description_excerpt', 'make_clickable', 9 );

add_filter( 'sz_get_event_name',                    'wp_filter_kses',        1 );
add_filter( 'sz_get_event_permalink',               'wp_filter_kses',        1 );
add_filter( 'sz_get_event_description',             'sz_events_filter_kses', 1 );
add_filter( 'sz_get_event_description_excerpt',     'wp_filter_kses',        1 );
add_filter( 'events_event_name_before_save',        'wp_filter_kses',        1 );
add_filter( 'events_event_description_before_save', 'wp_filter_kses',        1 );

add_filter( 'sz_get_event_description',         'stripslashes' );
add_filter( 'sz_get_event_description_excerpt', 'stripslashes' );
add_filter( 'sz_get_event_name',                'stripslashes' );
add_filter( 'sz_get_event_member_name',         'stripslashes' );
add_filter( 'sz_get_event_member_link',         'stripslashes' );

add_filter( 'events_event_name_before_save',        'force_balance_tags' );
add_filter( 'events_event_description_before_save', 'force_balance_tags' );

// Trim trailing spaces from name and description when saving.
add_filter( 'events_event_name_before_save',        'trim' );
add_filter( 'events_event_description_before_save', 'trim' );

// Support emoji.
if ( function_exists( 'wp_encode_emoji' ) ) {
	add_filter( 'events_event_description_before_save', 'wp_encode_emoji' );
}

// Escape output of new event creation details.
add_filter( 'sz_get_new_event_name',        'esc_attr'     );
add_filter( 'sz_get_new_event_description', 'esc_textarea' );

// Format numerical output.
add_filter( 'sz_get_total_event_count',          'sz_core_number_format' );
add_filter( 'sz_get_event_total_for_member',     'sz_core_number_format' );
add_filter( 'sz_get_event_total_members',        'sz_core_number_format' );
add_filter( 'sz_get_total_event_count_for_user', 'sz_core_number_format' );

// Activity component integration.
add_filter( 'sz_activity_at_name_do_notifications', 'sz_events_disable_at_mention_notification_for_non_public_events', 10, 4 );

// Default event avatar.
add_filter( 'sz_core_avatar_default',       'sz_events_default_avatar', 10, 3 );
add_filter( 'sz_core_avatar_default_thumb', 'sz_events_default_avatar', 10, 3 );

// Default event cover_image.
add_filter( 'sz_core_cover_image_default',       'sz_events_default_cover_image', 10, 3 );
add_filter( 'sz_core_cover_image_default_thumb', 'sz_events_default_cover_image', 10, 3 );

/**
 * Filter output of Event Description through WordPress's KSES API.
 *
 * @since 1.1.0
 *
 * @param string $content Content to filter.
 * @return string
 */
function sz_events_filter_kses( $content = '' ) {

	/**
	 * Note that we don't immediately bail if $content is empty. This is because
	 * WordPress's KSES API calls several other filters that might be relevant
	 * to someone's workflow (like `pre_kses`)
	 */

	// Get allowed tags using core WordPress API allowing third party plugins
	// to target the specific `sportszone-events` context.
	$allowed_tags = wp_kses_allowed_html( 'sportszone-events' );

	// Add our own tags allowed in event descriptions.
	$allowed_tags['a']['class']    = array();
	$allowed_tags['img']           = array();
	$allowed_tags['img']['src']    = array();
	$allowed_tags['img']['alt']    = array();
	$allowed_tags['img']['width']  = array();
	$allowed_tags['img']['height'] = array();
	$allowed_tags['img']['class']  = array();
	$allowed_tags['img']['id']     = array();
	$allowed_tags['code']          = array();

	/**
	 * Filters the HTML elements allowed for a given context.
	 *
	 * @since 1.2.0
	 *
	 * @param string $allowed_tags Allowed tags, attributes, and/or entities.
	 */
	$tags = apply_filters( 'sz_events_filter_kses', $allowed_tags );

	// Return KSES'ed content, allowing the above tags.
	return wp_kses( $content, $tags );
}

/**
 * Should SportsZone load the mentions scripts and related assets, including results to prime the
 * mentions suggestions?
 *
 * @since 2.2.0
 *
 * @param bool $load_mentions    True to load mentions assets, false otherwise.
 * @param bool $mentions_enabled True if mentions are enabled.
 * @return bool True if mentions scripts should be loaded.
 */
function sz_events_maybe_load_mentions_scripts( $load_mentions, $mentions_enabled ) {
	if ( ! $mentions_enabled ) {
		return $load_mentions;
	}

	if ( $load_mentions || sz_is_event_activity() ) {
		return true;
	}

	return $load_mentions;
}
add_filter( 'sz_activity_maybe_load_mentions_scripts', 'sz_events_maybe_load_mentions_scripts', 10, 2 );

/**
 * Disable at-mention notifications for users who are not a member of the non-public event where the activity appears.
 *
 * @since 2.5.0
 *
 * @param bool                 $send      Whether to send the notification.
 * @param array                $usernames Array of all usernames being notified.
 * @param int                  $user_id   ID of the user to be notified.
 * @param SZ_Activity_Activity $activity  Activity object.
 * @return bool
 */
function sz_events_disable_at_mention_notification_for_non_public_events( $send, $usernames, $user_id, SZ_Activity_Activity $activity ) {
	// Skip the check for administrators, who can get notifications from non-public events.
	if ( sz_user_can( $user_id, 'sz_moderate' ) ) {
		return $send;
	}

	if ( 'events' === $activity->component && ! sz_user_can( $user_id, 'events_access_event', array( 'event_id' => $activity->item_id ) ) ) {
		$send = false;
	}

	return $send;
}

/**
 * Use the mystery event avatar for events.
 *
 * @since 2.6.0
 *
 * @param string $avatar Current avatar src.
 * @param array  $params Avatar params.
 * @return string
 */
function sz_events_default_avatar( $avatar, $params ) {
	if ( isset( $params['object'] ) && 'event' === $params['object'] ) {
		if ( isset( $params['type'] ) && 'thumb' === $params['type'] ) {
			$file = 'mystery-event-50.png';
		} else {
			$file = 'mystery-event.png';
		}

		$avatar = sportszone()->plugin_url . "sz-core/images/$file";
	}

	return $avatar;
}


/**
 * Use the mystery event cover_image for events.
 *
 * @since 2.6.0
 *
 * @param string $avatar Current avatar src.
 * @param array  $params Avatar params.
 * @return string
 */
function sz_events_default_cover_image( $cover_image, $params ) {
	if ( isset( $params['object'] ) && 'event' === $params['object'] ) {
		if ( isset( $params['type'] ) && 'thumb' === $params['type'] ) {
			$file = 'mystery-event-50.png';
		} else {
			$file = 'mystery-event.png';
		}

		$cover_image = sportszone()->plugin_url . "sz-core/images/$file";
	}

	return $cover_image;
}

/**
 * Filter the sz_user_can value to determine what the user can do
 * with regards to a specific event.
 *
 * @since 3.0.0
 *
 * @param bool   $retval     Whether or not the current user has the capability.
 * @param int    $user_id
 * @param string $capability The capability being checked for.
 * @param int    $site_id    Site ID. Defaults to the BP root blog.
 * @param array  $args       Array of extra arguments passed.
 *
 * @return bool
 */
function sz_events_user_can_filter( $retval, $user_id, $capability, $site_id, $args ) {
	if ( empty( $args['event_id'] ) ) {
		$event_id = sz_get_current_event_id();
	} else {
		$event_id = (int) $args['event_id'];
	}

	switch ( $capability ) {
		case 'events_pay_event':
			// Return early if the user isn't logged in or the event ID is unknown.
			if ( ! $user_id || ! $event_id ) {
				break;
			}

			// Set to false to begin with.
			$retval = false;

			// The event must allow joining, and the user should not currently be a member.
			$event = events_get_event( $event_id );
			// TODO: Check for Team Management status
			if ( ( 'paid' === sz_get_event_status( $event )
				&& ! events_is_user_member( $user_id, $event->id )
				&& ! events_is_user_banned( $user_id, $event->id ) )
				// Site admins can join any event they are not a member of.
				|| ( sz_user_can( $user_id, 'sz_moderate' )
				&& ! events_is_user_member( $user_id, $event->id ) )
			) {
				$retval = true;
			}
			break;
			
		case 'events_join_event':
			// Return early if the user isn't logged in or the event ID is unknown.
			if ( ! $user_id || ! $event_id ) {
				break;
			}

			// Set to false to begin with.
			$retval = false;

			// The event must allow joining, and the user should not currently be a member.
			$event = events_get_event( $event_id );
			if ( ( 'public' === sz_get_event_status( $event )
				&& ! events_is_user_member( $user_id, $event->id )
				&& ! events_is_user_banned( $user_id, $event->id ) )
				// Site admins can join any event they are not a member of.
				|| ( sz_user_can( $user_id, 'sz_moderate' )
				&& ! events_is_user_member( $user_id, $event->id ) )
			) {
				$retval = true;
			}
			break;

		case 'events_request_membership':
			// Return early if the user isn't logged in or the event ID is unknown.
			if ( ! $user_id || ! $event_id ) {
				break;
			}

			// Set to false to begin with.
			$retval = false;

			/*
			* The event must accept membership requests, and the user should not
			* currently be a member, have an active request, or be banned.
			*/
			$event = events_get_event( $event_id );
			if ( 'private' === sz_get_event_status( $event )
				&& ! events_is_user_member( $user_id, $event->id )
				&& ! events_check_for_membership_request( $user_id, $event->id )
				&& ! events_is_user_banned( $user_id, $event->id )
			) {
				$retval = true;
			}
			break;

		case 'events_send_invitation':
			// Return early if the user isn't logged in or the event ID is unknown.
			if ( ! $user_id || ! $event_id ) {
				break;
			}

			/*
			* The event must allow invitations, and the user should not
			* currently be a member or be banned from the event.
			*/
			// Users with the 'sz_moderate' cap can always send invitations.
			if ( sz_user_can( $user_id, 'sz_moderate' ) ) {
				$retval = true;
			} else {
				$invite_status = sz_event_get_invite_status( $event_id );

				switch ( $invite_status ) {
					case 'admins' :
						if ( events_is_user_admin( $user_id, $event_id ) ) {
							$retval = true;
						}
						break;

					case 'mods' :
						if ( events_is_user_mod( $user_id, $event_id ) || events_is_user_admin( $user_id, $event_id ) ) {
							$retval = true;
						}
						break;

					case 'members' :
						if ( events_is_user_member( $user_id, $event_id ) ) {
							$retval = true;
						}
						break;
				}
			}
			break;

		case 'events_receive_invitation':
			// Return early if the user isn't logged in or the event ID is unknown.
			if ( ! $user_id || ! $event_id ) {
				break;
			}

			// Set to false to begin with.
			$retval = false;

			/*
			* The event must allow invitations, and the user should not
			* currently be a member or be banned from the event.
			*/
			$event = events_get_event( $event_id );
			if ( in_array( sz_get_event_status( $event ), array( 'private', 'hidden' ), true )
				&& ! events_is_user_member( $user_id, $event->id )
				&& ! events_is_user_banned( $user_id, $event->id )
			) {
				$retval = true;
			}
			break;

		case 'events_access_event':
			// Return early if the event ID is unknown.
			if ( ! $event_id ) {
				break;
			}

			$event = events_get_event( $event_id );

			// If the check is for the logged-in user, use the SZ_Events_Event property.
			if ( $user_id === sz_loggedin_user_id() ) {
				$retval = $event->user_has_access;

			/*
			 * If the check is for a specified user who is not the logged-in user
			 * run the check manually.
			 */
			} elseif ( 'public' === sz_get_event_status( $event ) || events_is_user_member( $user_id, $event->id ) ) {
				$retval = true;
			}
			break;

		case 'events_see_event':
			// Return early if the event ID is unknown.
			if ( ! $event_id ) {
				break;
			}

			$event = events_get_event( $event_id );

			// If the check is for the logged-in user, use the SZ_Events_Event property.
			if ( $user_id === sz_loggedin_user_id() ) {
				$retval = $event->is_visible;

			/*
			 * If the check is for a specified user who is not the logged-in user
			 * run the check manually.
			 */
			} elseif ( 'hidden' !== sz_get_event_status( $event ) || events_is_user_member( $user_id, $event->id ) ) {
				$retval = true;
			}
			break;
	}

	return $retval;

}
add_filter( 'sz_user_can', 'sz_events_user_can_filter', 10, 5 );
