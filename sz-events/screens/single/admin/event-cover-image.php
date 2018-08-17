<?php
/**
 * Events: Single event "Manage > Cover Image" screen handler
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a event's Change cover image page.
 *
 * @since 2.4.0
 */
function events_screen_event_admin_cover_image() {
	if ( 'event-cover-image' != sz_get_event_current_admin_tab() ) {
		return false;
	}

	// If the logged-in user doesn't have permission or if cover image uploads are disabled, then stop here.
	if ( ! sz_is_item_admin() || ! sz_event_use_cover_image_header() ) {
		return false;
	}

	/**
	 * Fires before the loading of the event Change cover image page template.
	 *
	 * @since 2.4.0
	 *
	 * @param int $id ID of the event that is being displayed.
	 *
	do_action( 'events_screen_event_admin_cover_image', sz_get_current_event_id() );

	/**
	 * Filters the template to load for a event's Change cover image page.
	 *
	 * @since 2.4.0
	 *
	 * @param string $value Path to a event's Change cover image template.
	 *
	sz_core_load_template( apply_filters( 'events_template_event_admin_cover_image', 'events/single/home' ) );
	*/
	
	
	//------------------------------
	
	$sz = sportszone();

	// If the event admin has deleted the admin cover image.
	if ( sz_is_action_variable( 'delete', 1 ) ) {

		// Check the nonce.
		check_admin_referer( 'sz_event_cover_image_delete' );

		if ( sz_core_delete_existing_cover_image( array( 'item_id' => $sz->events->current_event->id, 'object' => 'event' ) ) ) {
			sz_core_add_message( __( 'The event cover photo was deleted successfully!', 'sportszone' ) );
		} else {
			sz_core_add_message( __( 'There was a problem deleting the event cover photo. Please try again.', 'sportszone' ), 'error' );
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
		if ( sz_core_cover_image_handle_upload( $_FILES, 'events_cover_image_upload_dir' ) ) {
			$sz->cover_image_admin->step = 'crop-image';

			// Make sure we include the jQuery jCrop file for image cropping.
			add_action( 'wp_print_scripts', 'sz_core_add_cover_image_jquery_cropper' );
		}

	}

	// If the image cropping is done, crop the image and save a full/thumb version.
	if ( isset( $_POST['cover-image-crop-submit'] ) ) {

		// Check the nonce.
		check_admin_referer( 'sz_cover_image_cropstore' );

		$args = array(
			'object'        => 'event',
			'cover_image_dir'    => 'event-cover-images',
			'item_id'       => $sz->events->current_event->id,
			'original_file' => $_POST['image_src'],
			'crop_x'        => $_POST['x'],
			'crop_y'        => $_POST['y'],
			'crop_w'        => $_POST['w'],
			'crop_h'        => $_POST['h']
		);

		if ( !sz_core_cover_image_handle_crop( $args ) ) {
			sz_core_add_message( __( 'There was a problem cropping the event cover image photo.', 'sportszone' ), 'error' );
		} else {
			/**
			 * Fires after a event cover_image is uploaded.
			 *
			 * @since 2.8.0
			 *
			 * @param int    $event_id ID of the event.
			 * @param string $type     Avatar type. 'crop' or 'full'.
			 * @param array  $args     Array of parameters passed to the cover_image handler.
			 */
			do_action( 'events_cover_image_uploaded', sz_get_current_event_id(), 'crop', $args );
			sz_core_add_message( __( 'The new event cover image photo was uploaded successfully.', 'sportszone' ) );
		}
	}

	/**
	 * Fires before the loading of the event Change Avatar page template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the event that is being displayed.
	 */
	do_action( 'events_screen_event_admin_cover_image', $sz->events->current_event->id );

	/**
	 * Filters the template to load for a event's Change Avatar page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a event's Change Avatar template.
	 */
	sz_core_load_template( apply_filters( 'events_template_event_admin_cover_image', 'events/single/home' ) );
	
	//------------------------------
}
add_action( 'sz_screens', 'events_screen_event_admin_cover_image' );