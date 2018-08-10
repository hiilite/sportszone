<?php
/**
 * Messages functions
 *
 * @since 3.0.0
 * @version 3.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Enqueue styles for the Messages UI (mentions).
 *
 * @since 3.0.0
 *
 * @param array $styles Optional. The array of styles to enqueue.
 *
 * @return array The same array with the specific messages styles.
 */
function sz_nouveau_messages_enqueue_styles( $styles = array() ) {
	if ( ! sz_is_user_messages() ) {
		return $styles;
	}

	return array_merge( $styles, array(
		'sz-nouveau-messages-at' => array(
			'file'         => sportszone()->plugin_url . 'sz-activity/css/mentions%1$s%2$s.css',
			'dependencies' => array( 'sz-nouveau' ),
			'version'      => sz_get_version(),
		),
	) );
}

/**
 * Register Scripts for the Messages component
 *
 * @since 3.0.0
 *
 * @param array $scripts The array of scripts to register
 *
 * @return array The same array with the specific messages scripts.
 */
function sz_nouveau_messages_register_scripts( $scripts = array() ) {
	if ( ! isset( $scripts['sz-nouveau'] ) ) {
		return $scripts;
	}

	return array_merge( $scripts, array(
		'sz-nouveau-messages-at' => array(
			'file'         => sportszone()->plugin_url . 'sz-activity/js/mentions%s.js',
			'dependencies' => array( 'sz-nouveau', 'jquery', 'jquery-atwho' ),
			'version'      => sz_get_version(),
			'footer'       => true,
		),
		'sz-nouveau-messages' => array(
			'file'         => 'js/sportszone-messages%s.js',
			'dependencies' => array( 'sz-nouveau', 'json2', 'wp-backbone', 'sz-nouveau-messages-at' ),
			'footer'       => true,
		),
	) );
}

/**
 * Enqueue the messages scripts
 *
 * @since 3.0.0
 */
function sz_nouveau_messages_enqueue_scripts() {
	if ( ! sz_is_user_messages() ) {
		return;
	}

	wp_enqueue_script( 'sz-nouveau-messages' );

	// Add The tiny MCE init specific function.
	add_filter( 'tiny_mce_before_init', 'sz_nouveau_messages_at_on_tinymce_init', 10, 2 );
}

/**
 * Localize the strings needed for the messages UI
 *
 * @since 3.0.0
 *
 * @param  array $params Associative array containing the JS Strings needed by scripts
 * @return array         The same array with specific strings for the messages UI if needed.
 */
function sz_nouveau_messages_localize_scripts( $params = array() ) {
	if ( ! sz_is_user_messages() ) {
		return $params;
	}

	$params['messages'] = array(
		'errors' => array(
			'send_to'         => __( 'Please add at least one recipient.', 'sportszone' ),
			'subject'         => __( 'Please add a subject to your message.', 'sportszone' ),
			'message_content' => __( 'Please add some content to your message.', 'sportszone' ),
		),
		'nonces' => array(
			'send' => wp_create_nonce( 'messages_send_message' ),
		),
		'loading'       => __( 'Loading messages. Please wait.', 'sportszone' ),
		'doingAction'   => array(
			'read'   => __( 'Marking messages as read. Please wait.', 'sportszone' ),
			'unread' => __( 'Marking messages as unread. Please wait.', 'sportszone' ),
			'delete' => __( 'Deleting messages. Please wait.', 'sportszone' ),
			'star'   => __( 'Starring messages. Please wait.', 'sportszone' ),
			'unstar' => __( 'Unstarring messages. Please wait.', 'sportszone' ),
		),
		'bulk_actions'  => sz_nouveau_messages_get_bulk_actions(),
		'howto'         => __( 'Click on the message title to preview it in the Active conversation box below.', 'sportszone' ),
		'howtoBulk'     => __( 'Use the select box to define your bulk action and click on the &#10003; button to apply.', 'sportszone' ),
		'toOthers'      => array(
			'one'  => __( '(and 1 other)', 'sportszone' ),
			'more' => __( '(and %d others)', 'sportszone' ),
		),
		'rootUrl' => parse_url( trailingslashit( sz_displayed_user_domain() . sz_get_messages_slug() ), PHP_URL_PATH ),
	);

	// Star private messages.
	if ( sz_is_active( 'messages', 'star' ) ) {
		$params['messages'] = array_merge( $params['messages'], array(
			'strings' => array(
				'text_unstar'  => __( 'Unstar', 'sportszone' ),
				'text_star'    => __( 'Star', 'sportszone' ),
				'title_unstar' => __( 'Starred', 'sportszone' ),
				'title_star'   => __( 'Not starred', 'sportszone' ),
				'title_unstar_thread' => __( 'Remove all starred messages in this thread', 'sportszone' ),
				'title_star_thread'   => __( 'Star the first message in this thread', 'sportszone' ),
			),
			'is_single_thread' => (int) sz_is_messages_conversation(),
			'star_counter'     => 0,
			'unstar_counter'   => 0
		) );
	}

	return $params;
}

/**
 * @since 3.0.0
 */
function sz_nouveau_message_search_form() {
	$query_arg   = sz_core_get_component_search_query_arg( 'messages' );
	$placeholder = sz_get_search_default_text( 'messages' );

	$search_form_html = '<form action="" method="get" id="search-messages-form">
		<label for="messages_search"><input type="text" name="' . esc_attr( $query_arg ) . '" id="messages_search" placeholder="' . esc_attr( $placeholder ) . '" /></label>
		<input type="submit" id="messages_search_submit" name="messages_search_submit" value="' . esc_attr_x( 'Search', 'button', 'sportszone' ) . '" />
	</form>';

	/**
	 * Filters the private message component search form.
	 *
	 * @since 3.0.0
	 *
	 * @param string $search_form_html HTML markup for the message search form.
	 */
	echo apply_filters( 'sz_nouveau_message_search_form', $search_form_html );
}
add_filter( 'sz_message_search_form', 'sz_nouveau_message_search_form', 10, 1 );

/**
 * @since 3.0.0
 */
function sz_nouveau_messages_adjust_nav() {
	$sz = sportszone();

	$secondary_nav_items = $sz->members->nav->get_secondary( array( 'parent_slug' => sz_get_messages_slug() ), false );

	if ( empty( $secondary_nav_items ) ) {
		return;
	}

	foreach ( $secondary_nav_items as $secondary_nav_item ) {
		if ( empty( $secondary_nav_item->slug ) ) {
			continue;
		}

		if ( 'notices' === $secondary_nav_item->slug ) {
			sz_core_remove_subnav_item( sz_get_messages_slug(), $secondary_nav_item->slug, 'members' );
		} elseif ( 'compose' === $secondary_nav_item->slug ) {
			$sz->members->nav->edit_nav( array(
				'user_has_access' => sz_is_my_profile()
			), $secondary_nav_item->slug, sz_get_messages_slug() );
		}
	}
}

/**
 * @since 3.0.0
 */
function sz_nouveau_messages_adjust_admin_nav( $admin_nav ) {
	if ( empty( $admin_nav ) ) {
		return $admin_nav;
	}

	$user_messages_link = trailingslashit( sz_loggedin_user_domain() . sz_get_messages_slug() );

	foreach ( $admin_nav as $nav_iterator => $nav ) {
		$nav_id = str_replace( 'my-account-messages-', '', $nav['id'] );

		if ( 'notices' === $nav_id ) {
			$admin_nav[ $nav_iterator ]['href'] = esc_url( add_query_arg( array(
				'page' => 'sz-notices'
			), sz_get_admin_url( 'users.php' ) ) );
		}
	}

	return $admin_nav;
}

/**
 * @since 3.0.0
 */
function sz_nouveau_add_notice_notification_for_user( $notifications, $user_id ) {
	if ( ! sz_is_active( 'messages' ) || ! doing_action( 'admin_bar_menu' ) ) {
		return $notifications;
	}

	$notice = SZ_Messages_Notice::get_active();
	if ( empty( $notice->id ) ) {
		return $notifications;
	}

	$closed_notices = sz_get_user_meta( $user_id, 'closed_notices', true );
	if ( empty( $closed_notices ) ) {
		$closed_notices = array();
	}

	if ( in_array( $notice->id, $closed_notices, true ) ) {
		return $notifications;
	}

	$notice_notification                    = new stdClass;
	$notice_notification->id                = 0;
	$notice_notification->user_id           = $user_id;
	$notice_notification->item_id           = $notice->id;
	$notice_notification->secondary_item_id = '';
	$notice_notification->component_name    = 'messages';
	$notice_notification->component_action  = 'new_notice';
	$notice_notification->date_notified     = $notice->date_sent;
	$notice_notification->is_new            = '1';

	return array_merge( $notifications, array( $notice_notification ) );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_format_notice_notification_for_user( $array ) {
	if ( ! empty( $array['text'] ) || ! doing_action( 'admin_bar_menu' ) ) {
		return $array;
	}

	return array(
		'text' => __( 'New sitewide notice', 'sportszone' ),
		'link' => sz_loggedin_user_domain(),
	);
}

/**
 * @since 3.0.0
 */
function sz_nouveau_unregister_notices_widget() {
	unregister_widget( 'SZ_Messages_Sitewide_Notices_Widget' );
}

/**
 * Add active sitewide notices to the BP template_message global.
 *
 * @since 3.0.0
 */
function sz_nouveau_push_sitewide_notices() {
	// Do not show notices if user is not logged in.
	if ( ! is_user_logged_in() || ! sz_is_my_profile() ) {
		return;
	}

	$notice = SZ_Messages_Notice::get_active();
	if ( empty( $notice ) ) {
		return;
	}

	$user_id = sz_loggedin_user_id();

	$closed_notices = sz_get_user_meta( $user_id, 'closed_notices', true );
	if ( empty( $closed_notices ) ) {
		$closed_notices = array();
	}

	if ( $notice->id && is_array( $closed_notices ) && ! in_array( $notice->id, $closed_notices ) ) {
		// Inject the notice into the template_message if no other message has priority.
		$sz = sportszone();

		if ( empty( $sz->template_message ) ) {
			$message = sprintf(
				'<strong class="subject">%s</strong>
				%s',
				stripslashes( $notice->subject ),
				stripslashes( $notice->message )
			);
			$sz->template_message      = $message;
			$sz->template_message_type = 'sz-sitewide-notice';
		}
	}
}

/**
 * Disable the WP Editor buttons not allowed in messages content.
 *
 * @since 3.0.0
 *
 * @param array $buttons The WP Editor buttons list.
 * @param array          The filtered WP Editor buttons list.
 */
function sz_nouveau_messages_mce_buttons( $buttons = array() ) {
	$remove_buttons = array(
		'wp_more',
		'spellchecker',
		'wp_adv',
		'fullscreen',
		'alignleft',
		'alignright',
		'aligncenter',
		'formatselect',
	);

	// Remove unused buttons
	$buttons = array_diff( $buttons, $remove_buttons );

	// Add the image button
	array_push( $buttons, 'image' );

	return $buttons;
}

/**
 * @since 3.0.0
 */
function sz_nouveau_messages_at_on_tinymce_init( $settings, $editor_id ) {
	// We only apply the mentions init to the visual post editor in the WP dashboard.
	if ( 'message_content' === $editor_id ) {
		$settings['init_instance_callback'] = 'window.bp.Nouveau.Messages.tinyMCEinit';
	}

	return $settings;
}

/**
 * @since 3.0.0
 */
function sz_nouveau_get_message_date( $date ) {
	$now  = sz_core_current_time( true, 'timestamp' );
	$date = strtotime( $date );

	$now_date    = getdate( $now );
	$date_date   = getdate( $date );
	$compare     = array_diff( $date_date, $now_date );
	$date_format = 'Y/m/d';

	// Use Timezone string if set.
	$timezone_string = sz_get_option( 'timezone_string' );
	if ( ! empty( $timezone_string ) ) {
		$timezone_object = timezone_open( $timezone_string );
		$datetime_object = date_create( "@{$date}" );
		$timezone_offset = timezone_offset_get( $timezone_object, $datetime_object ) / HOUR_IN_SECONDS;

	// Fall back on less reliable gmt_offset
	} else {
		$timezone_offset = sz_get_option( 'gmt_offset' );
	}

	// Calculate time based on the offset
	$calculated_time = $date + ( $timezone_offset * HOUR_IN_SECONDS );

	if ( empty( $compare['mday'] ) && empty( $compare['mon'] ) && empty( $compare['year'] ) ) {
		$date_format = 'H:i';

	} elseif ( empty( $compare['mon'] ) || empty( $compare['year'] ) ) {
		$date_format = 'M j';
	}

	/**
	 * Filters the message date for SportsZone Nouveau display.
	 *
	 * @since 3.0.0
	 *
	 * @param string $value           Internationalization-ready formatted date value.
	 * @param mixed  $calculated_time Calculated time.
	 * @param string $date            Date value.
	 * @param string $date_format     Format to convert the calcuated date to.
	 */
	return apply_filters( 'sz_nouveau_get_message_date', date_i18n( $date_format, $calculated_time, true ), $calculated_time, $date, $date_format );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_messages_get_bulk_actions() {
	ob_start();
	sz_messages_bulk_management_dropdown();

	$bulk_actions = array();
	$bulk_options = ob_get_clean();

	$matched = preg_match_all( '/<option value="(.*?)"\s*>(.*?)<\/option>/', $bulk_options, $matches, PREG_SET_ORDER );

	if ( $matched && is_array( $matches ) ) {
		foreach ( $matches as $i => $match ) {
			if ( 0 === $i ) {
				continue;
			}

			if ( isset( $match[1] ) && isset( $match[2] ) ) {
				$bulk_actions[] = array(
					'value' => trim( $match[1] ),
					'label' => trim( $match[2] ),
				);
			}
		}
	}

	return $bulk_actions;
}

/**
 * Register notifications filters for the messages component.
 *
 * @since 3.0.0
 */
function sz_nouveau_messages_notification_filters() {
	sz_nouveau_notifications_register_filter(
		array(
			'id'       => 'new_message',
			'label'    => __( 'New private messages', 'sportszone' ),
			'position' => 115,
		)
	);
}

/**
 * Fires Messages Legacy hooks to catch the content and add them
 * as extra keys to the JSON Messages UI reply.
 *
 * @since 3.0.1
 *
 * @param array $hooks The list of hooks to fire.
 * @return array       An associative containing the caught content.
 */
function sz_nouveau_messages_catch_hook_content( $hooks = array() ) {
	$content = array();

	ob_start();
	foreach ( $hooks as $js_key => $hook ) {
		if ( ! has_action( $hook ) ) {
			continue;
		}

		// Fire the hook.
		do_action( $hook );

		// Catch the sanitized content.
		$content[ $js_key ] = sz_strip_script_and_style_tags( ob_get_contents() );

		// Clean the buffer.
		ob_clean();
	}
	ob_end_clean();

	return $content;
}
