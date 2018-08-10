<?php
/*
Based on Plugin: BP Group Hierarchy
Plugin URI: http://www.generalthreat.com/projects/sportszone-group-hierarchy/
Description: Allows SportsZone groups to belong to other groups
Version: 1.4.3
Revision Date: 04/09/2014
Requires at least: PHP 5, WP 3.2, SportsZone 1.6
Tested up to: WP 3.8.2, SportsZone 2.0-beta2
License: Example: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: David Dean
Author URI: http://www.generalthreat.com/
*/

define ( 'SZ_GROUP_HIERARCHY_IS_INSTALLED', 1 ); 
define ( 'SZ_GROUP_HIERARCHY_VERSION', '1.4.2' );
define ( 'SZ_GROUP_HIERARCHY_DB_VERSION', 1 );
if( ! defined( 'SZ_GROUP_HIERARCHY_SLUG' ) )
	define ( 'SZ_GROUP_HIERARCHY_SLUG', 'hierarchy' );


require ( dirname( __FILE__ ) . '/hierarchy/filters.php' );
require ( dirname( __FILE__ ) . '/hierarchy/actions.php' );
require ( dirname( __FILE__ ) . '/hierarchy/widgets.php' );

	
register_activation_hook( __FILE__, 'sz_group_hierarchy_install' );

/**
 * Install and/or upgrade the database
 */
function sz_group_hierarchy_install() {
	global $wpdb, $sz;

	// Check whether BP is active and whether Groups component is loaded, and throw error if not
	if( ! ( function_exists( 'sportszone' ) || is_a( $sz, 'SportsZone' ) ) || ! sz_is_active( 'groups' ) ) {
		_e( 'SportsZone is not installed or the Groups component is not activated. Cannot continue install.', 'sportszone' );
		exit;
	}

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	
	$sql[] = "CREATE TABLE {$sz->groups->table_name} (
				parent_id BIGINT(20) NOT NULL DEFAULT 0,
				KEY parent_id (parent_id),
			) {$charset_collate};
	 	   ";

	if( ! get_site_option( 'sz-group-hierarchy-db-version' ) || get_site_option( 'sz-group-hierarchy-db-version' ) < SZ_GROUP_HIERARCHY_DB_VERSION || ! sz_group_hierarchy_verify_install() ) {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);
	}
	
	if( sz_group_hierarchy_verify_install( true ) ) {
		update_site_option( 'sz-group-hierarchy-db-version', SZ_GROUP_HIERARCHY_DB_VERSION );
	} else {
		die('Could not create the required column.  Please enable debugging for more details.');
	}
}

/**
 * Try to DESCRIBE the groups table to see whether the column exists / was added
 * @param bool $debug_column Whether to report that the required column wasn't found - this is normal pre-install
 */
function sz_group_hierarchy_verify_install( $debug_column = false ) {

	global $wpdb, $sz;

	/** Manually confirm that parent_id column exists */
	$parent_id_exists = true;
	$columns = $wpdb->get_results( 'DESCRIBE ' . $sz->groups->table_name );
	
	if( $columns ) {
		$parent_id_exists = false;
		foreach( $columns as $column ) {
			if( $column->Field == 'parent_id') {
				$parent_id_exists = true;
				break;
			}
		}
		
		if( ! $parent_id_exists && $debug_column ) {
			sz_group_hierarchy_debug( 'Required column was not found - last MySQL error was: ' . $wpdb->last_error );
			return $parent_id_exists;
		}
		
	} else {
		sz_group_hierarchy_debug( 'Could not DESCRIBE table - last MySQL error was: ' . $wpdb->last_error );
		return false;
	}
	
	return $parent_id_exists;
	
}

/**
 * Debugging function
 */
function sz_group_hierarchy_debug( $message ) {

	if( ! defined( 'WP_DEBUG') || ! WP_DEBUG )	return;

	if( is_array( $message ) || is_object( $message ) ) {
		$message = print_r( $message, true );
	}

	if(defined( 'WP_DEBUG_LOG') && WP_DEBUG_LOG ) {
		$GLOBALS['wp_log']['sz_group_hierarchy'][] = 'SZ Group Hierarchy - ' .  $message;
		error_log('SZ Group Hierarchy - ' .  $message);
	}

	if( defined('WP_DEBUG_DISPLAY') && false !== WP_DEBUG_DISPLAY) {
		//echo '<div class="log">SZ Group Hierarchy - ' . $message . "</div>\n"; 
	}
	
}

/**
 * Detect whether the new BP 1.7+ "Groups" menu item is available
 */
function szgh_has_groups_admin_menu() {

	/**
	 * Hack to detect new toplevel Groups menu item
	 */
	global $admin_page_hooks;
	return isset( $admin_page_hooks['sz-groups'] );
}

/************************************
 * Utility and replacement functions
 ***********************************/

function sz_group_hierarchy_copy_vars($from, &$to, $attribs) {
	foreach($attribs as $var) {
		if(isset($from->$var)) {
			$to->$var = $from->$var;
		}
	}
}

/**
 * Catch requests for groups by parent and use SZ_Groups_Hierarchy::get_by_parent to handle
 */
function sz_group_hierarchy_get_by_hierarchy($args) {

	$defaults = array(
		'type' => 'active', // active, newest, alphabetical, random, popular, most-forum-topics or most-forum-posts
		'user_id' => false, // Pass a user_id to limit to only groups that this user is a member of
		'search_terms' => false, // Limit to groups that match these search terms

		'per_page' => 20, // The number of results to return per page
		'page' => 1, // The page to return if limiting per page
		'parent_id' => 0, //
		'populate_extras' => true, // Fetch meta such as is_banned and is_member
	);
	
	$params = wp_parse_args( $args, $defaults );
	
	extract( $params, EXTR_SKIP );
	
	if(isset($parent_id)) {
		$groups = SZ_Groups_Hierarchy::get_by_parent( $parent_id, $type, $per_page, $page, $user_id, $search_terms, $populate_extras );
	}
	return $groups;
}

/**
 * Function for creating groups with parents programmatically
 * @param array Args same as groups_create_group, but accepts a 'parent_id' param
 */
function groups_hierarchy_create_group( $args = '' ) {
	if( $group_id = groups_create_group( $args ) ) {
		if( isset( $args['parent_id'] ) ) {
			$group = new SZ_Groups_Hierarchy( $group_id );
			$group->parent_id = (int)$args['parent_id'];
			$group->save();
		}
		return $group_id;
	}
	return false;
}

/** Alias for sz_get_groups_root_slug originally for BP 1.2 compat */
function sz_get_groups_hierarchy_root_slug() {
	_deprecated_function( __FUNCTION__, '1.3.2', 'sz_get_groups_root_slug' );
	return sz_get_groups_root_slug();

}