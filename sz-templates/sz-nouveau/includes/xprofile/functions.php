<?php
/**
 * xProfile functions
 *
 * @since 3.0.0
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register Scripts for the xProfile component
 *
 * @since 3.0.0
 *
 * @param array $scripts The array of scripts to register
 *
 * @return array The same array with the specific groups scripts.
 */
function sz_nouveau_xprofile_register_scripts( $scripts = array() ) {
	if ( ! isset( $scripts['sz-nouveau'] ) ) {
		return $scripts;
	}

	return array_merge( $scripts, array(
		'sz-nouveau-xprofile' => array(
			'file'         => 'js/sportszone-xprofile%s.js',
			'dependencies' => array( 'sz-nouveau' ),
			'footer'       => true,
		),
	) );
}

/**
 * Enqueue the xprofile scripts
 *
 * @since 3.0.0
 */
function sz_nouveau_xprofile_enqueue_scripts() {
	if ( ! sz_is_user_profile_edit() && ! sz_is_register_page() ) {
		return;
	}

	wp_enqueue_script( 'sz-nouveau-xprofile' );
}
