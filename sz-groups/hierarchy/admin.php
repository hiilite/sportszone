<?php

/**
 * 
 * Admin options
 * 
 */

add_action( 'network_admin_menu', 'sz_group_hierarchy_extension_admin' );
add_action( 'admin_menu', 'sz_group_hierarchy_extension_admin' );

/**
 * Load the Group Hierarchy settings dashboard page and the Toplevel group permissions filter
 */
function sz_group_hierarchy_extension_admin() {
	
	if( szgh_has_groups_admin_menu() ) {
		add_submenu_page( 'sz-groups', __('Hierarchy Settings','sportszone'), __('Hierarchy Settings','sportszone'), 'manage_options', 'sz_group_hierarchy_settings', 'sz_group_hierarchy_admin_page' );
	} else {
		add_submenu_page( 'sz-general-settings', __('Group Hierarchy','sportszone'), __('Group Hierarchy','sportszone'), 'manage_options', 'sz_group_hierarchy_settings', 'sz_group_hierarchy_admin_page' );
	}
	add_filter( 'sz_group_hierarchy_toplevel_subgroup_permissions', 'sz_group_hierarchy_limit_toplevel_permissions_options' );
	
	do_action( 'sz_group_hierarchy_admin_loaded' );
}

/**
 * Filter subgroup permissions options available to toplevel groups, since membership-based constructs don't apply
 */
function sz_group_hierarchy_limit_toplevel_permissions_options( $options ) {

	$options = array_flip($options);
	$options = array_filter( $options, create_function(
		'$option',
		'return strpos($option,"one") !== false;'
	) );
	return array_flip($options);
}

/**
 * Display admin page
 */
function sz_group_hierarchy_admin_page() {
	global $sz, $wpdb;
	
	$updated = false;
	
	if(isset($_POST['save-settings']) && check_admin_referer( 'sz_group_hierarchy_extension_options' )) {
		
		$options = $_POST['options'];
		
		update_site_option( 'szgh_extension_show_group_tree', isset($options['show_group_tree']));
		update_site_option( 'szgh_extension_hide_group_list', isset($options['hide_group_list']));
		update_site_option( 'szgh_extension_toplevel_group_permission', $options['toplevel_group_permission']);
		update_site_option( 'szgh_extension_group_tree_name', $options['group_tree_name']);
		update_site_option( 'szgh_extension_nav_item_name',   $options['nav_item_name']);
		
		// allow plugins to receive saved data
		do_action( 'szgh_admin_after_save', $options );
		
		$updated = true;
	}
	
	$options = array(
		'show_group_tree'			=> get_site_option( 'szgh_extension_show_group_tree', false ),
		'hide_group_list'			=> get_site_option( 'szgh_extension_hide_group_list', false ),
		'toplevel_group_permission'	=> get_site_option( 'szgh_extension_toplevel_group_permission', 'anyone'),
		'group_tree_name'			=> get_site_option( 'szgh_extension_group_tree_name', __('Group Tree','sportszone') ),
		'nav_item_name'				=> get_site_option( 'szgh_extension_nav_item_name', __('Member Groups','sportszone') )
	);
	?>
	<div class="wrap">
		<?php if($updated) { ?><div id="message" class="updated"><p><strong><?php _e('Settings saved.'); ?></strong></p></div><?php } ?>
		<h2><?php _e('Group Hierarchy Settings','sportszone'); ?></h2>
		<form method="post">
			<h3>Options</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="show_group_tree"><?php _e('Show Group Tree','sportszone') ?></label></th>
					<td>
						<label>
							<input type="checkbox" id="show_group_tree" name="options[show_group_tree]"<?php checked($options['show_group_tree']); ?> />
							<?php _e('Show the Group Tree view on the Groups page along with the flat list of groups.','sportszone'); ?>
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="hide_group_list"><?php _e('Hide Group List','sportszone') ?></label></th>
					<td>
						<label>
							<input type="checkbox" id="hide_group_list" name="options[hide_group_list]"<?php checked($options['hide_group_list']); ?> />
							<?php _e('Hide the flat list of groups and show ONLY the Group Tree on the Groups page','sportszone'); ?> (EXPERIMENTAL)
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="hide_group_list"><?php _e('Toplevel Groups','sportszone') ?></label></th>
					<td>
						<label>
							<select id="toplevel_group_permission" name="options[toplevel_group_permission]">
								<?php
								$subgroup_permission_options = apply_filters( 'sz_group_hierarchy_toplevel_subgroup_permissions', $sz->group_hierarchy->subgroup_permission_options );
								foreach($subgroup_permission_options as $option => $text) { ?>
									<option value="<?php echo $option ?>" <?php if($option == $options['toplevel_group_permission']) echo ' selected'; ?>><?php echo $text ?></option>
								<?php }	?>
							</select>
							<?php _e('Select who is allowed to create toplevel groups. Super admins can always create toplevel groups.','sportszone'); ?>
						</label>
					</td>
				</tr>
				<?php 
				
				// allow plugins to add options
				do_action( 'szgh_admin_after_settings' ); 
				
				?>
			</table>
			<h3>Labels</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="nav_item_name"><?php _e('Nav Item','sportszone'); ?></label></th>
					<td>
						<input type="text" id="nav_item_name" name="options[nav_item_name]" value="<?php echo $options['nav_item_name'] ?>" /><br />
						<?php _e("Name of the nav item on an individual group's page.",'sportszone'); ?>
						<?php _e("Use <code>%d</code> to include the number of child groups.",'sportszone'); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="group_tree_name"><?php _e('Group Tree','sportszone'); ?></label></th>
					<td>
						<input type="text" id="group_tree_name" name="options[group_tree_name]" value="<?php echo $options['group_tree_name'] ?>" /><br />
						<?php _e('Name of the Group Tree listing on the main Groups page.','sportszone'); ?>
					</td>
				</tr>
			</table>
			<?php submit_button( __('Save Changes'), 'primary', 'save-settings' ); ?>
			<?php wp_nonce_field( 'sz_group_hierarchy_extension_options' ); ?>
		</form>
	</div>
	<?php
}
