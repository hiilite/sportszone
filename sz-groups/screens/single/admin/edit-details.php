<?php
/**
 * Groups: Single group "Manage > Details" screen handler
 *
 * @package SportsZone
 * @subpackage GroupsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a group's admin/edit-details page.
 *
 * @since 1.0.0
 */
function groups_screen_group_admin_edit_details() {

	if ( 'edit-details' != sz_get_group_current_admin_tab() )
		return false;

	if ( sz_is_item_admin() ) {

		$sz = sportszone();

		// If the edit form has been submitted, save the edited details.
		if ( isset( $_POST['save'] ) ) {
			// Check the nonce.
			if ( !check_admin_referer( 'groups_edit_group_details' ) )
				return false;

			$group_notify_members = isset( $_POST['group-notify-members'] ) ? (int) $_POST['group-notify-members'] : 0;

			// Name and description are required and may not be empty.
			if ( empty( $_POST['group-name'] ) || empty( $_POST['group-desc'] ) ) {
				sz_core_add_message( __( 'Groups must have a name and a description. Please try again.', 'sportszone' ), 'error' );
			} elseif ( ! groups_edit_base_group_details( array(
				'group_id'       => $_POST['group-id'],
				'name'           => $_POST['group-name'],
				'slug'           => null, // @TODO: Add to settings pane? If yes, editable by site admin only, or allow group admins to do this?
				'description'    => $_POST['group-desc'],
				'notify_members' => $group_notify_members,
			) ) ) {
				sz_core_add_message( __( 'There was an error updating group details. Please try again.', 'sportszone' ), 'error' );
			} else {
				sz_core_add_message( __( 'Group details were successfully updated.', 'sportszone' ) );
			}

			/**
			 * Fires before the redirect if a group details has been edited and saved.
			 *
			 * @since 1.0.0
			 *
			 * @param int $id ID of the group that was edited.
			 */
			do_action( 'groups_group_details_edited', $sz->groups->current_group->id );

			sz_core_redirect( sz_get_group_permalink( groups_get_current_group() ) . 'admin/edit-details/' );
		}

		/**
		 * Fires before the loading of the group admin/edit-details page template.
		 *
		 * @since 1.0.0
		 *
		 * @param int $id ID of the group that is being displayed.
		 */
		do_action( 'groups_screen_group_admin_edit_details', $sz->groups->current_group->id );

		/**
		 * Filters the template to load for a group's admin/edit-details page.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Path to a group's admin/edit-details template.
		 */
		sz_core_load_template( apply_filters( 'groups_template_group_admin', 'groups/single/home' ) );
	}
}
add_action( 'sz_screens', 'groups_screen_group_admin_edit_details' );