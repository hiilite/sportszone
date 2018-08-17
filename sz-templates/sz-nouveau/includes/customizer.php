<?php
/**
 * Code to hook into the WP Customizer
 *
 * @since 3.0.0
 * @version 3.1.0
 */

/**
 * Add a specific panel for the BP Nouveau Template Pack.
 *
 * @since 3.0.0
 *
 * @param WP_Customize_Manager $wp_customize WordPress customizer.
 */
function sz_nouveau_customize_register( WP_Customize_Manager $wp_customize ) {
	if ( ! sz_is_root_blog() ) {
		return;
	}

	require_once( trailingslashit( sz_nouveau()->includes_dir ) . 'customizer-controls.php' );
	$wp_customize->register_control_type( 'SZ_Nouveau_Nav_Customize_Control' );
	$sz_nouveau_options = sz_nouveau_get_appearance_settings(); 

	$wp_customize->add_panel( 'sz_nouveau_panel', array(
		'description' => __( 'Customize the appearance of SportsZone Nouveau Template pack.', 'sportszone' ),
		'title'       => _x( 'SportsZone Nouveau', 'Customizer Panel', 'sportszone' ),
		'priority'    => 200,
	) );

	/**
	 * Filters the SportsZone Nouveau customizer sections and their arguments.
	 *
	 * @since 3.0.0
	 *
	 * @param array $value Array of Customizer sections.
	 */
	$sections = apply_filters( 'sz_nouveau_customizer_sections', array(
		'sz_nouveau_general_settings' => array(
			'title'       => __( 'General BP Settings', 'sportszone' ),
			'panel'       => 'sz_nouveau_panel',
			'priority'    => 10,
			'description' => __( 'Configure general SportsZone appearance options.', 'sportszone' ),
		),
		'sz_nouveau_user_front_page' => array(
			'title'       => __( 'Member front page', 'sportszone' ),
			'panel'       => 'sz_nouveau_panel',
			'priority'    => 30,
			'description' => __( 'Configure the default front page for members.', 'sportszone' ),
		),
		'sz_nouveau_user_primary_nav' => array(
			'title'       => __( 'Member navigation', 'sportszone' ),
			'panel'       => 'sz_nouveau_panel',
			'priority'    => 50,
			'description' => __( 'Customize the navigation menu for members. In the preview window, navigate to a user to preview your changes.', 'sportszone' ),
		),
		'sz_nouveau_loops_layout' => array(
			'title'       => __( 'Loop layouts', 'sportszone' ),
			'panel'       => 'sz_nouveau_panel',
			'priority'    => 70,
			'description' => __( 'Set the number of columns to use for SportsZone loops.', 'sportszone' ),
		),
		'sz_nouveau_dir_layout' => array(
			'title'       => __( 'Directory layouts', 'sportszone' ),
			'panel'       => 'sz_nouveau_panel',
			'priority'    => 80,
			'description' => __( 'Select the layout style for directory content &amp; navigation.', 'sportszone' ),
		),
	) );

	// Add the sections to the customizer
	foreach ( $sections as $id_section => $section_args ) {
		$wp_customize->add_section( $id_section, $section_args );
	}

	/**
	 * Filters the SportsZone Nouveau customizer settings and their arguments.
	 *
	 * @since 3.0.0
	 *
	 * @param array $value Array of Customizer settings.
	 */
	$settings = apply_filters( 'sz_nouveau_customizer_settings', array(
		'sz_nouveau_appearance[avatar_style]' => array(
			'index'             => 'avatar_style',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[user_front_page]' => array(
			'index'             => 'user_front_page',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[user_front_bio]' => array(
			'index'             => 'user_front_bio',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[user_nav_display]' => array(
			'index'             => 'user_nav_display',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[user_nav_tabs]' => array(
			'index'             => 'user_nav_tabs',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[user_subnav_tabs]' => array(
			'index'             => 'user_subnav_tabs',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[user_nav_order]' => array(
			'index'             => 'user_nav_order',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'sz_nouveau_sanitize_nav_order',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[members_layout]' => array(
			'index'             => 'members_layout',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[members_group_layout]' => array(
			'index'             => 'members_group_layout',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[members_event_layout]' => array(
			'index'             => 'members_event_layout',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[members_friends_layout]' => array(
			'index'             => 'members_friends_layout',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[activity_dir_layout]' => array(
			'index'             => 'activity_dir_layout',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[activity_dir_tabs]' => array(
			'index'             => 'activity_dir_tabs',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[members_dir_layout]' => array(
			'index'             => 'members_dir_layout',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[members_dir_tabs]' => array(
			'index'             => 'members_dir_tabs',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[groups_dir_layout]' => array(
			'index'             => 'groups_dir_layout',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[events_dir_layout]' => array(
			'index'             => 'events_dir_layout',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[sites_dir_layout]' => array(
			'index'             => 'sites_dir_layout',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[sites_dir_tabs]' => array(
			'index'             => 'sites_dir_tabs',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
	) );

	// Add the settings
	foreach ( $settings as $id_setting => $setting_args ) {
		$args = array();

		if ( empty( $setting_args['index'] ) || ! isset( $sz_nouveau_options[ $setting_args['index'] ] ) ) {
			continue;
		}

		$args = array_merge( $setting_args, array( 'default' => $sz_nouveau_options[ $setting_args['index'] ] ) );

		$wp_customize->add_setting( $id_setting, $args );
	}

	$controls = array(
		'sz_site_avatars' => array(
			'label'      => __( 'Use the round style for member and group avatars.', 'sportszone' ),
			'section'    => 'sz_nouveau_general_settings',
			'settings'   => 'sz_nouveau_appearance[avatar_style]',
			'type'       => 'checkbox',
		),
		'user_front_page' => array(
			'label'      => __( 'Enable default front page for member profiles.', 'sportszone' ),
			'section'    => 'sz_nouveau_user_front_page',
			'settings'   => 'sz_nouveau_appearance[user_front_page]',
			'type'       => 'checkbox',
		),
		'user_front_bio' => array(
			'label'      => __( 'Display the biographical info from the member\'s WordPress profile.', 'sportszone' ),
			'section'    => 'sz_nouveau_user_front_page',
			'settings'   => 'sz_nouveau_appearance[user_front_bio]',
			'type'       => 'checkbox',
		),
		'user_nav_display' => array(
			'label'      => __( 'Display the member navigation vertically.', 'sportszone' ),
			'section'    => 'sz_nouveau_user_primary_nav',
			'settings'   => 'sz_nouveau_appearance[user_nav_display]',
			'type'       => 'checkbox',
		),
		'user_nav_tabs' => array(
			'label'      => __( 'Use tab styling for primary nav.', 'sportszone' ),
			'section'    => 'sz_nouveau_user_primary_nav',
			'settings'   => 'sz_nouveau_appearance[user_nav_tabs]',
			'type'       => 'checkbox',
		),
		'user_subnav_tabs' => array(
			'label'      => __( 'Use tab styling for secondary nav.', 'sportszone' ),
			'section'    => 'sz_nouveau_user_primary_nav',
			'settings'   => 'sz_nouveau_appearance[user_subnav_tabs]',
			'type'       => 'checkbox',
		),
		'user_nav_order' => array(
			'class'      => 'SZ_Nouveau_Nav_Customize_Control',
			'label'      => __( 'Reorder the primary navigation for a user.', 'sportszone' ),
			'section'    => 'sz_nouveau_user_primary_nav',
			'settings'   => 'sz_nouveau_appearance[user_nav_order]',
			'type'       => 'user',
		),
		'members_layout' => array(
			'label'      => __( 'Members', 'sportszone' ),
			'section'    => 'sz_nouveau_loops_layout',
			'settings'   => 'sz_nouveau_appearance[members_layout]',
			'type'       => 'select',
			'choices'    => sz_nouveau_customizer_grid_choices(),
		),
		'members_friends_layout' => array(
			'label'      => __( 'Member > Friends', 'sportszone' ),
			'section'    => 'sz_nouveau_loops_layout',
			'settings'   => 'sz_nouveau_appearance[members_friends_layout]',
			'type'       => 'select',
			'choices'    => sz_nouveau_customizer_grid_choices(),
		),
		'members_dir_layout' => array(
			'label'      => __( 'Use column navigation for the Members directory.', 'sportszone' ),
			'section'    => 'sz_nouveau_dir_layout',
			'settings'   => 'sz_nouveau_appearance[members_dir_layout]',
			'type'       => 'checkbox',
		),
		'members_dir_tabs' => array(
			'label'      => __( 'Use tab styling for Members directory navigation.', 'sportszone' ),
			'section'    => 'sz_nouveau_dir_layout',
			'settings'   => 'sz_nouveau_appearance[members_dir_tabs]',
			'type'       => 'checkbox',
		),
	);

	/**
	 * Filters the SportsZone Nouveau customizer controls and their arguments.
	 *
	 * @since 3.0.0
	 *
	 * @param array $value Array of Customizer controls.
	 */
	$controls = apply_filters( 'sz_nouveau_customizer_controls', $controls );

	// Add the controls to the customizer's section
	foreach ( $controls as $id_control => $control_args ) {
		if ( empty( $control_args['class'] ) ) {
			$wp_customize->add_control( $id_control, $control_args );
		} else {
			$wp_customize->add_control( new $control_args['class']( $wp_customize, $id_control, $control_args ) );
		}
	}
}
add_action( 'sz_customize_register', 'sz_nouveau_customize_register', 10, 1 );

/**
 * Enqueue needed JS for our customizer Settings & Controls
 *
 * @since 3.0.0
 */
function sz_nouveau_customizer_enqueue_scripts() {
	$min = sz_core_get_minified_asset_suffix();

	wp_enqueue_script(
		'sz-nouveau-customizer',
		trailingslashit( sz_get_theme_compat_url() ) . "js/customizer{$min}.js",
		array( 'jquery', 'jquery-ui-sortable', 'customize-controls', 'iris', 'underscore', 'wp-util' ),
		sz_nouveau()->version,
		true
	);

	/**
	 * Fires after Nouveau enqueues its required javascript.
	 *
	 * @since 3.0.0
	 */
	do_action( 'sz_nouveau_customizer_enqueue_scripts' );
}
add_action( 'customize_controls_enqueue_scripts', 'sz_nouveau_customizer_enqueue_scripts' );
