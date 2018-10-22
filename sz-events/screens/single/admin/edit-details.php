<?php
/**
 * Events: Single event "Manage > Details" screen handler
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a event's admin/edit-details page.
 *
 * @since 1.0.0
 */
function events_screen_event_admin_edit_details() {

	if ( 'edit-details' != sz_get_event_current_admin_tab() )
		return false;

	if ( sz_is_item_admin() ) {

		$sz = sportszone();

		// If the edit form has been submitted, save the edited details.
		if ( isset( $_POST['save'] ) ) {
			// Check the nonce.
			if ( !check_admin_referer( 'events_edit_event_details' ) )
				return false;

			$event_notify_members = isset( $_POST['event-notify-members'] ) ? (int) $_POST['event-notify-members'] : 0;

			// Name and description are required and may not be empty.
			if ( empty( $_POST['event-name'] ) || empty( $_POST['event-desc'] ) ) {
				sz_core_add_message( __( 'Events must have a name and a description. Please try again.', 'sportszone' ), 'error' );
			} elseif ( ! events_edit_base_event_details( array(
				'event_id'       => $_POST['event-id'],
				'name'           => $_POST['event-name'],
				'slug'           => null, // @TODO: Add to settings pane? If yes, editable by site admin only, or allow event admins to do this?
				'description'    => $_POST['event-desc'],
				'notify_members' => $event_notify_members,
			) ) ) {
				sz_core_add_message( __( 'There was an error updating event details. Please try again.', 'sportszone' ), 'error' );
			} else {
				sz_core_add_message( __( 'Event details were successfully updated.', 'sportszone' ) );
			}
			
			/*
			 * Save event types.
			 *
			 * Ensure we keep types that have 'show_in_create_screen' set to false.
			 */
			$current_types = sz_events_get_event_type( sz_get_current_event_id(), false );
			$current_types = array_intersect( sz_events_get_event_types( array( 'show_in_create_screen' => true ) ), (array) $current_types );
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

			/*
			 * Save Additinal Info Fields
			 */
			 
			$event_id = sz_get_current_event_id();
			if ( isset( $_POST['event-club'] ) ) {
				events_update_eventmeta( $event_id, 'event-club', $_POST['event-club'] );
			}
			if ( isset( $_POST['event-main-team'] ) ) {
				events_update_eventmeta( $event_id, 'event-main-team', $_POST['event-main-team'] );
			}
			if ( isset( $_POST['sz_event_country'] ) ) {
				events_update_eventmeta( $event_id, 'sz_event_country', $_POST['sz_event_country'] );
			}
			if ( isset( $_POST['sz_event_province'] ) ) {
				events_update_eventmeta( $event_id, 'sz_event_province', $_POST['sz_event_province'] );
			}
			if ( isset( $_POST['sz_event_city'] ) ) {
				events_update_eventmeta( $event_id, 'sz_event_city', $_POST['sz_event_city'] );
			}
			if ( isset( $_POST['sz_rules_regulations'] ) ) {
				events_update_eventmeta( $event_id, 'sz_rules_regulations', $_POST['sz_rules_regulations'] );
			}
			
			/**
			 * Fires before the redirect if a event details has been edited and saved.
			 *
			 * @since 1.0.0
			 *
			 * @param int $id ID of the event that was edited.
			 */
			do_action( 'events_event_details_edited', $sz->events->current_event->id );

			sz_core_redirect( sz_get_event_permalink( events_get_current_event() ) . 'admin/edit-details/' );
		}

		/**
		 * Fires before the loading of the event admin/edit-details page template.
		 *
		 * @since 1.0.0
		 *
		 * @param int $id ID of the event that is being displayed.
		 */
		do_action( 'events_screen_event_admin_edit_details', $sz->events->current_event->id );

		/**
		 * Filters the template to load for a event's admin/edit-details page.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Path to a event's admin/edit-details template.
		 */
		sz_core_load_template( apply_filters( 'events_template_event_admin', 'events/single/home' ) );
	}
}
add_action( 'sz_screens', 'events_screen_event_admin_edit_details' );