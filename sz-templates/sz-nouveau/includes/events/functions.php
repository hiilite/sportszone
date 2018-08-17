<?php
/**
 * Events functions
 *
 * @since 3.0.0
 * @version 3.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register Scripts for the Events component
 *
 * @since 3.0.0
 *
 * @param array $scripts Optional. The array of scripts to register.
 *
 * @return array The same array with the specific events scripts.
 */
function sz_nouveau_events_register_scripts( $scripts = array() ) {
	if ( ! isset( $scripts['sz-nouveau'] ) ) {
		return $scripts;
	}

	return array_merge( $scripts, array(
		'sz-nouveau-event-invites' => array(
			'file'         => 'js/sportszone-event-invites%s.js',
			'dependencies' => array( 'sz-nouveau', 'json2', 'wp-backbone' ),
			'footer'       => true,
		),
	) );
}

/**
 * Enqueue the events scripts
 *
 * @since 3.0.0
 */
function sz_nouveau_events_enqueue_scripts() {
	// Neutralize Ajax when using SportsZone Events & member widgets on default front page
	if ( sz_is_event_home() && sz_nouveau_get_appearance_settings( 'event_front_page' ) ) {
		wp_add_inline_style( 'sz-nouveau', '
			#event-front-widgets #events-list-options,
			#event-front-widgets #members-list-options {
				display: none;
			}
		' );
	}

	if ( ! sz_is_event_invites() && ! ( sz_is_event_create() && sz_is_event_creation_step( 'event-invites' ) ) ) {
		return;
	}

	wp_enqueue_script( 'sz-nouveau-event-invites' );
}

/**
 * Can all members be invited to join any event?
 *
 * @since 3.0.0
 *
 * @param bool $default False to allow. True to disallow.
 *
 * @return bool
 */
function sz_nouveau_events_disallow_all_members_invites( $default = false ) {
	/**
	 * Filter to remove the All members nav, returning true
	 *
	 * @since 3.0.0
	 *
	 * @param bool $default True to disable the nav. False otherwise.
	 */
	return apply_filters( 'sz_nouveau_events_disallow_all_members_invites', $default );
}

/**
 * Localize the strings needed for the Event's Invite UI
 *
 * @since 3.0.0
 *
 * @param array $params Associative array containing the JS Strings needed by scripts
 *
 * @return array The same array with specific strings for the Event's Invite UI if needed.
 */
function sz_nouveau_events_localize_scripts( $params = array() ) {
	if ( ! sz_is_event_invites() && ! ( sz_is_event_create() && sz_is_event_creation_step( 'event-invites' ) ) ) {
		return $params;
	}

	$show_pending = sz_event_has_invites( array( 'user_id' => 'any' ) ) && ! sz_is_event_create();

	// Init the Event invites nav
	$invites_nav = array(
		'members' => array(
			'id'      => 'members',
			'caption' => __( 'All Members', 'sportszone' ),
			'order'   => 5,
		),
		'invited' => array(
			'id'      => 'invited',
			'caption' => __( 'Pending Invites', 'sportszone' ),
			'order'   => 90,
			'hide'    => (int) ! $show_pending,
		),
		'invites' => array(
			'id'      => 'invites',
			'caption' => __( 'Send Invites', 'sportszone' ),
			'order'   => 100,
			'hide'    => 1,
			'href'    => '#send-invites-editor',
		),
	);

	if ( sz_is_active( 'friends' ) ) {
		$invites_nav['friends'] = array(
			'id'      => 'friends',
			'caption' => __( 'My Friends', 'sportszone' ),
			'order'   => 0,
		);

		if ( true === sz_nouveau_events_disallow_all_members_invites() ) {
			unset( $invites_nav['members'] );
		}
	}

	$params['event_invites'] = array(
		'nav'                => sz_sort_by_key( $invites_nav, 'order', 'num' ),
		'loading'            => __( 'Loading members. Please wait.', 'sportszone' ),
		'invites_form'       => __( 'Use the "Send" button to send your invite or the "Cancel" button to abort.', 'sportszone' ),
		'invites_form_reset' => __( 'Event invitations cleared. Please use one of the available tabs to select members to invite.', 'sportszone' ),
		'invites_sending'    => __( 'Sending event invitations. Please wait.', 'sportszone' ),
		'removeUserInvite'   => __( 'Cancel invitation %s', 'sportszone' ),
		'event_id'           => ! sz_get_current_event_id() ? sz_get_new_event_id() : sz_get_current_event_id(),
		'is_event_create'    => sz_is_event_create(),
		'nonces'             => array(
			'uninvite'     => wp_create_nonce( 'events_invite_uninvite_user' ),
			'send_invites' => wp_create_nonce( 'events_send_invites' )
		),
	);

	return $params;
}

/**
 * @since 3.0.0
 */
function sz_nouveau_events_get_inviter_ids( $user_id, $event_id ) {
	if ( empty( $user_id ) || empty( $event_id ) ) {
		return false;
	}

	return SZ_Nouveau_Event_Invite_Query::get_inviter_ids( $user_id, $event_id );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_prepare_event_potential_invites_for_js( $user ) {
	$sz = sportszone();

	$response = array(
		'id'           => intval( $user->ID ),
		'name'         => $user->display_name,
		'avatar'       => htmlspecialchars_decode( sz_core_fetch_avatar( array(
			'item_id' => $user->ID,
			'object'  => 'user',
			'type'    => 'thumb',
			'width'   => 50,
			'height'  => 50,
			'html'    => false )
		) ),
	);

	// Do extra queries only if needed
	if ( ! empty( $sz->events->invites_scope ) && 'invited' === $sz->events->invites_scope ) {
		$response['is_sent']  = (bool) events_check_user_has_invite( $user->ID, sz_get_current_event_id() );

		$inviter_ids = sz_nouveau_events_get_inviter_ids( $user->ID, sz_get_current_event_id() );

		foreach ( $inviter_ids as $inviter_id ) {
			$class = false;

			if ( sz_loggedin_user_id() === (int) $inviter_id ) {
				$class = 'event-self-inviter';
			}

			$response['invited_by'][] = array(
				'avatar' => htmlspecialchars_decode( sz_core_fetch_avatar( array(
					'item_id' => $inviter_id,
					'object'  => 'user',
					'type'    => 'thumb',
					'width'   => 50,
					'height'  => 50,
					'html'    => false,
					'class'   => $class,
				) ) ),
				'user_link' => sz_core_get_userlink( $inviter_id, false, true ),
				'user_name' => sz_core_get_username( $inviter_id ),
			);
		}

		if ( sz_is_item_admin() ) {
			$response['can_edit'] = true;
		} else {
			$response['can_edit'] = in_array( sz_loggedin_user_id(), $inviter_ids );
		}
	}

	/**
	 * Filters the response value for potential event invite data for use with javascript.
	 *
	 * @since 3.0.0
	 *
	 * @param array   $response Array of invite data.
	 * @param WP_User $user User object.
	 */
	return apply_filters( 'sz_nouveau_prepare_event_potential_invites_for_js', $response, $user );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_get_event_potential_invites( $args = array() ) {
	$r = sz_parse_args( $args, array(
		'event_id'     => sz_get_current_event_id(),
		'type'         => 'alphabetical',
		'per_page'     => 20,
		'page'         => 1,
		'search_terms' => false,
		'member_type'  => false,
		'user_id'      => 0,
		'is_confirmed' => true,
	) );

	if ( empty( $r['event_id'] ) ) {
		return false;
	}

	/*
	 * If it's not a friend request and users can restrict invites to friends,
	 * make sure they are not displayed in results.
	 */
	if ( ! $r['user_id'] && sz_is_active( 'friends' ) && sz_is_active( 'settings' ) && ! sz_nouveau_events_disallow_all_members_invites() ) {
		$r['meta_query'] = array(
			array(
				'key'     => '_sz_nouveau_restrict_invites_to_friends',
				'compare' => 'NOT EXISTS',
			),
		);
	}

	$query = new SZ_Nouveau_Event_Invite_Query( $r );

	$response = new stdClass();

	$response->meta = array( 'total_page' => 0, 'current_page' => 0 );
	$response->users = array();

	if ( ! empty( $query->results ) ) {
		$response->users = $query->results;

		if ( ! empty( $r['per_page'] ) ) {
			$response->meta = array(
				'total_page' => ceil( (int) $query->total_users / (int) $r['per_page'] ),
				'page'       => (int) $r['page'],
			);
		}
	}

	return $response;
}

/**
 * @since 3.0.0
 */
function sz_nouveau_event_invites_create_steps( $steps = array() ) {
	if ( sz_is_active( 'friends' ) && isset( $steps['event-invites'] ) ) {
		// Simply change the name
		$steps['event-invites']['name'] = _x( 'Invite', 'Event invitations menu title', 'sportszone' );
		return $steps;
	}

	// Add the create step if friends component is not active
	$steps['event-invites'] = array(
		'name'     => _x( 'Invite', 'Event invitations menu title', 'sportszone' ),
		'position' => 30,
	);

	return $steps;
}

/**
 * @since 3.0.0
 */
function sz_nouveau_event_setup_nav() {
	if ( ! sz_is_event() || ! sz_events_user_can_send_invites() ) {
		return;
	}

	// Simply change the name
	if ( sz_is_active( 'friends' ) ) {
		$sz = sportszone();

		$sz->events->nav->edit_nav(
			array( 'name' => _x( 'Invite', 'Event invitations menu title', 'sportszone' ) ),
			'send-invites',
			sz_get_current_event_slug()
		);

	// Create the Subnav item for the event
	} else {
		$current_event = events_get_current_event();
		$event_link    = sz_get_event_permalink( $current_event );

		sz_core_new_subnav_item( array(
			'name'            => _x( 'Invite', 'Event invitations menu title', 'sportszone' ),
			'slug'            => 'send-invites',
			'parent_url'      => $event_link,
			'parent_slug'     => $current_event->slug,
			'screen_function' => 'events_screen_event_invite',
			'item_css_id'     => 'invite',
			'position'        => 70,
			'user_has_access' => $current_event->user_has_access,
			'no_access_url'   => $event_link,
		) );
	}
}

/**
 * @since 3.0.0
 */
function sz_nouveau_events_invites_custom_message( $message = '' ) {
	if ( empty( $message ) ) {
		return $message;
	}

	$sz = sportszone();

	if ( empty( $sz->events->invites_message ) ) {
		return $message;
	}

	$message = str_replace( '---------------------', "
---------------------\n
" . $sz->events->invites_message . "\n
---------------------
	", $message );

	return $message;
}

/**
 * Format a Event for a json reply
 *
 * @since 3.0.0
 */
function sz_nouveau_prepare_event_for_js( $item ) {
	if ( empty( $item->id ) ) {
		return array();
	}

	$item_avatar_url = sz_core_fetch_avatar( array(
		'item_id'    => $item->id,
		'object'     => 'event',
		'type'       => 'thumb',
		'html'       => false
	) );

	return array(
		'id'          => $item->id,
		'name'        => $item->name,
		'avatar_url'  => $item_avatar_url,
		'object_type' => 'event',
		'is_public'   => ( 'public' === $item->status ),
	);
}

/**
 * Event invites restriction settings navigation.
 *
 * @since 3.0.0
 */
function sz_nouveau_events_invites_restriction_nav() {
	$slug        = sz_get_settings_slug();
	$user_domain = sz_loggedin_user_domain();

	if ( sz_displayed_user_domain() ) {
		$user_domain = sz_displayed_user_domain();
	}

	sz_core_new_subnav_item( array(
		'name'            => _x( 'Event Invites', 'Event invitations main menu title', 'sportszone' ),
		'slug'            => 'invites',
		'parent_url'      => trailingslashit( $user_domain . $slug ),
		'parent_slug'     => $slug,
		'screen_function' => 'sz_nouveau_events_screen_invites_restriction',
		'item_css_id'     => 'invites',
		'position'        => 70,
		'user_has_access' => sz_core_can_edit_settings(),
	), 'members' );
}

/**
 * Event invites restriction settings Admin Bar navigation.
 *
 * @since 3.0.0
 *
 * @param array $wp_admin_nav The list of settings admin subnav items.
 *
 * @return array The list of settings admin subnav items.
 */
function sz_nouveau_events_invites_restriction_admin_nav( $wp_admin_nav ) {
	// Setup the logged in user variables.
	$settings_link = trailingslashit( sz_loggedin_user_domain() . sz_get_settings_slug() );

	// Add the "Event Invites" subnav item.
	$wp_admin_nav[] = array(
		'parent' => 'my-account-' . sportszone()->settings->id,
		'id'     => 'my-account-' . sportszone()->settings->id . '-invites',
		'title'  => _x( 'Event Invites', 'Event invitations main menu title', 'sportszone' ),
		'href'   => trailingslashit( $settings_link . 'invites/' ),
	);

	return $wp_admin_nav;
}

/**
 * Event invites restriction screen.
 *
 * @since 3.0.0
 */
function sz_nouveau_events_screen_invites_restriction() {
	// Redirect if no invites restriction settings page is accessible.
	if ( 'invites' !== sz_current_action() || ! sz_is_active( 'friends' ) ) {
		sz_do_404();
		return;
	}

	if ( isset( $_POST['member-event-invites-submit'] ) ) {
		// Nonce check.
		check_admin_referer( 'sz_nouveau_event_invites_settings' );

		if ( sz_is_my_profile() || sz_current_user_can( 'sz_moderate' ) ) {
			if ( empty( $_POST['account-event-invites-preferences'] ) ) {
				sz_delete_user_meta( sz_displayed_user_id(), '_sz_nouveau_restrict_invites_to_friends' );
			} else {
				sz_update_user_meta( sz_displayed_user_id(), '_sz_nouveau_restrict_invites_to_friends', (int) $_POST['account-event-invites-preferences'] );
			}

			sz_core_add_message( __( 'Event invites preferences saved.', 'sportszone' ) );
		} else {
			sz_core_add_message( __( 'You are not allowed to perform this action.', 'sportszone' ), 'error' );
		}

		sz_core_redirect( trailingslashit( sz_displayed_user_domain() . sz_get_settings_slug() ) . 'invites/' );
	}

	/**
	 * Filters the template to load for the Event Invites settings screen.
	 *
	 * @since 3.0.0
	 *
	 * @param string $template Path to the Event Invites settings screen template to load.
	 */
	sz_core_load_template( apply_filters( 'sz_nouveau_events_screen_invites_restriction', 'members/single/settings/event-invites' ) );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_get_events_directory_nav_items() {
	$nav_items = array();

	$nav_items['all'] = array(
		'component' => 'events',
		'slug'      => 'all', // slug is used because SZ_Core_Nav requires it, but it's the scope
		'li_class'  => array( 'selected' ),
		'link'      => sz_get_events_directory_permalink(),
		'text'      => __( 'All Events', 'sportszone' ),
		'count'     => sz_get_total_event_count(),
		'position'  => 5,
	);

	if ( is_user_logged_in() ) {

		$my_events_count = sz_get_total_event_count_for_user( sz_loggedin_user_id() );

		// If the user has events create a nav item
		if ( $my_events_count ) {
			$nav_items['personal'] = array(
				'component' => 'events',
				'slug'      => 'personal', // slug is used because SZ_Core_Nav requires it, but it's the scope
				'li_class'  => array(),
				'link'      => sz_loggedin_user_domain() . sz_get_events_slug() . '/my-events/',
				'text'      => __( 'My Events', 'sportszone' ),
				'count'     => $my_events_count,
				'position'  => 15,
			);
		}

		// If the user can create events, add the create nav
		if ( sz_user_can_create_events() ) {
			$nav_items['create'] = array(
				'component' => 'events',
				'slug'      => 'create', // slug is used because SZ_Core_Nav requires it, but it's the scope
				'li_class'  => array( 'no-ajax', 'event-create', 'create-button' ),
				'link'      => trailingslashit( sz_get_events_directory_permalink() . 'create' ),
				'text'      => __( 'Create a Event', 'sportszone' ),
				'count'     => false,
				'position'  => 999,
			);
		}
	}

	// Check for the deprecated hook :
	$extra_nav_items = sz_nouveau_parse_hooked_dir_nav( 'sz_events_directory_event_filter', 'events', 20 );

	if ( ! empty( $extra_nav_items ) ) {
		$nav_items = array_merge( $nav_items, $extra_nav_items );
	}

	/**
	 * Use this filter to introduce your custom nav items for the events directory.
	 *
	 * @since 3.0.0
	 *
	 * @param  array $nav_items The list of the events directory nav items.
	 */
	return apply_filters( 'sz_nouveau_get_events_directory_nav_items', $nav_items );
}

/**
 * Get Dropdown filters for the events component
 *
 * @since 3.0.0
 *
 * @param string $context 'directory' or 'user'
 *
 * @return array the filters
 */
function sz_nouveau_get_events_filters( $context = '' ) {
	if ( empty( $context ) ) {
		return array();
	}

	$action = '';
	if ( 'user' === $context ) {
		$action = 'sz_member_event_order_options';
	} elseif ( 'directory' === $context ) {
		$action = 'sz_events_directory_order_options';
	}

	/**
	 * Recommended, filter here instead of adding an action to 'sz_member_event_order_options'
	 * or 'sz_events_directory_order_options'
	 *
	 * @since 3.0.0
	 *
	 * @param array  the members filters.
	 * @param string the context.
	 */
	$filters = apply_filters( 'sz_nouveau_get_events_filters', array(
		'active'       => __( 'Last Active', 'sportszone' ),
		'popular'      => __( 'Most Members', 'sportszone' ),
		'newest'       => __( 'Newly Created', 'sportszone' ),
		'alphabetical' => __( 'Alphabetical', 'sportszone' ),
	), $context );

	if ( $action ) {
		return sz_nouveau_parse_hooked_options( $action, $filters );
	}

	return $filters;
}

/**
 * Catch the arguments for buttons
 *
 * @since 3.0.0
 *
 * @param array $button The arguments of the button that SportsZone is about to create.
 *
 * @return array An empty array to stop the button creation process.
 */
function sz_nouveau_events_catch_button_args( $button = array() ) {
	/**
	 * Globalize the arguments so that we can use it
	 * in sz_nouveau_get_events_buttons().
	 */
	sz_nouveau()->events->button_args = $button;

	// return an empty array to stop the button creation process
	return array();
}

/**
 * Catch the content hooked to the 'sz_event_header_meta' action
 *
 * @since 3.0.0
 *
 * @return string|bool HTML Output if hooked. False otherwise.
 */
function sz_nouveau_get_hooked_event_meta() {
	ob_start();

	/**
	 * Fires after inside the event header item meta section.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_event_header_meta' );

	$output = ob_get_clean();

	if ( ! empty( $output ) ) {
		return $output;
	}

	return false;
}

/**
 * Display the Widgets of Event extensions into the default front page?
 *
 * @since 3.0.0
 *
 * @return bool True to display. False otherwise.
 */
function sz_nouveau_events_do_event_boxes() {
	$event_settings = sz_nouveau_get_appearance_settings();

	return ! empty( $event_settings['event_front_page'] ) && ! empty( $event_settings['event_front_boxes'] );
}

/**
 * Display description of the Event into the default front page?
 *
 * @since 3.0.0
 *
 * @return bool True to display. False otherwise.
 */
function sz_nouveau_events_front_page_description() {
	$event_settings = sz_nouveau_get_appearance_settings();

	// This check is a problem it needs to be used in templates but returns true even if not on the front page
	// return false on this if we are not displaying the front page 'sz_is_event_home()'
	// This may well be a bad approach to re-think ~hnla.
	// @todo
	return ! empty( $event_settings['event_front_page'] ) && ! empty( $event_settings['event_front_description'] ) && sz_is_event_home();
}

/**
 * Add sections to the customizer for the events component.
 *
 * @since 3.0.0
 *
 * @param array $sections the Customizer sections to add.
 *
 * @return array the Customizer sections to add.
 */
function sz_nouveau_events_customizer_sections( $sections = array() ) {
	return array_merge( $sections, array(
		'sz_nouveau_event_front_page' => array(
			'title'       => __( 'Event front page', 'sportszone' ),
			'panel'       => 'sz_nouveau_panel',
			'priority'    => 20,
			'description' => __( 'Configure the default front page for events.', 'sportszone' ),
		),
		'sz_nouveau_event_primary_nav' => array(
			'title'       => __( 'Event navigation', 'sportszone' ),
			'panel'       => 'sz_nouveau_panel',
			'priority'    => 40,
			'description' => __( 'Customize the navigation menu for events. See your changes by navigating to a event in the live-preview window.', 'sportszone' ),
		),
	) );
}

/**
 * Add settings to the customizer for the events component.
 *
 * @since 3.0.0
 *
 * @param array $settings Optional. The settings to add.
 *
 * @return array the settings to add.
 */
function sz_nouveau_events_customizer_settings( $settings = array() ) {
	return array_merge( $settings, array(
		'sz_nouveau_appearance[event_front_page]' => array(
			'index'             => 'event_front_page',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[event_front_boxes]' => array(
			'index'             => 'event_front_boxes',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[event_front_description]' => array(
			'index'             => 'event_front_description',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[event_nav_display]' => array(
			'index'             => 'event_nav_display',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[event_nav_tabs]' => array(
			'index'             => 'event_nav_tabs',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[event_subnav_tabs]' => array(
			'index'             => 'event_subnav_tabs',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[events_create_tabs]' => array(
			'index'             => 'events_create_tabs',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[event_nav_order]' => array(
			'index'             => 'event_nav_order',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'sz_nouveau_sanitize_nav_order',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[events_layout]' => array(
			'index'             => 'events_layout',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
		'sz_nouveau_appearance[events_dir_tabs]' => array(
			'index'             => 'events_dir_tabs',
			'capability'        => 'sz_moderate',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
			'type'              => 'option',
		),
	) );
}

/**
 * Add controls for the settings of the customizer for the events component.
 *
 * @since 3.0.0
 *
 * @param array $controls Optional. The controls to add.
 *
 * @return array the controls to add.
 */
function sz_nouveau_events_customizer_controls( $controls = array() ) {
	return array_merge( $controls, array(
		'event_front_page' => array(
			'label'      => __( 'Enable custom front pages for events.', 'sportszone' ),
			'section'    => 'sz_nouveau_event_front_page',
			'settings'   => 'sz_nouveau_appearance[event_front_page]',
			'type'       => 'checkbox',
		),
		'event_front_boxes' => array(
			'label'      => __( 'Enable widget region for event homepages. When enabled, the site admin can add widgets to event pages via the Widgets panel.', 'sportszone' ),
			'section'    => 'sz_nouveau_event_front_page',
			'settings'   => 'sz_nouveau_appearance[event_front_boxes]',
			'type'       => 'checkbox',
		),
		'event_front_description' => array(
			'label'      => __( "Display the event description in the body of the event's front page.", 'sportszone' ),
			'section'    => 'sz_nouveau_event_front_page',
			'settings'   => 'sz_nouveau_appearance[event_front_description]',
			'type'       => 'checkbox',
		),
		'event_nav_display' => array(
			'label'      => __( 'Display the event navigation vertically.', 'sportszone' ),
			'section'    => 'sz_nouveau_event_primary_nav',
			'settings'   => 'sz_nouveau_appearance[event_nav_display]',
			'type'       => 'checkbox',
		),
		'event_nav_tabs' => array(
			'label'      => __( 'Use tab styling for primary navigation.', 'sportszone' ),
			'section'    => 'sz_nouveau_event_primary_nav',
			'settings'   => 'sz_nouveau_appearance[event_nav_tabs]',
			'type'       => 'checkbox',
		),
		'event_subnav_tabs' => array(
			'label'      => __( 'Use tab styling for secondary navigation.', 'sportszone' ),
			'section'    => 'sz_nouveau_event_primary_nav',
			'settings'   => 'sz_nouveau_appearance[event_subnav_tabs]',
			'type'       => 'checkbox',
		),
		'events_create_tabs' => array(
			'label'      => __( 'Use tab styling for the event creation process.', 'sportszone' ),
			'section'    => 'sz_nouveau_event_primary_nav',
			'settings'   => 'sz_nouveau_appearance[events_create_tabs]',
			'type'       => 'checkbox',
		),
		'event_nav_order' => array(
			'class'       => 'SZ_Nouveau_Nav_Customize_Control',
			'label'      => __( 'Reorder the primary navigation for a event.', 'sportszone' ),
			'section'    => 'sz_nouveau_event_primary_nav',
			'settings'   => 'sz_nouveau_appearance[event_nav_order]',
			'type'       => 'event',
		),
		'events_layout' => array(
			'label'      => _x( 'Events', 'Customizer control label', 'sportszone' ),
			'section'    => 'sz_nouveau_loops_layout',
			'settings'   => 'sz_nouveau_appearance[events_layout]',
			'type'       => 'select',
			'choices'    => sz_nouveau_customizer_grid_choices(),
		),
		'members_event_layout' => array(
			'label'      => __( 'Event > Members', 'sportszone' ),
			'section'    => 'sz_nouveau_loops_layout',
			'settings'   => 'sz_nouveau_appearance[members_event_layout]',
			'type'       => 'select',
			'choices'    => sz_nouveau_customizer_grid_choices(),
		),
		'event_dir_layout' => array(
			'label'      => __( 'Use column navigation for the Events directory.', 'sportszone' ),
			'section'    => 'sz_nouveau_dir_layout',
			'settings'   => 'sz_nouveau_appearance[events_dir_layout]',
			'type'       => 'checkbox',
		),
		'event_dir_tabs' => array(
			'label'      => __( 'Use tab styling for Events directory navigation.', 'sportszone' ),
			'section'    => 'sz_nouveau_dir_layout',
			'settings'   => 'sz_nouveau_appearance[events_dir_tabs]',
			'type'       => 'checkbox',
		),
	) );
}

/**
 * Add the default event front template to the front template hierarchy.
 *
 * @since 3.0.0
 *
 * @param array           $templates Optional. The list of templates for the front.php template part.
 * @param SZ_Events_Event $event Optional. The event object.
 *
 * @return array The same list with the default front template if needed.
 */
function sz_nouveau_event_reset_front_template( $templates = array(), $event = null ) {
	if ( empty( $event->id ) ) {
		return $templates;
	}

	$use_default_front = sz_nouveau_get_appearance_settings( 'event_front_page' );

	// Setting the front template happens too early, so we need this!
	if ( is_customize_preview() ) {
		$use_default_front = sz_nouveau_get_temporary_setting( 'event_front_page', $use_default_front );
	}

	if ( ! empty( $use_default_front ) ) {
		array_push( $templates, 'events/single/default-front.php' );
	}

	/**
	 * Filters the SportsZone Nouveau template hierarchy after resetting front template for events.
	 *
	 * @since 3.0.0
	 *
	 * @param array $templates Array of templates.
	 */
	return apply_filters( '_sz_nouveau_event_reset_front_template', $templates );
}

/**
 * Locate a single event template into a specific hierarchy.
 *
 * @since 3.0.0
 *
 * @param string $template Optional. The template part to get (eg: activity, members...).
 *
 * @return string The located template.
 */
function sz_nouveau_event_locate_template_part( $template = '' ) {
	$current_event = events_get_current_event();
	$sz_nouveau    = sz_nouveau();

	if ( ! $template || empty( $current_event->id ) ) {
		return '';
	}

	// Use a global to avoid requesting the hierarchy for each template
	if ( ! isset( $sz_nouveau->events->current_event_hierarchy ) ) {
		$sz_nouveau->events->current_event_hierarchy = array(
			'events/single/%s-id-' . sanitize_file_name( $current_event->id ) . '.php',
			'events/single/%s-slug-' . sanitize_file_name( $current_event->slug ) . '.php',
		);

		/**
		 * Check for event types and add it to the hierarchy
		 */
		if ( sz_events_get_event_types() ) {
			$current_event_type = sz_events_get_event_type( $current_event->id );
			if ( ! $current_event_type ) {
				$current_event_type = 'none';
			}

			$sz_nouveau->events->current_event_hierarchy[] = 'events/single/%s-event-type-' . sanitize_file_name( $current_event_type ) . '.php';
		}

		$sz_nouveau->events->current_event_hierarchy = array_merge( $sz_nouveau->events->current_event_hierarchy, array(
			'events/single/%s-status-' . sanitize_file_name( $current_event->status ) . '.php',
			'events/single/%s.php'
		) );
	}

	// Init the templates
	$templates = array();

	// Loop in the hierarchy to fill it for the requested template part
	foreach ( $sz_nouveau->events->current_event_hierarchy as $part ) {
		$templates[] = sprintf( $part, sanitize_file_name( $template ) );
	}

	/**
	 * Filters the found template parts for the event template part locating functionality.
	 *
	 * @since 3.0.0
	 *
	 * @param array $templates Array of found templates.
	 */
	return sz_locate_template( apply_filters( 'sz_nouveau_event_locate_template_part', $templates ), false, true );
}

/**
 * Load a single event template part
 *
 * @since 3.0.0
 *
 * @param string $template Optional. The template part to get (eg: activity, members...).
 *
 * @return string HTML output.
 */
function sz_nouveau_event_get_template_part( $template = '' ) {
	$located = sz_nouveau_event_locate_template_part( $template );

	if ( false !== $located ) {
		$slug = str_replace( '.php', '', $located );
		$name = null;

		/**
		 * Let plugins adding an action to sz_get_template_part get it from here.
		 *
		 * This is a variable hook that is dependent on the template part slug.
		 *
		 * @since 3.0.0
		 *
		 * @param string $slug Template part slug requested.
		 * @param string $name Template part name requested.
		 */
		do_action( 'get_template_part_' . $slug, $slug, $name );

		load_template( $located, true );
	}

	return $located;
}

/**
 * Are we inside the Current event's default front page sidebar?
 *
 * @since 3.0.0
 *
 * @return bool True if in the event's home sidebar. False otherwise.
 */
function sz_nouveau_event_is_home_widgets() {
	return ( true === sz_nouveau()->events->is_event_home_sidebar );
}

/**
 * Filter the Latest activities Widget to only keep the one of the event displayed
 *
 * @since 3.0.0
 *
 * @param array $args Optional. The Activities Template arguments.
 *
 * @return array The Activities Template arguments.
 */
function sz_nouveau_event_activity_widget_overrides( $args = array() ) {
	return array_merge( $args, array(
		'object'     => 'events',
		'primary_id' => sz_get_current_event_id(),
	) );
}

/**
 * Filter the Events widget to only keep the displayed event.
 *
 * @since 3.0.0
 *
 * @param array $args Optional. The Events Template arguments.
 *
 * @return array The Events Template arguments.
 */
function sz_nouveau_event_events_widget_overrides( $args = array() ) {
	return array_merge( $args, array(
		'include' => sz_get_current_event_id(),
	) );
}

/**
 * Filter the Members widgets to only keep members of the displayed event.
 *
 * @since 3.0.0
 *
 * @param array $args Optional. The Members Template arguments.
 *
 * @return array The Members Template arguments.
 */
function sz_nouveau_event_members_widget_overrides( $args = array() ) {
	$event_members = events_get_event_members( array( 'exclude_admins_mods' => false ) );

	if ( empty( $event_members['members'] ) ) {
		return $args;
	}

	return array_merge( $args, array(
		'include' => wp_list_pluck( $event_members['members'], 'ID' ),
	) );
}

/**
 * Init the Event's default front page filters as we're in the sidebar
 *
 * @since 3.0.0
 */
function sz_nouveau_events_add_home_widget_filters() {
	add_filter( 'sz_nouveau_activity_widget_query', 'sz_nouveau_event_activity_widget_overrides', 10, 1 );
	add_filter( 'sz_before_has_events_parse_args', 'sz_nouveau_event_events_widget_overrides', 10, 1 );
	add_filter( 'sz_before_has_members_parse_args', 'sz_nouveau_event_members_widget_overrides', 10, 1 );

	/**
	 * Fires after SportsZone Nouveau events have added their home widget filters.
	 *
	 * @since 3.0.0
	 */
	do_action( 'sz_nouveau_events_add_home_widget_filters' );
}

/**
 * Remove the Event's default front page filters as we're no more in the sidebar
 *
 * @since 3.0.0
 */
function sz_nouveau_events_remove_home_widget_filters() {
	remove_filter( 'sz_nouveau_activity_widget_query', 'sz_nouveau_event_activity_widget_overrides', 10, 1 );
	remove_filter( 'sz_before_has_events_parse_args', 'sz_nouveau_event_events_widget_overrides', 10, 1 );
	remove_filter( 'sz_before_has_members_parse_args', 'sz_nouveau_event_members_widget_overrides', 10, 1 );

	/**
	 * Fires after SportsZone Nouveau events have removed their home widget filters.
	 *
	 * @since 3.0.0
	 */
	do_action( 'sz_nouveau_events_remove_home_widget_filters' );
}

/**
 * Get the hook, nonce, and eventually a specific template for Core Event's create screens.
 *
 * @since 3.0.0
 *
 * @param string $id Optional. The screen id
 *
 * @return mixed An array containing the hook dynamic part, the nonce, and eventually a specific template.
 *               False if it's not a core create screen.
 */
function sz_nouveau_event_get_core_create_screens( $id = '' ) {
	// screen id => dynamic part of the hooks, nonce & specific template to use.
	$screens = array(
		'event-details' => array(
			'hook'     => 'event_details_creation_step',
			'nonce'    => 'events_create_save_event-details',
			'template' => 'events/single/admin/edit-details',
		),
		'event-settings' => array(
			'hook'  => 'event_settings_creation_step',
			'nonce' => 'events_create_save_event-settings',
		),
		'event-avatar' => array(
			'hook'  => 'event_avatar_creation_step',
			'nonce' => 'events_create_save_event-avatar',
		),
		'event-cover-image' => array(
			'hook'  => 'event_cover_image_creation_step',
			'nonce' => 'events_create_save_event-cover-image',
		),
		'event-invites' => array(
			'hook'     => 'event_invites_creation_step',
			'nonce'    => 'events_create_save_event-invites',
			'template' => 'common/js-templates/invites/index',
		),
	);

	if ( isset( $screens[ $id ] ) ) {
		return $screens[ $id ];
	}

	return false;
}

/**
 * Get the hook and nonce for Core Event's manage screens.
 *
 * @since 3.0.0
 *
 * @param string $id Optional. The screen id
 *
 * @return mixed An array containing the hook dynamic part and the nonce.
 *               False if it's not a core manage screen.
 */
function sz_nouveau_event_get_core_manage_screens( $id = '' ) {
	// screen id => dynamic part of the hooks & nonce.
	$screens = array(
		'edit-details'        => array( 'hook' => 'event_details_admin',             'nonce' => 'events_edit_event_details'  ),
		'event-settings'      => array( 'hook' => 'event_settings_admin',            'nonce' => 'events_edit_event_settings' ),
		'event-avatar'        => array(),
		'event-cover-image'   => array( 'hook' => 'event_settings_cover_image',      'nonce' => ''                           ),
		'manage-members'      => array( 'hook' => 'event_manage_members_admin',      'nonce' => ''                           ),
		'membership-requests' => array( 'hook' => 'event_membership_requests_admin', 'nonce' => ''                           ),
		'delete-event'        => array( 'hook' => 'event_delete_admin',              'nonce' => 'events_delete_event'        ),
	);

	if ( isset( $screens[ $id ] ) ) {
		return $screens[ $id ];
	}

	return false;
}

/**
 * Register notifications filters for the events component.
 *
 * @since 3.0.0
 */
function sz_nouveau_events_notification_filters() {
	$notifications = array(
		array(
			'id'       => 'new_membership_request',
			'label'    => __( 'Pending Event membership requests', 'sportszone' ),
			'position' => 55,
		),
		array(
			'id'       => 'membership_request_accepted',
			'label'    => __( 'Accepted Event membership requests', 'sportszone' ),
			'position' => 65,
		),
		array(
			'id'       => 'membership_request_rejected',
			'label'    => __( 'Rejected Event membership requests', 'sportszone' ),
			'position' => 75,
		),
		array(
			'id'       => 'member_promoted_to_admin',
			'label'    => __( 'Event Administrator promotions', 'sportszone' ),
			'position' => 85,
		),
		array(
			'id'       => 'member_promoted_to_mod',
			'label'    => __( 'Event Moderator promotions', 'sportszone' ),
			'position' => 95,
		),
		array(
			'id'       => 'event_invite',
			'label'    => __( 'Event invitations', 'sportszone' ),
			'position' => 105,
		),
	);

	foreach ( $notifications as $notification ) {
		sz_nouveau_notifications_register_filter( $notification );
	}
}
