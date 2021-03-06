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
				'slug'           => null,
				'description'    => $_POST['group-desc'],
				'notify_members' => $group_notify_members,
			) ) ) {
				sz_core_add_message( __( 'There was an error updating group details. Please try again.', 'sportszone' ), 'error' );
			} else {
				sz_core_add_message( __( 'Group details were successfully updated.', 'sportszone' ) );
			}
			
			/*
			 * Save group types.
			 *
			 * Ensure we keep types that have 'show_in_create_screen' set to false.
			 */
			$current_types = sz_groups_get_group_type( sz_get_current_group_id(), false );
			$current_types = array_intersect( sz_groups_get_group_types( array( 'show_in_create_screen' => true ) ), (array) $current_types );
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

			/*
			 * Save Additinal Info Fields
			 */
			$group_id = sz_get_current_group_id();
			
			if(sz_get_current_group_id()) {
				$group_id = sz_get_current_group_id();
			}
			elseif( isset( $_SESSION['new_group_id'] ) ) {
				$group_id = $_SESSION['new_group_id'];
			}
			
			// New set
			if ( isset( $_POST['sz_group_email'] ) ) {
				groups_update_groupmeta( $group_id, 'sz_group_email', sanitize_text_field($_POST['sz_group_email']) );
			}
			if ( isset( $_POST['sz_group_website'] ) ) {
				groups_update_groupmeta( $group_id, 'sz_group_website', sanitize_text_field($_POST['sz_group_website']) );
			}
			if ( isset( $_POST['sz_group_facebook'] ) ) {
				groups_update_groupmeta( $group_id, 'sz_group_facebook', sanitize_text_field($_POST['sz_group_facebook']) );
			}
			if ( isset( $_POST['sz_group_twitter'] ) ) {
				groups_update_groupmeta( $group_id, 'sz_group_twitter', sanitize_text_field($_POST['sz_group_twitter']) );
			}
			if ( isset( $_POST['sz_group_country'] ) ) {
				groups_update_groupmeta( $group_id, 'sz_group_country', $_POST['sz_group_country'] );
			}
			if ( isset( $_POST['sz_group_province'] ) ) {
				groups_update_groupmeta( $group_id, 'sz_group_province', $_POST['sz_group_province'] );
			}
			if ( isset( $_POST['sz_group_colors'] ) ) {
				groups_update_groupmeta( $group_id, 'sz_group_colors', $_POST['sz_group_colors'] );
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