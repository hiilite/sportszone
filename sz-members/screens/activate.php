<?php
/**
 * Members: Activate screen handler
 *
 * @package SportsZone
 * @subpackage MembersScreens
 * @since 3.0.0
 */

/**
 * Handle the loading of the Activate screen.
 *
 * @since 1.1.0
 */
function sz_core_screen_activation() {

	// Bail if not viewing the activation page.
	if ( ! sz_is_current_component( 'activate' ) ) {
		return false;
	}

	// If the user is already logged in, redirect away from here.
	if ( is_user_logged_in() ) {

		// If activation page is also front page, set to members directory to
		// avoid an infinite loop. Otherwise, set to root domain.
		$redirect_to = sz_is_component_front_page( 'activate' )
			? sz_get_members_directory_permalink()
			: sz_get_root_domain();

		// Trailing slash it, as we expect these URL's to be.
		$redirect_to = trailingslashit( $redirect_to );

		/**
		 * Filters the URL to redirect logged in users to when visiting activation page.
		 *
		 * @since 1.9.0
		 *
		 * @param string $redirect_to URL to redirect user to.
		 */
		$redirect_to = apply_filters( 'sz_loggedin_activate_page_redirect_to', $redirect_to );

		// Redirect away from the activation page.
		sz_core_redirect( $redirect_to );
	}

	// Get SportsZone.
	$sz = sportszone();

	/**
	 * Filters the template to load for the Member activation page screen.
	 *
	 * @since 1.1.1
	 *
	 * @param string $value Path to the Member activation template to load.
	 */
	sz_core_load_template( apply_filters( 'sz_core_template_activate', array( 'activate', 'registration/activate' ) ) );
}
add_action( 'sz_screens', 'sz_core_screen_activation' );


/**
 * Catches and processes account activation requests.
 *
 * @since 3.0.0
 */
function sz_members_action_activate_account() {
	if ( ! sz_is_current_component( 'activate' ) ) {
		return;
	}

	if ( is_user_logged_in() ) {
		return;
	}

	if ( ! empty( $_POST['key'] ) ) {
		$key = wp_unslash( $_POST['key'] );

	// Backward compatibility with templates using `method="get"` in their activation forms.
	} elseif ( ! empty( $_GET['key'] ) ) {
		$key = wp_unslash( $_GET['key'] );
	}

	if ( empty( $key ) ) {
		return;
	}

	$sz = sportszone();

	/**
	 * Filters the activation signup.
	 *
	 * @since 1.1.0
	 *
	 * @param bool|int $value Value returned by activation.
	 *                        Integer on success, boolean on failure.
	 */
	$user = apply_filters( 'sz_core_activate_account', sz_core_activate_signup( $key ) );

	// If there were errors, add a message and redirect.
	if ( ! empty( $user->errors ) ) {
		sz_core_add_message( $user->get_error_message(), 'error' );
		sz_core_redirect( trailingslashit( sz_get_root_domain() . '/' . $sz->pages->activate->slug ) );
	}

	sz_core_add_message( __( 'Your account is now active!', 'sportszone' ) );
	sz_core_redirect( add_query_arg( 'activated', '1', sz_get_activation_page() ) );

}
add_action( 'sz_actions', 'sz_members_action_activate_account' );