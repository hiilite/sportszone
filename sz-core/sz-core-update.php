<?php
/**
 * SportsZone Updater.
 *
 * @package SportsZone
 * @subpackage Updater
 * @since 1.6.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Is this a fresh installation of SportsZone?
 *
 * If there is no raw DB version, we infer that this is the first installation.
 *
 * @since 1.7.0
 *
 * @return bool True if this is a fresh BP install, otherwise false.
 */
function sz_is_install() {
	return ! sz_get_db_version_raw();
}

/**
 * Is this a SportsZone update?
 *
 * Determined by comparing the registered SportsZone version to the version
 * number stored in the database. If the registered version is greater, it's
 * an update.
 *
 * @since 1.6.0
 *
 * @return bool True if update, otherwise false.
 */
function sz_is_update() {

	// Current DB version of this site (per site in a multisite network).
	$current_db   = sz_get_option( '_sz_db_version' );
	$current_live = sz_get_db_version();

	// Compare versions (cast as int and bool to be safe).
	$is_update = (bool) ( (int) $current_db < (int) $current_live );

	// Return the product of version comparison.
	return $is_update;
}

/**
 * Determine whether SportsZone is in the process of being activated.
 *
 * @since 1.6.0
 *
 * @param string $basename SportsZone basename.
 * @return bool True if activating SportsZone, false if not.
 */
function sz_is_activation( $basename = '' ) {
	$sz     = sportszone();
	$action = false;

	if ( ! empty( $_REQUEST['action'] ) && ( '-1' != $_REQUEST['action'] ) ) {
		$action = $_REQUEST['action'];
	} elseif ( ! empty( $_REQUEST['action2'] ) && ( '-1' != $_REQUEST['action2'] ) ) {
		$action = $_REQUEST['action2'];
	}

	// Bail if not activating.
	if ( empty( $action ) || !in_array( $action, array( 'activate', 'activate-selected' ) ) ) {
		return false;
	}

	// The plugin(s) being activated.
	if ( $action == 'activate' ) {
		$plugins = isset( $_GET['plugin'] ) ? array( $_GET['plugin'] ) : array();
	} else {
		$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
	}

	// Set basename if empty.
	if ( empty( $basename ) && !empty( $sz->basename ) ) {
		$basename = $sz->basename;
	}

	// Bail if no basename.
	if ( empty( $basename ) ) {
		return false;
	}

	// Is SportsZone being activated?
	return in_array( $basename, $plugins );
}

/**
 * Determine whether SportsZone is in the process of being deactivated.
 *
 * @since 1.6.0
 *
 * @param string $basename SportsZone basename.
 * @return bool True if deactivating SportsZone, false if not.
 */
function sz_is_deactivation( $basename = '' ) {
	$sz     = sportszone();
	$action = false;

	if ( ! empty( $_REQUEST['action'] ) && ( '-1' != $_REQUEST['action'] ) ) {
		$action = $_REQUEST['action'];
	} elseif ( ! empty( $_REQUEST['action2'] ) && ( '-1' != $_REQUEST['action2'] ) ) {
		$action = $_REQUEST['action2'];
	}

	// Bail if not deactivating.
	if ( empty( $action ) || !in_array( $action, array( 'deactivate', 'deactivate-selected' ) ) ) {
		return false;
	}

	// The plugin(s) being deactivated.
	if ( 'deactivate' == $action ) {
		$plugins = isset( $_GET['plugin'] ) ? array( $_GET['plugin'] ) : array();
	} else {
		$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
	}

	// Set basename if empty.
	if ( empty( $basename ) && !empty( $sz->basename ) ) {
		$basename = $sz->basename;
	}

	// Bail if no basename.
	if ( empty( $basename ) ) {
		return false;
	}

	// Is bbPress being deactivated?
	return in_array( $basename, $plugins );
}

/**
 * Update the BP version stored in the database to the current version.
 *
 * @since 1.6.0
 */
function sz_version_bump() {
	sz_update_option( '_sz_db_version', sz_get_db_version() );
}

/**
 * Set up the SportsZone updater.
 *
 * @since 1.6.0
 */
function sz_setup_updater() {

	// Are we running an outdated version of SportsZone?
	if ( ! sz_is_update() ) {
		return;
	}

	sz_version_updater();
}

/**
 * Initialize an update or installation of SportsZone.
 *
 * SportsZone's version updater looks at what the current database version is,
 * and runs whatever other code is needed - either the "update" or "install"
 * code.
 *
 * This is most often used when the data schema changes, but should also be used
 * to correct issues with SportsZone metadata silently on software update.
 *
 * @since 1.7.0
 */
function sz_version_updater() {

	// Get the raw database version.
	$raw_db_version = (int) sz_get_db_version_raw();

	/**
	 * Filters the default components to activate for a new install.
	 *
	 * @since 1.7.0
	 *
	 * @param array $value Array of default components to activate.
	 */
	$default_components = apply_filters( 'sz_new_install_default_components', array(
		'activity'      => 1,
		'members'       => 1,
		'settings'      => 1,
		'xprofile'      => 1,
		'notifications' => 1,
	) );

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	require_once( sportszone()->plugin_dir . '/sz-core/admin/sz-core-admin-schema.php' );
	$switched_to_root_blog = false;

	// Make sure the current blog is set to the root blog.
	if ( ! sz_is_root_blog() ) {
		switch_to_blog( sz_get_root_blog_id() );
		sz_register_taxonomies();

		$switched_to_root_blog = true;
	}

	// Install BP schema and activate only Activity and XProfile.
	if ( sz_is_install() ) {

		// Apply schema and set Activity and XProfile components as active.
		sz_core_install( $default_components );
		sz_update_option( 'sz-active-components', $default_components );
		sz_core_add_page_mappings( $default_components, 'delete' );
		sz_core_install_emails();

	// Upgrades.
	} else {

		// Run the schema install to update tables.
		sz_core_install();

		// Version 1.5.0.
		if ( $raw_db_version < 1801 ) {
			sz_update_to_1_5();
			sz_core_add_page_mappings( $default_components, 'delete' );
		}

		// Version 1.6.0.
		if ( $raw_db_version < 6067 ) {
			sz_update_to_1_6();
		}

		// Version 1.9.0.
		if ( $raw_db_version < 7553 ) {
			sz_update_to_1_9();
		}

		// Version 1.9.2.
		if ( $raw_db_version < 7731 ) {
			sz_update_to_1_9_2();
		}

		// Version 2.0.0.
		if ( $raw_db_version < 7892 ) {
			sz_update_to_2_0();
		}

		// Version 2.0.1.
		if ( $raw_db_version < 8311 ) {
			sz_update_to_2_0_1();
		}

		// Version 2.2.0.
		if ( $raw_db_version < 9181 ) {
			sz_update_to_2_2();
		}

		// Version 2.3.0.
		if ( $raw_db_version < 9615 ) {
			sz_update_to_2_3();
		}

		// Version 2.5.0.
		if ( $raw_db_version < 10440 ) {
			sz_update_to_2_5();
		}

		// Version 2.7.0.
		if ( $raw_db_version < 11105 ) {
			sz_update_to_2_7();
		}
	}

	/* All done! *************************************************************/

	// Bump the version.
	sz_version_bump();

	if ( $switched_to_root_blog ) {
		restore_current_blog();
	}
}

/**
 * Perform database operations that must take place before the general schema upgrades.
 *
 * `dbDelta()` cannot handle certain operations - like changing indexes - so we do it here instead.
 *
 * @since 2.3.0
 */
function sz_pre_schema_upgrade() {
	global $wpdb;

	$raw_db_version = (int) sz_get_db_version_raw();
	$sz_prefix      = sz_core_get_table_prefix();

	// 2.3.0: Change index lengths to account for utf8mb4.
	if ( $raw_db_version < 9695 ) {
		// Map table_name => columns.
		$tables = array(
			$sz_prefix . 'sz_activity_meta'       => array( 'meta_key' ),
			$sz_prefix . 'sz_groups_groupmeta'    => array( 'meta_key' ),
			$sz_prefix . 'sz_messages_meta'       => array( 'meta_key' ),
			$sz_prefix . 'sz_notifications_meta'  => array( 'meta_key' ),
			$sz_prefix . 'sz_user_blogs_blogmeta' => array( 'meta_key' ),
			$sz_prefix . 'sz_xprofile_meta'       => array( 'meta_key' ),
		);

		foreach ( $tables as $table_name => $indexes ) {
			foreach ( $indexes as $index ) {
				if ( $wpdb->query( $wpdb->prepare( "SHOW TABLES LIKE %s", sz_esc_like( $table_name ) ) ) ) {
					$wpdb->query( "ALTER TABLE {$table_name} DROP INDEX {$index}" );
				}
			}
		}
	}
}

/** Upgrade Routines **********************************************************/

/**
 * Remove unused metadata from database when upgrading from < 1.5.
 *
 * Database update methods based on version numbers.
 *
 * @since 1.7.0
 */
function sz_update_to_1_5() {

	// Delete old database version options.
	delete_site_option( 'sz-activity-db-version' );
	delete_site_option( 'sz-blogs-db-version'    );
	delete_site_option( 'sz-friends-db-version'  );
	delete_site_option( 'sz-groups-db-version'   );
	delete_site_option( 'sz-messages-db-version' );
	delete_site_option( 'sz-xprofile-db-version' );
}

/**
 * Remove unused metadata from database when upgrading from < 1.6.0.
 *
 * Database update methods based on version numbers.
 *
 * @since 1.7.0
 */
function sz_update_to_1_6() {

	// Delete possible site options.
	delete_site_option( 'sz-db-version'       );
	delete_site_option( '_sz_db_version'      );
	delete_site_option( 'sz-core-db-version'  );
	delete_site_option( '_sz-core-db-version' );

	// Delete possible blog options.
	delete_blog_option( sz_get_root_blog_id(), 'sz-db-version'       );
	delete_blog_option( sz_get_root_blog_id(), 'sz-core-db-version'  );
	delete_site_option( sz_get_root_blog_id(), '_sz-core-db-version' );
	delete_site_option( sz_get_root_blog_id(), '_sz_db_version'      );
}

/**
 * Add the notifications component to active components.
 *
 * Notifications was added in 1.9.0, and previous installations will already
 * have the core notifications API active. We need to add the new Notifications
 * component to the active components option to retain existing functionality.
 *
 * @since 1.9.0
 */
function sz_update_to_1_9() {

	// Setup hardcoded keys.
	$active_components_key      = 'sz-active-components';
	$notifications_component_id = 'notifications';

	// Get the active components.
	$active_components          = sz_get_option( $active_components_key );

	// Add notifications.
	if ( ! in_array( $notifications_component_id, $active_components ) ) {
		$active_components[ $notifications_component_id ] = 1;
	}

	// Update the active components option.
	sz_update_option( $active_components_key, $active_components );
}

/**
 * Perform database updates for BP 1.9.2.
 *
 * In 1.9, SportsZone stopped registering its theme directory when it detected
 * that sz-default (or a child theme) was not currently being used, in effect
 * deprecating sz-default. However, this ended up causing problems when site
 * admins using sz-default would switch away from the theme temporarily:
 * sz-default would no longer be available, with no obvious way (outside of
 * a manual filter) to restore it. In 1.9.2, we add an option that flags
 * whether sz-default or a child theme is active at the time of upgrade; if so,
 *
 * the theme directory will continue to be registered even if the theme is
 * deactivated temporarily. Thus, new installations will not see sz-default,
 * but legacy installations using the theme will continue to see it.
 *
 * @since 1.9.2
 */
function sz_update_to_1_9_2() {
	if ( 'sz-default' === get_stylesheet() || 'sz-default' === get_template() ) {
		update_site_option( '_sz_retain_sz_default', 1 );
	}
}

/**
 * 2.0 update routine.
 *
 * - Ensure that the activity tables are installed, for last_activity storage.
 * - Migrate last_activity data from usermeta to activity table.
 * - Add values for all SportsZone options to the options table.
 *
 * @since 2.0.0
 */
function sz_update_to_2_0() {

	/* Install activity tables for 'last_activity' ***************************/

	sz_core_install_activity_streams();

	/* Migrate 'last_activity' data ******************************************/

	sz_last_activity_migrate();

	/* Migrate signups data **************************************************/

	if ( ! is_multisite() ) {

		// Maybe install the signups table.
		sz_core_maybe_install_signups();

		// Run the migration script.
		sz_members_migrate_signups();
	}

	/* Add BP options to the options table ***********************************/

	sz_add_options();
}

/**
 * 2.0.1 database upgrade routine.
 *
 * @since 2.0.1
 */
function sz_update_to_2_0_1() {

	// We purposely call this during both the 2.0 upgrade and the 2.0.1 upgrade.
	// Don't worry; it won't break anything, and safely handles all cases.
	sz_core_maybe_install_signups();
}

/**
 * 2.2.0 update routine.
 *
 * - Add messages meta table.
 * - Update the component field of the 'new members' activity type.
 * - Clean up hidden friendship activities.
 *
 * @since 2.2.0
 */
function sz_update_to_2_2() {

	// Also handled by `sz_core_install()`.
	if ( sz_is_active( 'messages' ) ) {
		sz_core_install_private_messaging();
	}

	if ( sz_is_active( 'activity' ) ) {
		sz_migrate_new_member_activity_component();

		if ( sz_is_active( 'friends' ) ) {
			sz_cleanup_friendship_activities();
		}
	}
}

/**
 * 2.3.0 update routine.
 *
 * - Add notifications meta table.
 *
 * @since 2.3.0
 */
function sz_update_to_2_3() {

	// Also handled by `sz_core_install()`.
	if ( sz_is_active( 'notifications' ) ) {
		sz_core_install_notifications();
	}
}

/**
 * 2.5.0 update routine.
 *
 * - Add emails.
 *
 * @since 2.5.0
 */
function sz_update_to_2_5() {
	sz_core_install_emails();
}

/**
 * 2.7.0 update routine.
 *
 * - Add email unsubscribe salt.
 * - Save legacy directory titles to the corresponding WP pages.
 * - Add ignore deprecated code option (false for updates).
 *
 * @since 2.7.0
 */
function sz_update_to_2_7() {
	sz_add_option( 'sz-emails-unsubscribe-salt', base64_encode( wp_generate_password( 64, true, true ) ) );

	// Update post_titles
	sz_migrate_directory_page_titles();

	/*
	 * Add `parent_id` column to groups table.
	 * Also handled by `sz_core_install()`.
	 */
	if ( sz_is_active( 'groups' ) ) {
		sz_core_install_groups();

		// Invalidate all cached group objects.
		global $wpdb;
		$sz = sportszone();

		$group_ids = $wpdb->get_col( "SELECT id FROM {$sz->groups->table_name}" );

		foreach ( $group_ids as $group_id ) {
			wp_cache_delete( $group_id, 'sz_groups' );
		}
	}

	// Do not ignore deprecated code for existing installs.
	sz_add_option( '_sz_ignore_deprecated_code', false );
}

/**
 * Updates the component field for new_members type.
 *
 * @since 2.2.0
 *
 * @global $wpdb
 */
function sz_migrate_new_member_activity_component() {
	global $wpdb;
	$sz = sportszone();

	// Update the component for the new_member type.
	$wpdb->update(
		// Activity table.
		$sz->members->table_name_last_activity,
		array(
			'component' => $sz->members->id,
		),
		array(
			'component' => 'xprofile',
			'type'      => 'new_member',
		),
		// Data sanitization format.
		array(
			'%s',
		),
		// WHERE sanitization format.
		array(
			'%s',
			'%s'
		)
	);
}

/**
 * Remove all hidden friendship activities.
 *
 * @since 2.2.0
 */
function sz_cleanup_friendship_activities() {
	sz_activity_delete( array(
		'component'     => sportszone()->friends->id,
		'type'          => 'friendship_created',
		'hide_sitewide' => true,
	) );
}

/**
 * Update WP pages so that their post_title matches the legacy component directory title.
 *
 * As of 2.7.0, component directory titles come from the `post_title` attribute of the corresponding WP post object,
 * instead of being hardcoded. To ensure that directory titles don't change for existing installations, we update these
 * WP posts with the formerly hardcoded titles.
 *
 * @since 2.7.0
 */
function sz_migrate_directory_page_titles() {
	$sz_pages = sz_core_get_directory_page_ids( 'all' );

	$default_titles = sz_core_get_directory_page_default_titles();

	$legacy_titles = array(
		'activity' => _x( 'Site-Wide Activity', 'component directory title', 'sportszone' ),
		'blogs'    => _x( 'Sites', 'component directory title', 'sportszone' ),
		'groups'   => _x( 'Groups', 'component directory title', 'sportszone' ),
		'members'  => _x( 'Members', 'component directory title', 'sportszone' ),
	);

	foreach ( $sz_pages as $component => $page_id ) {
		if ( ! isset( $legacy_titles[ $component ] ) ) {
			continue;
		}

		$page = get_post( $page_id );
		if ( ! $page ) {
			continue;
		}

		// If the admin has changed the default title, don't touch it.
		if ( isset( $default_titles[ $component ] ) && $default_titles[ $component ] !== $page->post_title ) {
			continue;
		}

		// If the saved page title is the same as the legacy title, there's nothing to do.
		if ( $legacy_titles[ $component ] == $page->post_title ) {
			continue;
		}

		// Update the page with the legacy title.
		wp_update_post( array(
			'ID' => $page_id,
			'post_title' => $legacy_titles[ $component ],
		) );
	}
}

/**
 * Redirect user to BP's What's New page on first page load after activation.
 *
 * @since 1.7.0
 *
 * @internal Used internally to redirect SportsZone to the about page on activation.
 */
function sz_add_activation_redirect() {

	// Bail if activating from network, or bulk.
	if ( isset( $_GET['activate-multi'] ) ) {
		return;
	}

	// Record that this is a new installation, so we show the right
	// welcome message.
	if ( sz_is_install() ) {
		set_transient( '_sz_is_new_install', true, 30 );
	}

	// Add the transient to redirect.
	set_transient( '_sz_activation_redirect', true, 30 );
}

/** Signups *******************************************************************/

/**
 * Check if the signups table needs to be created or upgraded.
 *
 * @since 2.0.0
 *
 * @global WPDB $wpdb
 */
function sz_core_maybe_install_signups() {
	global $wpdb;

	// The table to run queries against.
	$signups_table = $wpdb->base_prefix . 'signups';

	// Suppress errors because users shouldn't see what happens next.
	$old_suppress  = $wpdb->suppress_errors();

	// Never use sz_core_get_table_prefix() for any global users tables.
	$table_exists  = (bool) $wpdb->get_results( "DESCRIBE {$signups_table};" );

	// Table already exists, so maybe upgrade instead?
	if ( true === $table_exists ) {

		// Look for the 'signup_id' column.
		$column_exists = $wpdb->query( "SHOW COLUMNS FROM {$signups_table} LIKE 'signup_id'" );

		// 'signup_id' column doesn't exist, so run the upgrade
		if ( empty( $column_exists ) ) {
			sz_core_upgrade_signups();
		}

	// Table does not exist, and we are a single site, so install the multisite
	// signups table using WordPress core's database schema.
	} elseif ( ! is_multisite() ) {
		sz_core_install_signups();
	}

	// Restore previous error suppression setting.
	$wpdb->suppress_errors( $old_suppress );
}

/** Activation Actions ********************************************************/

/**
 * Fire activation hooks and events.
 *
 * Runs on SportsZone activation.
 *
 * @since 1.6.0
 */
function sz_activation() {

	// Force refresh theme roots.
	delete_site_transient( 'theme_roots' );

	// Add options.
	sz_add_options();

	/**
	 * Fires during the activation of SportsZone.
	 *
	 * Use as of 1.6.0.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_activation' );

	// @deprecated as of 1.6.0
	do_action( 'sz_loader_activate' );
}

/**
 * Fire deactivation hooks and events.
 *
 * Runs on SportsZone deactivation.
 *
 * @since 1.6.0
 */
function sz_deactivation() {

	// Force refresh theme roots.
	delete_site_transient( 'theme_roots' );

	// Switch to WordPress's default theme if current parent or child theme
	// depend on sz-default. This is to prevent white screens of doom.
	if ( in_array( 'sz-default', array( get_template(), get_stylesheet() ) ) ) {
		switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
		update_option( 'template_root',   get_raw_theme_root( WP_DEFAULT_THEME, true ) );
		update_option( 'stylesheet_root', get_raw_theme_root( WP_DEFAULT_THEME, true ) );
	}

	/**
	 * Fires during the deactivation of SportsZone.
	 *
	 * Use as of 1.6.0.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_deactivation' );

	// @deprecated as of 1.6.0
	do_action( 'sz_loader_deactivate' );
}

/**
 * Fire uninstall hook.
 *
 * Runs when uninstalling SportsZone.
 *
 * @since 1.6.0
 */
function sz_uninstall() {

	/**
	 * Fires during the uninstallation of SportsZone.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_uninstall' );
}
