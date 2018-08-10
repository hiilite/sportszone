<?php

/** This function appears to work when loaded at sz_loaded, but more research is needed */
add_action( 'sz_loaded', 'sz_group_hierarchy_init' );

add_action( 'sz_loaded', 'sz_group_hierarchy_load_components' );
add_action( 'sz_setup_globals', 'sz_group_hierarchy_setup_globals' );
add_action( 'sz_groups_delete_group', 'sz_group_hierarchy_rescue_child_groups' );

/**
 * Set up global variables
 */
function sz_group_hierarchy_setup_globals() {
	global $sz, $wpdb;

	/* For internal identification */
	$sz->group_hierarchy = new stdClass();
	$sz->group_hierarchy->id = 'group_hierarchy';
	$sz->group_hierarchy->table_name = $wpdb->base_prefix . 'sz_group_hierarchy';
	$sz->group_hierarchy->slug = SZ_GROUP_HIERARCHY_SLUG;
	
	/* Register this in the active components array */
	$sz->active_components[$sz->group_hierarchy->slug] = $sz->group_hierarchy->id;
	
	do_action( 'sz_group_hierarchy_globals_loaded' );
}

/**
 * Activate group extension
 */
function sz_group_hierarchy_init() {
	
	/** Enable logging with WP Debug Logger */
	$GLOBALS['wp_log_plugins'][] = 'sz_group_hierarchy';
	
	/** Ensure BP is loaded before loading admin portion */
	require ( dirname( __FILE__ ) . '/admin.php' );
	require ( dirname( __FILE__ ) . '/extension.php' );
	
}

/**
 * Add hook for intercepting requests before they're routed by normal BP processes
 */
function sz_group_hierarchy_load_components() {

	if( version_compare( (float)sz_get_version(), '1.9', '>=' ) ) {
		// Load BP 1.9+ class
		require ( dirname( __FILE__ ) . '/classes.php' );
	} 
	
	require ( dirname( __FILE__ ) . '/template.php' );

	if( is_admin() && ! strpos( admin_url('admin-ajax.php'), $_SERVER['REQUEST_URI'] ) ) return;
	
	do_action( 'sz_group_hierarchy_components_loaded' );
}

/**
 * Before deleting a group, move all its child groups to its immediate parent.
 */
function sz_group_hierarchy_rescue_child_groups( &$parent_group ) {

	$parent_group_id = $parent_group->id;

	if($child_groups = SZ_Groups_Hierarchy::has_children( $parent_group_id )) {
		
		$group = new SZ_Groups_Hierarchy($parent_group_id);
		if($group) {
			$new_parent_group_id = $group->parent_id;
		} else {
			$new_parent_group_id = 0;
		}
		
		foreach($child_groups as $group_id) {
			$child_group = new SZ_Groups_Hierarchy($group_id);
			$child_group->parent_id = $new_parent_group_id;
			$child_group->save();
		}
	}
}

?>