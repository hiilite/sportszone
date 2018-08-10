<?php
/**
 * SportsZone Notifications Admin Bar functions.
 *
 * Admin Bar functions for the Notifications component.
 *
 * @package SportsZone
 * @subpackage NotificationsToolbar
 * @since 1.9.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Build the "Notifications" dropdown.
 *
 * @since 1.9.0
 *
 * @return bool
 */
function sz_notifications_toolbar_menu() {
	global $wp_admin_bar;

	if ( ! is_user_logged_in() ) {
		return false;
	}

	$notifications = sz_notifications_get_notifications_for_user( sz_loggedin_user_id(), 'object' );
	$count         = ! empty( $notifications ) ? count( $notifications ) : 0;
	$alert_class   = (int) $count > 0 ? 'pending-count alert' : 'count no-alert';
	$menu_title    = '<span id="ab-pending-notifications" class="' . $alert_class . '">' . number_format_i18n( $count ) . '</span>';
	$menu_link     = trailingslashit( sz_loggedin_user_domain() . sz_get_notifications_slug() );

	// Add the top-level Notifications button.
	$wp_admin_bar->add_menu( array(
		'parent'    => 'top-secondary',
		'id'        => 'sz-notifications',
		'title'     => $menu_title,
		'href'      => $menu_link,
	) );

	if ( ! empty( $notifications ) ) {
		foreach ( (array) $notifications as $notification ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'sz-notifications',
				'id'     => 'notification-' . $notification->id,
				'title'  => $notification->content,
				'href'   => $notification->href,
			) );
		}
	} else {
		$wp_admin_bar->add_menu( array(
			'parent' => 'sz-notifications',
			'id'     => 'no-notifications',
			'title'  => __( 'No new notifications', 'sportszone' ),
			'href'   => $menu_link,
		) );
	}

	return;
}
add_action( 'admin_bar_menu', 'sz_members_admin_bar_notifications_menu', 90 );
