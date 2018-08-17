<?php
/**
 * Plugin Dependency Action Hooks.
 *
 * The purpose of the following hooks is to mimic the behavior of something
 * called 'plugin dependency' which enables a plugin to have plugins of their
 * own in a safe and reliable way.
 *
 * We do this in SportsZone by mirroring existing WordPress hooks in many places
 * allowing dependant plugins to hook into the SportsZone specific ones, thus
 * guaranteeing proper code execution only when SportsZone is active.
 *
 * The following functions are wrappers for hooks, allowing them to be
 * manually called and/or piggy-backed on top of other hooks if needed.
 *
 * @package SportsZone
 * @subpackage Core
 * @since 1.7.0
 */

/**
 * Fire the 'sz_include' action, where plugins should include files.
 *
 * @since 1.2.5
 */
function sz_include() {

	/**
	 * Fires inside the 'sz_include' function, where plugins should include files.
	 *
	 * @since 1.2.5
	 */
	do_action( 'sz_include' );
}

/**
 * Fire the 'sz_late_include' action for loading conditional files.
 *
 * @since 3.0.0
 */
function sz_late_include() {

	/**
	 * Fires the 'sz_late_include' action.
	 *
	 * Allow for conditional includes on certain pages.
	 *
	 * @since 3.0.0
	 */
	do_action( 'sz_late_include' );
}

/**
 * Fire the 'sz_setup_components' action, where plugins should initialize components.
 *
 * @since 1.6.0
 */
function sz_setup_components() {

	/**
	 * Fires inside the 'sz_setup_components' function, where plugins should initialize components.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_setup_components' );
}

/**
 * Fire the 'sz_setup_canonical_stack' action, where plugins should set up their canonical URL.
 *
 * @since 2.1.0
 */
function sz_setup_canonical_stack() {

	/**
	 * Fires inside the 'sz_setup_canonical_stack' function, where plugins should set up their canonical URL.
	 *
	 * @since 2.1.0
	 */
	do_action( 'sz_setup_canonical_stack' );
}

/**
 * Fire the 'sz_register_taxonomies' action, where plugins should register taxonomies.
 *
 * @since 2.2.0
 */
function sz_register_taxonomies() {

	/**
	 * Fires inside the 'sz_register_taxonomies' function, where plugins should register taxonomies.
	 *
	 * @since 2.2.0
	 */
	do_action( 'sz_register_taxonomies' );
}

/**
 * Fire the 'sz_register_post_types' action, where plugins should register post types.
 *
 * @since 2.5.0
 */
function sz_register_post_types() {

	/**
	 * Fires inside the 'sz_register_post_types' function, where plugins should register post types.
	 *
	 * @since 2.5.0
	 */
	do_action( 'sz_register_post_types' );
}

/**
 * Fire the 'sz_setup_globals' action, where plugins should initialize global settings.
 *
 * @since 1.2.0
 */
function sz_setup_globals() {

	/**
	 * Fires inside the 'sz_setup_globals' function, where plugins should initialize global settings.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_setup_globals' );
}

/**
 * Fire the 'sz_setup_nav' action, where plugins should register their navigation items.
 *
 * @since 1.2.0
 */
function sz_setup_nav() {

	/**
	 * Fires inside the 'sz_setup_nav' function, where plugins should register their navigation items.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_setup_nav' );
}

/**
 * Fire the 'sz_setup_admin_bar' action, where plugins should add items to the WP admin bar.
 *
 * @since 1.5.0
 */
function sz_setup_admin_bar() {
	if ( sz_use_wp_admin_bar() ) {

		/**
		 * Fires inside the 'sz_setup_admin_bar' function, where plugins should add items to the WP admin bar.
		 *
		 * This hook will only fire if sz_use_wp_admin_bar() returns true.
		 *
		 * @since 1.5.0
		 */
		do_action( 'sz_setup_admin_bar', array() );
	}
}

/**
 * Fire the 'sz_setup_title' action, where plugins should modify the page title.
 *
 * @since 1.5.0
 */
function sz_setup_title() {

	/**
	 * Fires inside the 'sz_setup_title' function, where plugins should modify the page title.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_setup_title' );
}

/**
 * Fire the 'sz_register_widgets' action, where plugins should register widgets.
 *
 * @since 1.2.0
 */
function sz_setup_widgets() {

	/**
	 * Fires inside the 'sz_register_widgets' function, where plugins should register widgets.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_register_widgets' );
}

/**
 * Fire the 'sz_register_member_types' action, where plugins should register member types.
 *
 * @since 2.3.0
 */
function sz_register_member_types() {

	/**
	 * Fires inside sz_register_member_types(), so plugins can register member types.
	 *
	 * @since 2.3.0
	 */
	do_action( 'sz_register_member_types' );
}

/**
 * Fire the 'sz_setup_cache_groups' action, where cache groups are registered.
 *
 * @since 2.2.0
 */
function sz_setup_cache_groups() {

	/**
	 * Fires inside the 'sz_setup_cache_groups' function, where cache groups are registered.
	 *
	 * @since 2.2.0
	 */
	do_action( 'sz_setup_cache_groups' );
}

/**
 * Fire the 'sz_setup_cache_events' action, where cache events are registered.
 *
 * @since 2.2.0
 */
function sz_setup_cache_events() {

	/**
	 * Fires inside the 'sz_setup_cache_events' function, where cache events are registered.
	 *
	 * @since 2.2.0
	 */
	do_action( 'sz_setup_cache_events' );
}

/**
 * Set up the currently logged-in user.
 *
 * @since 1.7.0
 *
 * @link https://sportszone.trac.wordpress.org/ticket/6046
 * @link https://core.trac.wordpress.org/ticket/24169
 */
function sz_setup_current_user() {

	/**
	 * Fires to set up the current user setup process.
	 *
	 * @since 1.7.0
	 */
	do_action( 'sz_setup_current_user' );
}

/**
 * Fire the 'sz_init' action, SportsZone's main initialization hook.
 *
 * @since 1.2.5
 */
function sz_init() {

	/**
	 * Fires inside the 'sz_init' function, SportsZone' main initialization hook.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_init' );
}

/**
 * Fire the 'sz_rest_api_init' action, where SportsZone registers REST API endpoints.
 *
 * @since 2.6.0
 */
function sz_rest_api_init() {

	/**
	 * Fires the 'sz_rest_api_init' function, where SportsZone registers REST API endpoints.
	 *
	 * @since 2.6.0
	 */
	do_action( 'sz_rest_api_init' );
}

/**
 * Fire the 'sz_customize_register' action when the Customizer has loaded,
 * allowing scripts and styles to be initialized.
 *
 * @since 2.5.0
 *
 * @param WP_Customize_Manager $customizer Customizer instance.
 */
function sz_customize_register( WP_Customize_Manager $customizer ) {

	/**
	 * Fires once the Customizer has loaded, allow scripts and styles to be initialized.
	 *
	 * @since 2.5.0
	 *
	 * @param WP_Customize_Manager $customizer Customizer instance.
	 */
	do_action( 'sz_customize_register', $customizer );
}

/**
 * Fire the 'sz_loaded' action, which fires after BP's core plugin files have been loaded.
 *
 * Attached to 'plugins_loaded'.
 *
 * @since 1.2.0
 */
function sz_loaded() {

	/**
	 * Fires inside the 'sz_loaded' function, which fires after BP's core plugin files have been loaded.
	 *
	 * @since 1.2.5
	 */
	do_action( 'sz_loaded' );
}

/**
 * Fire the 'sz_ready' action, which runs after BP is set up and the page is about to render.
 *
 * Attached to 'wp'.
 *
 * @since 1.6.0
 */
function sz_ready() {

	/**
	 * Fires inside the 'sz_ready' function, which runs after BP is set up and the page is about to render.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_ready' );
}

/**
 * Fire the 'sz_actions' action, which runs just before rendering.
 *
 * Attach potential template actions, such as catching form requests or routing
 * custom URLs.
 *
 * @since 1.5.0
 */
function sz_actions() {

	/**
	 * Fires inside the 'sz_actions' function, which runs just before rendering.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_actions' );
}

/**
 * Fire the 'sz_screens' action, which runs just before rendering.
 *
 * Runs just after 'sz_actions'. Use this hook to attach your template
 * loaders.
 *
 * @since 1.5.0
 */
function sz_screens() {

	/**
	 * Fires inside the 'sz_screens' function, which runs just before rendering.
	 *
	 * Runs just after 'sz_actions'. Use this hook to attach your template loaders.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_screens' );
}

/**
 * Fire 'sz_widgets_init', which runs after widgets have been set up.
 *
 * Hooked to 'widgets_init'.
 *
 * @since 1.6.0
 */
function sz_widgets_init() {

	/**
	 * Fires inside the 'sz_widgets_init' function, which runs after widgets have been set up.
	 *
	 * Hooked to 'widgets_init'.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_widgets_init' );
}

/**
 * Fire 'sz_head', which is used to hook scripts and styles in the <head>.
 *
 * Hooked to 'wp_head'.
 *
 * @since 1.6.0
 */
function sz_head() {

	/**
	 * Fires inside the 'sz_head' function, which runs on 'wp_head'.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_head' );
}

/** Theme Permissions *********************************************************/

/**
 * Fire the 'sz_template_redirect' action.
 *
 * Run at 'template_redirect', just before WordPress selects and loads a theme
 * template. The main purpose of this hook in SportsZone is to redirect users
 * who do not have the proper permission to access certain content.
 *
 * @since 1.6.0
 */
function sz_template_redirect() {

	/**
	 * Fires inside the 'sz_template_redirect' function.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_template_redirect' );
}

/** Theme Helpers *************************************************************/

/**
 * Fire the 'sz_register_theme_directory' action.
 *
 * The main action used registering theme directories.
 *
 * @since 1.5.0
 */
function sz_register_theme_directory() {

	/**
	 * Fires inside the 'sz_register_theme_directory' function.
	 *
	 * The main action used registering theme directories.
	 *
	 * @since 1.7.0
	 */
	do_action( 'sz_register_theme_directory' );
}

/**
 * Fire the 'sz_register_theme_packages' action.
 *
 * The main action used registering theme packages.
 *
 * @since 1.7.0
 */
function sz_register_theme_packages() {

	/**
	 * Fires inside the 'sz_register_theme_packages' function.
	 *
	 * @since 1.7.0
	 */
	do_action( 'sz_register_theme_packages' );
}

/**
 * Fire the 'sz_enqueue_scripts' action, where BP enqueues its CSS and JS.
 *
 * @since 1.6.0
 */
function sz_enqueue_scripts() {

	/**
	 * Fires inside the 'sz_enqueue_scripts' function, where BP enqueues its CSS and JS.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_enqueue_scripts' );
}

/**
 * Fires the 'sz_enqueue_embed_scripts' action in the <head> for BP oEmbeds.
 *
 * @since 2.6.0
 */
function sz_enqueue_embed_scripts() {
	if ( ! is_sportszone() ) {
		return;
	}

	/**
	 * Enqueue CSS and JS files for SportsZone embeds.
	 *
	 * @since 2.6.0
	 */
	do_action( 'sz_enqueue_embed_scripts' );
}

/**
 * Fire the 'sz_add_rewrite_tag' action, where BP adds its custom rewrite tags.
 *
 * @since 1.8.0
 */
function sz_add_rewrite_tags() {

	/**
	 * Fires inside the 'sz_add_rewrite_tags' function, where BP adds its custom rewrite tags.
	 *
	 * @since 1.8.0
	 */
	do_action( 'sz_add_rewrite_tags' );
}

/**
 * Fire the 'sz_add_rewrite_rules' action, where BP adds its custom rewrite rules.
 *
 * @since 1.9.0
 */
function sz_add_rewrite_rules() {

	/**
	 * Fires inside the 'sz_add_rewrite_rules' function, where BP adds its custom rewrite rules.
	 *
	 * @since 1.9.0
	 */
	do_action( 'sz_add_rewrite_rules' );
}

/**
 * Fire the 'sz_add_permastructs' action, where BP adds its BP-specific permalink structure.
 *
 * @since 1.9.0
 */
function sz_add_permastructs() {

	/**
	 * Fires inside the 'sz_add_permastructs' function, where BP adds its BP-specific permalink structure.
	 *
	 * @since 1.9.0
	 */
	do_action( 'sz_add_permastructs' );
}

/**
 * Fire the 'sz_setup_theme' action.
 *
 * The main purpose of 'sz_setup_theme' is give themes a place to load their
 * SportsZone-specific functionality.
 *
 * @since 1.6.0
 */
function sz_setup_theme() {

	/**
	 * Fires inside the 'sz_setup_theme' function.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_setup_theme' );
}

/**
 * Fire the 'sz_after_setup_theme' action.
 *
 * Piggy-back action for SportsZone-specific theme actions once the theme has
 * been set up and the theme's functions.php has loaded.
 *
 * Hooked to 'after_setup_theme' with a priority of 100. This allows plenty of
 * time for other themes to load their features, such as SportsZone support,
 * before our theme compatibility layer kicks in.
 *
 * @since 1.6.0
 */
function sz_after_setup_theme() {

	/**
	 * Fires inside the 'sz_after_setup_theme' function.
	 *
	 * @since 1.7.0
	 */
	do_action( 'sz_after_setup_theme' );
}

/** Theme Compatibility Filter ************************************************/

/**
 * Fire the 'sz_request' filter, a piggy-back of WP's 'request'.
 *
 * @since 1.7.0
 *
 * @see WP::parse_request() for a description of parameters.
 *
 * @param array $query_vars See {@link WP::parse_request()}.
 * @return array $query_vars See {@link WP::parse_request()}.
 */
function sz_request( $query_vars = array() ) {

	/**
	 * Filters the query_vars for the current request.
	 *
	 * @since 1.7.0
	 *
	 * @param array $query_vars Array of query variables.
	 */
	return apply_filters( 'sz_request', $query_vars );
}

/**
 * Fire the 'sz_login_redirect' filter, a piggy-back of WP's 'login_redirect'.
 *
 * @since 1.7.0
 *
 * @param string $redirect_to     See 'login_redirect'.
 * @param string $redirect_to_raw See 'login_redirect'.
 * @param bool   $user            See 'login_redirect'.
 * @return string
 */
function sz_login_redirect( $redirect_to = '', $redirect_to_raw = '', $user = false ) {

	/**
	 * Filters the URL to redirect to after login.
	 *
	 * @since 1.7.0
	 *
	 * @param string           $redirect_to     The redirect destination URL.
	 * @param string           $redirect_to_raw The requested redirect destination URL passed as a parameter.
	 * @param WP_User|WP_Error $user            WP_User object if login was successful, WP_Error object otherwise.
	 */
	return apply_filters( 'sz_login_redirect', $redirect_to, $redirect_to_raw, $user );
}

/**
 * Fire 'sz_template_include', main filter used for theme compatibility and displaying custom BP theme files.
 *
 * Hooked to 'template_include'.
 *
 * @since 1.6.0
 *
 * @param string $template See 'template_include'.
 * @return string Template file to use.
 */
function sz_template_include( $template = '' ) {

	/**
	 * Filters the template to use with template_include.
	 *
	 * @since 1.6.0
	 *
	 * @param string $template The path of the template to include.
	 */
	return apply_filters( 'sz_template_include', $template );
}

/**
 * Fire the 'sz_generate_rewrite_rules' action, where BP generates its rewrite rules.
 *
 * @since 1.7.0
 *
 * @param WP_Rewrite $wp_rewrite See 'generate_rewrite_rules'.
 */
function sz_generate_rewrite_rules( $wp_rewrite ) {

	/**
	 * Fires inside the 'sz_generate_rewrite_rules' function.
	 *
	 * @since 1.7.0
	 *
	 * @param WP_Rewrite $wp_rewrite WP_Rewrite object. Passed by reference.
	 */
	do_action_ref_array( 'sz_generate_rewrite_rules', array( &$wp_rewrite ) );
}

/**
 * Fire the 'sz_allowed_themes' filter.
 *
 * Filter the allowed themes list for SportsZone-specific themes.
 *
 * @since 1.7.0
 *
 * @param array $themes The path of the template to include.
 * @return array
 */
function sz_allowed_themes( $themes ) {

	/**
	 * Filters the allowed themes list for SportsZone-specific themes.
	 *
	 * @since 1.7.0
	 *
	 * @param string $template The path of the template to include.
	 */
	return apply_filters( 'sz_allowed_themes', $themes );
}

/** Requests ******************************************************************/

/**
 * The main action used for handling theme-side POST requests.
 *
 * @since 1.9.0
 */
function sz_post_request() {

	// Bail if not a POST action.
	if ( ! sz_is_post_request() ) {
		return;
	}

	// Bail if no action.
	if ( empty( $_POST['action'] ) ) {
		return;
	}

	// Sanitize the POST action.
	$action = sanitize_key( $_POST['action'] );

	/**
	 * Fires at the end of the sz_post_request function.
	 *
	 * This dynamic action is probably the one you want to use. It narrows down
	 * the scope of the 'action' without needing to check it in your function.
	 *
	 * @since 1.9.0
	 */
	do_action( 'sz_post_request_' . $action );

	/**
	 * Fires at the end of the sz_post_request function.
	 *
	 * Use this static action if you don't mind checking the 'action' yourself.
	 *
	 * @since 1.9.0
	 *
	 * @param string $action The action being run.
	 */
	do_action( 'sz_post_request',   $action );
}

/**
 * The main action used for handling theme-side GET requests.
 *
 * @since 1.9.0
 */
function sz_get_request() {

	// Bail if not a POST action.
	if ( ! sz_is_get_request() ) {
		return;
	}

	// Bail if no action.
	if ( empty( $_GET['action'] ) ) {
		return;
	}

	// Sanitize the GET action.
	$action = sanitize_key( $_GET['action'] );

	/**
	 * Fires at the end of the sz_get_request function.
	 *
	 * This dynamic action is probably the one you want to use. It narrows down
	 * the scope of the 'action' without needing to check it in your function.
	 *
	 * @since 1.9.0
	 */
	do_action( 'sz_get_request_' . $action );

	/**
	 * Fires at the end of the sz_get_request function.
	 *
	 * Use this static action if you don't mind checking the 'action' yourself.
	 *
	 * @since 1.9.0
	 *
	 * @param string $action The action being run.
	 */
	do_action( 'sz_get_request',   $action );
}
