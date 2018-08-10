<?php
/**
 * Groups Ajax functions
 *
 * @since 3.0.0
 * @version 3.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', function() {
	$ajax_actions = array(
		array( 'groups_filter'                      => array( 'function' => 'sz_nouveau_ajax_object_template_loader', 'nopriv' => true  ) ),
		array( 'groups_join_group'                  => array( 'function' => 'sz_nouveau_ajax_joinleave_group', 'nopriv' => false ) ),
		array( 'groups_leave_group'                 => array( 'function' => 'sz_nouveau_ajax_joinleave_group', 'nopriv' => false ) ),
		array( 'groups_accept_invite'               => array( 'function' => 'sz_nouveau_ajax_joinleave_group', 'nopriv' => false ) ),
		array( 'groups_reject_invite'               => array( 'function' => 'sz_nouveau_ajax_joinleave_group', 'nopriv' => false ) ),
		array( 'groups_request_membership'          => array( 'function' => 'sz_nouveau_ajax_joinleave_group', 'nopriv' => false ) ),
		array( 'groups_get_group_potential_invites' => array( 'function' => 'sz_nouveau_ajax_get_users_to_invite', 'nopriv' => false ) ),
		array( 'groups_send_group_invites'          => array( 'function' => 'sz_nouveau_ajax_send_group_invites', 'nopriv' => false ) ),
		array( 'groups_delete_group_invite'         => array( 'function' => 'sz_nouveau_ajax_remove_group_invite', 'nopriv' => false ) ),
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
 * Join or leave a group when clicking the "join/leave" button via a POST request.
 *
 * @since 3.0.0
 *
 * @return string HTML
 */
function sz_nouveau_ajax_joinleave_group() {
	$response = array(
		'feedback' => sprintf(
			'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
			esc_html__( 'There was a problem performing this action. Please try again.', 'sportszone' )
		),
	);

	// Bail if not a POST action.
	if ( ! sz_is_post_request() || empty( $_POST['action'] ) ) {
		wp_send_json_error( $response );
	}

	if ( empty( $_POST['nonce'] ) || empty( $_POST['item_id'] ) || ! sz_is_active( 'groups' ) ) {
		wp_send_json_error( $response );
	}

	// Use default nonce
	$nonce = $_POST['nonce'];
	$check = 'sz_nouveau_groups';

	// Use a specific one for actions needed it
	if ( ! empty( $_POST['_wpnonce'] ) && ! empty( $_POST['action'] ) ) {
		$nonce = $_POST['_wpnonce'];
		$check = $_POST['action'];
	}

	// Nonce check!
	if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, $check ) ) {
		wp_send_json_error( $response );
	}

	// Cast gid as integer.
	$group_id = (int) $_POST['item_id'];

	$errors = array(
		'cannot' => sprintf( '<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>', esc_html__( 'You cannot join this group.', 'sportszone' ) ),
		'member' => sprintf( '<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>', esc_html__( 'You are already a member of the group.', 'sportszone' ) ),
	);

	if ( groups_is_user_banned( sz_loggedin_user_id(), $group_id ) ) {
		$response['feedback'] = $errors['cannot'];

		wp_send_json_error( $response );
	}

	// Validate and get the group
	$group = groups_get_group( array( 'group_id' => $group_id ) );

	if ( empty( $group->id ) ) {
		wp_send_json_error( $response );
	}

	// Manage all button's possible actions here.
	switch ( $_POST['action'] ) {

		case 'groups_accept_invite':
			if ( ! groups_accept_invite( sz_loggedin_user_id(), $group_id ) ) {
				$response = array(
					'feedback' => sprintf(
						'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
						esc_html__( 'Group invitation could not be accepted.', 'sportszone' )
					),
					'type'     => 'error',
				);

			} else {
				if ( sz_is_active( 'activity' ) ) {
					groups_record_activity(
						array(
							'type'    => 'joined_group',
							'item_id' => $group->id,
						)
					);
				}

				// User is now a member of the group
				$group->is_member = '1';

				$response = array(
					'feedback' => sprintf(
						'<div class="sz-feedback success"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
						esc_html__( 'Group invite accepted.', 'sportszone' )
					),
					'type'     => 'success',
					'is_user'  => sz_is_user(),
					'contents' => sz_get_group_join_button( $group ),
					'is_group' => sz_is_group(),
				);
			}
			break;

		case 'groups_reject_invite':
			if ( ! groups_reject_invite( sz_loggedin_user_id(), $group_id ) ) {
				$response = array(
					'feedback' => sprintf(
						'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
						esc_html__( 'Group invite could not be rejected', 'sportszone' )
					),
					'type'     => 'error',
				);
			} else {
				$response = array(
					'feedback' => sprintf(
						'<div class="sz-feedback success"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
						esc_html__( 'Group invite rejected', 'sportszone' )
					),
					'type'     => 'success',
					'is_user'  => sz_is_user(),
				);
			}
			break;

		case 'groups_join_group':
			if ( groups_is_user_member( sz_loggedin_user_id(), $group->id ) ) {
				$response = array(
					'feedback' => $errors['member'],
					'type'     => 'error',
				);
			} elseif ( 'public' !== $group->status ) {
				$response = array(
					'feedback' => $errors['cannot'],
					'type'     => 'error',
				);
			} elseif ( ! groups_join_group( $group->id ) ) {
				$response = array(
					'feedback' => sprintf(
						'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
						esc_html__( 'Error joining this group.', 'sportszone' )
					),
					'type'     => 'error',
				);
			} else {
				// User is now a member of the group
				$group->is_member = '1';

				$response = array(
					'contents' => sz_get_group_join_button( $group ),
					'is_group' => sz_is_group(),
					'type'     => 'success',
				);
			}
			break;

			case 'groups_request_membership' :
				if ( ! groups_send_membership_request( sz_loggedin_user_id(), $group->id ) ) {
					$response = array(
						'feedback' => sprintf(
							'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
							esc_html__( 'Error requesting membership.', 'sportszone' )
						),
						'type'     => 'error',
					);
				} else {
					// Request is pending
					$group->is_pending = '1';

					$response = array(
						'contents' => sz_get_group_join_button( $group ),
						'is_group' => sz_is_group(),
						'type'     => 'success',
					);
				}
				break;

			case 'groups_leave_group' :
				if (  groups_leave_group( $group->id ) ) {
					$response = array(
						'feedback' => sprintf(
							'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
							esc_html__( 'Error leaving group.', 'sportszone' )
						),
						'type'     => 'error',
					);
				} else {
					// User is no more a member of the group
					$group->is_member = '0';
					$sz               = sportszone();

					/**
					 * When inside the group or in the loggedin user's group memberships screen
					 * we need to reload the page.
					 */
					$sz_is_group = sz_is_group() || ( sz_is_user_groups() && sz_is_my_profile() );

					$response = array(
						'contents' => sz_get_group_join_button( $group ),
						'is_group' => $sz_is_group,
						'type'     => 'success',
					);

					// Reset the message if not in a Group or in a loggedin user's group memberships one!
					if ( ! $sz_is_group && isset( $sz->template_message ) && isset( $sz->template_message_type ) ) {
						unset( $sz->template_message, $sz->template_message_type );

						@setcookie( 'sz-message', false, time() - 1000, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
						@setcookie( 'sz-message-type', false, time() - 1000, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
					}
				}
				break;
	}

	if ( 'error' === $response['type'] ) {
		wp_send_json_error( $response );
	}

	wp_send_json_success( $response );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_ajax_get_users_to_invite() {
	$sz = sportszone();

	$response = array(
		'feedback' => __( 'There was a problem performing this action. Please try again.', 'sportszone' ),
		'type'     => 'error',
	);

	if ( empty( $_POST['nonce'] ) ) {
		wp_send_json_error( $response );
	}

	// Use default nonce
	$nonce = $_POST['nonce'];
	$check = 'sz_nouveau_groups';

	// Use a specific one for actions needed it
	if ( ! empty( $_POST['_wpnonce'] ) && ! empty( $_POST['action'] ) ) {
		$nonce = $_POST['_wpnonce'];
		$check = $_POST['action'];
	}

	// Nonce check!
	if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, $check ) ) {
		wp_send_json_error( $response );
	}

	$request = wp_parse_args( $_POST, array(
		'scope' => 'members',
	) );

	$sz->groups->invites_scope = 'members';
	$message = __( 'Select members to invite by clicking the + button. Once you\'ve made your selection, use the "Send Invites" navigation item to continue.', 'sportszone' );

	if ( 'friends' === $request['scope'] ) {
		$request['user_id'] = sz_loggedin_user_id();
		$sz->groups->invites_scope = 'friends';
		$message = __( 'Select friends to invite by clicking the + button. Once you\'ve made your selection, use the "Send Invites" navigation item to continue.', 'sportszone' );
	}

	if ( 'invited' === $request['scope'] ) {

		if ( ! sz_group_has_invites( array( 'user_id' => 'any' ) ) ) {
			wp_send_json_error( array(
				'feedback' => __( 'No pending group invitations found.', 'sportszone' ),
				'type'     => 'info',
			) );
		}

		$request['is_confirmed'] = false;
		$sz->groups->invites_scope = 'invited';
		$message = __( 'You can view the group\'s pending invitations from this screen.', 'sportszone' );
	}

	$potential_invites = sz_nouveau_get_group_potential_invites( $request );

	if ( empty( $potential_invites->users ) ) {
		$error = array(
			'feedback' => __( 'No members were found. Try another filter.', 'sportszone' ),
			'type'     => 'info',
		);

		if ( 'friends' === $sz->groups->invites_scope ) {
			$error = array(
				'feedback' => __( 'All your friends are already members of this group, or have already received an invite to join this group, or have requested to join it.', 'sportszone' ),
				'type'     => 'info',
			);

			if ( 0 === (int) sz_get_total_friend_count( sz_loggedin_user_id() ) ) {
				$error = array(
					'feedback' => __( 'You have no friends!', 'sportszone' ),
					'type'     => 'info',
				);
			}
		}

		unset( $sz->groups->invites_scope );

		wp_send_json_error( $error );
	}

	$potential_invites->users = array_map( 'sz_nouveau_prepare_group_potential_invites_for_js', array_values( $potential_invites->users ) );
	$potential_invites->users = array_filter( $potential_invites->users );

	// Set a message to explain use of the current scope
	$potential_invites->feedback = $message;

	unset( $sz->groups->invites_scope );

	wp_send_json_success( $potential_invites );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_ajax_send_group_invites() {
	$sz = sportszone();

	$response = array(
		'feedback' => __( 'Invites could not be sent. Please try again.', 'sportszone' ),
		'type'     => 'error',
	);

	// Verify nonce
	if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'groups_send_invites' ) ) {
		wp_send_json_error( $response );
	}

	$group_id = sz_get_current_group_id();

	if ( sz_is_group_create() && ! empty( $_POST['group_id'] ) ) {
		$group_id = (int) $_POST['group_id'];
	}

	if ( ! sz_groups_user_can_send_invites( $group_id ) ) {
		$response['feedback'] = __( 'You are not allowed to send invitations for this group.', 'sportszone' );
		wp_send_json_error( $response );
	}

	if ( empty( $_POST['users'] ) ) {
		wp_send_json_error( $response );
	}

	// For feedback
	$invited = array();

	foreach ( (array) $_POST['users'] as $user_id ) {
		$invited[ (int) $user_id ] = groups_invite_user(
			array(
				'user_id'  => $user_id,
				'group_id' => $group_id,
			)
		);
	}

	if ( ! empty( $_POST['message'] ) ) {
		$sz->groups->invites_message = wp_kses( wp_unslash( $_POST['message'] ), array() );

		add_filter( 'groups_notification_group_invites_message', 'sz_nouveau_groups_invites_custom_message', 10, 1 );
	}

	// Send the invites.
	groups_send_invites( sz_loggedin_user_id(), $group_id );

	if ( ! empty( $_POST['message'] ) ) {
		unset( $sz->groups->invites_message );

		remove_filter( 'groups_notification_group_invites_message', 'sz_nouveau_groups_invites_custom_message', 10, 1 );
	}

	if ( array_search( false, $invited ) ) {
		$errors = array_keys( $invited, false );

		$error_count   = count( $errors );
		$error_message = sprintf(
			/* translators: count of users affected */
			_n(
				'Invitation failed for %s user.',
				'Invitation failed for %s users.',
				$error_count, 'sportszone'
			),
			number_format_i18n( $error_count )
		);

		wp_send_json_error(
			array(
				'feedback' => $error_message,
				'users'    => $errors,
				'type'     => 'error',
			)
		);
	}

	wp_send_json_success(
		array(
			'feedback' => __( 'Invitations sent.', 'sportszone' ),
			'type'     => 'success',
		)
	);
}

/**
 * @since 3.0.0
 */
function sz_nouveau_ajax_remove_group_invite() {
	$user_id  = (int) $_POST['user'];
	$group_id = sz_get_current_group_id();

	// Verify nonce
	if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'groups_invite_uninvite_user' ) ) {
		wp_send_json_error(
			array(
				'feedback' => __( 'Group invitation could not be removed.', 'sportszone' ),
				'type'     => 'error',
			)
		);
	}

	if ( SZ_Groups_Member::check_for_membership_request( $user_id, $group_id ) ) {
		wp_send_json_error(
			array(
				'feedback' => __( 'The member is already a member of the group.', 'sportszone' ),
				'type'     => 'warning',
				'code'     => 1,
			)
		);
	}

	// Remove the unsent invitation.
	if ( ! groups_uninvite_user( $user_id, $group_id ) ) {
		wp_send_json_error(
			array(
				'feedback' => __( 'Group invitation could not be removed.', 'sportszone' ),
				'type'     => 'error',
				'code'     => 0,
			)
		);
	}

	wp_send_json_success(
		array(
			'feedback'    => __( 'There are no more pending invitations for the group.', 'sportszone' ),
			'type'        => 'info',
			'has_invites' => sz_group_has_invites( array( 'user_id' => 'any' ) ),
		)
	);
}
