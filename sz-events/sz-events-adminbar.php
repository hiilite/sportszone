<?php
/**
 * SportsZone Events Toolbar.
 *
 * Handles the events functions related to the WordPress Toolbar.
 *
 * @package SportsZone
 * @subpackage Events
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Add the Event Admin top-level menu when viewing event pages.
 *
 * @since 1.5.0
 *
 * @todo Add dynamic menu items for event extensions.
 *
 * @return false|null False if not on a event page, or if user does not have
 *                    access to event admin options.
 */
function sz_events_event_admin_menu() {
	global $wp_admin_bar;
	$sz = sportszone();

	// Only show if viewing a event.
	if ( ! sz_is_event() || sz_is_event_create() ) {
		return false;
	}

	// Only show this menu to event admins and super admins.
	if ( ! sz_current_user_can( 'sz_moderate' ) && ! sz_event_is_admin() ) {
		return false;
	}

	// Unique ID for the 'Edit Event' menu.
	$sz->event_admin_menu_id = 'event-admin';

	// Add the top-level Event Admin button.
	$wp_admin_bar->add_menu( array(
		'id'    => $sz->event_admin_menu_id,
		'title' => __( 'Edit Event', 'sportszone' ),
		'href'  => sz_get_event_permalink( $sz->events->current_event )
	) );

	// Index of the Manage tabs parent slug.
	$secondary_nav_items = $sz->events->nav->get_secondary( array( 'parent_slug' => $sz->events->current_event->slug . '_manage' ) );

	// Check if current event has Manage tabs.
	if ( ! $secondary_nav_items ) {
		return;
	}

	// Build the Event Admin menus.
	foreach ( $secondary_nav_items as $menu ) {
		/**
		 * Should we add the current manage link in the Event's "Edit" Admin Bar menu ?
		 *
		 * All core items will be added, plugins can use a new parameter in the BP Event Extension API
		 * to also add the link to the "edit screen" of their event component. To do so, set the
		 * the 'show_in_admin_bar' argument of your edit screen to true
		 */
		if ( $menu->show_in_admin_bar ) {
			$title = sprintf( _x( 'Edit Event %s', 'Event WP Admin Bar manage links', 'sportszone' ), $menu->name );

			// Title is specific for delete.
			if ( 'delete-event' == $menu->slug ) {
				$title = sprintf( _x( '%s Event', 'Event WP Admin Bar delete link', 'sportszone' ), $menu->name );
			}

			$wp_admin_bar->add_menu( array(
				'parent' => $sz->event_admin_menu_id,
				'id'     => $menu->slug,
				'title'  => $title,
				'href'   => sz_get_events_action_link( 'admin/' . $menu->slug )
			) );
		}
	}
}
add_action( 'admin_bar_menu', 'sz_events_event_admin_menu', 99 );

/**
 * Remove rogue WP core Edit menu when viewing a single event.
 *
 * @since 1.6.0
 */
function sz_events_remove_edit_page_menu() {
	if ( sz_is_event() ) {
		remove_action( 'admin_bar_menu', 'wp_admin_bar_edit_menu', 80 );
	}
}
add_action( 'add_admin_bar_menus', 'sz_events_remove_edit_page_menu' );
