<?php
/**
 * Groups: Single group "Manage > Photo" screen handler
 *
 * @package SportsZone
 * @subpackage GroupsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a group's Change Avatar page.
 *
 * @since 1.0.0
 */
function groups_screen_group_admin_avatar() {

	if ( 'group-avatar' != sz_get_group_current_admin_tab() )
		return false;

	// If the logged-in user doesn't have permission or if avatar uploads are disabled, then stop here.
	if ( ! sz_is_item_admin() || sz_disable_group_avatar_uploads() || ! sportszone()->avatar->show_avatars )
		return false;

	$sz = sportszone();

	// If the group admin has deleted the admin avatar.
	if ( sz_is_action_variable( 'delete', 1 ) ) {

		// Check the nonce.
		check_admin_referer( 'sz_group_avatar_delete' );

		if ( sz_core_delete_existing_avatar( array( 'item_id' => $sz->groups->current_group->id, 'object' => 'group' ) ) ) {
			sz_core_add_message( __( 'The group profile photo was deleted successfully!', 'sportszone' ) );
		} else {
			sz_core_add_message( __( 'There was a problem deleting the group profile photo. Please try again.', 'sportszone' ), 'error' );
		}
	}

	if ( ! isset( $sz->avatar_admin ) ) {
		$sz->avatar_admin = new stdClass();
	}

	$sz->avatar_admin->step = 'upload-image';
	
	if ( !empty( $_FILES ) ) {

		// Check the nonce.
		check_admin_referer( 'sz_avatar_upload' );

		// Pass the file to the avatar upload handler.
		if ( sz_core_avatar_handle_upload( $_FILES, 'groups_avatar_upload_dir' ) ) {
			$sz->avatar_admin->step = 'crop-image';

			// Make sure we include the jQuery jCrop file for image cropping.
			add_action( 'wp_print_scripts', 'sz_core_add_jquery_cropper' );
		}

	}

	// If the image cropping is done, crop the image and save a full/thumb version.
	if ( isset( $_POST['avatar-crop-submit'] ) ) {

		// Check the nonce.
		check_admin_referer( 'sz_avatar_cropstore' );

		$args = array(
			'object'        => 'group',
			'avatar_dir'    => 'group-avatars',
			'item_id'       => $sz->groups->current_group->id,
			'original_file' => $_POST['image_src'],
			'crop_x'        => $_POST['x'],
			'crop_y'        => $_POST['y'],
			'crop_w'        => $_POST['w'],
			'crop_h'        => $_POST['h']
		);

		if ( !sz_core_avatar_handle_crop( $args ) ) {
			sz_core_add_message( __( 'There was a problem cropping the group profile photo.', 'sportszone' ), 'error' );
		} else {
			/**
			 * Fires after a group avatar is uploaded.
			 *
			 * @since 2.8.0
			 *
			 * @param int    $group_id ID of the group.
			 * @param string $type     Avatar type. 'crop' or 'full'.
			 * @param array  $args     Array of parameters passed to the avatar handler.
			 */
			do_action( 'groups_avatar_uploaded', sz_get_current_group_id(), 'crop', $args );
			sz_core_add_message( __( 'The new group profile photo was uploaded successfully.', 'sportszone' ) );
		}
	}

	/**
	 * Fires before the loading of the group Change Avatar page template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the group that is being displayed.
	 */
	do_action( 'groups_screen_group_admin_avatar', $sz->groups->current_group->id );

	/**
	 * Filters the template to load for a group's Change Avatar page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a group's Change Avatar template.
	 */
	sz_core_load_template( apply_filters( 'groups_template_group_admin_avatar', 'groups/single/home' ) );
}
add_action( 'sz_screens', 'groups_screen_group_admin_avatar' );