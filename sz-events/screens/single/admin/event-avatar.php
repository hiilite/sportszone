<?php
/**
 * Events: Single event "Manage > Photo" screen handler
 *
 * @package SportsZone
 * @subpackage EventsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of a event's Change Avatar page.
 *
 * @since 1.0.0
 */
function events_screen_event_admin_avatar() {

	if ( 'event-avatar' != sz_get_event_current_admin_tab() )
		return false;

	
	$sz = sportszone();

	// If the event admin has deleted the admin avatar.
	if ( sz_is_action_variable( 'delete', 1 ) ) {

		// Check the nonce.
		check_admin_referer( 'sz_event_avatar_delete' );

		if ( sz_core_delete_existing_avatar( array( 'item_id' => $sz->events->current_event->id, 'object' => 'event' ) ) ) {
			sz_core_add_message( __( 'The event profile photo was deleted successfully!', 'sportszone' ) );
		} else {
			sz_core_add_message( __( 'There was a problem deleting the event profile photo. Please try again.', 'sportszone' ), 'error' );
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
		if ( sz_core_avatar_handle_upload( $_FILES, 'events_avatar_upload_dir' ) ) {
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
			'object'        => 'event',
			'avatar_dir'    => 'event-avatars',
			'item_id'       => $sz->events->current_event->id,
			'original_file' => $_POST['image_src'],
			'crop_x'        => $_POST['x'],
			'crop_y'        => $_POST['y'],
			'crop_w'        => $_POST['w'],
			'crop_h'        => $_POST['h']
		);

		if ( !sz_core_avatar_handle_crop( $args ) ) {
			sz_core_add_message( __( 'There was a problem cropping the event profile photo.', 'sportszone' ), 'error' );
		} else {
			/**
			 * Fires after a event avatar is uploaded.
			 *
			 * @since 2.8.0
			 *
			 * @param int    $event_id ID of the event.
			 * @param string $type     Avatar type. 'crop' or 'full'.
			 * @param array  $args     Array of parameters passed to the avatar handler.
			 */
			do_action( 'events_avatar_uploaded', sz_get_current_event_id(), 'crop', $args );
			sz_core_add_message( __( 'The new event profile photo was uploaded successfully.', 'sportszone' ) );
		}
	}

	/**
	 * Fires before the loading of the event Change Avatar page template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the event that is being displayed.
	 */
	do_action( 'events_screen_event_admin_avatar', $sz->events->current_event->id );

	/**
	 * Filters the template to load for a event's Change Avatar page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to a event's Change Avatar template.
	 */
	sz_core_load_template( apply_filters( 'events_template_event_admin_avatar', 'events/single/home' ) );
}
add_action( 'sz_screens', 'events_screen_event_admin_avatar' );