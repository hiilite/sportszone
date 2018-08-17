<?php
/**
 * SportsZone Events component admin screen.
 *
 * Props to WordPress core for the Comments admin screen, and its contextual
 * help text, on which this implementation is heavily based.
 *
 * @package SportsZone
 * @subpackage Events
 * @since 1.7.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Include WP's list table class.
if ( !class_exists( 'WP_List_Table' ) ) require( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

// The per_page screen option. Has to be hooked in extremely early.
if ( is_admin() && ! empty( $_REQUEST['page'] ) && 'sz-events' == $_REQUEST['page'] )
	add_filter( 'set-screen-option', 'sz_events_admin_screen_options', 10, 3 );

/**
 * Register the Events component admin screen.
 *
 * @since 1.7.0
 */
function sz_events_add_admin_menu() {

	// Add our screen.
	$hook = add_menu_page(
		_x( 'Events', 'Admin Events page title', 'sportszone' ),
		_x( 'Events', 'Admin Events menu', 'sportszone' ),
		'sz_moderate',
		'sz-events',
		'sz_events_admin',
		'div'
	);

	add_menu_page( 
		__( 'Event Types Settings', 'sportszone' ), 
		__( 'Event Types', 'sportszone' ), 
		'sz_moderarte', 
		'sz-events-types'
		
	);
	
	// Hook into early actions to load custom CSS and our init handler.
	add_action( "load-$hook", 'sz_events_admin_load' );
	
	
}
add_action( sz_core_admin_hook(), 'sz_events_add_admin_menu' );

/**
 * Add events component to custom menus array.
 *
 * This ensures that the Events menu item appears in the proper order on the
 * main Dashboard menu.
 *
 * @since 1.7.0
 *
 * @param array $custom_menus Array of BP top-level menu items.
 * @return array Menu item array, with Events added.
 */
function sz_events_admin_menu_order( $custom_menus = array() ) {
	array_push( $custom_menus, 'sz-events' );
	return $custom_menus;
}
add_filter( 'sz_admin_menu_order', 'sz_events_admin_menu_order' );

/**
 * Set up the Events admin page.
 *
 * Loaded before the page is rendered, this function does all initial setup,
 * including: processing form requests, registering contextual help, and
 * setting up screen options.
 *
 * @since 1.7.0
 *
 * @global SZ_Events_List_Table $sz_events_list_table Events screen list table.
 */
function sz_events_admin_load() {
	global $sz_events_list_table;

	// Build redirection URL.
	$redirect_to = remove_query_arg( array( 'action', 'action2', 'gid', 'deleted', 'error', 'updated', 'success_new', 'error_new', 'success_modified', 'error_modified' ), $_SERVER['REQUEST_URI'] );

	$doaction   = sz_admin_list_table_current_bulk_action();
	$min        = sz_core_get_minified_asset_suffix();

	/**
	 * Fires at top of events admin page.
	 *
	 * @since 1.7.0
	 *
	 * @param string $doaction Current $_GET action being performed in admin screen.
	 */
	do_action( 'sz_events_admin_load', $doaction );

	// Edit screen.
	if ( 'do_delete' == $doaction && ! empty( $_GET['gid'] ) ) {

		check_admin_referer( 'sz-events-delete' );

		$event_ids = wp_parse_id_list( $_GET['gid'] );

		$count = 0;
		foreach ( $event_ids as $event_id ) {
			if ( events_delete_event( $event_id ) ) {
				$count++;
			}
		}

		$redirect_to = add_query_arg( 'deleted', $count, $redirect_to );

		sz_core_redirect( $redirect_to );

	} elseif ( 'edit' == $doaction && ! empty( $_GET['gid'] ) ) {
		// Columns screen option.
		add_screen_option( 'layout_columns', array( 'default' => 2, 'max' => 2, ) );

		get_current_screen()->add_help_tab( array(
			'id'      => 'sz-event-edit-overview',
			'title'   => __( 'Overview', 'sportszone' ),
			'content' =>
				'<p>' . __( 'This page is a convenient way to edit the details associated with one of your events.', 'sportszone' ) . '</p>' .
				'<p>' . __( 'The Name and Description box is fixed in place, but you can reposition all the other boxes using drag and drop, and can minimize or expand them by clicking the title bar of each box. Use the Screen Options tab to hide or unhide, or to choose a 1- or 2-column layout for this screen.', 'sportszone' ) . '</p>'
		) );

		// Help panel - sidebar links.
		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'sportszone' ) . '</strong></p>' .
			'<p><a href="https://sportszone.org/support">' . __( 'Support Forums', 'sportszone' ) . '</a></p>'
		);

		// Register metaboxes for the edit screen.
		add_meta_box( 'submitdiv', _x( 'Save', 'event admin edit screen', 'sportszone' ), 'sz_events_admin_edit_metabox_status', get_current_screen()->id, 'side', 'high' );
		add_meta_box( 'sz_event_settings', _x( 'Settings', 'event admin edit screen', 'sportszone' ), 'sz_events_admin_edit_metabox_settings', get_current_screen()->id, 'side', 'core' );
		add_meta_box( 'sz_event_add_members', _x( 'Add New Members', 'event admin edit screen', 'sportszone' ), 'sz_events_admin_edit_metabox_add_new_members', get_current_screen()->id, 'normal', 'core' );
		add_meta_box( 'sz_event_members', _x( 'Manage Members', 'event admin edit screen', 'sportszone' ), 'sz_events_admin_edit_metabox_members', get_current_screen()->id, 'normal', 'core' );

		// Event Type metabox. Only added if event types have been registered.
		$event_types = sz_events_get_event_types();
		if ( ! empty( $event_types ) ) {
			add_meta_box(
				'sz_events_admin_event_type',
				_x( 'Event Type', 'events admin edit screen', 'sportszone' ),
				'sz_events_admin_edit_metabox_event_type',
				get_current_screen()->id,
				'side',
				'core'
			);
		}

		/**
		 * Fires after the registration of all of the default event meta boxes.
		 *
		 * @since 1.7.0
		 */
		do_action( 'sz_events_admin_meta_boxes' );

		// Enqueue JavaScript files.
		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'dashboard' );

	// Index screen.
	} else {
		// Create the Events screen list table.
		$sz_events_list_table = new SZ_Events_List_Table();

		// The per_page screen option.
		add_screen_option( 'per_page', array( 'label' => _x( 'Events', 'Events per page (screen options)', 'sportszone' )) );

		// Help panel - overview text.
		get_current_screen()->add_help_tab( array(
			'id'      => 'sz-events-overview',
			'title'   => __( 'Overview', 'sportszone' ),
			'content' =>
				'<p>' . __( 'You can manage events much like you can manage comments and other content. This screen is customizable in the same ways as other management screens, and you can act on events by using the on-hover action links or the Bulk Actions.', 'sportszone' ) . '</p>',
		) );

		get_current_screen()->add_help_tab( array(
			'id'      => 'sz-events-overview-actions',
			'title'   => __( 'Event Actions', 'sportszone' ),
			'content' =>
				'<p>' . __( 'Clicking "Visit" will take you to the event&#8217;s public page. Use this link to see what the event looks like on the front end of your site.', 'sportszone' ) . '</p>' .
				'<p>' . __( 'Clicking "Edit" will take you to a Dashboard panel where you can manage various details about the event, such as its name and description, its members, and other settings.', 'sportszone' ) . '</p>' .
				'<p>' . __( 'If you click "Delete" under a specific event, or select a number of events and then choose Delete from the Bulk Actions menu, you will be led to a page where you&#8217;ll be asked to confirm the permanent deletion of the event(s).', 'sportszone' ) . '</p>',
		) );

		// Help panel - sidebar links.
		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'sportszone' ) . '</strong></p>' .
			'<p>' . __( '<a href="https://sportszone.org/support/">Support Forums</a>', 'sportszone' ) . '</p>'
		);

		// Add accessible hidden heading and text for Events screen pagination.
		get_current_screen()->set_screen_reader_content( array(
			/* translators: accessibility text */
			'heading_pagination' => __( 'Events list navigation', 'sportszone' ),
		) );
	}

	$sz = sportszone();

	// Enqueue CSS and JavaScript.
	wp_enqueue_script( 'sz_events_admin_js', $sz->plugin_url . "sz-events/admin/js/admin{$min}.js", array( 'jquery', 'wp-ajax-response', 'jquery-ui-autocomplete' ), sz_get_version(), true );
	wp_localize_script( 'sz_events_admin_js', 'SZ_Event_Admin', array(
		'add_member_placeholder' => __( 'Start typing a username to add a new member.', 'sportszone' ),
		'warn_on_leave'          => __( 'If you leave this page, you will lose any unsaved changes you have made to the event.', 'sportszone' ),
	) );
	wp_enqueue_style( 'sz_events_admin_css', $sz->plugin_url . "sz-events/admin/css/admin{$min}.css", array(), sz_get_version() );

	wp_style_add_data( 'sz_events_admin_css', 'rtl', true );
	if ( $min ) {
		wp_style_add_data( 'sz_events_admin_css', 'suffix', $min );
	}


	if ( $doaction && 'save' == $doaction ) {
		// Get event ID.
		$event_id = isset( $_REQUEST['gid'] ) ? (int) $_REQUEST['gid'] : '';

		$redirect_to = add_query_arg( array(
			'gid'    => (int) $event_id,
			'action' => 'edit'
		), $redirect_to );

		// Check this is a valid form submission.
		check_admin_referer( 'edit-event_' . $event_id );

		// Get the event from the database.
		$event = events_get_event( $event_id );

		// If the event doesn't exist, just redirect back to the index.
		if ( empty( $event->slug ) ) {
			wp_redirect( $redirect_to );
			exit;
		}

		// Check the form for the updated properties.
		// Store errors.
		$error = 0;
		$success_new = $error_new = $success_modified = $error_modified = array();

		// Name, description and slug must not be empty.
		if ( empty( $_POST['sz-events-name'] ) ) {
			$error = $error - 1;
		}
		if ( empty( $_POST['sz-events-description'] ) ) {
			$error = $error - 2;
		}
		if ( empty( $_POST['sz-events-slug'] ) ) {
			$error = $error - 4;
		}

		/*
		 * Event name, slug, and description are handled with
		 * events_edit_base_event_details().
		 */
		if ( ! $error && ! events_edit_base_event_details( array(
				'event_id'       => $event_id,
				'name'           => $_POST['sz-events-name'],
				'slug'           => $_POST['sz-events-slug'],
				'description'    => $_POST['sz-events-description'],
				'notify_members' => false,
			) ) ) {
			$error = $event_id;
		}

		// Enable discussion forum.
		$enable_forum   = ( isset( $_POST['event-show-forum'] ) ) ? 1 : 0;

		/**
		 * Filters the allowed status values for the event.
		 *
		 * @since 1.0.2
		 *
		 * @param array $value Array of allowed event statuses.
		 */
		$allowed_status = apply_filters( 'events_allowed_status', array( 'public', 'private', 'hidden' ) );
		$status         = ( in_array( $_POST['event-status'], (array) $allowed_status ) ) ? $_POST['event-status'] : 'public';

		/**
		 * Filters the allowed invite status values for the event.
		 *
		 * @since 1.5.0
		 *
		 * @param array $value Array of allowed invite statuses.
		 */
		$allowed_invite_status = apply_filters( 'events_allowed_invite_status', array( 'members', 'mods', 'admins' ) );
		$invite_status	       = in_array( $_POST['event-invite-status'], (array) $allowed_invite_status ) ? $_POST['event-invite-status'] : 'members';

		if ( !events_edit_event_settings( $event_id, $enable_forum, $status, $invite_status ) ) {
			$error = $event_id;
		}

		// Process new members.
		$user_names = array();

		if ( ! empty( $_POST['sz-events-new-members'] ) ) {
			$user_names = array_merge( $user_names, explode( ',', $_POST['sz-events-new-members'] ) );
		}

		if ( ! empty( $user_names ) ) {

			foreach( array_values( $user_names ) as $user_name ) {
				$un = trim( $user_name );

				// Make sure the user exists before attempting
				// to add to the event.
				$user = get_user_by( 'slug', $un );

				if ( empty( $user ) ) {
					$error_new[] = $un;
				} else {
					if ( ! events_join_event( $event_id, $user->ID ) ) {
						$error_new[]   = $un;
					} else {
						$success_new[] = $un;
					}
				}
			}
		}

		// Process member role changes.
		if ( ! empty( $_POST['sz-events-role'] ) && ! empty( $_POST['sz-events-existing-role'] ) ) {

			// Before processing anything, make sure you're not
			// attempting to remove the all user admins.
			$admin_count = 0;
			foreach ( (array) $_POST['sz-events-role'] as $new_role ) {
				if ( 'admin' == $new_role ) {
					$admin_count++;
					break;
				}
			}

			if ( ! $admin_count ) {

				$redirect_to = add_query_arg( 'no_admins', 1, $redirect_to );
				$error = $event_id;

			} else {

				// Process only those users who have had their roles changed.
				foreach ( (array) $_POST['sz-events-role'] as $user_id => $new_role ) {
					$user_id = (int) $user_id;

					$existing_role = isset( $_POST['sz-events-existing-role'][$user_id] ) ? $_POST['sz-events-existing-role'][$user_id] : '';

					if ( $existing_role != $new_role ) {
						$result = false;

						switch ( $new_role ) {
							case 'mod' :
								// Admin to mod is a demotion. Demote to
								// member, then fall through.
								if ( 'admin' == $existing_role ) {
									events_demote_member( $user_id, $event_id );
								}

							case 'admin' :
								// If the user was banned, we must
								// unban first.
								if ( 'banned' == $existing_role ) {
									events_unban_member( $user_id, $event_id );
								}

								// At this point, each existing_role
								// is a member, so promote.
								$result = events_promote_member( $user_id, $event_id, $new_role );

								break;

							case 'member' :

								if ( 'admin' == $existing_role || 'mod' == $existing_role ) {
									$result = events_demote_member( $user_id, $event_id );
								} elseif ( 'banned' == $existing_role ) {
									$result = events_unban_member( $user_id, $event_id );
								}

								break;

							case 'banned' :

								$result = events_ban_member( $user_id, $event_id );

								break;

							case 'remove' :

								$result = events_remove_member( $user_id, $event_id );

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
		 * @param int $event_id ID of the event being edited.
		 */
		do_action( 'sz_event_admin_edit_after', $event_id );

		// Create the redirect URL.
		if ( $error ) {
			// This means there was an error updating event details.
			$redirect_to = add_query_arg( 'error', (int) $error, $redirect_to );
		} else {
			// Event details were update successfully.
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
		 * Filters the URL to redirect to after successfully editing a event.
		 *
		 * @since 1.7.0
		 *
		 * @param string $redirect_to URL to redirect user to.
		 */
		wp_redirect( apply_filters( 'sz_event_admin_edit_redirect', $redirect_to ) );
		exit;


	// If a referrer and a nonce is supplied, but no action, redirect back.
	} elseif ( ! empty( $_GET['_wp_http_referer'] ) ) {
		wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) );
		exit;
	}
}

/**
 * Handle save/update of screen options for the Events component admin screen.
 *
 * @since 1.7.0
 *
 * @param string $value     Will always be false unless another plugin filters it first.
 * @param string $option    Screen option name.
 * @param string $new_value Screen option form value.
 * @return string|int Option value. False to abandon update.
 */
function sz_events_admin_screen_options( $value, $option, $new_value ) {
	if ( 'toplevel_page_sz_events_per_page' != $option && 'toplevel_page_sz_events_network_per_page' != $option )
		return $value;

	// Per page.
	$new_value = (int) $new_value;
	if ( $new_value < 1 || $new_value > 999 )
		return $value;

	return $new_value;
}

/**
 * Select the appropriate Events admin screen, and output it.
 *
 * @since 1.7.0
 */
function sz_events_admin() {
	// Decide whether to load the index or edit screen.
	$doaction = sz_admin_list_table_current_bulk_action();

	// Display the single event edit screen.
	if ( 'edit' == $doaction && ! empty( $_GET['gid'] ) ) {
		sz_events_admin_edit();

	// Display the event deletion confirmation screen.
	} elseif ( 'delete' == $doaction && ! empty( $_GET['gid'] ) ) {
		sz_events_admin_delete();

	// Otherwise, display the events index screen.
	} else {
		sz_events_admin_index();
	}
}

/**
 * Display the single events edit screen.
 *
 * @since 1.7.0
 */
function sz_events_admin_edit() {

	if ( ! sz_current_user_can( 'sz_moderate' ) )
		die( '-1' );

	$messages = array();

	// If the user has just made a change to a event, build status messages.
	if ( !empty( $_REQUEST['no_admins'] ) || ! empty( $_REQUEST['error'] ) || ! empty( $_REQUEST['updated'] ) || ! empty( $_REQUEST['error_new'] ) || ! empty( $_REQUEST['success_new'] ) || ! empty( $_REQUEST['error_modified'] ) || ! empty( $_REQUEST['success_modified'] ) ) {
		$no_admins        = ! empty( $_REQUEST['no_admins']        ) ? 1                                             : 0;
		$errors           = ! empty( $_REQUEST['error']            ) ? $_REQUEST['error']                            : '';
		$updated          = ! empty( $_REQUEST['updated']          ) ? $_REQUEST['updated']                          : '';
		$error_new        = ! empty( $_REQUEST['error_new']        ) ? explode( ',', $_REQUEST['error_new'] )        : array();
		$success_new      = ! empty( $_REQUEST['success_new']      ) ? explode( ',', $_REQUEST['success_new'] )      : array();
		$error_modified   = ! empty( $_REQUEST['error_modified']   ) ? explode( ',', $_REQUEST['error_modified'] )   : array();
		$success_modified = ! empty( $_REQUEST['success_modified'] ) ? explode( ',', $_REQUEST['success_modified'] ) : array();

		if ( ! empty( $no_admins ) ) {
			$messages[] = __( 'You cannot remove all administrators from a event.', 'sportszone' );
		}

		if ( ! empty( $errors ) ) {
			if ( $errors < 0 ) {
				$messages[] = __( 'Event name, slug, and description are all required fields.', 'sportszone' );
			} else {
				$messages[] = __( 'An error occurred when trying to update your event details.', 'sportszone' );
			}

		} elseif ( ! empty( $updated ) ) {
			$messages[] = __( 'The event has been updated successfully.', 'sportszone' );
		}

		if ( ! empty( $error_new ) ) {
			$messages[] = sprintf( __( 'The following users could not be added to the event: %s', 'sportszone' ), '<em>' . esc_html( implode( ', ', $error_new ) ) . '</em>' );
		}

		if ( ! empty( $success_new ) ) {
			$messages[] = sprintf( __( 'The following users were successfully added to the event: %s', 'sportszone' ), '<em>' . esc_html( implode( ', ', $success_new ) ) . '</em>' );
		}

		if ( ! empty( $error_modified ) ) {
			$error_modified = sz_events_admin_get_usernames_from_ids( $error_modified );
			$messages[] = sprintf( __( 'An error occurred when trying to modify the following members: %s', 'sportszone' ), '<em>' . esc_html( implode( ', ', $error_modified ) ) . '</em>' );
		}

		if ( ! empty( $success_modified ) ) {
			$success_modified = sz_events_admin_get_usernames_from_ids( $success_modified );
			$messages[] = sprintf( __( 'The following members were successfully modified: %s', 'sportszone' ), '<em>' . esc_html( implode( ', ', $success_modified ) ) . '</em>' );
		}
	}

	$is_error = ! empty( $no_admins ) || ! empty( $errors ) || ! empty( $error_new ) || ! empty( $error_modified );

	// Get the event from the database.
	$event      = events_get_event( (int) $_GET['gid'] );

	$event_name = isset( $event->name ) ? sz_get_event_name( $event ) : '';

	// Construct URL for form.
	$form_url = remove_query_arg( array( 'action', 'deleted', 'no_admins', 'error', 'error_new', 'success_new', 'error_modified', 'success_modified' ), $_SERVER['REQUEST_URI'] );
	$form_url = add_query_arg( 'action', 'save', $form_url );

	/**
	 * Fires before the display of the edit form.
	 *
	 * Useful for plugins to modify the event before display.
	 *
	 * @since 1.7.0
	 *
	 * @param SZ_Events_Event $this Instance of the current event being edited. Passed by reference.
	 */
	do_action_ref_array( 'sz_events_admin_edit', array( &$event ) ); ?>

	<div class="wrap">
		<?php if ( version_compare( $GLOBALS['wp_version'], '4.8', '>=' ) ) : ?>

			<h1 class="wp-heading-inline"><?php _e( 'Edit Event', 'sportszone' ); ?></h1>

			<?php if ( is_user_logged_in() && sz_user_can_create_events() ) : ?>
				<a class="page-title-action" href="<?php echo trailingslashit( sz_get_events_directory_permalink() . 'create' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
			<?php endif; ?>

			<hr class="wp-header-end">

		<?php else : ?>

			<h1><?php _e( 'Edit Event', 'sportszone' ); ?>

				<?php if ( is_user_logged_in() && sz_user_can_create_events() ) : ?>
					<a class="add-new-h2" href="<?php echo trailingslashit( sz_get_events_directory_permalink() . 'create' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
				<?php endif; ?>

			</h1>

		<?php endif; ?>

		<?php // If the user has just made a change to an event, display the status messages. ?>
		<?php if ( !empty( $messages ) ) : ?>
			<div id="moderated" class="<?php echo ( $is_error ) ? 'error' : 'updated'; ?>"><p><?php echo implode( "</p><p>", $messages ); ?></p></div>
		<?php endif; ?>

		<?php if ( $event->id ) : ?>

			<form action="<?php echo esc_url( $form_url ); ?>" id="sz-events-edit-form" method="post">
				<div id="poststuff">

					<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
						<div id="post-body-content">
							<div id="postdiv">
								<div id="sz_events_name" class="postbox">
									<h2><?php _e( 'Name and Description', 'sportszone' ); ?></h2>
									<div class="inside">
										<label for="sz-events-name" class="screen-reader-text"><?php
											/* translators: accessibility text */
											_e( 'Event Name', 'sportszone' );
										?></label>
										<input type="text" name="sz-events-name" id="sz-events-name" value="<?php echo esc_attr( stripslashes( $event_name ) ) ?>" />
										<div id="sz-events-permalink-box">
											<strong><?php esc_html_e( 'Permalink:', 'sportszone' ) ?></strong>
											<span id="sz-events-permalink">
												<?php sz_events_directory_permalink(); ?> <input type="text" id="sz-events-slug" name="sz-events-slug" value="<?php sz_event_slug( $event ); ?>" autocomplete="off"> /
											</span>
											<a href="<?php echo sz_event_permalink( $event ) ?>" class="button button-small" id="sz-events-visit-event"><?php esc_html_e( 'Visit Event', 'sportszone' ) ?></a>
										</div>

										<label for="sz-events-description" class="screen-reader-text"><?php
											/* translators: accessibility text */
											_e( 'Event Description', 'sportszone' );
										?></label>
										<?php wp_editor( stripslashes( $event->description ), 'sz-events-description', array( 'media_buttons' => false, 'teeny' => true, 'textarea_rows' => 5, 'quicktags' => array( 'buttons' => 'strong,em,link,block,del,ins,img,code,spell,close' ) ) ); ?>
									</div>
								</div>
							</div>
						</div><!-- #post-body-content -->

						<div id="postbox-container-1" class="postbox-container">
							<?php do_meta_boxes( get_current_screen()->id, 'side', $event ); ?>
						</div>

						<div id="postbox-container-2" class="postbox-container">
							<?php do_meta_boxes( get_current_screen()->id, 'normal', $event ); ?>
							<?php do_meta_boxes( get_current_screen()->id, 'advanced', $event ); ?>
						</div>
					</div><!-- #post-body -->

				</div><!-- #poststuff -->
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
				<?php wp_nonce_field( 'edit-event_' . $event->id ); ?>
			</form>

		<?php else : ?>

			<p><?php
				printf(
					'%1$s <a href="%2$s">%3$s</a>',
					__( 'No event found with this ID.', 'sportszone' ),
					esc_url( sz_get_admin_url( 'admin.php?page=sz-events' ) ),
					__( 'Go back and try again.', 'sportszone' )
				);
			?></p>

		<?php endif; ?>

	</div><!-- .wrap -->

<?php
}

/**
 * Display the Event delete confirmation screen.
 *
 * We include a separate confirmation because event deletion is truly
 * irreversible.
 *
 * @since 1.7.0
 */
function sz_events_admin_delete() {

	if ( ! sz_current_user_can( 'sz_moderate' ) ) {
		die( '-1' );
	}

	$event_ids = isset( $_REQUEST['gid'] ) ? $_REQUEST['gid'] : 0;
	if ( ! is_array( $event_ids ) ) {
		$event_ids = explode( ',', $event_ids );
	}
	$event_ids = wp_parse_id_list( $event_ids );
	$events    = events_get_events( array(
		'include'     => $event_ids,
		'show_hidden' => true,
		'per_page'    => null, // Return all results.
	) );

	// Create a new list of event ids, based on those that actually exist.
	$gids = array();
	foreach ( $events['events'] as $event ) {
		$gids[] = $event->id;
	}

	$base_url  = remove_query_arg( array( 'action', 'action2', 'paged', 's', '_wpnonce', 'gid' ), $_SERVER['REQUEST_URI'] ); ?>

	<div class="wrap">
		<h1><?php _e( 'Delete Events', 'sportszone' ) ?></h1>
		<p><?php _e( 'You are about to delete the following events:', 'sportszone' ) ?></p>

		<ul class="sz-event-delete-list">
		<?php foreach ( $events['events'] as $event ) : ?>
			<li><?php echo esc_html( sz_get_event_name( $event ) ); ?></li>
		<?php endforeach; ?>
		</ul>

		<p><strong><?php _e( 'This action cannot be undone.', 'sportszone' ) ?></strong></p>

		<a class="button-primary" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'do_delete', 'gid' => implode( ',', $gids ) ), $base_url ), 'sz-events-delete' ) ); ?>"><?php _e( 'Delete Permanently', 'sportszone' ) ?></a>
		<a class="button" href="<?php echo esc_attr( $base_url ); ?>"><?php _e( 'Cancel', 'sportszone' ) ?></a>
	</div>

	<?php
}

/**
 * Display the Events admin index screen.
 *
 * This screen contains a list of all SportsZone events.
 *
 * @since 1.7.0
 *
 * @global SZ_Events_List_Table $sz_events_list_table Event screen list table.
 * @global string $plugin_page Currently viewed plugin page.
 */
function sz_events_admin_index() {
	global $sz_events_list_table, $plugin_page;

	$messages = array();

	// If the user has just made a change to a event, build status messages.
	if ( ! empty( $_REQUEST['deleted'] ) ) {
		$deleted  = ! empty( $_REQUEST['deleted'] ) ? (int) $_REQUEST['deleted'] : 0;

		if ( $deleted > 0 ) {
			$messages[] = sprintf( _n( '%s event has been permanently deleted.', '%s events have been permanently deleted.', $deleted, 'sportszone' ), number_format_i18n( $deleted ) );
		}
	}

	// Prepare the event items for display.
	$sz_events_list_table->prepare_items();

	/**
	 * Fires before the display of messages for the edit form.
	 *
	 * Useful for plugins to modify the messages before display.
	 *
	 * @since 1.7.0
	 *
	 * @param array $messages Array of messages to be displayed.
	 */
	do_action( 'sz_events_admin_index', $messages ); ?>

	<div class="wrap">
		<?php if ( version_compare( $GLOBALS['wp_version'], '4.8', '>=' ) ) : ?>

			<h1 class="wp-heading-inline"><?php _e( 'Events', 'sportszone' ); ?></h1>

			<?php if ( is_user_logged_in() && sz_user_can_create_events() ) : ?>
				<a class="page-title-action" href="<?php echo trailingslashit( sz_get_events_directory_permalink() . 'create' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
			<?php endif; ?>

			<?php if ( !empty( $_REQUEST['s'] ) ) : ?>
				<span class="subtitle"><?php printf( __( 'Search results for &#8220;%s&#8221;', 'sportszone' ), wp_html_excerpt( esc_html( stripslashes( $_REQUEST['s'] ) ), 50 ) ); ?></span>
			<?php endif; ?>

			<hr class="wp-header-end">

		<?php else : ?>

		<h1>
			<?php _e( 'Events', 'sportszone' ); ?>

			<?php if ( is_user_logged_in() && sz_user_can_create_events() ) : ?>
				<a class="add-new-h2" href="<?php echo trailingslashit( sz_get_events_directory_permalink() . 'create' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
			<?php endif; ?>

			<?php if ( !empty( $_REQUEST['s'] ) ) : ?>
				<span class="subtitle"><?php printf( __( 'Search results for &#8220;%s&#8221;', 'sportszone' ), wp_html_excerpt( esc_html( stripslashes( $_REQUEST['s'] ) ), 50 ) ); ?></span>
			<?php endif; ?>
		</h1>

		<?php endif; ?>

		<?php // If the user has just made a change to an event, display the status messages. ?>
		<?php if ( !empty( $messages ) ) : ?>
			<div id="moderated" class="<?php echo ( ! empty( $_REQUEST['error'] ) ) ? 'error' : 'updated'; ?>"><p><?php echo implode( "<br/>\n", $messages ); ?></p></div>
		<?php endif; ?>

		<?php // Display each event on its own row. ?>
		<?php $sz_events_list_table->views(); ?>

		<form id="sz-events-form" action="" method="get">
			<?php $sz_events_list_table->search_box( __( 'Search all Events', 'sportszone' ), 'sz-events' ); ?>
			<input type="hidden" name="page" value="<?php echo esc_attr( $plugin_page ); ?>" />
			<?php $sz_events_list_table->display(); ?>
		</form>

	</div>

<?php
}

/**
 * Markup for the single event's Settings metabox.
 *
 * @since 1.7.0
 *
 * @param object $item Information about the current event.
 */
function sz_events_admin_edit_metabox_settings( $item ) {

	$invite_status = sz_event_get_invite_status( $item->id ); ?>

	<?php if ( sz_is_active( 'forums' ) ) : ?>
		<div class="sz-events-settings-section" id="sz-events-settings-section-forum">
			<label for="event-show-forum"><input type="checkbox" name="event-show-forum" id="event-show-forum" <?php checked( $item->enable_forum ) ?> /> <?php _e( 'Enable discussion forum', 'sportszone' ) ?></label>
		</div>
	<?php endif; ?>

	<div class="sz-events-settings-section" id="sz-events-settings-section-status">
		<fieldset>
			<legend><?php _e( 'Privacy', 'sportszone' ); ?></legend>

			<label for="sz-event-status-public"><input type="radio" name="event-status" id="sz-event-status-public" value="public" <?php checked( $item->status, 'public' ) ?> /><?php _e( 'Public', 'sportszone' ) ?></label>
			<label for="sz-event-status-private"><input type="radio" name="event-status" id="sz-event-status-private" value="private" <?php checked( $item->status, 'private' ) ?> /><?php _e( 'Private', 'sportszone' ) ?></label>
			<label for="sz-event-status-hidden"><input type="radio" name="event-status" id="sz-event-status-hidden" value="hidden" <?php checked( $item->status, 'hidden' ) ?> /><?php _e( 'Hidden', 'sportszone' ) ?></label>
		</fieldset>
	</div>

	<div class="sz-events-settings-section" id="sz-events-settings-section-invite-status">
		<fieldset>
			<legend><?php _e( 'Who can invite others to this event?', 'sportszone' ); ?></legend>

			<label for="sz-event-invite-status-members"><input type="radio" name="event-invite-status" id="sz-event-invite-status-members" value="members" <?php checked( $invite_status, 'members' ) ?> /><?php _e( 'All event members', 'sportszone' ) ?></label>
			<label for="sz-event-invite-status-mods"><input type="radio" name="event-invite-status" id="sz-event-invite-status-mods" value="mods" <?php checked( $invite_status, 'mods' ) ?> /><?php _e( 'Event admins and mods only', 'sportszone' ) ?></label>
			<label for="sz-event-invite-status-admins"><input type="radio" name="event-invite-status" id="sz-event-invite-status-admins" value="admins" <?php checked( $invite_status, 'admins' ) ?> /><?php _e( 'Event admins only', 'sportszone' ) ?></label>
		</fieldset>
	</div>

<?php
}

/**
 * Output the markup for a single event's Add New Members metabox.
 *
 * @since 1.7.0
 *
 * @param SZ_Events_Event $item The SZ_Events_Event object for the current event.
 */
function sz_events_admin_edit_metabox_add_new_members( $item ) {
	?>

	<label for="sz-events-new-members" class="screen-reader-text"><?php
		/* translators: accessibility text */
		_e( 'Add new members', 'sportszone' );
	?></label>
	<input name="sz-events-new-members" type="text" id="sz-events-new-members" class="sz-suggest-user" placeholder="<?php esc_attr_e( 'Enter a comma-separated list of user logins.', 'sportszone' ) ?>" />
	<ul id="sz-events-new-members-list"></ul>
	<?php
}

/**
 * Renders the Members metabox on single event pages.
 *
 * @since 1.7.0
 *
 * @param SZ_Events_Event $item The SZ_Events_Event object for the current event.
 */
function sz_events_admin_edit_metabox_members( $item ) {

	// Pull up a list of event members, so we can separate out the types
	// We'll also keep track of event members here to place them into a
	// JavaScript variable, which will help with event member autocomplete.
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
		$member_type_query = new SZ_Event_Member_Query( array(
			'event_id'   => $item->id,
			'event_role' => array( $type ),
			'type'       => 'alphabetical',
			/**
			 * Filters the admin members type per page value.
			 *
			 * @since 2.8.0
			 *
			 * @param int    $value Member types per page. Default 10.
			 * @param string $type  Member type.
			 */
			'per_page'   => apply_filters( 'sz_events_admin_members_type_per_page', 10, $type ),
			'page'       => $current_type_page,
		) );

		$member_type_users   = $member_type_query->results;
		$pagination[ $type ] = sz_events_admin_create_pagination_links( $member_type_query, $type );
	}

	// Echo out the JavaScript variable.
	echo '<script type="text/javascript">var event_id = "' . esc_js( $item->id ) . '";</script>';

	// Loop through each member type.
	foreach ( $members as $member_type => $type_users ) : ?>

		<div class="sz-events-member-type" id="sz-events-member-type-<?php echo esc_attr( $member_type ) ?>">

			<h3><?php switch ( $member_type ) :
					case 'admin'  : esc_html_e( 'Administrators', 'sportszone' ); break;
					case 'mod'    : esc_html_e( 'Moderators',     'sportszone' ); break;
					case 'member' : esc_html_e( 'Members',        'sportszone' ); break;
					case 'banned' : esc_html_e( 'Banned Members', 'sportszone' ); break;
			endswitch; ?></h3>

			<div class="sz-event-admin-pagination table-top">
				<?php echo $pagination[ $member_type ] ?>
			</div>

		<?php if ( !empty( $type_users ) ) : ?>

			<table class="widefat sz-event-members">
				<thead>
					<tr>
						<th scope="col" class="uid-column"><?php _ex( 'ID', 'Event member user_id in event admin', 'sportszone' ); ?></th>
						<th scope="col" class="uname-column"><?php _ex( 'Name', 'Event member name in event admin', 'sportszone' ); ?></th>
						<th scope="col" class="urole-column"><?php _ex( 'Event Role', 'Event member role in event admin', 'sportszone' ); ?></th>
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
							<label for="sz-events-role-<?php echo esc_attr( $type_user->ID ); ?>" class="screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Select event role for member', 'sportszone' );
							?></label>
							<select class="sz-events-role" id="sz-events-role-<?php echo esc_attr( $type_user->ID ); ?>" name="sz-events-role[<?php echo esc_attr( $type_user->ID ); ?>]">
								<optevent label="<?php esc_attr_e( 'Roles', 'sportszone' ); ?>">
									<option class="admin"  value="admin"  <?php selected( 'admin',  $member_type ); ?>><?php esc_html_e( 'Administrator', 'sportszone' ); ?></option>
									<option class="mod"    value="mod"    <?php selected( 'mod',    $member_type ); ?>><?php esc_html_e( 'Moderator',     'sportszone' ); ?></option>
									<option class="member" value="member" <?php selected( 'member', $member_type ); ?>><?php esc_html_e( 'Member',        'sportszone' ); ?></option>
									<?php if ( 'banned' === $member_type ) : ?>
									<option class="banned" value="banned" <?php selected( 'banned', $member_type ); ?>><?php esc_html_e( 'Banned',        'sportszone' ); ?></option>
									<?php endif; ?>
								</optevent>
								<optevent label="<?php esc_attr_e( 'Actions', 'sportszone' ); ?>">
									<option class="remove" value="remove"><?php esc_html_e( 'Remove', 'sportszone' ); ?></option>
									<?php if ( 'banned' !== $member_type ) : ?>
										<option class="banned" value="banned"><?php esc_html_e( 'Ban', 'sportszone' ); ?></option>
									<?php endif; ?>
								</optevent>
							</select>

							<?php
							/**
							 * Store the current role for this user,
							 * so we can easily detect changes.
							 *
							 * @todo remove this, and do database detection on save
							 */
							?>
							<input type="hidden" name="sz-events-existing-role[<?php echo esc_attr( $type_user->ID ); ?>]" value="<?php echo esc_attr( $member_type ); ?>" />
						</td>
					</tr>

					<?php if ( has_filter( 'sz_events_admin_manage_member_row' ) ) : ?>
						<tr>
							<td colspan="3">
								<?php

								/**
								 * Fires after the listing of a single row for members in a event on the event edit screen.
								 *
								 * @since 1.8.0
								 *
								 * @param int             $ID   ID of the user being rendered.
								 * @param SZ_Events_Event $item Object for the current event.
								 */
								do_action( 'sz_events_admin_manage_member_row', $type_user->ID, $item ); ?>
							</td>
						</tr>
					<?php endif; ?>

				<?php endforeach; ?>

				</tbody>
			</table>

		<?php else : ?>

			<p class="sz-events-no-members description"><?php esc_html_e( 'No members of this type', 'sportszone' ); ?></p>

		<?php endif; ?>

		</div><!-- .sz-events-member-type -->

	<?php endforeach;
}

/**
 * Renders the Status metabox for the Events admin edit screen.
 *
 * @since 1.7.0
 *
 * @param object $item Information about the currently displayed event.
 */
function sz_events_admin_edit_metabox_status( $item ) {
	$base_url = add_query_arg( array(
		'page' => 'sz-events',
		'gid'  => $item->id
	), sz_get_admin_url( 'admin.php' ) ); ?>

	<div id="submitcomment" class="submitbox">
		<div id="major-publishing-actions">
			<div id="delete-action">
				<a class="submitdelete deletion" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', $base_url ), 'sz-events-delete' ) ); ?>"><?php _e( 'Delete Event', 'sportszone' ) ?></a>
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
 * Render the Event Type metabox.
 *
 * @since 2.6.0
 *
 * @param SZ_Events_Event|null $event The SZ_Events_Event object corresponding to the event being edited.
 */
function sz_events_admin_edit_metabox_event_type( SZ_Events_Event $event = null ) {

	// Bail if no event ID.
	if ( empty( $event->id ) ) {
		return;
	}

	$types         = sz_events_get_event_types( array(), 'objects' );
	$current_types = (array) sz_events_get_event_type( $event->id, false );
	$backend_only  = sz_events_get_event_types( array( 'show_in_create_screen' => false ) );
	?>

	<label for="sz-events-event-type" class="screen-reader-text"><?php
		/* translators: accessibility text */
		esc_html_e( 'Select event type', 'sportszone' );
	?></label>

	<ul class="categorychecklist form-no-clear">
		<?php foreach ( $types as $type ) : ?>
			<li>
				<label class="selectit"><input value="<?php echo esc_attr( $type->name ) ?>" name="sz-events-event-type[]" type="radio" <?php checked( true, in_array( $type->name, $current_types ) ); ?>>
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

	wp_nonce_field( 'sz-event-type-change-' . $event->id, 'sz-event-type-nonce' );
}

/**
 * Process changes from the Event Type metabox.
 *
 * @since 2.6.0
 *
 * @param int $event_id Event ID.
 */
function sz_events_process_event_type_update( $event_id ) {
	if ( ! isset( $_POST['sz-event-type-nonce'] ) ) {
		return;
	}

	check_admin_referer( 'sz-event-type-change-' . $event_id, 'sz-event-type-nonce' );

	// Permission check.
	if ( ! sz_current_user_can( 'sz_moderate' ) ) {
		return;
	}

	$event_types = ! empty( $_POST['sz-events-event-type'] ) ? wp_unslash( $_POST['sz-events-event-type'] ) : array();

	/*
	 * If an invalid event type is passed, someone's doing something
	 * fishy with the POST request, so we can fail silently.
	 */
	if ( sz_events_set_event_type( $event_id, $event_types ) ) {
		// @todo Success messages can't be posted because other stuff happens on the page load.
	}
}
add_action( 'sz_event_admin_edit_after', 'sz_events_process_event_type_update' );

/**
 * Create pagination links out of a SZ_Event_Member_Query.
 *
 * This function is intended to create pagination links for use under the
 * Manage Members section of the Events Admin Dashboard pages. It is a stopgap
 * measure until a more general pagination solution is in place for SportsZone.
 * Plugin authors should not use this function, as it is likely to be
 * deprecated soon.
 *
 * @since 1.8.0
 *
 * @param SZ_Event_Member_Query $query       A SZ_Event_Member_Query object.
 * @param string                $member_type member|mod|admin|banned.
 * @return string Pagination links HTML.
 */
function sz_events_admin_create_pagination_links( SZ_Event_Member_Query $query, $member_type ) {
	$pagination = '';

	if ( ! in_array( $member_type, array( 'admin', 'mod', 'member', 'banned' ) ) ) {
		return $pagination;
	}

	// The key used to paginate this member type in the $_GET global.
	$qs_key   = $member_type . '_page';
	$url_base = remove_query_arg( array( $qs_key, 'updated', 'success_modified' ), $_SERVER['REQUEST_URI'] );

	$page = isset( $_GET[ $qs_key ] ) ? absint( $_GET[ $qs_key ] ) : 1;

	/**
	 * Filters the number of members per member type that is displayed in event editing admin area.
	 *
	 * @since 2.8.0
	 *
	 * @param string $member_type Member type, which is a event role (admin, mod etc).
	 */
	$per_page = apply_filters( 'sz_events_admin_members_type_per_page', 10, $member_type );

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
			_nx( 'Viewing %1$s - %2$s of %3$s member', 'Viewing %1$s - %2$s of %3$s members', $query->total_users, 'Event members pagination in event admin', 'sportszone' ),
			sz_core_number_format( $current_page_start ),
			sz_core_number_format( $current_page_end ),
			sz_core_number_format( $query->total_users )
		);
	}

	$pagination .= '<span class="sz-event-admin-pagination-viewing">' . $viewing_text . '</span>';
	$pagination .= '<span class="sz-event-admin-pagination-links">' . $pag_links . '</span>';

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
function sz_events_admin_get_usernames_from_ids( $user_ids = array() ) {

	$usernames = array();
	$users     = new WP_User_Query( array( 'blog_id' => 0, 'include' => $user_ids ) );

	foreach ( (array) $users->results as $user ) {
		$usernames[] = $user->user_login;
	}

	return $usernames;
}

/**
 * AJAX handler for event member autocomplete requests.
 *
 * @since 1.7.0
 */
function sz_events_admin_autocomplete_handler() {

	// Bail if user user shouldn't be here, or is a large network.
	if ( ! sz_current_user_can( 'sz_moderate' ) || ( is_multisite() && wp_is_large_network( 'users' ) ) ) {
		wp_die( -1 );
	}

	$term     = isset( $_GET['term'] )     ? sanitize_text_field( $_GET['term'] ) : '';
	$event_id = isset( $_GET['event_id'] ) ? absint( $_GET['event_id'] )          : 0;

	if ( ! $term || ! $event_id ) {
		wp_die( -1 );
	}

	$suggestions = sz_core_get_suggestions( array(
		'event_id' => -$event_id,  // A negative value will exclude this event's members from the suggestions.
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
add_action( 'wp_ajax_sz_event_admin_member_autocomplete', 'sz_events_admin_autocomplete_handler' );

/**
 * Process input from the Event Type bulk change select.
 *
 * @since 2.7.0
 *
 * @param string $doaction Current $_GET action being performed in admin screen.
 */
function sz_events_admin_process_event_type_bulk_changes( $doaction ) {
	// Bail if no events are specified or if this isn't a relevant action.
	if ( empty( $_REQUEST['gid'] )
		|| ( empty( $_REQUEST['sz_change_type'] ) && empty( $_REQUEST['sz_change_type2'] ) )
		|| empty( $_REQUEST['sz_change_event_type'] )
	) {
		return;
	}

	// Bail if nonce check fails.
	check_admin_referer( 'sz-bulk-events-change-type-' . sz_loggedin_user_id(), 'sz-bulk-events-change-type-nonce' );

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
	if ( 'remove_event_type' !== $new_type && null === sz_events_get_event_type_object( $new_type ) ) {
		$error = true;
	} else {
		// Run through event ids.
		$error = false;
		foreach ( (array) $_REQUEST['gid'] as $event_id ) {
			$event_id = (int) $event_id;

			// Get the old event type to check against.
			$event_type = sz_events_get_event_type( $event_id );

			if ( 'remove_event_type' === $new_type ) {
				// Remove the current event type, if there's one to remove.
				if ( $event_type ) {
					$removed = sz_events_remove_event_type( $event_id, $event_type );
					if ( false === $removed || is_wp_error( $removed ) ) {
						$error = true;
					}
				}
			} else {
				// Set the new event type.
				if ( $new_type !== $event_type ) {
					$set = sz_events_set_event_type( $event_id, $new_type );
					if ( false === $set || is_wp_error( $set ) ) {
						$error = true;
					}
				}
			}
		}
	}

	// If there were any errors, show the error message.
	if ( $error ) {
		$redirect = add_query_arg( array( 'updated' => 'event-type-change-error' ), wp_get_referer() );
	} else {
		$redirect = add_query_arg( array( 'updated' => 'event-type-change-success' ), wp_get_referer() );
	}

	wp_redirect( $redirect );
	exit();
}
add_action( 'sz_events_admin_load', 'sz_events_admin_process_event_type_bulk_changes' );

/**
 * Display an admin notice upon event type bulk update.
 *
 * @since 2.7.0
 */
function sz_events_admin_events_type_change_notice() {
	$updated = isset( $_REQUEST['updated'] ) ? $_REQUEST['updated'] : false;

	// Display feedback.
	if ( $updated && in_array( $updated, array( 'event-type-change-error', 'event-type-change-success' ), true ) ) {

		if ( 'event-type-change-error' === $updated ) {
			$notice = __( 'There was an error while changing event type. Please try again.', 'sportszone' );
			$type   = 'error';
		} else {
			$notice = __( 'Event type was changed successfully.', 'sportszone' );
			$type   = 'updated';
		}

		sz_core_add_admin_notice( $notice, $type );
	}
}
add_action( sz_core_admin_hook(), 'sz_events_admin_events_type_change_notice' );
