<?php
/**
 * SportsZone Core Loader.
 *
 * Core contains the commonly used functions, classes, and APIs.
 *
 * @package SportsZone
 * @subpackage Core
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Creates the Core component.
 *
 * @since 1.5.0
 */
class SZ_Core extends SZ_Component {

	/**
	 * Start the members component creation process.
	 *
	 * @since 1.5.0
	 *
	 */
	public function __construct() {
		parent::start(
			'core',
			__( 'SportsZone Core', 'sportszone' ),
			sportszone()->plugin_dir
		);

		$this->bootstrap();
	}

	/**
	 * Populate the global data needed before SportsZone can continue.
	 *
	 * This involves figuring out the currently required, activated, deactivated,
	 * and optional components.
	 *
	 * @since 1.5.0
	 */
	private function bootstrap() {
		$sz = sportszone();

		/**
		 * Fires before the loading of individual components and after SportsZone Core.
		 *
		 * Allows plugins to run code ahead of the other components.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_core_loaded' );

		/** Components *******************************************************
		 */

		/**
		 * Filters the included and optional components.
		 *
		 * @since 1.5.0
		 *
		 * @param array $value Array of included and optional components.
		 */
		$sz->optional_components = apply_filters( 'sz_optional_components', array( 'activity', 'blogs', 'friends', 'groups', 'events', 'messages', 'notifications', 'settings', 'xprofile' ) );

		/**
		 * Filters the required components.
		 *
		 * @since 1.5.0
		 *
		 * @param array $value Array of required components.
		 */
		$sz->required_components = apply_filters( 'sz_required_components', array( 'members' ) );

		// Get a list of activated components.
		if ( $active_components = sz_get_option( 'sz-active-components' ) ) {

			/** This filter is documented in sz-core/admin/sz-core-admin-components.php */
			$sz->active_components      = apply_filters( 'sz_active_components', $active_components );

			/**
			 * Filters the deactivated components.
			 *
			 * @since 1.0.0
			 *
			 * @param array $value Array of deactivated components.
			 */
			$sz->deactivated_components = apply_filters( 'sz_deactivated_components', array_values( array_diff( array_values( array_merge( $sz->optional_components, $sz->required_components ) ), array_keys( $sz->active_components ) ) ) );

		// Pre 1.5 Backwards compatibility.
		} elseif ( $deactivated_components = sz_get_option( 'sz-deactivated-components' ) ) {

			// Trim off namespace and filename.
			foreach ( array_keys( (array) $deactivated_components ) as $component ) {
				$trimmed[] = str_replace( '.php', '', str_replace( 'sz-', '', $component ) );
			}

			/** This filter is documented in sz-core/sz-core-loader.php */
			$sz->deactivated_components = apply_filters( 'sz_deactivated_components', $trimmed );

			// Setup the active components.
			$active_components     = array_fill_keys( array_diff( array_values( array_merge( $sz->optional_components, $sz->required_components ) ), array_values( $sz->deactivated_components ) ), '1' );

			/** This filter is documented in sz-core/admin/sz-core-admin-components.php */
			$sz->active_components = apply_filters( 'sz_active_components', $sz->active_components );

		// Default to all components active.
		} else {

			// Set globals.
			$sz->deactivated_components = array();

			// Setup the active components.
			$active_components     = array_fill_keys( array_values( array_merge( $sz->optional_components, $sz->required_components ) ), '1' );

			/** This filter is documented in sz-core/admin/sz-core-admin-components.php */
			$sz->active_components = apply_filters( 'sz_active_components', $sz->active_components );
		}

		// Loop through optional components.
		foreach( $sz->optional_components as $component ) {
			if ( sz_is_active( $component ) && file_exists( $sz->plugin_dir . '/sz-' . $component . '/sz-' . $component . '-loader.php' ) ) {
				include( $sz->plugin_dir . '/sz-' . $component . '/sz-' . $component . '-loader.php' );
			}
		}

		// Loop through required components.
		foreach( $sz->required_components as $component ) {
			if ( file_exists( $sz->plugin_dir . '/sz-' . $component . '/sz-' . $component . '-loader.php' ) ) {
				include( $sz->plugin_dir . '/sz-' . $component . '/sz-' . $component . '-loader.php' );
			}
		}

		// Add Core to required components.
		$sz->required_components[] = 'core';

		/**
		 * Fires after the loading of individual components.
		 *
		 * @since 2.0.0
		 */
		do_action( 'sz_core_components_included' );
	}

	/**
	 * Include sz-core files.
	 *
	 * @since 1.6.0
	 *
	 * @see SZ_Component::includes() for description of parameters.
	 *
	 * @param array $includes See {@link SZ_Component::includes()}.
	 */
	public function includes( $includes = array() ) {

		if ( ! is_admin() ) {
			return;
		}

		$includes = array(
			'admin'
		);

		parent::includes( $includes );
	}

	/**
	 * Set up sz-core global settings.
	 *
	 * Sets up a majority of the SportsZone globals that require a minimal
	 * amount of processing, meaning they cannot be set in the SportsZone class.
	 *
	 * @since 1.5.0
	 *
	 * @see SZ_Component::setup_globals() for description of parameters.
	 *
	 * @param array $args See {@link SZ_Component::setup_globals()}.
	 */
	public function setup_globals( $args = array() ) {
		$sz = sportszone();

		/** Database *********************************************************
		 */

		// Get the base database prefix.
		if ( empty( $sz->table_prefix ) ) {
			$sz->table_prefix = sz_core_get_table_prefix();
		}

		// The domain for the root of the site where the main blog resides.
		if ( empty( $sz->root_domain ) ) {
			$sz->root_domain = sz_core_get_root_domain();
		}

		// Fetches all of the core SportsZone settings in one fell swoop.
		if ( empty( $sz->site_options ) ) {
			$sz->site_options = sz_core_get_root_options();
		}

		// The names of the core WordPress pages used to display SportsZone content.
		if ( empty( $sz->pages ) ) {
			$sz->pages = sz_core_get_directory_pages();
		}

		/** Basic current user data ******************************************
		 */

		// Logged in user is the 'current_user'.
		$current_user            = wp_get_current_user();

		// The user ID of the user who is currently logged in.
		$sz->loggedin_user       = new stdClass;
		$sz->loggedin_user->id   = isset( $current_user->ID ) ? $current_user->ID : 0;

		/** Avatars **********************************************************
		 */

		// Fetches the default Gravatar image to use if the user/group/blog has no avatar or gravatar.
		$sz->grav_default        = new stdClass;

		/**
		 * Filters the default user Gravatar.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value Default user Gravatar.
		 */
		$sz->grav_default->user  = apply_filters( 'sz_user_gravatar_default',  $sz->site_options['avatar_default'] );

		/**
		 * Filters the default group Gravatar.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value Default group Gravatar.
		 */
		$sz->grav_default->group = apply_filters( 'sz_group_gravatar_default', $sz->grav_default->user );

		/**
		 * Filters the default blog Gravatar.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value Default blog Gravatar.
		 */
		$sz->grav_default->blog  = apply_filters( 'sz_blog_gravatar_default',  $sz->grav_default->user );

		// Notifications table. Included here for legacy purposes. Use
		// sz-notifications instead.
		$sz->core->table_name_notifications = $sz->table_prefix . 'sz_notifications';

		// Backward compatibility for plugins modifying the legacy sz_nav and sz_options_nav global properties.
		$sz->sz_nav         = new SZ_Core_SZ_Nav_BackCompat();
		$sz->sz_options_nav = new SZ_Core_SZ_Options_Nav_BackCompat();

		/**
		 * Used to determine if user has admin rights on current content. If the
		 * logged in user is viewing their own profile and wants to delete
		 * something, is_item_admin is used. This is a generic variable so it
		 * can be used by other components. It can also be modified, so when
		 * viewing a group 'is_item_admin' would be 'true' if they are a group
		 * admin, and 'false' if they are not.
		 */
		sz_update_is_item_admin( sz_user_has_access(), 'core' );

		// Is the logged in user is a mod for the current item?
		sz_update_is_item_mod( false,                  'core' );

		/**
		 * Fires at the end of the setup of sz-core globals setting.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_core_setup_globals' );
	}

	/**
	 * Setup cache groups
	 *
	 * @since 2.2.0
	 */
	public function setup_cache_groups() {

		// Global groups.
		wp_cache_add_global_groups( array(
			'sz'
		) );

		parent::setup_cache_groups();
	}
	

	/**
	 * Set up post types.
	 *
	 * @since SportsZone (2.4.0)
	 */
	public function register_post_types() {

		// Emails
		if ( sz_is_root_blog() && ! is_network_admin() ) {
			register_post_type(
				sz_get_email_post_type(),
				apply_filters( 'sz_register_email_post_type', array(
					'description'       => _x( 'SportsZone emails', 'email post type description', 'sportszone' ),
					'labels'            => sz_get_email_post_type_labels(),
					'menu_icon'         => 'dashicons-email',
					'public'            => false,
					'publicly_queryable' => sz_current_user_can( 'sz_moderate' ),
					'query_var'         => false,
					'rewrite'           => false,
					'show_in_admin_bar' => false,
					'show_ui'           => sz_current_user_can( 'sz_moderate' ),
					'supports'          => sz_get_email_post_type_supports(),
				) )
			);
		}

		parent::register_post_types();
	}
}
