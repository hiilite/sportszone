<?php
/**
 * Settings: Account deletion action handler
 *
 * @package SportsZone
 * @subpackage SettingsActions
 * @since 3.0.0
 */

/**
 * Handles the deleting of a user.
 *
 * @since 1.6.0
 */
function sz_settings_action_delete_account() {
	if ( ! sz_is_post_request() ) {
		return;
	}

	// Bail if no submit action.
	if ( ! isset( $_POST['delete-account-understand'] ) ) {
		return;
	}

	// Bail if not in settings.
	if ( ! sz_is_settings_component() || ! sz_is_current_action( 'delete-account' ) ) {
		return false;
	}

	// 404 if there are any additional action variables attached
	if ( sz_action_variables() ) {
		sz_do_404();
		return;
	}

	// Bail if account deletion is disabled.
	if ( sz_disable_account_deletion() && ! sz_current_user_can( 'delete_users' ) ) {
		return false;
	}

	// Nonce check.
	check_admin_referer( 'delete-account' );

	// Get username now because it might be gone soon!
	$username = sz_get_displayed_user_fullname();

	// Delete the users account.
	if ( sz_core_delete_account( sz_displayed_user_id() ) ) {

		// Add feedback after deleting a user.
		sz_core_add_message( sprintf( __( '%s was successfully deleted.', 'sportszone' ), $username ), 'success' );

		// Redirect to the root domain.
		sz_core_redirect( sz_get_root_domain() );
	}
}
add_action( 'sz_actions', 'sz_settings_action_delete_account' );