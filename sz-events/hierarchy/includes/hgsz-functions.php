<?php
/**
 * Functions for the plugin in the global scope.
 * These may be useful for users working on theming or extending the plugin.
 *
 * @package   HierarchicalEventsForSZ
 * @author    dcavins
 * @license   GPL-2.0+
 * @copyright 2016 David Cavins
 */

/**
 * Is the current page a event's subevent directory?
 *
 * Eg http://example.com/events/myevent/hierarchy/.
 *
 * @since 1.0.0
 *
 * @return bool True if the current page is a event's directory of subevents.
 */
function hgsz_is_hierarchy_screen() {
	$screen_slug = hgsz_get_hierarchy_screen_slug();
	return (bool) ( sz_is_events_component() && sz_is_current_action( $screen_slug ) );
}

/**
 * Is this a user's "My Events" view? This can happen on the main directory or
 * at a user's profile (/members/username/events/).
 *
 * @since 1.0.0
 *
 * @return bool True if yes.
 */
function hgsz_is_my_events_view() {
	$retval = false;

	// Could be the user profile events pane.
	if ( sz_is_user_events() ) {
		$retval = true;

	// Could be the "my events" filter on the main directory?
	} elseif ( sz_is_events_directory() && ( isset( $_COOKIE['sz-events-scope'] ) && 'personal' == $_COOKIE['sz-events-scope'] ) ) {
		$retval = true;
	}

	return $retval;
}

/**
 * Get the child events for a specific event.
 *
 * To return all child events, leave the $user_id parameter empty. To return
 * only those child events visible to a specific user, specify a $user_id.
 *
 * @since 1.0.0
 *
 * @param int    $event_id ID of the event.
 * @param int    $user_id  ID of a user to check event visibility for.
 * @param string $context  See hgsz_include_event_by_context() for description.
 *
 * @return array Array of event objects.
 */
function hgsz_get_child_events( $event_id = false, $user_id = false, $context = 'normal' ) {
	$events = array();

	/*
	 * Passing a event id of 0 would find all top-level events, which could be
	 * intentional. We only try to find the current event when the $event_id is false.
	 */
	if ( false === $event_id ) {
		$event_id = sz_get_current_event_id();
		if ( ! $event_id ) {
			// If we can't resolve the event_id, don't proceed with a zero value.
			return $retval;
		}
	}

	// Fetch all child events.
	$child_args = array(
		'parent_id'   => $event_id,
		'show_hidden' => true,
		'per_page'    => false,
		'page'        => false,
	);
	$children  = events_get_events( $child_args );

	// If a user ID has been specified, we filter events accordingly.
	$filter = ( false !== $user_id && ! sz_user_can( $user_id, 'sz_moderate' ) );

	foreach ( $children['events'] as $child ) {
		if ( $filter ) {
			if ( hgsz_include_event_by_context( $child, $user_id, $context ) ) {
				$events[] = $child;
			}
		} else {
			$events[] = $child;
		}
	}

	return $events;
}

/**
 * Does a specific event have child events?
 *
 * To check for the actual existence of child events, leave the $user_id
 * parameter empty. To check whether any exist that are visible to a user,
 * supply a $user_id.
 *
 * @since 1.0.0
 *
 * @param int    $event_id ID of the event.
 * @param int    $user_id  ID of a user to check event visibility for.
 * @param string $context  See hgsz_include_event_by_context() for description.
 *
 * @return bool True if true, false if not.
 */
function hgsz_event_has_children( $event_id = false, $user_id = false, $context = 'normal' ) {

	/*
	 * Passing a event id of 0 finds all top-level events, which could be
	 * intentional. Try to find the current event only when the $event_id is false.
	 */
	if ( false === $event_id ) {
		$event_id = sz_get_current_event_id();
		if ( ! $event_id ) {
			// If we can't resolve the event_id, don't proceed with a zero value.
			return false;
		}
	}

	// We may need to adjust the context, based on what kind of directory we're on.
	if ( 'directory' == $context ) {
		if ( sz_is_events_directory() ) {
			// If the directory is AJAX powered, we have to check cookies.
			if ( isset( $_COOKIE['sz-events-scope'] ) && 'personal' == $_COOKIE['sz-events-scope'] ) {
				// Hidden events are included in this directory.
				$context = 'myevents';
			} else {
				// Hidden events are not included in standard directories.
				$context = 'exclude_hidden';
			}
		} elseif ( sz_is_user_events() ) {
			// Hidden events are included in this directory.
			$context = 'myevents';
		} else {
			// Fallback: Hidden events are not included in standard directories.
			$context = 'exclude_hidden';
		}
	}

	$children = hgsz_get_child_events( $event_id, $user_id, $context );
	return count( $children );
}

/**
 * Get all events that are descendants of a specific event.
 *
 * To return all descendent events, leave the $user_id parameter empty. To return
 * only those child events visible to a specific user, specify a $user_id.
 *
 * @since 1.0.0
 *
 * @param int    $event_id ID of the event.
 * @param int    $user_id  ID of a user to check event visibility for.
 * @param string $context  See hgsz_include_event_by_context() for description.
 *
 * @return array Array of event objects.
 */
function hgsz_get_descendent_events( $event_id = false, $user_id = false, $context = 'normal' ) {
	/*
	 * Passing a event id of 0 would find all top-level events, which could be
	 * intentional. We only try to find the current event when the $event_id is false.
	 */
	if ( false === $event_id ) {
		$event_id = sz_get_current_event_id();
		if ( ! $event_id ) {
			// If we can't resolve the event_id, don't proceed with a zero value.
			return array();
		}
	}

	// Prepare the return set.
	$events = array();
	// If a user ID has been specified, we filter hidden events accordingly.
	$filter = ( false !== $user_id && ! sz_user_can( $user_id, 'sz_moderate' ) );

	// Start from the event specified.
	$parents = array( $event_id );
	$descendants = array();

	// We work down the tree until no new children are found.
	while ( $parents ) {
		// Fetch all child events.
		$child_args = array(
			'parent_id'   => $parents,
			'show_hidden' => true,
			'per_page'    => false,
			'page'        => false,
		);
		$children = events_get_events( $child_args );
		// Reset parents array to rebuild for next round.
		$parents = array();
		foreach ( $children['events'] as $event ) {
			if ( $filter ) {
				if ( hgsz_include_event_by_context( $event, $user_id, $context ) ) {
					$events[] = $event;
					$parents[] = $event->id;
				}
			} else {
				$events[] = $event;
				$parents[] = $event->id;
			}
		}
	}

	return $events;
}

/**
 * Check a slug to see if a child event of a specific parent event exists.
 *
 * Like `events_get_id()`, but limited to children of a specific event. Avoids
 * slug collisions between event tab names and events with the same slug.
 * For instance, if there's a unrelated event called "Docs", you want the
 * "docs" tab of a event to ignore that event and return the docs pane for the
 * current event.
 * Caveat: If you create a child event with the same slug as a tab of the parent
 * event, you'll always get the child event.
 *
 * @since 1.0.0
 *
 * @param string $slug      Event slug to check.
 * @param int    $parent_id ID of the parent event.
 *
 * @return int ID of found event.
 */
function hgsz_child_event_exists( $slug, $parent_id = 0 ) {
	if ( empty( $slug ) ) {
		return 0;
	}

	/*
	 * Take advantage of caching in events_get_events().
	 */
	$child_id = 0;
	if ( version_compare( sz_get_version(), '2.9', '<' ) ) {
		// Fetch events with parent_id and loop through looking for a matching slug.
		$child_events = events_get_events( array(
			'parent_id'   => array( $parent_id ),
			'show_hidden' => true,
			'per_page'    => false,
			'page'        => false,
		) );

		foreach ( $child_events['events'] as $event ) {
			if ( $slug == $event->slug ) {
				$child_id = $event->id;
				// Stop once we've got a match.
				break;
			}
		}
	} else {
		// SZ 2.9 adds "slug" support to events_get_events().
		$child_events = events_get_events( array(
			'parent_id'   => array( $parent_id ),
			'slug'        => array( $slug ),
			'show_hidden' => true,
			'per_page'    => false,
			'page'        => false,
		) );

		if ( $child_events['events'] ) {
			$child_id = current( $child_events['events'] )->id;
		}
	}

	return $child_id;
}

/**
 * Get the parent event ID for a specific event.
 *
 * To return the parent event regardless of visibility, leave the $user_id
 * parameter empty. To return the parent only when visible to a specific user,
 * specify a $user_id.
 *
 * @since 1.0.0
 *
 * @param int    $event_id ID of the event.
 * @param int    $user_id  ID of a user to check event visibility for.
 * @param string $context  See hgsz_include_event_by_context() for description.
 *
 * @return int ID of parent event.
 */
function hgsz_get_parent_event_id( $event_id = false, $user_id = false, $context = 'normal' ) {
	if ( false === $event_id ) {
		$event_id = sz_get_current_event_id();
		if ( ! $event_id ) {
			// If we can't resolve the event_id, don't proceed.
			return 0;
		}
	}

	$event     = events_get_event( $event_id );
	$parent_id = $event->parent_id;

	// If the user is specified, is the parent event visible to that user?

	if ( false !== $user_id && ! sz_user_can( $user_id, 'sz_moderate' ) ) {
		$parent_event = events_get_event( $parent_id );
		if ( ! hgsz_include_event_by_context( $parent_event, $user_id, $context ) ) {
			$parent_id = 0;
		}
	}

	return (int) $parent_id;
}

/**
 * Get an array of event ids that are ancestors of a specific event.
 *
 * To return all ancestor events, leave the $user_id parameter empty. To return
 * only those ancestor events visible to a specific user, specify a $user_id.
 * Note that if events the user can't see are encountered, the chain of ancestry
 * is stopped. Also note that the order here is useful: the first element is the
 * parent event id, the second is the grandparent event id and so on.
 *
 * @since 1.0.0
 *
 * @param  int   $event_id ID of the event.
 * @param  int   $user_id  ID of a user to check event visibility for.
 * @param  string $context  'normal' filters hidden events only; 'activity' includes
 *                          only events for which the user should see the activity streams.
 *
 * @return array Array of event IDs.
 */
function hgsz_get_ancestor_event_ids( $event_id = false, $user_id = false, $context = 'normal' ) {
	/*
	 * Passing a event id of 0 would find all top-level events, which could be
	 * intentional. We only try to find the current event when the $event_id is false.
	 */
	if ( false === $event_id ) {
		$event_id = sz_get_current_event_id();
		if ( ! $event_id ) {
			// If we can't resolve the event_id, don't proceed with a zero value.
			return array();
		}
	}

	$ancestors = array();

	// We work up the tree until no new parent is found.
	while ( $event_id ) {
		$parent_event_id = hgsz_get_parent_event_id( $event_id, $user_id, $context );
		if ( $parent_event_id ) {
			$ancestors[] = $parent_event_id;
		}
		// Set a new event id to work from.
		$event_id = $parent_event_id;
	}

	return $ancestors;
}

/**
 * Get an array of possible parent event ids for a specific event and user.
 *
 * To be a candidate for event parenthood, the event cannot be a descendent of
 * this event, and the user must be allowed to create child events in that event.
 *
 * @since 1.0.0
 *
 * @param  int   $event_id ID of the event.
 * @param  int   $user_id  ID of a user to check event visibility for.
 *
 * @return array Array of event objects.
 */
function hgsz_get_possible_parent_events( $event_id = false, $user_id = false ) {
	/*
	 * Passing a event id of 0 would find all top-level events, which could be
	 * intentional. We only try to find the current event when the $event_id is false.
	 */
	if ( false === $event_id ) {
		$event_id = sz_get_current_event_id();
		if ( ! $event_id ) {
			// If we can't resolve the event_id, don't proceed with a zero value.
			return array();
		}
	}

	if ( false === $user_id ) {
		$user_id = sz_loggedin_user_id();
		if ( ! $user_id ) {
			// If we can't resolve the user_id, don't proceed with a zero value.
			return array();
		}
	}

	// First, get a list of descendants (don't pass a user id--we want them all).
	$descendants = hgsz_get_descendent_events( $event_id );
	$exclude_ids = wp_list_pluck( $descendants, 'id' );
	// Also exclude the current event.
	$exclude_ids[] = $event_id;

	$args = array(
		'orderby'         => 'name',
		'order'           => 'ASC',
		'populate_extras' => false,
		'exclude'         => $exclude_ids, // Exclude descendants and this event.
		'show_hidden'     => true,
		'per_page'        => false, // Do not limit the number returned.
		'page'            => false, // Do not limit the number returned.
	);

	$possible_parents = events_get_events( $args );
	foreach ( $possible_parents['events'] as $k => $event ) {
		// Check whether the user can create child events of this event.
		if ( ! sz_user_can( $user_id, 'create_subevents', array( 'event_id' => $event->id ) ) ) {
			unset( $possible_parents['events'][$k] );
		}
	}

	return $possible_parents['events'];
}
