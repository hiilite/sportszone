<?php

if( ! class_exists( 'SZ_Groups_Template') ) {
	// Groups component is not enabled; don't initialize this template class
	return;
}

/**
 * Hierarchy-aware extension of BP 1.2 Groups template class
 */
class SZ_Groups_Hierarchy_Template extends SZ_Groups_Template {

	var $vars = array();
	
	function __construct( ) { 
		
		$args = func_get_args();
		if( is_array( $args ) && count( $args ) > 1 ) {
			list(
				$params['user_id'],
				$params['type'],
				$params['page'],
				$params['per_page'],
				$params['max'],
				$params['slug'],
				$params['search_terms'],
				$params['populate_extras'],
				$params['parent_id']
			) = $args;

			$params['page'] = isset( $_REQUEST['grpage'] ) ? intval( $_REQUEST['grpage'] ) : $params['page'];
			$params['per_page']  = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : $params['per_page'];

			// TODO: handle order, orderby
			

			$this->params = $params;
			
			array_push( $args, '' );
			array_push( $args, '' );
			
			/**
			 * BP 1.7 switched to a single array param from the painstakingly-arranged series of params above
			 */
			if( (float)sz_get_version() >= 1.7 ) {
				parent::__construct( $params );
			} else {
				call_user_func_array( array( 'parent', '__construct' ), $args );
			}
			
			$this->synchronize();
		} else {
			$this->params = array();
		}
	}

	/**
	 * Since we don't always have access to the params passed to SZ_Groups_Template
	 * we have to wait until after constructor has run to fill in details
	 */
	function synchronize() {
		global $sz;
		
		if(isset($this->params) && array_key_exists('parent_id',$this->params)) {
	
			/**
			 * Fill in requests by parent_id for tree traversal on admin side
			 */
			$this->groups = sz_group_hierarchy_get_by_hierarchy($this->params);
			
			$this->total_group_count = $this->groups['total'];
			$this->groups = $this->groups['groups'];
			$this->group_count = count($this->groups);

			// Re-build pagination links with new group counts
			if ( (int)$this->total_group_count && (int)$this->pag_num ) {
				$this->pag_links = paginate_links( array(
					'base'      => add_query_arg( array( 'grpage' => '%#%', 'num' => $this->pag_num, 'sortby' => $this->sort_by, 'order' => $this->order ) ),
					'format'    => '',
					'total'     => ceil( (int)$this->total_group_count / (int)$this->pag_num ),
					'current'   => $this->pag_page,
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
					'mid_size'  => 1
				) );
			}

			
		} else if($this->single_group && $sz->groups->current_group) {
			/**
			 * Groups with multi-level slugs are missed by the parent.
			 * Fill them in from $sz->groups->current_group
			 */
			$this->groups = array(
				(object)array(
					'group_id'	=> sz_get_current_group_id()
				)
			);
			$this->group_count = 1;
		}
		
	}

	function the_group() {
		global $group;

		$this->in_the_loop = true;
		$this->group = $this->next_group();

		if ( $this->single_group )
			$this->group = groups_get_current_group();
		else {
			if ( $this->group )
				wp_cache_set( 'groups_group_nouserdata_' . $this->group->id, $this->group, 'bp' );
		}

		if ( 0 == $this->current_group ) // loop has just started
			do_action('loop_start');
	}

	function __isset($varName) {
		return array_key_exists($varName,$this->vars);
	}
	
	function __set($varName, $value) {
		$this->vars[$varName] = $value;
	}
	
	function __get($varName) {
		return $this->vars[$varName];
	}

}

/****************************************
 * Functions for use by theme developers
 ****************************************/

/**
 * Echo the fully-qualified name of the group (including all parents)
 */
function sz_group_hierarchy_full_name() {
	echo sz_group_hierarchy_get_full_name();
}
function sz_get_group_hierarchy_full_name( $separator = '|', $group = false ) {
	_deprecated_function( __FUNCTION__, '1.1.8', 'sz_group_hierarchy_get_full_name()' );
	return sz_group_hierarchy_get_full_name( $separator, $group );
}

/**
 * Return the fully-qualified name of the group (including all parents)
 * @param string $separator a string to display in between path components
 * @param SZ_Groups_Hierarchy_Template $group optional group object (only needed if not in the loop)
 */
function sz_group_hierarchy_get_full_name( $separator = '|', $group = false ) {
	global $groups_template;
	
	if ( !$group ) {
		/** need a copy since we're going to walk up the tree */
		$group = $groups_template->group;
	}
	$group_name = $group->name;
	
	while($group->parent_id != 0) {
		$group = new SZ_Groups_Hierarchy($group->parent_id);
		$group_name = $group->name . ' ' . $separator . ' ' . $group_name;
	}
	
	return $group_name;
}

/**
 * Echo the name selected for the Group Tree
 */
function sz_group_hierarchy_group_tree_name() {
	echo sz_group_hierarchy_get_group_tree_name();
}

/**
 * Return the name selected for the Group Tree
 */
function sz_group_hierarchy_get_group_tree_name() {
	global $sz;
	return $sz->group_hierarchy->extension_settings['group_tree_name'];
}

/**
 * Echo breadcrumbs for the current group
 */
function sz_group_hierarchy_breadcrumbs() {
	echo sz_group_hierarchy_get_breadcrumbs();
}

/**
 * Build breadcrumbs for a group
 * @param string $separator a string to display in between path components
 * @param SZ_Groups_Hierarchy_Template $group optional group object (only needed if not in the loop)
 */
function sz_group_hierarchy_get_breadcrumbs( $separator = '|', $group = false ) {
	global $groups_template;
	
	$groups_slug = sz_get_groups_root_slug();
	
	if ( !$group ) {
		/** need a copy since we're going to walk up the tree */
		$group = $groups_template->group;
	}
	$group_name = '<a href="/' . $groups_slug . '/' . $group->slug . '" title="' . $group->name . '">' . $group->name . '</a>';
	
	while($group->parent_id != 0) {
		$group = new SZ_Groups_Hierarchy($group->parent_id);
		$group_name = '<a href="/' . $groups_slug . '/' . $group->slug . '" title="' . $group->name . '">' . $group->name . '</a> ' . $separator . ' ' . $group_name;
	}
	
	return $group_name;
}
 

/**
 * Get the number of subgroups
 * @param SZ_Groups_Hierarchy_Template $group optional group object (only needed if not in the loop)
 */
function sz_group_hierarchy_has_subgroups( $group = null ) {
	global $groups_template;
	
	if ( !$group ) {
		$group =& $groups_template->group;
	}
	
	if(isset($group->child_group_count))	return $group->child_group_count;
	
	$child_count = count(SZ_Groups_Hierarchy::has_children( $group->id ));
	$group->child_group_count = $child_count;
	
	return $child_count;
}

/**
 * Get an array of a group's children (direct descendants)
 * NOTE: please do not use this to create a pseudo-loop for child groups.
 *       Just use sz_has_groups_hierarchy and life will be better
 * @param SZ_Groups_Hierarchy_Template $group optional group object (only needed if not in the loop)
 */
function sz_group_hierarchy_get_subgroups( $group = null ) {
	global $groups_template;
	
	if ( !$group ) {
		$group =& $groups_template->group;
	}
	
	$children = SZ_Groups_Hierarchy::has_children( $group->id );
	
	return $children;
}

/**
 * Return whether the selected group is top-level
 * @param SZ_Groups_Hierarchy_Template $group optional group object (only needed if not in the loop)
 * @return boolean TRUE if group has a parent, false if top-level
 */
function sz_group_hierarchy_has_parent( $group = null ) {
	global $groups_template;
	if ( !$group ) {
		$group =& $groups_template->group;
	}
	
	return $group->parent_id != 0;
}

/**
 * Return an array of the selected group's ancestors
 * For top-level groups, this array is empty
 * You can count the elements in the array to find the depth
 * @param SZ_Groups_Hierarchy_Template $group optional group object (only needed if not in the loop)
 */
function sz_group_hierarchy_get_parents( $group = null ) {
	global $groups_template;
	
	if ( !$group ) {
		/** need a copy since we're going to walk up the tree */
		$group = $groups_template->group;
	}
	
	$parents = array();
	
	while($group->parent_id != 0) {
		$parents[] = $group->parent_id;
		$group = new SZ_Groups_Hierarchy($group->parent_id);
	}
	
	return $parents;
}

/**
 * Hierarchy-aware replacement for sz_has_groups
 */
function sz_has_groups_hierarchy($args = '') {
	global $groups_template, $sz;

	/***
	 * Set the defaults based on the current page. Any of these will be overridden
	 * if arguments are directly passed into the loop. Custom plugins should always
	 * pass their parameters directly to the loop.
	 */
	$type = 'active';
	$user_id = false;
	$search_terms = false;
	$slug = false;

	/* User filtering */
	if ( sz_displayed_user_id() )
		$user_id = sz_displayed_user_id();

	/* Type */
	if ( 'my-groups' == sz_current_action() ) {
		if ( 'most-popular' == $order )
			$type = 'popular';
		else if ( 'alphabetically' == $order )
			$type = 'alphabetical';
	} else if ( 'invites' == sz_current_action() ) {
		$type = 'invites';
	} else if ( sz_get_current_group_slug() ) {
		$type = 'single-group';
		$slug =  sz_get_current_group_slug();
	}

	if ( isset( $_REQUEST['group-filter-box'] ) || isset( $_REQUEST['s'] ) )
		$search_terms = ( isset( $_REQUEST['group-filter-box'] ) ) ? $_REQUEST['group-filter-box'] : $_REQUEST['s'];

	$defaults = array(
		'type' => $type,
		'page' => 1,
		'per_page' => 20,
		'max' => false,

		'user_id' => $user_id, // Pass a user ID to limit to groups this user has joined
		'slug' => $slug, // Pass a group slug to only return that group
		'search_terms' => $search_terms, // Pass search terms to return only matching groups

		'populate_extras' => true // Get extra meta - is_member, is_banned
	);

	$r = wp_parse_args( $args, $defaults );

	extract( $r );

	$groups_template = new SZ_Groups_Hierarchy_Template( (int)$user_id, $type, (int)$page, (int)$per_page, (int)$max, $slug, $search_terms, (bool)$populate_extras, (int)$parent_id );

	return apply_filters( 'sz_has_groups', $groups_template->has_groups(), $groups_template );

}

?>