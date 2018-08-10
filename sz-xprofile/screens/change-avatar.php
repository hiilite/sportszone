<?php
/**
 * XProfile: User's "Profile > Change Avatar" screen handler
 *
 * @package SportsZone
 * @subpackage XProfileScreens
 * @since 3.0.0
 */

/**
 * Handles the uploading and cropping of a user avatar. Displays the change avatar page.
 *
 * @since 1.0.0
 *
 */
function xprofile_screen_change_avatar() {

	// Bail if not the correct screen.
	if ( ! sz_is_my_profile() && ! sz_current_user_can( 'sz_moderate' ) ) {
		return false;
	}

	// Bail if there are action variables.
	if ( sz_action_variables() ) {
		sz_do_404();
		return;
	}

	$sz = sportszone();

	if ( ! isset( $sz->avatar_admin ) ) {
		$sz->avatar_admin = new stdClass();
	}

	$sz->avatar_admin->step = 'upload-image';

	if ( !empty( $_FILES ) ) {

		// Check the nonce.
		check_admin_referer( 'sz_avatar_upload' );

		// Pass the file to the avatar upload handler.
		if ( sz_core_avatar_handle_upload( $_FILES, 'xprofile_avatar_upload_dir' ) ) {
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
			'item_id'       => sz_displayed_user_id(),
			'original_file' => $_POST['image_src'],
			'crop_x'        => $_POST['x'],
			'crop_y'        => $_POST['y'],
			'crop_w'        => $_POST['w'],
			'crop_h'        => $_POST['h']
		);

		if ( ! sz_core_avatar_handle_crop( $args ) ) {
			sz_core_add_message( __( 'There was a problem cropping your profile photo.', 'sportszone' ), 'error' );
		} else {

			/**
			 * Fires right before the redirect, after processing a new avatar.
			 *
			 * @since 1.1.0
			 * @since 2.3.4 Add two new parameters to inform about the user id and
			 *              about the way the avatar was set (eg: 'crop' or 'camera').
			 *
			 * @param string $item_id Inform about the user id the avatar was set for.
			 * @param string $value   Inform about the way the avatar was set ('crop').
			 */
			do_action( 'xprofile_avatar_uploaded', (int) $args['item_id'], 'crop' );
			sz_core_add_message( __( 'Your new profile photo was uploaded successfully.', 'sportszone' ) );
			sz_core_redirect( sz_displayed_user_domain() );
		}
	}

	/**
	 * Fires right before the loading of the XProfile change avatar screen template file.
	 *
	 * @since 1.0.0
	 */
	do_action( 'xprofile_screen_change_avatar' );

	/**
	 * Filters the template to load for the XProfile change avatar screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Path to the XProfile change avatar template to load.
	 */
	sz_core_load_template( apply_filters( 'xprofile_template_change_avatar', 'members/single/home' ) );
}