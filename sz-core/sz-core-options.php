<?php
/**
 * SportsZone Options.
 *
 * @package SportsZone
 * @subpackage Options
 * @since 1.6.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Get the default site options and their values.
 *
 * Default values should not be set by calls to `get_option()` or `get_site_option()` due to
 * these causing load order problems with `sz_core_clear_root_options_cache()`; see #BP7227.
 *
 * @since 1.6.0
 *
 * @return array Filtered option names and values.
 */
function sz_get_default_options() {

	// Default options.
	$options = array (

		/* Components ********************************************************/

		'sz-deactivated-components'            => array(),

		/* XProfile **********************************************************/

		// Base profile groups name.
		'sz-xprofile-base-group-name'          => 'Base',

		// Base fullname field name.
		'sz-xprofile-fullname-field-name'      => 'Name',

		/* Blogs *************************************************************/

		// Used to decide if blogs need indexing.
		'sz-blogs-first-install'               => false,

		/* Settings **********************************************************/

		// Disable the WP to BP profile sync.
		'sz-disable-profile-sync'              => false,

		// Hide the Toolbar for logged out users.
		'hide-loggedout-adminbar'              => false,

		// Avatar uploads.
		'sz-disable-avatar-uploads'            => false,

		// Cover image uploads.
		'sz-disable-cover-image-uploads'       => false,

		// Group Profile Photos.
		'sz-disable-group-avatar-uploads'      => false,

		// Group Cover image uploads.
		'sz-disable-group-cover-image-uploads' => false,

		// Allow users to delete their own accounts.
		'sz-disable-account-deletion'          => false,

		// Allow comments on post and comment activity items.
		'sz-disable-blogforum-comments'        => true,

		// The ID for the current theme package.
		'_sz_theme_package_id'                 => 'nouveau',

		// Email unsubscribe salt.
		'sz-emails-unsubscribe-salt'           => '',

		/* Groups ************************************************************/

		// @todo Move this into the groups component
		// Restrict group creation to super admins.
		'sz_restrict_group_creation'           => false,
		
		/* Events ************************************************************/

		// @todo Move this into the events component
		// Restrict event creation to super admins.
		'sz_restrict_event_creation'           => false,

		/* Akismet ***********************************************************/

		// Users from all sites can post.
		'_sz_enable_akismet'                   => true,

		/* Activity HeartBeat ************************************************/

		// HeartBeat is on to refresh activities.
		'_sz_enable_heartbeat_refresh'         => true,

		/* BuddyBar **********************************************************/

		// Force the BuddyBar.
		'_sz_force_buddybar'                   => false,

		/* Legacy *********************************************/

		// Do not register the sz-default themes directory.
		'_sz_retain_sz_default'                => false,

		// Ignore deprecated code.
		'_sz_ignore_deprecated_code'           => true,

		/* Widgets **************************************************/
		'widget_sz_core_login_widget'                => false,
		'widget_sz_core_members_widget'              => false,
		'widget_sz_core_whos_online_widget'          => false,
		'widget_sz_core_recently_active_widget'      => false,
		'widget_sz_groups_widget'                    => false,
		'widget_sz_events_widget'                    => false,
		'widget_sz_messages_sitewide_notices_widget' => false,
	);

	/**
	 * Filters the default options to be set upon activation.
	 *
	 * @since 1.6.0
	 *
	 * @param array $options Array of default options to set.
	 */
	return apply_filters( 'sz_get_default_options', $options );
}

/**
 * Add default options when SportsZone is first activated.
 *
 * Only called once when SportsZone is activated.
 * Non-destructive, so existing settings will not be overridden.
 *
 * @since 1.6.0
 */
function sz_add_options() {

	// Get the default options and values.
	$options = sz_get_default_options();

	// Add default options.
	foreach ( $options as $key => $value ) {
		sz_add_option( $key, $value );
	}

	/**
	 * Fires after the addition of default options when SportsZone is first activated.
	 *
	 * Allows previously activated plugins to append their own options.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_add_options' );
}

/**
 * Delete default options.
 *
 * Hooked to sz_uninstall, it is only called once when SportsZone is uninstalled.
 * This is destructive, so existing settings will be destroyed.
 *
 * Currently unused.
 *
 * @since 1.6.0
 */
function sz_delete_options() {

	// Get the default options and values.
	$options = sz_get_default_options();

	// Add default options.
	foreach ( array_keys( $options ) as $key ) {
		delete_option( $key );
	}

	/**
	 * Fires after the deletion of default options when SportsZone is first deactivated.
	 *
	 * Allows previously activated plugins to append their own options.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_delete_options' );
}

/**
 * Add filters to each BP option, allowing them to be overloaded from inside the $sz->options array.
 *
 * @since 1.6.0
 */
function sz_setup_option_filters() {

	// Get the default options and values.
	$options = sz_get_default_options();

	// Add filters to each SportsZone option.
	foreach ( array_keys( $options ) as $key ) {
		add_filter( 'pre_option_' . $key, 'sz_pre_get_option' );
	}

	/**
	 * Fires after the addition of filters to each SportsZone option.
	 *
	 * Allows previously activated plugins to append their own options.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_setup_option_filters' );
}

/**
 * Filter default options and allow them to be overloaded from inside the $sz->options array.
 *
 * @since 1.6.0
 *
 * @param bool $value Optional. Default value false.
 * @return mixed False if not overloaded, mixed if set.
 */
function sz_pre_get_option( $value = false ) {
	$sz = sportszone();

	// Remove the filter prefix.
	$option = str_replace( 'pre_option_', '', current_filter() );

	// Check the options global for preset value.
	if ( ! empty( $sz->options[ $option ] ) ) {
		$value = $sz->options[ $option ];
	}

	// Always return a value, even if false.
	return $value;
}

/**
 * Retrieve an option.
 *
 * This is a wrapper for {@link get_blog_option()}, which in turn stores settings data
 * (such as sz-pages) on the appropriate blog, given your current setup.
 *
 * The 'sz_get_option' filter is primarily for backward-compatibility.
 *
 * @since 1.5.0
 *
 * @param string $option_name The option to be retrieved.
 * @param string $default     Optional. Default value to be returned if the option
 *                            isn't set. See {@link get_blog_option()}.
 * @return mixed The value for the option.
 */
function sz_get_option( $option_name, $default = '' ) {
	$value = get_blog_option( sz_get_root_blog_id(), $option_name, $default );

	/**
	 * Filters the option value for the requested option.
	 *
	 * @since 1.5.0
	 *
	 * @param mixed $value The value for the option.
	 */
	return apply_filters( 'sz_get_option', $value );
}

/**
 * Add an option.
 *
 * This is a wrapper for {@link add_blog_option()}, which in turn stores
 * settings data on the appropriate blog, given your current setup.
 *
 * @since 2.0.0
 *
 * @param string $option_name The option key to be set.
 * @param mixed  $value       The value to be set.
 * @return bool True on success, false on failure.
 */
function sz_add_option( $option_name, $value ) {
	return add_blog_option( sz_get_root_blog_id(), $option_name, $value );
}

/**
 * Save an option.
 *
 * This is a wrapper for {@link update_blog_option()}, which in turn stores
 * settings data (such as sz-pages) on the appropriate blog, given your current
 * setup.
 *
 * @since 1.5.0
 *
 * @param string $option_name The option key to be set.
 * @param mixed  $value       The value to be set.
 * @return bool True on success, false on failure.
 */
function sz_update_option( $option_name, $value ) {
	return update_blog_option( sz_get_root_blog_id(), $option_name, $value );
}

/**
 * Delete an option.
 *
 * This is a wrapper for {@link delete_blog_option()}, which in turn deletes
 * settings data (such as sz-pages) on the appropriate blog, given your current
 * setup.
 *
 * @since 1.5.0
 *
 * @param string $option_name The option key to be deleted.
 * @return bool True on success, false on failure.
 */
function sz_delete_option( $option_name ) {
	return delete_blog_option( sz_get_root_blog_id(), $option_name );
}

/**
 * Copy BP options from a single site to multisite config.
 *
 * Run when switching from single to multisite and we need to copy blog options
 * to site options.
 *
 * This function is no longer used.
 *
 * @since 1.2.4
 * @deprecated 1.6.0
 *
 * @param array $keys Array of site options.
 * @return bool
 */
function sz_core_activate_site_options( $keys = array() ) {

	if ( !empty( $keys ) && is_array( $keys ) ) {
		$sz = sportszone();

		$errors = false;

		foreach ( $keys as $key => $default ) {
			if ( empty( $sz->site_options[ $key ] ) ) {
				$sz->site_options[ $key ] = sz_get_option( $key, $default );

				if ( !sz_update_option( $key, $sz->site_options[ $key ] ) ) {
					$errors = true;
				}
			}
		}

		if ( empty( $errors ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Fetch global BP options.
 *
 * SportsZone uses common options to store configuration settings. Many of these
 * settings are needed at run time. Instead of fetching them all and adding many
 * initial queries to each page load, let's fetch them all in one go.
 *
 * @since 1.5.0
 *
 * @todo Use settings API and audit these methods.
 *
 * @return array $root_blog_options_meta List of options.
 */
function sz_core_get_root_options() {
	global $wpdb;

	// Get all the SportsZone settings, and a few useful WP ones too.
	$root_blog_options                   = sz_get_default_options();
	$root_blog_options['registration']   = '0';
	$root_blog_options['avatar_default'] = 'mysteryman';
	$root_blog_option_keys               = array_keys( $root_blog_options );

	// Do some magic to get all the root blog options in 1 swoop
	// Check cache first - We cache here instead of using the standard WP
	// settings cache because the current blog may not be the root blog,
	// and it's not practical to access the cache across blogs.
	$root_blog_options_meta = wp_cache_get( 'root_blog_options', 'sz' );

	if ( false === $root_blog_options_meta ) {
		$blog_options_keys      = "'" . join( "', '", (array) $root_blog_option_keys ) . "'";
		$blog_options_table	    = sz_is_multiblog_mode() ? $wpdb->options : $wpdb->get_blog_prefix( sz_get_root_blog_id() ) . 'options';
		$blog_options_query     = "SELECT option_name AS name, option_value AS value FROM {$blog_options_table} WHERE option_name IN ( {$blog_options_keys} )";
		$root_blog_options_meta = $wpdb->get_results( $blog_options_query );

		// On Multisite installations, some options must always be fetched from sitemeta.
		if ( is_multisite() ) {

			/**
			 * Filters multisite options retrieved from sitemeta.
			 *
			 * @since 1.5.0
			 *
			 * @param array $value Array of multisite options from sitemeta table.
			 */
			$network_options = apply_filters( 'sz_core_network_options', array(
				'tags_blog_id'       => '0',
				'sitewide_tags_blog' => '',
				'registration'       => '0',
				'fileupload_maxk'    => '1500'
			) );

			$current_site           = get_current_site();
			$network_option_keys    = array_keys( $network_options );
			$sitemeta_options_keys  = "'" . join( "', '", (array) $network_option_keys ) . "'";
			$sitemeta_options_query = $wpdb->prepare( "SELECT meta_key AS name, meta_value AS value FROM {$wpdb->sitemeta} WHERE meta_key IN ( {$sitemeta_options_keys} ) AND site_id = %d", $current_site->id );
			$network_options_meta   = $wpdb->get_results( $sitemeta_options_query );

			// Sitemeta comes second in the merge, so that network 'registration' value wins.
			$root_blog_options_meta = array_merge( $root_blog_options_meta, $network_options_meta );
		}

		// Loop through our results and make them usable.
		foreach ( $root_blog_options_meta as $root_blog_option ) {
			$root_blog_options[$root_blog_option->name] = $root_blog_option->value;
		}

		// Copy the options no the return val.
		$root_blog_options_meta = $root_blog_options;

		// Clean up our temporary copy.
		unset( $root_blog_options );

		wp_cache_set( 'root_blog_options', $root_blog_options_meta, 'sz' );
	}

	/**
	 * Filters the global BP options.
	 *
	 * @since 1.5.0
	 *
	 * @param array $root_blog_options_meta Array of global BP options.
	 */
	return apply_filters( 'sz_core_get_root_options', $root_blog_options_meta );
}

/**
 * Get a root option.
 *
 * "Root options" are those that apply across an entire installation, and are fetched only a single
 * time during a pageload and stored in `sportszone()->site_options` to prevent future lookups.
 * See {@see sz_core_get_root_options()}.
 *
 * @since 2.3.0
 *
 * @param string $option Name of the option key.
 * @return mixed Value, if found.
 */
function sz_core_get_root_option( $option ) {
	$sz = sportszone();

	if ( ! isset( $sz->site_options ) ) {
		$sz->site_options = sz_core_get_root_options();
	}

	$value = '';
	if ( isset( $sz->site_options[ $option ] ) ) {
		$value = $sz->site_options[ $option ];
	}

	return $value;
}

/** Active? *******************************************************************/

/**
 * Is profile syncing disabled?
 *
 * @since 1.6.0
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: true.
 * @return bool True if profile sync is enabled, otherwise false.
 */
function sz_disable_profile_sync( $default = false ) {

	/**
	 * Filters whether or not profile syncing is disabled.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $value Whether or not syncing is disabled.
	 */
	return (bool) apply_filters( 'sz_disable_profile_sync', (bool) sz_get_option( 'sz-disable-profile-sync', $default ) );
}

/**
 * Is the Toolbar hidden for logged out users?
 *
 * @since 1.6.0
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: true.
 * @return bool True if the admin bar should be hidden for logged-out users,
 *              otherwise false.
 */
function sz_hide_loggedout_adminbar( $default = true ) {

	/**
	 * Filters whether or not the toolbar is hidden for logged out users.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $value Whether or not the toolbar is hidden.
	 */
	return (bool) apply_filters( 'sz_hide_loggedout_adminbar', (bool) sz_get_option( 'hide-loggedout-adminbar', $default ) );
}

/**
 * Are members able to upload their own avatars?
 *
 * @since 1.6.0
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: true.
 * @return bool True if avatar uploads are disabled, otherwise false.
 */
function sz_disable_avatar_uploads( $default = true ) {

	/**
	 * Filters whether or not members are able to upload their own avatars.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $value Whether or not members are able to upload their own avatars.
	 */
	return (bool) apply_filters( 'sz_disable_avatar_uploads', (bool) sz_get_option( 'sz-disable-avatar-uploads', $default ) );
}

/**
 * Are members able to upload their own cover images?
 *
 * @since 2.4.0
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: false.
 * @return bool True if cover image uploads are disabled, otherwise false.
 */
function sz_disable_cover_image_uploads( $default = false ) {

	/**
	 * Filters whether or not members are able to upload their own cover images.
	 *
	 * @since 2.4.0
	 *
	 * @param bool $value Whether or not members are able to upload their own cover images.
	 */
	return (bool) apply_filters( 'sz_disable_cover_image_uploads', (bool) sz_get_option( 'sz-disable-cover-image-uploads', $default ) );
}

/**
 * Are group avatars disabled?
 *
 * For backward compatibility, this option falls back on the value of 'sz-disable-avatar-uploads' when no value is
 * found in the database.
 *
 * @since 2.3.0
 *
 * @param bool|null $default Optional. Fallback value if not found in the database.
 *                           Defaults to the value of `sz_disable_avatar_uploads()`.
 * @return bool True if group avatar uploads are disabled, otherwise false.
 */
function sz_disable_group_avatar_uploads( $default = null ) {
	$disabled = sz_get_option( 'sz-disable-group-avatar-uploads', '' );

	if ( '' === $disabled ) {
		if ( is_null( $default ) ) {
			$disabled = sz_disable_avatar_uploads();
		} else {
			$disabled = $default;
		}
	}

	/**
	 * Filters whether or not members are able to upload group avatars.
	 *
	 * @since 2.3.0
	 *
	 * @param bool $disabled Whether or not members are able to upload their groups avatars.
	 * @param bool $default  Default value passed to the function.
	 */
	return (bool) apply_filters( 'sz_disable_group_avatar_uploads', $disabled, $default );
}

/**
 * Are group cover images disabled?
 *
 * @since 2.4.0
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: false.
 * @return bool True if group cover image uploads are disabled, otherwise false.
 */
function sz_disable_group_cover_image_uploads( $default = false ) {

	/**
	 * Filters whether or not members are able to upload group cover images.
	 *
	 * @since 2.4.0
	 *
	 * @param bool $value Whether or not members are able to upload thier groups cover images.
	 */
	return (bool) apply_filters( 'sz_disable_group_cover_image_uploads', (bool) sz_get_option( 'sz-disable-group-cover-image-uploads', $default ) );
}

/**
 * Are members able to delete their own accounts?
 *
 * @since 1.6.0
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: true.
 * @return bool True if users are able to delete their own accounts, otherwise
 *              false.
 */
function sz_disable_account_deletion( $default = false ) {

	/**
	 * Filters whether or not members are able to delete their own accounts.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $value Whether or not members are able to delete their own accounts.
	 */
	return apply_filters( 'sz_disable_account_deletion', (bool) sz_get_option( 'sz-disable-account-deletion', $default ) );
}

/**
 * Are post/comment activity stream comments disabled?
 *
 * @since 1.6.0
 *
 * @todo split and move into blog and forum components.
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: false.
 * @return bool True if activity comments are disabled for blog and forum
 *              items, otherwise false.
 */
function sz_disable_blogforum_comments( $default = false ) {

	/**
	 * Filters whether or not blog and forum activity stream comments are disabled.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $value Whether or not blog and forum activity stream comments are disabled.
	 */
	return (bool) apply_filters( 'sz_disable_blogforum_comments', (bool) sz_get_option( 'sz-disable-blogforum-comments', $default ) );
}

/**
 * Is group creation turned off?
 *
 * @since 1.6.0
 *
 * @todo Move into groups component.
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: true.
 * @return bool True if group creation is restricted, otherwise false.
 */
function sz_restrict_group_creation( $default = true ) {

	/**
	 * Filters whether or not group creation is turned off.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $value Whether or not group creation is turned off.
	 */
	return (bool) apply_filters( 'sz_restrict_group_creation', (bool) sz_get_option( 'sz_restrict_group_creation', $default ) );
}

/**
 * Is event creation turned off?
 *
 * @since 1.6.0
 *
 * @todo Move into event component.
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: true.
 * @return bool True if group creation is restricted, otherwise false.
 */
function sz_restrict_event_creation( $default = true ) {

	/**
	 * Filters whether or not event creation is turned off.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $value Whether or not event creation is turned off.
	 */
	return (bool) apply_filters( 'sz_restrict_event_creation', (bool) sz_get_option( 'sz_restrict_event_creation', $default ) );
}

/**
 * Should the old BuddyBar be forced in place of the WP admin bar?
 *
 * @since 1.6.0
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: true.
 * @return bool True if the BuddyBar should be forced on, otherwise false.
 */
function sz_force_buddybar( $default = true ) {

	/**
	 * Filters whether or not BuddyBar should be forced in place of WP Admin Bar.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $value Whether or not BuddyBar should be forced in place of WP Admin Bar.
	 */
	return (bool) apply_filters( 'sz_force_buddybar', (bool) sz_get_option( '_sz_force_buddybar', $default ) );
}

/**
 * Check whether Akismet is enabled.
 *
 * @since 1.6.0
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: true.
 * @return bool True if Akismet is enabled, otherwise false.
 */
function sz_is_akismet_active( $default = true ) {

	/**
	 * Filters whether or not Akismet is enabled.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $value Whether or not Akismet is enabled.
	 */
	return (bool) apply_filters( 'sz_is_akismet_active', (bool) sz_get_option( '_sz_enable_akismet', $default ) );
}

/**
 * Check whether Activity Heartbeat refresh is enabled.
 *
 * @since 2.0.0
 *
 * @param bool $default Optional. Fallback value if not found in the database.
 *                      Default: true.
 * @return bool True if Heartbeat refresh is enabled, otherwise false.
 */
function sz_is_activity_heartbeat_active( $default = true ) {

	/**
	 * Filters whether or not Activity Heartbeat refresh is enabled.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $value Whether or not Activity Heartbeat refresh is enabled.
	 */
	return (bool) apply_filters( 'sz_is_activity_heartbeat_active', (bool) sz_get_option( '_sz_enable_heartbeat_refresh', $default ) );
}

/**
 * Get the current theme package ID.
 *
 * @since 1.7.0
 *
 * @param string $default Optional. Fallback value if not found in the database.
 *                        Default: 'legacy'.
 * @return string ID of the theme package.
 */
function sz_get_theme_package_id( $default = 'legacy' ) {

	/**
	 * Filters the current theme package ID.
	 *
	 * @since 1.7.0
	 *
	 * @param string $value The current theme package ID.
	 */
	return apply_filters( 'sz_get_theme_package_id', sz_get_option( '_sz_theme_package_id', $default ) );
}
