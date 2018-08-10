<?php
/**
 * Members: Register screen handler
 *
 * @package SportsZone
 * @subpackage MembersScreens
 * @since 3.0.0
 */

/**
 * Handle the loading of the signup screen.
 *
 * @since 1.1.0
 */
function sz_core_screen_signup() {
	$sz = sportszone();

	if ( ! sz_is_current_component( 'register' ) || sz_current_action() )
		return;

	// Not a directory.
	sz_update_is_directory( false, 'register' );

	// If the user is logged in, redirect away from here.
	if ( is_user_logged_in() ) {

		$redirect_to = sz_is_component_front_page( 'register' )
			? sz_get_members_directory_permalink()
			: sz_get_root_domain();

		/**
		 * Filters the URL to redirect logged in users to when visiting registration page.
		 *
		 * @since 1.5.1
		 *
		 * @param string $redirect_to URL to redirect user to.
		 */
		sz_core_redirect( apply_filters( 'sz_loggedin_register_page_redirect_to', $redirect_to ) );

		return;
	}

	$sz->signup->step = 'request-details';

	if ( !sz_get_signup_allowed() ) {
		$sz->signup->step = 'registration-disabled';

		// If the signup page is submitted, validate and save.
	} elseif ( isset( $_POST['signup_submit'] ) && sz_verify_nonce_request( 'sz_new_signup' ) ) {

		/**
		 * Fires before the validation of a new signup.
		 *
		 * @since 2.0.0
		 */
		do_action( 'sz_signup_pre_validate' );

		// Check the base account details for problems.
		$account_details = sz_core_validate_user_signup( $_POST['signup_username'], $_POST['signup_email'] );

		// If there are errors with account details, set them for display.
		if ( !empty( $account_details['errors']->errors['user_name'] ) )
			$sz->signup->errors['signup_username'] = $account_details['errors']->errors['user_name'][0];

		if ( !empty( $account_details['errors']->errors['user_email'] ) )
			$sz->signup->errors['signup_email'] = $account_details['errors']->errors['user_email'][0];

		// Check that both password fields are filled in.
		if ( empty( $_POST['signup_password'] ) || empty( $_POST['signup_password_confirm'] ) )
			$sz->signup->errors['signup_password'] = __( 'Please make sure you enter your password twice', 'sportszone' );

		// Check that the passwords match.
		if ( ( !empty( $_POST['signup_password'] ) && !empty( $_POST['signup_password_confirm'] ) ) && $_POST['signup_password'] != $_POST['signup_password_confirm'] )
			$sz->signup->errors['signup_password'] = __( 'The passwords you entered do not match.', 'sportszone' );

		$sz->signup->username = $_POST['signup_username'];
		$sz->signup->email = $_POST['signup_email'];

		// Now we've checked account details, we can check profile information.
		if ( sz_is_active( 'xprofile' ) ) {

			// Make sure hidden field is passed and populated.
			if ( isset( $_POST['signup_profile_field_ids'] ) && !empty( $_POST['signup_profile_field_ids'] ) ) {

				// Let's compact any profile field info into an array.
				$profile_field_ids = explode( ',', $_POST['signup_profile_field_ids'] );

				// Loop through the posted fields formatting any datebox values then validate the field.
				foreach ( (array) $profile_field_ids as $field_id ) {
					sz_xprofile_maybe_format_datebox_post_data( $field_id );

					// Trim post fields.
					if ( isset( $_POST[ 'field_' . $field_id ] ) ) {
						if ( is_array( $_POST[ 'field_' . $field_id ] ) ) {
							$_POST[ 'field_' . $field_id ] = array_map( 'trim', $_POST[ 'field_' . $field_id ] );
						} else {
							$_POST[ 'field_' . $field_id ] = trim( $_POST[ 'field_' . $field_id ] );
						}
					}

					// Create errors for required fields without values.
					if ( xprofile_check_is_required_field( $field_id ) && empty( $_POST[ 'field_' . $field_id ] ) && ! sz_current_user_can( 'sz_moderate' ) )
						$sz->signup->errors['field_' . $field_id] = __( 'This is a required field', 'sportszone' );
				}

				// This situation doesn't naturally occur so bounce to website root.
			} else {
				sz_core_redirect( sz_get_root_domain() );
			}
		}

		// Finally, let's check the blog details, if the user wants a blog and blog creation is enabled.
		if ( isset( $_POST['signup_with_blog'] ) ) {
			$active_signup = sz_core_get_root_option( 'registration' );

			if ( 'blog' == $active_signup || 'all' == $active_signup ) {
				$blog_details = sz_core_validate_blog_signup( $_POST['signup_blog_url'], $_POST['signup_blog_title'] );

				// If there are errors with blog details, set them for display.
				if ( !empty( $blog_details['errors']->errors['blogname'] ) )
					$sz->signup->errors['signup_blog_url'] = $blog_details['errors']->errors['blogname'][0];

				if ( !empty( $blog_details['errors']->errors['blog_title'] ) )
					$sz->signup->errors['signup_blog_title'] = $blog_details['errors']->errors['blog_title'][0];
			}
		}

		/**
		 * Fires after the validation of a new signup.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_signup_validate' );

		// Add any errors to the action for the field in the template for display.
		if ( !empty( $sz->signup->errors ) ) {
			foreach ( (array) $sz->signup->errors as $fieldname => $error_message ) {
				/**
				 * Filters the error message in the loop.
				 *
				 * @since 1.5.0
				 *
				 * @param string $value Error message wrapped in html.
				 */
				add_action( 'sz_' . $fieldname . '_errors', function() use ( $error_message ) {
					echo apply_filters( 'sz_members_signup_error_message', "<div class=\"error\">" . $error_message . "</div>" );
				} );
			}
		} else {
			$sz->signup->step = 'save-details';

			// No errors! Let's register those deets.
			$active_signup = sz_core_get_root_option( 'registration' );

			if ( 'none' != $active_signup ) {

				// Make sure the extended profiles module is enabled.
				if ( sz_is_active( 'xprofile' ) ) {
					// Let's compact any profile field info into usermeta.
					$profile_field_ids = explode( ',', $_POST['signup_profile_field_ids'] );

					/*
					 * Loop through the posted fields, formatting any
					 * datebox values, then add to usermeta.
					 */
					foreach ( (array) $profile_field_ids as $field_id ) {
						sz_xprofile_maybe_format_datebox_post_data( $field_id );

						if ( !empty( $_POST['field_' . $field_id] ) )
							$usermeta['field_' . $field_id] = $_POST['field_' . $field_id];

						if ( !empty( $_POST['field_' . $field_id . '_visibility'] ) )
							$usermeta['field_' . $field_id . '_visibility'] = $_POST['field_' . $field_id . '_visibility'];
					}

					// Store the profile field ID's in usermeta.
					$usermeta['profile_field_ids'] = $_POST['signup_profile_field_ids'];
				}

				// Hash and store the password.
				$usermeta['password'] = wp_hash_password( $_POST['signup_password'] );

				// If the user decided to create a blog, save those details to usermeta.
				if ( 'blog' == $active_signup || 'all' == $active_signup )
					$usermeta['public'] = ( isset( $_POST['signup_blog_privacy'] ) && 'public' == $_POST['signup_blog_privacy'] ) ? true : false;

				/**
				 * Filters the user meta used for signup.
				 *
				 * @since 1.1.0
				 *
				 * @param array $usermeta Array of user meta to add to signup.
				 */
				$usermeta = apply_filters( 'sz_signup_usermeta', $usermeta );

				// Finally, sign up the user and/or blog.
				if ( isset( $_POST['signup_with_blog'] ) && is_multisite() )
					$wp_user_id = sz_core_signup_blog( $blog_details['domain'], $blog_details['path'], $blog_details['blog_title'], $_POST['signup_username'], $_POST['signup_email'], $usermeta );
				else
					$wp_user_id = sz_core_signup_user( $_POST['signup_username'], $_POST['signup_password'], $_POST['signup_email'], $usermeta );

				if ( is_wp_error( $wp_user_id ) ) {
					$sz->signup->step = 'request-details';
					sz_core_add_message( $wp_user_id->get_error_message(), 'error' );
				} else {
					$sz->signup->step = 'completed-confirmation';
				}
			}

			/**
			 * Fires after the completion of a new signup.
			 *
			 * @since 1.1.0
			 */
			do_action( 'sz_complete_signup' );
		}

	}

	/**
	 * Fires right before the loading of the Member registration screen template file.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_core_screen_signup' );

	/**
	 * Filters the template to load for the Member registration page screen.
	 *
	 * @since 1.5.0
	 *
	 * @param string $value Path to the Member registration template to load.
	 */
	sz_core_load_template( apply_filters( 'sz_core_template_register', array( 'register', 'registration/register' ) ) );
}
add_action( 'sz_screens', 'sz_core_screen_signup' );