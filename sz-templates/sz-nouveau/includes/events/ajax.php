<?php
/**
 * Events Ajax functions
 *
 * @since 3.0.0
 * @version 3.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', function() {
	$ajax_actions = array(
		array( 'events_filter'                      => array( 'function' => 'sz_nouveau_ajax_object_template_loader', 'nopriv' => true  ) ),
		array( 'events_join_event'                  => array( 'function' => 'sz_nouveau_ajax_joinleave_event', 'nopriv' => false ) ),
		array( 'events_pay_event'                  	=> array( 'function' => 'sz_nouveau_ajax_joinleave_event', 'nopriv' => false ) ),
		array( 'events_leave_event'                 => array( 'function' => 'sz_nouveau_ajax_joinleave_event', 'nopriv' => false ) ),
		array( 'events_accept_invite'               => array( 'function' => 'sz_nouveau_ajax_joinleave_event', 'nopriv' => false ) ),
		array( 'events_reject_invite'               => array( 'function' => 'sz_nouveau_ajax_joinleave_event', 'nopriv' => false ) ),
		array( 'events_request_membership'          => array( 'function' => 'sz_nouveau_ajax_joinleave_event', 'nopriv' => false ) ),
		array( 'events_get_event_potential_invites' => array( 'function' => 'sz_nouveau_ajax_get_users_to_invite', 'nopriv' => false ) ),
		array( 'events_send_event_invites'          => array( 'function' => 'sz_nouveau_ajax_send_event_invites', 'nopriv' => false ) ),
		array( 'events_delete_event_invite'         => array( 'function' => 'sz_nouveau_ajax_remove_event_invite', 'nopriv' => false ) ),
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
 * Join or leave a event when clicking the "join/leave" button via a POST request.
 *
 * @since 3.0.0
 *
 * @return string HTML
 */
function sz_nouveau_ajax_joinleave_event() {
	$response = array(
		'feedback' => sprintf(
			'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
			esc_html__( 'There was a problem performing this action. Please try again.', 'sportszone' )
		),
	);

	// Bail if not a POST action.
	if ( ! sz_is_post_request() || empty( $_POST['action'] ) ) {
		$response = array(
			'feedback' => sprintf(
				'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
				esc_html__( 'There was a problem performing this post action. Please try again.', 'sportszone' )
			),
		);
		wp_send_json_error( $response );
	}

	if ( empty( $_POST['nonce'] ) || empty( $_POST['item_id'] ) || ! sz_is_active( 'events' ) ) {
		$response = array(
			'feedback' => sprintf(
				'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
				esc_html__( 'There was a problem performing getting this actions data. Please try again.', 'sportszone' )
			),
		);
		wp_send_json_error( $response );
	}

	// Use default nonce
	$nonce = $_POST['nonce'];
	$check = 'sz_nouveau_events';

	// Use a specific one for actions needed it
	if ( ! empty( $_POST['_wpnonce'] ) && ! empty( $_POST['action'] ) ) {
		$nonce = $_POST['_wpnonce'];
		$check = $_POST['action'];
	}

	// Nonce check!
	if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, $check ) ) {
		$response = array(
			'feedback' => sprintf(
				'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
				esc_html__( 'There was a problem performing verifying this action. Please try again.', 'sportszone' )
			),
		);
		wp_send_json_error( $response );
	}

	// Cast gid as integer.
	$event_id = (int) $_POST['item_id'];

	$errors = array(
		'cannot' => sprintf( '<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>', esc_html__( 'You cannot join this event.', 'sportszone' ) ),
		'member' => sprintf( '<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>', esc_html__( 'You are already a member of the event.', 'sportszone' ) ),
	);

	if ( events_is_user_banned( sz_loggedin_user_id(), $event_id ) ) {
		$response['feedback'] = $errors['cannot'];

		wp_send_json_error( $response );
	}

	// Validate and get the event
	$event = events_get_event( array( 'event_id' => $event_id ) );

	if ( empty( $event->id ) ) {
		wp_send_json_error( $response );
	}

	// Manage all button's possible actions here.
	switch ( $_POST['action'] ) {
		case 'events_pay_event':
			// TODO: add actions for paying for event.
			
			if ( events_is_user_member( sz_loggedin_user_id(), $event->id ) ) {
				$response = array(
					'feedback' => $errors['member'],
					'type'     => 'error',
				);
			} elseif ( 'paid' !== $event->status ) {
				$response = array(
					'feedback' => $errors['cannot'],
					'type'     => 'error',
				);
			} elseif ( events_join_event( $event->id ) ) {
				// TODO : create new transaction post in database to store all info.
				$event_team_select = isset($_POST['event_team_select'])?intval($_POST['event_team_select']):0;
				$requesting_user_id = get_current_user_id();
				$post_id = wp_insert_post(array(
					'post_type'		=> 'sz_orders',
					'post_status'	=> 'publish',
					'post_title'	=> 'Order',
					'meta_input'	=> array(
						'order_amount'		=> isset($_POST['event_purchase_total'])?$_POST['event_purchase_total']:0,
						'item_id'			=> isset($_POST['item_id'])?$_POST['item_id']:0,
						'item_type'			=> 'event',
						'team_id'			=> $event_team_select,
						'paypal_email'		=> isset($_POST['event_paypal_email'])?$_POST['event_paypal_email']:'',
						'user_id'			=> $requesting_user_id,
						'orderID' 			=> isset($_POST['orderID'])?$_POST['orderID']:'',
						'payerID'			=> isset($_POST['payerID'])?$_POST['payerID']:'',
						'paymentID' 		=> isset($_POST['paymentID'])?$_POST['paymentID']:'',
						'paymentToken'		=> isset($_POST['paymentToken'])?$_POST['paymentToken']:'',
					) 
				));
				if(!is_wp_error($post_id)){
				  //the post is valid
				  wp_update_post(array(
						'ID'			=> $post_id,
						'post_title'	=> 'Order #'.$post_id,
					) );
					
					 // Ajax add all members of team to event
					 
			        // Add teams to a "approved_teams" meta for selection by the event.
			        $approved_teams = events_get_eventmeta( $event->id, 'approved_teams');
					if(!is_array($approved_teams))$approved_teams = array();
			        $new_approved_teams = array_unique( array_merge( $approved_teams, array( $event_team_select ) ) );

			        events_update_eventmeta( $event->id, 'approved_teams', $new_approved_teams );
			        
			        // Send notification to event creator that team has been added to event
			        $admins = events_get_event_admins( $event->id );
					
					
					// Saved okay, now send the email notification.
					for ( $i = 0, $count = count( $admins ); $i < $count; ++$i )
						events_notification_team_joined( $requesting_user_id, $admins[$i]->user_id, $event->id, $requesting_user_id );
						

					
					
					$event->is_member = '1';

					$response = array(
						'contents' => sz_get_event_join_button( $event ),
						'is_event' => sz_is_event(),
						'type'     => 'success',
					);
				}else{
				  //there was an error in the post insertion, 
				  
				  $response = array(
						'feedback' => sprintf(
							'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
							esc_html__( $post_id->get_error_message() , 'sportszone' )
						),
						'type'     => 'error',
					);
				}
				
				        
				 
				
		       
		        
				
			} else {
				// User is now a member of the event
				
				$event->is_member = '1';

				$response = array(
					'contents' => sz_get_event_join_button( $event ),
					'is_event' => sz_is_event(),
					'type'     => 'success',
				);
			}
			break;

		case 'events_accept_invite':
			if ( ! events_accept_invite( sz_loggedin_user_id(), $event_id ) ) {
				$response = array(
					'feedback' => sprintf(
						'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
						esc_html__( 'Event invitation could not be accepted.', 'sportszone' )
					),
					'type'     => 'error',
				);

			} else {
				if ( sz_is_active( 'activity' ) ) {
					events_record_activity(
						array(
							'type'    => 'joined_event',
							'item_id' => $event->id,
						)
					);
				}

				// User is now a member of the event
				$event->is_member = '1';

				$response = array(
					'feedback' => sprintf(
						'<div class="sz-feedback success"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
						esc_html__( 'Event invite accepted.', 'sportszone' )
					),
					'type'     => 'success',
					'is_user'  => sz_is_user(),
					'contents' => sz_get_event_join_button( $event ),
					'is_event' => sz_is_event(),
				);
			}
			break;

		case 'events_reject_invite':
			if ( ! events_reject_invite( sz_loggedin_user_id(), $event_id ) ) {
				$response = array(
					'feedback' => sprintf(
						'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
						esc_html__( 'Event invite could not be rejected', 'sportszone' )
					),
					'type'     => 'error',
				);
			} else {
				$response = array(
					'feedback' => sprintf(
						'<div class="sz-feedback success"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
						esc_html__( 'Event invite rejected', 'sportszone' )
					),
					'type'     => 'success',
					'is_user'  => sz_is_user(),
				);
			}
			break;

		case 'events_join_event':
			if ( events_is_user_member( sz_loggedin_user_id(), $event->id ) ) {
				$response = array(
					'feedback' => $errors['member'],
					'type'     => 'error',
				);
			} elseif ( 'public' !== $event->status ) {
				$response = array(
					'feedback' => $errors['cannot'],
					'type'     => 'error',
				);
			} elseif ( ! events_join_event( $event->id ) ) {
				$response = array(
					'feedback' => sprintf(
						'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
						esc_html__( 'Error joining this event.', 'sportszone' )
					),
					'type'     => 'error',
				);
			} else {
				// User is now a member of the event
				$event->is_member = '1';

				$response = array(
					'contents' => sz_get_event_join_button( $event ),
					'is_event' => sz_is_event(),
					'type'     => 'success',
				);
			}
			break;

			case 'events_request_membership' :
				if ( ! events_send_membership_request( sz_loggedin_user_id(), $event->id ) ) {
					$response = array(
						'feedback' => sprintf(
							'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
							esc_html__( 'Error requesting membership.', 'sportszone' )
						),
						'type'     => 'error',
					);
				} else {
					// Request is pending
					$event->is_pending = '1';

					$response = array(
						'contents' => sz_get_event_join_button( $event ),
						'is_event' => sz_is_event(),
						'type'     => 'success',
					);
				}
				break;

			case 'events_leave_event' :
				if (  events_leave_event( $event->id ) ) {
					$response = array(
						'feedback' => sprintf(
							'<div class="sz-feedback error"><span class="sz-icon" aria-hidden="true"></span><p>%s</p></div>',
							esc_html__( 'Error leaving event.', 'sportszone' )
						),
						'type'     => 'error',
					);
				} else {
					// User is no more a member of the event
					$event->is_member = '0';
					$sz               = sportszone();

					/**
					 * When inside the event or in the loggedin user's event memberships screen
					 * we need to reload the page.
					 */
					$sz_is_event = sz_is_event() || ( sz_is_user_events() && sz_is_my_profile() );

					$response = array(
						'contents' => sz_get_event_join_button( $event ),
						'is_event' => $sz_is_event,
						'type'     => 'success',
					);

					// Reset the message if not in a Event or in a loggedin user's event memberships one!
					if ( ! $sz_is_event && isset( $sz->template_message ) && isset( $sz->template_message_type ) ) {
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
	$check = 'sz_nouveau_events';

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

	$sz->events->invites_scope = 'members';
	$message = __( 'Select members to invite by clicking the + button. Once you\'ve made your selection, use the "Send Invites" navigation item to continue.', 'sportszone' );

	if ( 'friends' === $request['scope'] ) {
		$request['user_id'] = sz_loggedin_user_id();
		$sz->events->invites_scope = 'friends';
		$message = __( 'Select friends to invite by clicking the + button. Once you\'ve made your selection, use the "Send Invites" navigation item to continue.', 'sportszone' );
	}

	if ( 'invited' === $request['scope'] ) {

		if ( ! sz_event_has_invites( array( 'user_id' => 'any' ) ) ) {
			wp_send_json_error( array(
				'feedback' => __( 'No pending event invitations found.', 'sportszone' ),
				'type'     => 'info',
			) );
		}

		$request['is_confirmed'] = false;
		$sz->events->invites_scope = 'invited';
		$message = __( 'You can view the event\'s pending invitations from this screen.', 'sportszone' );
	}

	$potential_invites = sz_nouveau_get_event_potential_invites( $request );

	if ( empty( $potential_invites->users ) ) {
		$error = array(
			'feedback' => __( 'No members were found. Try another filter.', 'sportszone' ),
			'type'     => 'info',
		);

		if ( 'friends' === $sz->events->invites_scope ) {
			$error = array(
				'feedback' => __( 'All your friends are already members of this event, or have already received an invite to join this event, or have requested to join it.', 'sportszone' ),
				'type'     => 'info',
			);

			if ( 0 === (int) sz_get_total_friend_count( sz_loggedin_user_id() ) ) {
				$error = array(
					'feedback' => __( 'You have no friends!', 'sportszone' ),
					'type'     => 'info',
				);
			}
		}

		unset( $sz->events->invites_scope );

		wp_send_json_error( $error );
	}

	$potential_invites->users = array_map( 'sz_nouveau_prepare_event_potential_invites_for_js', array_values( $potential_invites->users ) );
	$potential_invites->users = array_filter( $potential_invites->users );

	// Set a message to explain use of the current scope
	$potential_invites->feedback = $message;

	unset( $sz->events->invites_scope );

	wp_send_json_success( $potential_invites );
}

/**
 * @since 3.0.0
 */
function sz_nouveau_ajax_send_event_invites() {
	$sz = sportszone();

	$response = array(
		'feedback' => __( 'Invites could not be sent. Please try again.', 'sportszone' ),
		'type'     => 'error',
	);

	// Verify nonce
	if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'events_send_invites' ) ) {
		wp_send_json_error( $response );
	}

	$event_id = sz_get_current_event_id();

	if ( sz_is_event_create() && ! empty( $_POST['event_id'] ) ) {
		$event_id = (int) $_POST['event_id'];
	}

	if ( ! sz_events_user_can_send_invites( $event_id ) ) {
		$response['feedback'] = __( 'You are not allowed to send invitations for this event.', 'sportszone' );
		wp_send_json_error( $response );
	}

	if ( empty( $_POST['users'] ) ) {
		wp_send_json_error( $response );
	}

	// For feedback
	$invited = array();

	foreach ( (array) $_POST['users'] as $user_id ) {
		$invited[ (int) $user_id ] = events_invite_user(
			array(
				'user_id'  => $user_id,
				'event_id' => $event_id,
			)
		);
	}

	if ( ! empty( $_POST['message'] ) ) {
		$sz->events->invites_message = wp_kses( wp_unslash( $_POST['message'] ), array() );

		add_filter( 'events_notification_event_invites_message', 'sz_nouveau_events_invites_custom_message', 10, 1 );
	}

	// Send the invites.
	events_send_invites( sz_loggedin_user_id(), $event_id );

	if ( ! empty( $_POST['message'] ) ) {
		unset( $sz->events->invites_message );

		remove_filter( 'events_notification_event_invites_message', 'sz_nouveau_events_invites_custom_message', 10, 1 );
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
function sz_nouveau_ajax_remove_event_invite() {
	$user_id  = (int) $_POST['user'];
	$event_id = sz_get_current_event_id();

	// Verify nonce
	if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'events_invite_uninvite_user' ) ) {
		wp_send_json_error(
			array(
				'feedback' => __( 'Event invitation could not be removed.', 'sportszone' ),
				'type'     => 'error',
			)
		);
	}

	if ( SZ_Events_Member::check_for_membership_request( $user_id, $event_id ) ) {
		wp_send_json_error(
			array(
				'feedback' => __( 'The member is already a member of the event.', 'sportszone' ),
				'type'     => 'warning',
				'code'     => 1,
			)
		);
	}

	// Remove the unsent invitation.
	if ( ! events_uninvite_user( $user_id, $event_id ) ) {
		wp_send_json_error(
			array(
				'feedback' => __( 'Event invitation could not be removed.', 'sportszone' ),
				'type'     => 'error',
				'code'     => 0,
			)
		);
	}

	wp_send_json_success(
		array(
			'feedback'    => __( 'There are no more pending invitations for the event.', 'sportszone' ),
			'type'        => 'info',
			'has_invites' => sz_event_has_invites( array( 'user_id' => 'any' ) ),
		)
	);
}
