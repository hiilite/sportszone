<?php
/**
 * SportsZone Events Caching.
 *
 * Caching functions handle the clearing of cached objects and pages on specific
 * actions throughout SportsZone.
 *
 * @package SportsZone
 * @subpackage Events
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Slurp up metadata for a set of events.
 *
 * This function is called in two places in the SZ_Events_Event class:
 *   - in the populate() method, when single event objects are populated
 *   - in the get() method, when multiple events are queried
 *
 * It grabs all eventmeta associated with all of the events passed in
 * $event_ids and adds it to WP cache. This improves efficiency when using
 * eventmeta within a loop context.
 *
 * @since 1.6.0
 *
 * @param int|string|array|bool $event_ids Accepts a single event_id, or a
 *                                         comma-separated list or array of
 *                                         event ids.
 */
function sz_events_update_meta_cache( $event_ids = false ) {
	$sz = sportszone();

	$cache_args = array(
		'object_ids' 	   => $event_ids,
		'object_type' 	   => $sz->events->id,
		'cache_event'      => 'event_meta',
		'object_column'    => 'event_id',
		'meta_table' 	   => $sz->events->table_name_eventmeta,
		'cache_key_prefix' => 'sz_events_eventmeta'
	);

	sz_update_meta_cache( $cache_args );
}

/**
 * Clear the cached event count.
 *
 * @since 1.0.0
 *
 * @param int $event_id Not used.
 */
function events_clear_event_object_cache( $event_id ) {
	wp_cache_delete( 'sz_total_event_count', 'sz' );
}
add_action( 'events_event_deleted',              'events_clear_event_object_cache' );
add_action( 'events_settings_updated',           'events_clear_event_object_cache' );
add_action( 'events_details_updated',            'events_clear_event_object_cache' );
add_action( 'events_event_avatar_updated',       'events_clear_event_object_cache' );
add_action( 'events_create_event_step_complete', 'events_clear_event_object_cache' );

/**
 * Bust event caches when editing or deleting.
 *
 * @since 1.7.0
 *
 * @param int $event_id The event being edited.
 */
function sz_events_delete_event_cache( $event_id = 0 ) {
	wp_cache_delete( $event_id, 'sz_events' );
}
add_action( 'events_delete_event',     'sz_events_delete_event_cache' );
add_action( 'events_update_event',     'sz_events_delete_event_cache' );
add_action( 'events_details_updated',  'sz_events_delete_event_cache' );
add_action( 'events_settings_updated', 'sz_events_delete_event_cache' );

/**
 * Bust event cache when modifying metadata.
 *
 * @since 2.0.0
 *
 * @param int $meta_id Meta ID.
 * @param int $event_id Event ID.
 */
function sz_events_delete_event_cache_on_metadata_change( $meta_id, $event_id ) {
	wp_cache_delete( $event_id, 'sz_events' );
}
add_action( 'updated_event_meta', 'sz_events_delete_event_cache_on_metadata_change', 10, 2 );
add_action( 'added_event_meta', 'sz_events_delete_event_cache_on_metadata_change', 10, 2 );

/**
 * Clear caches for the event creator when a event is created.
 *
 * @since 1.6.0
 *
 * @param int             $event_id  ID of the event.
 * @param SZ_Events_Event $event_obj Event object.
 */
function sz_events_clear_event_creator_cache( $event_id, $event_obj ) {
	// Clears the 'total events' for this user.
	events_clear_event_user_object_cache( $event_obj->id, $event_obj->creator_id );
}
add_action( 'events_created_event', 'sz_events_clear_event_creator_cache', 10, 2 );

/**
 * Clears caches for all members in a event when a event is deleted.
 *
 * @since 1.6.0
 *
 * @param SZ_Events_Event $event_obj Event object.
 * @param array           $user_ids  User IDs who were in this event.
 */
function sz_events_clear_event_members_caches( $event_obj, $user_ids ) {
	// Clears the 'total events' cache for each member in a event.
	foreach ( (array) $user_ids as $user_id )
		events_clear_event_user_object_cache( $event_obj->id, $user_id );
}
add_action( 'sz_events_delete_event', 'sz_events_clear_event_members_caches', 10, 2 );

/**
 * Clear a user's cached total event invite count.
 *
 * Count is cleared when an invite is accepted, rejected or deleted.
 *
 * @since 2.0.0
 *
 * @param int $user_id The user ID.
 */
function sz_events_clear_invite_count_for_user( $user_id ) {
	wp_cache_delete( $user_id, 'sz_event_invite_count' );
}
add_action( 'events_accept_invite', 'sz_events_clear_invite_count_for_user' );
add_action( 'events_reject_invite', 'sz_events_clear_invite_count_for_user' );
add_action( 'events_delete_invite', 'sz_events_clear_invite_count_for_user' );

/**
 * Clear a user's cached total event invite count when a user is uninvited.
 *
 * Groan. Our API functions are not consistent.
 *
 * @since 2.0.0
 *
 * @param int $event_id The event ID. Not used in this function.
 * @param int $user_id  The user ID.
 */
function sz_events_clear_invite_count_on_uninvite( $event_id, $user_id ) {
	sz_events_clear_invite_count_for_user( $user_id );
}
add_action( 'events_uninvite_user', 'sz_events_clear_invite_count_on_uninvite', 10, 2 );

/**
 * Clear a user's cached total event invite count when a new invite is sent.
 *
 * @since 2.0.0
 *
 * @param int   $event_id      The event ID. Not used in this function.
 * @param array $invited_users Array of invited user IDs.
 */
function sz_events_clear_invite_count_on_send( $event_id, $invited_users ) {
	foreach ( $invited_users as $user_id ) {
		sz_events_clear_invite_count_for_user( $user_id );
	}
}
add_action( 'events_send_invites', 'sz_events_clear_invite_count_on_send', 10, 2 );

/**
 * Clear a user's cached event count.
 *
 * @since 1.2.0
 *
 * @param int $event_id The event ID. Not used in this function.
 * @param int $user_id  The user ID.
 */
function events_clear_event_user_object_cache( $event_id, $user_id ) {
	wp_cache_delete( 'sz_total_events_for_user_' . $user_id, 'sz' );
}
add_action( 'events_join_event',    'events_clear_event_user_object_cache', 10, 2 );
add_action( 'events_leave_event',   'events_clear_event_user_object_cache', 10, 2 );
add_action( 'events_ban_member',    'events_clear_event_user_object_cache', 10, 2 );
add_action( 'events_unban_member',  'events_clear_event_user_object_cache', 10, 2 );
add_action( 'events_uninvite_user', 'events_clear_event_user_object_cache', 10, 2 );
add_action( 'events_remove_member', 'events_clear_event_user_object_cache', 10, 2 );

/**
 * Clear event administrator and moderator cache.
 *
 * @since 2.1.0
 *
 * @param int $event_id The event ID.
 */
function events_clear_event_administrator_cache( $event_id ) {
	wp_cache_delete( $event_id, 'sz_event_admins' );
	wp_cache_delete( $event_id, 'sz_event_mods' );
}
add_action( 'events_promote_member', 'events_clear_event_administrator_cache' );
add_action( 'events_demote_member',  'events_clear_event_administrator_cache' );
add_action( 'events_delete_event',   'events_clear_event_administrator_cache' );

/**
 * Clear event administrator and moderator cache when a event member is saved.
 *
 * This accounts for situations where event admins or mods are added manually
 * using {@link SZ_Events_Member::save()}.  Usually via a plugin.
 *
 * @since 2.1.0
 *
 * @param SZ_Events_Member $member Member object.
 */
function events_clear_event_administrator_cache_on_member_save( SZ_Events_Member $member ) {
	events_clear_event_administrator_cache( $member->event_id );
}
add_action( 'events_member_after_save', 'events_clear_event_administrator_cache_on_member_save' );

/**
 * Clear the event type cache for a event.
 *
 * Called when event is deleted.
 *
 * @since 2.6.0
 *
 * @param int $event_id The event ID.
 */
function events_clear_event_type_cache( $event_id = 0 ) {
	wp_cache_delete( $event_id, 'sz_events_event_type' );
}
add_action( 'events_delete_event', 'events_clear_event_type_cache' );

/**
 * Clear caches on membership save.
 *
 * @since 2.6.0
 *
 * @param SZ_Events_Member $member BP Events Member instance.
 */
function sz_events_clear_user_event_cache_on_membership_save( SZ_Events_Member $member ) {
	wp_cache_delete( $member->user_id, 'sz_events_memberships_for_user' );
	wp_cache_delete( $member->id, 'sz_events_memberships' );
}
add_action( 'events_member_before_save', 'sz_events_clear_user_event_cache_on_membership_save' );
add_action( 'events_member_before_remove', 'sz_events_clear_user_event_cache_on_membership_save' );

/**
 * Clear event memberships cache on miscellaneous actions not covered by the 'after_save' hook.
 *
 * @since 2.6.0
 *
 * @param int $user_id  Current user ID.
 * @param int $event_id Current event ID.
 */
function sz_events_clear_user_event_cache_on_other_events( $user_id, $event_id ) {
	wp_cache_delete( $user_id, 'sz_events_memberships_for_user' );

	$membership = new SZ_Events_Member( $user_id, $event_id );
	wp_cache_delete( $membership->id, 'sz_events_memberships' );
}
add_action( 'sz_events_member_before_delete', 'sz_events_clear_user_event_cache_on_other_events', 10, 2 );
add_action( 'sz_events_member_before_delete_invite', 'sz_events_clear_user_event_cache_on_other_events', 10, 2 );
add_action( 'events_accept_invite', 'sz_events_clear_user_event_cache_on_other_events', 10, 2 );

/**
 * Reset cache incrementor for the Events component.
 *
 * This function invalidates all cached results of event queries,
 * whenever one of the following events takes place:
 *   - A event is created or updated.
 *   - A event is deleted.
 *   - A event's metadata is modified.
 *
 * @since 2.7.0
 *
 * @return bool True on success, false on failure.
 */
function sz_events_reset_cache_incrementor() {
	return sz_core_reset_incrementor( 'sz_events' );
}
add_action( 'events_event_after_save', 'sz_events_reset_cache_incrementor' );
add_action( 'sz_events_delete_event',  'sz_events_reset_cache_incrementor' );
add_action( 'updated_event_meta',      'sz_events_reset_cache_incrementor' );
add_action( 'deleted_event_meta',      'sz_events_reset_cache_incrementor' );
add_action( 'added_event_meta',        'sz_events_reset_cache_incrementor' );

/**
 * Reset cache incrementor for Events component when a event's taxonomy terms change.
 *
 * We infer that a event is being affected by looking at the objects belonging
 * to the taxonomy being affected.
 *
 * @since 2.7.0
 *
 * @param int    $object_id ID of the item whose terms are being modified.
 * @param array  $terms     Array of object terms.
 * @param array  $tt_ids    Array of term taxonomy IDs.
 * @param string $taxonomy  Taxonomy slug.
 * @return bool True on success, false on failure.
 */
function sz_events_reset_cache_incrementor_on_event_term_change( $object_id, $terms, $tt_ids, $taxonomy ) {
	$tax_object = get_taxonomy( $taxonomy );
	if ( $tax_object && in_array( 'sz_event', $tax_object->object_type, true ) ) {
		return sz_events_reset_cache_incrementor();
	}

	return false;
}
add_action( 'sz_set_object_terms', 'sz_events_reset_cache_incrementor_on_event_term_change', 10, 4 );

/**
 * Reset cache incrementor for Events component when a event's taxonomy terms are removed.
 *
 * We infer that a event is being affected by looking at the objects belonging
 * to the taxonomy being affected.
 *
 * @since 2.7.0
 *
 * @param int    $object_id ID of the item whose terms are being modified.
 * @param array  $terms     Array of object terms.
 * @param string $taxonomy  Taxonomy slug.
 * @return bool True on success, false on failure.
 */
function sz_events_reset_cache_incrementor_on_event_term_remove( $object_id, $terms, $taxonomy ) {
	$tax_object = get_taxonomy( $taxonomy );
	if ( $tax_object && in_array( 'sz_event', $tax_object->object_type, true ) ) {
		return sz_events_reset_cache_incrementor();
	}

	return false;
}
add_action( 'sz_remove_object_terms', 'sz_events_reset_cache_incrementor_on_event_term_remove', 10, 3 );

/* List actions to clear super cached pages on, if super cache is installed */
add_action( 'events_join_event',                 'sz_core_clear_cache' );
add_action( 'events_leave_event',                'sz_core_clear_cache' );
add_action( 'events_accept_invite',              'sz_core_clear_cache' );
add_action( 'events_reject_invite',              'sz_core_clear_cache' );
add_action( 'events_invite_user',                'sz_core_clear_cache' );
add_action( 'events_uninvite_user',              'sz_core_clear_cache' );
add_action( 'events_details_updated',            'sz_core_clear_cache' );
add_action( 'events_settings_updated',           'sz_core_clear_cache' );
add_action( 'events_unban_member',               'sz_core_clear_cache' );
add_action( 'events_ban_member',                 'sz_core_clear_cache' );
add_action( 'events_demote_member',              'sz_core_clear_cache' );
add_action( 'events_promote_member',             'sz_core_clear_cache' );
add_action( 'events_membership_rejected',        'sz_core_clear_cache' );
add_action( 'events_membership_accepted',        'sz_core_clear_cache' );
add_action( 'events_membership_requested',       'sz_core_clear_cache' );
add_action( 'events_create_event_step_complete', 'sz_core_clear_cache' );
add_action( 'events_created_event',              'sz_core_clear_cache' );
add_action( 'events_event_avatar_updated',       'sz_core_clear_cache' );
