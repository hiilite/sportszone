<?php
/**
 * SportsZone Groups component admin screen.
 *
 * Props to WordPress core for the Comments admin screen, and its contextual
 * help text, on which this implementation is heavily based.
 *
 * @package SportsZone
 * @subpackage Groups
 * @since 1.7.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Include WP's list table class.
if ( !class_exists( 'WP_List_Table' ) ) require( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

// The per_page screen option. Has to be hooked in extremely early.
if ( is_admin() && ! empty( $_REQUEST['page'] ) && 'sz-groups' == $_REQUEST['page'] )
	add_filter( 'set-screen-option', 'sz_groups_admin_screen_options', 10, 3 );

/**
 * Register the Groups component admin screen.
 *
 * @since 1.7.0
 */
function sz_groups_add_admin_menu() {

	// Add our screen.
	$hook = add_menu_page(
		_x( 'Groups', 'Admin Groups page title', 'sportszone' ),
		_x( 'Groups', 'Admin Groups menu', 'sportszone' ),
		'sz_moderate',
		'sz-groups',
		'sz_groups_admin',
		'div'
	);

	add_menu_page( 
		__( 'Group Types Settings', 'sportszone' ), 
		__( 'Group Types', 'sportszone' ), 
		'sz_moderarte', 
		'sz-groups-types'
		
	);
	
	// Hook into early actions to load custom CSS and our init handler.
	add_action( "load-$hook", 'sz_groups_admin_load' );
	
	
}
add_action( sz_core_admin_hook(), 'sz_groups_add_admin_menu' );

/**
 * Add groups component to custom menus array.
 *
 * This ensures that the Groups menu item appears in the proper order on the
 * main Dashboard menu.
 *
 * @since 1.7.0
 *
 * @param array $custom_menus Array of BP top-level menu items.
 * @return array Menu item array, with Groups added.
 */
function sz_groups_admin_menu_order( $custom_menus = array() ) {
	array_push( $custom_menus, 'sz-groups' );
	return $custom_menus;
}
add_filter( 'sz_admin_menu_order', 'sz_groups_admin_menu_order' );

/**
 * Set up the Groups admin page.
 *
 * Loaded before the page is rendered, this function does all initial setup,
 * including: processing form requests, registering contextual help, and
 * setting up screen options.
 *
 * @since 1.7.0
 *
 * @global SZ_Groups_List_Table $sz_groups_list_table Groups screen list table.
 */
function sz_groups_admin_load() {
	global $sz_groups_list_table;

	// Build redirection URL.
	$redirect_to = remove_query_arg( array( 'action', 'action2', 'gid', 'deleted', 'error', 'updated', 'success_new', 'error_new', 'success_modified', 'error_modified' ), $_SERVER['REQUEST_URI'] );

	$doaction   = sz_admin_list_table_current_bulk_action();
	$min        = sz_core_get_minified_asset_suffix();

	/**
	 * Fires at top of groups admin page.
	 *
	 * @since 1.7.0
	 *
	 * @param string $doaction Current $_GET action being performed in admin screen.
	 */
	do_action( 'sz_groups_admin_load', $doaction );

	// Edit screen.
	if ( 'do_delete' == $doaction && ! empty( $_GET['gid'] ) ) {

		check_admin_referer( 'sz-groups-delete' );

		$group_ids = wp_parse_id_list( $_GET['gid'] );

		$count = 0;
		foreach ( $group_ids as $group_id ) {
			if ( groups_delete_group( $group_id ) ) {
				$count++;
			}
		}

		$redirect_to = add_query_arg( 'deleted', $count, $redirect_to );

		sz_core_redirect( $redirect_to );

	} elseif ( 'edit' == $doaction && ! empty( $_GET['gid'] ) ) {
		// Columns screen option.
		add_screen_option( 'layout_columns', array( 'default' => 2, 'max' => 2, ) );

		get_current_screen()->add_help_tab( array(
			'id'      => 'sz-group-edit-overview',
			'title'   => __( 'Overview', 'sportszone' ),
			'content' =>
				'<p>' . __( 'This page is a convenient way to edit the details associated with one of your groups.', 'sportszone' ) . '</p>' .
				'<p>' . __( 'The Name and Description box is fixed in place, but you can reposition all the other boxes using drag and drop, and can minimize or expand them by clicking the title bar of each box. Use the Screen Options tab to hide or unhide, or to choose a 1- or 2-column layout for this screen.', 'sportszone' ) . '</p>'
		) );

		// Help panel - sidebar links.
		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'sportszone' ) . '</strong></p>' .
			'<p><a href="https://sportszone.org/support">' . __( 'Support Forums', 'sportszone' ) . '</a></p>'
		);

		// Register metaboxes for the edit screen.
		add_meta_box( 'submitdiv', _x( 'Save', 'group admin edit screen', 'sportszone' ), 'sz_groups_admin_edit_metabox_status', get_current_screen()->id, 'side', 'high' );
		add_meta_box( 'sz_group_settings', _x( 'Settings', 'group admin edit screen', 'sportszone' ), 'sz_groups_admin_edit_metabox_settings', get_current_screen()->id, 'side', 'core' );
		add_meta_box( 'sz_group_add_members', _x( 'Add New Members', 'group admin edit screen', 'sportszone' ), 'sz_groups_admin_edit_metabox_add_new_members', get_current_screen()->id, 'normal', 'core' );
		add_meta_box( 'sz_group_members', _x( 'Manage Members', 'group admin edit screen', 'sportszone' ), 'sz_groups_admin_edit_metabox_members', get_current_screen()->id, 'normal', 'core' );

		// Group Type metabox. Only added if group types have been registered.
		$group_types = sz_groups_get_group_types();
		if ( ! empty( $group_types ) ) {
			add_meta_box(
				'sz_groups_admin_group_type',
				_x( 'Group Type', 'groups admin edit screen', 'sportszone' ),
				'sz_groups_admin_edit_metabox_group_type',
				get_current_screen()->id,
				'side',
				'core'
			);
		}

		/**
		 * Fires after the registration of all of the default group meta boxes.
		 *
		 * @since 1.7.0
		 */
		do_action( 'sz_groups_admin_meta_boxes' );

		// Enqueue JavaScript files.
		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'dashboard' );

	// Index screen.
	} else {
		// Create the Groups screen list table.
		$sz_groups_list_table = new SZ_Groups_List_Table();

		// The per_page screen option.
		add_screen_option( 'per_page', array( 'label' => _x( 'Groups', 'Groups per page (screen options)', 'sportszone' )) );

		// Help panel - overview text.
		get_current_screen()->add_help_tab( array(
			'id'      => 'sz-groups-overview',
			'title'   => __( 'Overview', 'sportszone' ),
			'content' =>
				'<p>' . __( 'You can manage groups much like you can manage comments and other content. This screen is customizable in the same ways as other management screens, and you can act on groups by using the on-hover action links or the Bulk Actions.', 'sportszone' ) . '</p>',
		) );

		get_current_screen()->add_help_tab( array(
			'id'      => 'sz-groups-overview-actions',
			'title'   => __( 'Group Actions', 'sportszone' ),
			'content' =>
				'<p>' . __( 'Clicking "Visit" will take you to the group&#8217;s public page. Use this link to see what the group looks like on the front end of your site.', 'sportszone' ) . '</p>' .
				'<p>' . __( 'Clicking "Edit" will take you to a Dashboard panel where you can manage various details about the group, such as its name and description, its members, and other settings.', 'sportszone' ) . '</p>' .
				'<p>' . __( 'If you click "Delete" under a specific group, or select a number of groups and then choose Delete from the Bulk Actions menu, you will be led to a page where you&#8217;ll be asked to confirm the permanent deletion of the group(s).', 'sportszone' ) . '</p>',
		) );

		// Help panel - sidebar links.
		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'sportszone' ) . '</strong></p>' .
			'<p>' . __( '<a href="https://sportszone.org/support/">Support Forums</a>', 'sportszone' ) . '</p>'
		);

		// Add accessible hidden heading and text for Groups screen pagination.
		get_current_screen()->set_screen_reader_content( array(
			/* translators: accessibility text */
			'heading_pagination' => __( 'Groups list navigation', 'sportszone' ),
		) );
	}

	$sz = sportszone();

	// Enqueue CSS and JavaScript.
	wp_enqueue_script( 'sz_groups_admin_js', $sz->plugin_url . "sz-groups/admin/js/admin{$min}.js", array( 'jquery', 'wp-ajax-response', 'jquery-ui-autocomplete' ), sz_get_version(), true );
	wp_localize_script( 'sz_groups_admin_js', 'SZ_Group_Admin', array(
		'add_member_placeholder' => __( 'Start typing a username to add a new member.', 'sportszone' ),
		'warn_on_leave'          => __( 'If you leave this page, you will lose any unsaved changes you have made to the group.', 'sportszone' ),
	) );
	wp_enqueue_style( 'sz_groups_admin_css', $sz->plugin_url . "sz-groups/admin/css/admin{$min}.css", array(), sz_get_version() );

	wp_style_add_data( 'sz_groups_admin_css', 'rtl', true );
	if ( $min ) {
		wp_style_add_data( 'sz_groups_admin_css', 'suffix', $min );
	}


	if ( $doaction && 'save' == $doaction ) {
		// Get group ID.
		$group_id = isset( $_REQUEST['gid'] ) ? (int) $_REQUEST['gid'] : '';

		$redirect_to = add_query_arg( array(
			'gid'    => (int) $group_id,
			'action' => 'edit'
		), $redirect_to );

		// Check this is a valid form submission.
		check_admin_referer( 'edit-group_' . $group_id );

		// Get the group from the database.
		$group = groups_get_group( $group_id );

		// If the group doesn't exist, just redirect back to the index.
		if ( empty( $group->slug ) ) {
			wp_redirect( $redirect_to );
			exit;
		}

		// Check the form for the updated properties.
		// Store errors.
		$error = 0;
		$success_new = $error_new = $success_modified = $error_modified = array();

		// Name, description and slug must not be empty.
		if ( empty( $_POST['sz-groups-name'] ) ) {
			$error = $error - 1;
		}
		if ( empty( $_POST['sz-groups-description'] ) ) {
			$error = $error - 2;
		}
		if ( empty( $_POST['sz-groups-slug'] ) ) {
			$error = $error - 4;
		}

		/*
		 * Group name, slug, and description are handled with
		 * groups_edit_base_group_details().
		 */
		if ( ! $error && ! groups_edit_base_group_details( array(
				'group_id'       => $group_id,
				'name'           => $_POST['sz-groups-name'],
				'slug'           => $_POST['sz-groups-slug'],
				'description'    => $_POST['sz-groups-description'],
				'notify_members' => false,
			) ) ) {
			$error = $group_id;
		}

		// Enable discussion forum.
		$enable_forum   = ( isset( $_POST['group-show-forum'] ) ) ? 1 : 0;

		/**
		 * Filters the allowed status values for the group.
		 *
		 * @since 1.0.2
		 *
		 * @param array $value Array of allowed group statuses.
		 */
		$allowed_status = apply_filters( 'groups_allowed_status', array( 'public', 'private', 'hidden' ) );
		$status         = ( in_array( $_POST['group-status'], (array) $allowed_status ) ) ? $_POST['group-status'] : 'public';

		/**
		 * Filters the allowed invite status values for the group.
		 *
		 * @since 1.5.0
		 *
		 * @param array $value Array of allowed invite statuses.
		 */
		$allowed_invite_status = apply_filters( 'groups_allowed_invite_status', array( 'members', 'mods', 'admins' ) );
		$invite_status	       = in_array( $_POST['group-invite-status'], (array) $allowed_invite_status ) ? $_POST['group-invite-status'] : 'members';

		if ( !groups_edit_group_settings( $group_id, $enable_forum, $status, $invite_status ) ) {
			$error = $group_id;
		}

		// Process new members.
		$user_names = array();

		if ( ! empty( $_POST['sz-groups-new-members'] ) ) {
			$user_names = array_merge( $user_names, explode( ',', $_POST['sz-groups-new-members'] ) );
		}

		if ( ! empty( $user_names ) ) {

			foreach( array_values( $user_names ) as $user_name ) {
				$un = trim( $user_name );

				// Make sure the user exists before attempting
				// to add to the group.
				$user = get_user_by( 'slug', $un );

				if ( empty( $user ) ) {
					$error_new[] = $un;
				} else {
					if ( ! groups_join_group( $group_id, $user->ID ) ) {
						$error_new[]   = $un;
					} else {
						$success_new[] = $un;
					}
				}
			}
		}

		// Process member role changes.
		if ( ! empty( $_POST['sz-groups-role'] ) && ! empty( $_POST['sz-groups-existing-role'] ) ) {

			// Before processing anything, make sure you're not
			// attempting to remove the all user admins.
			$admin_count = 0;
			foreach ( (array) $_POST['sz-groups-role'] as $new_role ) {
				if ( 'admin' == $new_role ) {
					$admin_count++;
					break;
				}
			}

			if ( ! $admin_count ) {

				$redirect_to = add_query_arg( 'no_admins', 1, $redirect_to );
				$error = $group_id;

			} else {

				// Process only those users who have had their roles changed.
				foreach ( (array) $_POST['sz-groups-role'] as $user_id => $new_role ) {
					$user_id = (int) $user_id;

					$existing_role = isset( $_POST['sz-groups-existing-role'][$user_id] ) ? $_POST['sz-groups-existing-role'][$user_id] : '';

					if ( $existing_role != $new_role ) {
						$result = false;

						switch ( $new_role ) {
							case 'mod' :
								// Admin to mod is a demotion. Demote to
								// member, then fall through.
								if ( 'admin' == $existing_role ) {
									groups_demote_member( $user_id, $group_id );
								}

							case 'admin' :
								// If the user was banned, we must
								// unban first.
								if ( 'banned' == $existing_role ) {
									groups_unban_member( $user_id, $group_id );
								}

								// At this point, each existing_role
								// is a member, so promote.
								$result = groups_promote_member( $user_id, $group_id, $new_role );

								break;

							case 'member' :

								if ( 'admin' == $existing_role || 'mod' == $existing_role ) {
									$result = groups_demote_member( $user_id, $group_id );
								} elseif ( 'banned' == $existing_role ) {
									$result = groups_unban_member( $user_id, $group_id );
								}

								break;

							case 'banned' :

								$result = groups_ban_member( $user_id, $group_id );

								break;

							case 'remove' :

								$result = groups_remove_member( $user_id, $group_id );

								break;
						}

						// Store the success or failure.
						if ( $result ) {
							$success_modified[] = $user_id;
						} else {
							$error_modified[]   = $user_id;
						}
					}
				}
			}
		}

		/**
		 * Fires before redirect so plugins can do something first on save action.
		 *
		 * @since 1.6.0
		 *
		 * @param int $group_id ID of the group being edited.
		 */
		do_action( 'sz_group_admin_edit_after', $group_id );

		// Create the redirect URL.
		if ( $error ) {
			// This means there was an error updating group details.
			$redirect_to = add_query_arg( 'error', (int) $error, $redirect_to );
		} else {
			// Group details were update successfully.
			$redirect_to = add_query_arg( 'updated', 1, $redirect_to );
		}

		if ( !empty( $success_new ) ) {
			$success_new = implode( ',', array_filter( $success_new, 'urlencode' ) );
			$redirect_to = add_query_arg( 'success_new', $success_new, $redirect_to );
		}

		if ( !empty( $error_new ) ) {
			$error_new = implode( ',', array_filter( $error_new, 'urlencode' ) );
			$redirect_to = add_query_arg( 'error_new', $error_new, $redirect_to );
		}

		if ( !empty( $success_modified ) ) {
			$success_modified = implode( ',', array_filter( $success_modified, 'urlencode' ) );
			$redirect_to = add_query_arg( 'success_modified', $success_modified, $redirect_to );
		}

		if ( !empty( $error_modified ) ) {
			$error_modified = implode( ',', array_filter( $error_modified, 'urlencode' ) );
			$redirect_to = add_query_arg( 'error_modified', $error_modified, $redirect_to );
		}

		/**
		 * Filters the URL to redirect to after successfully editing a group.
		 *
		 * @since 1.7.0
		 *
		 * @param string $redirect_to URL to redirect user to.
		 */
		wp_redirect( apply_filters( 'sz_group_admin_edit_redirect', $redirect_to ) );
		exit;


	// If a referrer and a nonce is supplied, but no action, redirect back.
	} elseif ( ! empty( $_GET['_wp_http_referer'] ) ) {
		wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) );
		exit;
	}
}

/**
 * Handle save/update of screen options for the Groups component admin screen.
 *
 * @since 1.7.0
 *
 * @param string $value     Will always be false unless another plugin filters it first.
 * @param string $option    Screen option name.
 * @param string $new_value Screen option form value.
 * @return string|int Option value. False to abandon update.
 */
function sz_groups_admin_screen_options( $value, $option, $new_value ) {
	if ( 'toplevel_page_sz_groups_per_page' != $option && 'toplevel_page_sz_groups_network_per_page' != $option )
		return $value;

	// Per page.
	$new_value = (int) $new_value;
	if ( $new_value < 1 || $new_value > 999 )
		return $value;

	return $new_value;
}

/**
 * Select the appropriate Groups admin screen, and output it.
 *
 * @since 1.7.0
 */
function sz_groups_admin() {
	// Decide whether to load the index or edit screen.
	$doaction = sz_admin_list_table_current_bulk_action();

	// Display the single group edit screen.
	if ( 'edit' == $doaction && ! empty( $_GET['gid'] ) ) {
		sz_groups_admin_edit();

	// Display the group deletion confirmation screen.
	} elseif ( 'delete' == $doaction && ! empty( $_GET['gid'] ) ) {
		sz_groups_admin_delete();

	// Otherwise, display the groups index screen.
	} else {
		sz_groups_admin_index();
	}
}

/**
 * Display the single groups edit screen.
 *
 * @since 1.7.0
 */
function sz_groups_admin_edit() {

	if ( ! sz_current_user_can( 'sz_moderate' ) )
		die( '-1' );

	$messages = array();

	// If the user has just made a change to a group, build status messages.
	if ( !empty( $_REQUEST['no_admins'] ) || ! empty( $_REQUEST['error'] ) || ! empty( $_REQUEST['updated'] ) || ! empty( $_REQUEST['error_new'] ) || ! empty( $_REQUEST['success_new'] ) || ! empty( $_REQUEST['error_modified'] ) || ! empty( $_REQUEST['success_modified'] ) ) {
		$no_admins        = ! empty( $_REQUEST['no_admins']        ) ? 1                                             : 0;
		$errors           = ! empty( $_REQUEST['error']            ) ? $_REQUEST['error']                            : '';
		$updated          = ! empty( $_REQUEST['updated']          ) ? $_REQUEST['updated']                          : '';
		$error_new        = ! empty( $_REQUEST['error_new']        ) ? explode( ',', $_REQUEST['error_new'] )        : array();
		$success_new      = ! empty( $_REQUEST['success_new']      ) ? explode( ',', $_REQUEST['success_new'] )      : array();
		$error_modified   = ! empty( $_REQUEST['error_modified']   ) ? explode( ',', $_REQUEST['error_modified'] )   : array();
		$success_modified = ! empty( $_REQUEST['success_modified'] ) ? explode( ',', $_REQUEST['success_modified'] ) : array();

		if ( ! empty( $no_admins ) ) {
			$messages[] = __( 'You cannot remove all administrators from a group.', 'sportszone' );
		}

		if ( ! empty( $errors ) ) {
			if ( $errors < 0 ) {
				$messages[] = __( 'Group name, slug, and description are all required fields.', 'sportszone' );
			} else {
				$messages[] = __( 'An error occurred when trying to update your group details.', 'sportszone' );
			}

		} elseif ( ! empty( $updated ) ) {
			$messages[] = __( 'The group has been updated successfully.', 'sportszone' );
		}

		if ( ! empty( $error_new ) ) {
			$messages[] = sprintf( __( 'The following users could not be added to the group: %s', 'sportszone' ), '<em>' . esc_html( implode( ', ', $error_new ) ) . '</em>' );
		}

		if ( ! empty( $success_new ) ) {
			$messages[] = sprintf( __( 'The following users were successfully added to the group: %s', 'sportszone' ), '<em>' . esc_html( implode( ', ', $success_new ) ) . '</em>' );
		}

		if ( ! empty( $error_modified ) ) {
			$error_modified = sz_groups_admin_get_usernames_from_ids( $error_modified );
			$messages[] = sprintf( __( 'An error occurred when trying to modify the following members: %s', 'sportszone' ), '<em>' . esc_html( implode( ', ', $error_modified ) ) . '</em>' );
		}

		if ( ! empty( $success_modified ) ) {
			$success_modified = sz_groups_admin_get_usernames_from_ids( $success_modified );
			$messages[] = sprintf( __( 'The following members were successfully modified: %s', 'sportszone' ), '<em>' . esc_html( implode( ', ', $success_modified ) ) . '</em>' );
		}
	}

	$is_error = ! empty( $no_admins ) || ! empty( $errors ) || ! empty( $error_new ) || ! empty( $error_modified );

	// Get the group from the database.
	$group      = groups_get_group( (int) $_GET['gid'] );

	$group_name = isset( $group->name ) ? sz_get_group_name( $group ) : '';

	// Construct URL for form.
	$form_url = remove_query_arg( array( 'action', 'deleted', 'no_admins', 'error', 'error_new', 'success_new', 'error_modified', 'success_modified' ), $_SERVER['REQUEST_URI'] );
	$form_url = add_query_arg( 'action', 'save', $form_url );

	/**
	 * Fires before the display of the edit form.
	 *
	 * Useful for plugins to modify the group before display.
	 *
	 * @since 1.7.0
	 *
	 * @param SZ_Groups_Group $this Instance of the current group being edited. Passed by reference.
	 */
	do_action_ref_array( 'sz_groups_admin_edit', array( &$group ) ); ?>

	<div class="wrap">
		<?php if ( version_compare( $GLOBALS['wp_version'], '4.8', '>=' ) ) : ?>

			<h1 class="wp-heading-inline"><?php _e( 'Edit Group', 'sportszone' ); ?></h1>

			<?php if ( is_user_logged_in() && sz_user_can_create_groups() ) : ?>
				<a class="page-title-action" href="<?php echo trailingslashit( sz_get_groups_directory_permalink() . 'create' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
			<?php endif; ?>

			<hr class="wp-header-end">

		<?php else : ?>

			<h1><?php _e( 'Edit Group', 'sportszone' ); ?>

				<?php if ( is_user_logged_in() && sz_user_can_create_groups() ) : ?>
					<a class="add-new-h2" href="<?php echo trailingslashit( sz_get_groups_directory_permalink() . 'create' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
				<?php endif; ?>

			</h1>

		<?php endif; ?>

		<?php // If the user has just made a change to an group, display the status messages. ?>
		<?php if ( !empty( $messages ) ) : ?>
			<div id="moderated" class="<?php echo ( $is_error ) ? 'error' : 'updated'; ?>"><p><?php echo implode( "</p><p>", $messages ); ?></p></div>
		<?php endif; ?>

		<?php if ( $group->id ) : ?>

			<form action="<?php echo esc_url( $form_url ); ?>" id="sz-groups-edit-form" method="post">
				<div id="poststuff">

					<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
						<div id="post-body-content">
							<div id="postdiv">
								<div id="sz_groups_name" class="postbox">
									<h2><?php _e( 'Name and Description', 'sportszone' ); ?></h2>
									<div class="inside">
										<label for="sz-groups-name" class="screen-reader-text"><?php
											/* translators: accessibility text */
											_e( 'Group Name', 'sportszone' );
										?></label>
										<input type="text" name="sz-groups-name" id="sz-groups-name" value="<?php echo esc_attr( stripslashes( $group_name ) ) ?>" />
										<div id="sz-groups-permalink-box">
											<strong><?php esc_html_e( 'Permalink:', 'sportszone' ) ?></strong>
											<span id="sz-groups-permalink">
												<?php sz_groups_directory_permalink(); ?> <input type="text" id="sz-groups-slug" name="sz-groups-slug" value="<?php sz_group_slug( $group ); ?>" autocomplete="off"> /
											</span>
											<a href="<?php echo sz_group_permalink( $group ) ?>" class="button button-small" id="sz-groups-visit-group"><?php esc_html_e( 'Visit Group', 'sportszone' ) ?></a>
										</div>

										<label for="sz-groups-description" class="screen-reader-text"><?php
											/* translators: accessibility text */
											_e( 'Group Description', 'sportszone' );
										?></label>
										<?php wp_editor( stripslashes( $group->description ), 'sz-groups-description', array( 'media_buttons' => false, 'teeny' => true, 'textarea_rows' => 5, 'quicktags' => array( 'buttons' => 'strong,em,link,block,del,ins,img,code,spell,close' ) ) ); ?>
									</div>
								</div>
							</div>
						</div><!-- #post-body-content -->

						<div id="postbox-container-1" class="postbox-container">
							<?php do_meta_boxes( get_current_screen()->id, 'side', $group ); ?>
						</div>

						<div id="postbox-container-2" class="postbox-container">
							<?php do_meta_boxes( get_current_screen()->id, 'normal', $group ); ?>
							<?php do_meta_boxes( get_current_screen()->id, 'advanced', $group ); ?>
						</div>
					</div><!-- #post-body -->

				</div><!-- #poststuff -->
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
				<?php wp_nonce_field( 'edit-group_' . $group->id ); ?>
			</form>

		<?php else : ?>

			<p><?php
				printf(
					'%1$s <a href="%2$s">%3$s</a>',
					__( 'No group found with this ID.', 'sportszone' ),
					esc_url( sz_get_admin_url( 'admin.php?page=sz-groups' ) ),
					__( 'Go back and try again.', 'sportszone' )
				);
			?></p>

		<?php endif; ?>

	</div><!-- .wrap -->

<?php
}

/**
 * Display the Group delete confirmation screen.
 *
 * We include a separate confirmation because group deletion is truly
 * irreversible.
 *
 * @since 1.7.0
 */
function sz_groups_admin_delete() {

	if ( ! sz_current_user_can( 'sz_moderate' ) ) {
		die( '-1' );
	}

	$group_ids = isset( $_REQUEST['gid'] ) ? $_REQUEST['gid'] : 0;
	if ( ! is_array( $group_ids ) ) {
		$group_ids = explode( ',', $group_ids );
	}
	$group_ids = wp_parse_id_list( $group_ids );
	$groups    = groups_get_groups( array(
		'include'     => $group_ids,
		'show_hidden' => true,
		'per_page'    => null, // Return all results.
	) );

	// Create a new list of group ids, based on those that actually exist.
	$gids = array();
	foreach ( $groups['groups'] as $group ) {
		$gids[] = $group->id;
	}

	$base_url  = remove_query_arg( array( 'action', 'action2', 'paged', 's', '_wpnonce', 'gid' ), $_SERVER['REQUEST_URI'] ); ?>

	<div class="wrap">
		<h1><?php _e( 'Delete Groups', 'sportszone' ) ?></h1>
		<p><?php _e( 'You are about to delete the following groups:', 'sportszone' ) ?></p>

		<ul class="sz-group-delete-list">
		<?php foreach ( $groups['groups'] as $group ) : ?>
			<li><?php echo esc_html( sz_get_group_name( $group ) ); ?></li>
		<?php endforeach; ?>
		</ul>

		<p><strong><?php _e( 'This action cannot be undone.', 'sportszone' ) ?></strong></p>

		<a class="button-primary" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'do_delete', 'gid' => implode( ',', $gids ) ), $base_url ), 'sz-groups-delete' ) ); ?>"><?php _e( 'Delete Permanently', 'sportszone' ) ?></a>
		<a class="button" href="<?php echo esc_attr( $base_url ); ?>"><?php _e( 'Cancel', 'sportszone' ) ?></a>
	</div>

	<?php
}

/**
 * Display the Groups admin index screen.
 *
 * This screen contains a list of all SportsZone groups.
 *
 * @since 1.7.0
 *
 * @global SZ_Groups_List_Table $sz_groups_list_table Group screen list table.
 * @global string $plugin_page Currently viewed plugin page.
 */
function sz_groups_admin_index() {
	global $sz_groups_list_table, $plugin_page;

	$messages = array();

	// If the user has just made a change to a group, build status messages.
	if ( ! empty( $_REQUEST['deleted'] ) ) {
		$deleted  = ! empty( $_REQUEST['deleted'] ) ? (int) $_REQUEST['deleted'] : 0;

		if ( $deleted > 0 ) {
			$messages[] = sprintf( _n( '%s group has been permanently deleted.', '%s groups have been permanently deleted.', $deleted, 'sportszone' ), number_format_i18n( $deleted ) );
		}
	}

	// Prepare the group items for display.
	$sz_groups_list_table->prepare_items();

	/**
	 * Fires before the display of messages for the edit form.
	 *
	 * Useful for plugins to modify the messages before display.
	 *
	 * @since 1.7.0
	 *
	 * @param array $messages Array of messages to be displayed.
	 */
	do_action( 'sz_groups_admin_index', $messages ); ?>

	<div class="wrap">
		<?php if ( version_compare( $GLOBALS['wp_version'], '4.8', '>=' ) ) : ?>

			<h1 class="wp-heading-inline"><?php _e( 'Groups', 'sportszone' ); ?></h1>

			<?php if ( is_user_logged_in() && sz_user_can_create_groups() ) : ?>
				<a class="page-title-action" href="<?php echo trailingslashit( sz_get_groups_directory_permalink() . 'create' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
			<?php endif; ?>

			<?php if ( !empty( $_REQUEST['s'] ) ) : ?>
				<span class="subtitle"><?php printf( __( 'Search results for &#8220;%s&#8221;', 'sportszone' ), wp_html_excerpt( esc_html( stripslashes( $_REQUEST['s'] ) ), 50 ) ); ?></span>
			<?php endif; ?>

			<hr class="wp-header-end">

		<?php else : ?>

		<h1>
			<?php _e( 'Groups', 'sportszone' ); ?>

			<?php if ( is_user_logged_in() && sz_user_can_create_groups() ) : ?>
				<a class="add-new-h2" href="<?php echo trailingslashit( sz_get_groups_directory_permalink() . 'create' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
			<?php endif; ?>

			<?php if ( !empty( $_REQUEST['s'] ) ) : ?>
				<span class="subtitle"><?php printf( __( 'Search results for &#8220;%s&#8221;', 'sportszone' ), wp_html_excerpt( esc_html( stripslashes( $_REQUEST['s'] ) ), 50 ) ); ?></span>
			<?php endif; ?>
		</h1>

		<?php endif; ?>

		<?php // If the user has just made a change to an group, display the status messages. ?>
		<?php if ( !empty( $messages ) ) : ?>
			<div id="moderated" class="<?php echo ( ! empty( $_REQUEST['error'] ) ) ? 'error' : 'updated'; ?>"><p><?php echo implode( "<br/>\n", $messages ); ?></p></div>
		<?php endif; ?>

		<?php // Display each group on its own row. ?>
		<?php $sz_groups_list_table->views(); ?>

		<form id="sz-groups-form" action="" method="get">
			<?php $sz_groups_list_table->search_box( __( 'Search all Groups', 'sportszone' ), 'sz-groups' ); ?>
			<input type="hidden" name="page" value="<?php echo esc_attr( $plugin_page ); ?>" />
			<?php $sz_groups_list_table->display(); ?>
		</form>

	</div>

<?php
}

/**
 * Markup for the single group's Settings metabox.
 *
 * @since 1.7.0
 *
 * @param object $item Information about the current group.
 */
function sz_groups_admin_edit_metabox_settings( $item ) {

	$invite_status = sz_group_get_invite_status( $item->id ); ?>

	<?php if ( sz_is_active( 'forums' ) ) : ?>
		<div class="sz-groups-settings-section" id="sz-groups-settings-section-forum">
			<label for="group-show-forum"><input type="checkbox" name="group-show-forum" id="group-show-forum" <?php checked( $item->enable_forum ) ?> /> <?php _e( 'Enable discussion forum', 'sportszone' ) ?></label>
		</div>
	<?php endif; ?>

	<div class="sz-groups-settings-section" id="sz-groups-settings-section-status">
		<fieldset>
			<legend><?php _e( 'Privacy', 'sportszone' ); ?></legend>

			<label for="sz-group-status-public"><input type="radio" name="group-status" id="sz-group-status-public" value="public" <?php checked( $item->status, 'public' ) ?> /><?php _e( 'Public', 'sportszone' ) ?></label>
			<label for="sz-group-status-private"><input type="radio" name="group-status" id="sz-group-status-private" value="private" <?php checked( $item->status, 'private' ) ?> /><?php _e( 'Private', 'sportszone' ) ?></label>
			<label for="sz-group-status-hidden"><input type="radio" name="group-status" id="sz-group-status-hidden" value="hidden" <?php checked( $item->status, 'hidden' ) ?> /><?php _e( 'Hidden', 'sportszone' ) ?></label>
		</fieldset>
	</div>

	<div class="sz-groups-settings-section" id="sz-groups-settings-section-invite-status">
		<fieldset>
			<legend><?php _e( 'Who can invite others to this group?', 'sportszone' ); ?></legend>

			<label for="sz-group-invite-status-members"><input type="radio" name="group-invite-status" id="sz-group-invite-status-members" value="members" <?php checked( $invite_status, 'members' ) ?> /><?php _e( 'All group members', 'sportszone' ) ?></label>
			<label for="sz-group-invite-status-mods"><input type="radio" name="group-invite-status" id="sz-group-invite-status-mods" value="mods" <?php checked( $invite_status, 'mods' ) ?> /><?php _e( 'Group admins and mods only', 'sportszone' ) ?></label>
			<label for="sz-group-invite-status-admins"><input type="radio" name="group-invite-status" id="sz-group-invite-status-admins" value="admins" <?php checked( $invite_status, 'admins' ) ?> /><?php _e( 'Group admins only', 'sportszone' ) ?></label>
		</fieldset>
	</div>

<?php
}

/**
 * Output the markup for a single group's Add New Members metabox.
 *
 * @since 1.7.0
 *
 * @param SZ_Groups_Group $item The SZ_Groups_Group object for the current group.
 */
function sz_groups_admin_edit_metabox_add_new_members( $item ) {
	?>

	<label for="sz-groups-new-members" class="screen-reader-text"><?php
		/* translators: accessibility text */
		_e( 'Add new members', 'sportszone' );
	?></label>
	<input name="sz-groups-new-members" type="text" id="sz-groups-new-members" class="sz-suggest-user" placeholder="<?php esc_attr_e( 'Enter a comma-separated list of user logins.', 'sportszone' ) ?>" />
	<ul id="sz-groups-new-members-list"></ul>
	<?php
}

/**
 * Renders the Members metabox on single group pages.
 *
 * @since 1.7.0
 *
 * @param SZ_Groups_Group $item The SZ_Groups_Group object for the current group.
 */
function sz_groups_admin_edit_metabox_members( $item ) {

	// Pull up a list of group members, so we can separate out the types
	// We'll also keep track of group members here to place them into a
	// JavaScript variable, which will help with group member autocomplete.
	$members = array(
		'admin'  => array(),
		'mod'    => array(),
		'member' => array(),
		'banned' => array(),
	);

	$pagination = array(
		'admin'  => array(),
		'mod'    => array(),
		'member' => array(),
		'banned' => array(),
	);

	foreach ( $members as $type => &$member_type_users ) {
		$page_qs_key       = $type . '_page';
		$current_type_page = isset( $_GET[ $page_qs_key ] ) ? absint( $_GET[ $page_qs_key ] ) : 1;
		$member_type_query = new SZ_Group_Member_Query( array(
			'group_id'   => $item->id,
			'group_role' => array( $type ),
			'type'       => 'alphabetical',
			/**
			 * Filters the admin members type per page value.
			 *
			 * @since 2.8.0
			 *
			 * @param int    $value Member types per page. Default 10.
			 * @param string $type  Member type.
			 */
			'per_page'   => apply_filters( 'sz_groups_admin_members_type_per_page', 10, $type ),
			'page'       => $current_type_page,
		) );

		$member_type_users   = $member_type_query->results;
		$pagination[ $type ] = sz_groups_admin_create_pagination_links( $member_type_query, $type );
	}

	// Echo out the JavaScript variable.
	echo '<script type="text/javascript">var group_id = "' . esc_js( $item->id ) . '";</script>';

	// Loop through each member type.
	foreach ( $members as $member_type => $type_users ) : ?>

		<div class="sz-groups-member-type" id="sz-groups-member-type-<?php echo esc_attr( $member_type ) ?>">

			<h3><?php switch ( $member_type ) :
					case 'admin'  : esc_html_e( 'Administrators', 'sportszone' ); break;
					case 'mod'    : esc_html_e( 'Moderators',     'sportszone' ); break;
					case 'member' : esc_html_e( 'Members',        'sportszone' ); break;
					case 'banned' : esc_html_e( 'Banned Members', 'sportszone' ); break;
			endswitch; ?></h3>

			<div class="sz-group-admin-pagination table-top">
				<?php echo $pagination[ $member_type ] ?>
			</div>

		<?php if ( !empty( $type_users ) ) : ?>

			<table class="widefat sz-group-members">
				<thead>
					<tr>
						<th scope="col" class="uid-column"><?php _ex( 'ID', 'Group member user_id in group admin', 'sportszone' ); ?></th>
						<th scope="col" class="uname-column"><?php _ex( 'Name', 'Group member name in group admin', 'sportszone' ); ?></th>
						<th scope="col" class="urole-column"><?php _ex( 'Group Role', 'Group member role in group admin', 'sportszone' ); ?></th>
					</tr>
				</thead>

				<tbody>

				<?php foreach ( $type_users as $type_user ) : ?>
					<tr>
						<th scope="row" class="uid-column"><?php echo esc_html( $type_user->ID ); ?></th>

						<td class="uname-column">
							<a style="float: left;" href="<?php echo sz_core_get_user_domain( $type_user->ID ); ?>"><?php echo sz_core_fetch_avatar( array(
								'item_id' => $type_user->ID,
								'width'   => '32',
								'height'  => '32'
							) ); ?></a>

							<span style="margin: 8px; float: left;"><?php echo sz_core_get_userlink( $type_user->ID ); ?></span>
						</td>

						<td class="urole-column">
							<label for="sz-groups-role-<?php echo esc_attr( $type_user->ID ); ?>" class="screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Select group role for member', 'sportszone' );
							?></label>
							<select class="sz-groups-role" id="sz-groups-role-<?php echo esc_attr( $type_user->ID ); ?>" name="sz-groups-role[<?php echo esc_attr( $type_user->ID ); ?>]">
								<optgroup label="<?php esc_attr_e( 'Roles', 'sportszone' ); ?>">
									<option class="admin"  value="admin"  <?php selected( 'admin',  $member_type ); ?>><?php esc_html_e( 'Administrator', 'sportszone' ); ?></option>
									<option class="mod"    value="mod"    <?php selected( 'mod',    $member_type ); ?>><?php esc_html_e( 'Moderator',     'sportszone' ); ?></option>
									<option class="member" value="member" <?php selected( 'member', $member_type ); ?>><?php esc_html_e( 'Member',        'sportszone' ); ?></option>
									<?php if ( 'banned' === $member_type ) : ?>
									<option class="banned" value="banned" <?php selected( 'banned', $member_type ); ?>><?php esc_html_e( 'Banned',        'sportszone' ); ?></option>
									<?php endif; ?>
								</optgroup>
								<optgroup label="<?php esc_attr_e( 'Actions', 'sportszone' ); ?>">
									<option class="remove" value="remove"><?php esc_html_e( 'Remove', 'sportszone' ); ?></option>
									<?php if ( 'banned' !== $member_type ) : ?>
										<option class="banned" value="banned"><?php esc_html_e( 'Ban', 'sportszone' ); ?></option>
									<?php endif; ?>
								</optgroup>
							</select>

							<?php
							/**
							 * Store the current role for this user,
							 * so we can easily detect changes.
							 *
							 * @todo remove this, and do database detection on save
							 */
							?>
							<input type="hidden" name="sz-groups-existing-role[<?php echo esc_attr( $type_user->ID ); ?>]" value="<?php echo esc_attr( $member_type ); ?>" />
						</td>
					</tr>

					<?php if ( has_filter( 'sz_groups_admin_manage_member_row' ) ) : ?>
						<tr>
							<td colspan="3">
								<?php

								/**
								 * Fires after the listing of a single row for members in a group on the group edit screen.
								 *
								 * @since 1.8.0
								 *
								 * @param int             $ID   ID of the user being rendered.
								 * @param SZ_Groups_Group $item Object for the current group.
								 */
								do_action( 'sz_groups_admin_manage_member_row', $type_user->ID, $item ); ?>
							</td>
						</tr>
					<?php endif; ?>

				<?php endforeach; ?>

				</tbody>
			</table>

		<?php else : ?>

			<p class="sz-groups-no-members description"><?php esc_html_e( 'No members of this type', 'sportszone' ); ?></p>

		<?php endif; ?>

		</div><!-- .sz-groups-member-type -->

	<?php endforeach;
}

/**
 * Renders the Status metabox for the Groups admin edit screen.
 *
 * @since 1.7.0
 *
 * @param object $item Information about the currently displayed group.
 */
function sz_groups_admin_edit_metabox_status( $item ) {
	$base_url = add_query_arg( array(
		'page' => 'sz-groups',
		'gid'  => $item->id
	), sz_get_admin_url( 'admin.php' ) ); ?>

	<div id="submitcomment" class="submitbox">
		<div id="major-publishing-actions">
			<div id="delete-action">
				<a class="submitdelete deletion" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', $base_url ), 'sz-groups-delete' ) ); ?>"><?php _e( 'Delete Group', 'sportszone' ) ?></a>
			</div>

			<div id="publishing-action">
				<?php submit_button( __( 'Save Changes', 'sportszone' ), 'primary', 'save', false ); ?>
			</div>
			<div class="clear"></div>
		</div><!-- #major-publishing-actions -->
	</div><!-- #submitcomment -->

<?php
}

/**
 * Render the Group Type metabox.
 *
 * @since 2.6.0
 *
 * @param SZ_Groups_Group|null $group The SZ_Groups_Group object corresponding to the group being edited.
 */
function sz_groups_admin_edit_metabox_group_type( SZ_Groups_Group $group = null ) {

	// Bail if no group ID.
	if ( empty( $group->id ) ) {
		return;
	}

	$types         = sz_groups_get_group_types( array(), 'objects' );
	$current_types = (array) sz_groups_get_group_type( $group->id, false );
	$backend_only  = sz_groups_get_group_types( array( 'show_in_create_screen' => false ) );
	?>

	<label for="sz-groups-group-type" class="screen-reader-text"><?php
		/* translators: accessibility text */
		esc_html_e( 'Select group type', 'sportszone' );
	?></label>

	<ul class="categorychecklist form-no-clear">
		<?php foreach ( $types as $type ) : ?>
			<li>
				<label class="selectit"><input value="<?php echo esc_attr( $type->name ) ?>" name="sz-groups-group-type[]" type="radio" <?php checked( true, in_array( $type->name, $current_types ) ); ?>>
					<?php
						echo esc_html( $type->labels['singular_name'] );
						if ( in_array( $type->name, $backend_only ) ) {
							printf( ' <span class="description">%s</span>', esc_html__( '(Not available on the front end)', 'sportszone' ) );
						}
					?>

				</label>
			</li>

		<?php endforeach; ?>

	</ul>

	<?php

	wp_nonce_field( 'sz-group-type-change-' . $group->id, 'sz-group-type-nonce' );
}

/**
 * Process changes from the Group Type metabox.
 *
 * @since 2.6.0
 *
 * @param int $group_id Group ID.
 */
function sz_groups_process_group_type_update( $group_id ) {
	if ( ! isset( $_POST['sz-group-type-nonce'] ) ) {
		return;
	}

	check_admin_referer( 'sz-group-type-change-' . $group_id, 'sz-group-type-nonce' );

	// Permission check.
	if ( ! sz_current_user_can( 'sz_moderate' ) ) {
		return;
	}

	$group_types = ! empty( $_POST['sz-groups-group-type'] ) ? wp_unslash( $_POST['sz-groups-group-type'] ) : array();

	/*
	 * If an invalid group type is passed, someone's doing something
	 * fishy with the POST request, so we can fail silently.
	 */
	if ( sz_groups_set_group_type( $group_id, $group_types ) ) {
		// @todo Success messages can't be posted because other stuff happens on the page load.
	}
}
add_action( 'sz_group_admin_edit_after', 'sz_groups_process_group_type_update' );

/**
 * Create pagination links out of a SZ_Group_Member_Query.
 *
 * This function is intended to create pagination links for use under the
 * Manage Members section of the Groups Admin Dashboard pages. It is a stopgap
 * measure until a more general pagination solution is in place for SportsZone.
 * Plugin authors should not use this function, as it is likely to be
 * deprecated soon.
 *
 * @since 1.8.0
 *
 * @param SZ_Group_Member_Query $query       A SZ_Group_Member_Query object.
 * @param string                $member_type member|mod|admin|banned.
 * @return string Pagination links HTML.
 */
function sz_groups_admin_create_pagination_links( SZ_Group_Member_Query $query, $member_type ) {
	$pagination = '';

	if ( ! in_array( $member_type, array( 'admin', 'mod', 'member', 'banned' ) ) ) {
		return $pagination;
	}

	// The key used to paginate this member type in the $_GET global.
	$qs_key   = $member_type . '_page';
	$url_base = remove_query_arg( array( $qs_key, 'updated', 'success_modified' ), $_SERVER['REQUEST_URI'] );

	$page = isset( $_GET[ $qs_key ] ) ? absint( $_GET[ $qs_key ] ) : 1;

	/**
	 * Filters the number of members per member type that is displayed in group editing admin area.
	 *
	 * @since 2.8.0
	 *
	 * @param string $member_type Member type, which is a group role (admin, mod etc).
	 */
	$per_page = apply_filters( 'sz_groups_admin_members_type_per_page', 10, $member_type );

	// Don't show anything if there's no pagination.
	if ( 1 === $page && $query->total_users <= $per_page ) {
		return $pagination;
	}

	$current_page_start = ( ( $page - 1 ) * $per_page ) + 1;
	$current_page_end   = $page * $per_page > intval( $query->total_users ) ? $query->total_users : $page * $per_page;

	$pag_links = paginate_links( array(
		'base'      => add_query_arg( $qs_key, '%#%', $url_base ),
		'format'    => '',
		'prev_text' => __( '&laquo;', 'sportszone' ),
		'next_text' => __( '&raquo;', 'sportszone' ),
		'total'     => ceil( $query->total_users / $per_page ),
		'current'   => $page,
	) );

	if ( 1 == $query->total_users ) {
		$viewing_text = __( 'Viewing 1 member', 'sportszone' );
	} else {
		$viewing_text = sprintf(
			_nx( 'Viewing %1$s - %2$s of %3$s member', 'Viewing %1$s - %2$s of %3$s members', $query->total_users, 'Group members pagination in group admin', 'sportszone' ),
			sz_core_number_format( $current_page_start ),
			sz_core_number_format( $current_page_end ),
			sz_core_number_format( $query->total_users )
		);
	}

	$pagination .= '<span class="sz-group-admin-pagination-viewing">' . $viewing_text . '</span>';
	$pagination .= '<span class="sz-group-admin-pagination-links">' . $pag_links . '</span>';

	return $pagination;
}

/**
 * Get a set of usernames corresponding to a set of user IDs.
 *
 * @since 1.7.0
 *
 * @param array $user_ids Array of user IDs.
 * @return array Array of user_logins corresponding to $user_ids.
 */
function sz_groups_admin_get_usernames_from_ids( $user_ids = array() ) {

	$usernames = array();
	$users     = new WP_User_Query( array( 'blog_id' => 0, 'include' => $user_ids ) );

	foreach ( (array) $users->results as $user ) {
		$usernames[] = $user->user_login;
	}

	return $usernames;
}

/**
 * AJAX handler for group member autocomplete requests.
 *
 * @since 1.7.0
 */
function sz_groups_admin_autocomplete_handler() {

	// Bail if user user shouldn't be here, or is a large network.
	if ( ! sz_current_user_can( 'sz_moderate' ) || ( is_multisite() && wp_is_large_network( 'users' ) ) ) {
		wp_die( -1 );
	}

	$term     = isset( $_GET['term'] )     ? sanitize_text_field( $_GET['term'] ) : '';
	$group_id = isset( $_GET['group_id'] ) ? absint( $_GET['group_id'] )          : 0;

	if ( ! $term || ! $group_id ) {
		wp_die( -1 );
	}

	$suggestions = sz_core_get_suggestions( array(
		'group_id' => -$group_id,  // A negative value will exclude this group's members from the suggestions.
		'limit'    => 10,
		'term'     => $term,
		'type'     => 'members',
	) );

	$matches = array();

	if ( $suggestions && ! is_wp_error( $suggestions ) ) {
		foreach ( $suggestions as $user ) {

			$matches[] = array(
				// Translators: 1: user_login, 2: user_email.
				'label' => sprintf( __( '%1$s (%2$s)', 'sportszone' ), $user->name, $user->ID ),
				'value' => $user->ID,
			);
		}
	}

	wp_die( json_encode( $matches ) );
}
add_action( 'wp_ajax_sz_group_admin_member_autocomplete', 'sz_groups_admin_autocomplete_handler' );

/**
 * Process input from the Group Type bulk change select.
 *
 * @since 2.7.0
 *
 * @param string $doaction Current $_GET action being performed in admin screen.
 */
function sz_groups_admin_process_group_type_bulk_changes( $doaction ) {
	// Bail if no groups are specified or if this isn't a relevant action.
	if ( empty( $_REQUEST['gid'] )
		|| ( empty( $_REQUEST['sz_change_type'] ) && empty( $_REQUEST['sz_change_type2'] ) )
		|| empty( $_REQUEST['sz_change_group_type'] )
	) {
		return;
	}

	// Bail if nonce check fails.
	check_admin_referer( 'sz-bulk-groups-change-type-' . sz_loggedin_user_id(), 'sz-bulk-groups-change-type-nonce' );

	if ( ! sz_current_user_can( 'sz_moderate' )  ) {
		return;
	}

	$new_type = '';
	if ( ! empty( $_REQUEST['sz_change_type2'] ) ) {
		$new_type = sanitize_text_field( $_REQUEST['sz_change_type2'] );
	} elseif ( ! empty( $_REQUEST['sz_change_type'] ) ) {
		$new_type = sanitize_text_field( $_REQUEST['sz_change_type'] );
	}

	// Check that the selected type actually exists.
	if ( 'remove_group_type' !== $new_type && null === sz_groups_get_group_type_object( $new_type ) ) {
		$error = true;
	} else {
		// Run through group ids.
		$error = false;
		foreach ( (array) $_REQUEST['gid'] as $group_id ) {
			$group_id = (int) $group_id;

			// Get the old group type to check against.
			$group_type = sz_groups_get_group_type( $group_id );

			if ( 'remove_group_type' === $new_type ) {
				// Remove the current group type, if there's one to remove.
				if ( $group_type ) {
					$removed = sz_groups_remove_group_type( $group_id, $group_type );
					if ( false === $removed || is_wp_error( $removed ) ) {
						$error = true;
					}
				}
			} else {
				// Set the new group type.
				if ( $new_type !== $group_type ) {
					$set = sz_groups_set_group_type( $group_id, $new_type );
					if ( false === $set || is_wp_error( $set ) ) {
						$error = true;
					}
				}
			}
		}
	}

	// If there were any errors, show the error message.
	if ( $error ) {
		$redirect = add_query_arg( array( 'updated' => 'group-type-change-error' ), wp_get_referer() );
	} else {
		$redirect = add_query_arg( array( 'updated' => 'group-type-change-success' ), wp_get_referer() );
	}

	wp_redirect( $redirect );
	exit();
}
add_action( 'sz_groups_admin_load', 'sz_groups_admin_process_group_type_bulk_changes' );

/**
 * Display an admin notice upon group type bulk update.
 *
 * @since 2.7.0
 */
function sz_groups_admin_groups_type_change_notice() {
	$updated = isset( $_REQUEST['updated'] ) ? $_REQUEST['updated'] : false;

	// Display feedback.
	if ( $updated && in_array( $updated, array( 'group-type-change-error', 'group-type-change-success' ), true ) ) {

		if ( 'group-type-change-error' === $updated ) {
			$notice = __( 'There was an error while changing group type. Please try again.', 'sportszone' );
			$type   = 'error';
		} else {
			$notice = __( 'Group type was changed successfully.', 'sportszone' );
			$type   = 'updated';
		}

		sz_core_add_admin_notice( $notice, $type );
	}
}
add_action( sz_core_admin_hook(), 'sz_groups_admin_groups_type_change_notice' );
