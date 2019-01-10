<?php
/**
 * SportsZone Events Notification Functions.
 *
 * These functions handle the recording, deleting and formatting of notifications
 * for the user and for this specific component.
 *
 * @package SportsZone
 * @subpackage EventsActivity
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/** Emails ********************************************************************/

/**
 * Notify all event members when a event is updated.
 *
 * @since 1.0.0
 *
 * @param int                  $event_id  ID of the event.
 * @param SZ_Events_Event|null $old_event Event before new details were saved.
 */
function events_notification_event_updated( $event_id = 0, $old_event = null ) {
	$event = events_get_event( $event_id );

	if ( $old_event instanceof SZ_Events_Event ) {
		$changed = array();

		if ( $event->name !== $old_event->name ) {
			$changed[] = sprintf(
				_x( '* Name changed from "%s" to "%s".', 'Event update email text', 'sportszone' ),
				esc_html( $old_event->name ),
				esc_html( $event->name )
			);
		}

		if ( $event->description !== $old_event->description ) {
			$changed[] = sprintf(
				_x( '* Description changed from "%s" to "%s".', 'Event update email text', 'sportszone' ),
				esc_html( $old_event->description ),
				esc_html( $event->description )
			);
		}

		if ( $event->slug !== $old_event->slug ) {
			$changed[] = sprintf(
				_x( '* Permalink changed from "%s" to "%s".', 'Event update email text', 'sportszone' ),
				esc_url( sz_get_event_permalink( $old_event ) ),
				esc_url( sz_get_event_permalink( $event ) )
			);
		}
	}

	/**
	 * Filters the bullet points listing updated items in the email notification after a event is updated.
	 *
	 * @since 2.2.0
	 *
	 * @param array $changed Array of bullet points.
	 */
	$changed = apply_filters( 'events_notification_event_update_updated_items', $changed );

	$changed_text = '';
	if ( ! empty( $changed ) ) {
		$changed_text = implode( "\n", $changed );
	}

	$user_ids = SZ_Events_Member::get_event_member_ids( $event->id );
	foreach ( (array) $user_ids as $user_id ) {

		// Continue if member opted out of receiving this email.
		if ( 'no' === sz_get_user_meta( $user_id, 'notification_events_event_updated', true ) ) {
			continue;
		}

		$unsubscribe_args = array(
			'user_id'           => $user_id,
			'notification_type' => 'events-details-updated',
		);

		$args = array(
			'tokens' => array(
				'changed_text' => $changed_text,
				'event'        => $event,
				'event.id'     => $event_id,
				'event.url'    => esc_url( sz_get_event_permalink( $event ) ),
				'event.name'   => $event->name,
				'unsubscribe'  => esc_url( sz_email_get_unsubscribe_link( $unsubscribe_args ) ),
			),
		);
		sz_send_email( 'events-details-updated', (int) $user_id, $args );
	}

	/**
	 * Fires after the notification is sent that a event has been updated.
	 *
	 * See https://sportszone.trac.wordpress.org/ticket/3644 for blank message parameter.
	 *
	 * @since 1.5.0
	 * @since 2.5.0 $subject has been unset and is deprecated.
	 *
	 * @param array  $user_ids Array of user IDs to notify about the update.
	 * @param string $subject  Deprecated in 2.5; now an empty string.
	 * @param string $value    Empty string preventing PHP error.
	 * @param int    $event_id ID of the event that was updated.
	 */
	do_action( 'sz_events_sent_updated_email', $user_ids, '', '', $event_id );
}

/**
 * Notify event admin about new membership request.
 *
 * @since 1.0.0
 *
 * @param int $requesting_user_id ID of the user requesting event membership.
 * @param int $admin_id           ID of the event admin.
 * @param int $event_id           ID of the event.
 * @param int $membership_id      ID of the event membership object.
 */
function events_notification_new_membership_request( $requesting_user_id = 0, $admin_id = 0, $event_id = 0, $membership_id = 0 ) {

	// Trigger a SportsZone Notification.
	if ( sz_is_active( 'notifications' ) ) {
		sz_notifications_add_notification( array(
			'user_id'           => $admin_id,
			'item_id'           => $event_id,
			'secondary_item_id' => $requesting_user_id,
			'component_name'    => sportszone()->events->id,
			'component_action'  => 'new_membership_request',
		) );
	}

	// Bail if member opted out of receiving this email.
	if ( 'no' === sz_get_user_meta( $admin_id, 'notification_events_membership_request', true ) ) {
		return;
	}

	$unsubscribe_args = array(
		'user_id'           => $admin_id,
		'notification_type' => 'events-membership-request',
	);

	$event = events_get_event( $event_id );
	$args  = array(
		'tokens' => array(
			'admin.id'             => $admin_id,
			'event'                => $event,
			'event.name'           => $event->name,
			'event.id'             => $event_id,
			'event-requests.url'   => esc_url( sz_get_event_permalink( $event ) . 'admin/membership-requests' ),
			'membership.id'        => $membership_id,
			'profile.url'          => esc_url( sz_core_get_user_domain( $requesting_user_id ) ),
			'requesting-user.id'   => $requesting_user_id,
			'requesting-user.name' => sz_core_get_user_displayname( $requesting_user_id ),
			'unsubscribe'          => esc_url( sz_email_get_unsubscribe_link( $unsubscribe_args ) ),
		),
	);
	sz_send_email( 'events-membership-request', (int) $admin_id, $args );
}

/**
 * Notify event admin about new membership request.
 *
 * @since 1.0.0
 *
 * @param int $requesting_user_id ID of the user requesting event membership.
 * @param int $admin_id           ID of the event admin.
 * @param int $event_id           ID of the event.
 * @param int $membership_id      ID of the event membership object.
 */
function events_notification_team_joined( $requesting_user_id = 0, $admin_id = 0, $event_id = 0, $membership_id = 0 ) {

	// Trigger a SportsZone Notification.
	if ( sz_is_active( 'notifications' ) ) {
		sz_notifications_add_notification( array(
			'user_id'           => $admin_id,
			'item_id'           => $event_id,
			'secondary_item_id' => $requesting_user_id,
			'component_name'    => sportszone()->events->id,
			'component_action'  => 'new_membership_request',
		) );
	}

	// Bail if member opted out of receiving this email.
	if ( 'no' === sz_get_user_meta( $admin_id, 'notification_events_membership_request', true ) ) {
		return;
	}

	$unsubscribe_args = array(
		'user_id'           => $admin_id,
		'notification_type' => 'events-membership-request',
	);

	$event = events_get_event( $event_id );
	$args  = array(
		'tokens' => array(
			'admin.id'             => $admin_id,
			'event'                => $event,
			'event.name'           => $event->name,
			'event.id'             => $event_id,
			'event-requests.url'   => esc_url( sz_get_event_permalink( $event ) . 'admin/membership-requests' ),
			'membership.id'        => $membership_id,
			'profile.url'          => esc_url( sz_core_get_user_domain( $requesting_user_id ) ),
			'requesting-user.id'   => $requesting_user_id,
			'requesting-user.name' => sz_core_get_user_displayname( $requesting_user_id ),
			'unsubscribe'          => esc_url( sz_email_get_unsubscribe_link( $unsubscribe_args ) ),
		),
	);
	sz_send_email( 'events-membership-request', (int) $admin_id, $args );
}

/**
 * Notify member about their event membership request.
 *
 * @since 1.0.0
 *
 * @param int  $requesting_user_id ID of the user requesting event membership.
 * @param int  $event_id           ID of the event.
 * @param bool $accepted           Optional. Whether the membership request was accepted.
 *                                 Default: true.
 */
function events_notification_membership_request_completed( $requesting_user_id = 0, $event_id = 0, $accepted = true ) {

	// Trigger a SportsZone Notification.
	if ( sz_is_active( 'notifications' ) ) {

		// What type of acknowledgement.
		$type = ! empty( $accepted ) ? 'membership_request_accepted' : 'membership_request_rejected';

		sz_notifications_add_notification( array(
			'user_id'           => $requesting_user_id,
			'item_id'           => $event_id,
			'component_name'    => sportszone()->events->id,
			'component_action'  => $type,
		) );
	}

	// Bail if member opted out of receiving this email.
	if ( 'no' === sz_get_user_meta( $requesting_user_id, 'notification_membership_request_completed', true ) ) {
		return;
	}

	$event = events_get_event( $event_id );
	$args  = array(
		'tokens' => array(
			'event'              => $event,
			'event.id'           => $event_id,
			'event.name'         => $event->name,
			'event.url'          => esc_url( sz_get_event_permalink( $event ) ),
			'requesting-user.id' => $requesting_user_id,
		),
	);

	if ( ! empty( $accepted ) ) {

		$unsubscribe_args = array(
			'user_id'           => $requesting_user_id,
			'notification_type' => 'events-membership-request-accepted',
		);

		$args['tokens']['unsubscribe'] = esc_url( sz_email_get_unsubscribe_link( $unsubscribe_args ) );

		sz_send_email( 'events-membership-request-accepted', (int) $requesting_user_id, $args );

	} else {

		$unsubscribe_args = array(
			'user_id'           => $requesting_user_id,
			'notification_type' => 'events-membership-request-rejected',
		);

		$args['tokens']['unsubscribe'] = esc_url( sz_email_get_unsubscribe_link( $unsubscribe_args ) );

		sz_send_email( 'events-membership-request-rejected', (int) $requesting_user_id, $args );
	}
}
add_action( 'events_membership_accepted', 'events_notification_membership_request_completed', 10, 3 );
add_action( 'events_membership_rejected', 'events_notification_membership_request_completed', 10, 3 );

/**
 * Notify event member they have been promoted.
 *
 * @since 1.0.0
 *
 * @param int $user_id  ID of the user.
 * @param int $event_id ID of the event.
 */
function events_notification_promoted_member( $user_id = 0, $event_id = 0 ) {

	// What type of promotion is this?
	if ( events_is_user_admin( $user_id, $event_id ) ) {
		$promoted_to = __( 'an administrator', 'sportszone' );
		$type        = 'member_promoted_to_admin';
	} else {
		$promoted_to = __( 'a moderator', 'sportszone' );
		$type        = 'member_promoted_to_mod';
	}

	// Trigger a SportsZone Notification.
	if ( sz_is_active( 'notifications' ) ) {
		sz_notifications_add_notification( array(
			'user_id'           => $user_id,
			'item_id'           => $event_id,
			'component_name'    => sportszone()->events->id,
			'component_action'  => $type,
		) );
	}

	// Bail if admin opted out of receiving this email.
	if ( 'no' === sz_get_user_meta( $user_id, 'notification_events_admin_promotion', true ) ) {
		return;
	}

	$unsubscribe_args = array(
		'user_id'           => $user_id,
		'notification_type' => 'events-member-promoted',
	);

	$event = events_get_event( $event_id );
	$args  = array(
		'tokens' => array(
			'event'       => $event,
			'event.id'    => $event_id,
			'event.url'   => esc_url( sz_get_event_permalink( $event ) ),
			'event.name'  => $event->name,
			'promoted_to' => $promoted_to,
			'user.id'     => $user_id,
			'unsubscribe' => esc_url( sz_email_get_unsubscribe_link( $unsubscribe_args ) ),
		),
	);
	sz_send_email( 'events-member-promoted', (int) $user_id, $args );
}
add_action( 'events_promoted_member', 'events_notification_promoted_member', 10, 2 );

/**
 * Notify a member they have been invited to a event.
 *
 * @since 1.0.0
 *
 * @param SZ_Events_Event  $event           Event object.
 * @param SZ_Events_Member $member          Member object.
 * @param int              $inviter_user_id ID of the user who sent the invite.
 */
function events_notification_event_invites( &$event, &$member, $inviter_user_id ) {

	// Bail if member has already been invited.
	if ( ! empty( $member->invite_sent ) ) {
		return;
	}

	// @todo $inviter_ud may be used for caching, test without it
	$inviter_ud      = sz_core_get_core_userdata( $inviter_user_id );
	$invited_user_id = $member->user_id;

	// Trigger a SportsZone Notification.
	if ( sz_is_active( 'notifications' ) ) {
		sz_notifications_add_notification( array(
			'user_id'          => $invited_user_id,
			'item_id'          => $event->id,
			'component_name'   => sportszone()->events->id,
			'component_action' => 'event_invite',
		) );
	}

	// Bail if member opted out of receiving this email.
	if ( 'no' === sz_get_user_meta( $invited_user_id, 'notification_events_invite', true ) ) {
		return;
	}

	$invited_link = sz_core_get_user_domain( $invited_user_id ) . sz_get_events_slug();

	$unsubscribe_args = array(
		'user_id'           => $invited_user_id,
		'notification_type' => 'events-invitation',
	);

	$args         = array(
		'tokens' => array(
			'event'        => $event,
			'event.url'    => sz_get_event_permalink( $event ),
			'event.name'   => $event->name,
			'inviter.name' => sz_core_get_userlink( $inviter_user_id, true, false, true ),
			'inviter.url'  => sz_core_get_user_domain( $inviter_user_id ),
			'inviter.id'   => $inviter_user_id,
			'invites.url'  => esc_url( $invited_link . '/invites/' ),
			'unsubscribe'  => esc_url( sz_email_get_unsubscribe_link( $unsubscribe_args ) ),
		),
	);
	sz_send_email( 'events-invitation', (int) $invited_user_id, $args );
}

/** Notifications *************************************************************/

/**
 * Format notifications for the Events component.
 *
 * @since 1.0.0
 *
 * @param string $action            The kind of notification being rendered.
 * @param int    $item_id           The primary item ID.
 * @param int    $secondary_item_id The secondary item ID.
 * @param int    $total_items       The total number of messaging-related notifications
 *                                  waiting for the user.
 * @param string $format            'string' for BuddyBar-compatible notifications; 'array'
 *                                  for WP Toolbar. Default: 'string'.
 * @return string
 */
function events_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

	switch ( $action ) {
		case 'new_membership_request':
			$event_id = $item_id;
			$requesting_user_id = $secondary_item_id;

			$event = events_get_event( $event_id );
			$event_link = sz_get_event_permalink( $event );
			$amount = 'single';

			// Set up the string and the filter
			// because different values are passed to the filters,
			// we'll return values inline.
			if ( (int) $total_items > 1 ) {
				$text = sprintf( __( '%1$d new membership requests for the event "%2$s"', 'sportszone' ), (int) $total_items, $event->name );
				$amount = 'multiple';
				$notification_link = $event_link . 'admin/membership-requests/?n=1';

				if ( 'string' == $format ) {

					/**
					 * Filters events multiple new membership request notification for string format.
					 *
					 * This is a dynamic filter that is dependent on item count and action.
					 * Complete filter - sz_events_multiple_new_membership_requests_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param string $string            HTML anchor tag for request.
					 * @param string $event_link        The permalink for the event.
					 * @param int    $total_items       Total number of membership requests.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . 's_notification', '<a href="' . $notification_link . '">' . $text . '</a>', $event_link, $total_items, $event->name, $text, $notification_link );
				} else {

					/**
					 * Filters events multiple new membership request notification for any non-string format.
					 *
					 * This is a dynamic filter that is dependent on item count and action.
					 * Complete filter - sz_events_multiple_new_membership_requests_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param array  $array             Array holding permalink and content for notification.
					 * @param string $event_link        The permalink for the event.
					 * @param int    $total_items       Total number of membership requests.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . 's_notification', array(
						'link' => $notification_link,
						'text' => $text
					), $event_link, $total_items, $event->name, $text, $notification_link );
				}
			} else {
				$user_fullname = sz_core_get_user_displayname( $requesting_user_id );
				$text = sprintf( __( '%s requests event membership', 'sportszone' ), $user_fullname );
				$notification_link = $event_link . 'admin/membership-requests/?n=1';

				if ( 'string' == $format ) {

					/**
					 * Filters events single new membership request notification for string format.
					 *
					 * This is a dynamic filter that is dependent on item count and action.
					 * Complete filter - sz_events_single_new_membership_request_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param string $string            HTML anchor tag for request.
					 * @param string $event_link        The permalink for the event.
					 * @param string $user_fullname     Full name of requesting user.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', '<a href="' . $notification_link . '">' . $text . '</a>', $event_link, $user_fullname, $event->name, $text, $notification_link );
				} else {

					/**
					 * Filters events single new membership request notification for any non-string format.
					 *
					 * This is a dynamic filter that is dependent on item count and action.
					 * Complete filter - sz_events_single_new_membership_request_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param array  $array             Array holding permalink and content for notification.
					 * @param string $event_link        The permalink for the event.
					 * @param string $user_fullname     Full name of requesting user.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', array(
						'link' => $notification_link,
						'text' => $text
					), $event_link, $user_fullname, $event->name, $text, $notification_link );
				}
			}

			break;

		case 'membership_request_accepted':
			$event_id = $item_id;

			$event = events_get_event( $event_id );
			$event_link = sz_get_event_permalink( $event );
			$amount = 'single';

			if ( (int) $total_items > 1 ) {
				$text = sprintf( __( '%d accepted event membership requests', 'sportszone' ), (int) $total_items, $event->name );
				$amount = 'multiple';
				$notification_link = trailingslashit( sz_loggedin_user_domain() . sz_get_events_slug() ) . '?n=1';

				if ( 'string' == $format ) {

					/**
					 * Filters multiple accepted event membership requests notification for string format.
					 * Complete filter - sz_events_multiple_membership_request_accepted_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param string $string            HTML anchor tag for notification.
					 * @param int    $total_items       Total number of accepted requests.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', '<a href="' . $notification_link . '">' . $text . '</a>', $total_items, $event->name, $text, $notification_link );
				} else {

					/**
					 * Filters multiple accepted event membership requests notification for non-string format.
					 * Complete filter - sz_events_multiple_membership_request_accepted_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param array  $array             Array holding permalink and content for notification
					 * @param int    $total_items       Total number of accepted requests.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', array(
						'link' => $notification_link,
						'text' => $text
					), $total_items, $event->name, $text, $notification_link );
				}
			} else {
				$text = sprintf( __( 'Membership for event "%s" accepted', 'sportszone' ), $event->name );
				$filter = 'sz_events_single_membership_request_accepted_notification';
				$notification_link = $event_link . '?n=1';

				if ( 'string' == $format ) {

					/**
					 * Filters single accepted event membership request notification for string format.
					 * Complete filter - sz_events_single_membership_request_accepted_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param string $string            HTML anchor tag for notification.
					 * @param string $event_link        The permalink for the event.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', '<a href="' . $notification_link . '">' . $text . '</a>', $event_link, $event->name, $text, $notification_link );
				} else {

					/**
					 * Filters single accepted event membership request notification for non-string format.
					 * Complete filter - sz_events_single_membership_request_accepted_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param array  $array             Array holding permalink and content for notification.
					 * @param string $event_link        The permalink for the event.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( $filter, array(
						'link' => $notification_link,
						'text' => $text
					), $event_link, $event->name, $text, $notification_link );
				}
			}

			break;

		case 'membership_request_rejected':
			$event_id = $item_id;

			$event = events_get_event( $event_id );
			$event_link = sz_get_event_permalink( $event );
			$amount = 'single';

			if ( (int) $total_items > 1 ) {
				$text = sprintf( __( '%d rejected event membership requests', 'sportszone' ), (int) $total_items, $event->name );
				$amount = 'multiple';
				$notification_link = trailingslashit( sz_loggedin_user_domain() . sz_get_events_slug() ) . '?n=1';

				if ( 'string' == $format ) {

					/**
					 * Filters multiple rejected event membership requests notification for string format.
					 * Complete filter - sz_events_multiple_membership_request_rejected_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param string $string            HTML anchor tag for notification.
					 * @param int    $total_items       Total number of rejected requests.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', '<a href="' . $notification_link . '">' . $text . '</a>', $total_items, $event->name );
				} else {

					/**
					 * Filters multiple rejected event membership requests notification for non-string format.
					 * Complete filter - sz_events_multiple_membership_request_rejected_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param array  $array             Array holding permalink and content for notification.
					 * @param int    $total_items       Total number of rejected requests.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', array(
						'link' => $notification_link,
						'text' => $text
					), $total_items, $event->name, $text, $notification_link );
				}
			} else {
				$text = sprintf( __( 'Membership for event "%s" rejected', 'sportszone' ), $event->name );
				$notification_link = $event_link . '?n=1';

				if ( 'string' == $format ) {

					/**
					 * Filters single rejected event membership requests notification for string format.
					 * Complete filter - sz_events_single_membership_request_rejected_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param string $string            HTML anchor tag for notification.
					 * @param int    $event_link        The permalink for the event.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', '<a href="' . $notification_link . '">' . $text . '</a>', $event_link, $event->name, $text, $notification_link );
				} else {

					/**
					 * Filters single rejected event membership requests notification for non-string format.
					 * Complete filter - sz_events_single_membership_request_rejected_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param array  $array             Array holding permalink and content for notification.
					 * @param int    $event_link        The permalink for the event.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', array(
						'link' => $notification_link,
						'text' => $text
					), $event_link, $event->name, $text, $notification_link );
				}
			}

			break;

		case 'member_promoted_to_admin':
			$event_id = $item_id;

			$event = events_get_event( $event_id );
			$event_link = sz_get_event_permalink( $event );
			$amount = 'single';

			if ( (int) $total_items > 1 ) {
				$text = sprintf( __( 'You were promoted to an admin in %d events', 'sportszone' ), (int) $total_items );
				$amount = 'multiple';
				$notification_link = trailingslashit( sz_loggedin_user_domain() . sz_get_events_slug() ) . '?n=1';

				if ( 'string' == $format ) {
					/**
					 * Filters multiple promoted to event admin notification for string format.
					 * Complete filter - sz_events_multiple_member_promoted_to_admin_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param string $string            HTML anchor tag for notification.
					 * @param int    $total_items       Total number of rejected requests.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', '<a href="' . $notification_link . '">' . $text . '</a>', $total_items, $text, $notification_link );
				} else {
					/**
					 * Filters multiple promoted to event admin notification for non-string format.
					 * Complete filter - sz_events_multiple_member_promoted_to_admin_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param array  $array             Array holding permalink and content for notification.
					 * @param int    $total_items       Total number of rejected requests.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', array(
						'link' => $notification_link,
						'text' => $text
					), $total_items, $text, $notification_link );
				}
			} else {
				$text = sprintf( __( 'You were promoted to an admin in the event "%s"', 'sportszone' ), $event->name );
				$notification_link = $event_link . '?n=1';

				if ( 'string' == $format ) {
					/**
					 * Filters single promoted to event admin notification for non-string format.
					 * Complete filter - sz_events_single_member_promoted_to_admin_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param string $string            HTML anchor tag for notification.
					 * @param int    $event_link        The permalink for the event.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', '<a href="' . $notification_link . '">' . $text . '</a>', $event_link, $event->name, $text, $notification_link );
				} else {
					/**
					 * Filters single promoted to event admin notification for non-string format.
					 * Complete filter - sz_events_single_member_promoted_to_admin_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param array  $array             Array holding permalink and content for notification.
					 * @param int    $event_link        The permalink for the event.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', array(
						'link' => $notification_link,
						'text' => $text
					), $event_link, $event->name, $text, $notification_link );
				}
			}

			break;

		case 'member_promoted_to_mod':
			$event_id = $item_id;

			$event = events_get_event( $event_id );
			$event_link = sz_get_event_permalink( $event );
			$amount = 'single';

			if ( (int) $total_items > 1 ) {
				$text = sprintf( __( 'You were promoted to a mod in %d events', 'sportszone' ), (int) $total_items );
				$amount = 'multiple';
				$notification_link = trailingslashit( sz_loggedin_user_domain() . sz_get_events_slug() ) . '?n=1';

				if ( 'string' == $format ) {
					/**
					 * Filters multiple promoted to event mod notification for string format.
					 * Complete filter - sz_events_multiple_member_promoted_to_mod_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param string $string            HTML anchor tag for notification.
					 * @param int    $total_items       Total number of rejected requests.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', '<a href="' . $notification_link . '">' . $text . '</a>', $total_items, $text, $notification_link );
				} else {
					/**
					 * Filters multiple promoted to event mod notification for non-string format.
					 * Complete filter - sz_events_multiple_member_promoted_to_mod_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param array  $array             Array holding permalink and content for notification.
					 * @param int    $total_items       Total number of rejected requests.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', array(
						'link' => $notification_link,
						'text' => $text
					), $total_items, $text, $notification_link );
				}
			} else {
				$text = sprintf( __( 'You were promoted to a mod in the event "%s"', 'sportszone' ), $event->name );
				$notification_link = $event_link . '?n=1';

				if ( 'string' == $format ) {
					/**
					 * Filters single promoted to event mod notification for string format.
					 * Complete filter - sz_events_single_member_promoted_to_mod_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param string $string            HTML anchor tag for notification.
					 * @param int    $event_link        The permalink for the event.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', '<a href="' . $notification_link . '">' . $text . '</a>', $event_link, $event->name, $text, $notification_link );
				} else {
					/**
					 * Filters single promoted to event admin notification for non-string format.
					 * Complete filter - sz_events_single_member_promoted_to_mod_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param array  $array             Array holding permalink and content for notification.
					 * @param int    $event_link        The permalink for the event.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', array(
						'link' => $notification_link,
						'text' => $text
					), $event_link, $event->name, $text, $notification_link );
				}
			}

			break;

		case 'event_invite':
			$event_id = $item_id;
			$event = events_get_event( $event_id );
			$event_link = sz_get_event_permalink( $event );
			$amount = 'single';

			$notification_link = sz_loggedin_user_domain() . sz_get_events_slug() . '/invites/?n=1';

			if ( (int) $total_items > 1 ) {
				$text = sprintf( __( 'You have %d new event invitations', 'sportszone' ), (int) $total_items );
				$amount = 'multiple';

				if ( 'string' == $format ) {
					/**
					 * Filters multiple event invitation notification for string format.
					 * Complete filter - sz_events_multiple_event_invite_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param string $string            HTML anchor tag for notification.
					 * @param int    $total_items       Total number of rejected requests.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', '<a href="' . $notification_link . '">' . $text . '</a>', $total_items, $text, $notification_link );
				} else {
					/**
					 * Filters multiple event invitation notification for non-string format.
					 * Complete filter - sz_events_multiple_event_invite_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param array  $array             Array holding permalink and content for notification.
					 * @param int    $total_items       Total number of rejected requests.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', array(
						'link' => $notification_link,
						'text' => $text
					), $total_items, $text, $notification_link );
				}
			} else {
				$text = sprintf( __( 'You have an invitation to the event: %s', 'sportszone' ), $event->name );
				$filter = 'sz_events_single_event_invite_notification';

				if ( 'string' == $format ) {
					/**
					 * Filters single event invitation notification for string format.
					 * Complete filter - sz_events_single_event_invite_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param string $string            HTML anchor tag for notification.
					 * @param int    $event_link        The permalink for the event.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', '<a href="' . $notification_link . '">' . $text . '</a>', $event_link, $event->name, $text, $notification_link );
				} else {
					/**
					 * Filters single event invitation notification for non-string format.
					 * Complete filter - sz_events_single_event_invite_notification.
					 *
					 * @since 1.0.0
					 *
					 * @param array  $array             Array holding permalink and content for notification.
					 * @param int    $event_link        The permalink for the event.
					 * @param string $event->name       Name of the event.
					 * @param string $text              Notification content.
					 * @param string $notification_link The permalink for notification.
					 */
					return apply_filters( 'sz_events_' . $amount . '_' . $action . '_notification', array(
						'link' => $notification_link,
						'text' => $text
					), $event_link, $event->name, $text, $notification_link );
				}
			}

			break;

		default:

			/**
			 * Filters plugin-added event-related custom component_actions.
			 *
			 * @since 2.4.0
			 *
			 * @param string $notification      Null value.
			 * @param int    $item_id           The primary item ID.
			 * @param int    $secondary_item_id The secondary item ID.
			 * @param int    $total_items       The total number of messaging-related notifications
			 *                                  waiting for the user.
			 * @param string $format            'string' for BuddyBar-compatible notifications;
			 *                                  'array' for WP Toolbar.
			 */
			$custom_action_notification = apply_filters( 'sz_events_' . $action . '_notification', null, $item_id, $secondary_item_id, $total_items, $format );

			if ( ! is_null( $custom_action_notification ) ) {
				return $custom_action_notification;
			}

			break;
	}

	/**
	 * Fires right before returning the formatted event notifications.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action            The type of notification being rendered.
	 * @param int    $item_id           The primary item ID.
	 * @param int    $secondary_item_id The secondary item ID.
	 * @param int    $total_items       Total amount of items to format.
	 */
	do_action( 'events_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return false;
}

/**
 * Remove all notifications for any member belonging to a specific event.
 *
 * @since 1.9.0
 *
 * @param int $event_id ID of the event.
 */
function sz_events_delete_event_delete_all_notifications( $event_id ) {
	if ( sz_is_active( 'notifications' ) ) {
		sz_notifications_delete_all_notifications_by_type( $event_id, sportszone()->events->id );
	}
}
add_action( 'events_delete_event', 'sz_events_delete_event_delete_all_notifications', 10 );

/**
 * When a demotion takes place, delete any corresponding promotion notifications.
 *
 * @since 2.0.0
 *
 * @param int $user_id  ID of the user.
 * @param int $event_id ID of the event.
 */
function sz_events_delete_promotion_notifications( $user_id = 0, $event_id = 0 ) {
	if ( sz_is_active( 'notifications' ) && ! empty( $event_id ) && ! empty( $user_id ) ) {
		sz_notifications_delete_notifications_by_item_id( $user_id, $event_id, sportszone()->events->id, 'member_promoted_to_admin' );
		sz_notifications_delete_notifications_by_item_id( $user_id, $event_id, sportszone()->events->id, 'member_promoted_to_mod' );
	}
}
add_action( 'events_demoted_member', 'sz_events_delete_promotion_notifications', 10, 2 );

/**
 * Mark notifications read when a member accepts a event invitation.
 *
 * @since 1.9.0
 *
 * @param int $user_id  ID of the user.
 * @param int $event_id ID of the event.
 */
function sz_events_accept_invite_mark_notifications( $user_id, $event_id ) {
	if ( sz_is_active( 'notifications' ) ) {
		sz_notifications_mark_notifications_by_item_id( $user_id, $event_id, sportszone()->events->id, 'event_invite' );
	}
}
add_action( 'events_accept_invite', 'sz_events_accept_invite_mark_notifications', 10, 2 );
add_action( 'events_reject_invite', 'sz_events_accept_invite_mark_notifications', 10, 2 );
add_action( 'events_delete_invite', 'sz_events_accept_invite_mark_notifications', 10, 2 );

/**
 * Mark notifications read when a member's event membership request is granted.
 *
 * @since 2.8.0
 *
 * @param int $user_id  ID of the user.
 * @param int $event_id ID of the event.
 */
function sz_events_accept_request_mark_notifications( $user_id, $event_id ) {
	if ( sz_is_active( 'notifications' ) ) {
		// First null parameter marks read for all admins.
		sz_notifications_mark_notifications_by_item_id( null, $event_id, sportszone()->events->id, 'new_membership_request', $user_id );
	}
}
add_action( 'events_membership_accepted', 'sz_events_accept_request_mark_notifications', 10, 2 );
add_action( 'events_membership_rejected', 'sz_events_accept_request_mark_notifications', 10, 2 );

/**
 * Mark notifications read when a member views their event memberships.
 *
 * @since 1.9.0
 */
function sz_events_screen_my_events_mark_notifications() {

	// Delete event request notifications for the user.
	if ( isset( $_GET['n'] ) && sz_is_active( 'notifications' ) ) {

		// Get the necessary ID's.
		$event_id = sportszone()->events->id;
		$user_id  = sz_loggedin_user_id();

		// Mark notifications read.
		sz_notifications_mark_notifications_by_type( $user_id, $event_id, 'membership_request_accepted' );
		sz_notifications_mark_notifications_by_type( $user_id, $event_id, 'membership_request_rejected' );
		sz_notifications_mark_notifications_by_type( $user_id, $event_id, 'member_promoted_to_mod'      );
		sz_notifications_mark_notifications_by_type( $user_id, $event_id, 'member_promoted_to_admin'    );
	}
}
add_action( 'events_screen_my_events',  'sz_events_screen_my_events_mark_notifications', 10 );
add_action( 'events_screen_event_home', 'sz_events_screen_my_events_mark_notifications', 10 );

/**
 * Mark event invitation notifications read when a member views their invitations.
 *
 * @since 1.9.0
 */
function sz_events_screen_invites_mark_notifications() {
	if ( sz_is_active( 'notifications' ) ) {
		sz_notifications_mark_notifications_by_type( sz_loggedin_user_id(), sportszone()->events->id, 'event_invite' );
	}
}
add_action( 'events_screen_event_invites', 'sz_events_screen_invites_mark_notifications', 10 );

/**
 * Mark event join requests read when an admin or moderator visits the event administration area.
 *
 * @since 1.9.0
 */
function sz_events_screen_event_admin_requests_mark_notifications() {
	if ( sz_is_active( 'notifications' ) ) {
		sz_notifications_mark_notifications_by_type( sz_loggedin_user_id(), sportszone()->events->id, 'new_membership_request' );
	}
}
add_action( 'events_screen_event_admin_requests', 'sz_events_screen_event_admin_requests_mark_notifications', 10 );

/**
 * Delete new event membership notifications when a user is being deleted.
 *
 * @since 1.9.0
 *
 * @param int $user_id ID of the user.
 */
function sz_events_remove_data_for_user_notifications( $user_id ) {
	if ( sz_is_active( 'notifications' ) ) {
		sz_notifications_delete_notifications_from_user( $user_id, sportszone()->events->id, 'new_membership_request' );
	}
}
add_action( 'events_remove_data_for_user', 'sz_events_remove_data_for_user_notifications', 10 );

/**
 * Render the event settings fields on the Notification Settings page.
 *
 * @since 1.0.0
 */
function events_screen_notification_settings() {

	if ( !$event_invite = sz_get_user_meta( sz_displayed_user_id(), 'notification_events_invite', true ) )
		$event_invite  = 'yes';

	if ( !$event_update = sz_get_user_meta( sz_displayed_user_id(), 'notification_events_event_updated', true ) )
		$event_update  = 'yes';

	if ( !$event_promo = sz_get_user_meta( sz_displayed_user_id(), 'notification_events_admin_promotion', true ) )
		$event_promo   = 'yes';

	if ( !$event_request = sz_get_user_meta( sz_displayed_user_id(), 'notification_events_membership_request', true ) )
		$event_request = 'yes';

	if ( ! $event_request_completed = sz_get_user_meta( sz_displayed_user_id(), 'notification_membership_request_completed', true ) ) {
		$event_request_completed = 'yes';
	}
	?>

	<table class="notification-settings" id="events-notification-settings">
		<thead>
			<tr>
				<th class="icon"></th>
				<th class="title"><?php _ex( 'Events', 'Event settings on notification settings page', 'sportszone' ) ?></th>
				<th class="yes"><?php _e( 'Yes', 'sportszone' ) ?></th>
				<th class="no"><?php _e( 'No', 'sportszone' )?></th>
			</tr>
		</thead>

		<tbody>
			<tr id="events-notification-settings-invitation">
				<td></td>
				<td><?php _ex( 'A member invites you to join a event', 'event settings on notification settings page','sportszone' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_events_invite]" id="notification-events-invite-yes" value="yes" <?php checked( $event_invite, 'yes', true ) ?>/><label for="notification-events-invite-yes" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Yes, send email', 'sportszone' );
				?></label></td>
				<td class="no"><input type="radio" name="notifications[notification_events_invite]" id="notification-events-invite-no" value="no" <?php checked( $event_invite, 'no', true ) ?>/><label for="notification-events-invite-no" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'No, do not send email', 'sportszone' );
				?></label></td>
			</tr>
			<tr id="events-notification-settings-info-updated">
				<td></td>
				<td><?php _ex( 'Event information is updated', 'event settings on notification settings page', 'sportszone' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_events_event_updated]" id="notification-events-event-updated-yes" value="yes" <?php checked( $event_update, 'yes', true ) ?>/><label for="notification-events-event-updated-yes" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Yes, send email', 'sportszone' );
				?></label></td>
				<td class="no"><input type="radio" name="notifications[notification_events_event_updated]" id="notification-events-event-updated-no" value="no" <?php checked( $event_update, 'no', true ) ?>/><label for="notification-events-event-updated-no" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'No, do not send email', 'sportszone' );
				?></label></td>
			</tr>
			<tr id="events-notification-settings-promoted">
				<td></td>
				<td><?php _ex( 'You are promoted to a event administrator or moderator', 'event settings on notification settings page', 'sportszone' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_events_admin_promotion]" id="notification-events-admin-promotion-yes" value="yes" <?php checked( $event_promo, 'yes', true ) ?>/><label for="notification-events-admin-promotion-yes" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Yes, send email', 'sportszone' );
				?></label></td>
				<td class="no"><input type="radio" name="notifications[notification_events_admin_promotion]" id="notification-events-admin-promotion-no" value="no" <?php checked( $event_promo, 'no', true ) ?>/><label for="notification-events-admin-promotion-no" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'No, do not send email', 'sportszone' );
				?></label></td>
			</tr>
			<tr id="events-notification-settings-request">
				<td></td>
				<td><?php _ex( 'A member requests to join a private event for which you are an admin', 'event settings on notification settings page', 'sportszone' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_events_membership_request]" id="notification-events-membership-request-yes" value="yes" <?php checked( $event_request, 'yes', true ) ?>/><label for="notification-events-membership-request-yes" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Yes, send email', 'sportszone' );
				?></label></td>
				<td class="no"><input type="radio" name="notifications[notification_events_membership_request]" id="notification-events-membership-request-no" value="no" <?php checked( $event_request, 'no', true ) ?>/><label for="notification-events-membership-request-no" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'No, do not send email', 'sportszone' );
				?></label></td>
			</tr>
			<tr id="events-notification-settings-request-completed">
				<td></td>
				<td><?php _ex( 'Your request to join a event has been approved or denied', 'event settings on notification settings page', 'sportszone' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_membership_request_completed]" id="notification-events-membership-request-completed-yes" value="yes" <?php checked( $event_request_completed, 'yes', true ) ?>/><label for="notification-events-membership-request-completed-yes" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Yes, send email', 'sportszone' );
				?></label></td>
				<td class="no"><input type="radio" name="notifications[notification_membership_request_completed]" id="notification-events-membership-request-completed-no" value="no" <?php checked( $event_request_completed, 'no', true ) ?>/><label for="notification-events-membership-request-completed-no" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'No, do not send email', 'sportszone' );
				?></label></td>
			</tr>

			<?php

			/**
			 * Fires at the end of the available event settings fields on Notification Settings page.
			 *
			 * @since 1.0.0
			 */
			do_action( 'events_screen_notification_settings' ); ?>

		</tbody>
	</table>

<?php
}
add_action( 'sz_notification_settings', 'events_screen_notification_settings' );
