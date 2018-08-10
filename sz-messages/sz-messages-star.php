<?php
/**
 * Functions related to starring private messages.
 *
 * @package SportsZone
 * @subpackage MessagesStar
 * @since 2.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/** UTILITY **************************************************************/

/**
 * Return the starred messages slug. Defaults to 'starred'.
 *
 * @since 2.3.0
 *
 * @return string
 */
function sz_get_messages_starred_slug() {
	/**
	 * Filters the starred message slug.
	 *
	 * @since 2.3.0
	 *
	 * @param string
	 */
	return sanitize_title( apply_filters( 'sz_get_messages_starred_slug', 'starred' ) );
}

/**
 * Function to determine if a message ID is starred.
 *
 * @since 2.3.0
 *
 * @param  int $mid     The message ID. Please note that this isn't the message thread ID.
 * @param  int $user_id The user ID.
 * @return bool
 */
function sz_messages_is_message_starred( $mid = 0, $user_id = 0 ) {
	if ( empty( $user_id ) ) {
		$user_id = sz_displayed_user_id();
	}

	if ( empty( $mid ) ) {
		return false;
	}

	$starred = array_flip( (array) sz_messages_get_meta( $mid, 'starred_by_user', false ) );

	if ( isset( $starred[$user_id] ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Output the link or raw URL for starring or unstarring a message.
 *
 * @since 2.3.0
 *
 * @param array $args See sz_get_the_message_star_action_link() for full documentation.
 */
function sz_the_message_star_action_link( $args = array() ) {
	echo sz_get_the_message_star_action_link( $args );
}
	/**
	 * Return the link or raw URL for starring or unstarring a message.
	 *
	 * @since 2.3.0
	 *
	 * @param array $args {
	 *     Array of arguments.
	 *     @type int    $user_id       The user ID. Defaults to the logged-in user ID.
	 *     @type int    $thread_id     The message thread ID. Default: 0. If not zero, this takes precedence over
	 *                                 $message_id.
	 *     @type int    $message_id    The individual message ID. If on a single thread page, defaults to the
	 *                                 current message ID in the message loop.
	 *     @type bool   $url_only      Whether to return the URL only. If false, returns link with markup.
	 *                                 Default: false.
	 *     @type string $text_unstar   Link text for the 'unstar' action. Only applicable if $url_only is false.
	 *     @type string $text_star     Link text for the 'star' action. Only applicable if $url_only is false.
	 *     @type string $title_unstar  Link title for the 'unstar' action. Only applicable if $url_only is false.
	 *     @type string $title_star    Link title for the 'star' action. Only applicable if $url_only is false.
	 *     @type string $title_unstar_thread Link title for the 'unstar' action when displayed in a thread loop.
	 *                                       Only applicable if $message_id is set and if $url_only is false.
	 *     @type string $title_star_thread   Link title for the 'star' action when displayed in a thread loop.
	 *                                       Only applicable if $message_id is set and if $url_only is false.
	 * }
	 * @return string
	 */
	function sz_get_the_message_star_action_link( $args = array() ) {

		// Default user ID.
		$user_id = sz_displayed_user_id()
			? sz_displayed_user_id()
			: sz_loggedin_user_id();

		$r = sz_parse_args( $args, array(
			'user_id'             => (int) $user_id,
			'thread_id'           => 0,
			'message_id'          => (int) sz_get_the_thread_message_id(),
			'url_only'            => false,
			'text_unstar'         => __( 'Unstar',      'sportszone' ),
			'text_star'           => __( 'Star',        'sportszone' ),
			'title_unstar'        => __( 'Starred',     'sportszone' ),
			'title_star'          => __( 'Not starred', 'sportszone' ),
			'title_unstar_thread' => __( 'Remove all starred messages in this thread', 'sportszone' ),
			'title_star_thread'   => __( 'Star the first message in this thread',      'sportszone' ),
		), 'messages_star_action_link' );

		// Check user ID and determine base user URL.
		switch ( $r['user_id'] ) {

			// Current user.
			case sz_loggedin_user_id() :
				$user_domain = sz_loggedin_user_domain();
				break;

			// Displayed user.
			case sz_displayed_user_id() :
				$user_domain = sz_displayed_user_domain();
				break;

			// Empty or other.
			default :
				$user_domain = sz_core_get_user_domain( $r['user_id'] );
				break;
		}

		// Bail if no user domain was calculated.
		if ( empty( $user_domain ) ) {
			return '';
		}

		// Define local variables.
		$retval = $bulk_attr = '';

		// Thread ID.
		if ( (int) $r['thread_id'] > 0 ) {

			// See if we're in the loop.
			if ( sz_get_message_thread_id() == $r['thread_id'] ) {

				// Grab all message ids.
				$mids = wp_list_pluck( $GLOBALS['messages_template']->thread->messages, 'id' );

				// Make sure order is ASC.
				// Order is DESC when used in the thread loop by default.
				$mids = array_reverse( $mids );

			// Pull up the thread.
			} else {
				$thread = new SZ_Messages_Thread( $r['thread_id'] );
				$mids   = wp_list_pluck( $thread->messages, 'id' );
			}

			$is_starred = false;
			$message_id = 0;
			foreach ( $mids as $mid ) {

				// Try to find the first msg that is starred in a thread.
				if ( true === sz_messages_is_message_starred( $mid ) ) {
					$is_starred = true;
					$message_id = $mid;
					break;
				}
			}

			// No star, so default to first message in thread.
			if ( empty( $message_id ) ) {
				$message_id = $mids[0];
			}

			$message_id = (int) $message_id;

			// Nonce.
			$nonce = wp_create_nonce( "sz-messages-star-{$message_id}" );

			if ( true === $is_starred ) {
				$action    = 'unstar';
				$bulk_attr = ' data-star-bulk="1"';
				$retval    = $user_domain . sz_get_messages_slug() . '/unstar/' . $message_id . '/' . $nonce . '/all/';
			} else {
				$action    = 'star';
				$retval    = $user_domain . sz_get_messages_slug() . '/star/' . $message_id . '/' . $nonce . '/';
			}

			$title = $r["title_{$action}_thread"];

		// Message ID.
		} else {
			$message_id = (int) $r['message_id'];
			$is_starred = sz_messages_is_message_starred( $message_id );
			$nonce      = wp_create_nonce( "sz-messages-star-{$message_id}" );

			if ( true === $is_starred ) {
				$action = 'unstar';
				$retval = $user_domain . sz_get_messages_slug() . '/unstar/' . $message_id . '/' . $nonce . '/';
			} else {
				$action = 'star';
				$retval = $user_domain . sz_get_messages_slug() . '/star/' . $message_id . '/' . $nonce . '/';
			}

			$title = $r["title_{$action}"];
		}

		/**
		 * Filters the star action URL for starring / unstarring a message.
		 *
		 * @since 2.3.0
		 *
		 * @param string $retval URL for starring / unstarring a message.
		 * @param array  $r      Parsed link arguments. See $args in sz_get_the_message_star_action_link().
		 */
		$retval = esc_url( apply_filters( 'sz_get_the_message_star_action_urlonly', $retval, $r ) );
		if ( true === (bool) $r['url_only'] ) {
			return $retval;
		}

		/**
		 * Filters the star action link, including markup.
		 *
		 * @since 2.3.0
		 *
		 * @param string $retval Link for starring / unstarring a message, including markup.
		 * @param array  $r      Parsed link arguments. See $args in sz_get_the_message_star_action_link().
		 */
		return apply_filters( 'sz_get_the_message_star_action_link', '<a data-sz-tooltip="' . esc_attr( $title ) . '" class="sz-tooltip message-action-' . esc_attr( $action ) . '" data-star-status="' . esc_attr( $action ) .'" data-star-nonce="' . esc_attr( $nonce ) . '"' . $bulk_attr . ' data-message-id="' . esc_attr( (int) $message_id ) . '" href="' . $retval . '" role="button" aria-pressed="false"><span class="icon"></span> <span class="sz-screen-reader-text">' . $r['text_' . $action] . '</span></a>', $r );
	}

/**
 * Save or delete star message meta according to a message's star status.
 *
 * @since 2.3.0
 *
 * @param array $args {
 *     Array of arguments.
 *     @type string $action     The star action. Either 'star' or 'unstar'. Default: 'star'.
 *     @type int    $thread_id  The message thread ID. Default: 0. If not zero, this takes precedence over
 *                              $message_id.
 *     @type int    $message_id The indivudal message ID to star or unstar.  Default: 0.
 *     @type int    $user_id    The user ID. Defaults to the logged-in user ID.
 *     @type bool   $bulk       Whether to mark all messages in a thread as a certain action. Only relevant
 *                              when $action is 'unstar' at the moment. Default: false.
 * }
 * @return bool
 */
function sz_messages_star_set_action( $args = array() ) {
	$r = wp_parse_args( $args, array(
		'action'     => 'star',
		'thread_id'  => 0,
		'message_id' => 0,
		'user_id'    => sz_displayed_user_id(),
		'bulk'       => false
	) );

	// Set thread ID.
	if ( ! empty( $r['thread_id'] ) ) {
		$thread_id = (int) $r['thread_id'];
	} else {
		$thread_id = messages_get_message_thread_id( $r['message_id'] );
	}
	if ( empty( $thread_id ) ) {
		return false;
	}

	// Check if user has access to thread.
	if( ! messages_check_thread_access( $thread_id, $r['user_id'] ) ) {
		return false;
	}

	$is_starred = sz_messages_is_message_starred( $r['message_id'], $r['user_id'] );

	// Star.
	if ( 'star' == $r['action'] ) {
		if ( true === $is_starred ) {
			return true;
		} else {
			sz_messages_add_meta( $r['message_id'], 'starred_by_user', $r['user_id'] );
			return true;
		}
	// Unstar.
	} else {
		// Unstar one message.
		if ( false === $r['bulk'] ) {
			if ( false === $is_starred ) {
				return true;
			} else {
				sz_messages_delete_meta( $r['message_id'], 'starred_by_user', $r['user_id'] );
				return true;
			}

		// Unstar all messages in a thread.
		} else {
			$thread = new SZ_Messages_Thread( $thread_id );
			$mids = wp_list_pluck( $thread->messages, 'id' );

			foreach ( $mids as $mid ) {
				if ( true === sz_messages_is_message_starred( $mid, $r['user_id'] ) ) {
					sz_messages_delete_meta( $mid, 'starred_by_user', $r['user_id'] );
				}
			}

			return true;
		}
	}
}

/** HOOKS ****************************************************************/

/**
 * Enqueues the dashicons font.
 *
 * The dashicons font is used for the star / unstar icon.
 *
 * @since 2.3.0
 */
function sz_messages_star_enqueue_scripts() {
	if ( ! sz_is_user_messages() ) {
		return;
	}

	wp_enqueue_style( 'dashicons' );
}
add_action( 'sz_enqueue_scripts', 'sz_messages_star_enqueue_scripts' );

/**
 * Add the "Add star" and "Remove star" options to the bulk management list.
 *
 * @since 2.3.0
 */
function sz_messages_star_bulk_management_dropdown() {
?>

	<option value="star"><?php _e( 'Add star', 'sportszone' ); ?></option>
	<option value="unstar"><?php _e( 'Remove star', 'sportszone' ); ?></option>

<?php
}
add_action( 'sz_messages_bulk_management_dropdown', 'sz_messages_star_bulk_management_dropdown', 1 );

/**
 * Add CSS class for the current message depending on starred status.
 *
 * @since 2.3.0
 *
 * @param  array $retval Current CSS classes.
 * @return array
 */
function sz_messages_star_message_css_class( $retval = array() ) {
	if ( true === sz_messages_is_message_starred( sz_get_the_thread_message_id() ) ) {
		$status = 'starred';
	} else {
		$status = 'not-starred';
	}

	// Add css class based on star status for the current message.
	$retval[] = "message-{$status}";

	return $retval;
}
add_filter( 'sz_get_the_thread_message_css_class', 'sz_messages_star_message_css_class' );

/**
 * Filter message threads by those starred by the logged-in user.
 *
 * @since 2.3.0
 *
 * @param  array $r Current message thread arguments.
 * @return array $r Array of starred message threads.
 */
function sz_messages_filter_starred_message_threads( $r = array() ) {
	$r['box'] = 'starred';
	$r['meta_query'] = array( array(
		'key'   => 'starred_by_user',
		'value' => $r['user_id']
	) );

	return $r;
}
