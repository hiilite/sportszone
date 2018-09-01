<?php
/**
 * SportsZone Events Functions.
 *
 * Functions are where all the magic happens in SportsZone. They will
 * handle the actual saving or manipulation of information. Usually they will
 * hand off to a database class for data access, then return
 * true or false on success or failure.
 *
 * @package SportsZone
 * @subpackage EventsFunctions
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Check whether there is a Events directory page in the $sz global.
 *
 * @since 1.5.0
 *
 * @return bool True if set, False if empty.
 */
function sz_events_has_directory() {
	$sz = sportszone();

	return (bool) !empty( $sz->pages->events->id );
}

/**
 * Fetch a single event object.
 *
 * When calling up a event object, you should always use this function instead
 * of instantiating SZ_Events_Event directly, so that you will inherit cache
 * support and pass through the events_get_event filter.
 *
 * @since 1.2.0
 * @since 2.7.0 The function signature was changed to accept a event ID only,
 *              instead of an array containing the event ID.
 *
 * @param int $event_id ID of the event.
 * @return SZ_Events_Event $event The event object.
 */
function events_get_event( $event_id ) {
	/*
	 * Backward compatibilty.
	 * Old-style arguments take the form of an array or a query string.
	 */
	if ( ! is_numeric( $event_id ) ) {
		$r = sz_parse_args( $event_id, array(
			'event_id'        => false,
			'load_users'      => false,
			'populate_extras' => false,
		), 'events_get_event' );

		$event_id = $r['event_id'];
	}

	$event = new SZ_Events_Event( $event_id );

	/**
	 * Filters a single event object.
	 *
	 * @since 1.2.0
	 *
	 * @param SZ_Events_Event $event Single event object.
	 */
	return apply_filters( 'events_get_event', $event );
}

/** Event Creation, Editing & Deletion ****************************************/

/**
 * Create a event.
 *
 * @since 1.0.0
 *
 * @param array|string $args {
 *     An array of arguments.
 *     @type int|bool $event_id     Pass a event ID to update an existing item, or
 *                                  0 / false to create a new event. Default: 0.
 *     @type int      $creator_id   The user ID that creates the event.
 *     @type string   $name         The event name.
 *     @type string   $description  Optional. The event's description.
 *     @type string   $slug         The event slug.
 *     @type string   $status       The event's status. Accepts 'public', 'private' or
 *                                  'hidden'. Defaults to 'public'.
 *     @type int      $parent_id    The ID of the parent event. Default: 0.
 *     @type int      $enable_forum Optional. Whether the event has a forum enabled.
 *                                  If a bbPress forum is enabled for the event,
 *                                  set this to 1. Default: 0.
 *     @type string   $date_created The GMT time, in Y-m-d h:i:s format, when the event
 *                                  was created. Defaults to the current time.
 * }
 * @return int|bool The ID of the event on success. False on error.
 */
function events_create_event( $args = '' ) {

	$args = sz_parse_args( $args, array(
		'event_id'     => 0,
		'creator_id'   => 0,
		'name'         => '',
		'description'  => '',
		'slug'         => '',
		'status'       => null,
		'parent_id'    => null,
		'enable_forum' => null,
		'date_created' => null
	), 'events_create_event' );

	extract( $args, EXTR_SKIP );

	// Pass an existing event ID.
	if ( ! empty( $event_id ) ) {
		$event = events_get_event( $event_id );
		$name  = ! empty( $name ) ? $name : $event->name;
		$slug  = ! empty( $slug ) ? $slug : $event->slug;
		$creator_id  = ! empty( $creator_id ) ? $creator_id : $event->creator_id;
		$description = ! empty( $description ) ? $description : $event->description;
		$status = ! is_null( $status ) ? $status : $event->status;
		$parent_id = ! is_null( $parent_id ) ? $parent_id : $event->parent_id;
		$enable_forum = ! is_null( $enable_forum ) ? $enable_forum : $event->enable_forum;
		$date_created = ! is_null( $date_created ) ? $date_created : $event->date_created;

		// Events need at least a name.
		if ( empty( $name ) ) {
			return false;
		}

	// Create a new event.
	} else {
		// Instantiate new event object.
		$event = new SZ_Events_Event;

		// Check for null values, reset to sensible defaults.
		$status = ! is_null( $status ) ? $status : 'public';
		$parent_id = ! is_null( $parent_id ) ? $parent_id : 0;
		$enable_forum = ! is_null( $enable_forum ) ? $enable_forum : 0;
		$date_created = ! is_null( $date_created ) ? $date_created : sz_core_current_time();
	}

	// Set creator ID.
	if ( $creator_id ) {
		$event->creator_id = (int) $creator_id;
	} elseif ( is_user_logged_in() ) {
		$event->creator_id = sz_loggedin_user_id();
	}

	if ( ! $event->creator_id ) {
		return false;
	}

	// Validate status.
	if ( ! events_is_valid_status( $status ) ) {
		return false;
	}

	// Set event name.
	$event->name         = $name;
	$event->description  = $description;
	$event->slug         = $slug;
	$event->status       = $status;
	$event->parent_id    = $parent_id;
	$event->enable_forum = (int) $enable_forum;
	$event->date_created = $date_created;

	// Save event.
	if ( ! $event->save() ) {
		return false;
	}

	// If this is a new event, set up the creator as the first member and admin.
	if ( empty( $event_id ) ) {
		$member                = new SZ_Events_Member;
		$member->event_id      = $event->id;
		$member->user_id       = $event->creator_id;
		$member->is_admin      = 1;
		$member->user_title    = __( 'Event Admin', 'sportszone' );
		$member->is_confirmed  = 1;
		$member->date_modified = sz_core_current_time();
		$member->save();

		/**
		 * Fires after the creation of a new event and a event creator needs to be made.
		 *
		 * @since 1.5.0
		 *
		 * @param int              $id     ID of the newly created event.
		 * @param SZ_Events_Member $member Instance of the member who is assigned
		 *                                 as event creator.
		 * @param SZ_Events_Event  $event  Instance of the event being created.
		 */
		do_action( 'events_create_event', $event->id, $member, $event );

	} else {

		/**
		 * Fires after the update of a event.
		 *
		 * @since 1.5.0
		 *
		 * @param int             $id    ID of the updated event.
		 * @param SZ_Events_Event $event Instance of the event being updated.
		 */
		do_action( 'events_update_event', $event->id, $event );
	}

	/**
	 * Fires after the creation or update of a event.
	 *
	 * @since 1.0.0
	 *
	 * @param int             $id    ID of the newly created event.
	 * @param SZ_Events_Event $event Instance of the event being updated.
	 */
	do_action( 'events_created_event', $event->id, $event );

	return $event->id;
}

/**
 * Edit the base details for a event.
 *
 * These are the settings that appear on the first page of the event's Admin
 * section (Name, Description, and "Notify members...").
 *
 * @since 1.0.0
 *
 * @param array $args {
 *     An array of optional arguments.
 *     @type int    $event_id       ID of the event.
 *     @type string $name           Name of the event.
 *     @type string $slug           Slug of the event.
 *     @type string $description    Description of the event.
 *     @type bool   $notify_members Whether to send an email notification to event
 *                                  members about changes in these details.
 * }
 * @return bool True on success, false on failure.
 */
function events_edit_base_event_details( $args = array() ) {

	// Backward compatibility with old method of passing arguments.
	if ( ! is_array( $args ) || func_num_args() > 1 ) {
		_deprecated_argument( __METHOD__, '2.9.0', sprintf( __( 'Arguments passed to %1$s should be in an associative array. See the inline documentation at %2$s for more details.', 'sportszone' ), __METHOD__, __FILE__ ) );

		$old_args_keys = array(
			0 => 'event_id',
			1 => 'name',
			2 => 'description',
			3 => 'notify_members',
		);

		$args = sz_core_parse_args_array( $old_args_keys, func_get_args() );
	}

	$r = sz_parse_args( $args, array(
		'event_id'       => sz_get_current_event_id(),
		'name'           => null,
		'slug'           => null,
		'description'    => null,
		'notify_members' => false,
	), 'events_edit_base_event_details' );

	if ( ! $r['event_id'] ) {
		return false;
	}

	$event     = events_get_event( $r['event_id'] );
	$old_event = clone $event;

	// Event name, slug and description can never be empty. Update only if provided.
	if ( $r['name'] ) {
		$event->name = $r['name'];
	}
	if ( $r['slug'] && $r['slug'] != $event->slug ) {
		$event->slug = events_check_slug( $r['slug'] );
	}
	if ( $r['description'] ) {
		$event->description = $r['description'];
	}

	if ( ! $event->save() ) {
		return false;
	}

	// Maybe update the "previous_slug" eventmeta.
	if ( $event->slug != $old_event->slug ) {
		/*
		 * If the old slug exists in this event's past, delete that entry.
		 * Recent previous_slugs are preferred when selecting the current event
		 * from an old event slug, so we want the previous slug to be
		 * saved "now" in the eventmeta table and don't need the old record.
		 */
		events_delete_eventmeta( $event->id, 'previous_slug', $old_event->slug );
		events_add_eventmeta( $event->id, 'previous_slug', $old_event->slug );
	}

	if ( $r['notify_members'] ) {
		events_notification_event_updated( $event->id, $old_event );
	}

	/**
	 * Fired after a event's details are updated.
	 *
	 * @since 2.2.0
	 *
	 * @param int             $value          ID of the event.
	 * @param SZ_Events_Event $old_event      Event object, before being modified.
	 * @param bool            $notify_members Whether to send an email notification to members about the change.
	 */
	do_action( 'events_details_updated', $event->id, $old_event, $r['notify_members'] );

	return true;
}

/**
 * Edit the base details for a event.
 *
 * These are the settings that appear on the Settings page of the event's Admin
 * section (privacy settings, "enable forum", invitation status).
 *
 * @since 1.0.0
 *
 * @param int         $event_id      ID of the event.
 * @param bool        $enable_forum  Whether to enable a forum for the event.
 * @param string      $status        Event status. 'public', 'private', 'hidden'.
 * @param string|bool $invite_status Optional. Who is allowed to send invitations
 *                                   to the event. 'members', 'mods', or 'admins'.
 * @param int|bool    $parent_id     Parent event ID.
 * @return bool True on success, false on failure.
 */
function events_edit_event_settings( $event_id, $enable_forum, $status, $invite_status = false, $parent_id = false ) {

	$event = events_get_event( $event_id );
	$event->enable_forum = $enable_forum;

	/**
	 * Before we potentially switch the event status, if it has been changed to public
	 * from private and there are outstanding membership requests, auto-accept those requests.
	 */
	if ( 'private' == $event->status && 'public' == $status )
		events_accept_all_pending_membership_requests( $event->id );

	// Now update the status.
	$event->status = $status;

	// Update the parent ID if necessary.
	if ( false !== $parent_id ) {
		$event->parent_id = $parent_id;
	}

	if ( !$event->save() )
		return false;

	// Set the invite status.
	if ( $invite_status )
		events_update_eventmeta( $event->id, 'invite_status', $invite_status );

	events_update_eventmeta( $event->id, 'last_activity', sz_core_current_time() );

	/**
	 * Fires after the update of a events settings.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the event that was updated.
	 */
	do_action( 'events_settings_updated', $event->id );

	return true;
}

/**
 * Delete a event and all of its associated metadata.
 *
 * @since 1.0.0
 *
 * @param int $event_id ID of the event to delete.
 * @return bool True on success, false on failure.
 */
function events_delete_event( $event_id ) {

	/**
	 * Fires before the deletion of a event.
	 *
	 * @since 1.5.0
	 *
	 * @param int $event_id ID of the event to be deleted.
	 */
	do_action( 'events_before_delete_event', $event_id );

	// Get the event object.
	$event = events_get_event( $event_id );

	// Bail if event cannot be deleted.
	if ( ! $event->delete() ) {
		return false;
	}

	// Remove all outstanding invites for this event.
	events_delete_all_event_invites( $event_id );

	/**
	 * Fires after the deletion of a event.
	 *
	 * @since 1.0.0
	 *
	 * @param int $event_id ID of the event that was deleted.
	 */
	do_action( 'events_delete_event', $event_id );

	return true;
}

/**
 * Check a event status (eg 'private') against the whitelist of registered statuses.
 *
 * @since 1.1.0
 *
 * @param string $status Status to check.
 * @return bool True if status is allowed, otherwise false.
 */
function events_is_valid_status( $status ) {
	$sz = sportszone();

	return in_array( $status, (array) $sz->events->valid_status );
}

/**
 * Provide a unique, sanitized version of a event slug.
 *
 * @since 1.0.0
 *
 * @param string $slug Event slug to check.
 * @return string $slug A unique and sanitized slug.
 */
function events_check_slug( $slug ) {
	$sz = sportszone();

	// First, make the proposed slug work in a URL.
	$slug = sanitize_title( $slug );

	if ( 'wp' == substr( $slug, 0, 2 ) )
		$slug = substr( $slug, 2, strlen( $slug ) - 2 );

	if ( in_array( $slug, (array) $sz->events->forbidden_names ) )
		$slug = $slug . '-' . rand();

	if ( SZ_Events_Event::check_slug( $slug ) ) {
		do {
			$slug = $slug . '-' . rand();
		}
		while ( SZ_Events_Event::check_slug( $slug ) );
	}

	return $slug;
}

/**
 * Get a event slug by its ID.
 *
 * @since 1.0.0
 *
 * @param int $event_id The numeric ID of the event.
 * @return string The event's slug.
 */
function events_get_slug( $event_id ) {
	$event = events_get_event( $event_id );
	return !empty( $event->slug ) ? $event->slug : '';
}

/**
 * Get a event ID by its slug.
 *
 * @since 1.6.0
 *
 * @param string $event_slug The event's slug.
 * @return int|null The event ID on success; null on failure.
 */
function events_get_id( $event_slug ) {
	return SZ_Events_Event::event_exists( $event_slug );
}

/**
 * Get a event ID by checking against old (not currently active) slugs.
 *
 * @since 2.9.0
 *
 * @param string $event_slug The event's slug.
 * @return int|null The event ID on success; null on failure.
 */
function events_get_id_by_previous_slug( $event_slug ) {
	return SZ_Events_Event::get_id_by_previous_slug( $event_slug );
}

/** User Actions **************************************************************/

/**
 * Remove a user from a event.
 *
 * @since 1.0.0
 *
 * @param int $event_id ID of the event.
 * @param int $user_id  Optional. ID of the user. Defaults to the currently
 *                      logged-in user.
 * @return bool True on success, false on failure.
 */
function events_leave_event( $event_id, $user_id = 0 ) {

	if ( empty( $user_id ) )
		$user_id = sz_loggedin_user_id();

	// Don't let single admins leave the event.
	if ( count( events_get_event_admins( $event_id ) ) < 2 ) {
		if ( events_is_user_admin( $user_id, $event_id ) ) {
			sz_core_add_message( __( 'As the only admin, you cannot leave the event.', 'sportszone' ), 'error' );
			return false;
		}
	}

	if ( ! SZ_Events_Member::delete( $user_id, $event_id ) ) {
		return false;
	}

	sz_core_add_message( __( 'You successfully left the event.', 'sportszone' ) );

	/**
	 * Fires after a user leaves a event.
	 *
	 * @since 1.0.0
	 *
	 * @param int $event_id ID of the event.
	 * @param int $user_id  ID of the user leaving the event.
	 */
	do_action( 'events_leave_event', $event_id, $user_id );

	return true;
}

/**
 * Add a user to a event.
 *
 * @since 1.0.0
 *
 * @param int $event_id ID of the event.
 * @param int $user_id  Optional. ID of the user. Defaults to the currently
 *                      logged-in user.
 * @return bool True on success, false on failure.
 */
function events_join_event( $event_id, $user_id = 0 ) {

	if ( empty( $user_id ) )
		$user_id = sz_loggedin_user_id();

	// Check if the user has an outstanding invite. If so, delete it.
	if ( events_check_user_has_invite( $user_id, $event_id ) )
		events_delete_invite( $user_id, $event_id );

	// Check if the user has an outstanding request. If so, delete it.
	if ( events_check_for_membership_request( $user_id, $event_id ) )
		events_delete_membership_request( null, $user_id, $event_id );

	// User is already a member, just return true.
	if ( events_is_user_member( $user_id, $event_id ) )
		return true;

	$new_member                = new SZ_Events_Member;
	$new_member->event_id      = $event_id;
	$new_member->user_id       = $user_id;
	$new_member->inviter_id    = 0;
	$new_member->is_admin      = 0;
	$new_member->user_title    = '';
	$new_member->date_modified = sz_core_current_time();
	$new_member->is_confirmed  = 1;

	if ( !$new_member->save() )
		return false;

	$sz = sportszone();

	if ( !isset( $sz->events->current_event ) || !$sz->events->current_event || $event_id != $sz->events->current_event->id )
		$event = events_get_event( $event_id );
	else
		$event = $sz->events->current_event;

	// Record this in activity streams.
	if ( sz_is_active( 'activity' ) ) {
		events_record_activity( array(
			'type'    => 'joined_event',
			'item_id' => $event_id,
			'user_id' => $user_id,
		) );
	}

	/**
	 * Fires after a user joins a event.
	 *
	 * @since 1.0.0
	 *
	 * @param int $event_id ID of the event.
	 * @param int $user_id  ID of the user joining the event.
	 */
	do_action( 'events_join_event', $event_id, $user_id );

	return true;
}

/**
 * Update the last_activity meta value for a given event.
 *
 * @since 1.0.0
 *
 * @param int $event_id Optional. The ID of the event whose last_activity is
 *                      being updated. Default: the current event's ID.
 * @return false|null False on failure.
 */
function events_update_last_activity( $event_id = 0 ) {

	if ( empty( $event_id ) ) {
		$event_id = sportszone()->events->current_event->id;
	}

	if ( empty( $event_id ) ) {
		return false;
	}

	events_update_eventmeta( $event_id, 'last_activity', sz_core_current_time() );
}
add_action( 'events_join_event',           'events_update_last_activity' );
add_action( 'events_leave_event',          'events_update_last_activity' );
add_action( 'events_created_event',        'events_update_last_activity' );

/** General Event Functions ***************************************************/

/**
 * Get a list of event administrators.
 *
 * @since 1.0.0
 *
 * @param int $event_id ID of the event.
 * @return array Info about event admins (user_id + date_modified).
 */
function events_get_event_admins( $event_id ) {
	return SZ_Events_Member::get_event_administrator_ids( $event_id );
}

/**
 * Get a list of event moderators.
 *
 * @since 1.0.0
 *
 * @param int $event_id ID of the event.
 * @return array Info about event admins (user_id + date_modified).
 */
function events_get_event_mods( $event_id ) {
	return SZ_Events_Member::get_event_moderator_ids( $event_id );
}

/**
 * Fetch the members of a event.
 *
 * Since SportsZone 1.8, a procedural wrapper for SZ_Event_Member_Query.
 * Previously called SZ_Events_Member::get_all_for_event().
 *
 * To use the legacy query, filter 'sz_use_legacy_event_member_query',
 * returning true.
 *
 * @since 1.0.0
 * @since 3.0.0 $event_id now supports multiple values. Only works if legacy query is not
 *              in use.
 *
 * @param array $args {
 *     An array of optional arguments.
 *     @type int|array|string $event_id            ID of the event to limit results to. Also accepts multiple values
 *                                                 either as an array or as a comma-delimited string.
 *     @type int              $page                Page of results to be queried. Default: 1.
 *     @type int              $per_page            Number of items to return per page of results. Default: 20.
 *     @type int              $max                 Optional. Max number of items to return.
 *     @type array            $exclude             Optional. Array of user IDs to exclude.
 *     @type bool|int         $exclude_admins_mods True (or 1) to exclude admins and mods from results. Default: 1.
 *     @type bool|int         $exclude_banned      True (or 1) to exclude banned users from results. Default: 1.
 *     @type array            $event_role          Optional. Array of event roles to include.
 *     @type string           $search_terms        Optional. Filter results by a search string.
 *     @type string           $type                Optional. Sort the order of results. 'last_joined', 'first_joined', or
 *                                                 any of the $type params available in {@link SZ_User_Query}. Default:
 *                                                 'last_joined'.
 * }
 * @return false|array Multi-d array of 'members' list and 'count'.
 */
function events_get_event_members( $args = array() ) {

	// Backward compatibility with old method of passing arguments.
	if ( ! is_array( $args ) || func_num_args() > 1 ) {
		_deprecated_argument( __METHOD__, '2.0.0', sprintf( __( 'Arguments passed to %1$s should be in an associative array. See the inline documentation at %2$s for more details.', 'sportszone' ), __METHOD__, __FILE__ ) );

		$old_args_keys = array(
			0 => 'event_id',
			1 => 'per_page',
			2 => 'page',
			3 => 'exclude_admins_mods',
			4 => 'exclude_banned',
			5 => 'exclude',
			6 => 'event_role',
		);

		$args = sz_core_parse_args_array( $old_args_keys, func_get_args() );
	}

	$r = sz_parse_args( $args, array(
		'event_id'            => sz_get_current_event_id(),
		'per_page'            => false,
		'page'                => false,
		'exclude_admins_mods' => true,
		'exclude_banned'      => true,
		'exclude'             => false,
		'event_role'          => array(),
		'search_terms'        => false,
		'type'                => 'last_joined',
	), 'events_get_event_members' );

	// For legacy users. Use of SZ_Events_Member::get_all_for_event() is deprecated.
	if ( apply_filters( 'sz_use_legacy_event_member_query', false, __FUNCTION__, func_get_args() ) ) {
		$retval = SZ_Events_Member::get_all_for_event( $r['event_id'], $r['per_page'], $r['page'], $r['exclude_admins_mods'], $r['exclude_banned'], $r['exclude'] );
	} else {

		// Both exclude_admins_mods and exclude_banned are legacy arguments.
		// Convert to event_role.
		if ( empty( $r['event_role'] ) ) {
			$r['event_role'] = array( 'member' );

			if ( ! $r['exclude_admins_mods'] ) {
				$r['event_role'][] = 'mod';
				$r['event_role'][] = 'admin';
			}

			if ( ! $r['exclude_banned'] ) {
				$r['event_role'][] = 'banned';
			}
		}

		// Perform the event member query (extends SZ_User_Query).
		$members = new SZ_Event_Member_Query( array(
			'event_id'       => $r['event_id'],
			'per_page'       => $r['per_page'],
			'page'           => $r['page'],
			'event_role'     => $r['event_role'],
			'exclude'        => $r['exclude'],
			'search_terms'   => $r['search_terms'],
			'type'           => $r['type'],
		) );

		// Structure the return value as expected by the template functions.
		$retval = array(
			'members' => array_values( $members->results ),
			'count'   => $members->total_users,
		);
	}

	return $retval;
}

/**
 * Get the member count for a event.
 *
 * @since 1.2.3
 *
 * @param int $event_id Event ID.
 * @return int Count of confirmed members for the event.
 */
function events_get_total_member_count( $event_id ) {
	return SZ_Events_Event::get_total_member_count( $event_id );
}

/** Event Fetching, Filtering & Searching  ************************************/

/**
 * Get a collection of events, based on the parameters passed.
 *
 * @since 1.2.0
 * @since 2.6.0 Added `$event_type`, `$event_type__in`, and `$event_type__not_in` parameters.
 * @since 2.7.0 Added `$update_admin_cache` and `$parent_id` parameters.
 *
 * @param array|string $args {
 *     Array of arguments. Supports all arguments of
 *     {@link SZ_Events_Event::get()}. Where the default values differ, they
 *     have been described here.
 *     @type int $per_page Default: 20.
 *     @type int $page Default: 1.
 * }
 * @return array See {@link SZ_Events_Event::get()}.
 */
function events_get_events( $args = '' ) {

	$defaults = array(
		'type'               => false,          // Active, newest, alphabetical, random, popular.
		'order'              => 'DESC',         // 'ASC' or 'DESC'
		'orderby'            => 'date_created', // date_created, last_activity, total_member_count, name, random, meta_id.
		'user_id'            => false,          // Pass a user_id to limit to only events that this user is a member of.
		'include'            => false,          // Only include these specific events (event_ids).
		'exclude'            => false,          // Do not include these specific events (event_ids).
		'parent_id'          => null,           // Get events that are children of the specified event(s).
		'slug'               => array(),        // Find a event or events by slug.
		'search_terms'       => false,          // Limit to events that match these search terms.
		'search_columns'     => array(),        // Select which columns to search.
		'event_type'         => '',             // Array or comma-separated list of event types to limit results to.
		'event_type__in'     => '',             // Array or comma-separated list of event types to limit results to.
		'event_type__not_in' => '',             // Array or comma-separated list of event types that will be excluded from results.
		'meta_query'         => false,          // Filter by eventmeta. See WP_Meta_Query for syntax.
		'show_hidden'        => false,          // Show hidden events to non-admins.
		'status'             => array(),        // Array or comma-separated list of event statuses to limit results to.
		'per_page'           => 20,             // The number of results to return per page.
		'page'               => 1,              // The page to return if limiting per page.
		'update_meta_cache'  => true,           // Pre-fetch eventmeta for queried events.
		'update_admin_cache' => false,
		'fields'             => 'all',          // Return SZ_Events_Event objects or a list of ids.
	);

	$r = sz_parse_args( $args, $defaults, 'events_get_events' );

	$events = SZ_Events_Event::get( array(
		'type'               => $r['type'],
		'user_id'            => $r['user_id'],
		'include'            => $r['include'],
		'exclude'            => $r['exclude'],
		'slug'               => $r['slug'],
		'parent_id'          => $r['parent_id'],
		'search_terms'       => $r['search_terms'],
		'search_columns'     => $r['search_columns'],
		'event_type'         => $r['event_type'],
		'event_type__in'     => $r['event_type__in'],
		'event_type__not_in' => $r['event_type__not_in'],
		'meta_query'         => $r['meta_query'],
		'show_hidden'        => $r['show_hidden'],
		'status'             => $r['status'],
		'per_page'           => $r['per_page'],
		'page'               => $r['page'],
		'update_meta_cache'  => $r['update_meta_cache'],
		'update_admin_cache' => $r['update_admin_cache'],
		'order'              => $r['order'],
		'orderby'            => $r['orderby'],
		'fields'             => $r['fields'],
	) );

	/**
	 * Filters the collection of events based on parsed parameters.
	 *
	 * @since 1.2.0
	 *
	 * @param SZ_Events_Event $events Object of found events based on parameters.
	 *                                Passed by reference.
	 * @param array           $r      Array of parsed arguments used for event query.
	 *                                Passed by reference.
	 */
	return apply_filters_ref_array( 'events_get_events', array( &$events, &$r ) );
}

/**
 * Get the total event count for the site.
 *
 * @since 1.2.0
 *
 * @return int
 */
function events_get_total_event_count() {
	$count = wp_cache_get( 'sz_total_event_count', 'sz' );

	if ( false === $count ) {
		$count = SZ_Events_Event::get_total_event_count();
		wp_cache_set( 'sz_total_event_count', $count, 'sz' );
	}

	return $count;
}

/**
 * Get the IDs of the events of which a specified user is a member.
 *
 * @since 1.0.0
 *
 * @param int $user_id  ID of the user.
 * @param int $pag_num  Optional. Max number of results to return.
 *                      Default: false (no limit).
 * @param int $pag_page Optional. Page offset of results to return.
 *                      Default: false (no limit).
 * @return array {
 *     @type array $events Array of events returned by paginated query.
 *     @type int   $total Count of events matching query.
 * }
 */
function events_get_user_events( $user_id = 0, $pag_num = 0, $pag_page = 0 ) {

	if ( empty( $user_id ) )
		$user_id = sz_displayed_user_id();

	return SZ_Events_Member::get_event_ids( $user_id, $pag_num, $pag_page );
}

/**
 * Get a list of events of which the specified user is a member.
 *
 * Get a list of the events to which this member belongs,
 * filtered by event membership status and role.
 * Usage examples: Used with no arguments specified,
 *
 *    sz_get_user_events( sz_loggedin_user_id() );
 *
 * returns an array of the events in which the logged-in user
 * is an unpromoted member. To fetch an array of all events that
 * the current user belongs to, in any membership role,
 * member, moderator or administrator, use
 *
 *    sz_get_user_events( $user_id, array(
 *        'is_admin' => null,
 *        'is_mod' => null,
 *    ) );
 *
 * @since 2.6.0
 *
 * @param int $user_id ID of the user.
 * @param array $args {
 *     Array of optional args.
 *     @param bool|null   $is_confirmed Whether to return only confirmed memberships. Pass `null` to disable this
 *                                      filter. Default: true.
 *     @param bool|null   $is_banned    Whether to return only banned memberships. Pass `null` to disable this filter.
 *                                      Default: false.
 *     @param bool|null   $is_admin     Whether to return only admin memberships. Pass `null` to disable this filter.
 *                                      Default: false.
 *     @param bool|null   $is_mod       Whether to return only mod memberships. Pass `null` to disable this filter.
 *                                      Default: false.
 *     @param bool|null   $invite_sent  Whether to return only memberships with 'invite_sent'. Pass `null` to disable
 *                                      this filter. Default: false.
 *     @param string      $orderby      Field to order by. Accepts 'id' (membership ID), 'event_id', 'date_modified'.
 *                                      Default: 'event_id'.
 *     @param string      $order        Sort order. Accepts 'ASC' or 'DESC'. Default: 'ASC'.
 * }
 * @return array Array of matching event memberships, keyed by event ID.
 */
function sz_get_user_events( $user_id, $args = array() ) {
	$r = sz_parse_args( $args, array(
		'is_confirmed' => true,
		'is_banned'    => false,
		'is_admin'     => false,
		'is_mod'       => false,
		'invite_sent'  => null,
		'orderby'      => 'event_id',
		'order'        => 'ASC',
	), 'get_user_events' );

	$user_id = intval( $user_id );

	$membership_ids = wp_cache_get( $user_id, 'sz_events_memberships_for_user' );
	if ( false === $membership_ids ) {
		$membership_ids = SZ_Events_Member::get_membership_ids_for_user( $user_id );
		wp_cache_set( $user_id, $membership_ids, 'sz_events_memberships_for_user' );
	}

	// Prime the membership cache.
	$uncached_membership_ids = sz_get_non_cached_ids( $membership_ids, 'sz_events_memberships' );
	if ( ! empty( $uncached_membership_ids ) ) {
		$uncached_memberships = SZ_Events_Member::get_memberships_by_id( $uncached_membership_ids );

		foreach ( $uncached_memberships as $uncached_membership ) {
			wp_cache_set( $uncached_membership->id, $uncached_membership, 'sz_events_memberships' );
		}
	}

	// Assemble filter array for use in `wp_list_filter()`.
	$filters = wp_array_slice_assoc( $r, array( 'is_confirmed', 'is_banned', 'is_admin', 'is_mod', 'invite_sent' ) );
	foreach ( $filters as $filter_name => $filter_value ) {
		if ( is_null( $filter_value ) ) {
			unset( $filters[ $filter_name ] );
		}
	}

	// Populate event membership array from cache, and normalize.
	$events    = array();
	$int_keys  = array( 'id', 'event_id', 'user_id', 'inviter_id' );
	$bool_keys = array( 'is_admin', 'is_mod', 'is_confirmed', 'is_banned', 'invite_sent' );
	foreach ( $membership_ids as $membership_id ) {
		$membership = wp_cache_get( $membership_id, 'sz_events_memberships' );

		// Sanity check.
		if ( ! isset( $membership->event_id ) ) {
			continue;
		}

		// Integer values.
		foreach ( $int_keys as $index ) {
			$membership->{$index} = intval( $membership->{$index} );
		}

		// Boolean values.
		foreach ( $bool_keys as $index ) {
			$membership->{$index} = (bool) $membership->{$index};
		}

		foreach ( $filters as $filter_name => $filter_value ) {
			if ( ! isset( $membership->{$filter_name} ) || $filter_value != $membership->{$filter_name} ) {
				continue 2;
			}
		}

		$event_id = (int) $membership->event_id;

		$events[ $event_id ] = $membership;
	}

	// By default, results are ordered by membership id.
	if ( 'event_id' === $r['orderby'] ) {
		ksort( $events );
	} elseif ( in_array( $r['orderby'], array( 'id', 'date_modified' ) ) ) {
		$events = sz_sort_by_key( $events, $r['orderby'] );
	}

	// By default, results are ordered ASC.
	if ( 'DESC' === strtoupper( $r['order'] ) ) {
		// `true` to preserve keys.
		$events = array_reverse( $events, true );
	}

	return $events;
}

/**
 * Get the count of events of which the specified user is a member.
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. Default: ID of the displayed user.
 * @return int Event count.
 */
function events_total_events_for_user( $user_id = 0 ) {

	if ( empty( $user_id ) )
		$user_id = ( sz_displayed_user_id() ) ? sz_displayed_user_id() : sz_loggedin_user_id();

	$count = wp_cache_get( 'sz_total_events_for_user_' . $user_id, 'sz' );

	if ( false === $count ) {
		$count = SZ_Events_Member::total_event_count( $user_id );
		wp_cache_set( 'sz_total_events_for_user_' . $user_id, $count, 'sz' );
	}

	return (int) $count;
}

/**
 * Get the SZ_Events_Event object corresponding to the current event.
 *
 * @since 1.5.0
 *
 * @return SZ_Events_Event The current event object.
 */
function events_get_current_event() {
	$sz = sportszone();

	$current_event = isset( $sz->events->current_event )
		? $sz->events->current_event
		: false;

	/**
	 * Filters the SZ_Events_Event object corresponding to the current event.
	 *
	 * @since 1.5.0
	 *
	 * @param SZ_Events_Event $current_event Current SZ_Events_Event object.
	 */
	return apply_filters( 'events_get_current_event', $current_event );
}

/** Event Avatars *************************************************************/

/**
 * Generate the avatar upload directory path for a given event.
 *
 * @since 1.1.0
 *
 * @param int $event_id Optional. ID of the event. Default: ID of the current event.
 * @return string
 */
function events_avatar_upload_dir( $event_id = 0 ) {

	if ( empty( $event_id ) ) {
		$event_id = sz_get_current_event_id();
	}

	$directory = 'event-avatars';
	$path      = sz_core_avatar_upload_path() . '/' . $directory . '/' . $event_id;
	$newbdir   = $path;
	$newurl    = sz_core_avatar_url() . '/' . $directory . '/' . $event_id;
	$newburl   = $newurl;
	$newsubdir = '/' . $directory . '/' . $event_id;

	/**
	 * Filters the avatar upload directory path for a given event.
	 *
	 * @since 1.1.0
	 *
	 * @param array $value Array of parts related to the events avatar upload directory.
	 */
	return apply_filters( 'events_avatar_upload_dir', array(
		'path'    => $path,
		'url'     => $newurl,
		'subdir'  => $newsubdir,
		'basedir' => $newbdir,
		'baseurl' => $newburl,
		'error'   => false
	) );
}

/** Event Cover Images *************************************************************/

/**
 * Generate the avatar upload directory path for a given event.
 *
 * @since 1.1.0
 *
 * @param int $event_id Optional. ID of the event. Default: ID of the current event.
 * @return string
 */
function events_cover_image_upload_dir( $event_id = 0 ) {

	if ( empty( $event_id ) ) {
		$event_id = sz_get_current_event_id();
	}

	$directory = 'event-cover-images';
	$path      = sz_core_cover_image_upload_path() . '/' . $directory . '/' . $event_id;
	$newbdir   = $path;
	$newurl    = sz_core_cover_image_url() . '/' . $directory . '/' . $event_id;
	$newburl   = $newurl;
	$newsubdir = '/' . $directory . '/' . $event_id;

	/**
	 * Filters the avatar upload directory path for a given event.
	 *
	 * @since 1.1.0
	 *
	 * @param array $value Array of parts related to the events avatar upload directory.
	 */
	return apply_filters( 'events_cover_image_upload_dir', array(
		'path'    => $path,
		'url'     => $newurl,
		'subdir'  => $newsubdir,
		'basedir' => $newbdir,
		'baseurl' => $newburl,
		'error'   => false
	) );
}

/** Event Member Status Checks ************************************************/

/**
 * Check whether a user is an admin of a given event.
 *
 * @since 1.0.0
 *
 * @param int $user_id ID of the user.
 * @param int $event_id ID of the event.
 * @return int|bool ID of the membership if the user is admin, otherwise false.
 */
function events_is_user_admin( $user_id, $event_id ) {
	$is_admin = false;

	$user_events = sz_get_user_events( $user_id, array(
		'is_admin' => true,
	) );

	if ( isset( $user_events[ $event_id ] ) ) {
		$is_admin = $user_events[ $event_id ]->id;
	}

	return $is_admin;
}

/**
 * Check whether a user is a mod of a given event.
 *
 * @since 1.0.0
 *
 * @param int $user_id ID of the user.
 * @param int $event_id ID of the event.
 * @return int|bool ID of the membership if the user is mod, otherwise false.
 */
function events_is_user_mod( $user_id, $event_id ) {
	$is_mod = false;

	$user_events = sz_get_user_events( $user_id, array(
		'is_mod' => true,
	) );

	if ( isset( $user_events[ $event_id ] ) ) {
		$is_mod = $user_events[ $event_id ]->id;
	}

	return $is_mod;
}

/**
 * Check whether a user is a member of a given event.
 *
 * @since 1.0.0
 *
 * @param int $user_id ID of the user.
 * @param int $event_id ID of the event.
 * @return int|bool ID of the membership if the user is member, otherwise false.
 */
function events_is_user_member( $user_id, $event_id ) {
	$is_member = false;

	$user_events = sz_get_user_events( $user_id, array(
		'is_admin' => null,
		'is_mod' => null,
	) );

	if ( isset( $user_events[ $event_id ] ) ) {
		$is_member = $user_events[ $event_id ]->id;
	}

	return $is_member;
}

/**
 * Check whether a user is banned from a given event.
 *
 * @since 1.0.0
 *
 * @param int $user_id  ID of the user.
 * @param int $event_id ID of the event.
 * @return int|bool ID of the membership if the user is banned, otherwise false.
 */
function events_is_user_banned( $user_id, $event_id ) {
	$is_banned = false;

	$user_events = sz_get_user_events( $user_id, array(
		'is_confirmed' => null,
		'is_banned' => true,
	) );

	if ( isset( $user_events[ $event_id ] ) ) {
		$is_banned = $user_events[ $event_id ]->id;
	}

	return $is_banned;
}

/**
 * Check whether a user has an outstanding invitation to a event.
 *
 * @since 2.6.0
 *
 * @param int $user_id ID of the user.
 * @param int $event_id ID of the event.
 * @return int|bool ID of the membership if the user is invited, otherwise false.
 */
function events_is_user_invited( $user_id, $event_id ) {
	$is_invited = false;

	$user_events = sz_get_user_events( $user_id, array(
		'invite_sent' => true,
		'is_confirmed' => false,
	) );

	if ( isset( $user_events[ $event_id ] ) ) {
		$is_invited = $user_events[ $event_id ]->id;
	}

	return $is_invited;
}

/**
 * Check whether a user has a pending membership request for a event.
 *
 * @since 2.6.0
 *
 * @param int $user_id ID of the user.
 * @param int $event_id ID of the event.
 * @return int|bool ID of the membership if the user is pending, otherwise false.
 */
function events_is_user_pending( $user_id, $event_id ) {
	$is_pending = false;

	$user_events = sz_get_user_events( $user_id, array(
		'invite_sent' => false,
		'is_confirmed' => false,
	) );

	if ( isset( $user_events[ $event_id ] ) ) {
		$is_pending = $user_events[ $event_id ]->id;
	}

	return $is_pending;
}

/**
 * Is the specified user the creator of the event?
 *
 * @since 1.2.6
 *
 * @param int $user_id ID of the user.
 * @param int $event_id ID of the event.
 * @return int|null
 */
function events_is_user_creator( $user_id, $event_id ) {
	return SZ_Events_Member::check_is_creator( $user_id, $event_id );
}

/** Event Activity Posting ****************************************************/

/**
 * Post an Activity status update affiliated with a event.
 *
 * @since 1.2.0
 * @since 2.6.0 Added 'error_type' parameter to $args.
 *
 * @param array|string $args {
 *     Array of arguments.
 *     @type string $content  The content of the update.
 *     @type int    $user_id  Optional. ID of the user posting the update. Default:
 *                            ID of the logged-in user.
 *     @type int    $event_id Optional. ID of the event to be affiliated with the
 *                            update. Default: ID of the current event.
 * }
 * @return WP_Error|bool|int Returns the ID of the new activity item on success, or false on failure.
 */
function events_post_update( $args = '' ) {
	if ( ! sz_is_active( 'activity' ) ) {
		return false;
	}

	$sz = sportszone();

	$r = sz_parse_args( $args, array(
		'content'    => false,
		'user_id'    => sz_loggedin_user_id(),
		'event_id'   => 0,
		'error_type' => 'bool'
	), 'events_post_update' );
	extract( $r, EXTR_SKIP );

	if ( empty( $event_id ) && !empty( $sz->events->current_event->id ) )
		$event_id = $sz->events->current_event->id;

	if ( empty( $content ) || !strlen( trim( $content ) ) || empty( $user_id ) || empty( $event_id ) )
		return false;

	$sz->events->current_event = events_get_event( $event_id );

	// Be sure the user is a member of the event before posting.
	if ( !sz_current_user_can( 'sz_moderate' ) && !events_is_user_member( $user_id, $event_id ) )
		return false;

	// Record this in activity streams.
	$activity_action  = sprintf( __( '%1$s posted an update in the event %2$s', 'sportszone'), sz_core_get_userlink( $user_id ), '<a href="' . sz_get_event_permalink( $sz->events->current_event ) . '">' . esc_attr( $sz->events->current_event->name ) . '</a>' );
	$activity_content = $content;

	/**
	 * Filters the action for the new event activity update.
	 *
	 * @since 1.2.0
	 *
	 * @param string $activity_action The new event activity update.
	 */
	$action = apply_filters( 'events_activity_new_update_action',  $activity_action  );

	/**
	 * Filters the content for the new event activity update.
	 *
	 * @since 1.2.0
	 *
	 * @param string $activity_content The content of the update.
	 */
	$content_filtered = apply_filters( 'events_activity_new_update_content', $activity_content );

	$activity_id = events_record_activity( array(
		'user_id'    => $user_id,
		'action'     => $action,
		'content'    => $content_filtered,
		'type'       => 'activity_update',
		'item_id'    => $event_id,
		'error_type' => $error_type
	) );

	events_update_eventmeta( $event_id, 'last_activity', sz_core_current_time() );

	/**
	 * Fires after posting of an Activity status update affiliated with a event.
	 *
	 * @since 1.2.0
	 *
	 * @param string $content     The content of the update.
	 * @param int    $user_id     ID of the user posting the update.
	 * @param int    $event_id    ID of the event being posted to.
	 * @param bool   $activity_id Whether or not the activity recording succeeded.
	 */
	do_action( 'sz_events_posted_update', $content, $user_id, $event_id, $activity_id );

	return $activity_id;
}

/** Event Invitations *********************************************************/

/**
 * Get IDs of users with outstanding invites to a given event from a specified user.
 *
 * @since 1.0.0
 *
 * @param int               $user_id ID of the inviting user.
 * @param int|bool          $limit   Limit to restrict to.
 * @param int|bool          $page    Optional. Page offset of results to return.
 * @param string|array|bool $exclude Array of comma-separated list of event IDs
 *                                   to exclude from results.
 * @return array $value IDs of users who have been invited to the event by the
 *                      user but have not yet accepted.
 */
function events_get_invites_for_user( $user_id = 0, $limit = false, $page = false, $exclude = false ) {

	if ( empty( $user_id ) )
		$user_id = sz_loggedin_user_id();

	return SZ_Events_Member::get_invites( $user_id, $limit, $page, $exclude );
}

/**
 * Get the total event invite count for a user.
 *
 * @since 2.0.0
 *
 * @param int $user_id The user ID.
 * @return int
 */
function events_get_invite_count_for_user( $user_id = 0 ) {
	if ( empty( $user_id ) ) {
		$user_id = sz_loggedin_user_id();
	}

	return SZ_Events_Member::get_invite_count_for_user( $user_id );
}

/**
 * Invite a user to a event.
 *
 * @since 1.0.0
 *
 * @param array|string $args {
 *     Array of arguments.
 *     @type int    $user_id       ID of the user being invited.
 *     @type int    $event_id      ID of the event to which the user is being invited.
 *     @type int    $inviter_id    Optional. ID of the inviting user. Default:
 *                                 ID of the logged-in user.
 *     @type string $date_modified Optional. Modified date for the invitation.
 *                                 Default: current date/time.
 *     @type bool   $is_confirmed  Optional. Whether the invitation should be
 *                                 marked confirmed. Default: false.
 * }
 * @return bool True on success, false on failure.
 */
function events_invite_user( $args = '' ) {

	$args = sz_parse_args( $args, array(
		'user_id'       => false,
		'event_id'      => false,
		'inviter_id'    => sz_loggedin_user_id(),
		'date_modified' => sz_core_current_time(),
		'is_confirmed'  => 0
	), 'events_invite_user' );
	extract( $args, EXTR_SKIP );

	if ( ! $user_id || ! $event_id || ! $inviter_id ) {
		return false;
	}

	// If the user has already requested membership, accept the request.
	if ( $membership_id = events_check_for_membership_request( $user_id, $event_id ) ) {
		events_accept_membership_request( $membership_id, $user_id, $event_id );

	// Otherwise, create a new invitation.
	} elseif ( ! events_is_user_member( $user_id, $event_id ) && ! events_check_user_has_invite( $user_id, $event_id, 'all' ) ) {
		$invite                = new SZ_Events_Member;
		$invite->event_id      = $event_id;
		$invite->user_id       = $user_id;
		$invite->date_modified = $date_modified;
		$invite->inviter_id    = $inviter_id;
		$invite->is_confirmed  = $is_confirmed;

		if ( !$invite->save() )
			return false;

		/**
		 * Fires after the creation of a new event invite.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args Array of parsed arguments for the event invite.
		 */
		do_action( 'events_invite_user', $args );
	}

	return true;
}

/**
 * Uninvite a user from a event.
 *
 * Functionally, this is equivalent to removing a user from a event.
 *
 * @since 1.0.0
 *
 * @param int $user_id  ID of the user.
 * @param int $event_id ID of the event.
 * @return bool True on success, false on failure.
 */
function events_uninvite_user( $user_id, $event_id ) {

	if ( ! SZ_Events_Member::delete_invite( $user_id, $event_id ) )
		return false;

	/**
	 * Fires after uninviting a user from a event.
	 *
	 * @since 1.0.0
	 *
	 * @param int $event_id ID of the event being uninvited from.
	 * @param int $user_id  ID of the user being uninvited.
	 */
	do_action( 'events_uninvite_user', $event_id, $user_id );

	return true;
}

/**
 * Process the acceptance of a event invitation.
 *
 * Returns true if a user is already a member of the event.
 *
 * @since 1.0.0
 *
 * @param int $user_id  ID of the user.
 * @param int $event_id ID of the event.
 * @return bool True when the user is a member of the event, otherwise false.
 */
function events_accept_invite( $user_id, $event_id ) {

	// If the user is already a member (because BP at one point allowed two invitations to
	// slip through), delete all existing invitations/requests and return true.
	if ( events_is_user_member( $user_id, $event_id ) ) {
		if ( events_check_user_has_invite( $user_id, $event_id ) ) {
			events_delete_invite( $user_id, $event_id );
		}

		if ( events_check_for_membership_request( $user_id, $event_id ) ) {
			events_delete_membership_request( null, $user_id, $event_id );
		}

		return true;
	}

	$member = new SZ_Events_Member( $user_id, $event_id );

	// Save the inviter ID so that we can pass it to the action below.
	$inviter_id = $member->inviter_id;

	$member->accept_invite();

	if ( !$member->save() ) {
		return false;
	}

	// Remove request to join.
	if ( $member->check_for_membership_request( $user_id, $event_id ) ) {
		$member->delete_request( $user_id, $event_id );
	}

	// Modify event meta.
	events_update_eventmeta( $event_id, 'last_activity', sz_core_current_time() );

	/**
	 * Fires after a user has accepted a event invite.
	 *
	 * @since 1.0.0
	 * @since 2.8.0 The $inviter_id arg was added.
	 *
	 * @param int $user_id    ID of the user who accepted the event invite.
	 * @param int $event_id   ID of the event being accepted to.
	 * @param int $inviter_id ID of the user who invited this user to the event.
	 */
	do_action( 'events_accept_invite', $user_id, $event_id, $inviter_id );

	return true;
}

/**
 * Reject a event invitation.
 *
 * @since 1.0.0
 *
 * @param int $user_id  ID of the user.
 * @param int $event_id ID of the event.
 * @return bool True on success, false on failure.
 */
function events_reject_invite( $user_id, $event_id ) {
	if ( ! SZ_Events_Member::delete_invite( $user_id, $event_id ) )
		return false;

	/**
	 * Fires after a user rejects a event invitation.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id  ID of the user rejecting the invite.
	 * @param int $event_id ID of the event being rejected.
	 */
	do_action( 'events_reject_invite', $user_id, $event_id );

	return true;
}

/**
 * Delete a event invitation.
 *
 * @since 1.0.0
 *
 * @param int $user_id  ID of the invited user.
 * @param int $event_id ID of the event.
 * @return bool True on success, false on failure.
 */
function events_delete_invite( $user_id, $event_id ) {
	if ( ! SZ_Events_Member::delete_invite( $user_id, $event_id ) )
		return false;

	/**
	 * Fires after the deletion of a event invitation.
	 *
	 * @since 1.9.0
	 *
	 * @param int $user_id  ID of the user whose invitation is being deleted.
	 * @param int $event_id ID of the event whose invitation is being deleted.
	 */
	do_action( 'events_delete_invite', $user_id, $event_id );

	return true;
}

/**
 * Send all pending invites by a single user to a specific event.
 *
 * @since 1.0.0
 *
 * @param int $user_id  ID of the inviting user.
 * @param int $event_id ID of the event.
 */
function events_send_invites( $user_id, $event_id ) {

	if ( empty( $user_id ) )
		$user_id = sz_loggedin_user_id();

	// Send friend invites.
	$invited_users = events_get_invites_for_event( $user_id, $event_id );
	$event = events_get_event( $event_id );

	for ( $i = 0, $count = count( $invited_users ); $i < $count; ++$i ) {
		$member = new SZ_Events_Member( $invited_users[$i], $event_id );

		// Skip if we've already sent an invite to this user.
		if ( $member->invite_sent ) {
			continue;
		}

		// Send the actual invite.
		events_notification_event_invites( $event, $member, $user_id );

		$member->invite_sent = 1;
		$member->save();
	}

	/**
	 * Fires after the sending of invites for a event.
	 *
	 * @since 1.0.0
	 * @since 2.5.0 Added $user_id to passed parameters.
	 *
	 * @param int   $event_id      ID of the event who's being invited to.
	 * @param array $invited_users Array of users being invited to the event.
	 * @param int   $user_id       ID of the inviting user.
	 */
	do_action( 'events_send_invites', $event_id, $invited_users, $user_id );
}

/**
 * Get IDs of users with outstanding invites to a given event from a specified user.
 *
 * @since 1.0.0
 * @since 2.9.0 Added $sent as a parameter.
 *
 * @param  int      $user_id  ID of the inviting user.
 * @param  int      $event_id ID of the event.
 * @param  int|null $sent     Query for a specific invite sent status. If 0, this will query for users
 *                            that haven't had an invite sent to them yet. If 1, this will query for
 *                            users that have had an invite sent to them. If null, no invite status will
 *                            queried. Default: null.
 * @return array    IDs of users who have been invited to the event by the user but have not
 *                  yet accepted.
 */
function events_get_invites_for_event( $user_id, $event_id, $sent = null ) {
	return SZ_Events_Event::get_invites( $user_id, $event_id, $sent );
}

/**
 * Check to see whether a user has already been invited to a event.
 *
 * By default, the function checks for invitations that have been sent.
 * Entering 'all' as the $type parameter will return unsent invitations as
 * well (useful to make sure AJAX requests are not duplicated).
 *
 * @since 1.0.0
 *
 * @param int    $user_id  ID of potential event member.
 * @param int    $event_id ID of potential event.
 * @param string $type     Optional. Use 'sent' to check for sent invites,
 *                         'all' to check for all. Default: 'sent'.
 * @return int|bool ID of the membership if found, otherwise false.
 */
function events_check_user_has_invite( $user_id, $event_id, $type = 'sent' ) {
	$invite = false;

	$args = array(
		'is_confirmed' => false,
		'is_banned'    => null,
		'is_admin'     => null,
		'is_mod'       => null,
	);

	if ( 'sent' === $type ) {
		$args['invite_sent'] = true;
	}

	$user_events = sz_get_user_events( $user_id, $args );

	if ( isset( $user_events[ $event_id ] ) && 0 !== $user_events[ $event_id ]->inviter_id ) {
		$invite = $user_events[ $event_id ]->id;
	}

	return $invite;
}

/**
 * Delete all invitations to a given event.
 *
 * @since 1.0.0
 *
 * @param int $event_id ID of the event whose invitations are being deleted.
 * @return int|null Number of rows records deleted on success, null on failure.
 */
function events_delete_all_event_invites( $event_id ) {
	return SZ_Events_Event::delete_all_invites( $event_id );
}

/** Event Promotion & Banning *************************************************/

/**
 * Promote a member to a new status within a event.
 *
 * @since 1.0.0
 *
 * @param int    $user_id  ID of the user.
 * @param int    $event_id ID of the event.
 * @param string $status   The new status. 'mod' or 'admin'.
 * @return bool True on success, false on failure.
 */
function events_promote_member( $user_id, $event_id, $status ) {

	if ( ! sz_is_item_admin() )
		return false;

	$member = new SZ_Events_Member( $user_id, $event_id );

	// Don't use this action. It's deprecated as of SportsZone 1.6.
	do_action( 'events_premote_member', $event_id, $user_id, $status );

	/**
	 * Fires before the promotion of a user to a new status.
	 *
	 * @since 1.6.0
	 *
	 * @param int    $event_id ID of the event being promoted in.
	 * @param int    $user_id  ID of the user being promoted.
	 * @param string $status   New status being promoted to.
	 */
	do_action( 'events_promote_member', $event_id, $user_id, $status );

	return $member->promote( $status );
}

/**
 * Demote a user to 'member' status within a event.
 *
 * @since 1.0.0
 *
 * @param int $user_id  ID of the user.
 * @param int $event_id ID of the event.
 * @return bool True on success, false on failure.
 */
function events_demote_member( $user_id, $event_id ) {

	if ( ! sz_is_item_admin() )
		return false;

	$member = new SZ_Events_Member( $user_id, $event_id );

	/**
	 * Fires before the demotion of a user to 'member'.
	 *
	 * @since 1.0.0
	 *
	 * @param int $event_id ID of the event being demoted in.
	 * @param int $user_id  ID of the user being demoted.
	 */
	do_action( 'events_demote_member', $event_id, $user_id );

	return $member->demote();
}

/**
 * Ban a member from a event.
 *
 * @since 1.0.0
 *
 * @param int $user_id  ID of the user.
 * @param int $event_id ID of the event.
 * @return bool True on success, false on failure.
 */
function events_ban_member( $user_id, $event_id ) {

	if ( ! sz_is_item_admin() )
		return false;

	$member = new SZ_Events_Member( $user_id, $event_id );

	/**
	 * Fires before the banning of a member from a event.
	 *
	 * @since 1.0.0
	 *
	 * @param int $event_id ID of the event being banned from.
	 * @param int $user_id  ID of the user being banned.
	 */
	do_action( 'events_ban_member', $event_id, $user_id );

	return $member->ban();
}

/**
 * Unban a member from a event.
 *
 * @since 1.0.0
 *
 * @param int $user_id  ID of the user.
 * @param int $event_id ID of the event.
 * @return bool True on success, false on failure.
 */
function events_unban_member( $user_id, $event_id ) {

	if ( ! sz_is_item_admin() )
		return false;

	$member = new SZ_Events_Member( $user_id, $event_id );

	/**
	 * Fires before the unbanning of a member from a event.
	 *
	 * @since 1.0.0
	 *
	 * @param int $event_id ID of the event being unbanned from.
	 * @param int $user_id  ID of the user being unbanned.
	 */
	do_action( 'events_unban_member', $event_id, $user_id );

	return $member->unban();
}

/** Event Removal *************************************************************/

/**
 * Remove a member from a event.
 *
 * @since 1.2.6
 *
 * @param int $user_id  ID of the user.
 * @param int $event_id ID of the event.
 * @return bool True on success, false on failure.
 */
function events_remove_member( $user_id, $event_id ) {

	if ( ! sz_is_item_admin() ) {
		return false;
	}

	$member = new SZ_Events_Member( $user_id, $event_id );

	/**
	 * Fires before the removal of a member from a event.
	 *
	 * @since 1.2.6
	 *
	 * @param int $event_id ID of the event being removed from.
	 * @param int $user_id  ID of the user being removed.
	 */
	do_action( 'events_remove_member', $event_id, $user_id );

	return $member->remove();
}

/** Event Membership **********************************************************/

/**
 * Create a event membership request.
 *
 * @since 1.0.0
 *
 * @param int $requesting_user_id ID of the user requesting membership.
 * @param int $event_id           ID of the event.
 * @return bool True on success, false on failure.
 */
function events_send_membership_request( $requesting_user_id, $event_id ) {

	// Prevent duplicate requests.
	if ( events_check_for_membership_request( $requesting_user_id, $event_id ) )
		return false;

	// Check if the user is already a member or is banned.
	if ( events_is_user_member( $requesting_user_id, $event_id ) || events_is_user_banned( $requesting_user_id, $event_id ) )
		return false;

	// Check if the user is already invited - if so, simply accept invite.
	if ( events_check_user_has_invite( $requesting_user_id, $event_id ) ) {
		events_accept_invite( $requesting_user_id, $event_id );
		return true;
	}

	$requesting_user                = new SZ_Events_Member;
	$requesting_user->event_id      = $event_id;
	$requesting_user->user_id       = $requesting_user_id;
	$requesting_user->inviter_id    = 0;
	$requesting_user->is_admin      = 0;
	$requesting_user->user_title    = '';
	$requesting_user->date_modified = sz_core_current_time();
	$requesting_user->is_confirmed  = 0;
	$requesting_user->comments      = isset( $_POST['event-request-membership-comments'] ) ? $_POST['event-request-membership-comments'] : '';

	if ( $requesting_user->save() ) {
		$admins = events_get_event_admins( $event_id );

		// Saved okay, now send the email notification.
		for ( $i = 0, $count = count( $admins ); $i < $count; ++$i )
			events_notification_new_membership_request( $requesting_user_id, $admins[$i]->user_id, $event_id, $requesting_user->id );

		/**
		 * Fires after the creation of a new membership request.
		 *
		 * @since 1.0.0
		 *
		 * @param int   $requesting_user_id  ID of the user requesting membership.
		 * @param array $admins              Array of event admins.
		 * @param int   $event_id            ID of the event being requested to.
		 * @param int   $requesting_user->id ID of the membership.
		 */
		do_action( 'events_membership_requested', $requesting_user_id, $admins, $event_id, $requesting_user->id );

		return true;
	}

	return false;
}

/**
 * Accept a pending event membership request.
 *
 * @since 1.0.0
 *
 * @param int $membership_id ID of the membership object.
 * @param int $user_id       Optional. ID of the user who requested membership.
 *                           Provide this value along with $event_id to override
 *                           $membership_id.
 * @param int $event_id      Optional. ID of the event to which membership is being
 *                           requested. Provide this value along with $user_id to
 *                           override $membership_id.
 * @return bool True on success, false on failure.
 */
function events_accept_membership_request( $membership_id, $user_id = 0, $event_id = 0 ) {

	if ( !empty( $user_id ) && !empty( $event_id ) ) {
		$membership = new SZ_Events_Member( $user_id, $event_id );
	} else {
		$membership = new SZ_Events_Member( false, false, $membership_id );
	}

	$membership->accept_request();

	if ( !$membership->save() ) {
		return false;
	}

	// Check if the user has an outstanding invite, if so delete it.
	if ( events_check_user_has_invite( $membership->user_id, $membership->event_id ) ) {
		events_delete_invite( $membership->user_id, $membership->event_id );
	}

	/**
	 * Fires after a event membership request has been accepted.
	 *
	 * @since 1.0.0
	 *
	 * @param int  $user_id  ID of the user who accepted membership.
	 * @param int  $event_id ID of the event that was accepted membership to.
	 * @param bool $value    If membership was accepted.
	 */
	do_action( 'events_membership_accepted', $membership->user_id, $membership->event_id, true );

	return true;
}

/**
 * Reject a pending event membership request.
 *
 * @since 1.0.0
 *
 * @param int $membership_id ID of the membership object.
 * @param int $user_id       Optional. ID of the user who requested membership.
 *                           Provide this value along with $event_id to override
 *                           $membership_id.
 * @param int $event_id      Optional. ID of the event to which membership is being
 *                           requested. Provide this value along with $user_id to
 *                           override $membership_id.
 * @return bool True on success, false on failure.
 */
function events_reject_membership_request( $membership_id, $user_id = 0, $event_id = 0 ) {
	if ( !$membership = events_delete_membership_request( $membership_id, $user_id, $event_id ) ) {
		return false;
	}

	/**
	 * Fires after a event membership request has been rejected.
	 *
	 * @since 1.0.0
	 *
	 * @param int  $user_id  ID of the user who rejected membership.
	 * @param int  $event_id ID of the event that was rejected membership to.
	 * @param bool $value    If membership was accepted.
	 */
	do_action( 'events_membership_rejected', $membership->user_id, $membership->event_id, false );

	return true;
}

/**
 * Delete a pending event membership request.
 *
 * @since 1.2.0
 *
 * @param int $membership_id ID of the membership object.
 * @param int $user_id       Optional. ID of the user who requested membership.
 *                           Provide this value along with $event_id to override
 *                           $membership_id.
 * @param int $event_id      Optional. ID of the event to which membership is being
 *                           requested. Provide this value along with $user_id to
 *                           override $membership_id.
 * @return false|SZ_Events_Member True on success, false on failure.
 */
function events_delete_membership_request( $membership_id, $user_id = 0, $event_id = 0 ) {
	if ( !empty( $user_id ) && !empty( $event_id ) )
		$membership = new SZ_Events_Member( $user_id, $event_id );
	else
		$membership = new SZ_Events_Member( false, false, $membership_id );

	if ( ! SZ_Events_Member::delete_request( $membership->user_id, $membership->event_id ) )
		return false;

	return $membership;
}

/**
 * Check whether a user has an outstanding membership request for a given event.
 *
 * @since 1.0.0
 *
 * @param int $user_id  ID of the user.
 * @param int $event_id ID of the event.
 * @return int|bool ID of the membership if found, otherwise false.
 */
function events_check_for_membership_request( $user_id, $event_id ) {
	$request = false;

	$user_events = sz_get_user_events( $user_id, array(
		'is_confirmed' => false,
		'is_banned'    => false,
		'is_admin'     => null,
		'is_mod'       => null
	) );

	if ( isset( $user_events[ $event_id ] ) && 0 === $user_events[ $event_id ]->inviter_id ) {
		$request = $user_events[ $event_id ]->id;
	}

	return $request;
}

/**
 * Accept all pending membership requests to a event.
 *
 * @since 1.0.2
 *
 * @param int $event_id ID of the event.
 * @return bool True on success, false on failure.
 */
function events_accept_all_pending_membership_requests( $event_id ) {
	$user_ids = SZ_Events_Member::get_all_membership_request_user_ids( $event_id );

	if ( !$user_ids )
		return false;

	foreach ( (array) $user_ids as $user_id )
		events_accept_membership_request( false, $user_id, $event_id );

	/**
	 * Fires after the acceptance of all pending membership requests to a event.
	 *
	 * @since 1.0.2
	 *
	 * @param int $event_id ID of the event whose pending memberships were accepted.
	 */
	do_action( 'events_accept_all_pending_membership_requests', $event_id );

	return true;
}

/** Event Meta ****************************************************************/

/**
 * Delete metadata for a event.
 *
 * @since 1.0.0
 *
 * @param int         $event_id   ID of the event.
 * @param string|bool $meta_key   The key of the row to delete.
 * @param string|bool $meta_value Optional. Metadata value. If specified, only delete
 *                                metadata entries with this value.
 * @param bool        $delete_all Optional. If true, delete matching metadata entries
 *                                for all events. Otherwise, only delete matching
 *                                metadata entries for the specified event.
 *                                Default: false.
 * @return bool True on success, false on failure.
 */
function events_delete_eventmeta( $event_id, $meta_key = false, $meta_value = false, $delete_all = false ) {
	global $wpdb;

	// Legacy - if no meta_key is passed, delete all for the item.
	if ( empty( $meta_key ) ) {
		$keys = $wpdb->get_col( $wpdb->prepare( "SELECT meta_key FROM {$wpdb->eventmeta} WHERE event_id = %d", $event_id ) );

		// With no meta_key, ignore $delete_all.
		$delete_all = false;
	} else {
		$keys = array( $meta_key );
	}

	add_filter( 'query', 'sz_filter_metaid_column_name' );

	$retval = true;
	foreach ( $keys as $key ) {
		$retval = delete_metadata( 'event', $event_id, $key, $meta_value, $delete_all );
	}

	remove_filter( 'query', 'sz_filter_metaid_column_name' );

	return $retval;
}

/**
 * Get a piece of event metadata.
 *
 * @since 1.0.0
 *
 * @param int    $event_id ID of the event.
 * @param string $meta_key Metadata key.
 * @param bool   $single   Optional. If true, return only the first value of the
 *                         specified meta_key. This parameter has no effect if
 *                         meta_key is empty.
 * @return mixed Metadata value.
 */
function events_get_eventmeta( $event_id, $meta_key = '', $single = true ) {
	add_filter( 'query', 'sz_filter_metaid_column_name' );
	$retval = get_metadata( 'event', $event_id, $meta_key, $single );
	remove_filter( 'query', 'sz_filter_metaid_column_name' );

	return $retval;
}

/**
 * Update a piece of event metadata.
 *
 * @since 1.0.0
 *
 * @param int    $event_id   ID of the event.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Value to store.
 * @param mixed  $prev_value Optional. If specified, only update existing
 *                           metadata entries with the specified value.
 *                           Otherwise, update all entries.
 * @return bool|int $retval Returns false on failure. On successful update of existing
 *                          metadata, returns true. On successful creation of new metadata,
 *                          returns the integer ID of the new metadata row.
 */
function events_update_eventmeta( $event_id, $meta_key, $meta_value, $prev_value = '' ) {
	add_filter( 'query', 'sz_filter_metaid_column_name' );
	$retval = update_metadata( 'event', $event_id, $meta_key, $meta_value, $prev_value );
	remove_filter( 'query', 'sz_filter_metaid_column_name' );

	return $retval;
}

/**
 * Add a piece of event metadata.
 *
 * @since 2.0.0
 *
 * @param int    $event_id   ID of the event.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value.
 * @param bool   $unique     Optional. Whether to enforce a single metadata value
 *                           for the given key. If true, and the object already
 *                           has a value for the key, no change will be made.
 *                           Default: false.
 * @return int|bool The meta ID on successful update, false on failure.
 */
function events_add_eventmeta( $event_id, $meta_key, $meta_value, $unique = false ) {
	add_filter( 'query', 'sz_filter_metaid_column_name' );
	$retval = add_metadata( 'event', $event_id, $meta_key, $meta_value, $unique );
	remove_filter( 'query', 'sz_filter_metaid_column_name' );

	return $retval;
}

/** Event Cleanup Functions ***************************************************/

/**
 * Delete all event membership information for the specified user.
 *
 * @since 1.0.0
 *
 * @param int $user_id ID of the user.
 */
function events_remove_data_for_user( $user_id ) {
	SZ_Events_Member::delete_all_for_user( $user_id );

	/**
	 * Fires after the deletion of all data for a user.
	 *
	 * @since 1.1.0
	 *
	 * @param int $user_id ID of the user whose data is being deleted.
	 */
	do_action( 'events_remove_data_for_user', $user_id );
}
add_action( 'wpmu_delete_user',  'events_remove_data_for_user' );
add_action( 'delete_user',       'events_remove_data_for_user' );
add_action( 'sz_make_spam_user', 'events_remove_data_for_user' );

/**
 * Update orphaned child events when the parent is deleted.
 *
 * @since 2.7.0
 *
 * @param SZ_Events_Event $event Instance of the event item being deleted.
 */
function sz_events_update_orphaned_events_on_event_delete( $event ) {
	// Get child events and set the parent to the deleted parent's parent.
	$grandparent_event_id = $event->parent_id;
	$child_args = array(
		'parent_id'         => $event->id,
		'show_hidden'       => true,
		'per_page'          => false,
		'update_meta_cache' => false,
	);
	$children = events_get_events( $child_args );
	$children = $children['events'];

	foreach ( $children as $cevent ) {
		$cevent->parent_id = $grandparent_event_id;
		$cevent->save();
	}
}
add_action( 'sz_events_delete_event', 'sz_events_update_orphaned_events_on_event_delete', 10, 2 );

/** Event Types ***************************************************************/

/**
 * Fire the 'sz_events_register_event_types' action.
 *
 * @since 2.6.0
 */
function sz_events_register_event_types() {
	/**
	 * Fires when it's appropriate to register event types.
	 *
	 * @since 2.6.0
	 */
	do_action( 'sz_events_register_event_types' );
}
add_action( 'sz_register_taxonomies', 'sz_events_register_event_types' );

/**
 * Register a event type.
 *
 * @since 2.6.0
 * @since 2.7.0 Introduce $has_directory, $show_in_create_screen, $show_in_list, and
 *              $description, $create_screen_checked as $args parameters.
 *
 * @param string $event_type Unique string identifier for the event type.
 * @param array  $args {
 *     Array of arguments describing the event type.
 *
 *     @type string|bool $has_directory         Set the slug to be used for custom event directory page. eg.
 *                                              example.com/events/type/MY_SLUG. Default: false.
 *     @type bool        $show_in_create_screen Whether this event type is allowed to be selected on the event creation
 *                                              page. Default: false.
 *     @type bool|null   $show_in_list          Whether this event type should be shown in lists rendered by
 *                                              sz_event_type_list(). Default: null. If $show_in_create_screen is true,
 *                                              this will default to true, unless this is set explicitly to false.
 *     @type string      $description           A short descriptive summary of what the event type is. Currently shown
 *                                              on a event's "Manage > Settings" page when selecting event types.
 *     @type bool        $create_screen_checked If $show_in_create_screen is true, whether we should have our event type
 *                                              checkbox checked by default. Handy if you want to imply that the event
 *                                              type should be enforced, but decision lies with the event creator.
 *                                              Default: false.
 *     @type array       $labels {
 *         Array of labels to use in various parts of the interface.
 *
 *         @type string $name          Default name. Should typically be plural.
 *         @type string $singular_name Singular name.
 *     }
 * }
 * @return object|WP_Error Event type object on success, WP_Error object on failure.
 */
function sz_events_register_event_type( $event_type, $args = array() ) {
	$sz = sportszone();

	if ( isset( $sz->events->types[ $event_type ] ) ) {
		return new WP_Error( 'sz_event_type_exists', __( 'Event type already exists.', 'sportszone' ), $event_type );
	}

	$r = sz_parse_args( $args, array(
		'has_directory'         => false,
		'show_in_create_screen' => false,
		'show_in_list'          => null,
		'description'           => '',
		'create_screen_checked' => false,
		'labels'                => array(),
	), 'register_event_type' );

	$event_type = sanitize_key( $event_type );

	/**
	 * Filters the list of illegal event type names.
	 *
	 * - 'any' is a special pseudo-type, representing items unassociated with any event type.
	 * - 'null' is a special pseudo-type, representing users without any type.
	 * - '_none' is used internally to denote an item that should not apply to any event types.
	 *
	 * @since 2.6.0
	 *
	 * @param array $illegal_names Array of illegal names.
	 */
	$illegal_names = apply_filters( 'sz_event_type_illegal_names', array( 'any', 'null', '_none' ) );
	if ( in_array( $event_type, $illegal_names, true ) ) {
		return new WP_Error( 'sz_event_type_illegal_name', __( 'You may not register a event type with this name.', 'sportszone' ), $event_type );
	}

	// Store the event type name as data in the object (not just as the array key).
	$r['name'] = $event_type;

	// Make sure the relevant labels have been filled in.
	$default_name = isset( $r['labels']['name'] ) ? $r['labels']['name'] : ucfirst( $r['name'] );
	$r['labels'] = array_merge( array(
		'name'          => $default_name,
		'singular_name' => $default_name,
	), $r['labels'] );

	// Directory slug.
	if ( ! empty( $r['has_directory'] ) ) {
		// A string value is intepreted as the directory slug.
		if ( is_string( $r['has_directory'] ) ) {
			$directory_slug = $r['has_directory'];

		// Otherwise fall back on event type.
		} else {
			$directory_slug = $event_type;
		}

		// Sanitize for use in URLs.
		$r['directory_slug'] = sanitize_title( $directory_slug );
		$r['has_directory']  = true;
	} else {
		$r['directory_slug'] = '';
		$r['has_directory']  = false;
	}

	// Type lists.
	if ( true === $r['show_in_create_screen'] && is_null( $r['show_in_list'] ) ) {
		$r['show_in_list'] = true;
	} else {
		$r['show_in_list'] = (bool) $r['show_in_list'];
	}

	$sz->events->types[ $event_type ] = $type = (object) $r;

	/**
	 * Fires after a event type is registered.
	 *
	 * @since 2.6.0
	 *
	 * @param string $event_type Event type identifier.
	 * @param object $type       Event type object.
	 */
	do_action( 'sz_events_register_event_type', $event_type, $type );

	return $type;
}

/**
 * Get a list of all registered event type objects.
 *
 * @since 2.6.0
 *
 * @see sz_events_register_event_type() for accepted arguments.
 *
 * @param array|string $args     Optional. An array of key => value arguments to match against
 *                               the event type objects. Default empty array.
 * @param string       $output   Optional. The type of output to return. Accepts 'names'
 *                               or 'objects'. Default 'names'.
 * @param string       $operator Optional. The logical operation to perform. 'or' means only one
 *                               element from the array needs to match; 'and' means all elements
 *                               must match. Accepts 'or' or 'and'. Default 'and'.
 * @return array       $types    A list of events type names or objects.
 */
function sz_events_get_event_types( $args = array(), $output = 'names', $operator = 'and' ) {
	$types = sportszone()->events->types;

	$types = wp_filter_object_list( $types, $args, $operator );

	/**
	 * Filters the array of event type objects.
	 *
	 * This filter is run before the $output filter has been applied, so that
	 * filtering functions have access to the entire event type objects.
	 *
	 * @since 2.6.0
	 *
	 * @param array  $types     event type objects, keyed by name.
	 * @param array  $args      Array of key=>value arguments for filtering.
	 * @param string $operator  'or' to match any of $args, 'and' to require all.
	 */
	$types = apply_filters( 'sz_events_get_event_types', $types, $args, $operator );

	if ( 'names' === $output ) {
		$types = wp_list_pluck( $types, 'name' );
	}

	return $types;
}

/**
 * Retrieve a event type object by name.
 *
 * @since 2.6.0
 *
 * @param string $event_type The name of the event type.
 * @return object A event type object.
 */
function sz_events_get_event_type_object( $event_type ) {
	$types = sz_events_get_event_types( array(), 'objects' );

	if ( empty( $types[ $event_type ] ) ) {
		return null;
	}

	return $types[ $event_type ];
}

/**
 * Set type for a event.
 *
 * @since 2.6.0
 * @since 2.7.0 $event_type parameter also accepts an array of event types now.
 *
 * @param int          $event_id   ID of the event.
 * @param string|array $event_type Event type or array of event types to set.
 * @param bool         $append     Optional. True to append this to existing types for event,
 *                                 false to replace. Default: false.
 * @return false|array $retval See sz_set_object_terms().
 */
function sz_events_set_event_type( $event_id, $event_type, $append = false ) {
	// Pass an empty event type to remove event's type.
	if ( ! empty( $event_type ) && is_string( $event_type ) && ! sz_events_get_event_type_object( $event_type ) ) {
		return false;
	}

	// Cast as array.
	$event_type = (array) $event_type;

	// Validate event types.
	foreach ( $event_type as $type ) {
		// Remove any invalid event types.
		if ( is_null( sz_events_get_event_type_object( $type ) ) ) {
			unset( $event_type[ $type ] );
		}
	}

	$retval = sz_set_object_terms( $event_id, $event_type, 'sz_event_type', $append );

	// Bust the cache if the type has been updated.
	if ( ! is_wp_error( $retval ) ) {
		wp_cache_delete( $event_id, 'sz_events_event_type' );

		/**
		 * Fires just after a event type has been changed.
		 *
		 * @since 2.6.0
		 *
		 * @param int          $event_id   ID of the event whose event type has been updated.
		 * @param string|array $event_type Event type or array of event types.
		 * @param bool         $append     Whether the type is being appended to existing types.
		 */
		do_action( 'sz_events_set_event_type', $event_id, $event_type, $append );
	}

	return $retval;
}

/**
 * Get type for a event.
 *
 * @since 2.6.0
 *
 * @param int  $event_id ID of the event.
 * @param bool $single   Optional. Whether to return a single type string. If multiple types are found
 *                       for the event, the oldest one will be returned. Default: true.
 * @return string|array|bool On success, returns a single event type (if `$single` is true) or an array of event
 *                           types (if `$single` is false). Returns false on failure.
 */
function sz_events_get_event_type( $event_id, $single = true ) {
	$types = wp_cache_get( $event_id, 'sz_events_event_type' );

	if ( false === $types ) {
		$raw_types = sz_get_object_terms( $event_id, 'sz_event_type' );

		if ( ! is_wp_error( $raw_types ) ) {
			$types = array();

			// Only include currently registered event types.
			foreach ( $raw_types as $gtype ) {
				if ( sz_events_get_event_type_object( $gtype->name ) ) {
					$types[] = $gtype->name;
				}
			}

			wp_cache_set( $event_id, $types, 'sz_events_event_type' );
		}
	}

	$type = false;
	if ( ! empty( $types ) ) {
		if ( $single ) {
			$type = end( $types );
		} else {
			$type = $types;
		}
	}

	/**
	 * Filters a events's event type(s).
	 *
	 * @since 2.6.0
	 *
	 * @param string|array $type     Event type.
	 * @param int          $event_id ID of the event.
	 * @param bool         $single   Whether to return a single type string, or an array.
	 */
	return apply_filters( 'sz_events_get_event_type', $type, $event_id, $single );
}

/**
 * Remove type for a event.
 *
 * @since 2.6.0
 *
 * @param int            $event_id   ID of the user.
 * @param string         $event_type Event type.
 * @return bool|WP_Error $deleted    True on success. False or WP_Error on failure.
 */
function sz_events_remove_event_type( $event_id, $event_type ) {
	if ( empty( $event_type ) || ! sz_events_get_event_type_object( $event_type ) ) {
		return false;
	}

	$deleted = sz_remove_object_terms( $event_id, $event_type, 'sz_event_type' );

	// Bust the case, if the type has been removed.
	if ( ! is_wp_error( $deleted ) ) {
		wp_cache_delete( $event_id, 'sz_events_event_type' );

		/**
		 * Fires just after a event's event type has been removed.
		 *
		 * @since 2.6.0
		 *
		 * @param int    $event      ID of the event whose event type has been removed.
		 * @param string $event_type Event type.
		 */
		do_action( 'sz_events_remove_event_type', $event_id, $event_type );
	}

	return $deleted;
}

/**
 * Check whether the given event has a certain event type.
 *
 * @since 2.6.0
 *
 * @param  int    $event_id   ID of the event.
 * @param  string $event_type Event type.
 * @return bool   Whether the event has the give event type.
 */
function sz_events_has_event_type( $event_id, $event_type ) {
	if ( empty( $event_type ) || ! sz_events_get_event_type_object( $event_type ) ) {
		return false;
	}

	// Get all event's event types.
	$types = sz_events_get_event_type( $event_id, false );

	if ( ! is_array( $types ) ) {
		return false;
	}

	return in_array( $event_type, $types );
}

/**
 * Check whether the given event has team registered with it.
 *
 * @since 2.6.0
 *
 * @param  int    $event_id   ID of the event.
 * @param  string $event_type Event type.
 * @return bool   Whether the event has the give event type.
 */
function sz_event_has_team( $event_id, $event_team ) {
	if ( empty( $event_team ) ) {
		return false;
	}
	// Get all event's event types.
	$teams = (array) events_get_eventmeta( $event_id, 'event_teams' );
	
	if ( ! is_array( $teams ) ) {
		return false;
	}
	if(in_array( $event_team, $teams )) {
		return true;
	}
	
}

/**
 * Get the "current" event type, if one is provided, in event directories.
 *
 * @since 2.7.0
 *
 * @return string
 */
function sz_get_current_event_directory_type() {

	/**
	 * Filters the "current" event type, if one is provided, in event directories.
	 *
	 * @since 2.7.0
	 *
	 * @param string $value "Current" event type.
	 */
	return apply_filters( 'sz_get_current_event_directory_type', sportszone()->events->current_directory_type );
}

/**
 * Delete a event's type when the event is deleted.
 *
 * @since 2.6.0
 *
 * @param  int   $event_id ID of the event.
 * @return array|null $value    See {@see sz_events_set_event_type()}.
 */
function sz_remove_event_type_on_event_delete( $event_id = 0 ) {
	sz_events_set_event_type( $event_id, '' );
}
add_action( 'events_delete_event', 'sz_remove_event_type_on_event_delete' );



/**
 *	
 * -------------- ADDITONAL EVENT FIELDS --------------------*
 *
 */
 
 
add_action( 'sz_init', 'sz_additional_fields_select_teams_event_extension' );
add_action( 'sz_init', 'sz_additional_fields_add_matches_event_extension' );

/**
 * Add the Teams select tab to the Event Creation
 *
 * @since 3.1.0
 *
 * @return bool   Whether the event has the give event type.
 */
function sz_additional_fields_select_teams_event_extension() {
	if ( class_exists( 'SZ_Event_Extension' ) ) :

	class SZ_Additional_Fields_Select_Teams_Event_Extension extends SZ_Event_Extension {

		function __construct() {
			$args = array(
				'slug' => 'add-teams',
				'name' => 'Teams',
				'nav_item_position'	=> 2,
				'screens'	=> array(
					'create'	=> array(
						'position'	=> 1
					),	
				),
			);
			parent::init( $args );
		}

		function display($event_id = NULL) {
			$event_id = sz_get_event_id();
			$event_club 		= events_get_eventmeta( $event_id, 'event_club' );
			
			// TODO: Change to display club name
			if($event_club) echo "<h5>$event_club</h5>";
			
			echo 'Add Event Teams';
			
		}

		function settings_screen( $event_id = NULL ) {
			$event_club 		= events_get_eventmeta( $event_id, 'event_club' );
			?>
			<div>
				<label for="event-club"><?php esc_html_e( 'Associated Club', 'sportszone' ); ?></label>
				<?php
				$clubs_args = array(
					'user_id'		=> get_current_user_id( ),
					'group_type'	=> 'club'
				);
				if(sz_has_groups($clubs_args)):
					echo '<select name="event-club" id="event-club">';
					while(sz_groups()): sz_the_group();
						$selected = ($event_club == sz_get_group_id())?'selected="selected"':'';
						echo '<option value="'.sz_get_group_id().'" '.$selected.'>'.sz_get_group_name().'</option>';
					endwhile;
					echo '</select>';
				else:
					echo '<p>You arn\'t apart of any Clubs currently.';
				endif;
				
			?>
			</div>
			<div>
			<?php
			if( sz_events_has_event_type( $event_id, 'tour' ) ):
				/*
				 *	Main Team
				 */
				 // TODO: Only show main team if Tour type is selected
				 // TODO: Show teams that are associated with a club the user is a member of.
				$event_main_team 		= events_get_eventmeta( $event_id, 'event_main_team' );
				echo '<label for="event-main-team">';
					esc_html_e( 'Main Team', 'sportszone' );
				echo '</label>';
				
				$teams_args = array(
					'user_id'		=> get_current_user_id( ),
					'group_type'	=> 'team'
				);
				if(sz_has_groups($teams_args)):
					echo '<select name="event-main-team" id="event-main-team">';
					while(sz_groups()): sz_the_group();
						$selected = ($event_main_team == sz_get_group_id())?'selected="selected"':'';
						echo '<option value="'.sz_get_group_id().'" '.$selected.'>'.sz_get_group_name().'</option>';
					endwhile;
					echo '</select>';
				else:
					echo '<p>You arn\'t apart of any Teams currently.';
				endif;
			
			endif;
				?>
			</div>
			<?php 
			/*
			 *	Teams Loop
			 */
			 // TODO: Only show main team if Tour type is selected
			 // TODO: Show teams that are associated with a club the user is a member of.
			if ( sz_has_groups( array( 'group_type'	=> 'team') ) ) : ?>

				<?php sz_nouveau_pagination( 'top' ); ?>
			
				<ul id="groups-list" class="<?php sz_nouveau_loop_classes(); ?>">
			
				<?php
				while ( sz_groups() ) :
					sz_the_group();
				?>
			
					<li <?php sz_group_class( array( 'item-entry' ) ); ?> data-sz-item-id="<?php sz_group_id(); ?>" data-sz-item-component="groups">
						<label class="list-wrap" for="<?php printf( 'event-team-%s', sz_group_slug() ); ?>">
							<input type="checkbox" name="event-teams[]" id="<?php printf( 'event-team-%s', sz_group_slug() ); ?>" value="<?php echo esc_attr( sz_group_slug() ); ?>" <?php checked( sz_event_has_team( sz_get_current_event_id(), sz_get_group_slug() ) ); ?>/>
							<?php 
								if ( ! sz_disable_group_avatar_uploads() ) : ?>
								<div class="item-avatar">
									<?php sz_group_avatar( sz_nouveau_avatar_args() ); ?>
								</div>
							<?php endif; ?>
			
							<div class="item">
			
								<div class="item-block">
			
									<h2 class="list-title groups-title"><?php sz_group_name(); ?></h2>
			
									<?php if ( sz_nouveau_group_has_meta() ) : ?>
			
										<p class="item-meta group-details"><?php sz_nouveau_group_meta(); ?></p>
			
									<?php endif; ?>
			
			
								</div>
								<?php sz_nouveau_groups_loop_item(); ?>
							</div>
			
			
						</label>
					</li>
			
				<?php endwhile; ?>
			
				</ul>
			
				<?php sz_nouveau_pagination( 'bottom' );
			
			else :
				sz_nouveau_user_feedback( 'groups-loop-none' );
			endif; 
				
			
		}

		function settings_screen_save( $event_id = NULL ) {
			
			if ( isset( $_POST['event-club'] ) ) {
				events_update_eventmeta( $event_id, 'event_club', sanitize_text_field($_POST['event-club']) );
			}
			if ( isset( $_POST['event-main-team'] ) ) {
				events_update_eventmeta( $event_id, 'event_main_team', sanitize_text_field($_POST['event-main-team']) );
			}
			if ( isset( $_POST['event-teams'] ) ) {
				events_update_eventmeta( $event_id, 'event_teams', $_POST['event-teams'] );
			}
		}
	}
	sz_register_event_extension( 'SZ_Additional_Fields_Select_Teams_Event_Extension' );
	
	
	
	endif; // if ( class_exists( 'SZ_Additional_Fields_Select_Teams_Event_Extension' ) )
}


/**
 * Add the additional fields for the Matches tab when creating Events
 *
 * @since 3.1.0
 *
 * @param  int    $event_id   ID of the event.
 * @param  string $event_type Event type.
 * @return bool   Whether the event has the give event type.
 */
function sz_additional_fields_add_matches_event_extension() {
	if ( class_exists( 'SZ_Event_Extension' ) ) :

	class SZ_Additional_Fields_Add_Matches_Event_Extension extends SZ_Event_Extension {

		function __construct() {
			$args = array(
				'slug' => 'add-matches',
				'name' => 'Matches',
				'nav_item_position'	=> 2,
				'screens'	=> array(
					'create'	=> array(
						'position'	=> 2
					),	
				),
			);
			parent::init( $args );
		}

		function display($event_id = NULL) {
			$event_id = sz_get_event_id();
			$event_matches 		= events_get_eventmeta( $event_id, 'sz_matches_group' );
			
			// TODO: Change to display club name
			
			echo '<pre>';
			if($event_matches) print_r($event_matches);
			echo 'Add Matches Here';
			
		}

		function settings_screen( $event_id = NULL ) {
			$event_club 		= events_get_eventmeta( $event_id, 'event_club' );
			$user_id = get_current_user_id( );
				
			//$is_mod = SZ_Groups_Member::get_is_mod_of( $user_id );
			
			$is_admin_of = SZ_Groups_Member::get_is_admin_of( $user_id ); // Get list of all groups user is a admin of
			
			// if user is admin of any groups
			if(is_array($is_admin_of)){
				$accepted_types = array('team', 'club', 'union');
				$can_create = false;
				
				// Loop through each group and check its type
				foreach($is_admin_of['groups'] as $group){
					$type = sz_groups_get_group_type($group->id);
					if(in_array($type, $accepted_types) ) {
						$can_create = true;
					}
				}
				if($can_create) {
					
					cmb2_metabox_form( 'matches_metabox', $event_id );
					
				} else {
					echo '<h3>You must be a admin of a Team, Club, or Union to create an event.</h3>';
				}
			} else {
				echo '<h3>You must be a admin of a Team, Club, or Union to create an event.</h3>';
			}
			
		}
		
		/**
		 * Save all Match Settings
		 */
		function settings_screen_save( $event_id = NULL ) {
			if ( isset( $_POST['sz_matches_group'] ) ) {
				$matches = $_POST['sz_matches_group'];
				events_update_eventmeta( $event_id, 'sz_matches_group', $matches);
				
				foreach($matches as $match) :
					$match_data = array(
						'post_type'		=> 'sz_match',
						'post_title'	=> 'Test Title',
						'post_status'	=> 'publish',
						'post_author'	=> get_current_user_id(),
						'meta_input'	=> array(
							'sz_team'		=> array(
								(int) $match['match_team1']['team'],
								(int) $match['match_team2']['team']
							),
							'sz_day'		=>  $match['match_date']['date']
						)
					);
					
					
					$new_match_id = wp_insert_post( $match_data, true );
				endforeach;
			}
		}
	}
	sz_register_event_extension( 'SZ_Additional_Fields_Add_Matches_Event_Extension' );
	
	
	
	endif; // if ( class_exists( 'SZ_Event_Extension' ) )
}



add_action( 'cmb2_init', 'cmb2_events_matches_metaboxes' );
/**
 * Define the metabox and field configurations.
 */
function cmb2_events_matches_metaboxes() {
	global $sz;
	
	// Start with an underscore to hide fields from custom fields list
	$prefix = 'sz_';
	/**
	 * Initiate the metabox
	 */
	$cmb = new_cmb2_box( array(
		'id'            => 'matches_metabox',
		'title'         => __( 'Matches Metabox', 'cmb2' ),
		'object_types'  => array( 'event', ), // Post type
		'context'       => 'normal',
		'priority'      => 'high',
		'show_names'    => true, 
	) );
	
	$group_field_id = $cmb->add_field( array(
		'id'          => 'sz_matches_group',
		'type'        => 'group',
		'description' => __( 'Add your matches', 'cmb2' ),
		'options'     => array(
			'group_title'   => __( 'Match {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
			'add_button'    => __( 'Add Another MAtch', 'cmb2' ),
			'remove_button' => __( 'Remove Match', 'cmb2' ),
			'sortable'      => true, // beta
			'closed'     	=> true, // true to have the groups closed by default
		),
	) );
	
	$cmb->add_group_field( $group_field_id, array(
		'name' => 'Match Type',
		'id'   => 'match_type',
		'type' => 'radio_inline',
		'options' => array(
			'friendly' 		=> __( 'Friendly', 'cmb2' ),
			'round-robin' 	=> __( 'Round Robin', 'cmb2' ),
			'semi-final'    => __( 'Semi-Final', 'cmb2' ),
			'final'    		=> __( 'Final', 'cmb2' ),
			'league-play'   => __( 'League Play', 'cmb2' ),
		),
	) );
	
	$cmb->add_group_field( $group_field_id, array(
		'name' => 'Venue',
		'id'   => 'match_venue',
		'type' => 'text',
	) );
	
	$cmb->add_group_field( $group_field_id, array( 
		'name' => 'Date/Time',
		'id'   => 'match_date',
		'type' => 'text_datetime_timestamp',
	) );
	
	$cmb->add_group_field( $group_field_id, array(
		'name' => 'Host',
		'id'   => 'match_host',
		'type' => 'text',
	) );
	
	$cmb->add_group_field( $group_field_id, array(
		'name' => 'Sponsor',
		'id'   => 'match_sponsor',
		'type' => 'text',
	) );
	
	$cmb->add_group_field( $group_field_id, array(
		'name' => 'Referee',
		'id'   => 'match_referee',
		'type' => 'pw_select',
		'options' => array(
			'flour'  => 'Flour',
			'salt'   => 'Salt',
			'eggs'   => 'Eggs',
			'milk'   => 'Milk',
			'butter' => 'Butter',
		),
		'attributes' => array(
			'tags' => true,
		),
	) );
	
	// If tour type, set the default of the first team to the main team
	$event_id = 0;
	if(isset($sz->events->current_event->id)) {
		$event_id = $sz->events->current_event->id;
	}
	$default_team = ( sz_events_get_event_type($event_id) == 'tour' ) ? (string) events_get_eventmeta( $event_id, 'event_main_team' ) : 0;
	$cmb->add_group_field( $group_field_id, array(
		'name' => 'Team 1',
		'id'   => 'match_team1',
		'type' => 'select_team',
		'default'	=> $default_team
	) );

		
	
	$cmb->add_group_field( $group_field_id, array(
		'name' => 'Team 2',
		'id'   => 'match_team2',
		'type' => 'select_team',
		
	) );
}