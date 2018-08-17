<?php
/**
 * Events: Single event "Manage > Members" screen handler
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * This function handles actions related to member management on the event admin.
 *
 * @since 1.0.0
 */
function events_screen_event_admin_manage_members() {

	if ( 'manage-members' != sz_get_event_current_admin_tab() )
		return false;

	if ( ! sz_is_item_admin() )
		return false;

	$sz = sportszone();

	if ( sz_action_variable( 1 ) && sz_action_variable( 2 ) && sz_action_variable( 3 ) ) {
		if ( sz_is_action_variable( 'promote', 1 ) && ( sz_is_action_variable( 'mod', 2 ) || sz_is_action_variable( 'admin', 2 ) ) && is_numeric( sz_action_variable( 3 ) ) ) {
			$user_id = sz_action_variable( 3 );
			$status  = sz_action_variable( 2 );

			// Check the nonce first.
			if ( !check_admin_referer( 'events_promote_member' ) )
				return false;

			// Promote a user.
			if ( !events_promote_member( $user_id, $sz->events->current_event->id, $status ) )
				sz_core_add_message( __( 'There was an error when promoting that user. Please try again.', 'sportszone' ), 'error' );
			else
				sz_core_add_message( __( 'User promoted successfully', 'sportszone' ) );

			/**
			 * Fires before the redirect after a event member has been promoted.
			 *
			 * @since 1.0.0
			 *
			 * @param int $user_id ID of the user being promoted.
			 * @param int $id      ID of the event user is promoted within.
			 */
			do_action( 'events_promoted_member', $user_id, $sz->events->current_event->id );

			sz_core_redirect( sz_get_event_permalink( events_get_current_event() ) . 'admin/manage-members/' );
		}
	}

	if ( sz_action_variable( 1 ) && sz_action_variable( 2 ) ) {
		if ( sz_is_action_variable( 'demote', 1 ) && is_numeric( sz_action_variable( 2 ) ) ) {
			$user_id = sz_action_variable( 2 );

			// Check the nonce first.
			if ( !check_admin_referer( 'events_demote_member' ) )
				return false;

			// Stop sole admins from abandoning their event.
			$event_admins = events_get_event_admins( $sz->events->current_event->id );
			if ( 1 == count( $event_admins ) && $event_admins[0]->user_id == $user_id )
				sz_core_add_message( __( 'This event must have at least one admin', 'sportszone' ), 'error' );

			// Demote a user.
			elseif ( !events_demote_member( $user_id, $sz->events->current_event->id ) )
				sz_core_add_message( __( 'There was an error when demoting that user. Please try again.', 'sportszone' ), 'error' );
			else
				sz_core_add_message( __( 'User demoted successfully', 'sportszone' ) );

			/**
			 * Fires before the redirect after a event member has been demoted.
			 *
			 * @since 1.0.0
			 *
			 * @param int $user_id ID of the user being demoted.
			 * @param int $id      ID of the event user is demoted within.
			 */
			do_action( 'events_demoted_member', $user_id, $sz->events->current_event->id );

			sz_core_redirect( sz_get_event_permalink( events_get_current_event() ) . 'admin/manage-members/' );
		}

		if ( sz_is_action_variable( 'ban', 1 ) && is_numeric( sz_action_variable( 2 ) ) ) {
			$user_id = sz_action_variable( 2 );

			// Check the nonce first.
			if ( !check_admin_referer( 'events_ban_member' ) )
				return false;

			// Ban a user.
			if ( !events_ban_member( $user_id, $sz->events->current_event->id ) )
				sz_core_add_message( __( 'There was an error when banning that user. Please try again.', 'sportszone' ), 'error' );
			else
				sz_core_add_message( __( 'User banned successfully', 'sportszone' ) );

			/**
			 * Fires before the redirect after a event member has been banned.
			 *
			 * @since 1.0.0
			 *
			 * @param int $user_id ID of the user being banned.
			 * @param int $id      ID of the event user is banned from.
			 */
			do_action( 'events_banned_member', $user_id, $sz->events->current_event->id );

			sz_core_redirect( sz_get_event_permalink( events_get_current_event() ) . 'admin/manage-members/' );
		}

		if ( sz_is_action_variable( 'unban', 1 ) && is_numeric( sz_action_variable( 2 ) ) ) {
			$user_id = sz_action_variable( 2 );

			// Check the nonce first.
			if ( !check_admin_referer( 'events_unban_member' ) )
				return false;

			// Remove a ban for user.
			if ( !events_unban_member( $user_id, $sz->events->current_event->id ) )
				sz_core_add_message( __( 'There was an error when unbanning that user. Please try again.', 'sportszone' ), 'error' );
			else
				sz_core_add_message( __( 'User ban removed successfully', 'sportszone' ) );

			/**
			 * Fires before the redirect after a event member has been unbanned.
			 *
			 * @since 1.0.0
			 *
			 * @param int $user_id ID of the user being unbanned.
			 * @param int $id      ID of the event user is unbanned from.
			 */
			do_action( 'events_unbanned_member', $user_id, $sz->events->current_event->id );

			sz_core_redirect( sz_get_event_permalink( events_get_current_event() ) . 'admin/manage-members/' );
		}

		if ( sz_is_action_variable( 'remove', 1 ) && is_numeric( sz_action_variable( 2 ) ) ) {
			$user_id = sz_action_variable( 2 );

			// Check the nonce first.
			if ( !check_admin_referer( 'events_remove_member' ) )
				return false;

			// Remove a user.
			if ( !events_remove_member( $user_id, $sz->events->current_event->id ) )
				sz_core_add_message( __( 'There was an error removing that user from the event. Please try again.', 'sportszone' ), 'error' );
			else
				sz_core_add_message( __( 'User removed successfully', 'sportszone' ) );

			/**
			 * Fires before the redirect after a event member has been removed.
			 *
			 * @since 1.2.6
			 *
			 * @param int $user_id ID of the user being removed.
			 * @param int $id      ID of the event the user is removed from.
			 */
			do_action( 'events_removed_member', $user_id, $sz->events->current_event->id );

			sz_core_redirect( sz_get_event_permalink( events_get_current_event() ) . 'admin/manage-members/' );
		}
	}

	/**
	 * Fires before the loading of a event's manage members template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the event whose manage members page is being displayed.
	 */
	do_action( 'events_screen_event_admin_manage_members', $sz->events->current_event->id );

	/**
	 * Filters the template to load for a event's manage members page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a event's manage members template.
	 */
	sz_core_load_template( apply_filters( 'events_template_event_admin_manage_members', 'events/single/home' ) );
}
add_action( 'sz_screens', 'events_screen_event_admin_manage_members' );