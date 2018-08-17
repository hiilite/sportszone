<?php
/**
 * Events Template tags
 *
 * @since 3.0.0
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Template tag to wrap all Legacy actions that was used
 * before the events directory content
 *
 * @since 3.0.0
 */
function sz_nouveau_before_events_directory_content() {
	/**
	 * Fires at the begining of the templates BP injected content.
	 *
	 * @since 2.3.0
	 */
	do_action( 'sz_before_directory_events_page' );

	/**
	 * Fires before the display of the events.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_before_directory_events' );

	/**
	 * Fires before the display of the events content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_before_directory_events_content' );
}

/**
 * Template tag to wrap all Legacy actions that was used
 * after the events directory content
 *
 * @since 3.0.0
 */
function sz_nouveau_after_events_directory_content() {
	/**
	 * Fires and displays the event content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_directory_events_content' );

	/**
	 * Fires after the display of the events content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_after_directory_events_content' );

	/**
	 * Fires after the display of the events.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_after_directory_events' );

	/**
	 * Fires at the bottom of the events directory template file.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_after_directory_events_page' );
}

/**
 * Fire specific hooks into the events create template.
 *
 * @since 3.0.0
 *
 * @param string $when   Optional. Either 'before' or 'after'.
 * @param string $suffix Optional. Use it to add terms at the end of the hook name.
 */
function sz_nouveau_events_create_hook( $when = '', $suffix = '' ) {
	$hook = array( 'sz' );

	if ( $when ) {
		$hook[] = $when;
	}

	// It's a create event hook
	$hook[] = 'create_event';

	if ( $suffix ) {
		$hook[] = $suffix;
	}

	sz_nouveau_hook( $hook );
}

/**
 * Fire specific hooks into the single events templates.
 *
 * @since 3.0.0
 *
 * @param string $when   Optional. Either 'before' or 'after'.
 * @param string $suffix Optional. Use it to add terms at the end of the hook name.
 */
function sz_nouveau_event_hook( $when = '', $suffix = '' ) {
	$hook = array( 'sz' );

	if ( $when ) {
		$hook[] = $when;
	}

	// It's a event hook
	$hook[] = 'event';

	if ( $suffix ) {
		$hook[] = $suffix;
	}

	sz_nouveau_hook( $hook );
}

/**
 * Fire an isolated hook inside the events loop
 *
 * @since 3.0.0
 */
function sz_nouveau_events_loop_item() {
	/**
	 * Fires inside the listing of an individual event listing item.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_directory_events_item' );
}

/**
 * Display the current event activity post form if needed
 *
 * @since 3.0.0
 */
function sz_nouveau_events_activity_post_form() {
	/**
	 * Fires before the display of the event activity post form.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_event_activity_post_form' );

	if ( is_user_logged_in() && sz_event_is_member() ) {
		sz_get_template_part( 'activity/post-form' );
	}

	/**
	 * Fires after the display of the event activity post form.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_event_activity_post_form' );
}

/**
 * Load the Event Invites UI.
 *
 * @since 3.0.0
 *
 * @return string HTML Output.
 */
function sz_nouveau_event_invites_interface() {
	/**
	 * Fires before the send invites content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_before_event_send_invites_content' );

	sz_get_template_part( 'common/js-templates/invites/index' );

	/**
	 * Fires after the send invites content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_event_send_invites_content' );
}

/**
 * Gets the displayed user event invites preferences
 *
 * @since 3.0.0
 *
 * @return int Returns 1 if user chose to restrict to friends, 0 otherwise.
 */
function sz_nouveau_events_get_event_invites_setting() {
	return (int) sz_get_user_meta( sz_displayed_user_id(), '_sz_nouveau_restrict_invites_to_friends' );
}

/**
 * Outputs the event creation numbered steps navbar
 *
 * @since 3.0.0
 *
 * @todo This output isn't localised correctly.
 */
function sz_nouveau_event_creation_tabs() {
	$sz = sportszone();

	if ( ! is_array( $sz->events->event_creation_steps ) ) {
		return;
	}

	if ( ! sz_get_events_current_create_step() ) {
		$keys                            = array_keys( $sz->events->event_creation_steps );
		$sz->events->current_create_step = array_shift( $keys );
	}

	$counter = 1;

	foreach ( (array) $sz->events->event_creation_steps as $slug => $step ) {
		$is_enabled = sz_are_previous_event_creation_steps_complete( $slug ); ?>

		<li<?php if ( sz_get_events_current_create_step() === $slug ) : ?> class="current"<?php endif; ?>>
			<?php if ( $is_enabled ) : ?>
				<a href="<?php echo esc_url( sz_events_directory_permalink() . 'create/step/' . $slug . '/' ); ?>">
					<?php echo (int) $counter; ?> <?php echo esc_html( $step['name'] ); ?>
				</a>
			<?php else : ?>
				<?php echo (int) $counter; ?>. <?php echo esc_html( $step['name'] ); ?>
			<?php endif ?>
		</li>
			<?php
		$counter++;
	}

	unset( $is_enabled );

	/**
	 * Fires at the end of the creation of the event tabs.
	 *
	 * @since 1.0.0
	 */
	do_action( 'events_creation_tabs' );
}

/**
 * Load the requested Create Screen for the new event.
 *
 * @since 3.0.0
 */
function sz_nouveau_event_creation_screen() {
	return sz_nouveau_event_manage_screen();
}

/**
 * Load the requested Manage Screen for the current event.
 *
 * @since 3.0.0
 */

function sz_nouveau_event_manage_screen() {
	$action          = sz_action_variable( 0 );
	$is_event_create = sz_is_event_create();
	$output          = '';

	if ( $is_event_create ) {
		$action = sz_action_variable( 1 );
	}

	$screen_id = sanitize_file_name( $action );
	if ( ! sz_is_event_admin_screen( $screen_id ) && ! sz_is_event_creation_step( $screen_id ) ) {
		return;
	}

	if ( ! $is_event_create ) {
		/**
		 * Fires inside the event admin form and before the content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_before_event_admin_content' );

		$core_screen = sz_nouveau_event_get_core_manage_screens( $screen_id );

	// It's a event step, get the creation screens.
	} else {
		$core_screen = sz_nouveau_event_get_core_create_screens( $screen_id );
	}

	if ( ! $core_screen ) {
		if ( ! $is_event_create ) {
			/**
			 * Fires inside the event admin template.
			 *
			 * Allows plugins to add custom event edit screens.
			 *
			 * @since 1.1.0
			 */
			do_action( 'events_custom_edit_steps' );

		// Else use the event create hook
		} else {
			/**
			 * Fires inside the event admin template.
			 *
			 * Allows plugins to add custom event creation steps.
			 *
			 * @since 1.1.0
			 */
			do_action( 'events_custom_create_steps' );
		}

	// Else we load the core screen.
	} else {
		if ( ! empty( $core_screen['hook'] ) ) {
			/**
			 * Fires before the display of event delete admin.
			 *
			 * @since 1.1.0 For most hooks.
			 * @since 2.4.0 For the cover image hook.
			 */
			do_action( 'sz_before_' . $core_screen['hook'] );
		}

		$template = 'events/single/admin/' . $screen_id;

		if ( ! empty( $core_screen['template'] ) ) {
			$template = $core_screen['template'];
		}

		sz_get_template_part( $template );

		if ( ! empty( $core_screen['hook'] ) ) {
			/**
			 * Fires before the display of event delete admin.
			 *
			 * @since 1.1.0 For most hooks.
			 * @since 2.4.0 For the cover image hook.
			 */
			do_action( 'sz_after_' . $core_screen['hook'] );
		}

		if ( ! empty( $core_screen['nonce'] ) ) {
			if ( ! $is_event_create ) {
				$output = sprintf( '<p><input type="submit" value="%s" id="save" name="save" /></p>', esc_attr__( 'Save Changes', 'sportszone' ) );

				// Specific case for the delete event screen
				if ( 'delete-event' === $screen_id ) {
					$output = sprintf(
						'<div class="submit">
							<input type="submit" disabled="disabled" value="%s" id="delete-event-button" name="delete-event-button" />
						</div>',
						esc_attr__( 'Delete Event', 'sportszone' )
					);
				}
			}
		}
	}

	if ( $is_event_create ) {
		/**
		 * Fires before the display of the event creation step buttons.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_before_event_creation_step_buttons' );

		if ( 'crop-image' !== sz_get_avatar_admin_step() ) {
			$creation_step_buttons = '';

			if ( ! sz_is_first_event_creation_step() ) {
				$creation_step_buttons .= sprintf(
					'<input type="button" value="%1$s" id="event-creation-previous" name="previous" onclick="%2$s" />',
					esc_attr__( 'Back to Previous Step', 'sportszone' ),
					"location.href='" . esc_js( esc_url_raw( sz_get_event_creation_previous_link() ) ) . "'"
				);
			}

			if ( ! sz_is_last_event_creation_step() && ! sz_is_first_event_creation_step() ) {
				$creation_step_buttons .= sprintf(
					'<input type="submit" value="%s" id="event-creation-next" name="save" />',
					esc_attr__( 'Next Step', 'sportszone' )
				);
			}

			if ( sz_is_first_event_creation_step() ) {
				$creation_step_buttons .= sprintf(
					'<input type="submit" value="%s" id="event-creation-create" name="save" />',
					esc_attr__( 'Create Event and Continue', 'sportszone' )
				);
			}

			if ( sz_is_last_event_creation_step() ) {
				$creation_step_buttons .= sprintf(
					'<input type="submit" value="%s" id="event-creation-finish" name="save" />',
					esc_attr__( 'Finish', 'sportszone' )
				);
			}

			// Set the output for the buttons
			$output = sprintf( '<div class="submit" id="previous-next">%s</div>', $creation_step_buttons );
		}

		/**
		 * Fires after the display of the event creation step buttons.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_after_event_creation_step_buttons' );
	}

	/**
	 * Avoid nested forms with the Backbone views for the event invites step.
	 */
	if ( 'event-invites' === sz_get_events_current_create_step() ) {
		printf(
			'<form action="%s" method="post" enctype="multipart/form-data">',
			sz_get_event_creation_form_action()
		);
	}

	if ( ! empty( $core_screen['nonce'] ) ) {
		wp_nonce_field( $core_screen['nonce'] );
	}

	printf(
		'<input type="hidden" name="event-id" id="event-id" value="%s" />',
		$is_event_create ? esc_attr( sz_get_new_event_id() ) : esc_attr( sz_get_event_id() )
	);

	// The submit actions
	echo $output;

	if ( ! $is_event_create ) {
		/**
		 * Fires inside the event admin form and after the content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_after_event_admin_content' );

	} else {
		/**
		 * Fires and displays the events directory content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_directory_events_content' );
	}

	/**
	 * Avoid nested forms with the Backbone views for the event invites step.
	 */
	if ( 'event-invites' === sz_get_events_current_create_step() ) {
		echo '</form>';
	}
}

/**
 * Output the action buttons for the displayed event
 *
 * @since 3.0.0
 *
 * @param array $args Optional. See sz_nouveau_wrapper() for the description of parameters.
 */
function sz_nouveau_event_header_buttons( $args = array() ) {
	$sz_nouveau = sz_nouveau();

	$output = join( ' ', sz_nouveau_get_events_buttons( $args ) );

	// On the event's header we need to reset the event button's global.
	if ( ! empty( $sz_nouveau->events->event_buttons ) ) {
		unset( $sz_nouveau->events->event_buttons );
	}

	ob_start();
	/**
	 * Fires in the event header actions section.
	 *
	 * @since 1.2.6
	 */
	do_action( 'sz_event_header_actions' );
	$output .= ob_get_clean();

	if ( ! $output ) {
		return;
	}

	if ( ! $args ) {
		$args = array( 'classes' => array( 'item-buttons' ) );
	}

	sz_nouveau_wrapper( array_merge( $args, array( 'output' => $output ) ) );
}

/**
 * Output the action buttons inside the events loop.
 *
 * @since 3.0.0
 *
 * @param array $args Optional. See sz_nouveau_wrapper() for the description of parameters.
 */
function sz_nouveau_events_loop_buttons( $args = array() ) {
	if ( empty( $GLOBALS['events_template'] ) ) {
		return;
	}

	$args['type'] = 'loop';

	$output = join( ' ', sz_nouveau_get_events_buttons( $args ) );

	ob_start();
	/**
	 * Fires inside the action section of an individual event listing item.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_directory_events_actions' );
	$output .= ob_get_clean();

	if ( ! $output ) {
		return;
	}

	sz_nouveau_wrapper( array_merge( $args, array( 'output' => $output ) ) );
}

/**
 * Output the action buttons inside the invites loop of the displayed user.
 *
 * @since 3.0.0
 *
 * @param array $args Optional. See sz_nouveau_wrapper() for the description of parameters.
 */
function sz_nouveau_events_invite_buttons( $args = array() ) {
	if ( empty( $GLOBALS['events_template'] ) ) {
		return;
	}

	$args['type'] = 'invite';

	$output = join( ' ', sz_nouveau_get_events_buttons( $args ) );

	ob_start();
	/**
	 * Fires inside the member event item action markup.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_event_invites_item_action' );
	$output .= ob_get_clean();

	if ( ! $output ) {
		return;
	}

	sz_nouveau_wrapper( array_merge( $args, array( 'output' => $output ) ) );
}

/**
 * Output the action buttons inside the requests loop of the event's manage screen.
 *
 * @since 3.0.0
 *
 * @param array $args Optional. See sz_nouveau_wrapper() for the description of parameters.
 */
function sz_nouveau_events_request_buttons( $args = array() ) {
	if ( empty( $GLOBALS['requests_template'] ) ) {
		return;
	}

	$args['type'] = 'request';

	$output = join( ' ', sz_nouveau_get_events_buttons( $args ) );

	ob_start();
	/**
	 * Fires inside the list of membership request actions.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_event_membership_requests_admin_item_action' );
	$output .= ob_get_clean();

	if ( ! $output ) {
		return;
	}

	sz_nouveau_wrapper( array_merge( $args, array( 'output' => $output ) ) );
}

/**
 * Output the action buttons inside the manage members loop of the event's manage screen.
 *
 * @since 3.0.0
 *
 * @param array $args Optional. See sz_nouveau_wrapper() for the description of parameters.
 */
function sz_nouveau_events_manage_members_buttons( $args = array() ) {
	if ( empty( $GLOBALS['members_template'] ) ) {
		return;
	}

	$args['type'] = 'manage_members';

	$output = join( ' ', sz_nouveau_get_events_buttons( $args ) );

	ob_start();
	/**
	 * Fires inside the display of a member admin item in event management area.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_event_manage_members_admin_item' );
	$output .= ob_get_clean();

	if ( ! $output ) {
		return;
	}

	if ( ! $args ) {
		$args = array(
			'wrapper' => 'span',
			'classes' => array( 'small' ),
		);
	}

	sz_nouveau_wrapper( array_merge( $args, array( 'output' => $output ) ) );
}

	/**
	 * Get the action buttons for the current event in the loop,
	 * or the current displayed event.
	 *
	 * @since 3.0.0
	 *
	 * @param array $args Optional. See sz_nouveau_wrapper() for the description of parameters.
	 */
	function sz_nouveau_get_events_buttons( $args = array() ) {
		$type = ( ! empty( $args['type'] ) ) ? $args['type'] : 'event';

		// @todo Not really sure why BP Legacy needed to do this...
		if ( 'event' === $type && is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return;
		}

		$buttons = array();

		if ( ( 'loop' === $type || 'invite' === $type ) && isset( $GLOBALS['events_template']->event ) ) {
			$event = $GLOBALS['events_template']->event;
		} else {
			$event = events_get_current_event();
		}

		if ( empty( $event->id ) ) {
			return $buttons;
		}

		/*
		 * If the 'container' is set to 'ul' set $parent_element to li,
		 * otherwise simply pass any value found in $args or set var false.
		 */
		if ( ! empty( $args['container'] ) && 'ul' === $args['container']  ) {
			$parent_element = 'li';
		} elseif ( ! empty( $args['parent_element'] ) ) {
			$parent_element = $args['parent_element'];
		} else {
			$parent_element = false;
		}

		/*
		 * If we have a arg value for $button_element passed through
		 * use it to default all the $buttons['button_element'] values
		 * otherwise default to 'a' (anchor) o override & hardcode the
		 * 'element' string on $buttons array.
		 *
		 * Icons sets a class for icon display if not using the button element
		 */
		$icons = '';
		if ( ! empty( $args['button_element'] ) ) {
			$button_element = $args['button_element'] ;
		} else {
			$button_element = 'a';
			$icons = ' icons';
		}

		// If we pass through parent classes add them to $button array
		$parent_class = '';
		if ( ! empty( $args['parent_attr']['class'] ) ) {
			$parent_class = $args['parent_attr']['class'];
		}

		// Invite buttons on member's invites screen
		if ( 'invite' === $type ) {
			// Don't show button if not logged in or previously banned
			if ( ! is_user_logged_in() || sz_event_is_user_banned( $event ) || empty( $event->status ) ) {
				return $buttons;
			}

			// Setup Accept button attributes
			$buttons['accept_invite'] =  array(
				'id'                => 'accept_invite',
				'position'          => 5,
				'component'         => 'events',
				'must_be_logged_in' => true,
				'parent_element'    => $parent_element,
				'link_text'         => esc_html__( 'Accept', 'sportszone' ),
				'button_element'    => $button_element,
				'parent_attr'       => array(
					'id'    => '',
					'class' => $parent_class . ' ' . 'accept',
				),
				'button_attr'       => array(
					'id'    => '',
					'class' => 'button accept event-button accept-invite',
					'rel'   => '',
				),
			);

			// If button element set add nonce link to data-attr attr
			if ( 'button' === $button_element ) {
				$buttons['accept_invite']['button_attr']['data-sz-nonce'] = esc_url( sz_get_event_accept_invite_link() );
			} else {
				$buttons['accept_invite']['button_attr']['href'] = esc_url( sz_get_event_accept_invite_link() );
			}

			// Setup Reject button attributes
			$buttons['reject_invite'] = array(
				'id'                => 'reject_invite',
				'position'          => 15,
				'component'         => 'events',
				'must_be_logged_in' => true,
				'parent_element'    => $parent_element,
				'link_text'         => __( 'Reject', 'sportszone' ),
				'parent_attr'       => array(
					'id'    => '',
					'class' => $parent_class . ' ' . 'reject',
				),
				'button_element'    => $button_element,
				'button_attr'       => array(
					'id'    => '',
					'class' => 'button reject event-button reject-invite',
					'rel'   => '',
				),
			);

			// If button element set add nonce link to formaction attr
			if ( 'button' === $button_element ) {
				$buttons['reject_invite']['button_attr']['data-sz-nonce'] = esc_url( sz_get_event_reject_invite_link() );
			} else {
				$buttons['reject_invite']['button_attr']['href'] = esc_url( sz_get_event_reject_invite_link() );
			}

		// Request button for the event's manage screen
		} elseif ( 'request' === $type ) {
			// Setup Accept button attributes
			$buttons['event_membership_accept'] =  array(
				'id'                => 'event_membership_accept',
				'position'          => 5,
				'component'         => 'events',
				'must_be_logged_in' => true,
				'parent_element'    => $parent_element,
				'link_text'         => esc_html__( 'Accept', 'sportszone' ),
				'button_element'    => $button_element,
				'parent_attr'       => array(
					'id'    => '',
					'class' => $parent_class,
				),
				'button_attr'       => array(
					'id'    => '',
					'class' => 'button accept',
					'rel'   => '',
				),
			);

			// If button element set add nonce link to data-attr attr
			if ( 'button' === $button_element ) {
				$buttons['event_membership_accept']['button_attr']['data-sz-nonce'] = esc_url( sz_get_event_request_accept_link() );
			} else {
				$buttons['event_membership_accept']['button_attr']['href'] = esc_url( sz_get_event_request_accept_link() );
			}

			$buttons['event_membership_reject'] = array(
				'id'                => 'event_membership_reject',
				'position'          => 15,
				'component'         => 'events',
				'must_be_logged_in' => true,
				'parent_element'    => $parent_element,
				'button_element'    => $button_element,
				'link_text'         => __( 'Reject', 'sportszone' ),
				'parent_attr'       => array(
					'id'    => '',
					'class' => $parent_class,
				),
				'button_attr'       => array(
					'id'    => '',
					'class' => 'button reject',
					'rel'   => '',
				),
			);

			// If button element set add nonce link to data-attr attr
			if ( 'button' === $button_element ) {
				$buttons['event_membership_reject']['button_attr']['data-sz-nonce'] = esc_url( sz_get_event_request_reject_link() );
			} else {
				$buttons['event_membership_reject']['button_attr']['href'] = esc_url( sz_get_event_request_reject_link() );
			}

		/*
		 * Manage event members for the event's manage screen.
		 * The 'button_attr' keys 'href' & 'formaction' are set at the end of this array block
		 */
		} elseif ( 'manage_members' === $type && isset( $GLOBALS['members_template']->member->user_id ) ) {
			$user_id = $GLOBALS['members_template']->member->user_id;

			$buttons = array(
				'unban_member' => array(
					'id'                => 'unban_member',
					'position'          => 5,
					'component'         => 'events',
					'must_be_logged_in' => true,
					'parent_element'    => $parent_element,
					'button_element'    => $button_element,
					'link_text'         => __( 'Remove Ban', 'sportszone' ),
					'parent_attr'       => array(
						'id'    => '',
						'class' => $parent_class,
					),
					'button_attr'       => array(
						'id'    => '',
						'class' => 'button confirm member-unban',
						'rel'   => '',
						'title' => '',
					),
				),
				'ban_member' => array(
					'id'                => 'ban_member',
					'position'          => 15,
					'component'         => 'events',
					'must_be_logged_in' => true,
					'parent_element'    => $parent_element,
					'button_element'    => $button_element,
					'link_text'         => __( 'Kick &amp; Ban', 'sportszone' ),
					'parent_attr'       => array(
						'id'    => '',
						'class' => $parent_class,
					),
					'button_attr'       => array(
						'id'    => '',
						'class' => 'button confirm member-ban',
						'rel'   => '',
						'title' => '',
					),
				),
				'promote_mod' => array(
					'id'                => 'promote_mod',
					'position'          => 25,
					'component'         => 'events',
					'must_be_logged_in' => true,
					'parent_element'    => $parent_element,
					'parent_attr'       => array(
						'id'    => '',
						'class' => $parent_class,
					),
					'button_element'    => $button_element,
					'button_attr'       => array(
						'id'               => '',
						'class'            => 'button confirm member-promote-to-mod',
						'rel'              => '',
						'title'            => '',
					),
					'link_text'         => __( 'Promote to Mod', 'sportszone' ),
				),
				'promote_admin' => array(
					'id'                => 'promote_admin',
					'position'          => 35,
					'component'         => 'events',
					'must_be_logged_in' => true,
					'parent_element'    => $parent_element,
					'button_element'    => $button_element,
					'link_text'         => __( 'Promote to Admin', 'sportszone' ),
					'parent_attr'       => array(
						'id'    => '',
						'class' => $parent_class,
					),
					'button_attr'       => array(
						'href'  => esc_url( sz_get_event_member_promote_admin_link() ),
						'id'    => '',
						'class' => 'button confirm member-promote-to-admin',
						'rel'   => '',
						'title' => '',
					),
				),
				'remove_member' => array(
					'id'                => 'remove_member',
					'position'          => 45,
					'component'         => 'events',
					'must_be_logged_in' => true,
					'parent_element'    => $parent_element,
					'button_element'    => $button_element,
					'link_text'         => __( 'Remove from event', 'sportszone' ),
					'parent_attr'       => array(
						'id'    => '',
						'class' => $parent_class,
					),
					'button_attr'       => array(
						'id'    => '',
						'class' => 'button confirm',
						'rel'   => '',
						'title' => '',
					),
				),
			);

			// If 'button' element is set add the nonce link to data-attr attr, else add it to the href.
			if ( 'button' === $button_element ) {
				$buttons['unban_member']['button_attr']['data-sz-nonce'] = sz_get_event_member_unban_link( $user_id );
				$buttons['ban_member']['button_attr']['data-sz-nonce'] = sz_get_event_member_ban_link( $user_id );
				$buttons['promote_mod']['button_attr']['data-sz-nonce'] = sz_get_event_member_promote_mod_link();
				$buttons['promote_admin']['button_attr']['data-sz-nonce'] = sz_get_event_member_promote_admin_link();
				$buttons['remove_member']['button_attr']['data-sz-nonce'] = sz_get_event_member_remove_link( $user_id );
			} else {
				$buttons['unban_member']['button_attr']['href'] = sz_get_event_member_unban_link( $user_id );
				$buttons['ban_member']['button_attr']['href'] = sz_get_event_member_ban_link( $user_id );
				$buttons['promote_mod']['button_attr']['href'] = sz_get_event_member_promote_mod_link();
				$buttons['promote_admin']['button_attr']['href'] = sz_get_event_member_promote_admin_link();
				$buttons['remove_member']['button_attr']['href'] = sz_get_event_member_remove_link( $user_id );
			}

		// Membership button on events loop or single event's header
		} else {
			/*
			 * This filter workaround is waiting for a core adaptation
			 * so that we can directly get the events button arguments
			 * instead of the button.
			 *
			 * See https://sportszone.trac.wordpress.org/ticket/7126
			 */
			add_filter( 'sz_get_event_join_button', 'sz_nouveau_events_catch_button_args', 100, 1 );

			sz_get_event_join_button( $event );

			remove_filter( 'sz_get_event_join_button', 'sz_nouveau_events_catch_button_args', 100, 1 );

			if ( ! empty( sz_nouveau()->events->button_args ) ) {
				$button_args = sz_nouveau()->events->button_args;

				// If we pass through parent classes merge those into the existing ones
				if ( $parent_class ) {
					$parent_class .= ' ' . $button_args['wrapper_class'];
				}

				// The join or leave event header button should default to 'button'
				// Reverse the earler button var to set default as 'button' not 'a'
				if ( empty( $args['button_element'] ) ) {
					$button_element = 'button';
				}

				$buttons['event_membership'] = array(
					'id'                => 'event_membership',
					'position'          => 5,
					'component'         => $button_args['component'],
					'must_be_logged_in' => $button_args['must_be_logged_in'],
					'block_self'        => $button_args['block_self'],
					'parent_element'    => $parent_element,
					'button_element'    => $button_element,
					'link_text'         => $button_args['link_text'],
					'parent_attr'       => array(
							'id'    => $button_args['wrapper_id'],
							'class' => $parent_class,
					),
					'button_attr'       => array(
						'id'    => ! empty( $button_args['link_id'] ) ? $button_args['link_id'] : '',
						'class' => $button_args['link_class'] . ' button',
						'rel'   => ! empty( $button_args['link_rel'] ) ? $button_args['link_rel'] : '',
						'title' => '',
					),
				);

			// If button element set add nonce 'href' link to data-attr attr.
			if ( 'button' === $button_element ) {
				$buttons['event_membership']['button_attr']['data-sz-nonce'] = $button_args['link_href'];
			} else {
			// Else this is an anchor so use an 'href' attr.
				$buttons['event_membership']['button_attr']['href'] = $button_args['link_href'];
			}

				unset( sz_nouveau()->events->button_args );
			}
		}

		/**
		 * Filter to add your buttons, use the position argument to choose where to insert it.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $buttons The list of buttons.
		 * @param int    $event   The current event object.
		 * @param string $type    Whether we're displaying a events loop or a events single item.
		 */
		$buttons_event = apply_filters( 'sz_nouveau_get_events_buttons', $buttons, $event, $type );

		if ( ! $buttons_event ) {
			return $buttons;
		}

		// It's the first entry of the loop, so build the Event and sort it
		if ( ! isset( sz_nouveau()->events->event_buttons ) || ! is_a( sz_nouveau()->events->event_buttons, 'SZ_Buttons_Event' ) ) {
			$sort = true;
			sz_nouveau()->events->event_buttons = new SZ_Buttons_Event( $buttons_event );

		// It's not the first entry, the order is set, we simply need to update the Buttons Event
		} else {
			$sort = false;
			sz_nouveau()->events->event_buttons->update( $buttons_event );
		}

		$return = sz_nouveau()->events->event_buttons->get( $sort );

		if ( ! $return ) {
			return array();
		}

		// Remove buttons according to the user's membership type.
		if ( 'manage_members' === $type && isset( $GLOBALS['members_template'] ) ) {
			if ( sz_get_event_member_is_banned() ) {
				unset( $return['ban_member'], $return['promote_mod'], $return['promote_admin'] );
			} else {
				unset( $return['unban_member'] );
			}
		}

		/**
		 * Leave a chance to adjust the $return
		 *
		 * @since 3.0.0
		 *
		 * @param array  $return  The list of buttons.
		 * @param int    $event   The current event object.
		 * @parem string $type    Whether we're displaying a events loop or a events single item.
		 */
		do_action_ref_array( 'sz_nouveau_return_events_buttons', array( &$return, $event, $type ) );

		return $return;
	}

/**
 * Does the event has meta.
 *
 * @since 3.0.0
 *
 * @return bool True if the event has meta. False otherwise.
 */
function sz_nouveau_event_has_meta() {
	return (bool) sz_nouveau_get_event_meta();
}

/**
 * Does the event have extra meta?
 *
 * @since 3.0.0
 *
 * @return bool True if the event has meta. False otherwise.
 */
function sz_nouveau_event_has_meta_extra() {
	return (bool) sz_nouveau_get_hooked_event_meta();
}

/**
 * Display the event meta.
 *
 * @since 3.0.0
 *
 * @return string HTML Output.
 */
function sz_nouveau_event_meta() {
	$meta = sz_nouveau_get_event_meta();

	if ( ! sz_is_event() ) {
		echo join( ' / ', array_map( 'esc_html', (array) $meta ) );
	} else {

		/*
		 * Lets return an object not echo an array here for the single events,
		 * more flexible for the template!!?? ~hnla
		 *
		 * @todo Paul says that a function that prints and/or returns a value,
		 * depending on global state, is madness. This needs changing.
		 */
		return (object) sz_nouveau_get_event_meta();
	}
}

	/**
	 * Get the event meta.
	 *
	 * @since 3.0.0
	 *
	 * @return array The event meta.
	 */
	function sz_nouveau_get_event_meta() {
		/*
		 * @todo For brevity required approapriate markup is added here as strings
		 * this needs to be either filterable or the function needs to be able to accept
		 * & parse args!
		 */
		$meta     = array();
		$is_event = sz_is_event();

		if ( ! empty( $GLOBALS['events_template']->event ) ) {
			$event = $GLOBALS['events_template']->event;
		}

		if ( empty( $event->id ) ) {
			return $meta;
		}

		if ( empty( $event->template_meta ) ) {
			// It's a single event
			if ( $is_event ) {
					$meta = array(
						'status'          =>  sz_get_event_type(),
						'event_type_list' =>  sz_get_event_type_list(),
						'description'     =>  sz_get_event_description(),
					);

				// Make sure to include hooked meta.
				$extra_meta = sz_nouveau_get_hooked_event_meta();

				if ( $extra_meta ) {
					$meta['extra'] = $extra_meta;
				}

			// We're in the events loop
			} else {
				$meta = array(
					'status' => sz_get_event_type(),
					'count'  => sz_get_event_member_count(),
				);
			}

			/**
			 * Filter to add/remove Event meta.
			 *
			 * @since 3.0.0
			 *
			 * @param array  $meta     The list of meta to output.
			 * @param object $event    The current Event of the loop object.
			 * @param bool   $is_event True if a single event is displayed. False otherwise.
			 */
			$event->template_meta = apply_filters( 'sz_nouveau_get_event_meta', $meta, $event, $is_event );
		}

		return $event->template_meta;
	}

/**
 * Load the appropriate content for the single event pages
 *
 * @since 3.0.0
 */
function sz_nouveau_event_template_part() {
	/**
	 * Fires before the display of the event home body.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_event_body' );

	$sz_is_event_home = sz_is_event_home();

	if ( $sz_is_event_home && ! sz_current_user_can( 'events_access_event' ) ) {
		/**
		 * Fires before the display of the event status message.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_before_event_status_message' );
		?>

		<div id="message" class="info">
			<p><?php sz_event_status_message(); ?></p>
		</div>

		<?php

		/**
		 * Fires after the display of the event status message.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_after_event_status_message' );

	// We have a front template, Use SportsZone function to load it.
	} elseif ( $sz_is_event_home && false !== sz_events_get_front_template() ) {
		sz_events_front_template_part();

	// Otherwise use SZ_Nouveau template hierarchy
	} else {
		$template = 'plugins';

		// the home page
		if ( $sz_is_event_home ) {
			if ( sz_is_active( 'activity' ) ) {
				$template = 'activity';
			} else {
				$template = 'members';
			}

		// Not the home page
		} elseif ( sz_is_event_admin_page() ) {
			$template = 'admin';
		} elseif ( sz_is_event_activity() ) {
			$template = 'activity';
		} elseif ( sz_is_event_members() ) {
			$template = 'members';
		} elseif ( sz_is_event_invites() ) {
			$template = 'send-invites';
		} elseif ( sz_is_event_membership_request() ) {
			$template = 'request-membership';
		}

		sz_nouveau_event_get_template_part( $template );
	}

	/**
	 * Fires after the display of the event home body.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_event_body' );
}

/**
 * Use the appropriate Event header and enjoy a template hierarchy
 *
 * @since 3.0.0
 */
function sz_nouveau_event_header_template_part() {
	$template = 'event-header';

	if ( sz_event_use_cover_image_header() ) {
		$template = 'cover-image-header';
	}

	/**
	 * Fires before the display of a event's header.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_event_header' );

	// Get the template part for the header
	sz_nouveau_event_get_template_part( $template );

	/**
	 * Fires after the display of a event's header.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_event_header' );

	sz_nouveau_template_notices();
}

/**
 * Get a link to set the Event's default front page and directly
 * reach the Customizer section where it's possible to do it.
 *
 * @since 3.0.0
 *
 * @return string HTML Output
 */
function sz_nouveau_events_get_customizer_option_link() {
	return sz_nouveau_get_customizer_link(
		array(
			'object'    => 'event',
			'autofocus' => 'sz_nouveau_event_front_page',
			'text'      => __( 'Events default front page', 'sportszone' ),
		)
	);
}

/**
 * Get a link to set the Event's front page widgets and directly
 * reach the Customizer section where it's possible to do it.
 *
 * @since 3.0.0
 *
 * @return string HTML Output
 */
function sz_nouveau_events_get_customizer_widgets_link() {
	return sz_nouveau_get_customizer_link(
		array(
			'object'    => 'event',
			'autofocus' => 'sidebar-widgets-sidebar-sportszone-events',
			'text'      => __( '(SportsZone) Widgets', 'sportszone' ),
		)
	);
}

/**
 * Output the event description excerpt
 *
 * @since 3.0.0
 *
 * @param object $event Optional. The event being referenced.
 *                      Defaults to the event currently being iterated on in the events loop.
 * @param int $length   Optional. Length of returned string, including ellipsis. Default: 100.
 *
 * @return string Excerpt.
 */
function sz_nouveau_event_description_excerpt( $event = null, $length = null ) {
	echo sz_nouveau_get_event_description_excerpt( $event, $length );
}

/**
 * Filters the excerpt of a event description.
 *
 * Checks if the event loop is set as a 'Grid' layout and returns a reduced excerpt.
 *
 * @since 3.0.0
 *
 * @param object $event Optional. The event being referenced. Defaults to the event currently being
 *                      iterated on in the events loop.
 * @param int $length   Optional. Length of returned string, including ellipsis. Default: 100.
 *
 * @return string Excerpt.
 */
function sz_nouveau_get_event_description_excerpt( $event = null, $length = null ) {
	global $events_template;

	if ( ! $event ) {
		$event =& $events_template->event;
	}

	/**
	 * If this is a grid layout but no length is passed in set a shorter
	 * default value otherwise use the passed in value.
	 * If not a grid then the BP core default is used or passed in value.
	 */
	if ( sz_nouveau_loop_is_grid() && 'events' === sz_current_component() ) {
		if ( ! $length ) {
			$length = 100;
		} else {
			$length = $length;
		}
	}

	/**
	 * Filters the excerpt of a event description.
	 *
	 * @since 3.0.0
	 *
	 * @param string $value Excerpt of a event description.
	 * @param object $event Object for event whose description is made into an excerpt.
	 */
	return apply_filters( 'sz_nouveau_get_event_description_excerpt', sz_create_excerpt( $event->description, $length ), $event );
}
