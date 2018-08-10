<?php
/**
 * SportsZone Member Screens.
 *
 * Handlers for member screens that aren't handled elsewhere.
 *
 * @package SportsZone
 * @subpackage MembersScreens
 * @since 1.7.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The main theme compat class for SportsZone Registration.
 *
 * This class sets up the necessary theme compatibility actions to safely output
 * registration template parts to the_title and the_content areas of a theme.
 *
 * @since 1.7.0
 */
class SZ_Registration_Theme_Compat {

	/**
	 * Setup the groups component theme compatibility.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {
		add_action( 'sz_setup_theme_compat', array( $this, 'is_registration' ) );
	}

	/**
	 * Are we looking at either the registration or activation pages?
	 *
	 * @since 1.7.0
	 */
	public function is_registration() {

		// Bail if not looking at the registration or activation page.
		if ( ! sz_is_register_page() && ! sz_is_activation_page() ) {
			return;
		}

		// Not a directory.
		sz_update_is_directory( false, 'register' );

		// Setup actions.
		add_filter( 'sz_get_sportszone_template',                array( $this, 'template_hierarchy' ) );
		add_action( 'sz_template_include_reset_dummy_post_data', array( $this, 'dummy_post'    ) );
		add_filter( 'sz_replace_the_content',                    array( $this, 'dummy_content' ) );
	}

	/** Template ***********************************************************/

	/**
	 * Add template hierarchy to theme compat for registration/activation pages.
	 *
	 * This is to mirror how WordPress has
	 * {@link https://codex.wordpress.org/Template_Hierarchy template hierarchy}.
	 *
	 * @since 1.8.0
	 *
	 * @param string $templates The templates from sz_get_theme_compat_templates().
	 * @return array $templates Array of custom templates to look for.
	 */
	public function template_hierarchy( $templates ) {
		$component = sanitize_file_name( sz_current_component() );

		/**
		 * Filters the template hierarchy for theme compat and registration/activation pages.
		 *
		 * This filter is a variable filter that depends on the current component
		 * being used.
		 *
		 * @since 1.8.0
		 *
		 * @param array $value Array of template paths to add to hierarchy.
		 */
		$new_templates = apply_filters( "sz_template_hierarchy_{$component}", array(
			"members/index-{$component}.php"
		) );

		// Merge new templates with existing stack
		// @see sz_get_theme_compat_templates().
		$templates = array_merge( (array) $new_templates, $templates );

		return $templates;
	}

	/**
	 * Update the global $post with dummy data.
	 *
	 * @since 1.7.0
	 */
	public function dummy_post() {
		// Registration page.
		if ( sz_is_register_page() ) {
			$title = __( 'Create an Account', 'sportszone' );

			if ( 'completed-confirmation' == sz_get_current_signup_step() ) {
				$title = __( 'Check Your Email To Activate Your Account!', 'sportszone' );
			}

		// Activation page.
		} else {
			$title = __( 'Activate Your Account', 'sportszone' );

			if ( sz_account_was_activated() ) {
				$title = __( 'Account Activated', 'sportszone' );
			}
		}

		sz_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => $title,
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'is_page'        => true,
			'comment_status' => 'closed'
		) );
	}

	/**
	 * Filter the_content with either the register or activate templates.
	 *
	 * @since 1.7.0
	 */
	public function dummy_content() {
		if ( sz_is_register_page() ) {
			return sz_buffer_template_part( 'members/register', null, false );
		} else {
			return sz_buffer_template_part( 'members/activate', null, false );
		}
	}
}
