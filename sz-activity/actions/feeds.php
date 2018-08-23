<?php
/**
 * Activity: RSS feed actions
 *
 * @package SportsZone
 * @subpackage ActivityActions
 * @since 3.0.0
 */

/**
 * Load the sitewide activity feed.
 *
 * @since 1.0.0
 *
 * @return bool False on failure.
 */
function sz_activity_action_sitewide_feed() {
	$sz = sportszone();

	if ( ! sz_is_activity_component() || ! sz_is_current_action( 'feed' ) || sz_is_user() || ! empty( $sz->groups->current_group ) || ! empty( $sz->events->current_event ) )
		return false;

	// Setup the feed.
	sportszone()->activity->feed = new SZ_Activity_Feed( array(
		'id'            => 'sitewide',

		/* translators: Sitewide activity RSS title - "[Site Name] | Site Wide Activity" */
		'title'         => sprintf( __( '%s | Site-Wide Activity', 'sportszone' ), sz_get_site_name() ),

		'link'          => sz_get_activity_directory_permalink(),
		'description'   => __( 'Activity feed for the entire site.', 'sportszone' ),
		'activity_args' => 'display_comments=threaded'
	) );
}
add_action( 'sz_actions', 'sz_activity_action_sitewide_feed' );

/**
 * Load a user's personal activity feed.
 *
 * @since 1.0.0
 *
 * @return bool False on failure.
 */
function sz_activity_action_personal_feed() {
	if ( ! sz_is_user_activity() || ! sz_is_current_action( 'feed' ) ) {
		return false;
	}

	// Setup the feed.
	sportszone()->activity->feed = new SZ_Activity_Feed( array(
		'id'            => 'personal',

		/* translators: Personal activity RSS title - "[Site Name] | [User Display Name] | Activity" */
		'title'         => sprintf( __( '%1$s | %2$s | Activity', 'sportszone' ), sz_get_site_name(), sz_get_displayed_user_fullname() ),

		'link'          => trailingslashit( sz_displayed_user_domain() . sz_get_activity_slug() ),
		'description'   => sprintf( __( 'Activity feed for %s.', 'sportszone' ), sz_get_displayed_user_fullname() ),
		'activity_args' => 'user_id=' . sz_displayed_user_id()
	) );
}
add_action( 'sz_actions', 'sz_activity_action_personal_feed' );

/**
 * Load a user's friends' activity feed.
 *
 * @since 1.0.0
 *
 * @return bool False on failure.
 */
function sz_activity_action_friends_feed() {
	if ( ! sz_is_active( 'friends' ) || ! sz_is_user_activity() || ! sz_is_current_action( sz_get_friends_slug() ) || ! sz_is_action_variable( 'feed', 0 ) ) {
		return false;
	}

	// Setup the feed.
	sportszone()->activity->feed = new SZ_Activity_Feed( array(
		'id'            => 'friends',

		/* translators: Friends activity RSS title - "[Site Name] | [User Display Name] | Friends Activity" */
		'title'         => sprintf( __( '%1$s | %2$s | Friends Activity', 'sportszone' ), sz_get_site_name(), sz_get_displayed_user_fullname() ),

		'link'          => trailingslashit( sz_displayed_user_domain() . sz_get_activity_slug() . '/' . sz_get_friends_slug() ),
		'description'   => sprintf( __( "Activity feed for %s's friends.", 'sportszone' ), sz_get_displayed_user_fullname() ),
		'activity_args' => 'scope=friends'
	) );
}
add_action( 'sz_actions', 'sz_activity_action_friends_feed' );

/**
 * Load the activity feed for a user's groups.
 *
 * @since 1.2.0
 *
 * @return bool False on failure.
 */
function sz_activity_action_my_groups_feed() {
	if ( ! sz_is_active( 'groups' ) || ! sz_is_user_activity() || ! sz_is_current_action( sz_get_groups_slug() ) || ! sz_is_action_variable( 'feed', 0 ) ) {
		return false;
	}

	// Get displayed user's group IDs.
	$groups    = groups_get_user_groups();
	$group_ids = implode( ',', $groups['groups'] );

	// Setup the feed.
	sportszone()->activity->feed = new SZ_Activity_Feed( array(
		'id'            => 'mygroups',

		/* translators: Member groups activity RSS title - "[Site Name] | [User Display Name] | Groups Activity" */
		'title'         => sprintf( __( '%1$s | %2$s | Group Activity', 'sportszone' ), sz_get_site_name(), sz_get_displayed_user_fullname() ),

		'link'          => trailingslashit( sz_displayed_user_domain() . sz_get_activity_slug() . '/' . sz_get_groups_slug() ),
		'description'   => sprintf( __( "Public group activity feed of which %s is a member.", 'sportszone' ), sz_get_displayed_user_fullname() ),
		'activity_args' => array(
			'object'           => sportszone()->groups->id,
			'primary_id'       => $group_ids,
			'display_comments' => 'threaded'
		)
	) );
}
add_action( 'sz_actions', 'sz_activity_action_my_groups_feed' );

/**
 * Load the activity feed for a user's events.
 *
 * @since 1.2.0
 *
 * @return bool False on failure.
 */
function sz_activity_action_my_events_feed() {
	if ( ! sz_is_active( 'events' ) || ! sz_is_user_activity() || ! sz_is_current_action( sz_get_events_slug() ) || ! sz_is_action_variable( 'feed', 0 ) ) {
		return false;
	}

	// Get displayed user's event IDs.
	$events    = events_get_user_events();
	$event_ids = implode( ',', $events['events'] );

	// Setup the feed.
	sportszone()->activity->feed = new SZ_Activity_Feed( array(
		'id'            => 'myevents',

		/* translators: Member events activity RSS title - "[Site Name] | [User Display Name] | Events Activity" */
		'title'         => sprintf( __( '%1$s | %2$s | Event Activity', 'sportszone' ), sz_get_site_name(), sz_get_displayed_user_fullname() ),

		'link'          => trailingslashit( sz_displayed_user_domain() . sz_get_activity_slug() . '/' . sz_get_events_slug() ),
		'description'   => sprintf( __( "Public event activity feed of which %s is a member.", 'sportszone' ), sz_get_displayed_user_fullname() ),
		'activity_args' => array(
			'object'           => sportszone()->events->id,
			'primary_id'       => $event_ids,
			'display_comments' => 'threaded'
		)
	) );
}
add_action( 'sz_actions', 'sz_activity_action_my_events_feed' );

/**
 * Load a user's @mentions feed.
 *
 * @since 1.2.0
 *
 * @return bool False on failure.
 */
function sz_activity_action_mentions_feed() {
	if ( ! sz_activity_do_mentions() ) {
		return false;
	}

	if ( !sz_is_user_activity() || ! sz_is_current_action( 'mentions' ) || ! sz_is_action_variable( 'feed', 0 ) ) {
		return false;
	}

	// Setup the feed.
	sportszone()->activity->feed = new SZ_Activity_Feed( array(
		'id'            => 'mentions',

		/* translators: User mentions activity RSS title - "[Site Name] | [User Display Name] | Mentions" */
		'title'         => sprintf( __( '%1$s | %2$s | Mentions', 'sportszone' ), sz_get_site_name(), sz_get_displayed_user_fullname() ),

		'link'          => sz_displayed_user_domain() . sz_get_activity_slug() . '/mentions/',
		'description'   => sprintf( __( "Activity feed mentioning %s.", 'sportszone' ), sz_get_displayed_user_fullname() ),
		'activity_args' => array(
			'search_terms' => '@' . sz_core_get_username( sz_displayed_user_id() )
		)
	) );
}
add_action( 'sz_actions', 'sz_activity_action_mentions_feed' );

/**
 * Load a user's favorites feed.
 *
 * @since 1.2.0
 *
 * @return bool False on failure.
 */
function sz_activity_action_favorites_feed() {
	if ( ! sz_is_user_activity() || ! sz_is_current_action( 'favorites' ) || ! sz_is_action_variable( 'feed', 0 ) ) {
		return false;
	}

	// Get displayed user's favorite activity IDs.
	$favs = sz_activity_get_user_favorites( sz_displayed_user_id() );
	$fav_ids = implode( ',', (array) $favs );

	// Setup the feed.
	sportszone()->activity->feed = new SZ_Activity_Feed( array(
		'id'            => 'favorites',

		/* translators: User activity favorites RSS title - "[Site Name] | [User Display Name] | Favorites" */
		'title'         => sprintf( __( '%1$s | %2$s | Favorites', 'sportszone' ), sz_get_site_name(), sz_get_displayed_user_fullname() ),

		'link'          => sz_displayed_user_domain() . sz_get_activity_slug() . '/favorites/',
		'description'   => sprintf( __( "Activity feed of %s's favorites.", 'sportszone' ), sz_get_displayed_user_fullname() ),
		'activity_args' => 'include=' . $fav_ids
	) );
}
add_action( 'sz_actions', 'sz_activity_action_favorites_feed' );
