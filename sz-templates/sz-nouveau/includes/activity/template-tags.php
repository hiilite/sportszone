<?php
/**
 * Activity Template tags
 *
 * @since 3.0.0
 * @version 3.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Before Activity's directory content legacy do_action hooks wrapper
 *
 * @since 3.0.0
 */
function sz_nouveau_before_activity_directory_content() {
	/**
	 * Fires at the begining of the templates BP injected content.
	 *
	 * @since 2.3.0
	 */
	do_action( 'sz_before_directory_activity' );

	/**
	 * Fires before the activity directory display content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_directory_activity_content' );
}

/**
 * After Activity's directory content legacy do_action hooks wrapper
 *
 * @since 3.0.0
 */
function sz_nouveau_after_activity_directory_content() {
	/**
	 * Fires after the display of the activity list.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_after_directory_activity_list' );

	/**
	 * Fires inside and displays the activity directory display content.
	 */
	do_action( 'sz_directory_activity_content' );

	/**
	 * Fires after the activity directory display content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_directory_activity_content' );

	/**
	 * Fires after the activity directory listing.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_after_directory_activity' );
}

/**
 * Enqueue needed scripts for the Activity Post Form
 *
 * @since 3.0.0
 */
function sz_nouveau_before_activity_post_form() {
	if ( sz_nouveau_current_user_can( 'publish_activity' ) ) {
		wp_enqueue_script( 'sz-nouveau-activity-post-form' );
	}

	/**
	 * Fires before the activity post form.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_activity_post_form' );
}

/**
 * Load JS Templates for the Activity Post Form
 *
 * @since 3.0.0
 */
function sz_nouveau_after_activity_post_form() {
	if ( sz_nouveau_current_user_can( 'publish_activity' ) ) {
		sz_get_template_part( 'common/js-templates/activity/form' );
	}

	/**
	 * Fires after the activity post form.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_activity_post_form' );
}

/**
 * Display the displayed user activity post form if needed
 *
 * @since 3.0.0
 *
 * @return string HTML.
 */
function sz_nouveau_activity_member_post_form() {

	/**
	 * Fires before the display of the member activity post form.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_member_activity_post_form' );

	if ( is_user_logged_in() && sz_is_my_profile() && ( ! sz_current_action() || sz_is_current_action( 'just-me' ) ) ) {
		sz_get_template_part( 'activity/post-form' );
	}

	/**
	 * Fires after the display of the member activity post form.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_member_activity_post_form' );
}

/**
 * Fire specific hooks into the activity entry template
 *
 * @since 3.0.0
 *
 * @param string $when   Optional. Either 'before' or 'after'.
 * @param string $suffix Optional. Use it to add terms at the end of the hook name.
 */
function sz_nouveau_activity_hook( $when = '', $suffix = '' ) {
	$hook = array( 'sz' );

	if ( $when ) {
		$hook[] = $when;
	}

	// It's a activity entry hook
	$hook[] = 'activity';

	if ( $suffix ) {
		$hook[] = $suffix;
	}

	sz_nouveau_hook( $hook );
}

/**
 * Checks if an activity of the loop has some content.
 *
 * @since 3.0.0
 *
 * @return bool True if the activity has some content. False Otherwise.
 */
function sz_nouveau_activity_has_content() {
	return sz_activity_has_content() || (bool) has_action( 'sz_activity_entry_content' );
}

/**
 * Output the Activity content into the loop.
 *
 * @since 3.0.0
 */
function sz_nouveau_activity_content() {
	if ( sz_activity_has_content() ) {
		sz_activity_content_body();
	}

	/**
	 * Fires after the display of an activity entry content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_activity_entry_content' );
}

/**
 * Output the Activity timestamp into the sz-timestamp attribute.
 *
 * @since 3.0.0
 */
function sz_nouveau_activity_timestamp() {
	echo esc_attr( sz_nouveau_get_activity_timestamp() );
}

	/**
	 * Get the Activity timestamp.
	 *
	 * @since 3.0.0
	 *
	 * @return integer The Activity timestamp.
	 */
	function sz_nouveau_get_activity_timestamp() {
		/**
		 * Filter here to edit the activity timestamp.
		 *
		 * @since 3.0.0
		 *
		 * @param integer $value The Activity timestamp.
		 */
		return apply_filters( 'sz_nouveau_get_activity_timestamp', strtotime( sz_get_activity_date_recorded() ) );
	}

/**
 * Output the action buttons inside an Activity Loop.
 *
 * @since 3.0.0
 *
 * @param array $args See sz_nouveau_wrapper() for the description of parameters.
 */
function sz_nouveau_activity_entry_buttons( $args = array() ) {
	$output = join( ' ', sz_nouveau_get_activity_entry_buttons( $args ) );

	ob_start();

	/**
	 * Fires at the end of the activity entry meta data area.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_activity_entry_meta' );

	$output .= ob_get_clean();

	$has_content = trim( $output, ' ' );
	if ( ! $has_content ) {
		return;
	}

	if ( ! $args ) {
		$args = array( 'classes' => array( 'activity-meta' ) );
	}

	sz_nouveau_wrapper( array_merge( $args, array( 'output' => $output ) ) );
}

	/**
	 * Get the action buttons inside an Activity Loop,
	 *
	 * @todo This function is too large and needs refactoring and reviewing.
	 * @since 3.0.0
	 */
	function sz_nouveau_get_activity_entry_buttons( $args ) {
		$buttons = array();

		if ( ! isset( $GLOBALS['activities_template'] ) ) {
			return $buttons;
		}

		$activity_id    = sz_get_activity_id();
		$activity_type  = sz_get_activity_type();
		$parent_element = '';
		$button_element = 'a';

		if ( ! $activity_id ) {
			return $buttons;
		}

		/*
		 * If the container is set to 'ul' force the $parent_element to 'li',
		 * else use parent_element args if set.
		 *
		 * This will render li elements around anchors/buttons.
		 */
		if ( isset( $args['container'] ) && 'ul' === $args['container'] ) {
			$parent_element = 'li';
		} elseif ( ! empty( $args['parent_element'] ) ) {
			$parent_element = $args['parent_element'];
		}

		$parent_attr = ( ! empty( $args['parent_attr'] ) ) ? $args['parent_attr'] : array();

		/*
		 * If we have a arg value for $button_element passed through
		 * use it to default all the $buttons['button_element'] values
		 * otherwise default to 'a' (anchor)
		 * Or override & hardcode the 'element' string on $buttons array.
		 *
		 */
		if ( ! empty( $args['button_element'] ) ) {
			$button_element = $args['button_element'];
		}

		/*
		 * The view conversation button and the comment one are sharing
		 * the same id because when display_comments is on stream mode,
		 * it is not possible to comment an activity comment and as we
		 * are updating the links to avoid sorting the activity buttons
		 * for each entry of the loop, it's a convenient way to make
		 * sure the right button will be displayed.
		 */
		if ( $activity_type === 'activity_comment' ) {
			$buttons['activity_conversation'] = array(
				'id'                => 'activity_conversation',
				'position'          => 5,
				'component'         => 'activity',
				'parent_element'    => $parent_element,
				'parent_attr'       => $parent_attr,
				'must_be_logged_in' => false,
				'button_element'    => $button_element,
				'button_attr'       => array(
					'class'           => 'button view sz-secondary-action sz-tooltip',
					'data-sz-tooltip' => __( 'View Conversation', 'sportszone' ),
					),
				'link_text' => sprintf(
					'<span class="sz-screen-reader-text">%1$s</span>',
					__( 'View Conversation', 'sportszone' )
				),
			);

			// If button element set add url link to data-attr
			if ( 'button' === $button_element ) {
				$buttons['activity_conversation']['button_attr']['data-sz-url'] = sz_get_activity_thread_permalink();
			} else {
				$buttons['activity_conversation']['button_attr']['href'] = sz_get_activity_thread_permalink();
				$buttons['activity_conversation']['button_attr']['role'] = 'button';
			}

		/*
		 * We always create the Button to make sure we always have the right numbers of buttons,
		 * no matter the previous activity had less.
		 */
		} else {
			$buttons['activity_conversation'] =  array(
				'id'                => 'activity_conversation',
				'position'          => 5,
				'component'         => 'activity',
				'parent_element'    => $parent_element,
				'parent_attr'       => $parent_attr,
				'must_be_logged_in' => true,
				'button_element'    => $button_element,
				'button_attr'       => array(
					'id'              => 'acomment-comment-' . $activity_id,
					'class'           => 'button acomment-reply sz-primary-action sz-tooltip',
					'data-sz-tooltip' => _x( 'Comment', 'button', 'sportszone' ),
					'aria-expanded'   => 'false',
				),
				'link_text'  => sprintf(
					'<span class="sz-screen-reader-text">%1$s</span> <span class="comment-count">%2$s</span>',
					_x( 'Comment', 'link', 'sportszone' ),
					esc_html( sz_activity_get_comment_count() )
				),
			);

			// If button element set add href link to data-attr
			if ( 'button' === $button_element ) {
				$buttons['activity_conversation']['button_attr']['data-sz-url'] = sz_get_activity_comment_link();
			} else {
				$buttons['activity_conversation']['button_attr']['href'] = sz_get_activity_comment_link();
				$buttons['activity_conversation']['button_attr']['role'] = 'button';
			}

		}

		if ( sz_activity_can_favorite() ) {

			// If button element set attr needs to be data-* else 'href'
			if ( 'button' === $button_element ) {
				$key = 'data-sz-nonce';
			} else {
				$key = 'href';
			}

			if ( ! sz_get_activity_is_favorite() ) {
				$fav_args = array(
					'parent_element'   => $parent_element,
					'parent_attr'      => $parent_attr,
					'button_element'   => $button_element,
					'link_class'       => 'button fav sz-secondary-action sz-tooltip',
					'data_sz_tooltip'  => __( 'Mark as Favorite', 'sportszone' ),
					'link_text'        => __( 'Favorite', 'sportszone' ),
					'aria-pressed'     => 'false',
					'link_attr'        => sz_get_activity_favorite_link(),
				);

			} else {
				$fav_args = array(
					'parent_element'  => $parent_element,
					'parent_attr'     => $parent_attr,
					'button_element'  => $button_element,
					'link_class'      => 'button unfav sz-secondary-action sz-tooltip',
					'data_sz_tooltip' => __( 'Remove Favorite', 'sportszone' ),
					'link_text'       => __( 'Remove Favorite', 'sportszone' ),
					'aria-pressed'    => 'true',
					'link_attr'       => sz_get_activity_unfavorite_link(),
				);
			}

			$buttons['activity_favorite'] =  array(
				'id'                => 'activity_favorite',
				'position'          => 15,
				'component'         => 'activity',
				'parent_element'    => $parent_element,
				'parent_attr'       => $parent_attr,
				'must_be_logged_in' => true,
				'button_element'    => $fav_args['button_element'],
				'link_text'         => sprintf( '<span class="sz-screen-reader-text">%1$s</span>', esc_html( $fav_args['link_text'] ) ),
				'button_attr'       => array(
					$key              => $fav_args['link_attr'],
					'class'           => $fav_args['link_class'],
					'data-sz-tooltip' => $fav_args['data_sz_tooltip'],
					'aria-pressed'    => $fav_args['aria-pressed'],
				),
			);
		}

		// The delete button is always created, and removed later on if needed.
		$delete_args = array();

		/*
		 * As the delete link is filterable we need this workaround
		 * to try to intercept the edits the filter made and build
		 * a button out of it.
		 */
		if ( has_filter( 'sz_get_activity_delete_link' ) ) {
			preg_match( '/<a\s[^>]*>(.*)<\/a>/siU', sz_get_activity_delete_link(), $link );

			if ( ! empty( $link[0] ) && ! empty( $link[1] ) ) {
				$delete_args['link_text'] = $link[1];
				$subject = str_replace( $delete_args['link_text'], '', $link[0] );
			}

			preg_match_all( '/([\w\-]+)=([^"\'> ]+|([\'"]?)(?:[^\3]|\3+)+?\3)/', $subject, $attrs );

			if ( ! empty( $attrs[1] ) && ! empty( $attrs[2] ) ) {
				foreach ( $attrs[1] as $key_attr => $key_value ) {
					$delete_args[ 'link_'. $key_value ] = trim( $attrs[2][$key_attr], '"' );
				}
			}

			$delete_args = wp_parse_args( $delete_args, array(
				'link_text'   => '',
				'button_attr' => array(
					'link_id'         => '',
					'link_href'       => '',
					'link_class'      => '',
					'link_rel'        => 'nofollow',
					'data_sz_tooltip' => '',
				),
			) );
		}

		if ( empty( $delete_args['link_href'] ) ) {
			$delete_args = array(
				'button_element'  => $button_element,
				'link_id'         => '',
				'link_class'      => 'button item-button sz-secondary-action sz-tooltip delete-activity confirm',
				'link_rel'        => 'nofollow',
				'data_sz_tooltip' => _x( 'Delete', 'button', 'sportszone' ),
				'link_text'       => _x( 'Delete', 'button', 'sportszone' ),
				'link_href'       => sz_get_activity_delete_url(),
			);

			// If button element set add nonce link to data-attr attr
			if ( 'button' === $button_element ) {
				$delete_args['data-attr'] = sz_get_activity_delete_url();
				$delete_args['link_href'] = '';
			} else {
				$delete_args['link_href'] = sz_get_activity_delete_url();
				$delete_args['data-attr'] = '';
			}
		}

		$buttons['activity_delete'] = array(
			'id'                => 'activity_delete',
			'position'          => 35,
			'component'         => 'activity',
			'parent_element'    => $parent_element,
			'parent_attr'       => $parent_attr,
			'must_be_logged_in' => true,
			'button_element'    => $button_element,
			'button_attr'       => array(
				'id'              => $delete_args['link_id'],
				'href'            => $delete_args['link_href'],
				'class'           => $delete_args['link_class'],
				'data-sz-tooltip' => $delete_args['data_sz_tooltip'],
				'data-sz-nonce'   => $delete_args['data-attr'] ,
			),
			'link_text'  => sprintf( '<span class="sz-screen-reader-text">%s</span>', esc_html( $delete_args['data_sz_tooltip'] ) ),
		);

		// Add the Spam Button if supported
		if ( sz_is_akismet_active() && isset( sportszone()->activity->akismet ) && sz_activity_user_can_mark_spam() ) {
			$buttons['activity_spam'] = array(
				'id'                => 'activity_spam',
				'position'          => 45,
				'component'         => 'activity',
				'parent_element'    => $parent_element,
				'parent_attr'       => $parent_attr,
				'must_be_logged_in' => true,
				'button_element'    => $button_element,
				'button_attr'       => array(
					'class'           => 'sz-secondary-action spam-activity confirm button item-button sz-tooltip',
					'id'              => 'activity_make_spam_' . $activity_id,
					'data-sz-tooltip' => _x( 'Spam', 'button', 'sportszone' ),
					),
				'link_text'  => sprintf(
					/** @todo: use a specific css rule for this *************************************************************/
					'<span class="dashicons dashicons-flag" style="color:#a00;vertical-align:baseline;width:18px;height:18px" aria-hidden="true"></span><span class="sz-screen-reader-text">%s</span>',
					esc_html_x( 'Spam', 'button', 'sportszone' )
				),
			);

			// If button element, add nonce link to data attribute.
			if ( 'button' === $button_element ) {
				$data_element = 'data-sz-nonce';
			} else {
				$data_element = 'href';
			}

			$buttons['activity_spam']['button_attr'][ $data_element ] = wp_nonce_url(
				sz_get_root_domain() . '/' . sz_get_activity_slug() . '/spam/' . $activity_id . '/',
				'sz_activity_akismet_spam_' . $activity_id
			);
		}

		/**
		 * Filter to add your buttons, use the position argument to choose where to insert it.
		 *
		 * @since 3.0.0
		 *
		 * @param array $buttons     The list of buttons.
		 * @param int   $activity_id The current activity ID.
		 */
		$buttons_group = apply_filters( 'sz_nouveau_get_activity_entry_buttons', $buttons, $activity_id );

		if ( ! $buttons_group ) {
			return $buttons;
		}

		// It's the first entry of the loop, so build the Group and sort it
		if ( ! isset( sz_nouveau()->activity->entry_buttons ) || ! is_a( sz_nouveau()->activity->entry_buttons, 'SZ_Buttons_Group' ) ) {
			$sort = true;
			sz_nouveau()->activity->entry_buttons = new SZ_Buttons_Group( $buttons_group );

		// It's not the first entry, the order is set, we simply need to update the Buttons Group
		} else {
			$sort = false;
			sz_nouveau()->activity->entry_buttons->update( $buttons_group );
		}

		$return = sz_nouveau()->activity->entry_buttons->get( $sort );

		if ( ! $return ) {
			return array();
		}

		// Remove the Comment button if the user can't comment
		if ( ! sz_activity_can_comment() && $activity_type !== 'activity_comment' ) {
			unset( $return['activity_conversation'] );
		}

		// Remove the Delete button if the user can't delete
		if ( ! sz_activity_user_can_delete() ) {
			unset( $return['activity_delete'] );
		}

		if ( isset( $return['activity_spam'] ) && ! in_array( $activity_type, SZ_Akismet::get_activity_types() ) ) {
			unset( $return['activity_spam'] );
		}

		/**
		 * Leave a chance to adjust the $return
		 *
		 * @since 3.0.0
		 *
		 * @param array $return      The list of buttons ordered.
		 * @param int   $activity_id The current activity ID.
		 */
		do_action_ref_array( 'sz_nouveau_return_activity_entry_buttons', array( &$return, $activity_id ) );

		return $return;
	}

/**
 * Output Activity Comments if any
 *
 * @since 3.0.0
 */
function sz_nouveau_activity_comments() {
	global $activities_template;

	if ( empty( $activities_template->activity->children ) ) {
		return;
	}

	sz_nouveau_activity_recurse_comments( $activities_template->activity );
}

/**
 * Loops through a level of activity comments and loads the template for each.
 *
 * Note: This is an adaptation of the sz_activity_recurse_comments() SportsZone core function
 *
 * @since 3.0.0
 *
 * @param object $comment The activity object currently being recursed.
 */
function sz_nouveau_activity_recurse_comments( $comment ) {
	global $activities_template;

	if ( empty( $comment->children ) ) {
		return;
	}

	/**
	 * Filters the opening tag for the template that lists activity comments.
	 *
	 * @since 1.6.0
	 *
	 * @param string $value Opening tag for the HTML markup to use.
	 */
	echo apply_filters( 'sz_activity_recurse_comments_start_ul', '<ul>' );

	foreach ( (array) $comment->children as $comment_child ) {

		// Put the comment into the global so it's available to filters.
		$activities_template->activity->current_comment = $comment_child;

		/**
		 * Fires before the display of an activity comment.
		 *
		 * @since 1.5.0
		 */
		do_action( 'sz_before_activity_comment' );

		sz_get_template_part( 'activity/comment' );

		/**
		 * Fires after the display of an activity comment.
		 *
		 * @since 1.5.0
		 */
		do_action( 'sz_after_activity_comment' );

		unset( $activities_template->activity->current_comment );
	}

	/**
	 * Filters the closing tag for the template that list activity comments.
	 *
	 * @since 1.6.0
	 *
	 * @param string $value Closing tag for the HTML markup to use.
	 */
	echo apply_filters( 'sz_activity_recurse_comments_end_ul', '</ul>' );
}

/**
 * Ouptut the Activity comment action string
 *
 * @since 3.0.0
 */
function sz_nouveau_activity_comment_action() {
	echo sz_nouveau_get_activity_comment_action();
}

	/**
	 * Get the Activity comment action string
	 *
	 * @since 3.0.0
	 */
	function sz_nouveau_get_activity_comment_action() {

		/**
		 * Filter to edit the activity comment action.
		 *
		 * @since 3.0.0
		 *
		 * @param string $value HTML Output
		 */
		return apply_filters( 'sz_nouveau_get_activity_comment_action',
			/* translators: 1: user profile link, 2: user name, 3: activity permalink, 4: activity recorded date, 5: activity timestamp, 6: activity human time since */
			sprintf( __( '<a href="%1$s">%2$s</a> replied <a href="%3$s" class="activity-time-since"><time class="time-since" datetime="%4$s" data-sz-timestamp="%5$d">%6$s</time></a>', 'sportszone' ),
				esc_url( sz_get_activity_comment_user_link() ),
				esc_html( sz_get_activity_comment_name() ),
				esc_url( sz_get_activity_comment_permalink() ),
				esc_attr( sz_get_activity_comment_date_recorded_raw() ),
				esc_attr( strtotime( sz_get_activity_comment_date_recorded_raw() ) ),
				esc_attr( sz_get_activity_comment_date_recorded() )
		) );
	}

/**
 * Load the Activity comment form
 *
 * @since 3.0.0
 */
function sz_nouveau_activity_comment_form() {
	sz_get_template_part( 'activity/comment-form' );
}

/**
 * Output the action buttons for the activity comments
 *
 * @since 3.0.0
 *
 * @param array $args Optional. See sz_nouveau_wrapper() for the description of parameters.
 */
function sz_nouveau_activity_comment_buttons( $args = array() ) {
	$output = join( ' ', sz_nouveau_get_activity_comment_buttons( $args ) );

	ob_start();
	/**
	 * Fires after the defualt comment action options display.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_activity_comment_options' );

	$output     .= ob_get_clean();
	$has_content = trim( $output, ' ' );
	if ( ! $has_content ) {
		return;
	}

	if ( ! $args ) {
		$args = array( 'classes' => array( 'acomment-options' ) );
	}

	sz_nouveau_wrapper( array_merge( $args, array( 'output' => $output ) ) );
}

	/**
	 * Get the action buttons for the activity comments
	 *
	 * @since 3.0.0
	 *
	 * @param array $args Optional. See sz_nouveau_wrapper() for the description of parameters.
	 *
	 * @return array
	 */
	function sz_nouveau_get_activity_comment_buttons($args) {
		$buttons = array();

		if ( ! isset( $GLOBALS['activities_template'] ) ) {
			return $buttons;
		}

		$activity_comment_id = sz_get_activity_comment_id();
		$activity_id         = sz_get_activity_id();

		if ( ! $activity_comment_id || ! $activity_id ) {
			return $buttons;
		}

		/*
		 * If the 'container' is set to 'ul'
		 * set a var $parent_element to li
		 * otherwise simply pass any value found in args
		 * or set var false.
		 */
		if ( 'ul' === $args['container']  ) {
			$parent_element = 'li';
		} elseif ( ! empty( $args['parent_element'] ) ) {
			$parent_element = $args['parent_element'];
		} else {
			$parent_element = false;
		}

		$parent_attr = ( ! empty( $args['parent_attr'] ) ) ? $args['parent_attr'] : array();

		/*
		 * If we have a arg value for $button_element passed through
		 * use it to default all the $buttons['button_element'] values
		 * otherwise default to 'a' (anchor).
		 */
		if ( ! empty( $args['button_element'] ) ) {
			$button_element = $args['button_element'] ;
		} else {
			$button_element = 'a';
		}

		$buttons = array(
			'activity_comment_reply' => array(
				'id'                => 'activity_comment_reply',
				'position'          => 5,
				'component'         => 'activity',
				'must_be_logged_in' => true,
				'parent_element'    => $parent_element,
				'parent_attr'       => $parent_attr,
				'button_element'    => $button_element,
				'link_text'         => _x( 'Reply', 'link', 'sportszone' ),
				'button_attr'       => array(
					'class' => "acomment-reply sz-primary-action",
					'id'    => sprintf( 'acomment-reply-%1$s-from-%2$s', $activity_id, $activity_comment_id ),
				),
			),
			'activity_comment_delete' => array(
				'id'                => 'activity_comment_delete',
				'position'          => 15,
				'component'         => 'activity',
				'must_be_logged_in' => true,
				'parent_element'    => $parent_element,
				'parent_attr'       => $parent_attr,
				'button_element'    => $button_element,
				'link_text'         => _x( 'Delete', 'link', 'sportszone' ),
				'button_attr'       => array(
					'class' => 'delete acomment-delete confirm sz-secondary-action',
					'rel'   => 'nofollow',
				),
			),
		);

		// If button element set add nonce link to data-attr attr
		if ( 'button' === $button_element ) {
			$buttons['activity_comment_reply']['button_attr']['data-sz-act-reply-nonce'] = sprintf( '#acomment-%s', $activity_comment_id );
			$buttons['activity_comment_delete']['button_attr']['data-sz-act-reply-delete-nonce'] = sz_get_activity_comment_delete_link();
		} else {
			$buttons['activity_comment_reply']['button_attr']['href'] = sprintf( '#acomment-%s', $activity_comment_id );
			$buttons['activity_comment_delete']['button_attr']['href'] = sz_get_activity_comment_delete_link();
		}

		// Add the Spam Button if supported
		if ( sz_is_akismet_active() && isset( sportszone()->activity->akismet ) && sz_activity_user_can_mark_spam() ) {
			$buttons['activity_comment_spam'] = array(
				'id'                => 'activity_comment_spam',
				'position'          => 25,
				'component'         => 'activity',
				'must_be_logged_in' => true,
				'parent_element'    => $parent_element,
				'parent_attr'       => $parent_attr,
				'button_element'    => $button_element,
				'link_text'         => _x( 'Spam', 'link', 'sportszone' ),
				'button_attr'       => array(
					'id'     => "activity_make_spam_{$activity_comment_id}",
					'class'  => 'sz-secondary-action spam-activity-comment confirm',
					'rel'    => 'nofollow',
				),
			);

			// If button element set add nonce link to data-attr attr
			if ( 'button' === $button_element ) {
				$data_element = 'data-sz-act-spam-nonce';
			} else {
				$data_element = 'href';
			}

			$buttons['activity_comment_spam']['button_attr'][ $data_element ] = wp_nonce_url(
				sz_get_root_domain() . '/' . sz_get_activity_slug() . '/spam/' . $activity_comment_id . '/?cid=' . $activity_comment_id,
				'sz_activity_akismet_spam_' . $activity_comment_id
			);
		}

		/**
		 * Filter to add your buttons, use the position argument to choose where to insert it.
		 *
		 * @since 3.0.0
		 *
		 * @param array $buttons             The list of buttons.
		 * @param int   $activity_comment_id The current activity comment ID.
		 * @param int   $activity_id         The current activity ID.
		 */
		$buttons_group = apply_filters( 'sz_nouveau_get_activity_comment_buttons', $buttons, $activity_comment_id, $activity_id );

		if ( ! $buttons_group ) {
			return $buttons;
		}

		// It's the first comment of the loop, so build the Group and sort it
		if ( ! isset( sz_nouveau()->activity->comment_buttons ) || ! is_a( sz_nouveau()->activity->comment_buttons, 'SZ_Buttons_Group' ) ) {
			$sort = true;
			sz_nouveau()->activity->comment_buttons = new SZ_Buttons_Group( $buttons_group );

		// It's not the first comment, the order is set, we simply need to update the Buttons Group
		} else {
			$sort = false;
			sz_nouveau()->activity->comment_buttons->update( $buttons_group );
		}

		$return = sz_nouveau()->activity->comment_buttons->get( $sort );

		if ( ! $return ) {
			return array();
		}

		/**
		 * If post comment / Activity comment sync is on, it's safer
		 * to unset the comment button just before returning it.
		 */
		if ( ! sz_activity_can_comment_reply( sz_activity_current_comment() ) ) {
			unset( $return['activity_comment_reply'] );
		}

		/**
		 * If there was an activity of the user before one af another
		 * user as we're updating buttons, we need to unset the delete link
		 */
		if ( ! sz_activity_user_can_delete() ) {
			unset( $return['activity_comment_delete'] );
		}

		if ( isset( $return['activity_comment_spam'] ) && ( ! sz_activity_current_comment() || ! in_array( sz_activity_current_comment()->type, SZ_Akismet::get_activity_types(), true ) ) ) {
			unset( $return['activity_comment_spam'] );
		}

		/**
		 * Leave a chance to adjust the $return
		 *
		 * @since 3.0.0
		 *
		 * @param array $return              The list of buttons ordered.
		 * @param int   $activity_comment_id The current activity comment ID.
		 * @param int   $activity_id         The current activity ID.
		 */
		do_action_ref_array( 'sz_nouveau_return_activity_comment_buttons', array( &$return, $activity_comment_id, $activity_id ) );

		return $return;
	}
