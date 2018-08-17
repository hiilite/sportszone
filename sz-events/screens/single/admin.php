<?php
/**
 * Events: Single event "Manage" screen handler
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a event's Admin pages.
 *
 * @since 1.0.0
 */
function events_screen_event_admin() {
	if ( !sz_is_events_component() || !sz_is_current_action( 'admin' ) )
		return false;

	if ( sz_action_variables() )
		return false;

	sz_core_redirect( sz_get_event_permalink( events_get_current_event() ) . 'admin/edit-details/' );
}