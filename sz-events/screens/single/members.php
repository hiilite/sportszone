<?php
/**
 * Events: Single event "Members" screen handler
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a event's Members page.
 *
 * @since 1.0.0
 */
function events_screen_event_members() {

	if ( !sz_is_single_item() )
		return false;

	$sz = sportszone();

	// Refresh the event member count meta.
	events_update_eventmeta( $sz->events->current_event->id, 'total_member_count', events_get_total_member_count( $sz->events->current_event->id ) );

	/**
	 * Fires before the loading of a event's Members page.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the event whose members are being displayed.
	 */
	do_action( 'events_screen_event_members', $sz->events->current_event->id );

	/**
	 * Filters the template to load for a event's Members page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a event's Members template.
	 */
	sz_core_load_template( apply_filters( 'events_template_event_members', 'events/single/home' ) );
}