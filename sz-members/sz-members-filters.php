<?php
/**
 * SportsZone Members Filters.
 *
 * Filters specific to the Members component.
 *
 * @package SportsZone
 * @subpackage MembersFilters
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Escape commonly used fullname output functions.
 */
add_filter( 'sz_displayed_user_fullname',    'esc_html' );
add_filter( 'sz_get_loggedin_user_fullname', 'esc_html' );

// Filter the user registration URL to point to SportsZone's registration page.
add_filter( 'register_url', 'sz_get_signup_page' );

/**
 * Load additional sign-up sanitization filters on sz_loaded.
 *
 * These are used to prevent XSS in the SportsZone sign-up process. You can
 * unhook these to allow for customization of your registration fields;
 * however, it is highly recommended that you leave these in place for the
 * safety of your network.
 *
 * @since 1.5.0
 */
function sz_members_signup_sanitization() {

	// Filters on sign-up fields.
	$fields = array (
		'sz_get_signup_username_value',
		'sz_get_signup_email_value',
		'sz_get_signup_with_blog_value',
		'sz_get_signup_blog_url_value',
		'sz_get_signup_blog_title_value',
		'sz_get_signup_blog_privacy_value',
		'sz_get_signup_avatar_dir_value',
	);

	// Add the filters to each field.
	foreach( $fields as $filter ) {
		add_filter( $filter, 'esc_html',       1 );
		add_filter( $filter, 'wp_filter_kses', 2 );
		add_filter( $filter, 'stripslashes',   3 );
	}

	// Sanitize email.
	add_filter( 'sz_get_signup_email_value', 'sanitize_email' );
}
add_action( 'sz_loaded', 'sz_members_signup_sanitization' );

/**
 * Make sure the username is not the blog slug in case of root profile & subdirectory blog.
 *
 * If SZ_ENABLE_ROOT_PROFILES is defined & multisite config is set to subdirectories,
 * then there is a chance site.url/username == site.url/blogslug. If so, user's profile
 * is not reachable, instead the blog is displayed. This filter makes sure the signup username
 * is not the same than the blog slug for this particular config.
 *
 * @since 2.1.0
 *
 * @param array $illegal_names Array of illiegal names.
 * @return array $illegal_names
 */
function sz_members_signup_with_subdirectory_blog( $illegal_names = array() ) {
	if ( ! sz_core_enable_root_profiles() ) {
		return $illegal_names;
	}

	if ( is_network_admin() && isset( $_POST['blog'] ) ) {
		$blog = $_POST['blog'];
		$domain = '';

		if ( preg_match( '|^([a-zA-Z0-9-])$|', $blog['domain'] ) ) {
			$domain = strtolower( $blog['domain'] );
		}

		if ( username_exists( $domain ) ) {
			$illegal_names[] = $domain;
		}

	} else {
		$illegal_names[] = sportszone()->signup->username;
	}

	return $illegal_names;
}
add_filter( 'subdirectory_reserved_names', 'sz_members_signup_with_subdirectory_blog', 10, 1 );

/**
 * Filter the user profile URL to point to SportsZone profile edit.
 *
 * @since 1.6.0
 *
 * @param string $url     WP profile edit URL.
 * @param int    $user_id ID of the user.
 * @param string $scheme  Scheme to use.
 * @return string
 */
function sz_members_edit_profile_url( $url, $user_id, $scheme = 'admin' ) {

	// If xprofile is active, use profile domain link.
	if ( ! is_admin() && sz_is_active( 'xprofile' ) ) {
		$profile_link = trailingslashit( sz_core_get_user_domain( $user_id ) . sz_get_profile_slug() . '/edit' );

	} else {
		// Default to $url.
		$profile_link = $url;
	}

	/**
	 * Filters the user profile URL to point to SportsZone profile edit.
	 *
	 * @since 1.5.2
	 *
	 * @param string $url WP profile edit URL.
	 * @param int    $user_id ID of the user.
	 * @param string $scheme Scheme to use.
	 */
	return apply_filters( 'sz_members_edit_profile_url', $profile_link, $url, $user_id, $scheme );
}
add_filter( 'edit_profile_url', 'sz_members_edit_profile_url', 10, 3 );
