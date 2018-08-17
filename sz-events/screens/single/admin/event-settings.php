<?php
/**
 * Events: Single event "Manage > Settings" screen handler
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a event's admin/event-settings page.
 *
 * @since 1.0.0
 */
function events_screen_event_admin_settings() {

	if ( 'event-settings' != sz_get_event_current_admin_tab() )
		return false;

	if ( ! sz_is_item_admin() )
		return false;

	$sz = sportszone();

	// If the edit form has been submitted, save the edited details.
	if ( isset( $_POST['save'] ) ) {
		$enable_forum   = ( isset($_POST['event-show-forum'] ) ) ? 1 : 0;

		// Checked against a whitelist for security.
		/** This filter is documented in sz-events/sz-events-admin.php */
		$allowed_status = apply_filters( 'events_allowed_status', array( 'public', 'private', 'hidden' ) );
		$status         = ( in_array( $_POST['event-status'], (array) $allowed_status ) ) ? $_POST['event-status'] : 'public';

		// Checked against a whitelist for security.
		/** This filter is documented in sz-events/sz-events-admin.php */
		$allowed_invite_status = apply_filters( 'events_allowed_invite_status', array( 'members', 'mods', 'admins' ) );
		$invite_status	       = isset( $_POST['event-invite-status'] ) && in_array( $_POST['event-invite-status'], (array) $allowed_invite_status ) ? $_POST['event-invite-status'] : 'members';

		// Check the nonce.
		if ( !check_admin_referer( 'events_edit_event_settings' ) )
			return false;

		/*
		 * Save event types.
		 *
		 * Ensure we keep types that have 'show_in_create_screen' set to false.
		 */
		$current_types = sz_events_get_event_type( sz_get_current_event_id(), false );
		$current_types = array_intersect( sz_events_get_event_types( array( 'show_in_create_screen' => false ) ), (array) $current_types );
		if ( isset( $_POST['event-types'] ) ) {
			$current_types = array_merge( $current_types, $_POST['event-types'] );

			// Set event types.
			sz_events_set_event_type( sz_get_current_event_id(), $current_types );

		// No event types checked, so this means we want to wipe out all event types.
		} else {
			/*
			 * Passing a blank string will wipe out all types for the event.
			 *
			 * Ensure we keep types that have 'show_in_create_screen' set to false.
			 */
			$current_types = empty( $current_types ) ? '' : $current_types;

			// Set event types.
			sz_events_set_event_type( sz_get_current_event_id(), $current_types );
		}

		if ( !events_edit_event_settings( $_POST['event-id'], $enable_forum, $status, $invite_status ) ) {
			sz_core_add_message( __( 'There was an error updating event settings. Please try again.', 'sportszone' ), 'error' );
		} else {
			sz_core_add_message( __( 'Event settings were successfully updated.', 'sportszone' ) );
		}

		/**
		 * Fires before the redirect if a event settings has been edited and saved.
		 *
		 * @since 1.0.0
		 *
		 * @param int $id ID of the event that was edited.
		 */
		do_action( 'events_event_settings_edited', $sz->events->current_event->id );

		sz_core_redirect( sz_get_event_permalink( events_get_current_event() ) . 'admin/event-settings/' );
	}

	/**
	 * Fires before the loading of the event admin/event-settings page template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the event that is being displayed.
	 */
	do_action( 'events_screen_event_admin_settings', $sz->events->current_event->id );

	/**
	 * Filters the template to load for a event's admin/event-settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a event's admin/event-settings template.
	 */
	sz_core_load_template( apply_filters( 'events_template_event_admin_settings', 'events/single/home' ) );
}
add_action( 'sz_screens', 'events_screen_event_admin_settings' );