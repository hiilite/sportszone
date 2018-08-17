<?php
/**
 * SportsZone Filters & Actions.
 *
 * This file contains the actions and filters that are used through-out SportsZone.
 * They are consolidated here to make searching for them easier, and to help
 * developers understand at a glance the order in which things occur.
 *
 * @package SportsZone
 * @subpackage Hooks
 * @since 1.6.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Attach SportsZone to WordPress.
 *
 * SportsZone uses its own internal actions to help aid in third-party plugin
 * development, and to limit the amount of potential future code changes when
 * updates to WordPress core occur.
 *
 * These actions exist to create the concept of 'plugin dependencies'. They
 * provide a safe way for plugins to execute code *only* when SportsZone is
 * installed and activated, without needing to do complicated guesswork.
 *
 * For more information on how this works, see the 'Plugin Dependency' section
 * near the bottom of this file.
 *
 *           v--WordPress Actions       v--SportsZone Sub-actions
 */
add_action( 'plugins_loaded',          'sz_loaded',                 10    );
add_action( 'init',                    'sz_init',                   10    );
add_action( 'rest_api_init',           'sz_rest_api_init',          20    ); // After WP core.
add_action( 'customize_register',      'sz_customize_register',     20    ); // After WP core.
add_action( 'parse_query',             'sz_parse_query',            2     ); // Early for overrides.
add_action( 'wp',                      'sz_ready',                  10    );
add_action( 'set_current_user',        'sz_setup_current_user',     10    );
add_action( 'setup_theme',             'sz_setup_theme',            10    );
add_action( 'after_setup_theme',       'sz_after_setup_theme',      100   ); // After WP themes.
add_action( 'wp_enqueue_scripts',      'sz_enqueue_scripts',        10    );
add_action( 'enqueue_embed_scripts',   'sz_enqueue_embed_scripts',  10    );
add_action( 'admin_bar_menu',          'sz_setup_admin_bar',        20    ); // After WP core.
add_action( 'template_redirect',       'sz_template_redirect',      10    );
add_action( 'widgets_init',            'sz_widgets_init',           10    );
add_action( 'generate_rewrite_rules',  'sz_generate_rewrite_rules', 10    );

/**
 * The sz_loaded hook - Attached to 'plugins_loaded' above.
 *
 * Attach various loader actions to the sz_loaded action.
 * The load order helps to execute code at the correct time.
 *                                                      v---Load order
 */
add_action( 'sz_loaded', 'sz_setup_components',         2  );
add_action( 'sz_loaded', 'sz_include',                  4  );
add_action( 'sz_loaded', 'sz_setup_option_filters',     5  );
add_action( 'sz_loaded', 'sz_setup_cache_groups',       5  );
add_action( 'sz_loaded', 'sz_setup_cache_events',       5  );
add_action( 'sz_loaded', 'sz_setup_widgets',            6  );
add_action( 'sz_loaded', 'sz_register_theme_packages',  12 );
add_action( 'sz_loaded', 'sz_register_theme_directory', 14 );

/**
 * The sz_init hook - Attached to 'init' above.
 *
 * Attach various initialization actions to the sz_init action.
 * The load order helps to execute code at the correct time.
 *                                                   v---Load order
 */
add_action( 'sz_init', 'sz_register_post_types',     2  );
add_action( 'sz_init', 'sz_register_taxonomies',     2  );
add_action( 'sz_init', 'sz_core_set_uri_globals',    2  );
add_action( 'sz_init', 'sz_setup_globals',           4  );
add_action( 'sz_init', 'sz_setup_canonical_stack',   5  );
add_action( 'sz_init', 'sz_setup_nav',               6  );
add_action( 'sz_init', 'sz_setup_title',             8  );
add_action( 'sz_init', 'sz_core_load_admin_bar_css', 12 );
add_action( 'sz_init', 'sz_add_rewrite_tags',        20 );
add_action( 'sz_init', 'sz_add_rewrite_rules',       30 );
add_action( 'sz_init', 'sz_add_permastructs',        40 );

/**
 * The sz_register_taxonomies hook - Attached to 'sz_init' @ priority 2 above.
 */
add_action( 'sz_register_taxonomies', 'sz_register_member_types' );

/**
 * Late includes.
 *
 * Run after the canonical stack is setup to allow for conditional includes
 * on certain pages.
 */
add_action( 'sz_setup_canonical_stack', 'sz_late_include', 20 );

/**
 * The sz_template_redirect hook - Attached to 'template_redirect' above.
 *
 * Attach various template actions to the sz_template_redirect action.
 * The load order helps to execute code at the correct time.
 *
 * Note that we currently use template_redirect versus template include because
 * SportsZone is a bully and overrides the existing themes output in many
 * places. This won't always be this way, we promise.
 *                                                           v---Load order
 */
add_action( 'sz_template_redirect', 'sz_redirect_canonical', 2  );
add_action( 'sz_template_redirect', 'sz_actions',            4  );
add_action( 'sz_template_redirect', 'sz_screens',            6  );
add_action( 'sz_template_redirect', 'sz_post_request',       10 );
add_action( 'sz_template_redirect', 'sz_get_request',        10 );

/**
 * Add the SportsZone functions file and the Theme Compat Default features.
 */
add_action( 'sz_after_setup_theme', 'sz_check_theme_template_pack_dependency',   -10 );
add_action( 'sz_after_setup_theme', 'sz_load_theme_functions',                    1  );
add_action( 'sz_after_setup_theme', 'sz_register_theme_compat_default_features',  10 );

// Load the admin.
if ( is_admin() ) {
	add_action( 'sz_loaded', 'sz_admin' );
}

// Activation redirect.
add_action( 'sz_activation', 'sz_add_activation_redirect' );

// Email unsubscribe.
add_action( 'sz_get_request_unsubscribe', 'sz_email_unsubscribe_handler' );
