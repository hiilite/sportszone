<?php

add_filter( 'sz_optional_components', 'sz_group_hierarchy_overload_groups' );
add_filter( 'sz_current_action', 'group_hierarchy_override_current_action' );
add_filter( 'sz_has_groups', 'sz_group_hierarchy_override_template', 10, 2 );
add_filter( 'sz_get_group_permalink', 'sz_group_hierarchy_fixup_permalink' );
add_filter( 'sz_forums_get_forum_topics', 'sz_group_hierarchy_fixup_forum_paths', 10, 2 );
add_filter( 'sz_has_topic_posts', 'sz_group_hierarchy_fixup_forum_links', 10, 2 );

/**
 * Catch requests for the groups component and find the requested group
 */
function group_hierarchy_override_current_action( $current_action ) {
	global $sz;
	
	do_action( 'sz_group_hierarchy_route_requests' );

	/** Only process once - hopefully this won't have any side effects */
	remove_filter( 'sz_current_action', 'group_hierarchy_override_current_action' );
	
	/** Abort processing on dashboard pages and when not in groups component */
	if( is_admin() && ! strpos( admin_url('admin-ajax.php'), $_SERVER['REQUEST_URI'] ) ) {
		return $current_action;
	}
	
	if( ! sz_is_groups_component() ) {
		return $current_action;
	}
	
	$groups_slug = sz_get_groups_root_slug();

	sz_group_hierarchy_debug('Routing request');
	sz_group_hierarchy_debug('Current component: ' . $sz->current_component);
	sz_group_hierarchy_debug('Current action: ' . $current_action);
	sz_group_hierarchy_debug('Groups slug: ' . $groups_slug);
	sz_group_hierarchy_debug('Are we on a user profile page?: ' . ( empty($sz->displayed_user->id) ? 'N' : 'Y' ));

	if($current_action == '')	return $current_action;
	
	if( ! empty($sz->displayed_user->id) || in_array($current_action, apply_filters( 'groups_forbidden_names', array( 'my-groups', 'create', 'invites', 'send-invites', 'forum', 'delete', 'add', 'admin', 'request-membership', 'members', 'settings', 'avatar', $groups_slug, '' ) ) ) ) {
		sz_group_hierarchy_debug('Not rewriting current action.');
		return $current_action;
	}
	
	$action_vars = $sz->action_variables;

	$group = new SZ_Groups_Hierarchy( $current_action );

	if( ! $group->id && ( ! isset( $sz->current_item ) || ! $sz->current_item ) ) {
		$current_action = '';
		sz_group_hierarchy_debug( 'Group not found - returning 404.' );
		sz_do_404();
		return;
	}

	if( $group->has_children() ) {
		$parent_id = $group->id;
		foreach($sz->action_variables as $action_var) {
			$subgroup_id = SZ_Groups_Hierarchy::check_slug($action_var, $parent_id);
			if($subgroup_id) {
				$action_var = array_shift($action_vars);
				$current_action .= '/' . $action_var;
				$parent_id = $subgroup_id;
			} else {
				// once we find something that isn't a group, we're done
				break;
			}
		}
	}

	sz_group_hierarchy_debug('Action changed to: ' . $current_action);

	$sz->action_variables = $action_vars;
	$sz->current_action = $current_action;
	
	return $current_action;
}


/**
 *	Override group retrieval for global $groups_template,
 *	replacing every SZ_Groups_Group with a SZ_Groups_Hierarchy object
 *  @return int|bool number of matching groups or FALSE if none
 */
function sz_group_hierarchy_override_template($has_groups) {
	
	global $sz, $groups_template;

	if(!$has_groups)	return false;
	
	$groups_hierarchy_template = new SZ_Groups_Hierarchy_Template();

	sz_group_hierarchy_copy_vars(
		$groups_template,
		$groups_hierarchy_template, 
		array(
			'group',
			'group_count',
			'groups',
			'single_group',
			'total_group_count',
			'pag_links',
			'pag_num',
			'pag_page'
		)
	);

	$groups_hierarchy_template->synchronize();

	foreach($groups_hierarchy_template->groups as $key => $group) {
		if(isset($group->id)) {
			$groups_hierarchy_template->groups[$key] = new SZ_Groups_Hierarchy($group->id, 0, array( 'populate_extras' => true ) );
		}
	}
	$groups_template = $groups_hierarchy_template;
	
	return $has_groups;
}


/**
 * Fix forum topic permalinks for subgroups
 */
function sz_group_hierarchy_fixup_forum_paths( $topics ) {
	
	// replace each simple slug with its full path
	if(is_array($topics)) {
		foreach($topics as $key => $topic) {
	
			$group_id = SZ_Groups_Group::group_exists($topic->object_slug);
			if($group_id) {
				$topics[$key]->object_slug = SZ_Groups_Hierarchy::get_path( $group_id );
			}
		}
	}
	return $topics;
	
}

/**
 * Fix forum topic action links (Edit, Delete, Close, Sticky, etc.)
 */
function sz_group_hierarchy_fixup_forum_links( $has_topics ) {
	global $forum_template;
	
	$group_id = SZ_Groups_Group::group_exists( $forum_template->topic->object_slug );
	$forum_template->topic->object_slug = SZ_Groups_Hierarchy::get_path( $group_id );
	
	return $has_topics;
	
}

/**
 * Override the group slug in permalinks with a group's full path
 */
function sz_group_hierarchy_fixup_permalink( $permalink ) {
	
	global $sz;
	
	$group_slug = substr( $permalink, strlen( $sz->root_domain . '/' . sz_get_groups_root_slug() . '/' ), -1 );
	
	if(strpos($group_slug,'/'))	return $permalink;
	
	$group_id = SZ_Groups_Group::get_id_from_slug( $group_slug );
	
	if( $group_id ) {
		$group_path = SZ_Groups_Hierarchy::get_path( $group_id );
		return str_replace( '/' . $group_slug . '/', '/' . $group_path . '/', $permalink );
	}
	return $permalink;
	
}


/**
 * Load the normal SZ_Groups_Component, then quickly replace it with the derived class and prevent re-loading
 * This loads the Groups component out of order, but testing has revealed no issues
 */
function sz_group_hierarchy_overload_groups( $components ) {

	require dirname(__FILE__) . '/functions.php';
	
	if( is_admin() && ! strpos( admin_url('admin-ajax.php'), $_SERVER['REQUEST_URI'] ) )	return $components;
	
	global $sz;

	$components = array_flip( $components );

	if( array_key_exists( 'groups', $components ) ) {

		require( SZ_PLUGIN_DIR . '/sz-groups/sz-groups-loader.php' );

		remove_action( 'sz_setup_components', 'sz_setup_groups', 6);
		add_action( 'sz_setup_components', 'sz_setup_groups_hierarchy', 6);
		
		require dirname(__FILE__) . '/loader.php';
		
	}

	unset($components['groups']);
	$components = array_flip( $components );
	
	return $components;
	
}
?>