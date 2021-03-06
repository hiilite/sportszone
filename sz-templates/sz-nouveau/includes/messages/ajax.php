<?php
/**
 * Messages Ajax functions
 *
 * @since 3.0.0
 * @version 3.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', function() {
	$ajax_actions = array(
		array( 'messages_send_message'             => array( 'function' => 'sz_nouveau_ajax_messages_send_message', 'nopriv' => false ) ),
		array( 'messages_send_reply'               => array( 'function' => 'sz_nouveau_ajax_messages_send_reply', 'nopriv' => false ) ),
		array( 'messages_get_user_message_threads' => array( 'function' => 'sz_nouveau_ajax_get_user_message_threads', 'nopriv' => false ) ),
		array( 'messages_thread_read'              => array( 'function' => 'sz_nouveau_ajax_messages_thread_read', 'nopriv' => false ) ),
		array( 'messages_get_thread_messages'      => array( 'function' => 'sz_nouveau_ajax_get_thread_messages', 'nopriv' => false ) ),
		array( 'messages_delete'                   => array( 'function' => 'sz_nouveau_ajax_delete_thread_messages', 'nopriv' => false ) ),
		array( 'messages_unstar'                   => array( 'function' => 'sz_nouveau_ajax_star_thread_messages', 'nopriv' => false ) ),
		array( 'messages_star'                     => array( 'function' => 'sz_nouveau_ajax_star_thread_messages', 'nopriv' => false ) ),
		array( 'messages_unread'                   => array( 'function' => 'sz_nouveau_ajax_readunread_thread_messages', 'nopriv' => false ) ),
		array( 'messages_read'                     => array( 'function' => 'sz_nouveau_ajax_readunread_thread_messages', 'nopriv' => false ) ),
		array( 'messages_dismiss_sitewide_notice'  => array( 'function' => 'sz_nouveau_ajax_dismiss_sitewide_notice', 'nopriv' => false ) ),
	);

	foreach ( $ajax_actions as $ajax_action ) {
		$action = key( $ajax_action );

		add_action( 'wp_ajax_' . $action, $ajax_action[ $action ]['function'] );

		if ( ! empty( $ajax_action[ $action ]['nopriv'] ) ) {
			add_action( 'wp_ajax_nopriv_' . $action, $ajax_action[ $action ]['function'] );
		}
	}
}, 12 );

/**
 * @since 3.0.0
 */
function sz_nouveau_ajax_messages_send_message() {
	$response = array(
		'feedback' => __( 'Your message could not be sent. Please try again.', 'sportszone' ),
		'type'     => 'error',
	);

	// Verify nonce
	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'messages_send_message' ) ) {
		wp_send_json_error( $response );
	}

	// Validate subject and message content
	if ( empty( $_POST['subject'] ) || empty( $_POST['message_content'] ) ) {
		if ( empty( $_POST['subject'] ) ) {
			$response['feedback'] = __( 'Your message was not sent. Please enter a subject line.', 'sportszone' );
		} else {
			$response['feedback'] = __( 'Your message was not sent. Please enter some content.', 'sportszone' );
		}

		wp_send_json_error( $response );
	}

	// Validate recipients
	if ( empty( $_POST['send_to'] ) || ! is_array( $_POST['send_to'] ) ) {
		$response['feedback'] = __( 'Your message was not sent. Please enter at least one username.', 'sportszone' );

		wp_send_json_error( $response );
	}

	// Trim @ from usernames
	/**
	 * Filters the results of trimming of `@` characters from usernames for who is set to receive a message.
	 *
	 * @since 3.0.0
	 *
	 * @param array $value Array of trimmed usernames.
	 * @param array $value Array of un-trimmed usernames submitted.
	 */
	$recipients = apply_filters( 'sz_messages_recipients', array_map( function( $username ) {
		return trim( $username, '@' );
	}, $_POST['send_to'] ) );

	// Attempt to send the message.
	$send = messages_new_message( array(
		'recipients' => $recipients,
		'subject'    => $_POST['subject'],
		'content'    => $_POST['message_content'],
		'error_type' => 'wp_error',
	) );

	// Send the message.
	if ( true === is_int( $send ) ) {
		wp_send_json_success( array(
			'feedback' => __( 'Message successfully sent.', 'sportszone' ),
			'type'     => 'success',
		) );

	// Message could not be sent.
	} else {
		$response['feedback'] = $send->get_error_message();

		wp_send_json_error( $response );
	}
}

/**
 * @since 3.0.0
 */
function sz_nouveau_ajax_messages_send_reply() {
	$response = array(
		'feedback' => __( 'There was a problem sending your reply. Please try again.', 'sportszone' ),
		'type'     => 'error',
	);

	// Verify nonce
	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'messages_send_message' ) ) {
		wp_send_json_error( $response );
	}

	if ( empty( $_POST['content'] ) || empty( $_POST['thread_id'] ) ) {
		$response['feedback'] = __( 'Your reply was not sent. Please enter some content.', 'sportszone' );

		wp_send_json_error( $response );
	}

	$new_reply = messages_new_message( array(
		'thread_id' => (int) $_POST['thread_id'],
		'subject'   => ! empty( $_POST['subject'] ) ? $_POST['subject'] : false,
		'content'   => $_POST['content']
	) );

	// Send the reply.
	if ( empty( $new_reply ) ) {
		wp_send_json_error( $response );
	}

	// Get the message by pretending we're in the message loop.
	global $thread_template;

	$sz           = sportszone();
	$reset_action = $sz->current_action;

	// Override sz_current_action().
	$sz->current_action = 'view';

	sz_thread_has_messages( array( 'thread_id' => (int) $_POST['thread_id'] ) );

	// Set the current message to the 2nd last.
	$thread_template->message = end( $thread_template->thread->messages );
	$thread_template->message = prev( $thread_template->thread->messages );

	// Set current message to current key.
	$thread_template->current_message = key( $thread_template->thread->messages );

	// Now manually iterate message like we're in the loop.
	sz_thread_the_message();

	// Manually call oEmbed
	// this is needed because we're not at the beginning of the loop.
	sz_messages_embed();

	// Output single message template part.
	$reply = array(
		'id'            => sz_get_the_thread_message_id(),
		'content'       => html_entity_decode( do_shortcode( sz_get_the_thread_message_content() ) ),
		'sender_id'     => sz_get_the_thread_message_sender_id(),
		'sender_name'   => esc_html( sz_get_the_thread_message_sender_name() ),
		'sender_link'   => sz_get_the_thread_message_sender_link(),
		'sender_avatar' => htmlspecialchars_decode( sz_core_fetch_avatar( array(
			'item_id' => sz_get_the_thread_message_sender_id(),
			'object'  => 'user',
			'type'    => 'thumb',
			'width'   => 32,
			'height'  => 32,
			'html'    => false,
		) ) ),
		'date'          => sz_get_the_thread_message_date_sent() * 1000,
		'display_date'  => sz_get_the_thread_message_time_since(),
	);

	if ( sz_is_active( 'messages', 'star' ) ) {
		$star_link = sz_get_the_message_star_action_link( array(
			'message_id' => sz_get_the_thread_message_id(),
			'url_only'  => true,
		) );

		$reply['star_link']  = $star_link;
		$reply['is_starred'] = array_search( 'unstar', explode( '/', $star_link ) );
	}

	$extra_content = sz_nouveau_messages_catch_hook_content( array(
		'beforeMeta'    => 'sz_before_message_meta',
		'afterMeta'     => 'sz_after_message_meta',
		'beforeContent' => 'sz_before_message_content',
		'afterContent'  => 'sz_after_message_content',
	) );

	if ( array_filter( $extra_content ) ) {
		$reply = array_merge( $reply, $extra_content );
	}

	// Clean up the loop.
	sz_thread_messages();

	// Remove the sz_current_action() override.
	$sz->current_action = $reset_action;

	wp_send_json_success( array(
		'messages' => array( $reply ),
		'feedback' => __( 'Your reply was sent successfully', 'sportszone' ),
		'type'     => 'success',
	) );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_ajax_get_user_message_threads() {
	global $messages_template;

	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'sz_nouveau_messages' ) ) {
		wp_send_json_error( array(
			'feedback' => __( 'Unauthorized request.', 'sportszone' ),
			'type'     => 'error'
		) );
	}

	$sz           = sportszone();
	$reset_action = $sz->current_action;

	// Override sz_current_action().
	if ( isset( $_POST['box'] ) ) {
		$sz->current_action = $_POST['box'];
	}

	// Add the message thread filter.
	if ( 'starred' === $sz->current_action ) {
		add_filter( 'sz_after_has_message_threads_parse_args', 'sz_messages_filter_starred_message_threads' );
	}

	// Simulate the loop.
	if ( ! sz_has_message_threads( sz_ajax_querystring( 'messages' ) ) ) {
		// Remove the sz_current_action() override.
		$sz->current_action = $reset_action;

		wp_send_json_error( array(
			'feedback' => __( 'Sorry, no messages were found.', 'sportszone' ),
			'type'     => 'info'
		) );
	}

	// remove the message thread filter.
	if ( 'starred' === $sz->current_action ) {
		remove_filter( 'sz_after_has_message_threads_parse_args', 'sz_messages_filter_starred_message_threads' );
	}

	$threads       = new stdClass;
	$threads->meta = array(
		'total_page' => ceil( (int) $messages_template->total_thread_count / (int) $messages_template->pag_num ),
		'page'       => $messages_template->pag_page,
	);

	$threads->threads = array();
	$i                = 0;

	while ( sz_message_threads() ) : sz_message_thread();
		$last_message_id = (int) $messages_template->thread->last_message_id;

		$threads->threads[ $i ] = array(
			'id'            => sz_get_message_thread_id(),
			'message_id'    => (int) $last_message_id,
			'subject'       => html_entity_decode( sz_get_message_thread_subject() ),
			'excerpt'       => html_entity_decode( sz_get_message_thread_excerpt() ),
			'content'       => html_entity_decode( do_shortcode( sz_get_message_thread_content() ) ),
			'unread'        => sz_message_thread_has_unread(),
			'sender_name'   => sz_core_get_user_displayname( $messages_template->thread->last_sender_id ),
			'sender_link'   => sz_core_get_userlink( $messages_template->thread->last_sender_id, false, true ),
			'sender_avatar' => htmlspecialchars_decode( sz_core_fetch_avatar( array(
				'item_id' => $messages_template->thread->last_sender_id,
				'object'  => 'user',
				'type'    => 'thumb',
				'width'   => 32,
				'height'  => 32,
				'html'    => false,
			) ) ),
			'count'         => sz_get_message_thread_total_count(),
			'date'          => strtotime( sz_get_message_thread_last_post_date_raw() ) * 1000,
			'display_date'  => sz_nouveau_get_message_date( sz_get_message_thread_last_post_date_raw() ),
		);

		if ( is_array( $messages_template->thread->recipients ) ) {
			foreach ( $messages_template->thread->recipients as $recipient ) {
				$threads->threads[ $i ]['recipients'][] = array(
					'avatar' => htmlspecialchars_decode( sz_core_fetch_avatar( array(
						'item_id' => $recipient->user_id,
						'object'  => 'user',
						'type'    => 'thumb',
						'width'   => 28,
						'height'  => 28,
						'html'    => false,
					) ) ),
					'user_link' => sz_core_get_userlink( $recipient->user_id, false, true ),
					'user_name' => sz_core_get_username( $recipient->user_id ),
				);
			}
		}

		if ( sz_is_active( 'messages', 'star' ) ) {
			$star_link = sz_get_the_message_star_action_link( array(
				'thread_id' => sz_get_message_thread_id(),
				'url_only'  => true,
			) );

			$threads->threads[ $i ]['star_link']  = $star_link;

			$star_link_data = explode( '/', $star_link );
			$threads->threads[ $i ]['is_starred'] = array_search( 'unstar', $star_link_data );

			// Defaults to last
			$sm_id = $last_message_id;

			if ( $threads->threads[ $i ]['is_starred'] ) {
				$sm_id = (int) $star_link_data[ $threads->threads[ $i ]['is_starred'] + 1 ];
			}

			$threads->threads[ $i ]['star_nonce'] = wp_create_nonce( 'sz-messages-star-' . $sm_id );
			$threads->threads[ $i ]['starred_id'] = $sm_id;
		}

		$thread_extra_content = sz_nouveau_messages_catch_hook_content( array(
			'inboxListItem' => 'sz_messages_inbox_list_item',
			'threadOptions' => 'sz_messages_thread_options',
		) );

		if ( array_filter( $thread_extra_content ) ) {
			$threads->threads[ $i ] = array_merge( $threads->threads[ $i ], $thread_extra_content );
		}

		$i += 1;
	endwhile;

	$threads->threads = array_filter( $threads->threads );

	$extra_content = sz_nouveau_messages_catch_hook_content( array(
		'beforeLoop' => 'sz_before_member_messages_loop',
		'afterLoop'  => 'sz_after_member_messages_loop',
	) );

	if ( array_filter( $extra_content ) ) {
		$threads->extraContent = $extra_content;
	}

	// Remove the sz_current_action() override.
	$sz->current_action = $reset_action;

	// Return the successfull reply.
	wp_send_json_success( $threads );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_ajax_messages_thread_read() {
	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'sz_nouveau_messages' ) ) {
		wp_send_json_error();
	}

	if ( empty( $_POST['id'] ) || empty( $_POST['message_id'] ) ) {
		wp_send_json_error();
	}

	$thread_id  = (int) $_POST['id'];
	$message_id = (int) $_POST['message_id'];

	if ( ! messages_is_valid_thread( $thread_id ) || ( ! messages_check_thread_access( $thread_id ) && ! sz_current_user_can( 'sz_moderate' ) ) ) {
		wp_send_json_error();
	}

	// Mark thread as read
	messages_mark_thread_read( $thread_id );

	// Mark latest message as read
	if ( sz_is_active( 'notifications' ) ) {
		sz_notifications_mark_notifications_by_item_id( sz_loggedin_user_id(), (int) $message_id, sportszone()->messages->id, 'new_message' );
	}

	wp_send_json_success();
}

/**
 * @since 3.0.0
 */
function sz_nouveau_ajax_get_thread_messages() {
	global $thread_template;

	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'sz_nouveau_messages' ) ) {
		wp_send_json_error( array(
			'feedback' => __( 'Unauthorized request.', 'sportszone' ),
			'type'     => 'error'
		) );
	}

	$response = array(
		'feedback' => __( 'Sorry, no messages were found.', 'sportszone' ),
		'type'     => 'info'
	);

	if ( empty( $_POST['id'] ) ) {
		wp_send_json_error( $response );
	}

	$thread_id    = (int) $_POST['id'];
	$sz           = sportszone();
	$reset_action = $sz->current_action;

	// Override sz_current_action().
	$sz->current_action = 'view';

	// Simulate the loop.
	if ( ! sz_thread_has_messages( array( 'thread_id' => $thread_id ) ) ) {
		// Remove the sz_current_action() override.
		$sz->current_action = $reset_action;

		wp_send_json_error( $response );
	}

	$thread = new stdClass;

	if ( empty( $_POST['js_thread'] ) ) {
		$thread->thread = array(
			'id'      => sz_get_the_thread_id(),
			'subject' => html_entity_decode( sz_get_the_thread_subject() ),
		);

		if ( is_array( $thread_template->thread->recipients ) ) {
			foreach ( $thread_template->thread->recipients as $recipient ) {
				$thread->thread['recipients'][] = array(
					'avatar' => htmlspecialchars_decode( sz_core_fetch_avatar( array(
						'item_id' => $recipient->user_id,
						'object'  => 'user',
						'type'    => 'thumb',
						'width'   => 28,
						'height'  => 28,
						'html'    => false,
					) ) ),
					'user_link' => sz_core_get_userlink( $recipient->user_id, false, true ),
					'user_name' => sz_core_get_username( $recipient->user_id ),
				);
			}
		}
	}

	$thread->messages = array();
	$i = 0;

	while ( sz_thread_messages() ) : sz_thread_the_message();
		$thread->messages[ $i ] = array(
			'id'            => sz_get_the_thread_message_id(),
			'content'       => html_entity_decode( do_shortcode( sz_get_the_thread_message_content() ) ),
			'sender_id'     => sz_get_the_thread_message_sender_id(),
			'sender_name'   => esc_html( sz_get_the_thread_message_sender_name() ),
			'sender_link'   => sz_get_the_thread_message_sender_link(),
			'sender_avatar' => htmlspecialchars_decode( sz_core_fetch_avatar( array(
				'item_id' => sz_get_the_thread_message_sender_id(),
				'object'  => 'user',
				'type'    => 'thumb',
				'width'   => 32,
				'height'  => 32,
				'html'    => false,
			) ) ),
			'date'          => sz_get_the_thread_message_date_sent() * 1000,
			'display_date'  => sz_get_the_thread_message_time_since(),
		);

		if ( sz_is_active( 'messages', 'star' ) ) {
			$star_link = sz_get_the_message_star_action_link( array(
				'message_id' => sz_get_the_thread_message_id(),
				'url_only'  => true,
			) );

			$thread->messages[ $i ]['star_link']  = $star_link;
			$thread->messages[ $i ]['is_starred'] = array_search( 'unstar', explode( '/', $star_link ) );
			$thread->messages[ $i ]['star_nonce'] = wp_create_nonce( 'sz-messages-star-' . sz_get_the_thread_message_id() );
		}

		$extra_content = sz_nouveau_messages_catch_hook_content( array(
			'beforeMeta'    => 'sz_before_message_meta',
			'afterMeta'     => 'sz_after_message_meta',
			'beforeContent' => 'sz_before_message_content',
			'afterContent'  => 'sz_after_message_content',
		) );

		if ( array_filter( $extra_content ) ) {
			$thread->messages[ $i ] = array_merge( $thread->messages[ $i ], $extra_content );
		}

		$i += 1;
	endwhile;

	$thread->messages = array_filter( $thread->messages );

	// Remove the sz_current_action() override.
	$sz->current_action = $reset_action;

	wp_send_json_success( $thread );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_ajax_delete_thread_messages() {
	$response = array(
		'feedback' => __( 'There was a problem deleting your messages. Please try again.', 'sportszone' ),
		'type'     => 'error',
	);

	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'sz_nouveau_messages' ) ) {
		wp_send_json_error( $response );
	}

	if ( empty( $_POST['id'] ) ) {
		wp_send_json_error( $response );
	}

	$thread_ids = wp_parse_id_list( $_POST['id'] );

	foreach ( $thread_ids as $thread_id ) {
		if ( ! messages_check_thread_access( $thread_id ) && ! sz_current_user_can( 'sz_moderate' ) ) {
			wp_send_json_error( $response );
		}

		messages_delete_thread( $thread_id );
	}

	wp_send_json_success( array(
		'feedback' => __( 'Messages deleted', 'sportszone' ),
		'type'     => 'success',
	) );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_ajax_star_thread_messages() {
	if ( empty( $_POST['action'] ) ) {
		wp_send_json_error();
	}

	$action = str_replace( 'messages_', '', $_POST['action'] );

	if ( 'star' === $action ) {
		$error_message = __( 'There was a problem starring your messages. Please try again.', 'sportszone' );
	} else {
		$error_message = __( 'There was a problem unstarring your messages. Please try agian.', 'sportszone' );
	}

	$response = array(
		'feedback' => esc_html( $error_message ),
		'type'     => 'error',
	);

	if ( false === sz_is_active( 'messages', 'star' ) || empty( $_POST['id'] ) ) {
		wp_send_json_error( $response );
	}

	// Check capability.
	if ( ! is_user_logged_in() || ! sz_core_can_edit_settings() ) {
		wp_send_json_error( $response );
	}

	$ids      = wp_parse_id_list( $_POST['id'] );
	$messages = array();

	// Use global nonce for bulk actions involving more than one id
	if ( 1 !== count( $ids ) ) {
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'sz_nouveau_messages' ) ) {
			wp_send_json_error( $response );
		}

		foreach ( $ids as $mid ) {
			if ( 'star' === $action ) {
				sz_messages_star_set_action( array(
					'action'     => 'star',
					'message_id' => $mid,
				) );
			} else {
				$thread_id = messages_get_message_thread_id( $mid );

				sz_messages_star_set_action( array(
					'action'    => 'unstar',
					'thread_id' => $thread_id,
					'bulk'      => true
				) );
			}

			$messages[ $mid ] = array(
				'star_link' => sz_get_the_message_star_action_link( array(
					'message_id' => $mid,
					'url_only'  => true,
				) ),
				'is_starred' => 'star' === $action,
			);
		}

	// Use global star nonce for bulk actions involving one id or regular action
	} else {
		$id = reset( $ids );

		if ( empty( $_POST['star_nonce'] ) || ! wp_verify_nonce( $_POST['star_nonce'], 'sz-messages-star-' . $id ) ) {
			wp_send_json_error( $response );
		}

		sz_messages_star_set_action( array(
			'action'     => $action,
			'message_id' => $id,
		) );

		$messages[ $id ] = array(
			'star_link' => sz_get_the_message_star_action_link( array(
				'message_id' => $id,
				'url_only'  => true,
			) ),
			'is_starred' => 'star' === $action,
		);
	}

	if ( 'star' === $action ) {
		$success_message = __( 'Messages successfully starred.', 'sportszone' );
	} else {
		$success_message = __( 'Messages successfully unstarred.', 'sportszone' );
	}

	wp_send_json_success( array(
		'feedback' => esc_html( $success_message ),
		'type'     => 'success',
		'messages' => $messages,
	) );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_ajax_readunread_thread_messages() {
	if ( empty( $_POST['action'] ) ) {
		wp_send_json_error();
	}

	$action = str_replace( 'messages_', '', $_POST['action'] );

	$response = array(
		'feedback' => __( 'There was a problem marking your messages as read. Please try again.', 'sportszone' ),
		'type'     => 'error',
	);

	if ( 'unread' === $action ) {
		$response = array(
			'feedback' => __( 'There was a problem marking your messages as unread. Please try again.', 'sportszone' ),
			'type'     => 'error',
		);
	}

	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'sz_nouveau_messages' ) ) {
		wp_send_json_error( $response );
	}

	if ( empty( $_POST['id'] ) ) {
		wp_send_json_error( $response );
	}

	$thread_ids = wp_parse_id_list( $_POST['id'] );

	$response['messages'] = array();

	if ( 'unread' === $action ) {
		$response['feedback'] = __( 'Messages marked as unread.', 'sportszone' );
	} else {
		$response['feedback'] = __( 'Messages marked as read.', 'sportszone' );
	}

	foreach ( $thread_ids as $thread_id ) {
		if ( ! messages_check_thread_access( $thread_id ) && ! sz_current_user_can( 'sz_moderate' ) ) {
			wp_send_json_error( $response );
		}

		if ( 'unread' === $action ) {
			// Mark unread
			messages_mark_thread_unread( $thread_id );
		} else {
			// Mark read
			messages_mark_thread_read( $thread_id );
		}

		$response['messages'][ $thread_id ] = array(
			'unread' => 'unread' === $action,
		);
	}

	$response['type'] = 'success';

	wp_send_json_success( $response );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_ajax_dismiss_sitewide_notice() {
	if ( empty( $_POST['action'] ) ) {
		wp_send_json_error();
	}

	$response = array(
		'feedback' => '<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>' . __( 'There was a problem dismissing the notice. Please try again.', 'sportszone' ) . '</p></div>',
		'type'     => 'error',
	);

	if ( false === sz_is_active( 'messages' ) ) {
		wp_send_json_error( $response );
	}

	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'sz_nouveau_messages' ) ) {
		wp_send_json_error( $response );
	}

	// Check capability.
	if ( ! is_user_logged_in() || ! sz_core_can_edit_settings() ) {
		wp_send_json_error( $response );
	}

	// Mark the active notice as closed.
	$notice = SZ_Messages_Notice::get_active();

	if ( ! empty( $notice->id ) ) {
		$user_id = sz_loggedin_user_id();

		$closed_notices = sz_get_user_meta( $user_id, 'closed_notices', true );

		if ( empty( $closed_notices ) ) {
			$closed_notices = array();
		}

		// Add the notice to the array of the user's closed notices.
		$closed_notices[] = (int) $notice->id;
		sz_update_user_meta( $user_id, 'closed_notices', array_map( 'absint', array_unique( $closed_notices ) ) );

		wp_send_json_success( array(
			'feedback' => '<div class="sz-feedback info"><span class="sz-icon" aria-hidden="true"></span><p>' . __( 'Sitewide notice dismissed', 'sportszone' ) . '</p></div>',
			'type'     => 'success',
		) );
	}
}
