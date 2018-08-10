<?php
/**
 * Deprecated functions.
 *
 * @deprecated 2.9.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Check whether bbPress plugin-powered Group Forums are enabled.
 *
 * @since 1.6.0
 * @since 3.0.0 $default argument's default value changed from true to false.
 * @deprecated 3.0.0 No longer used in core, but supported for third-party code.
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: false.
 * @return bool True if group forums are active, otherwise false.
 */
function sz_is_group_forums_active( $default = false ) {
	_deprecated_function( __FUNCTION__, '3.0', 'groups_get_group( $id )->enable_forum' );

	$is_active = function_exists( 'bsz_is_group_forums_active' ) ? bsz_is_group_forums_active( $default ) : $default;

	/**
	 * Filters whether or not bbPress plugin-powered Group Forums are enabled.
	 *
	 * @since 1.6.0
	 * @deprecated 3.0.0 No longer used in core, but supported for third-party code.
	 *
	 * @param bool $value Whether or not bbPress plugin-powered Group Forums are enabled.
	 */
	return (bool) apply_filters( 'sz_is_group_forums_active', $is_active );
}

/**
 * Is this a user's forums page?
 *
 * Eg http://example.com/members/joe/forums/ (or a subpage thereof).
 *
 * @since 1.5.0
 * @deprecated 3.0.0 No longer used in core, but supported for third-party code.
 *
 * @return false
 */
function sz_is_user_forums() {
	_deprecated_function( __FUNCTION__, '3.0', 'legacy forum support removed' );
	return false;
}

/**
 * Is the current page a group's (legacy bbPress) forum page?
 *
 * @since 1.1.0
 * @since 3.0.0 Always returns false.
 * @deprecated 3.0.0 No longer used in core, but supported for custom theme templates.
 *
 * @return bool
 */
function sz_is_group_forum() {
	_deprecated_function( __FUNCTION__, '3.0', 'legacy forum support removed' );
	return false;
}


/**
 * Output a 'New Topic' button for a group.
 *
 * @since 1.2.7
 * @deprecated 3.0.0 No longer used in core, but supported for third-party code.
 *
 * @param SZ_Groups_Group|bool $group The BP Groups_Group object if passed, boolean false if not passed.
 */
function sz_group_new_topic_button( $group = false ) {
	_deprecated_function( __FUNCTION__, '3.0', 'legacy forum support removed' );
}

	/**
	 * Return a 'New Topic' button for a group.
	 *
	 * @since 1.2.7
	 * @deprecated 3.0.0 No longer used in core, but supported for third-party code.
	 *
	 * @param SZ_Groups_Group|bool $group The BP Groups_Group object if passed, boolean false if not passed.
	 *
	 * @return false
	 */
	function sz_get_group_new_topic_button( $group = false ) {
		_deprecated_function( __FUNCTION__, '3.0', 'legacy forum support removed' );
		return false;
	}

/**
 * Catch a "Mark as Spammer/Not Spammer" click from the toolbar.
 *
 * When a site admin selects "Mark as Spammer/Not Spammer" from the admin menu
 * this action will fire and mark or unmark the user and their blogs as spam.
 * Must be a site admin for this function to run.
 *
 * Note: no longer used in the current state. See the Settings component.
 *
 * @since 1.1.0
 * @since 1.6.0 No longer used, unhooked.
 * @since 3.0.0 Formally marked as deprecated.
 *
 * @param int $user_id Optional. User ID to mark as spam. Defaults to displayed user.
 */
function sz_core_action_set_spammer_status( $user_id = 0 ) {
	_deprecated_function( __FUNCTION__, '3.0' );

	// Only super admins can currently spam users (but they can't spam
	// themselves).
	if ( ! is_super_admin() || sz_is_my_profile() ) {
		return;
	}

	// Use displayed user if it's not yourself.
	if ( empty( $user_id ) )
		$user_id = sz_displayed_user_id();

	if ( sz_is_current_component( 'admin' ) && ( in_array( sz_current_action(), array( 'mark-spammer', 'unmark-spammer' ) ) ) ) {

		// Check the nonce.
		check_admin_referer( 'mark-unmark-spammer' );

		// To spam or not to spam.
		$status = sz_is_current_action( 'mark-spammer' ) ? 'spam' : 'ham';

		// The heavy lifting.
		sz_core_process_spammer_status( $user_id, $status );

		// Add feedback message. @todo - Error reporting.
		if ( 'spam' == $status ) {
			sz_core_add_message( __( 'User marked as spammer. Spam users are visible only to site admins.', 'sportszone' ) );
		} else {
			sz_core_add_message( __( 'User removed as spammer.', 'sportszone' ) );
		}

		// Deprecated. Use sz_core_process_spammer_status.
		$is_spam = 'spam' == $status;
		do_action( 'sz_core_action_set_spammer_status', sz_displayed_user_id(), $is_spam );

		// Redirect back to where we came from.
		sz_core_redirect( wp_get_referer() );
	}
}

/**
 * Process user deletion requests.
 *
 * Note: no longer used in the current state. See the Settings component.
 *
 * @since 1.1.0
 * @since 1.6.0 No longer used, unhooked.
 * @since 3.0.0 Formally marked as deprecated.
 */
function sz_core_action_delete_user() {
	_deprecated_function( __FUNCTION__, '3.0' );

	if ( !sz_current_user_can( 'sz_moderate' ) || sz_is_my_profile() || !sz_displayed_user_id() )
		return false;

	if ( sz_is_current_component( 'admin' ) && sz_is_current_action( 'delete-user' ) ) {

		// Check the nonce.
		check_admin_referer( 'delete-user' );

		$errors = false;
		do_action( 'sz_core_before_action_delete_user', $errors );

		if ( sz_core_delete_account( sz_displayed_user_id() ) ) {
			sz_core_add_message( sprintf( __( '%s has been deleted from the system.', 'sportszone' ), sz_get_displayed_user_fullname() ) );
		} else {
			sz_core_add_message( sprintf( __( 'There was an error deleting %s from the system. Please try again.', 'sportszone' ), sz_get_displayed_user_fullname() ), 'error' );
			$errors = true;
		}

		do_action( 'sz_core_action_delete_user', $errors );

		if ( $errors )
			sz_core_redirect( sz_displayed_user_domain() );
		else
			sz_core_redirect( sz_loggedin_user_domain() );
	}
}
