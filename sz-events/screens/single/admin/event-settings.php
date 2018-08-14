<?php
/**
 * Groups: Single group "Manage > Settings" screen handler
 *
 * @package SportsZone
 * @subpackage GroupsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a group's admin/group-settings page.
 *
 * @since 1.0.0
 */
function groups_screen_group_admin_settings() {

	if ( 'group-settings' != sz_get_group_current_admin_tab() )
		return false;

	if ( ! sz_is_item_admin() )
		return false;

	$sz = sportszone();

	// If the edit form has been submitted, save the edited details.
	if ( isset( $_POST['save'] ) ) {
		$enable_forum   = ( isset($_POST['group-show-forum'] ) ) ? 1 : 0;

		// Checked against a whitelist for security.
		/** This filter is documented in sz-groups/sz-groups-admin.php */
		$allowed_status = apply_filters( 'groups_allowed_status', array( 'public', 'private', 'hidden' ) );
		$status         = ( in_array( $_POST['group-status'], (array) $allowed_status ) ) ? $_POST['group-status'] : 'public';

		// Checked against a whitelist for security.
		/** This filter is documented in sz-groups/sz-groups-admin.php */
		$allowed_invite_status = apply_filters( 'groups_allowed_invite_status', array( 'members', 'mods', 'admins' ) );
		$invite_status	       = isset( $_POST['group-invite-status'] ) && in_array( $_POST['group-invite-status'], (array) $allowed_invite_status ) ? $_POST['group-invite-status'] : 'members';

		// Check the nonce.
		if ( !check_admin_referer( 'groups_edit_group_settings' ) )
			return false;

		/*
		 * Save group types.
		 *
		 * Ensure we keep types that have 'show_in_create_screen' set to false.
		 */
		$current_types = sz_groups_get_group_type( sz_get_current_group_id(), false );
		$current_types = array_intersect( sz_groups_get_group_types( array( 'show_in_create_screen' => false ) ), (array) $current_types );
		if ( isset( $_POST['group-types'] ) ) {
			$current_types = array_merge( $current_types, $_POST['group-types'] );

			// Set group types.
			sz_groups_set_group_type( sz_get_current_group_id(), $current_types );

		// No group types checked, so this means we want to wipe out all group types.
		} else {
			/*
			 * Passing a blank string will wipe out all types for the group.
			 *
			 * Ensure we keep types that have 'show_in_create_screen' set to false.
			 */
			$current_types = empty( $current_types ) ? '' : $current_types;

			// Set group types.
			sz_groups_set_group_type( sz_get_current_group_id(), $current_types );
		}

		if ( !groups_edit_group_settings( $_POST['group-id'], $enable_forum, $status, $invite_status ) ) {
			sz_core_add_message( __( 'There was an error updating group settings. Please try again.', 'sportszone' ), 'error' );
		} else {
			sz_core_add_message( __( 'Group settings were successfully updated.', 'sportszone' ) );
		}

		/**
		 * Fires before the redirect if a group settings has been edited and saved.
		 *
		 * @since 1.0.0
		 *
		 * @param int $id ID of the group that was edited.
		 */
		do_action( 'groups_group_settings_edited', $sz->groups->current_group->id );

		sz_core_redirect( sz_get_group_permalink( groups_get_current_group() ) . 'admin/group-settings/' );
	}

	/**
	 * Fires before the loading of the group admin/group-settings page template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the group that is being displayed.
	 */
	do_action( 'groups_screen_group_admin_settings', $sz->groups->current_group->id );

	/**
	 * Filters the template to load for a group's admin/group-settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a group's admin/group-settings template.
	 */
	sz_core_load_template( apply_filters( 'groups_template_group_admin_settings', 'groups/single/home' ) );
}
add_action( 'sz_screens', 'groups_screen_group_admin_settings' );