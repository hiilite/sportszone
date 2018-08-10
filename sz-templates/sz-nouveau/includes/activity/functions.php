<?php
/**
 * Activity functions
 *
 * @since 3.0.0
 * @version 3.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register Scripts for the Activity component
 *
 * @since 3.0.0
 *
 * @param array $scripts  The array of scripts to register.
 *
 * @return array The same array with the specific activity scripts.
 */
function sz_nouveau_activity_register_scripts( $scripts = array() ) {
	if ( ! isset( $scripts['sz-nouveau'] ) ) {
		return $scripts;
	}

	return array_merge( $scripts, array(
		'sz-nouveau-activity' => array(
			'file'         => 'js/sportszone-activity%s.js',
			'dependencies' => array( 'sz-nouveau' ),
			'footer'       => true,
		),
		'sz-nouveau-activity-post-form' => array(
			'file'         => 'js/sportszone-activity-post-form%s.js',
			'dependencies' => array( 'sz-nouveau', 'sz-nouveau-activity', 'json2', 'wp-backbone' ),
			'footer'       => true,
		),
	) );
}

/**
 * Enqueue the activity scripts
 *
 * @since 3.0.0
 */
function sz_nouveau_activity_enqueue_scripts() {
	if ( ! sz_is_activity_component() && ! sz_is_group_activity() ) {
		return;
	}

	wp_enqueue_script( 'sz-nouveau-activity' );
}

/**
 * Localize the strings needed for the Activity Post form UI
 *
 * @since 3.0.0
 *
 * @param array $params Associative array containing the JS Strings needed by scripts.
 *
 * @return array The same array with specific strings for the Activity Post form UI if needed.
 */
function sz_nouveau_activity_localize_scripts( $params = array() ) {
	if ( ! sz_is_activity_component() && ! sz_is_group_activity() ) {
		return $params;
	}

	$activity_params = array(
		'user_id'     => sz_loggedin_user_id(),
		'object'      => 'user',
		'backcompat'  => (bool) has_action( 'sz_activity_post_form_options' ),
		'post_nonce'  => wp_create_nonce( 'post_update', '_wpnonce_post_update' ),
	);

	$user_displayname = sz_get_loggedin_user_fullname();

	if ( sportszone()->avatar->show_avatars ) {
		$width  = sz_core_avatar_thumb_width();
		$height = sz_core_avatar_thumb_height();
		$activity_params = array_merge( $activity_params, array(
			'avatar_url'    => sz_get_loggedin_user_avatar( array(
				'width'  => $width,
				'height' => $height,
				'html'   => false,
			) ),
			'avatar_width'  => $width,
			'avatar_height' => $height,
			'user_domain'   => sz_loggedin_user_domain(),
			'avatar_alt'    => sprintf(
				/* translators: %s = member name */
				__( 'Profile photo of %s', 'sportszone' ),
				$user_displayname
			),
		) );
	}

	/**
	 * Filters the included, specific, Action buttons.
	 *
	 * @since 3.0.0
	 *
	 * @param array $value The array containing the button params. Must look like:
	 * array( 'buttonid' => array(
	 *  'id'      => 'buttonid',                            // Id for your action
	 *  'caption' => __( 'Button caption', 'text-domain' ),
	 *  'icon'    => 'dashicons-*',                         // The dashicon to use
	 *  'order'   => 0,
	 *  'handle'  => 'button-script-handle',                // The handle of the registered script to enqueue
	 * );
	 */
	$activity_buttons = apply_filters( 'sz_nouveau_activity_buttons', array() );

	if ( ! empty( $activity_buttons ) ) {
		$activity_params['buttons'] = sz_sort_by_key( $activity_buttons, 'order', 'num' );

		// Enqueue Buttons scripts and styles
		foreach ( $activity_params['buttons'] as $key_button => $buttons ) {
			if ( empty( $buttons['handle'] ) ) {
				continue;
			}

			if ( wp_style_is( $buttons['handle'], 'registered' ) ) {
				wp_enqueue_style( $buttons['handle'] );
			}

			if ( wp_script_is( $buttons['handle'], 'registered' ) ) {
				wp_enqueue_script( $buttons['handle'] );
			}

			unset( $activity_params['buttons'][ $key_button ]['handle'] );
		}
	}

	// Activity Objects
	if ( ! sz_is_single_item() && ! sz_is_user() ) {
		$activity_objects = array(
			'profile' => array(
				'text'                     => __( 'Post in: Profile', 'sportszone' ),
				'autocomplete_placeholder' => '',
				'priority'                 => 5,
			),
		);

		// the groups component is active & the current user is at least a member of 1 group
		if ( sz_is_active( 'groups' ) && sz_has_groups( array( 'user_id' => sz_loggedin_user_id(), 'max' => 1 ) ) ) {
			$activity_objects['group'] = array(
				'text'                     => __( 'Post in: Group', 'sportszone' ),
				'autocomplete_placeholder' => __( 'Start typing the group name...', 'sportszone' ),
				'priority'                 => 10,
			);
		}

		/**
		 * Filters the activity objects to apply for localized javascript data.
		 *
		 * @since 3.0.0
		 *
		 * @param array $activity_objects Array of activity objects.
		 */
		$activity_params['objects'] = apply_filters( 'sz_nouveau_activity_objects', $activity_objects );
	}

	$activity_strings = array(
		'whatsnewPlaceholder' => sprintf( __( "What's new, %s?", 'sportszone' ), sz_get_user_firstname( $user_displayname ) ),
		'whatsnewLabel'       => __( 'Post what\'s new', 'sportszone' ),
		'whatsnewpostinLabel' => __( 'Post in', 'sportszone' ),
		'postUpdateButton'    => __( 'Post Update', 'sportszone' ),
		'cancelButton'        => __( 'Cancel', 'sportszone' ),
	);

	if ( sz_is_group() ) {
		$activity_params = array_merge(
			$activity_params,
			array(
				'object'  => 'group',
				'item_id' => sz_get_current_group_id(),
			)
		);
	}

	$params['activity'] = array(
		'params'  => $activity_params,
		'strings' => $activity_strings,
	);

	return $params;
}

/**
 * @since 3.0.0
 */
function sz_nouveau_get_activity_directory_nav_items() {
	$nav_items = array();

	$nav_items['all'] = array(
		'component' => 'activity',
		'slug'      => 'all', // slug is used because SZ_Core_Nav requires it, but it's the scope
		'li_class'  => array( 'dynamic' ),
		'link'      => sz_get_activity_directory_permalink(),
		'text'      => __( 'All Members', 'sportszone' ),
		'count'     => '',
		'position'  => 5,
	);

	// deprecated hooks
	$deprecated_hooks = array(
		array( 'sz_before_activity_type_tab_all', 'activity', 0 ),
		array( 'sz_activity_type_tabs', 'activity', 46 ),
	);

	if ( is_user_logged_in() ) {
		$deprecated_hooks = array_merge(
			$deprecated_hooks,
			array(
				array( 'sz_before_activity_type_tab_friends', 'activity', 6 ),
				array( 'sz_before_activity_type_tab_groups', 'activity', 16 ),
				array( 'sz_before_activity_type_tab_favorites', 'activity', 26 ),
			)
		);

		// If the user has favorite create a nav item
		if ( sz_get_total_favorite_count_for_user( sz_loggedin_user_id() ) ) {
			$nav_items['favorites'] = array(
				'component' => 'activity',
				'slug'      => 'favorites', // slug is used because SZ_Core_Nav requires it, but it's the scope
				'li_class'  => array(),
				'link'      => sz_loggedin_user_domain() . sz_get_activity_slug() . '/favorites/',
				'text'      => __( 'My Favorites', 'sportszone' ),
				'count'     => false,
				'position'  => 35,
			);
		}

		// The friends component is active and user has friends
		if ( sz_is_active( 'friends' ) && sz_get_total_friend_count( sz_loggedin_user_id() ) ) {
			$nav_items['friends'] = array(
				'component' => 'activity',
				'slug'      => 'friends', // slug is used because SZ_Core_Nav requires it, but it's the scope
				'li_class'  => array( 'dynamic' ),
				'link'      => sz_loggedin_user_domain() . sz_get_activity_slug() . '/' . sz_get_friends_slug() . '/',
				'text'      => __( 'My Friends', 'sportszone' ),
				'count'     => '',
				'position'  => 15,
			);
		}

		// The groups component is active and user has groups
		if ( sz_is_active( 'groups' ) && sz_get_total_group_count_for_user( sz_loggedin_user_id() ) ) {
			$nav_items['groups'] = array(
				'component' => 'activity',
				'slug'      => 'groups', // slug is used because SZ_Core_Nav requires it, but it's the scope
				'li_class'  => array( 'dynamic' ),
				'link'      => sz_loggedin_user_domain() . sz_get_activity_slug() . '/' . sz_get_groups_slug() . '/',
				'text'      => __( 'My Groups', 'sportszone' ),
				'count'     => '',
				'position'  => 25,
			);
		}

		// Mentions are allowed
		if ( sz_activity_do_mentions() ) {
			$deprecated_hooks[] = array( 'sz_before_activity_type_tab_mentions', 'activity', 36 );

			$count = '';
			if ( sz_get_total_mention_count_for_user( sz_loggedin_user_id() ) ) {
				$count = sz_get_total_mention_count_for_user( sz_loggedin_user_id() );
			}

			$nav_items['mentions'] = array(
				'component' => 'activity',
				'slug'      => 'mentions', // slug is used because SZ_Core_Nav requires it, but it's the scope
				'li_class'  => array( 'dynamic' ),
				'link'      => sz_loggedin_user_domain() . sz_get_activity_slug() . '/mentions/',
				'text'      => __( 'Mentions', 'sportszone' ),
				'count'     => $count,
				'position'  => 45,
			);
		}
	}

	// Check for deprecated hooks :
	foreach ( $deprecated_hooks as $deprectated_hook ) {
		list( $hook, $component, $position ) = $deprectated_hook;

		$extra_nav_items = sz_nouveau_parse_hooked_dir_nav( $hook, $component, $position );

		if ( ! empty( $extra_nav_items ) ) {
			$nav_items = array_merge( $nav_items, $extra_nav_items );
		}
	}

	/**
	 * Filters the activity directory navigation items.
	 *
	 * Use this filter to introduce your custom nav items for the activity directory.
	 *
	 * @since 3.0.0
	 *
	 * @param array $nav_items The list of the activity directory nav items.
	 */
	return apply_filters( 'sz_nouveau_get_activity_directory_nav_items', $nav_items );
}

/**
 * Make sure sz_get_activity_show_filters() will return the filters and the context
 * instead of the output.
 *
 * @since 3.0.0
 *
 * @param string $output  HTML output
 * @param array  $filters Optional.
 * @param string $context
 *
 * @return array
 */
function sz_nouveau_get_activity_filters_array( $output = '', $filters = array(), $context = '' ) {
	return array(
		'filters' => $filters,
		'context' => $context,
	);
}

/**
 * Get Dropdown filters of the activity component
 *
 * @since 3.0.0
 *
 * @return array the filters
 */
function sz_nouveau_get_activity_filters() {
	add_filter( 'sz_get_activity_show_filters', 'sz_nouveau_get_activity_filters_array', 10, 3 );

	$filters_data = sz_get_activity_show_filters();

	remove_filter( 'sz_get_activity_show_filters', 'sz_nouveau_get_activity_filters_array', 10, 3 );

	$action = '';
	if ( 'group' === $filters_data['context'] ) {
		$action = 'sz_group_activity_filter_options';
	} elseif ( 'member' === $filters_data['context'] || 'member_groups' === $filters_data['context'] ) {
		$action = 'sz_member_activity_filter_options';
	} else {
		$action = 'sz_activity_filter_options';
	}

	$filters = $filters_data['filters'];

	if ( $action ) {
		return sz_nouveau_parse_hooked_options( $action, $filters );
	}

	return $filters;
}

/**
 * @since 3.0.0
 */
function sz_nouveau_activity_secondary_avatars( $action, $activity ) {
	switch ( $activity->component ) {
		case 'groups':
		case 'friends':
			// Only insert avatar if one exists.
			if ( $secondary_avatar = sz_get_activity_secondary_avatar() ) {
				$reverse_content = strrev( $action );
				$position        = strpos( $reverse_content, 'a<' );
				$action          = substr_replace( $action, $secondary_avatar, -$position - 2, 0 );
			}
			break;
	}

	return $action;
}

/**
 * @since 3.0.0
 */
function sz_nouveau_activity_scope_newest_class( $classes = '' ) {
	if ( ! is_user_logged_in() ) {
		return $classes;
	}

	$user_id    = sz_loggedin_user_id();
	$my_classes = array();

	/*
	 * HeartBeat requests will transport the scope.
	 * See sz_nouveau_ajax_querystring().
	 */
	$scope = '';

	if ( ! empty( $_POST['data']['sz_heartbeat']['scope'] ) ) {
		$scope = sanitize_key( $_POST['data']['sz_heartbeat']['scope'] );
	}

	// Add specific classes to perform specific actions on the client side.
	if ( $scope && sz_is_activity_directory() ) {
		$component = sz_get_activity_object_name();

		/*
		 * These classes will be used to count the number of newest activities for
		 * the 'Mentions', 'My Groups' & 'My Friends' tabs
		 */
		if ( 'all' === $scope ) {
			if ( 'groups' === $component && sz_is_active( $component ) ) {
				// Is the current user a member of the group the activity is attached to?
				if ( groups_is_user_member( $user_id, sz_get_activity_item_id() ) ) {
					$my_classes[] = 'sz-my-groups';
				}
			}

			// Friends can post in groups the user is a member of
			if ( sz_is_active( 'friends' ) && (int) $user_id !== (int) sz_get_activity_user_id() ) {
				if ( friends_check_friendship( $user_id, sz_get_activity_user_id() ) ) {
					$my_classes[] = 'sz-my-friends';
				}
			}

			// A mention can be posted by a friend within a group
			if ( true === sz_activity_do_mentions() ) {
				$new_mentions = sz_get_user_meta( $user_id, 'sz_new_mentions', true );

				// The current activity is one of the new mentions
				if ( is_array( $new_mentions ) && in_array( sz_get_activity_id(), $new_mentions ) ) {
					$my_classes[] = 'sz-my-mentions';
				}
			}

		/*
		 * This class will be used to highlight the newest activities when
		 * viewing the 'Mentions', 'My Groups' or the 'My Friends' tabs
		 */
		} elseif ( 'friends' === $scope || 'groups' === $scope || 'mentions' === $scope ) {
			$my_classes[] = 'newest_' . $scope . '_activity';
		}

		// Leave other components do their specific stuff if needed.
		/**
		 * Filters the classes to be applied to the newest activity item.
		 *
		 * Leave other components do their specific stuff if needed.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $my_classes Array of classes to output to class attribute.
		 * @param string $scope      Current scope for the activity type.
		 */
		$my_classes = (array) apply_filters( 'sz_nouveau_activity_scope_newest_class', $my_classes, $scope );

		if ( ! empty( $my_classes ) ) {
			$classes .= ' ' . join( ' ', $my_classes );
		}
	}

	return $classes;
}

/**
 * Get the activity query args for the widget.
 *
 * @since 3.0.0
 *
 * @return array The activity arguments.
 */
function sz_nouveau_activity_widget_query() {
	$args       = array();
	$sz_nouveau = sz_nouveau();

	if ( isset( $sz_nouveau->activity->widget_args ) ) {
		$args = $sz_nouveau->activity->widget_args;
	}

	/**
	 * Filter to edit the activity widget arguments.
	 *
	 * @since 3.0.0
	 *
	 * @param array $args The activity arguments.
	 */
	return apply_filters( 'sz_nouveau_activity_widget_query', $args );
}

/**
 * Register notifications filters for the activity component.
 *
 * @since 3.0.0
 */
function sz_nouveau_activity_notification_filters() {
	$notifications = array(
		array(
			'id'       => 'new_at_mention',
			'label'    => __( 'New mentions', 'sportszone' ),
			'position' => 5,
		),
		array(
			'id'       => 'update_reply',
			'label'    => __( 'New update replies', 'sportszone' ),
			'position' => 15,
		),
		array(
			'id'       => 'comment_reply',
			'label'    => __( 'New update comment replies', 'sportszone' ),
			'position' => 25,
		),
	);

	foreach ( $notifications as $notification ) {
		sz_nouveau_notifications_register_filter( $notification );
	}
}

/**
 * Add controls for the settings of the customizer for the activity component.
 *
 * @since 3.0.0
 *
 * @param array $controls Optional. The controls to add.
 *
 * @return array the controls to add.
 */
function sz_nouveau_activity_customizer_controls( $controls = array() ) {
	return array_merge( $controls, array(
		'act_dir_layout' => array(
			'label'      => __( 'Use column navigation for the Activity directory.', 'sportszone' ),
			'section'    => 'sz_nouveau_dir_layout',
			'settings'   => 'sz_nouveau_appearance[activity_dir_layout]',
			'type'       => 'checkbox',
		),
		'act_dir_tabs' => array(
			'label'      => __( 'Use tab styling for Activity directory navigation.', 'sportszone' ),
			'section'    => 'sz_nouveau_dir_layout',
			'settings'   => 'sz_nouveau_appearance[activity_dir_tabs]',
			'type'       => 'checkbox',
		),
	) );
}
