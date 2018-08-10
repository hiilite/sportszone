<?php
/**
 * Groups: Single group "Manage > Cover Image" screen handler
 *
 * @package SportsZone
 * @subpackage GroupsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a group's Change cover image page.
 *
 * @since 2.4.0
 */
function groups_screen_group_admin_cover_image() {
	if ( 'group-cover-image' != sz_get_group_current_admin_tab() ) {
		return false;
	}

	// If the logged-in user doesn't have permission or if cover image uploads are disabled, then stop here.
	if ( ! sz_is_item_admin() || ! sz_group_use_cover_image_header() ) {
		return false;
	}

	/**
	 * Fires before the loading of the group Change cover image page template.
	 *
	 * @since 2.4.0
	 *
	 * @param int $id ID of the group that is being displayed.
	 *
	do_action( 'groups_screen_group_admin_cover_image', sz_get_current_group_id() );

	/**
	 * Filters the template to load for a group's Change cover image page.
	 *
	 * @since 2.4.0
	 *
	 * @param string $value Path to a group's Change cover image template.
	 *
	sz_core_load_template( apply_filters( 'groups_template_group_admin_cover_image', 'groups/single/home' ) );
	*/
	
	
	//------------------------------
	
	$sz = sportszone();

	// If the group admin has deleted the admin cover image.
	if ( sz_is_action_variable( 'delete', 1 ) ) {

		// Check the nonce.
		check_admin_referer( 'sz_group_cover_image_delete' );

		if ( sz_core_delete_existing_cover_image( array( 'item_id' => $sz->groups->current_group->id, 'object' => 'group' ) ) ) {
			sz_core_add_message( __( 'The group cover photo was deleted successfully!', 'sportszone' ) );
		} else {
			sz_core_add_message( __( 'There was a problem deleting the group cover photo. Please try again.', 'sportszone' ), 'error' );
		}
	}

	if ( ! isset( $sz->cover_image_admin ) ) {
		$sz->cover_image_admin = new stdClass();
	}

	$sz->cover_image_admin->step = 'upload-image';

	if ( !empty( $_FILES ) ) {

		// Check the nonce.
		check_admin_referer( 'sz_cover_image_upload' );

		// Pass the file to the cover_image upload handler.
		if ( sz_core_cover_image_handle_upload( $_FILES, 'groups_cover_image_upload_dir' ) ) {
			$sz->cover_image_admin->step = 'crop-image';

			// Make sure we include the jQuery jCrop file for image cropping.
			add_action( 'wp_print_scripts', 'sz_core_add_jquery_cropper' );
		}

	}

	// If the image cropping is done, crop the image and save a full/thumb version.
	if ( isset( $_POST['cover-image-crop-submit'] ) ) {

		// Check the nonce.
		check_admin_referer( 'sz_cover_image_cropstore' );

		$args = array(
			'object'        => 'group',
			'cover_image_dir'    => 'group-cover-image',
			'item_id'       => $sz->groups->current_group->id,
			'original_file' => $_POST['image_src'],
			'crop_x'        => $_POST['x'],
			'crop_y'        => $_POST['y'],
			'crop_w'        => $_POST['w'],
			'crop_h'        => $_POST['h']
		);

		if ( !sz_core_cover_image_handle_crop( $args ) ) {
			sz_core_add_message( __( 'There was a problem cropping the group cover image photo.', 'sportszone' ), 'error' );
		} else {
			/**
			 * Fires after a group cover_image is uploaded.
			 *
			 * @since 2.8.0
			 *
			 * @param int    $group_id ID of the group.
			 * @param string $type     Avatar type. 'crop' or 'full'.
			 * @param array  $args     Array of parameters passed to the cover_image handler.
			 */
			do_action( 'groups_cover_image_uploaded', sz_get_current_group_id(), 'crop', $args );
			sz_core_add_message( __( 'The new group cover image photo was uploaded successfully.', 'sportszone' ) );
		}
	}

	/**
	 * Fires before the loading of the group Change Avatar page template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the group that is being displayed.
	 */
	do_action( 'groups_screen_group_admin_cover_image', $sz->groups->current_group->id );

	/**
	 * Filters the template to load for a group's Change Avatar page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a group's Change Avatar template.
	 */
	sz_core_load_template( apply_filters( 'groups_template_group_admin_cover_image', 'groups/single/home' ) );
	
	//------------------------------
}
add_action( 'sz_screens', 'groups_screen_group_admin_cover_image' );