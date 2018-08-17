<?php
/**
 * Events: Single event "Manage > Delete" screen handler
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of the Delete Event page.
 *
 * @since 1.0.0
 */
function events_screen_event_admin_delete_event() {

	if ( 'delete-event' != sz_get_event_current_admin_tab() )
		return false;

	if ( ! sz_is_item_admin() && !sz_current_user_can( 'sz_moderate' ) )
		return false;

	$sz = sportszone();

	if ( isset( $_REQUEST['delete-event-button'] ) && isset( $_REQUEST['delete-event-understand'] ) ) {

		// Check the nonce first.
		if ( !check_admin_referer( 'events_delete_event' ) ) {
			return false;
		}

		/**
		 * Fires before the deletion of a event from the Delete Event page.
		 *
		 * @since 1.5.0
		 *
		 * @param int $id ID of the event being deleted.
		 */
		do_action( 'events_before_event_deleted', $sz->events->current_event->id );

		// Event admin has deleted the event, now do it.
		if ( !events_delete_event( $sz->events->current_event->id ) ) {
			sz_core_add_message( __( 'There was an error deleting the event. Please try again.', 'sportszone' ), 'error' );
		} else {
			sz_core_add_message( __( 'The event was deleted successfully.', 'sportszone' ) );

			/**
			 * Fires after the deletion of a event from the Delete Event page.
			 *
			 * @since 1.0.0
			 *
			 * @param int $id ID of the event being deleted.
			 */
			do_action( 'events_event_deleted', $sz->events->current_event->id );

			sz_core_redirect( trailingslashit( sz_loggedin_user_domain() . sz_get_events_slug() ) );
		}

		sz_core_redirect( trailingslashit( sz_loggedin_user_domain() . sz_get_events_slug() ) );
	}

	/**
	 * Fires before the loading of the Delete Event page template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the event that is being displayed.
	 */
	do_action( 'events_screen_event_admin_delete_event', $sz->events->current_event->id );

	/**
	 * Filters the template to load for the Delete Event page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to the Delete Event template.
	 */
	sz_core_load_template( apply_filters( 'events_template_event_admin_delete_event', 'events/single/home' ) );
}
add_action( 'sz_screens', 'events_screen_event_admin_delete_event' );