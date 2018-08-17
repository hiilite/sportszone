<?php
/**
 * SportsZone Events Activity Functions.
 *
 * These functions handle the recording, deleting and formatting of activity
 * for the user and for this specific component.
 *
 * @package SportsZone
 * @subpackage EventsActivity
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register activity actions for the Events component.
 *
 * @since 1.1.0
 *
 * @return false|null False on failure.
 */
function events_register_activity_actions() {
	$sz = sportszone();

	if ( ! sz_is_active( 'activity' ) ) {
		return false;
	}

	sz_activity_set_action(
		$sz->events->id,
		'created_event',
		__( 'Created a event', 'sportszone' ),
		'sz_events_format_activity_action_created_event',
		__( 'New Events', 'sportszone' ),
		array( 'activity', 'member', 'member_events' )
	);

	sz_activity_set_action(
		$sz->events->id,
		'joined_event',
		__( 'Joined a event', 'sportszone' ),
		'sz_events_format_activity_action_joined_event',
		__( 'Event Memberships', 'sportszone' ),
		array( 'activity', 'event', 'member', 'member_events' )
	);

	sz_activity_set_action(
		$sz->events->id,
		'event_details_updated',
		__( 'Event details edited', 'sportszone' ),
		'sz_events_format_activity_action_event_details_updated',
		__( 'Event Updates', 'sportszone' ),
		array( 'activity', 'event', 'member', 'member_events' )
	);

	/**
	 * Fires at end of registration of the default activity actions for the Events component.
	 *
	 * @since 1.1.0
	 */
	do_action( 'events_register_activity_actions' );
}
add_action( 'sz_register_activity_actions', 'events_register_activity_actions' );

/**
 * Format 'created_event' activity actions.
 *
 * @since 2.0.0
 *
 * @param string $action   Static activity action.
 * @param object $activity Activity data object.
 * @return string
 */
function sz_events_format_activity_action_created_event( $action, $activity ) {
	$user_link = sz_core_get_userlink( $activity->user_id );

	$event      = events_get_event( $activity->item_id );
	$event_link = '<a href="' . esc_url( sz_get_event_permalink( $event ) ) . '">' . esc_html( $event->name ) . '</a>';

	$action = sprintf( __( '%1$s created the event %2$s', 'sportszone'), $user_link, $event_link );

	/**
	 * Filters the 'created_event' activity actions.
	 *
	 * @since 1.2.0
	 *
	 * @param string $action   The 'created_event' activity action.
	 * @param object $activity Activity data object.
	 */
	return apply_filters( 'events_activity_created_event_action', $action, $activity );
}

/**
 * Format 'joined_event' activity actions.
 *
 * @since 2.0.0
 *
 * @param string $action   Static activity action.
 * @param object $activity Activity data object.
 * @return string
 */
function sz_events_format_activity_action_joined_event( $action, $activity ) {
	$user_link = sz_core_get_userlink( $activity->user_id );

	$event      = events_get_event( $activity->item_id );
	$event_link = '<a href="' . esc_url( sz_get_event_permalink( $event ) ) . '">' . esc_html( $event->name ) . '</a>';

	$action = sprintf( __( '%1$s joined the event %2$s', 'sportszone' ), $user_link, $event_link );

	// Legacy filters (do not follow parameter patterns of other activity
	// action filters, and requires apply_filters_ref_array()).
	if ( has_filter( 'events_activity_membership_accepted_action' ) ) {
		$action = apply_filters_ref_array( 'events_activity_membership_accepted_action', array( $action, $user_link, &$event ) );
	}

	// Another legacy filter.
	if ( has_filter( 'events_activity_accepted_invite_action' ) ) {
		$action = apply_filters_ref_array( 'events_activity_accepted_invite_action', array( $action, $activity->user_id, &$event ) );
	}

	/**
	 * Filters the 'joined_event' activity actions.
	 *
	 * @since 2.0.0
	 *
	 * @param string $action   The 'joined_event' activity actions.
	 * @param object $activity Activity data object.
	 */
	return apply_filters( 'sz_events_format_activity_action_joined_event', $action, $activity );
}

/**
 * Format 'event_details_updated' activity actions.
 *
 * @since 2.2.0
 *
 * @param  string $action   Static activity action.
 * @param  object $activity Activity data object.
 * @return string
 */
function sz_events_format_activity_action_event_details_updated( $action, $activity ) {
	$user_link = sz_core_get_userlink( $activity->user_id );

	$event      = events_get_event( $activity->item_id );
	$event_link = '<a href="' . esc_url( sz_get_event_permalink( $event ) ) . '">' . esc_html( $event->name ) . '</a>';

	/*
	 * Changed event details are stored in eventmeta, keyed by the activity
	 * timestamp. See {@link sz_events_event_details_updated_add_activity()}.
	 */
	$changed = events_get_eventmeta( $activity->item_id, 'updated_details_' . $activity->date_recorded );

	// No changed details were found, so use a generic message.
	if ( empty( $changed ) ) {
		$action = sprintf( __( '%1$s updated details for the event %2$s', 'sportszone' ), $user_link, $event_link );

	// Name and description changed - to keep things short, don't describe changes in detail.
	} elseif ( isset( $changed['name'] ) && isset( $changed['description'] ) ) {
		$action = sprintf( __( '%1$s changed the name and description of the event %2$s', 'sportszone' ), $user_link, $event_link );

	// Name only.
	} elseif ( ! empty( $changed['name']['old'] ) && ! empty( $changed['name']['new'] ) ) {
		$action = sprintf( __( '%1$s changed the name of the event %2$s from "%3$s" to "%4$s"', 'sportszone' ), $user_link, $event_link, esc_html( $changed['name']['old'] ), esc_html( $changed['name']['new'] ) );

	// Description only.
	} elseif ( ! empty( $changed['description']['old'] ) && ! empty( $changed['description']['new'] ) ) {
		$action = sprintf( __( '%1$s changed the description of the event %2$s from "%3$s" to "%4$s"', 'sportszone' ), $user_link, $event_link, esc_html( $changed['description']['old'] ), esc_html( $changed['description']['new'] ) );

	} elseif ( ! empty( $changed['slug']['old'] ) && ! empty( $changed['slug']['new'] ) ) {
		$action = sprintf( __( '%1$s changed the permalink of the event %2$s.', 'sportszone' ), $user_link, $event_link );

	}

	/**
	 * Filters the 'event_details_updated' activity actions.
	 *
	 * @since 2.0.0
	 *
	 * @param string $action   The 'event_details_updated' activity actions.
	 * @param object $activity Activity data object.
	 */
	return apply_filters( 'sz_events_format_activity_action_joined_event', $action, $activity );
}

/**
 * Fetch data related to events at the beginning of an activity loop.
 *
 * This reduces database overhead during the activity loop.
 *
 * @since 2.0.0
 *
 * @param array $activities Array of activity items.
 * @return array
 */
function sz_events_prefetch_activity_object_data( $activities ) {
	$event_ids = array();

	if ( empty( $activities ) ) {
		return $activities;
	}

	foreach ( $activities as $activity ) {
		if ( sportszone()->events->id !== $activity->component ) {
			continue;
		}

		$event_ids[] = $activity->item_id;
	}

	if ( ! empty( $event_ids ) ) {

		// TEMPORARY - Once the 'populate_extras' issue is solved
		// in the events component, we can do this with events_get_events()
		// rather than manually.
		$uncached_ids = array();
		foreach ( $event_ids as $event_id ) {
			if ( false === wp_cache_get( $event_id, 'sz_events' ) ) {
				$uncached_ids[] = $event_id;
			}
		}

		if ( ! empty( $uncached_ids ) ) {
			global $wpdb;
			$sz = sportszone();
			$uncached_ids_sql = implode( ',', wp_parse_id_list( $uncached_ids ) );
			$events = $wpdb->get_results( "SELECT * FROM {$sz->events->table_name} WHERE id IN ({$uncached_ids_sql})" );
			foreach ( $events as $event ) {
				wp_cache_set( $event->id, $event, 'sz_events' );
			}
		}
	}

	return $activities;
}
add_filter( 'sz_activity_prefetch_object_data', 'sz_events_prefetch_activity_object_data' );

/**
 * Set up activity arguments for use with the 'events' scope.
 *
 * @since 2.2.0
 *
 * @param array $retval Empty array by default.
 * @param array $filter Current activity arguments.
 * @return array
 */
function sz_events_filter_activity_scope( $retval = array(), $filter = array() ) {

	// Determine the user_id.
	if ( ! empty( $filter['user_id'] ) ) {
		$user_id = $filter['user_id'];
	} else {
		$user_id = sz_displayed_user_id()
			? sz_displayed_user_id()
			: sz_loggedin_user_id();
	}

	// Determine events of user.
	$events = events_get_user_events( $user_id );
	if ( empty( $events['events'] ) ) {
		$events = array( 'events' => 0 );
	}

	// Should we show all items regardless of sitewide visibility?
	$show_hidden = array();
	if ( ! empty( $user_id ) && ( $user_id !== sz_loggedin_user_id() ) ) {
		$show_hidden = array(
			'column' => 'hide_sitewide',
			'value'  => 0
		);
	}

	$retval = array(
		'relation' => 'AND',
		array(
			'relation' => 'AND',
			array(
				'column' => 'component',
				'value'  => sportszone()->events->id
			),
			array(
				'column'  => 'item_id',
				'compare' => 'IN',
				'value'   => (array) $events['events']
			),
		),
		$show_hidden,

		// Overrides.
		'override' => array(
			'filter'      => array( 'user_id' => 0 ),
			'show_hidden' => true
		),
	);

	return $retval;
}
add_filter( 'sz_activity_set_events_scope_args', 'sz_events_filter_activity_scope', 10, 2 );

/**
 * Record an activity item related to the Events component.
 *
 * A wrapper for {@link sz_activity_add()} that provides some Events-specific
 * defaults.
 *
 * @since 1.0.0
 *
 * @see sz_activity_add() for more detailed description of parameters and
 *      return values.
 *
 * @param array|string $args {
 *     An array of arguments for the new activity item. Accepts all parameters
 *     of {@link sz_activity_add()}. However, this wrapper provides some
 *     additional defaults, as described below:
 *     @type string $component     Default: the id of your Events component
 *                                 (usually 'events').
 *     @type bool   $hide_sitewide Default: True if the current event is not
 *                                 public, otherwise false.
 * }
 * @return WP_Error|bool|int See {@link sz_activity_add()}.
 */
function events_record_activity( $args = '' ) {

	if ( ! sz_is_active( 'activity' ) ) {
		return false;
	}

	// Set the default for hide_sitewide by checking the status of the event.
	$hide_sitewide = false;
	if ( !empty( $args['item_id'] ) ) {
		if ( sz_get_current_event_id() == $args['item_id'] ) {
			$event = events_get_current_event();
		} else {
			$event = events_get_event( $args['item_id'] );
		}

		if ( isset( $event->status ) && 'public' != $event->status ) {
			$hide_sitewide = true;
		}
	}

	$r = sz_parse_args( $args, array(
		'id'                => false,
		'user_id'           => sz_loggedin_user_id(),
		'action'            => '',
		'content'           => '',
		'primary_link'      => '',
		'component'         => sportszone()->events->id,
		'type'              => false,
		'item_id'           => false,
		'secondary_item_id' => false,
		'recorded_time'     => sz_core_current_time(),
		'hide_sitewide'     => $hide_sitewide,
		'error_type'        => 'bool'
	), 'events_record_activity' );

	return sz_activity_add( $r );
}

/**
 * Function used to determine if a user can comment on a event activity item.
 *
 * Used as a filter callback to 'sz_activity_can_comment'.
 *
 * @since 3.0.0
 *
 * @param  bool                      $retval   True if item can receive comments.
 * @param  null|SZ_Activity_Activity $activity Null by default. Pass an activity object to check against that instead.
 * @return bool
 */
function sz_events_filter_activity_can_comment( $retval, $activity = null ) {
	// Bail if item cannot receive comments or if no current user.
	if ( empty( $retval ) || ! is_user_logged_in() ) {
		return $retval;
	}

	// Use passed activity object, if available.
	if ( is_a( $activity, 'SZ_Activity_Activity' ) ) {
		$component = $activity->component;
		$event_id  = $activity->item_id;

	// Use activity info from current activity item in the loop.
	} else {
		$component = sz_get_activity_object_name();
		$event_id  = sz_get_activity_item_id();
	}

	// If not a event activity item, bail.
	if ( 'events' !== $component ) {
		return $retval;
	}

	// If current user is not a event member or is banned, user cannot comment.
	if ( ! sz_current_user_can( 'sz_moderate' ) &&
		( ! events_is_user_member( sz_loggedin_user_id(), $event_id ) || events_is_user_banned( sz_loggedin_user_id(), $event_id ) )
	) {
		$retval = false;
	}

	return $retval;
}
add_filter( 'sz_activity_can_comment', 'sz_events_filter_activity_can_comment', 99, 1 );

/**
 * Function used to determine if a user can reply on a event activity comment.
 *
 * Used as a filter callback to 'sz_activity_can_comment_reply'.
 *
 * @since 3.0.0
 *
 * @param  bool        $retval  True if activity comment can be replied to.
 * @param  object|bool $comment Current activity comment object. If empty, parameter is boolean false.
 * @return bool
 */
function sz_events_filter_activity_can_comment_reply( $retval, $comment ) {
	// Bail if no current user, if comment is empty or if retval is already empty.
	if ( ! is_user_logged_in() || empty( $comment ) || empty( $retval ) ) {
		return $retval;
	}

	// Grab parent activity item.
	$parent = new SZ_Activity_Activity( $comment->item_id );

	// Check to see if user can reply to parent event activity item.
	return sz_events_filter_activity_can_comment( $retval, $parent );
}
add_filter( 'sz_activity_can_comment_reply', 'sz_events_filter_activity_can_comment_reply', 99, 2 );

/**
 * Add an activity stream item when a member joins a event.
 *
 * @since 1.9.0
 *
 * @param int $user_id  ID of the user joining the event.
 * @param int $event_id ID of the event.
 * @return false|null False on failure.
 */
function sz_events_membership_accepted_add_activity( $user_id, $event_id ) {

	// Bail if Activity is not active.
	if ( ! sz_is_active( 'activity' ) ) {
		return false;
	}

	// Get the event so we can get it's name.
	$event = events_get_event( $event_id );

	/**
	 * Filters the 'membership_accepted' activity actions.
	 *
	 * @since 1.2.0
	 *
	 * @param string $value    The 'membership_accepted' activity action.
	 * @param int    $user_id  ID of the user joining the event.
	 * @param int    $event_id ID of the event. Passed by reference.
	 */
	$action = apply_filters_ref_array( 'events_activity_membership_accepted_action', array( sprintf( __( '%1$s joined the event %2$s', 'sportszone' ), sz_core_get_userlink( $user_id ), '<a href="' . sz_get_event_permalink( $event ) . '">' . esc_attr( $event->name ) . '</a>' ), $user_id, &$event ) );

	// Record in activity streams.
	events_record_activity( array(
		'action'  => $action,
		'type'    => 'joined_event',
		'item_id' => $event_id,
		'user_id' => $user_id
	) );
}
add_action( 'events_membership_accepted', 'sz_events_membership_accepted_add_activity', 10, 2 );

/**
 * Add an activity item when a event's details are updated.
 *
 * @since 2.2.0
 *
 * @param  int             $event_id       ID of the event.
 * @param  SZ_Events_Event $old_event      Event object before the details had been changed.
 * @param  bool            $notify_members True if the admin has opted to notify event members, otherwise false.
 * @return null|WP_Error|bool|int The ID of the activity on success. False on error.
 */
function sz_events_event_details_updated_add_activity( $event_id, $old_event, $notify_members ) {

	// Bail if Activity is not active.
	if ( ! sz_is_active( 'activity' ) ) {
		return false;
	}

	if ( ! isset( $old_event->name ) || ! isset( $old_event->slug ) || ! isset( $old_event->description ) ) {
		return false;
	}

	// If the admin has opted not to notify members, don't post an activity item either.
	if ( empty( $notify_members ) ) {
		return;
	}

	$event = events_get_event( array(
		'event_id' => $event_id,
	) );

	/*
	 * Store the changed data, which will be used to generate the activity
	 * action. Since we haven't yet created the activity item, we store the
	 * old event data in eventmeta, keyed by the timestamp that we'll put
	 * on the activity item.
	 */
	$changed = array();

	if ( $event->name !== $old_event->name ) {
		$changed['name'] = array(
			'old' => $old_event->name,
			'new' => $event->name,
		);
	}

	if ( $event->slug !== $old_event->slug ) {
		$changed['slug'] = array(
			'old' => $old_event->slug,
			'new' => $event->slug,
		);
	}

	if ( $event->description !== $old_event->description ) {
		$changed['description'] = array(
			'old' => $old_event->description,
			'new' => $event->description,
		);
	}

	// If there are no changes, don't post an activity item.
	if ( empty( $changed ) ) {
		return;
	}

	$time = sz_core_current_time();
	events_update_eventmeta( $event_id, 'updated_details_' . $time, $changed );

	// Record in activity streams.
	return events_record_activity( array(
		'type'          => 'event_details_updated',
		'item_id'       => $event_id,
		'user_id'       => sz_loggedin_user_id(),
		'recorded_time' => $time,

	) );

}
add_action( 'events_details_updated', 'sz_events_event_details_updated_add_activity', 10, 3 );

/**
 * Delete all activity items related to a specific event.
 *
 * @since 1.9.0
 *
 * @param int $event_id ID of the event.
 */
function sz_events_delete_event_delete_all_activity( $event_id ) {
	if ( sz_is_active( 'activity' ) ) {
		sz_activity_delete_by_item_id( array(
			'item_id'   => $event_id,
			'component' => sportszone()->events->id
		) );
	}
}
add_action( 'events_delete_event', 'sz_events_delete_event_delete_all_activity', 10 );

/**
 * Delete event member activity if they leave or are removed within 5 minutes of membership modification.
 *
 * If the user joined this event less than five minutes ago, remove the
 * joined_event activity so users cannot flood the activity stream by
 * joining/leaving the event in quick succession.
 *
 * @since 1.9.0
 *
 * @param int $event_id ID of the event.
 * @param int $user_id  ID of the user leaving the event.
 */
function sz_events_leave_event_delete_recent_activity( $event_id, $user_id ) {

	// Bail if Activity component is not active.
	if ( ! sz_is_active( 'activity' ) ) {
		return;
	}

	// Get the member's event membership information.
	$membership = new SZ_Events_Member( $user_id, $event_id );

	// Check the time period, and maybe delete their recent event activity.
	if ( time() <= strtotime( '+5 minutes', (int) strtotime( $membership->date_modified ) ) ) {
		sz_activity_delete( array(
			'component' => sportszone()->events->id,
			'type'      => 'joined_event',
			'user_id'   => $user_id,
			'item_id'   => $event_id
		) );
	}
}
add_action( 'events_leave_event',   'sz_events_leave_event_delete_recent_activity', 10, 2 );
add_action( 'events_remove_member', 'sz_events_leave_event_delete_recent_activity', 10, 2 );
add_action( 'events_ban_member',    'sz_events_leave_event_delete_recent_activity', 10, 2 );
