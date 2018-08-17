<?php
/**
 * Utility functions for the plugin in the global scope.
 * These are used internally, but are probably not interesting for users
 * of the plugin.
 *
 * @package   HierarchicalEventsForSZ
 * @author    dcavins
 * @license   GPL-2.0+
 * @copyright 2016 David Cavins
 */

/**
 * Get the slug of the hierarchy screen for a event.
 *
 * @since 1.0.0
 *
 * @return string Slug to use as part of the url.
 */
function hgsz_get_hierarchy_screen_slug() {
	/**
	 * Filters the slug used for the hierarchy screen for a event.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Slug to use.
	 */
	return apply_filters( 'hgsz_screen_slug', 'hierarchy' );
}

/**
 * Get the label of the hierarchy screen's navigation item for a event.
 *
 * @since 1.0.0
 *
 * @return string Label to use on the hierarchy navigation item.
 */
function hgsz_get_hierarchy_nav_item_name() {
	// Check for a saved option for this string first.
	$name = sz_get_option( 'hgsz-event-tab-label' );
	// Next, allow translations to be applied.
	if ( empty( $name ) ) {
		$name = _x( 'Hierarchy %s', 'Label for event navigation tab. %s will be replaced with the number of child events.', 'hierarchical-events-for-sz' );
	}
	/*
	 * Apply the number of events indicator span.
	 * Don't run if we don't know the event ID.
	 */
	if ( $event_id = sz_get_current_event_id() ) {
		$name = sprintf( $name, '<span>' . number_format( hgsz_event_has_children( $event_id, sz_loggedin_user_id(), 'exclude_hidden' ) ) . '</span>' );
	}
	/**
	 * Filters the label of the hierarchy screen's navigation item for a event.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value    Label to use.
	 * @param int    $event_id ID of the current event.
	 */
	return apply_filters( 'hgsz_event_tab_label', $name, $event_id );
}

/**
 * Determine whether a event should be included in results sets for a
 * user in a specific context.
 *
 * @since 1.0.0
 *
 * @param object $event   SZ_Events_Event object to check.
 * @param int    $user_id ID of a user to check event visibility for.
 * @param string $context 'normal' filters hidden events only that the user doesn't belong to.
 *                        'activity' includes only events for which the user should see
 *                        the activity streams.
 *                        'exclude_hidden' filters all hidden events out (for directories).
 *
 * @return bool True if event meets context requirements.
 */
function hgsz_include_event_by_context( $event = false, $user_id = false, $context = 'normal' ) {
	$include = false;
	if ( ! isset( $event->id ) ) {
		return $include;
	}

	if ( current_user_can( 'sz_moderate' ) ) {
		$include = true;
	}

	/*
	 * 'exclude_hidden' is useful on directories, where hidden events
	 * are excluded by SZ.
	 */
	if ( 'exclude_hidden' == $context ) {
		if ( 'hidden' != $event->status ) {
			$include = true;
		}
	/*
	 * 'activity' includes only events for which the user can view the activity streams.
	 */
	} elseif ( 'activity' == $context ) {
		// For activity stream inclusion, require public status or membership.
		if ( 'public' == $event->status || events_is_user_member( $user_id, $event->id ) ) {
			$include = true;
		}
	/*
	 * 'myevents' is useful on user-specific directories, where only events the
	 * user belongs to are returned, and the event status is irrelevant.
	 */
	} elseif ( 'myevents' == $context ) {
		if ( events_is_user_member( $user_id, $event->id ) ) {
			$include = true;
		}
	} elseif ( 'normal' == $context ) {
		if ( 'hidden' != $event->status || events_is_user_member( $user_id, $event->id ) ) {
			$include = true;
		}
	}

	/**
	 * Filters whether this event should be included for this user and context combination.
	 *
	 * @since 1.0.0
	 *
	 * @param bool            $include Whether to include this event.
	 * @param SZ_Events_Event $event   The event object in question.
	 * @param int             $user_id ID of user to check.
	 * @param string          $user_id Current context.
	 */
	return apply_filters( 'hgsz_include_event_by_context', $include, $event, $user_id, $context );
}

/**
 * Create the hierarchical-style URL for a subevent: events/parent/child/action.
 *
 * @since 1.0.0
 *
 * @param  int   $event_id ID of the event.
 *
 * @return string Slug for event, empty if no slug found.
 */
function hgsz_build_hierarchical_slug( $event_id = 0 ) {

	if ( ! $event_id ) {
		$event_id = sz_get_current_event_id();
	}
	if ( ! $event_id ) {
		return '';
	}

	$event = events_get_event( $event_id );
	$path = array( sz_get_event_slug( $event ) );

	while ( $event->parent_id != 0 ) {
		$event  = events_get_event( $event->parent_id );
		$path[] = sz_get_event_slug( $event );
	}

	return implode( '/', array_reverse( $path ) );
}

/**
 * Should a event's activity stream include parent or child event activity?
 *
 * @since 1.0.0
 *
 * @param int $event_id Event to fetch setting for.
 *
 * @return string Setting to use.
 */
function hgsz_event_include_hierarchical_activity( $event_id = 0 ) {
	if ( ! $event_id ) {
		$event_id = sz_get_current_event_id();
	}
	$include = false;

	/*
	 * First, we check which setting has priority.
	 */
	$enforce = hgsz_get_global_activity_enforce_setting();
	if ( 'site-admins' == $enforce || 'event-admins' == $enforce ) {
		// Events can override, so check the event's raw setting first.
		$include = events_get_eventmeta( $event_id, 'hgsz-include-activity-from-relatives' );

		if ( $include ) {
			// Only run this if not empty. We want to pass empty values to the next check.
			$include = hgsz_sanitize_include_setting( $include );
		}
	}

	// If include hasn't yet been set, check the global setting.
	if ( ! $include ) {
		$include = hgsz_get_global_activity_setting();
	}

	/**
	 * Filters whether a event's activity stream should include parent or
	 * child event activity.
	 *
	 * @since 1.0.0
	 *
	 * @param string $include  Whether to include this event.
	 * @param int    $event_id ID of the event to check.
	 */
	return apply_filters( 'hgsz_event_include_hierarchical_activity', $include, $event_id );
}

/* Plugin settings management *************************************************/

/**
 * Fetch and parse the saved global settings.
 *
 * @since 1.0.0
 *
 * @return bool Which members of a event are allowed to associate subevents with it.
 */
function hgsz_get_directory_as_tree_setting() {
	return (bool) sz_get_option( 'hgsz-events-directory-show-tree' );
}

/**
 * Fetch and parse the saved global settings.
 *
 * @since 1.0.0
 *
 * @param int $event_id Which event ID's meta to fetch.
 *
 * @return string Which members of a event are allowed to associate subevents with it.
 */
function hgsz_get_allowed_subevent_creators( $event_id = 0 ) {
	if ( ! $event_id ) {
		$event_id = sz_get_current_event_id();
	}

	$value = events_get_eventmeta( $event_id, 'hgsz-allowed-subevent-creators' );

	return hgsz_sanitize_subevent_creators_setting( $value );
}

/**
 * Filter the syndication enforcement setting against a whitelist.
 *
 * @since 1.0.0
 *
 * @return string Level of enforcement for overriding the default settings.
 */
function hgsz_sanitize_subevent_creators_setting( $value = 'noone' ) {
	$valid = array( 'loggedin', 'member', 'mod', 'admin', 'noone' );
	if ( ! in_array( $value, $valid, true ) ) {
		$value = 'noone';
	}
	return $value;
}

/**
 * Fetch and parse the saved global settings.
 *
 * @since 1.0.0
 *
 * @return string|bool "yes" or "no" if it's set, false if unknown.
 */
function hgsz_get_global_activity_setting() {
	$option = sz_get_option( 'hgsz-include-activity-from-relatives', 'include-from-none' );
	return hgsz_sanitize_include_setting( $option );
}

/**
 * Fetch and parse the saved global settings.
 *
 * @since 1.0.0
 *
 * @return string Level of enforcement for overriding the default settings.
 */
function hgsz_get_global_activity_enforce_setting() {
	$option = sz_get_option( 'hgsz-include-activity-enforce', 'strict' );
	return hgsz_sanitize_include_setting_enforce( $option );
}

/**
 * Filter the syndication enforcement setting against a whitelist.
 *
 * @since 1.0.0
 *
 * @return string Level of enforcement for overriding the default settings.
 */
function hgsz_sanitize_include_setting( $value = 'include-from-none' ) {
	$valid = array(
		'include-from-parents',
		'include-from-children',
		'include-from-both',
		'include-from-none'
	);
	if ( ! in_array( $value, $valid, true ) ) {
		$value = 'include-from-none';
	}
	return $value;
}

/**
 * Filter the syndication enforcement setting against a whitelist.
 *
 * @since 1.0.0
 *
 * @return string Level of enforcement for overriding the default settings.
 */
function hgsz_sanitize_include_setting_enforce( $value = 'strict' ) {
	$valid = array(
		'event-admins',
		'site-admins',
		'strict'
	);
	if ( ! in_array( $value, $valid, true ) ) {
		$value = 'strict';
	}
	return $value;
}
