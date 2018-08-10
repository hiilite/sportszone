<?php
/**
 * SportsZone Customizer implementation for email.
 *
 * @package SportsZone
 * @subpackage Core
 * @since 2.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Initialize the Customizer for emails.
 *
 * @since 2.5.0
 *
 * @param WP_Customize_Manager $wp_customize The Customizer object.
 */
function sz_email_init_customizer( WP_Customize_Manager $wp_customize ) {
	if ( ! sz_is_email_customizer() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		return;
	}

	$wp_customize->add_panel( 'sz_mailtpl', array(
		'description' => __( 'Customize the appearance of emails sent by SportsZone.', 'sportszone' ),
		'title'       => _x( 'SportsZone Emails', 'screen heading', 'sportszone' ),
	) );

	$sections = sz_email_get_customizer_sections();
	foreach( $sections as $section_id => $args ) {
		$wp_customize->add_section( $section_id, $args );
	}

	$settings = sz_email_get_customizer_settings();
	foreach( $settings as $setting_id => $args ) {
		$wp_customize->add_setting( $setting_id, $args );
	}

	/**
	 * Fires to let plugins register extra Customizer controls for emails.
	 *
	 * @since 2.5.0
	 *
	 * @param WP_Customize_Manager $wp_customize The Customizer object.
	 */
	do_action( 'sz_email_customizer_register_sections', $wp_customize );

	$controls = sz_email_get_customizer_controls();
	foreach ( $controls as $control_id => $args ) {
		$wp_customize->add_control( new $args['class']( $wp_customize, $control_id, $args ) );
	}

	/*
	 * Hook actions/filters for further configuration.
	 */

	add_filter( 'customize_section_active', 'sz_email_customizer_hide_sections', 12, 2 );

	if ( is_customize_preview() ) {
		/*
		 * Enqueue scripts/styles for the Customizer's preview window.
		 *
		 * Scripts can't be registered in sz_core_register_common_styles() etc because
		 * the Customizer loads very, very early.
		 */
		$sz  = sportszone();
		$min = sz_core_get_minified_asset_suffix();

		wp_enqueue_script(
			'sz-customizer-receiver-emails',
			"{$sz->plugin_url}sz-core/admin/js/customizer-receiver-emails{$min}.js",
			array( 'customize-preview' ),
			sz_get_version(),
			true
		);

		// Include the preview loading style.
		add_action( 'wp_footer', array( $wp_customize, 'customize_preview_loading_style' ) );
	}
}
add_action( 'sz_customize_register', 'sz_email_init_customizer' );

/**
 * Are we looking at the email customizer?
 *
 * @since 2.5.0
 *
 * @return bool
 */
function sz_is_email_customizer() {
	return isset( $_GET['sz_customizer'] ) && $_GET['sz_customizer'] === 'email';
}

/**
 * Only show email sections in the Customizer.
 *
 * @since 2.5.0
 *
 * @param bool                 $active  Whether the Customizer section is active.
 * @param WP_Customize_Section $section {@see WP_Customize_Section} instance.
 * @return bool
 */
function sz_email_customizer_hide_sections( $active, $section ) {
	if ( ! sz_is_email_customizer() ) {
		return $active;
	}

	return in_array( $section->id, array_keys( sz_email_get_customizer_sections() ), true );
}

/**
 * Get Customizer sections for emails.
 *
 * @since 2.5.0
 *
 * @return array
 */
function sz_email_get_customizer_sections() {

	/**
	 * Filter Customizer sections for emails.
	 *
	 * @since 2.5.0
	 *
	 * @param array $sections Email Customizer sections to add.
	 */
	return apply_filters( 'sz_email_get_customizer_sections', array(
		'section_sz_mailtpl_header' => array(
			'capability' => 'sz_moderate',
			'panel'      => 'sz_mailtpl',
			'title'      => _x( 'Header', 'email', 'sportszone' ),
		),
		'section_sz_mailtpl_body' => array(
			'capability' => 'sz_moderate',
			'panel'      => 'sz_mailtpl',
			'title'      => _x( 'Body', 'email', 'sportszone' ),
		),
		'section_sz_mailtpl_footer' => array(
			'capability' => 'sz_moderate',
			'panel'      => 'sz_mailtpl',
			'title'      => _x( 'Footer', 'email', 'sportszone' ),
		),
	) );
}

/**
 * Get Customizer settings for emails.
 *
 * @since 2.5.0
 *
 * @return array
 */
function sz_email_get_customizer_settings() {
	$defaults = sz_email_get_appearance_settings();

	/**
	 * Filter Customizer settings for emails.
	 *
	 * @since 2.5.0
	 *
	 * @param array $settings Email Customizer settings to add.
	 */
	return apply_filters( 'sz_email_get_customizer_settings', array(
		'sz_email_options[email_bg]' => array(
			'capability'        => 'sz_moderate',
			'default'           => $defaults['email_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
			'type'              => 'option',
		),
		'sz_email_options[header_bg]' => array(
			'capability'        => 'sz_moderate',
			'default'           => $defaults['header_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
			'type'              => 'option',
		),
		'sz_email_options[header_text_size]' => array(
			'capability'        => 'sz_moderate',
			'default'           => $defaults['header_text_size'],
			'sanitize_callback' => 'absint',
			'transport'         => 'postMessage',
			'type'              => 'option',
		),
		'sz_email_options[header_text_color]' => array(
			'capability'        => 'sz_moderate',
			'default'           => $defaults['header_text_color'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
			'type'              => 'option',
		),
		'sz_email_options[highlight_color]' => array(
			'capability'        => 'sz_moderate',
			'default'           => $defaults['highlight_color'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
			'type'              => 'option',
		),
		'sz_email_options[body_bg]' => array(
			'capability'        => 'sz_moderate',
			'default'           => $defaults['body_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
			'type'              => 'option',
		),
		'sz_email_options[body_text_size]' => array(
			'capability'        => 'sz_moderate',
			'default'           => $defaults['body_text_size'],
			'sanitize_callback' => 'absint',
			'transport'         => 'postMessage',
			'type'              => 'option',
		),
		'sz_email_options[body_text_color]' => array(
			'capability'        => 'sz_moderate',
			'default'           => $defaults['body_text_color'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
			'type'              => 'option',
		),
		'sz_email_options[footer_text]' => array(
			'capability'        => 'sz_moderate',
			'default'           => $defaults['footer_text'],
			'sanitize_callback' => 'wp_filter_post_kses',
			'transport'         => 'postMessage',
			'type'              => 'option',
		),
		'sz_email_options[footer_bg]' => array(
			'capability'        => 'sz_moderate',
			'default'           => $defaults['footer_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
			'type'              => 'option',
		),
		'sz_email_options[footer_text_size]' => array(
			'capability'        => 'sz_moderate',
			'default'           => $defaults['footer_text_size'],
			'sanitize_callback' => 'absint',
			'transport'         => 'postMessage',
			'type'              => 'option',
		),
		'sz_email_options[footer_text_color]' => array(
			'capability'        => 'sz_moderate',
			'default'           => $defaults['footer_text_color'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
			'type'              => 'option',
		),
	) );
}

/**
 * Get Customizer controls for emails.
 *
 * @since 2.5.0
 *
 * @return array
 */
function sz_email_get_customizer_controls() {

	/**
	 * Filter Customizer controls for emails.
	 *
	 * @since 2.5.0
	 *
	 * @param array $controls Email Customizer controls to add.
	 */
	return apply_filters( 'sz_email_get_customizer_controls', array(
		'sz_mailtpl_email_bg' => array(
			'class'    => 'WP_Customize_Color_Control',
			'label'    => __( 'Email background color', 'sportszone' ),
			'section'  => 'section_sz_mailtpl_header',
			'settings' => 'sz_email_options[email_bg]',
		),

		'sz_mailtpl_header_bg' => array(
			'class'    => 'WP_Customize_Color_Control',
			'label'    => __( 'Header background color', 'sportszone' ),
			'section'  => 'section_sz_mailtpl_header',
			'settings' => 'sz_email_options[header_bg]',
		),

		'sz_mailtpl_highlight_color' => array(
			'class'       => 'WP_Customize_Color_Control',
			'description' => __( 'Applied to links and other decorative areas.', 'sportszone' ),
			'label'       => __( 'Highlight color', 'sportszone' ),
			'section'     => 'section_sz_mailtpl_header',
			'settings'    => 'sz_email_options[highlight_color]',
		),

		'sz_mailtpl_header_text_color' => array(
			'class'    => 'WP_Customize_Color_Control',
			'label'    => __( 'Text color', 'sportszone' ),
			'section'  => 'section_sz_mailtpl_header',
			'settings' => 'sz_email_options[header_text_color]',
		),

		'sz_mailtpl_header_text_size' => array(
			'class'    => 'SZ_Customizer_Control_Range',
			'label'    => __( 'Text size', 'sportszone' ),
			'section'  => 'section_sz_mailtpl_header',
			'settings' => 'sz_email_options[header_text_size]',

			'input_attrs' => array(
				'max'  => 100,
				'min'  => 1,
				'step' => 1,
			),
		),


		'sz_mailtpl_body_bg' => array(
			'class'    => 'WP_Customize_Color_Control',
			'label'    => __( 'Background color', 'sportszone' ),
			'section'  => 'section_sz_mailtpl_body',
			'settings' => 'sz_email_options[body_bg]',
		),


		'sz_mailtpl_body_text_color' => array(
			'class'    => 'WP_Customize_Color_Control',
			'label'    => __( 'Text color', 'sportszone' ),
			'section'  => 'section_sz_mailtpl_body',
			'settings' => 'sz_email_options[body_text_color]',
		),

		'sz_mailtpl_body_text_size' => array(
			'class'    => 'SZ_Customizer_Control_Range',
			'label'    => __( 'Text size', 'sportszone' ),
			'section'  => 'section_sz_mailtpl_body',
			'settings' => 'sz_email_options[body_text_size]',

			'input_attrs' => array(
				'max'  => 24,
				'min'  => 8,
				'step' => 1,
			),
		),


		'sz_mailtpl_footer_text' => array(
			'class'       => 'WP_Customize_Control',
			'description' => __('Change the email footer here', 'sportszone' ),
			'label'       => __( 'Footer text', 'sportszone' ),
			'section'     => 'section_sz_mailtpl_footer',
			'settings'    => 'sz_email_options[footer_text]',
			'type'        => 'textarea',
		),

		'sz_mailtpl_footer_bg' => array(
			'class'    => 'WP_Customize_Color_Control',
			'label'    => __( 'Background color', 'sportszone' ),
			'section'  => 'section_sz_mailtpl_footer',
			'settings' => 'sz_email_options[footer_bg]',
		),

		'sz_mailtpl_footer_text_color' => array(
			'class'    => 'WP_Customize_Color_Control',
			'label'    => __( 'Text color', 'sportszone' ),
			'section'  => 'section_sz_mailtpl_footer',
			'settings' => 'sz_email_options[footer_text_color]',
		),

		'sz_mailtpl_footer_text_size' => array(
			'class'    => 'SZ_Customizer_Control_Range',
			'label'    => __( 'Text size', 'sportszone' ),
			'section'  => 'section_sz_mailtpl_footer',
			'settings' => 'sz_email_options[footer_text_size]',

			'input_attrs' => array(
				'max'  => 24,
				'min'  => 8,
				'step' => 1,
			),
		),
	) );
}

/**
 * Implements a JS redirect to the Customizer, previewing a randomly selected email.
 *
 * @since 2.5.0
 */
function sz_email_redirect_to_customizer() {
	$switched = false;

	// Switch to the root blog, where the email posts live.
	if ( ! sz_is_root_blog() ) {
		switch_to_blog( sz_get_root_blog_id() );
		$switched = true;
	}

	$email = get_posts( array(
		'fields'           => 'ids',
		'orderby'          => 'rand',
		'post_status'      => 'publish',
		'post_type'        => sz_get_email_post_type(),
		'posts_per_page'   => 1,
		'suppress_filters' => false,
	) );

	$preview_url = admin_url();

	if ( $email ) {
		$preview_url = get_post_permalink( $email[0] ) . '&sz_customizer=email';
	}

	$redirect_url = add_query_arg(
		array(
			'autofocus[panel]' => 'sz_mailtpl',
			'sz_customizer'    => 'email',
			'return'           => rawurlencode( admin_url() ),
			'url'              => rawurlencode( $preview_url ),
		),
		admin_url( 'customize.php' )
	);

	if ( $switched ) {
		restore_current_blog();
	}

	printf(
		'<script type="text/javascript">window.location = "%s";</script>',
		esc_url_raw( $redirect_url )
	);

	exit;
}
