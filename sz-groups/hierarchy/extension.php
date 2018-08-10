<?php
/**
 * 
 * This file contains a reference user interface for hierarchical groups.
 * One part is the Groups extension that adds the Member Groups tab to groups 
 * and allows creators to place new groups within the hierarchy.
 * The other is an administrative and permissions interface for that feature
 * 
 * This is not tied to the implementation of hierarchical groups contained in the other files
 * 
 */

/**
 * Save the parent when creating a new group as early as possible
 * This hook was added in BP 1.6, and must be called before the SZ_Group_Extension class exists
 */
add_action( 'groups_create_group_step_save_group-details', 'sz_group_hierarchy_save_parent_selection' );

if( ! class_exists( 'SZ_Group_Extension') ) {
	// Groups component is not enabled; don't initialize the extension
	return;
}

class SZ_Groups_Hierarchy_Extension extends SZ_Group_Extension {
	
	var $visibility = 'public';
	
	/**
	 * Disable metabox in BP 1.7 Group Edit screen until something is written to fill it :)
	 */
	var $enable_admin_item = false;
	
	public function __construct() {
		
		global $sz;
		
		$this->name = __( 'Group Hierarchy', 'sz-group-hierarchy' );
		$this->nav_item_name = apply_filters( 
			'sz_group_hierarchy_extension_tab_name',
			get_site_option( 'szgh_extension_nav_item_name', __('Member Groups %d','sz-group-hierarchy') ),
			$sz->groups->current_group
		);
		
		if( isset( $sz->groups->current_group ) && $sz->groups->current_group ) {
			$this->nav_item_name = str_replace( '%d', '<span>%d</span>', $this->nav_item_name );
			
			// Only count subgroups if admin has a placeholder in the nav item name
			if( strpos( $this->nav_item_name, '%d' ) !== FALSE )
				$this->nav_item_name = sprintf($this->nav_item_name, SZ_Groups_Hierarchy::get_total_subgroup_count( $sz->groups->current_group->id ) );
		}
		
		$this->slug = SZ_GROUP_HIERARCHY_SLUG;
		
		if( isset( $_COOKIE['sz_new_group_parent_id'] ) ) {
			$sz->group_hierarchy->new_group_parent_id = $_COOKIE['sz_new_group_parent_id'];
			add_action( 'sz_after_group_details_creation_step', array( &$this, 'add_parent_selection' ) );
		}
		$this->create_step_position = 6;
		$this->nav_item_position = 61;

		$this->subgroup_permission_options = array(
			'anyone'		=> __('Anybody','sz-group-hierarchy'),
			'noone'			=> __('Nobody','sz-group-hierarchy'),
			'group_members'	=> __('only Group Members','sz-group-hierarchy'),
			'group_admins'	=> __('only Group Admins','sz-group-hierarchy')
		);
		$sz->group_hierarchy->subgroup_permission_options = $this->subgroup_permission_options;
		
		if(isset($sz->groups->current_group) && $sz->groups->current_group) {
			$sz->groups->current_group->can_create_subitems = sz_group_hierarchy_can_create_subgroups();
		}
		
		$this->enable_nav_item = $this->enable_nav_item();
		
		// BP 1.8+ initiation process -- for future compatibility
		if( method_exists( $this, 'init' ) ) {

			$args = array(
				'name'              => $this->name,
				'slug'              => SZ_GROUP_HIERARCHY_SLUG,
				'enable_nav_item'   => $this->enable_nav_item(),
				'nav_item_name'     => $this->nav_item_name,
				'nav_item_position' => $this->nav_item_position,
				'screens'           => array(
					'create'           => array(
						'position'        => $this->create_step_position
					),
					'edit'             => array(
						'name'            => $this->name // Didn't want to pass an empty array
					)
				)
			);
			
			parent::init( $args );
		}
	}
	
	public static function get_default_permission_option() {
		return 'group_members';
	}
	
	function enable_nav_item() {
		global $sz;
		
		if( is_admin() )	return false;
		if( ! is_object($sz->groups->current_group) )	return false;
		
		/** Only display the nav item for admins, those who can create subgroups, or everyone if the group has subgroups */
		if (
				$sz->is_item_admin || 
				$sz->groups->current_group->can_create_subitems || 
				SZ_Groups_Hierarchy::has_children( $sz->groups->current_group->id )
			) {
			return apply_filters( 'sz_group_hierarchy_show_member_groups', true );
		}
		return false;
	}
	
	function add_parent_selection() {
		global $sz;
		if(!sz_is_group_creation_step( 'group-details' )) {
			return false;
		}
		
		$parent_group = new SZ_Groups_Hierarchy( $sz->group_hierarchy->new_group_parent_id );
		
		?>
		<label for="group-parent_id"><?php _e( 'Parent Group', 'sz-group-hierarchy' ); ?></label>
		<input type="hidden" name="group-parent_id" id="group-parent_id" value="<?php echo $parent_group->id ?>" />
		<?php echo $parent_group->name ?>
		<?php
	}
	 
	function create_screen( $group_id = null ) {
		
		global $sz;

		if(!sz_is_group_creation_step( $this->slug )) {
			return false;
		}
				
		$this_group = new SZ_Groups_Hierarchy( $sz->groups->new_group_id );

		if(isset($_COOKIE['sz_new_group_parent_id'])) { 
			$this_group->parent_id = $_COOKIE['sz_new_group_parent_id'];
			$this_group->save();
		}

		$groups = SZ_Groups_Hierarchy::get( array(
				'type'		=>'alphabetical', 
				'per_page'	=> null, 
				'page'		=> null, 
				'user_id'	=> 0, 
				'search_term'=> false, 
				'include'	=> false, 
				'populate_extras' => true, 
				'exclude'	=> $sz->groups->new_group_id 
			));
		
		$site_root = new stdClass();
		$site_root->id = 0;
		$site_root->name = __( 'Site Root', 'sz-group-hierarchy' );
		
		$display_groups = array(
			$site_root
		);
		foreach($groups['groups'] as $group) {
			$display_groups[] = $group;
		}
		
		/* deprecated */
		$display_groups = apply_filters( 'sz_group_hierarchy_display_groups', $display_groups );
		
		$display_groups = apply_filters( 'sz_group_hierarchy_available_parent_groups', $display_groups, $this_group );

		?>
		<label for="parent_id"><?php _e( 'Parent Group', 'sz-group-hierarchy' ); ?></label>
		<select name="parent_id" id="parent_id">
			<!--<option value="0"><?php _e( 'Site Root', 'sz-group-hierarchy' ); ?></option>-->
			<?php foreach($display_groups as $group) { ?>
				<option value="<?php echo $group->id ?>"<?php if($group->id == $this_group->parent_id) echo ' selected'; ?>><?php echo stripslashes( $group->name ); ?></option>
			<?php } ?>
		</select>
		<?php

		$subgroup_permission_options = apply_filters( 'sz_group_hierarchy_subgroup_permission_options', $this->subgroup_permission_options, $this_group );
		
		$current_subgroup_permission = groups_get_groupmeta( $sz->groups->current_group->id, 'sz_group_hierarchy_subgroup_creators' );
		if($current_subgroup_permission == '')
			$current_subgroup_permission = $this->get_default_permission_option();
		
		$permission_select = '<select name="allow_children_by" id="allow_children_by">';
		foreach($subgroup_permission_options as $option => $text) {
			$permission_select .= '<option value="' . $option . '"' . (($option == $current_subgroup_permission) ? ' selected' : '') . '>' . $text . '</option>' . "\n";
		}
		$permission_select .= '</select>';
		?>
		<p>
			<label for="allow_children_by"><?php _e( 'Member Groups', 'sz-group-hierarchy' ); ?></label>
			<?php printf( __( 'Allow %1$s to create %2$s', 'sz-group-hierarchy' ), $permission_select, __( 'Member Groups', 'sz-group-hierarchy' ) ); ?>
		</p>
		<?php
		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}
	
	function create_screen_save( $group_id = null ) {
		global $sz;
		
		check_admin_referer( 'groups_create_save_' . $this->slug );

		setcookie( 'sz_new_group_parent_id', false, time() - 1000, COOKIEPATH );
		
		/** save the selected parent_id */
		$parent_id = (int)$_POST['parent_id'];
		
		if(sz_group_hierarchy_can_create_subgroups( $sz->loggedin_user->id, $parent_id )) {
			$sz->groups->current_group = new SZ_Groups_Hierarchy( $sz->groups->new_group_id );
	
			$sz->groups->current_group->parent_id = $parent_id;
			$sz->groups->current_group->save();
		}

		/** save the selected subgroup permission setting */
		$permission_options = apply_filters( 'sz_group_hierarchy_subgroup_permission_options', $this->subgroup_permission_options );
		if(array_key_exists( $_POST['allow_children_by'], $permission_options )) {
			$allow_children_by = $_POST['allow_children_by'];
		} else {
			$allow_children_by = $this->get_default_permission_option();
		}
		
		groups_update_groupmeta( $sz->groups->current_group->id, 'sz_group_hierarchy_subgroup_creators', $allow_children_by );
		
	}
	
	function edit_screen( $group_id = null ) {

		global $sz;

		if(!sz_is_group_admin_screen( $this->slug )) {
			return false;
		}
		
		if(is_super_admin()) {

			$exclude_groups = SZ_Groups_Hierarchy::get_by_parent( $sz->groups->current_group->id );
			if(count($exclude_groups['groups']) > 0) {
				foreach($exclude_groups['groups'] as $key => $exclude_group) {
					$exclude_groups['groups'][$key] = $exclude_group->id;
				}
				$exclude_groups = $exclude_groups['groups'];
			} else {
				$exclude_groups = array();
			}
			$exclude_groups[] = $sz->groups->current_group->id;
			
			$groups = SZ_Groups_Hierarchy::get( array(
				'type'		=>'alphabetical', 
				'per_page'	=> null, 
				'page'		=> null, 
				'user_id'	=> 0, 
				'search_term'=> false, 
				'include'	=> false, 
				'populate_extras' => true, 
				'exclude'	=> $exclude_groups
			));
			
			$site_root = new stdClass();
			$site_root->id = 0;
			$site_root->name = __( 'Site Root', 'sz-group-hierarchy' );
			
			$display_groups = array(
				$site_root
			);
			foreach($groups['groups'] as $group) {
				$display_groups[] = $group;
			}
			
			/* deprecated */
			$display_groups = apply_filters( 'sz_group_hierarchy_display_groups', $display_groups );
			
			$display_groups = apply_filters( 'sz_group_hierarchy_available_parent_groups', $display_groups );
			
			?>
			<label for="parent_id"><?php _e( 'Parent Group', 'sz-group-hierarchy' ); ?></label>
			<select name="parent_id" id="parent_id">
				<?php foreach($display_groups as $group) { ?>
					<option value="<?php echo $group->id ?>"<?php if($group->id == $sz->groups->current_group->parent_id) echo ' selected'; ?>><?php echo stripslashes($group->name); ?></option>
				<?php } ?>
			</select>
			<?php
		} else {
			?>
			<div id="message">
				<p><?php _e('Only a site administrator can edit the group hierarchy.', 'sz-group-hierarchy' ); ?></p>
			</div>
			<?php
		}
		
		if(is_super_admin() || sz_group_is_admin()) {
				
			$subgroup_permission_options = apply_filters( 'sz_group_hierarchy_subgroup_permission_options', $this->subgroup_permission_options );
			
			$current_subgroup_permission = groups_get_groupmeta( $sz->groups->current_group->id, 'sz_group_hierarchy_subgroup_creators' );
			if($current_subgroup_permission == '')
				$current_subgroup_permission = $this->get_default_permission_option();
			
			$permission_select = '<select name="allow_children_by" id="allow_children_by">';
			foreach($subgroup_permission_options as $option => $text) {
				$permission_select .= '<option value="' . $option . '"' . (($option == $current_subgroup_permission) ? ' selected' : '') . '>' . $text . '</option>' . "\n";
			}
			$permission_select .= '</select>';
			?>
			<p>
				<label for="allow_children_by"><?php _e( 'Member Groups', 'sz-group-hierarchy' ); ?></label>
				<?php printf( __( 'Allow %1$s to create %2$s', 'sz-group-hierarchy' ), $permission_select, __( 'Member Groups', 'sz-group-hierarchy' ) ); ?>
			</p>
			<p>
				<input type="submit" class="button primary" id="save" name="save" value="<?php _e( 'Save Changes', 'sz-group-hierarchy' ); ?>" />
			</p>
			<?php
			wp_nonce_field( 'groups_edit_save_' . $this->slug );
			
		}
	}
	
	function edit_screen_save( $group_id = null ) {
		global $sz;
		
		if( !isset($_POST['save']) ) {
			return false;
		}
		
		check_admin_referer( 'groups_edit_save_' . $this->slug );

		/** save the selected subgroup permission setting */
		$permission_options = apply_filters( 'sz_group_hierarchy_subgroup_permission_options', $this->subgroup_permission_options );
		if(array_key_exists( $_POST['allow_children_by'], $permission_options )) {
			$allow_children_by = $_POST['allow_children_by'];
		} else if(groups_get_groupmeta( $sz->groups->current_group->id, 'sz_group_hierarchy_subgroup_creators' ) != '') {
			$allow_children_by = groups_get_groupmeta( $sz->groups->current_group->id, 'sz_group_hierarchy_subgroup_creators' );
		} else {
			$allow_children_by = $this->get_default_permission_option();
		}
		
		groups_update_groupmeta( $sz->groups->current_group->id, 'sz_group_hierarchy_subgroup_creators', $allow_children_by );
		
		if(is_super_admin()) {
			/** save changed parent_id */
			$parent_id = (int)$_POST['parent_id'];
			
			if( sz_group_hierarchy_can_create_subgroups( $sz->loggedin_user->id, $sz->groups->current_group->id ) ) {
				$sz->groups->current_group->parent_id = $parent_id;
				$success = $sz->groups->current_group->save();
			}
			
			if( !$success ) {
				sz_core_add_message( __( 'There was an error saving; please try again.', 'sz-group-hierarchy' ), 'error' );
			} else {
				sz_core_add_message( __( 'Group hierarchy settings saved successfully.', 'sz-group-hierarchy' ) );
			}
		}
		
		sz_core_redirect( sz_get_group_admin_permalink( $sz->groups->current_group ) );
	}
	
	function display($page = 1) {
		global $sz, $groups_template;
		
		$parent_template = $groups_template;
		$hide_button = false;
		
		if(isset($_REQUEST['grpage'])) {
			$page = (int)$_REQUEST['grpage'];
		} else if(!is_numeric($page)) {
			$page = 1;
		} else {
			$page = (int)$page;
		}
		
		/** Respect BuddyPress group creation restriction */
		if(function_exists('sz_user_can_create_groups')) {
			$hide_button = !sz_user_can_create_groups();
		}
		
		sz_has_groups_hierarchy(array(
			'type'		=> 'alphabetical',
			'parent_id'	=> $sz->groups->current_group->id,
			'page'		=> $page
		));
		
		?>
		<div class="group">

			<?php if(($sz->is_item_admin || $sz->groups->current_group->can_create_subitems) && !$hide_button) { ?>
			<div class="generic-button group-button">
				<a title="<?php printf( __( 'Create a %s', 'sz-group-hierarchy' ),__( 'Member Group', 'sz-group-hierarchy' ) ) ?>" href="<?php echo $sz->root_domain . '/' . sz_get_groups_root_slug() . '/' . 'create' .'/?parent_id=' . $sz->groups->current_group->id ?>"><?php printf( __( 'Create a %s', 'sz-group-hierarchy' ),__( 'Member Group', 'sz-group-hierarchy' ) ) ?></a>
			</div><br /><br />
			<?php } ?>

		<?php if($groups_template && count($groups_template->groups) > 0) : ?>

			<div id="pag-top" class="pagination">
				<div class="pag-count" id="group-dir-count-top">
					<?php sz_groups_pagination_count() ?>
				</div>
		
				<div class="pagination-links" id="group-dir-pag-top">
					<?php sz_groups_pagination_links() ?>
				</div>
			</div>
	
			<ul id="groups-list" class="item-list">
				<?php while ( sz_groups() ) : sz_the_group(); ?>
				<?php $subgroup = $groups_template->group; ?>
				<?php if($subgroup->status == 'hidden' && !( groups_is_user_member( $sz->loggedin_user->id, $subgroup->id ) || groups_is_user_admin( $sz->loggedin_user->id, $sz->groups->current_group->id ) ) ) continue; ?>
				<li id="tree-childof_<?php sz_group_id() ?>">
					<div class="item-avatar">
						<a href="<?php sz_group_permalink() ?>"><?php sz_group_avatar( 'type=thumb&width=50&height=50' ) ?></a>
					</div>
		
					<div class="item">
						<div class="item-title"><a href="<?php sz_group_permalink() ?>"><?php sz_group_name() ?></a></div>
						<div class="item-meta"><span class="activity"><?php printf( __( 'active %s', 'sportszone' ), sz_get_group_last_active() ); ?></span></div>
						<div class="item-desc"><?php sz_group_description_excerpt() ?></div>
		
						<?php do_action( 'sz_directory_groups_item' ) ?>
		
					</div>
		
					<div class="action">
						<?php do_action( 'sz_directory_groups_actions' ) ?>
						<div class="meta">
							<?php sz_group_type() ?> / <?php sz_group_member_count() ?>
						</div>
					</div>
					<div class="clear"></div>
				</li>
		
				<?php endwhile; ?>
			</ul>
			<div id="pag-bottom" class="pagination">
		
				<div class="pag-count" id="group-dir-count-bottom">
					<?php sz_groups_pagination_count() ?>
				</div>
		
				<div class="pagination-links" id="group-dir-pag-bottom">
					<?php sz_groups_pagination_links() ?>
				</div>
		
			</div>
			<script type="text/javascript">
			jQuery('#nav-hierarchy-personal-li').attr('id','group-hierarchy-personal-li');
			jQuery('#nav-hierarchy-groups-li').attr('id','group-hierarchy-group-li');
			</script>
			
		<?php else: ?>
		<p><?php _e('No member groups were found.','sz-group-hierarchy'); ?></p>
		<?php endif; ?>
		</div>
		<?php
		// reset the $groups_template global and continue with the page
		$groups_template = $parent_template;
	}
}

sz_register_group_extension( 'SZ_Groups_Hierarchy_Extension' );

/**
 * 
 * Group creation permission / restriction functions
 * 
 */

/**
 * Store the ID of the group the user selected as the parent for group creation
 */
function sz_group_hierarchy_set_parent_id_cookie() {
	global $sz;
	
	if( sz_is_groups_component() && $sz->current_action == 'create' && isset( $_REQUEST['parent_id'] ) && $_REQUEST['parent_id'] != 0 ) {
		
		if( sz_group_hierarchy_can_create_subgroups( $sz->loggedin_user->id, (int)$_REQUEST['parent_id'] ) ) {
			setcookie( 'sz_new_group_parent_id', (int)$_REQUEST['parent_id'], time() + 1000, COOKIEPATH );
		} else {
			do_action( 'sz_group_hierarchy_unauthorized_parent', (int)$_REQUEST['parent_id'] );
		}
		
	}
}
add_action( 'sz_group_hierarchy_route_requests', 'sz_group_hierarchy_set_parent_id_cookie' );

/**
 * Save the parent group even before the extension has loaded in BP 1.6+
 */
function sz_group_hierarchy_save_parent_selection() {

	global $sz;

	if( isset( $_COOKIE['sz_new_group_parent_id'] ) ) {
		$this_group = new SZ_Groups_Hierarchy( $sz->groups->new_group_id );
		$this_group->parent_id = $_COOKIE['sz_new_group_parent_id'];
		$this_group->save();
	}
	
}


/**
 * Check whether the user is allowed to create subgroups of the selected group
 * 	and to see the Create a Member Group button
 * @param int UserID ID of the user whose access is being checked (or current user if omitted)
 * @param int GroupID ID of the group being checked (or group being displayed if omitted)
 * @return bool TRUE if permitted, FALSE otherwise
 */
function sz_group_hierarchy_can_create_subgroups( $user_id = null, $group_id = null ) {
	global $sz;

	if(is_null($user_id)) {
		$user_id = $sz->loggedin_user->id;
	}
	if(is_null($group_id)) {
		$group_id = $sz->groups->current_group->id;
	}

	if(is_super_admin()) {
		return true;
	}

	if($group_id == 0) {
		$subgroup_permission = get_site_option('szgh_extension_toplevel_group_permission','anyone');
	} else {
		$subgroup_permission = groups_get_groupmeta( $group_id, 'sz_group_hierarchy_subgroup_creators');
	}
	if($subgroup_permission == '') {
		$subgroup_permission = SZ_Groups_Hierarchy_Extension::get_default_permission_option();
	}
	switch($subgroup_permission) {
		case 'noone':
			return false;
			break;
		case 'anyone':
			return (is_user_logged_in() || get_site_option( 'szgh_extension_allow_anon_subgroups', false ) );
			break;
		case 'group_members':
			return groups_is_user_member( $user_id, $group_id );
			break;
		case 'group_admins':
			return groups_is_user_admin( $user_id, $group_id );
			break;
		default:
			if(
				has_filter('sz_group_hierarchy_enforce_subgroup_permission_' . $subgroup_permission ) && 
				apply_filters( 'sz_group_hierarchy_enforce_subgroup_permission_' . $subgroup_permission, false, $user_id, $group_id )) 
			{
				return true;
			}
			break;
	}
	return false;
}

/**
 * Enforce subgroup creation restrictions in parent group selection boxes
 */
function sz_group_hierarchy_enforce_subgroup_permissions( $groups ) {
	
	global $sz;
	
	/** super admins can add subgroups to any group */
	if(is_super_admin()) {
		return $groups;
	}
	
	if($allowed_groups = wp_cache_get( 'subgroup_creation_permitted_' . $sz->loggedin_user->id, 'sz_group_hierarchy' )) {
		return $allowed_groups;
	}
	
	$allowed_groups = array();
	foreach($groups as $group) {
		if(sz_group_hierarchy_can_create_subgroups( $sz->loggedin_user->id, $group->id )) {
			$allowed_groups[] = $group;
		}
	}
	wp_cache_set( 'subgroup_creation_permitted_' . $sz->loggedin_user->id, $allowed_groups, 'sz_group_hierarchy' );
	return $allowed_groups;
}
add_filter( 'sz_group_hierarchy_available_parent_groups', 'sz_group_hierarchy_enforce_subgroup_permissions' );


/**
 * 
 * Hierarchical Group Display functions
 * These are controlled by admin settings - see admin section, below
 */

function sz_group_hierarchy_tab() {
	global $sz;
	?>
	<li id="tree-all"><a href="<?php echo sz_get_root_domain() . '/' . sz_get_groups_root_slug() . '/?tree' ?>"><?php echo $sz->group_hierarchy->extension_settings['group_tree_name'] ?></a></li>
	<?php
}

/**
 * Functions for new 'tree' object-based hierachy display, which supports a new template
 */
 
/** 
 * Filter group results when requesting as part of the tree 
 */
function sz_group_hierarchy_display( $query_string, $object, $parent_id = 0 ) {
	if($object == 'tree') {
		if(isset($_POST['scope']) && $_POST['scope'] != 'all') {
			$parent_id = substr($_POST['scope'],8);
			$parent_id = (int)$parent_id;
		}
		$query_string .= '&parent_id=' . $parent_id;
		if($parent_id != 0) {
			$query_string .= '&per_page=100';
		}
		add_filter( 'groups_get_groups', 'sz_group_hierarchy_get_groups_tree', 10, 2 );
	}
	return $query_string;
}
add_filter( 'sz_ajax_querystring', 'sz_group_hierarchy_display', 20, 2 );

/** 
 * Load the tree loop instead of the group loop when requested as part of the tree 
 */
function sz_group_hierarchy_object_template_loader() {
	$object = esc_attr( $_POST['object'] );
	if($object == 'tree') {
		if($template = apply_filters('sz_located_template',locate_template( array( "$object/$object-loop.php" ), false ), "$object/$object-loop.php" )) {
			load_template($template);
			die();
		} else {
			sz_group_hierarchy_debug('Failed to find loop template for object: ' . $object);
		}
	}
}
add_action( 'wp_ajax_tree_filter', 'sz_group_hierarchy_object_template_loader' );
add_action( 'wp_ajax_nopriv_tree_filter', 'sz_group_hierarchy_object_template_loader' );

function sz_group_hierarchy_display_member_group_pages() {
	die(SZ_Groups_Hierarchy_Extension::display($_POST['page']));
}
add_action( 'wp_ajax_group_filter', 'sz_group_hierarchy_display_member_group_pages');
add_action( 'wp_ajax_nopriv_group_filter', 'sz_group_hierarchy_display_member_group_pages');

/** 
 * Enable loading template files from the plugin directory
 * This plugin only has template files for group pages, so pass on any requests for other components
 */
function sz_group_hierarchy_load_template_filter( $found_template, $templates ) {
	
	/** Starting in BP 1.6, group list page (or maybe AJAX requests from it) is not in the groups component */
	if ( ! sz_is_groups_component() && ! isset( $_POST['object'] ) )
		return $found_template;
	
	$filtered_templates = array();
	foreach ( (array) $templates as $template ) {
		if ( file_exists( STYLESHEETPATH . '/' . $template ) ) {
			$filtered_templates[] = STYLESHEETPATH . '/' . $template;
		} else if ( file_exists( TEMPLATEPATH . '/' . $template ) ) {
			$filtered_templates[] = TEMPLATEPATH . '/' . $template;
		} else if ( file_exists( dirname( __FILE__ ) . '/templates/' . $template ) ) {
			$filtered_templates[] = dirname( __FILE__ ) . '/templates/' . $template;
		} else {
			sz_group_hierarchy_debug( 'Could not locate the requested template file: ' . $template );
		}
	}
	
	if(count($filtered_templates) == 0 ) {
		return $found_template;
	}
	
	$found_template = $filtered_templates[0];
	return $found_template;
}
add_filter( 'sz_located_template', 'sz_group_hierarchy_load_template_filter', 10, 2 );

/**
 * Restrict group listing to top-level groups
 */
function sz_group_hierarchy_get_groups_tree( $groups, $params, $parent_id = 0 ) {
	global $sz, $groups_template;
	
	if( isset($_POST['scope']) && $_POST['object'] == 'tree' && $_POST['scope'] != 'all' ) {
		$parent_id = substr( $_POST['scope'], 8 );
		$parent_id = (int)$parent_id;
	}
	
	/** 
	 * Replace retrieved list with toplevel groups
	 * unless on a group page (member groups list) or viewing "My Groups" 
	 */
	if( ! isset( $sz->groups->current_group->id ) && ! $params['user_id'] ) {

		/** remove search placeholder text for BP 1.5 */
//		if( function_exists( 'sz_get_search_default_text' ) && trim( $params['search_terms'] ) == sz_get_search_default_text( 'groups' ) )	$params['search_terms'] = '';
		
		if( empty( $params['search_terms'] ) ) {
	
			$params['parent_id'] = $parent_id;
			
			$toplevel_groups = sz_group_hierarchy_get_by_hierarchy( $params );
			$groups = $toplevel_groups;
		}
		
	}
	return $groups;
}

/**
 * Strip the SPAN tags from the HTML title
 */
function sz_group_hierarchy_clean_title( $full_title ) {
	return strip_tags( html_entity_decode( $full_title ) );
}

/**
 * Change the HTML title to reflect custom Group Tree name
 */
function sz_group_hierarchy_group_tree_title(  $title, $sep, $sep_location = null ) {
	global $sz;
	if($sep_location != null) {
		return str_replace(
			sprintf( __( '%s Directory', 'sportszone' ), sz_get_name_from_root_slug() ),
			sz_group_hierarchy_clean_title( $sz->group_hierarchy->extension_settings['group_tree_name'] ),
			$title
		);
 	}
 	
 	// I think this is left over from BP 1.2, so just return the title
	return $title;
}

/************************************************************
 * Enforce toplevel group creation restrictions on the UI
 */

/**
 * If the user doesn't have any place to create a new group, don't let him create a group
 * @param boolean Return control behavior if user cannot create groups - if TRUE, return FALSE; if FALSE, die
 * TODO: this parameter is poorly named / implemented
 */
function sz_group_hierarchy_assert_parent_available( $return = false ) {
	global $sz;
	
	if(is_super_admin())	return true;
	
	if( $cache_result = wp_cache_get( $sz->loggedin_user->id, 'szgh_has_available_parent_group' ) ) {
		if( $cache_result == 'true' ) {
			return true;
		}
		if( $return ) {
			return false;
		} else {
			wp_die( __( 'Sorry, you are not allowed to create groups.', 'sportszone' ), __( 'Sorry, you are not allowed to create groups.', 'sportszone' ) );
		}
	}
	
	$group_permission = get_site_option('szgh_extension_toplevel_group_permission');
	if(
		$group_permission == 'anyone' || 
		(has_filter('sz_group_hierarchy_enforce_subgroup_permission_' . $group_permission ) && 
		apply_filters( 'sz_group_hierarchy_enforce_subgroup_permission_' . $group_permission, false, $sz->loggedin_user->id, 0 ))
	) {
		/** If the user can create top-level groups, we're done looking */
		wp_cache_set( $sz->loggedin_user->id, 'true', 'szgh_has_available_parent_group' );
		return true;
	}
	
	$user_groups = groups_get_groups(array('user_id'=>$sz->loggedin_user->id));
	
	foreach($user_groups['groups'] as $group) {

		if( sz_group_hierarchy_can_create_subgroups( $sz->loggedin_user->id, $group->id ) ) {
			/** If the user can create subgroups here, we're done looking */
			wp_cache_set( $sz->loggedin_user->id, 'true', 'szgh_has_available_parent_group' );
			return true;
		}
	}

	wp_cache_set( $sz->loggedin_user->id, 'false', 'szgh_has_available_parent_group' );
	if($return) {
		return false;
	} else {
		wp_die( __( 'Sorry, you are not allowed to create groups.', 'sportszone' ), __( 'Sorry, you are not allowed to create groups.', 'sportszone' ) );
	}

}
add_action( 'sz_before_create_group', 'sz_group_hierarchy_assert_parent_available' );

/**
 * (BP 1.5+) Hide the Create a New Group button if the user doesn't have a place to create new groups
 */
function sz_group_hierarchy_can_create_any_group( $permitted, $global_setting ) {
	return $permitted && sz_group_hierarchy_assert_parent_available(true);
}
add_filter( 'sz_user_can_create_groups', 'sz_group_hierarchy_can_create_any_group', 10, 2 );


/**
 * (BP 1.7+) Add plugin templates folder to list of available template paths
 */
function sz_group_hierarchy_register_template_location() {
	return dirname( __FILE__ ) . '/templates/';
}

/**
 * (BP 1.7+) Replace groups/index with tree/index-compat depending on user settings
 */
function sz_group_hierarchy_maybe_replace_group_loop_template( $templates, $slug, $name ) {
	
	if( 'groups/index' != $slug )
		return $templates;
	
	return array( 'tree/index-compat.php' );
}

/**
 * Get the party started
 */
function sz_group_hierarchy_extension_init() {
	global $sz;
	
	add_action( 'wp_ajax_groups_tree_filter', 'sz_dtheme_object_template_loader' );
	add_action( 'wp_ajax_nopriv_groups_tree_filter', 'sz_dtheme_object_template_loader' );
	
	/** Register templates folder for theme compatibility support when available */
	if( function_exists( 'sz_register_template_stack' ) )
		sz_register_template_stack( 'sz_group_hierarchy_register_template_location' );
	
	$sz->group_hierarchy->extension_settings = array(
		'show_group_tree'	=> get_site_option( 'szgh_extension_show_group_tree', false ),
		'hide_group_list'	=> get_site_option( 'szgh_extension_hide_group_list', false ),
		'nav_item_name'		=> get_site_option( 'szgh_extension_nav_item_name', __('Member Groups (%d)','sz-group-hierarchy') ),
		'group_tree_name'	=> get_site_option( 'szgh_extension_group_tree_name', __('Group Tree','sz-group-hierarchy') )
	);

	wp_register_script( 'sz-group-hierarchy-tree-script', plugins_url( 'includes/hierarchy.js', __FILE__ ), array('jquery') );
	
	/** Load the hierarchy.css file from the user's theme, if available */
	if( $hierarchy_css = apply_filters( 'sz_located_template', locate_template( array( '_inc/css/hierarchy.css' ), false ), '_inc/css/hierarchy.css' ) ) {

		// Detect when loading CSS from the plugin dir and rewrite with plugins_url for better MS / symlink support
		if( 0 === strpos( $hierarchy_css, dirname( __FILE__ ) ) )
			$hierarchy_css = plugins_url( 'templates/_inc/css/hierarchy.css', __FILE__ );
		
		wp_register_style( 'sz-group-hierarchy-tree-style', str_replace(array(substr(ABSPATH,0,-1),'\\'), array('','/'), $hierarchy_css) );
	}
	
	if(sz_is_groups_component() && $sz->current_action == '' && $sz->group_hierarchy->extension_settings['hide_group_list']) {
		add_filter( 'groups_get_groups', 'sz_group_hierarchy_get_groups_tree', 10, 2 );
		
		add_filter( 'wp_title', 'sz_group_hierarchy_group_tree_title', 10, 3 );
		
		if( $sz->current_action == '' && ! isset( $_POST['object'] ) ) {
			wp_enqueue_script('sz-group-hierarchy-tree-script');
			wp_enqueue_style('sz-group-hierarchy-tree-style');
			
			/**
			 * Override BP's default group index with the tree
			 */
			if(
				! class_exists( 'SZ_Theme_Compat' ) || 
				current_theme_supports('sportszone') || 
				in_array( 'sz-default', array( get_stylesheet(), get_template() ) ) 
			) {
				add_filter( 'groups_template_directory_groups', create_function( '$template', 'return "tree/index";' ) );
			} else {
				add_filter( 'sz_get_template_part', 'sz_group_hierarchy_maybe_replace_group_loop_template', 10, 3 );
			}
		}
		
	} else if(sz_is_groups_component() && $sz->current_action == '' && $sz->group_hierarchy->extension_settings['show_group_tree']) {
		wp_enqueue_script('sz-group-hierarchy-tree-script');
		wp_enqueue_style('sz-group-hierarchy-tree-style');

		add_action( 'sz_groups_directory_group_filter', 'sz_group_hierarchy_tab' );
	}
	
}
add_action( 'init', 'sz_group_hierarchy_extension_init' );

?>