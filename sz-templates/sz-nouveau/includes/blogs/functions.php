<?php
/**
 * Blogs functions
 *
 * @since 3.0.0
 * @version 3.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * @since 3.0.0
 */
function sz_nouveau_get_blogs_directory_nav_items() {
	$nav_items = array();

	$nav_items['all'] = array(
		'component' => 'blogs',
		'slug'      => 'all', // slug is used because SZ_Core_Nav requires it, but it's the scope
		'li_class'  => array( 'selected' ),
		'link'      => sz_get_root_domain() . '/' . sz_get_blogs_root_slug(),
		'text'      => __( 'All Sites', 'sportszone' ),
		'count'     => sz_get_total_blog_count(),
		'position'  => 5,
	);

	if ( is_user_logged_in() ) {
		$my_blogs_count = sz_get_total_blog_count_for_user( sz_loggedin_user_id() );

		// If the user has blogs create a nav item
		if ( $my_blogs_count ) {
			$nav_items['personal'] = array(
				'component' => 'blogs',
				'slug'      => 'personal', // slug is used because SZ_Core_Nav requires it, but it's the scope
				'li_class'  => array(),
				'link'      => sz_loggedin_user_domain() . sz_get_blogs_slug(),
				'text'      => __( 'My Sites', 'sportszone' ),
				'count'     => $my_blogs_count,
				'position'  => 15,
			);
		}

		// If the user can create blogs, add the create nav
		if ( sz_blog_signup_enabled() ) {
			$nav_items['create'] = array(
				'component' => 'blogs',
				'slug'      => 'create', // slug is used because SZ_Core_Nav requires it, but it's the scope
				'li_class'  => array( 'no-ajax', 'site-create', 'create-button' ),
				'link'      => trailingslashit( sz_get_blogs_directory_permalink() . 'create' ),
				'text'      => __( 'Create a Site', 'sportszone' ),
				'count'     => false,
				'position'  => 999,
			);
		}
	}

	// Check for the deprecated hook :
	$extra_nav_items = sz_nouveau_parse_hooked_dir_nav( 'sz_blogs_directory_blog_types', 'blogs', 20 );

	if ( ! empty( $extra_nav_items ) ) {
		$nav_items = array_merge( $nav_items, $extra_nav_items );
	}

	/**
	 * Use this filter to introduce your custom nav items for the blogs directory.
	 *
	 * @since 3.0.0
	 *
	 * @param  array $nav_items The list of the blogs directory nav items.
	 */
	return apply_filters( 'sz_nouveau_get_blogs_directory_nav_items', $nav_items );
}

/**
 * Get Dropdown filters for the blogs component
 *
 * @since 3.0.0
 *
 * @param string $context 'directory' or 'user'
 *
 * @return array the filters
 */
function sz_nouveau_get_blogs_filters( $context = '' ) {
	if ( empty( $context ) ) {
		return array();
	}

	$action = '';
	if ( 'user' === $context ) {
		$action = 'sz_member_blog_order_options';
	} elseif ( 'directory' === $context ) {
		$action = 'sz_blogs_directory_order_options';
	}

	/**
	 * Recommended, filter here instead of adding an action to 'sz_member_blog_order_options'
	 * or 'sz_blogs_directory_order_options'
	 *
	 * @since 3.0.0
	 *
	 * @param array  the blogs filters.
	 * @param string the context.
	 */
	$filters = apply_filters( 'sz_nouveau_get_blogs_filters', array(
		'active'       => __( 'Last Active', 'sportszone' ),
		'newest'       => __( 'Newest', 'sportszone' ),
		'alphabetical' => __( 'Alphabetical', 'sportszone' ),
	), $context );

	if ( $action ) {
		return sz_nouveau_parse_hooked_options( $action, $filters );
	}

	return $filters;
}

/**
 * Catch the arguments for buttons
 *
 * @since 3.0.0
 *
 * @param array $buttons The arguments of the button that SportsZone is about to create.
 *
 * @return array An empty array to stop the button creation process.
 */
function sz_nouveau_blogs_catch_button_args( $button = array() ) {
	// Globalize the arguments so that we can use it  in sz_nouveau_get_blogs_buttons().
	sz_nouveau()->blogs->button_args = $button;

	// return an empty array to stop the button creation process
	return array();
}

/**
 * Add settings to the customizer for the blogs component.
 *
 * @since 3.0.0
 *
 * @param array $settings the settings to add.
 *
 * @return array the settings to add.
 */
function sz_nouveau_blogs_customizer_settings( $settings = array() ) {
	return array_merge( $settings, array(
		'sz_nouveau_appearance[blogs_layout]' => array(
			'index'             => 'blogs_layout',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
	) );
}

/**
 * Add controls for the settings of the customizer for the blogs component.
 *
 * @since 3.0.0
 *
 * @param array $controls the controls to add.
 *
 * @return array the controls to add.
 */
function sz_nouveau_blogs_customizer_controls( $controls = array() ) {
	return array_merge( $controls, array(
		'blogs_layout' => array(
			'label'      => __( 'Sites loop:', 'sportszone' ),
			'section'    => 'sz_nouveau_loops_layout',
			'settings'   => 'sz_nouveau_appearance[blogs_layout]',
			'type'       => 'select',
			'choices'    => sz_nouveau_customizer_grid_choices(),
		),
		'sites_dir_layout' => array(
			'label'      => __( 'Use column navigation for the Sites directory.', 'sportszone' ),
			'section'    => 'sz_nouveau_dir_layout',
			'settings'   => 'sz_nouveau_appearance[sites_dir_layout]',
			'type'       => 'checkbox',
		),
		'sites_dir_tabs' => array(
			'label'      => __( 'Use tab styling for Sites directory navigation.', 'sportszone' ),
			'section'    => 'sz_nouveau_dir_layout',
			'settings'   => 'sz_nouveau_appearance[sites_dir_tabs]',
			'type'       => 'checkbox',
		),
	) );
}

/**
 * Inline script to toggle the signup blog form
 *
 * @since 3.0.0
 *
 * @return string Javascript output
 */
function sz_nouveau_get_blog_signup_inline_script() {
	return '
		( function( $ ) {
			if ( $( \'body\' ).hasClass( \'register\' ) ) {
				var blog_checked = $( \'#signup_with_blog\' );

				// hide "Blog Details" block if not checked by default
				if ( ! blog_checked.prop( \'checked\' ) ) {
					$( \'#blog-details\' ).toggle();
				}

				// toggle "Blog Details" block whenever checkbox is checked
				blog_checked.change( function( event ) {
					// Toggle HTML5 required attribute.
					$.each( $( \'#blog-details\' ).find( \'[aria-required]\' ), function( i, input ) {
						$( input ).prop( \'required\',  $( event.target ).prop( \'checked\' ) );
					} );

					$( \'#blog-details\' ).toggle();
				} );
			}
		} )( jQuery );
	';
}

/**
 * Filter sz_get_blog_class().
 * Adds a class if blog item has a latest post.
 *
 * @since 3.0.0
 */
function sz_nouveau_blog_loop_item_has_lastest_post( $classes ) {
	if ( sz_get_blog_latest_post_title() ) {
		$classes[] = 'has-latest-post';
	}

	return $classes;
}
add_filter( 'sz_get_blog_class', 'sz_nouveau_blog_loop_item_has_lastest_post' );
