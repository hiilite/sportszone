<?php
/**
 * Events: Create action
 *
 * @package SportsZone
 * @subpackage EventActions
 * @since 3.0.0
 */

/**
 * Catch and process event creation form submissions.
 *
 * @since 1.2.0
 *
 * @return bool
 */
function events_action_create_event() {

	// If we're not at domain.org/events/create/ then return false.
	if ( !sz_is_events_component() || !sz_is_current_action( 'create' ) )
		return false;

	if ( !is_user_logged_in() )
		return false;

	if ( !sz_user_can_create_events() ) {
		sz_core_add_message( __( 'Sorry, you are not allowed to create events.', 'sportszone' ), 'error' );
		sz_core_redirect( sz_get_events_directory_permalink() );
	}

	$sz = sportszone();

	// Make sure creation steps are in the right order.
	events_action_sort_creation_steps();

	// If no current step is set, reset everything so we can start a fresh event creation.
	$sz->events->current_create_step = sz_action_variable( 1 );
	if ( !sz_get_events_current_create_step() ) {
		unset( $sz->events->current_create_step );
		unset( $sz->events->completed_create_steps );

		setcookie( 'sz_new_event_id', false, time() - 1000, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
		setcookie( 'sz_completed_create_steps', false, time() - 1000, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );

		$reset_steps = true;
		$keys        = array_keys( $sz->events->event_creation_steps );
		sz_core_redirect( trailingslashit( sz_get_events_directory_permalink() . 'create/step/' . array_shift( $keys ) ) );
	}

	// If this is a creation step that is not recognized, just redirect them back to the first screen.
	if ( sz_get_events_current_create_step() && empty( $sz->events->event_creation_steps[sz_get_events_current_create_step()] ) ) {
		sz_core_add_message( __('There was an error saving event details. Please try again.', 'sportszone'), 'error' );
		sz_core_redirect( trailingslashit( sz_get_events_directory_permalink() . 'create' ) );
	}

	// Fetch the currently completed steps variable.
	if ( isset( $_COOKIE['sz_completed_create_steps'] ) && !isset( $reset_steps ) )
		$sz->events->completed_create_steps = json_decode( base64_decode( stripslashes( $_COOKIE['sz_completed_create_steps'] ) ) );

	// Set the ID of the new event, if it has already been created in a previous step.
	if ( sz_get_new_event_id() ) {
		$sz->events->current_event = events_get_event( $sz->events->new_event_id );

		// Only allow the event creator to continue to edit the new event.
		if ( ! sz_is_event_creator( $sz->events->current_event, sz_loggedin_user_id() ) ) {
			sz_core_add_message( __( 'Only the event creator may continue editing this event.', 'sportszone' ), 'error' );
			sz_core_redirect( trailingslashit( sz_get_events_directory_permalink() . 'create' ) );
		}
	}

	// If the save, upload or skip button is hit, lets calculate what we need to save.
	if ( isset( $_POST['save'] ) ) {

		// Check the nonce.
		check_admin_referer( 'events_create_save_' . sz_get_events_current_create_step() );

		if ( 'event-details' == sz_get_events_current_create_step() ) {
			if ( empty( $_POST['event-name'] ) || empty( $_POST['event-desc'] ) || !strlen( trim( $_POST['event-name'] ) ) || !strlen( trim( $_POST['event-desc'] ) ) ) {
				sz_core_add_message( __( 'Please fill in all of the required fields', 'sportszone' ), 'error' );
				sz_core_redirect( trailingslashit( sz_get_events_directory_permalink() . 'create/step/' . sz_get_events_current_create_step() ) );
			}

			$new_event_id = isset( $sz->events->new_event_id ) ? $sz->events->new_event_id : 0;

			if ( !$sz->events->new_event_id = events_create_event( array( 
					'event_id' => $new_event_id, 
					'name' => $_POST['event-name'], 
					'description' => $_POST['event-desc'], 
					'slug' => events_check_slug( sanitize_title( esc_attr( $_POST['event-name'] ) ) ), 
					'date_created' => sz_core_current_time(), 
					'status' => 'public' ) ) 
				) {
				sz_core_add_message( __( 'There was an error saving event details. Please try again.', 'sportszone' ), 'error' );
				sz_core_redirect( trailingslashit( sz_get_events_directory_permalink() . 'create/step/' . sz_get_events_current_create_step() ) );
			}
			
			if ( isset( $_POST['event-club'] ) ) {
				events_update_eventmeta( $new_event_id, 'event_club', $_POST['event-club'] );
			}
			/*
			 * Save event types.
			 *
			 * Ensure we keep types that have 'show_in_create_screen' set to false.
			 */
			$current_types = sz_events_get_event_type( $new_event_id, false );
			$current_types = array_intersect( sz_events_get_event_types( array( 'show_in_create_screen' => true ) ), (array) $current_types );
			if ( isset( $_POST['event-types'] ) ) {
				$current_types = array_merge( $current_types, $_POST['event-types'] );
	
				// Set event types.
				sz_events_set_event_type( $new_event_id, $current_types );
	
			// No event types checked, so this means we want to wipe out all event types.
			} else {
				/*
				 * Passing a blank string will wipe out all types for the event.
				 *
				 * Ensure we keep types that have 'show_in_create_screen' set to false.
				 */
				$current_types = empty( $current_types ) ? '' : $current_types;
	
				// Set event types.
				sz_events_set_event_type( $new_event_id, $current_types );
			}
		}

		if ( 'event-settings' == sz_get_events_current_create_step() ) {
			$event_status = 'public';
			$event_enable_forum = 1;

			if ( !isset($_POST['event-show-forum']) ) {
				$event_enable_forum = 0;
			}

			if ( 'private' == $_POST['event-status'] )
				$event_status = 'private';
			elseif ( 'hidden' == $_POST['event-status'] )
				$event_status = 'hidden';

			if ( !$sz->events->new_event_id = events_create_event( array( 'event_id' => $sz->events->new_event_id, 'status' => $event_status, 'enable_forum' => $event_enable_forum ) ) ) {
				sz_core_add_message( __( 'There was an error saving event details. Please try again.', 'sportszone' ), 'error' );
				sz_core_redirect( trailingslashit( sz_get_events_directory_permalink() . 'create/step/' . sz_get_events_current_create_step() ) );
			}

			// Save event types.
			if ( ! empty( $_POST['event-types'] ) ) {
				sz_events_set_event_type( $sz->events->new_event_id, $_POST['event-types'] );
			}

			/**
			 * Filters the allowed invite statuses.
			 *
			 * @since 1.5.0
			 *
			 * @param array $value Array of statuses allowed.
			 *                     Possible values are 'members,
			 *                     'mods', and 'admins'.
			 */
			$allowed_invite_status = apply_filters( 'events_allowed_invite_status', array( 'members', 'mods', 'admins' ) );
			$invite_status	       = !empty( $_POST['event-invite-status'] ) && in_array( $_POST['event-invite-status'], (array) $allowed_invite_status ) ? $_POST['event-invite-status'] : 'members';

			events_update_eventmeta( $sz->events->new_event_id, 'invite_status', $invite_status );
		}

		if ( 'event-invites' === sz_get_events_current_create_step() ) {
			if ( ! empty( $_POST['friends'] ) ) {
				foreach ( (array) $_POST['friends'] as $friend ) {
					events_invite_user( array(
						'user_id'  => (int) $friend,
						'event_id' => $sz->events->new_event_id,
					) );
				}
			}

			events_send_invites( sz_loggedin_user_id(), $sz->events->new_event_id );
		}

		/**
		 * Fires before finalization of event creation and cookies are set.
		 *
		 * This hook is a variable hook dependent on the current step
		 * in the creation process.
		 *
		 * @since 1.1.0
		 */
		do_action( 'events_create_event_step_save_' . sz_get_events_current_create_step() );

		/**
		 * Fires after the event creation step is completed.
		 *
		 * Mostly for clearing cache on a generic action name.
		 *
		 * @since 1.1.0
		 */
		do_action( 'events_create_event_step_complete' );

		/**
		 * Once we have successfully saved the details for this step of the creation process
		 * we need to add the current step to the array of completed steps, then update the cookies
		 * holding the information
		 */
		$completed_create_steps = isset( $sz->events->completed_create_steps ) ? $sz->events->completed_create_steps : array();
		if ( !in_array( sz_get_events_current_create_step(), $completed_create_steps ) )
			$sz->events->completed_create_steps[] = sz_get_events_current_create_step();

		// Reset cookie info.
		setcookie( 'sz_new_event_id', $sz->events->new_event_id, time()+60*60*24, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
		setcookie( 'sz_completed_create_steps', base64_encode( json_encode( $sz->events->completed_create_steps ) ), time()+60*60*24, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );

		// If we have completed all steps and hit done on the final step we
		// can redirect to the completed event.
		$keys = array_keys( $sz->events->event_creation_steps );
		if ( count( $sz->events->completed_create_steps ) == count( $keys ) && sz_get_events_current_create_step() == array_pop( $keys ) ) {
			unset( $sz->events->current_create_step );
			unset( $sz->events->completed_create_steps );

			setcookie( 'sz_new_event_id', false, time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
			setcookie( 'sz_completed_create_steps', false, time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );

			// Once we completed all steps, record the event creation in the activity stream.
			if ( sz_is_active( 'activity' ) ) {
				events_record_activity( array(
					'type' => 'created_event',
					'item_id' => $sz->events->new_event_id
				) );
			}

			/**
			 * Fires after the event has been successfully created.
			 *
			 * @since 1.1.0
			 *
			 * @param int $new_event_id ID of the newly created event.
			 */
			do_action( 'events_event_create_complete', $sz->events->new_event_id );

			sz_core_redirect( sz_get_event_permalink( $sz->events->current_event ) );
		} else {
			/**
			 * Since we don't know what the next step is going to be (any plugin can insert steps)
			 * we need to loop the step array and fetch the next step that way.
			 */
			foreach ( $keys as $key ) {
				if ( $key == sz_get_events_current_create_step() ) {
					$next = 1;
					continue;
				}

				if ( isset( $next ) ) {
					$next_step = $key;
					break;
				}
			}

			sz_core_redirect( trailingslashit( sz_get_events_directory_permalink() . 'create/step/' . $next_step ) );
		}
	}

	// Remove invitations.
	if ( 'event-invites' === sz_get_events_current_create_step() && ! empty( $_REQUEST['user_id'] ) && is_numeric( $_REQUEST['user_id'] ) ) {
		if ( ! check_admin_referer( 'events_invite_uninvite_user' ) ) {
			return false;
		}

		$message = __( 'Invite successfully removed', 'sportszone' );
		$error   = false;

		if( ! events_uninvite_user( (int) $_REQUEST['user_id'], $sz->events->new_event_id ) ) {
			$message = __( 'There was an error removing the invite', 'sportszone' );
			$error   = 'error';
		}

		sz_core_add_message( $message, $error );
		sz_core_redirect( trailingslashit( sz_get_events_directory_permalink() . 'create/step/event-invites' ) );
	}

	// Event avatar is handled separately.
	if ( 'event-avatar' == sz_get_events_current_create_step() && isset( $_POST['upload'] ) ) {
		if ( ! isset( $sz->avatar_admin ) ) {
			$sz->avatar_admin = new stdClass();
		}

		if ( !empty( $_FILES ) && isset( $_POST['upload'] ) ) {
			// Normally we would check a nonce here, but the event save nonce is used instead.
			// Pass the file to the avatar upload handler.
			if ( sz_core_avatar_handle_upload( $_FILES, 'events_avatar_upload_dir' ) ) {
				$sz->avatar_admin->step = 'crop-image';

				// Make sure we include the jQuery jCrop file for image cropping.
				add_action( 'wp_print_scripts', 'sz_core_add_jquery_cropper' );
			}
		}

		// If the image cropping is done, crop the image and save a full/thumb version.
		if ( isset( $_POST['avatar-crop-submit'] ) && isset( $_POST['upload'] ) ) {

			// Normally we would check a nonce here, but the event save nonce is used instead.
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

			if ( ! sz_core_avatar_handle_crop( $args ) ) {
				sz_core_add_message( __( 'There was an error saving the event profile photo, please try uploading again.', 'sportszone' ), 'error' );
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

				sz_core_add_message( __( 'The event profile photo was uploaded successfully.', 'sportszone' ) );
			}
		}
	}
	
	// Event cover image is handled separately.
	if ( 'event-cover-image' == sz_get_events_current_create_step() && isset( $_POST['upload'] ) ) {
		if ( ! isset( $sz->avatar_admin ) ) {
			$sz->cover_image_admin = new stdClass();
		}

		if ( !empty( $_FILES ) && isset( $_POST['upload'] ) ) {
			// Normally we would check a nonce here, but the event save nonce is used instead.
			// Pass the file to the avatar upload handler.
			if ( sz_core_cover_image_handle_upload( $_FILES, 'events_cover_image_upload_dir' ) ) {
				$sz->cover_image_admin->step = 'crop-image';
				echo sportszone()->cover_image_admin->image->dir;
				// Make sure we include the jQuery jCrop file for image cropping.
				add_action( 'wp_print_scripts', 'sz_core_add_cover_image_jquery_cropper' );
			}
		}

		// If the image cropping is done, crop the image and save a full/thumb version.
		if ( isset( $_POST['cover-image-crop-submit'] ) && isset( $_POST['upload'] ) ) {

			// Normally we would check a nonce here, but the event save nonce is used instead.
			$args = array(
				'object'        => 'event',
				'avatar_dir'    => 'event-cover-images',
				'item_id'       => $sz->events->current_event->id,
				'original_file' => $_POST['image_src'],
				'crop_x'        => $_POST['x'],
				'crop_y'        => $_POST['y'],
				'crop_w'        => $_POST['w'],
				'crop_h'        => $_POST['h']
			);

			if ( ! sz_core_avatar_handle_crop( $args ) ) {
				sz_core_add_message( __( 'There was an error saving the event profile photo, please try uploading again.', 'sportszone' ), 'error' );
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

				sz_core_add_message( __( 'The event profile photo was uploaded successfully.', 'sportszone' ) );
			}
		}
	}

	/**
	 * Filters the template to load for the event creation screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to the event creation template to load.
	 */
	sz_core_load_template( apply_filters( 'events_template_create_event', 'events/create' ) );
}
add_action( 'sz_actions', 'events_action_create_event' );

/**
 * Sort the event creation steps.
 *
 * @since 1.1.0
 *
 * @return false|null False on failure.
 */
function events_action_sort_creation_steps() {

	if ( !sz_is_events_component() || !sz_is_current_action( 'create' ) )
		return false;

	$sz = sportszone();

	if ( !is_array( $sz->events->event_creation_steps ) )
		return false;

	foreach ( (array) $sz->events->event_creation_steps as $slug => $step ) {
		while ( !empty( $temp[$step['position']] ) )
			$step['position']++;

		$temp[$step['position']] = array( 'name' => $step['name'], 'slug' => $slug );
	}

	// Sort the steps by their position key.
	ksort($temp);
	unset($sz->events->event_creation_steps);

	foreach( (array) $temp as $position => $step )
		$sz->events->event_creation_steps[$step['slug']] = array( 'name' => $step['name'], 'position' => $position );

	/**
	 * Fires after event creation sets have been sorted.
	 *
	 * @since 2.3.0
	 */
	do_action( 'events_action_sort_creation_steps' );
}