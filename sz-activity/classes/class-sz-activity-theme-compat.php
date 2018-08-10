<?php
/**
 * SportsZone Activity Theme Compatibility.
 *
 * @package SportsZone
 * @since 1.7.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The main theme compat class for SportsZone Activity.
 *
 * This class sets up the necessary theme compatibility actions to safely output
 * activity template parts to the_title and the_content areas of a theme.
 *
 * @since 1.7.0
 */
class SZ_Activity_Theme_Compat {

	/**
	 * Set up the activity component theme compatibility.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {
		add_action( 'sz_setup_theme_compat', array( $this, 'is_activity' ) );
	}

	/**
	 * Set up the theme compatibility hooks, if we're looking at an activity page.
	 *
	 * @since 1.7.0
	 */
	public function is_activity() {

		// Bail if not looking at a group.
		if ( ! sz_is_activity_component() )
			return;

		// Activity Directory.
		if ( ! sz_displayed_user_id() && ! sz_current_action() ) {
			sz_update_is_directory( true, 'activity' );

			/** This action is documented in sz-activity/sz-activity-screens.php */
			do_action( 'sz_activity_screen_index' );

			add_filter( 'sz_get_sportszone_template',                array( $this, 'directory_template_hierarchy' ) );
			add_action( 'sz_template_include_reset_dummy_post_data', array( $this, 'directory_dummy_post' ) );
			add_filter( 'sz_replace_the_content',                    array( $this, 'directory_content'    ) );

		// Single activity.
		} elseif ( sz_is_single_activity() ) {
			add_filter( 'sz_get_sportszone_template',                array( $this, 'single_template_hierarchy' ) );
			add_action( 'sz_template_include_reset_dummy_post_data', array( $this, 'single_dummy_post' ) );
			add_filter( 'sz_replace_the_content',                    array( $this, 'single_dummy_content'    ) );
		}
	}

	/** Directory *************************************************************/

	/**
	 * Add template hierarchy to theme compat for the activity directory page.
	 *
	 * This is to mirror how WordPress has {@link https://codex.wordpress.org/Template_Hierarchy template hierarchy}.
	 *
	 * @since 1.8.0
	 *
	 * @param string $templates The templates from sz_get_theme_compat_templates().
	 * @return array $templates Array of custom templates to look for.
	 */
	public function directory_template_hierarchy( $templates ) {

		/**
		 * Filters the template hierarchy for the activity directory page.
		 *
		 * @since 1.8.0
		 *
		 * @param array $index-directory Array holding template names to be merged into template list.
		 */
		$new_templates = apply_filters( 'sz_template_hierarchy_activity_directory', array(
			'activity/index-directory.php'
		) );

		// Merge new templates with existing stack
		// @see sz_get_theme_compat_templates().
		$templates = array_merge( (array) $new_templates, $templates );

		return $templates;
	}

	/**
	 * Update the global $post with directory data.
	 *
	 * @since 1.7.0
	 */
	public function directory_dummy_post() {
		sz_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => sz_get_directory_title( 'activity' ),
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
	 * Filter the_content with the groups index template part.
	 *
	 * @since 1.7.0
	 */
	public function directory_content() {
		return sz_buffer_template_part( 'activity/index', null, false );
	}

	/** Single ****************************************************************/

	/**
	 * Add custom template hierarchy to theme compat for activity permalink pages.
	 *
	 * This is to mirror how WordPress has {@link https://codex.wordpress.org/Template_Hierarchy template hierarchy}.
	 *
	 * @since 1.8.0
	 *
	 * @param string $templates The templates from sz_get_theme_compat_templates().
	 * @return array $templates Array of custom templates to look for.
	 */
	public function single_template_hierarchy( $templates ) {

		/**
		 * Filters the template hierarchy for the activity permalink pages.
		 *
		 * @since 1.8.0
		 *
		 * @param array $index Array holding template names to be merged into template list.
		 */
		$new_templates = apply_filters( 'sz_template_hierarchy_activity_single_item', array(
			'activity/single/index.php'
		) );

		// Merge new templates with existing stack
		// @see sz_get_theme_compat_templates().
		$templates = array_merge( (array) $new_templates, $templates );

		return $templates;
	}

	/**
	 * Update the global $post with the displayed user's data.
	 *
	 * @since 1.7.0
	 */
	public function single_dummy_post() {
		sz_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => __( 'Activity', 'sportszone' ),
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
	 * Filter the_content with the members' activity permalink template part.
	 *
	 * @since 1.7.0
	 */
	public function single_dummy_content() {
		return sz_buffer_template_part( 'activity/single/home', null, false );
	}
}
