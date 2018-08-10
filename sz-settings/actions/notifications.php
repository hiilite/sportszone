<?php
/**
 * Settings: Email notifications action handler
 *
 * @package SportsZone
 * @subpackage SettingsActions
 * @since 3.0.0
 */

/**
 * Handles the changing and saving of user notification settings.
 *
 * @since 1.6.0
 */
function sz_settings_action_notifications() {
	if ( ! sz_is_post_request() ) {
		return;
	}

	// Bail if no submit action.
	if ( ! isset( $_POST['submit'] ) ) {
		return;
	}

	// Bail if not in settings.
	if ( ! sz_is_settings_component() || ! sz_is_current_action( 'notifications' ) ) {
		return false;
	}

	// 404 if there are any additional action variables attached
	if ( sz_action_variables() ) {
		sz_do_404();
		return;
	}

	check_admin_referer( 'sz_settings_notifications' );

	sz_settings_update_notification_settings( sz_displayed_user_id(), (array) $_POST['notifications'] );

	// Switch feedback for super admins.
	if ( sz_is_my_profile() ) {
		sz_core_add_message( __( 'Your notification settings have been saved.',        'sportszone' ), 'success' );
	} else {
		sz_core_add_message( __( "This user's notification settings have been saved.", 'sportszone' ), 'success' );
	}

	/**
	 * Fires after the notification settings have been saved, and before redirect.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_core_notification_settings_after_save' );

	sz_core_redirect( sz_displayed_user_domain() . sz_get_settings_slug() . '/notifications/' );
}
add_action( 'sz_actions', 'sz_settings_action_notifications' );