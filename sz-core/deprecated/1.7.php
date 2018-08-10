<?php
/**
 * Deprecated Functions
 *
 * @package SportsZone
 * @subpackage Core
 * @deprecated Since 1.7.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Output the SportsZone maintenance mode
 *
 * @since 1.6.0
 * @deprecated 1.7.0
 */
function sz_maintenance_mode() {
	echo sz_get_maintenance_mode();
}
	/**
	 * Return the SportsZone maintenance mode
	 *
	 * @since 1.6.0
	 * @deprecated 1.7.0
	 * @return string The SportsZone maintenance mode
	 */
	function sz_get_maintenance_mode() {
		return sportszone()->maintenance_mode;
	}

/**
 * @deprecated 1.7.0
 */
function xprofile_get_profile() {
	_deprecated_function( __FUNCTION__, '1.7' );
	sz_locate_template( array( 'profile/profile-loop.php' ), true );
}

/**
 * @deprecated 1.7.0
 */
function sz_get_profile_header() {
	_deprecated_function( __FUNCTION__, '1.7' );
	sz_locate_template( array( 'profile/profile-header.php' ), true );
}

/**
 * @deprecated 1.7.0
 * @param string $component_name
 * @return boolean
 */
function sz_exists( $component_name ) {
	_deprecated_function( __FUNCTION__, '1.7' );
	if ( function_exists( $component_name . '_install' ) )
		return true;

	return false;
}

/**
 * @deprecated 1.7.0
 */
function sz_get_plugin_sidebar() {
	_deprecated_function( __FUNCTION__, '1.7' );
	sz_locate_template( array( 'plugin-sidebar.php' ), true );
}

/**
 * On multiblog installations you must first allow themes to be activated and
 * show up on the theme selection screen. This function will let the SportsZone
 * bundled themes show up on the root blog selection screen and bypass this
 * step. It also means that the themes won't show for selection on other blogs.
 *
 * @deprecated 1.7.0
 * @return array
 */
function sz_core_allow_default_theme( $themes ) {
	_deprecated_function( __FUNCTION__, '1.7' );

	if ( !sz_current_user_can( 'sz_moderate' ) )
		return $themes;

	if ( sz_get_root_blog_id() != get_current_blog_id() )
		return $themes;

	if ( isset( $themes['sz-default'] ) )
		return $themes;

	$themes['sz-default'] = true;

	return $themes;
}

/**
 * No longer used by SportsZone core
 *
 * @deprecated 1.7.0
 * @param string $page
 * @return boolean True if is SportsZone page
 */
function sz_is_page( $page = '' ) {
	_deprecated_function( __FUNCTION__, '1.7' );

	if ( !sz_is_user() && sz_is_current_component( $page )  )
		return true;

	if ( 'home' == $page )
		return is_front_page();

	return false;
}

/** Admin *********************************************************************/

/**
 * This function was originally used to update pre-1.1 schemas, but that was
 * before we had a legitimate update process.
 *
 * @deprecated 1.7.0
 * @global WPDB $wpdb
 */
function sz_update_db_stuff() {
	global $wpdb;

	$sz        = sportszone();
	$sz_prefix = sz_core_get_table_prefix();

	// Rename the old user activity cached table if needed.
	if ( $wpdb->get_var( "SHOW TABLES LIKE '%{$sz_prefix}sz_activity_user_activity_cached%'" ) ) {
		$wpdb->query( "RENAME TABLE {$sz_prefix}sz_activity_user_activity_cached TO {$sz->activity->table_name}" );
	}

	// Rename fields from pre BP 1.2
	if ( $wpdb->get_var( "SHOW TABLES LIKE '%{$sz->activity->table_name}%'" ) ) {
		if ( $wpdb->get_var( "SHOW COLUMNS FROM {$sz->activity->table_name} LIKE 'component_action'" ) ) {
			$wpdb->query( "ALTER TABLE {$sz->activity->table_name} CHANGE component_action type varchar(75) NOT NULL" );
		}

		if ( $wpdb->get_var( "SHOW COLUMNS FROM {$sz->activity->table_name} LIKE 'component_name'" ) ) {
			$wpdb->query( "ALTER TABLE {$sz->activity->table_name} CHANGE component_name component varchar(75) NOT NULL" );
		}
	}

	// On first installation - record all existing blogs in the system.
	if ( !(int) $sz->site_options['sz-blogs-first-install'] ) {
		sz_blogs_record_existing_blogs();
		sz_update_option( 'sz-blogs-first-install', 1 );
	}

	if ( is_multisite() ) {
		sz_core_add_illegal_names();
	}

	// Update and remove the message threads table if it exists
	if ( $wpdb->get_var( "SHOW TABLES LIKE '%{$sz_prefix}sz_messages_threads%'" ) ) {
		if ( SZ_Messages_Thread::update_tables() ) {
			$wpdb->query( "DROP TABLE {$sz_prefix}sz_messages_threads" );
		}
	}
}
