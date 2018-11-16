<?php
/**
 * AJAX Functions
 *
 * All of these functions enhance the responsiveness of the user interface in
 * the default theme by adding AJAX functionality.
 *
 * For more information on how the custom AJAX functions work, see
 * http://codex.wordpress.org/AJAX_in_Plugins.
 *
 * @package SportsZone
 * @since SportsZone (1.2)
 * @subpackage BP-Default
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register AJAX handlers for BP Default theme functionality.
 *
 * This function is registered to the after_setup_theme hook with priority 20 as
 * this file is included in a function hooked to after_setup_theme at priority 10.
 *
 * @since SportsZone (1.6)
 */
function sz_dtheme_register_actions() {
	$actions = array(
		// Directory filters
		'blogs_filter'    => 'sz_dtheme_object_template_loader',
		'forums_filter'   => 'sz_dtheme_object_template_loader',
		'groups_filter'   => 'sz_dtheme_object_template_loader',
		'events_filter'   => 'sz_dtheme_object_template_loader',
		'members_filter'  => 'sz_dtheme_object_template_loader',
		'messages_filter' => 'sz_dtheme_messages_template_loader',

		// Friends
		'accept_friendship' => 'sz_dtheme_ajax_accept_friendship',
		'addremove_friend'  => 'sz_dtheme_ajax_addremove_friend',
		'reject_friendship' => 'sz_dtheme_ajax_reject_friendship',

		// Activity
		'activity_get_older_updates'  => 'sz_dtheme_activity_template_loader',
		'activity_mark_fav'           => 'sz_dtheme_mark_activity_favorite',
		'activity_mark_unfav'         => 'sz_dtheme_unmark_activity_favorite',
		'activity_widget_filter'      => 'sz_dtheme_activity_template_loader',
		'delete_activity'             => 'sz_dtheme_delete_activity',
		'delete_activity_comment'     => 'sz_dtheme_delete_activity_comment',
		'get_single_activity_content' => 'sz_dtheme_get_single_activity_content',
		'new_activity_comment'        => 'sz_dtheme_new_activity_comment',
		'post_update'                 => 'sz_dtheme_post_update',
		'sz_spam_activity'            => 'sz_dtheme_spam_activity',
		'sz_spam_activity_comment'    => 'sz_dtheme_spam_activity',

		// Groups
		'groups_invite_user' => 'sz_dtheme_ajax_invite_user',
		'joinleave_group'    => 'sz_dtheme_ajax_joinleave_group',

		// Messages
		'messages_autocomplete_results' => 'sz_dtheme_ajax_messages_autocomplete_results',
		'messages_close_notice'         => 'sz_dtheme_ajax_close_notice',
		'messages_delete'               => 'sz_dtheme_ajax_messages_delete',
		'messages_markread'             => 'sz_dtheme_ajax_message_markread',
		'messages_markunread'           => 'sz_dtheme_ajax_message_markunread',
		'messages_send_reply'           => 'sz_dtheme_ajax_messages_send_reply',
	);

	/**
	 * Register all of these AJAX handlers
	 *
	 * The "wp_ajax_" action is used for logged in users, and "wp_ajax_nopriv_"
	 * executes for users that aren't logged in. This is for backpat with BP <1.6.
	 */
	foreach( $actions as $name => $function ) {
		add_action( 'wp_ajax_'        . $name, $function );
		add_action( 'wp_ajax_nopriv_' . $name, $function );
	}
}
add_action( 'after_setup_theme', 'sz_dtheme_register_actions', 20 );

/**
 * This function looks scarier than it actually is. :)
 * Each object loop (activity/members/groups/blogs/forums) contains default parameters to
 * show specific information based on the page we are currently looking at.
 * The following function will take into account any cookies set in the JS and allow us
 * to override the parameters sent. That way we can change the results returned without reloading the page.
 * By using cookies we can also make sure that user settings are retained across page loads.
 *
 * @return string Query string for the activity/members/groups/blogs/forums loops
 * @since SportsZone (1.2)
 */
function sz_dtheme_ajax_querystring( $query_string, $object ) {
	if ( empty( $object ) )
		return '';

	// Set up the cookies passed on this AJAX request. Store a local var to avoid conflicts
	if ( ! empty( $_POST['cookie'] ) )
		$_SZ_COOKIE = wp_parse_args( str_replace( '; ', '&', urldecode( $_POST['cookie'] ) ) );
	else
		$_SZ_COOKIE = &$_COOKIE;

	$qs = array();

	/**
	 * Check if any cookie values are set. If there are then override the default params passed to the
	 * template loop
	 */

	// Activity stream filtering on action
	if ( ! empty( $_SZ_COOKIE['sz-' . $object . '-filter'] ) && '-1' != $_SZ_COOKIE['sz-' . $object . '-filter'] ) {
		$qs[] = 'type='   . urlencode( $_SZ_COOKIE['sz-' . $object . '-filter'] );
		$qs[] = 'action=' . urlencode( $_SZ_COOKIE['sz-' . $object . '-filter'] );
	}

	if ( ! empty( $_SZ_COOKIE['sz-' . $object . '-scope'] ) ) {
		if ( 'personal' == $_SZ_COOKIE['sz-' . $object . '-scope'] ) {
			$user_id = ( sz_displayed_user_id() ) ? sz_displayed_user_id() : sz_loggedin_user_id();
			$qs[] = 'user_id=' . $user_id;
		}

		// Activity stream scope only on activity directory.
		if ( 'all' != $_SZ_COOKIE['sz-' . $object . '-scope'] && ! sz_displayed_user_id() && ! sz_is_single_item() )
			$qs[] = 'scope=' . urlencode( $_SZ_COOKIE['sz-' . $object . '-scope'] );
	}

	// If page and search_terms have been passed via the AJAX post request, use those.
	if ( ! empty( $_POST['page'] ) && '-1' != $_POST['page'] )
		$qs[] = 'page=' . absint( $_POST['page'] );

	// exludes activity just posted and avoids duplicate ids
	if ( ! empty( $_POST['exclude_just_posted'] ) ) {
		$just_posted = wp_parse_id_list( $_POST['exclude_just_posted'] );
		$qs[] = 'exclude=' . implode( ',', $just_posted );
	}

	$object_search_text = sz_get_search_default_text( $object );
 	if ( ! empty( $_POST['search_terms'] ) && $object_search_text != $_POST['search_terms'] && 'false' != $_POST['search_terms'] && 'undefined' != $_POST['search_terms'] )
		$qs[] = 'search_terms=' . urlencode( $_POST['search_terms'] );

	// Now pass the querystring to override default values.
	$query_string = empty( $qs ) ? '' : join( '&', (array) $qs );

	$object_filter = '';
	if ( isset( $_SZ_COOKIE['sz-' . $object . '-filter'] ) )
		$object_filter = $_SZ_COOKIE['sz-' . $object . '-filter'];

	$object_scope = '';
	if ( isset( $_SZ_COOKIE['sz-' . $object . '-scope'] ) )
		$object_scope = $_SZ_COOKIE['sz-' . $object . '-scope'];

	$object_page = '';
	if ( isset( $_SZ_COOKIE['sz-' . $object . '-page'] ) )
		$object_page = $_SZ_COOKIE['sz-' . $object . '-page'];

	$object_search_terms = '';
	if ( isset( $_SZ_COOKIE['sz-' . $object . '-search-terms'] ) )
		$object_search_terms = $_SZ_COOKIE['sz-' . $object . '-search-terms'];

	$object_extras = '';
	if ( isset( $_SZ_COOKIE['sz-' . $object . '-extras'] ) )
		$object_extras = $_SZ_COOKIE['sz-' . $object . '-extras'];

	return apply_filters( 'sz_dtheme_ajax_querystring', $query_string, $object, $object_filter, $object_scope, $object_page, $object_search_terms, $object_extras );
}
add_filter( 'sz_ajax_querystring', 'sz_dtheme_ajax_querystring', 10, 2 );

/**
 * Load the template loop for the current object.
 *
 * @return string Prints template loop for the specified object
 * @since SportsZone (1.2)
 */
function sz_dtheme_object_template_loader() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	// Bail if no object passed
	if ( empty( $_POST['object'] ) )
		return;

	// Sanitize the object
	$object = sanitize_title( $_POST['object'] );

	// Bail if object is not an active component
	if ( ! sz_is_active( $object ) )
		return;

 	/**
	 * AJAX requests happen too early to be seen by sz_update_is_directory()
	 * so we do it manually here to ensure templates load with the correct
	 * context. Without this check, templates will load the 'single' version
	 * of themselves rather than the directory version.
	 */
	if ( ! sz_current_action() )
		sz_update_is_directory( true, sz_current_component() );

	// Locate the object template
	locate_template( array( "$object/$object-loop.php" ), true );
	exit;
}

/**
 * Load messages template loop when searched on the private message page
 *
 * @return string Prints template loop for the Messages component
 * @since SportsZone (1.6)
 */
function sz_dtheme_messages_template_loader(){
	locate_template( array( 'members/single/messages/messages-loop.php' ), true );
	exit;
}

/**
 * Load the activity loop template when activity is requested via AJAX,
 *
 * @return string JSON object containing 'contents' (output of the template loop for the Activity component) and 'feed_url' (URL to the relevant RSS feed).
 * @since SportsZone (1.2)
 */
function sz_dtheme_activity_template_loader() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	$scope = '';
	if ( ! empty( $_POST['scope'] ) )
		$scope = $_POST['scope'];

	// We need to calculate and return the feed URL for each scope
	switch ( $scope ) {
		case 'friends':
			$feed_url = sz_loggedin_user_domain() . sz_get_activity_slug() . '/friends/feed/';
			break;
		case 'groups':
			$feed_url = sz_loggedin_user_domain() . sz_get_activity_slug() . '/groups/feed/';
			break;
		case 'events':
			$feed_url = sz_loggedin_user_domain() . sz_get_activity_slug() . '/events/feed/';
			break;
		case 'favorites':
			$feed_url = sz_loggedin_user_domain() . sz_get_activity_slug() . '/favorites/feed/';
			break;
		case 'mentions':
			$feed_url = sz_loggedin_user_domain() . sz_get_activity_slug() . '/mentions/feed/';

			if ( isset( $_POST['_wpnonce_activity_filter'] ) && wp_verify_nonce( wp_unslash( $_POST['_wpnonce_activity_filter'] ), 'activity_filter' ) ) {
				sz_activity_clear_new_mentions( sz_loggedin_user_id() );
			}
			break;
		default:
			$feed_url = home_url( sz_get_activity_root_slug() . '/feed/' );
			break;
	}

	// Buffer the loop in the template to a var for JS to spit out.
	ob_start();
	locate_template( array( 'activity/activity-loop.php' ), true );
	$result['contents'] = ob_get_contents();
	$result['feed_url'] = apply_filters( 'sz_dtheme_activity_feed_url', $feed_url, $scope );
	ob_end_clean();

	exit( json_encode( $result ) );
}

/**
 * Processes Activity updates received via a POST request.
 *
 * @return string HTML
 * @since SportsZone (1.2)
 */
function sz_dtheme_post_update() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	// Check the nonce
	check_admin_referer( 'post_update', '_wpnonce_post_update' );

	if ( ! is_user_logged_in() )
		exit( '-1' );

	if ( empty( $_POST['content'] ) )
		exit( '-1<div id="message" class="error"><p>' . __( 'Please enter some content to post.', 'sportszone' ) . '</p></div>' );

	$activity_id = 0;
	if ( empty( $_POST['object'] ) && sz_is_active( 'activity' ) ) {
		$activity_id = sz_activity_post_update( array( 'content' => $_POST['content'], 'error_type' => 'wp_error' ) );

	} elseif ( $_POST['object'] == 'groups' ) {
		if ( ! empty( $_POST['item_id'] ) && sz_is_active( 'groups' ) )
			$activity_id = groups_post_update( array( 'content' => $_POST['content'], 'group_id' => $_POST['item_id'], 'error_type' => 'wp_error' ) );

	} elseif ( $_POST['object'] == 'events' ) {
		if ( ! empty( $_POST['item_id'] ) && sz_is_active( 'events' ) )
			$activity_id = events_post_update( array( 'content' => $_POST['content'], 'event_id' => $_POST['item_id'], 'error_type' => 'wp_error' ) );

	} else {
		$activity_id = apply_filters( 'sz_activity_custom_update', $_POST['object'], $_POST['item_id'], $_POST['content'] );
	}

	if ( false === $activity_id ) {
		exit( '-1<div id="message" class="error"><p>' . __( 'There was a problem posting your update, please try again.', 'sportszone' ) . '</p></div>' );
	} elseif ( is_wp_error( $activity_id ) && $activity_id->get_error_code() ) {
		exit( '-1<div id="message" class="error sz-ajax-message"><p>' . $activity_id->get_error_message() . '</p></div>' );
	}

	if ( sz_has_activities ( 'include=' . $activity_id ) ) {
		while ( sz_activities() ) {
			sz_the_activity();
			locate_template( array( 'activity/entry.php' ), true );
		}
	}

	exit;
}

/**
 * Posts new Activity comments received via a POST request.
 *
 * @global SZ_Activity_Template $activities_template
 * @return string HTML
 * @since SportsZone (1.2)
 */
function sz_dtheme_new_activity_comment() {
	global $activities_template;

	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	// Check the nonce
	check_admin_referer( 'new_activity_comment', '_wpnonce_new_activity_comment' );

	if ( ! is_user_logged_in() )
		exit( '-1' );

	if ( empty( $_POST['content'] ) )
		exit( '-1<div id="message" class="error"><p>' . __( 'Please do not leave the comment area blank.', 'sportszone' ) . '</p></div>' );

	if ( empty( $_POST['form_id'] ) || empty( $_POST['comment_id'] ) || ! is_numeric( $_POST['form_id'] ) || ! is_numeric( $_POST['comment_id'] ) )
		exit( '-1<div id="message" class="error"><p>' . __( 'There was an error posting that reply, please try again.', 'sportszone' ) . '</p></div>' );

	$comment_id = sz_activity_new_comment( array(
		'activity_id' => $_POST['form_id'],
		'content'     => $_POST['content'],
		'parent_id'   => $_POST['comment_id'],
		'error_type'  => 'wp_error'
	) );

	if ( false === $comment_id ) {
		exit( '-1<div id="message" class="error"><p>' . __( 'There was an error posting that reply, please try again.', 'sportszone' ) . '</p></div>' );
	} elseif ( is_wp_error( $comment_id ) ) {
		exit( '-1<div id="message" class="error sz-ajax-message"><p>' . esc_html( $comment_id->get_error_message() ) . '</p></div>' );
	}

	// Load the new activity item into the $activities_template global
	sz_has_activities( 'display_comments=stream&hide_spam=false&show_hidden=true&include=' . $comment_id );

	// Swap the current comment with the activity item we just loaded
	$activities_template->activity                  = new stdClass;
	$activities_template->activity->id              = $activities_template->activities[0]->item_id;
	$activities_template->activity->current_comment = $activities_template->activities[0];

	$template = locate_template( 'activity/comment.php', false, false );

	/**
	 * Backward compatibility. In older versions of BP, the markup was
	 * generated in the PHP instead of a template. This ensures that
	 * older themes (which are not children of sz-default and won't
	 * have the new template) will still work.
	 */
	if ( empty( $template ) )
		$template = sportszone()->plugin_dir . '/sz-themes/sz-default/activity/comment.php';

	load_template( $template, false );

	unset( $activities_template );
	exit;
}

/**
 * Deletes an Activity item received via a POST request.
 *
 * @return mixed String on error, void on success
 * @since SportsZone (1.2)
 */
function sz_dtheme_delete_activity() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	// Check the nonce
	check_admin_referer( 'sz_activity_delete_link' );

	if ( ! is_user_logged_in() )
		exit( '-1' );

	if ( empty( $_POST['id'] ) || ! is_numeric( $_POST['id'] ) )
		exit( '-1' );

	$activity = new SZ_Activity_Activity( (int) $_POST['id'] );

	// Check access
	if ( ! sz_activity_user_can_delete( $activity ) )
		exit( '-1' );

	// Call the action before the delete so plugins can still fetch information about it
	do_action( 'sz_activity_before_action_delete_activity', $activity->id, $activity->user_id );

	if ( ! sz_activity_delete( array( 'id' => $activity->id, 'user_id' => $activity->user_id ) ) )
		exit( '-1<div id="message" class="error"><p>' . __( 'There was a problem when deleting. Please try again.', 'sportszone' ) . '</p></div>' );

	do_action( 'sz_activity_action_delete_activity', $activity->id, $activity->user_id );
	exit;
}

/**
 * Deletes an Activity comment received via a POST request
 *
 * @return mixed String on error, void on success
 * @since SportsZone (1.2)
 */
function sz_dtheme_delete_activity_comment() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	// Check the nonce
	check_admin_referer( 'sz_activity_delete_link' );

	if ( ! is_user_logged_in() )
		exit( '-1' );

	$comment = new SZ_Activity_Activity( $_POST['id'] );

	// Check access
	if ( ! sz_current_user_can( 'sz_moderate' ) && $comment->user_id != sz_loggedin_user_id() )
		exit( '-1' );

	if ( empty( $_POST['id'] ) || ! is_numeric( $_POST['id'] ) )
		exit( '-1' );

	// Call the action before the delete so plugins can still fetch information about it
	do_action( 'sz_activity_before_action_delete_activity', $_POST['id'], $comment->user_id );

	if ( ! sz_activity_delete_comment( $comment->item_id, $comment->id ) )
		exit( '-1<div id="message" class="error"><p>' . __( 'There was a problem when deleting. Please try again.', 'sportszone' ) . '</p></div>' );

	do_action( 'sz_activity_action_delete_activity', $_POST['id'], $comment->user_id );
	exit;
}

/**
 * AJAX spam an activity item or comment
 *
 * @global SportsZone $sz The one true SportsZone instance
 * @return mixed String on error, void on success
 * @since SportsZone (1.6)
 */
function sz_dtheme_spam_activity() {
	global $sz;

	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	// Check that user is logged in, Activity Streams are enabled, and Akismet is present.
	if ( ! is_user_logged_in() || ! sz_is_active( 'activity' ) || empty( $sz->activity->akismet ) )
		exit( '-1' );

	// Check an item ID was passed
	if ( empty( $_POST['id'] ) || ! is_numeric( $_POST['id'] ) )
		exit( '-1' );

	// Is the current user allowed to spam items?
	if ( ! sz_activity_user_can_mark_spam() )
		exit( '-1' );

	// Load up the activity item
	$activity = new SZ_Activity_Activity( (int) $_POST['id'] );
	if ( empty( $activity->component ) )
		exit( '-1' );

	// Check nonce
	check_admin_referer( 'sz_activity_akismet_spam_' . $activity->id );

	// Call an action before the spamming so plugins can modify things if they want to
	do_action( 'sz_activity_before_action_spam_activity', $activity->id, $activity );

	// Mark as spam
	sz_activity_mark_as_spam( $activity );
	$activity->save();

	do_action( 'sz_activity_action_spam_activity', $activity->id, $activity->user_id );
	exit;
}

/**
 * Mark an activity as a favourite via a POST request.
 *
 * @return string HTML
 * @since SportsZone (1.2)
 */
function sz_dtheme_mark_activity_favorite() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	if ( ! isset( $_POST['nonce'] ) ) {
		return;
	}

	// Either the 'mark' or 'unmark' nonce is accepted, for backward compatibility.
	$nonce = wp_unslash( $_POST['nonce'] );
	if ( ! wp_verify_nonce( $nonce, 'mark_favorite' ) && ! wp_verify_nonce( $nonce, 'unmark_favorite' ) ) {
		return;
	}

	if ( sz_activity_add_user_favorite( $_POST['id'] ) )
		_e( 'Remove Favorite', 'sportszone' );
	else
		_e( 'Favorite', 'sportszone' );

	exit;
}

/**
 * Un-favourite an activity via a POST request.
 *
 * @return string HTML
 * @since SportsZone (1.2)
 */
function sz_dtheme_unmark_activity_favorite() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	if ( ! isset( $_POST['nonce'] ) ) {
		return;
	}

	// Either the 'mark' or 'unmark' nonce is accepted, for backward compatibility.
	$nonce = wp_unslash( $_POST['nonce'] );
	if ( ! wp_verify_nonce( $nonce, 'mark_favorite' ) && ! wp_verify_nonce( $nonce, 'unmark_favorite' ) ) {
		return;
	}

	if ( sz_activity_remove_user_favorite( $_POST['id'] ) )
		_e( 'Favorite', 'sportszone' );
	else
		_e( 'Remove Favorite', 'sportszone' );

	exit;
}

/**
 * Fetches full an activity's full, non-excerpted content via a POST request.
 * Used for the 'Read More' link on long activity items.
 *
 * @return string HTML
 * @since SportsZone (1.5)
 */
function sz_dtheme_get_single_activity_content() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	$activity_array = sz_activity_get_specific( array(
		'activity_ids'     => $_POST['activity_id'],
		'display_comments' => 'stream'
	) );

	$activity = ! empty( $activity_array['activities'][0] ) ? $activity_array['activities'][0] : false;

	if ( empty( $activity ) )
		exit; // @todo: error?

	do_action_ref_array( 'sz_dtheme_get_single_activity_content', array( &$activity ) );

	// Activity content retrieved through AJAX should run through normal filters, but not be truncated
	remove_filter( 'sz_get_activity_content_body', 'sz_activity_truncate_entry', 5 );
	$content = apply_filters( 'sz_get_activity_content_body', $activity->content );

	exit( $content );
}

/**
 * Invites a friend to join a group via a POST request.
 *
 * @since SportsZone (1.2)
 * @todo Audit return types
 */
function sz_dtheme_ajax_invite_user() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	check_ajax_referer( 'groups_invite_uninvite_user' );

	if ( ! $_POST['friend_id'] || ! $_POST['friend_action'] || ! $_POST['group_id'] )
		return;

	if ( ! sz_groups_user_can_send_invites( $_POST['group_id'] ) )
		return;

	if ( ! friends_check_friendship( sz_loggedin_user_id(), $_POST['friend_id'] ) )
		return;

	$group_id = (int) $_POST['group_id'];
	$friend_id = (int) $_POST['friend_id'];

	if ( 'invite' == $_POST['friend_action'] ) {
		$group = groups_get_group( $group_id );

		// Users who have previously requested membership do not need
		// another invitation created for them
		if ( SZ_Groups_Member::check_for_membership_request( $friend_id, $group_id ) ) {
			$user_status = 'is_pending';

		// Create the user invitation
		} else if ( groups_invite_user( array( 'user_id' => $friend_id, 'group_id' => $group_id ) ) ) {
			$user_status = 'is_invited';

		// Miscellaneous failure
		} else {
			return;
		}

		$user = new SZ_Core_User( $_POST['friend_id'] );

		echo '<li id="uid-' . $user->id . '">';
		echo $user->avatar_thumb;
		echo '<h4>' . $user->user_link . '</h4>';
		echo '<span class="activity">' . esc_attr( $user->last_active ) . '</span>';
		echo '<div class="action">
				<a class="button remove" href="' . wp_nonce_url( sz_loggedin_user_domain() . sz_get_groups_slug() . '/' . $_POST['group_id'] . '/invites/remove/' . $user->id, 'groups_invite_uninvite_user' ) . '" id="uid-' . esc_attr( $user->id ) . '">' . __( 'Remove Invite', 'sportszone' ) . '</a>
			  </div>';

		if ( 'is_pending' == $user_status ) {
			echo '<p class="description">' . sprintf( __( '%s has previously requested to join this group. Sending an invitation will automatically add the member to the group.', 'sportszone' ), $user->user_link ) . '</p>';
		}

		echo '</li>';
		exit;

	} elseif ( 'uninvite' == $_POST['friend_action'] ) {
		// Users who have previously requested membership should not
		// have their requests deleted on the "uninvite" action
		if ( SZ_Groups_Member::check_for_membership_request( $friend_id, $group_id ) ) {
			return;
		}

		// Remove the unsent invitation
		if ( ! groups_uninvite_user( $friend_id, $group_id ) ) {
			return;
		}

		exit;

	} else {
		return;
	}
}

/**
 * Friend/un-friend a user via a POST request.
 *
 * @return string HTML
 * @since SportsZone (1.2)
 */
function sz_dtheme_ajax_addremove_friend() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	// Cast fid as an integer
	$friend_id = (int) $_POST['fid'];

	// Trying to cancel friendship
	if ( 'is_friend' == SZ_Friends_Friendship::check_is_friend( sz_loggedin_user_id(), $friend_id ) ) {
		check_ajax_referer( 'friends_remove_friend' );

		if ( ! friends_remove_friend( sz_loggedin_user_id(), $friend_id ) ) {
			echo __( 'Friendship could not be canceled.', 'sportszone' );
		} else {
			echo '<a id="friend-' . esc_attr( $friend_id ) . '" class="add" rel="add" title="' . __( 'Add Friend', 'sportszone' ) . '" href="' . wp_nonce_url( sz_loggedin_user_domain() . sz_get_friends_slug() . '/add-friend/' . $friend_id, 'friends_add_friend' ) . '">' . __( 'Add Friend', 'sportszone' ) . '</a>';
		}

	// Trying to request friendship
	} elseif ( 'not_friends' == SZ_Friends_Friendship::check_is_friend( sz_loggedin_user_id(), $friend_id ) ) {
		check_ajax_referer( 'friends_add_friend' );

		if ( ! friends_add_friend( sz_loggedin_user_id(), $friend_id ) ) {
			echo __(' Friendship could not be requested.', 'sportszone' );
		} else {
			echo '<a id="friend-' . esc_attr( $friend_id ) . '" class="remove" rel="remove" title="' . __( 'Cancel Friendship Request', 'sportszone' ) . '" href="' . wp_nonce_url( sz_loggedin_user_domain() . sz_get_friends_slug() . '/requests/cancel/' . $friend_id . '/', 'friends_withdraw_friendship' ) . '" class="requested">' . __( 'Cancel Friendship Request', 'sportszone' ) . '</a>';
		}

	// Trying to cancel pending request
	} elseif ( 'pending' == SZ_Friends_Friendship::check_is_friend( sz_loggedin_user_id(), $friend_id ) ) {
		check_ajax_referer( 'friends_withdraw_friendship' );

		if ( friends_withdraw_friendship( sz_loggedin_user_id(), $friend_id ) ) {
			echo '<a id="friend-' . esc_attr( $friend_id ) . '" class="add" rel="add" title="' . __( 'Add Friend', 'sportszone' ) . '" href="' . wp_nonce_url( sz_loggedin_user_domain() . sz_get_friends_slug() . '/add-friend/' . $friend_id, 'friends_add_friend' ) . '">' . __( 'Add Friend', 'sportszone' ) . '</a>';
		} else {
			echo __("Friendship request could not be cancelled.", 'sportszone');
		}

	// Request already pending
	} else {
		echo __( 'Request Pending', 'sportszone' );
	}

	exit;
}

/**
 * Accept a user friendship request via a POST request.
 *
 * @return mixed String on error, void on success
 * @since SportsZone (1.2)
 */
function sz_dtheme_ajax_accept_friendship() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	check_admin_referer( 'friends_accept_friendship' );

	if ( ! friends_accept_friendship( (int) $_POST['id'] ) )
		echo "-1<div id='message' class='error'><p>" . __( 'There was a problem accepting that request. Please try again.', 'sportszone' ) . '</p></div>';

	exit;
}

/**
 * Reject a user friendship request via a POST request.
 *
 * @return mixed String on error, void on success
 * @since SportsZone (1.2)
 */
function sz_dtheme_ajax_reject_friendship() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	check_admin_referer( 'friends_reject_friendship' );

	if ( ! friends_reject_friendship( (int) $_POST['id'] ) )
		echo "-1<div id='message' class='error'><p>" . __( 'There was a problem rejecting that request. Please try again.', 'sportszone' ) . '</p></div>';

	exit;
}

/**
 * Join or leave a group when clicking the "join/leave" button via a POST request.
 *
 * @return string HTML
 * @since SportsZone (1.2)
 */
function sz_dtheme_ajax_joinleave_group() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	// Cast gid as integer
	$group_id = (int) $_POST['gid'];

	if ( groups_is_user_banned( sz_loggedin_user_id(), $group_id ) )
		return;

	if ( ! $group = groups_get_group( array( 'group_id' => $group_id ) ) )
		return;

	if ( ! groups_is_user_member( sz_loggedin_user_id(), $group->id ) ) {
		if ( 'public' == $group->status ) {
			check_ajax_referer( 'groups_join_group' );

			if ( ! groups_join_group( $group->id ) ) {
				_e( 'Error joining group', 'sportszone' );
			} else {
				echo '<a id="group-' . esc_attr( $group->id ) . '" class="leave-group" rel="leave" title="' . __( 'Leave Group', 'sportszone' ) . '" href="' . wp_nonce_url( sz_get_group_permalink( $group ) . 'leave-group', 'groups_leave_group' ) . '">' . __( 'Leave Group', 'sportszone' ) . '</a>';
			}

		} elseif ( 'private' == $group->status ) {

			// If the user has already been invited, then this is
			// an Accept Invitation button
			if ( groups_check_user_has_invite( sz_loggedin_user_id(), $group->id ) ) {
				check_ajax_referer( 'groups_accept_invite' );

				if ( ! groups_accept_invite( sz_loggedin_user_id(), $group->id ) ) {
					_e( 'Error requesting membership', 'sportszone' );
				} else {
					echo '<a id="group-' . esc_attr( $group->id ) . '" class="leave-group" rel="leave" title="' . __( 'Leave Group', 'sportszone' ) . '" href="' . wp_nonce_url( sz_get_group_permalink( $group ) . 'leave-group', 'groups_leave_group' ) . '">' . __( 'Leave Group', 'sportszone' ) . '</a>';
				}

			// Otherwise, it's a Request Membership button
			} else {
				check_ajax_referer( 'groups_request_membership' );

				if ( ! groups_send_membership_request( sz_loggedin_user_id(), $group->id ) ) {
					_e( 'Error requesting membership', 'sportszone' );
				} else {
					echo '<a id="group-' . esc_attr( $group->id ) . '" class="membership-requested" rel="membership-requested" title="' . __( 'Membership Requested', 'sportszone' ) . '" href="' . sz_get_group_permalink( $group ) . '">' . __( 'Membership Requested', 'sportszone' ) . '</a>';
				}
			}
		}

	} else {
		check_ajax_referer( 'groups_leave_group' );

		if ( ! groups_leave_group( $group->id ) ) {
			_e( 'Error leaving group', 'sportszone' );
		} elseif ( 'public' == $group->status ) {
			echo '<a id="group-' . esc_attr( $group->id ) . '" class="join-group" rel="join" title="' . __( 'Join Group', 'sportszone' ) . '" href="' . wp_nonce_url( sz_get_group_permalink( $group ) . 'join', 'groups_join_group' ) . '">' . __( 'Join Group', 'sportszone' ) . '</a>';
		} elseif ( 'private' == $group->status ) {
			echo '<a id="group-' . esc_attr( $group->id ) . '" class="request-membership" rel="join" title="' . __( 'Request Membership', 'sportszone' ) . '" href="' . wp_nonce_url( sz_get_group_permalink( $group ) . 'request-membership', 'groups_send_membership_request' ) . '">' . __( 'Request Membership', 'sportszone' ) . '</a>';
		}
	}

	exit;
}

/**
 * Close and keep closed site wide notices from an admin in the sidebar, via a POST request.
 *
 * @return mixed String on error, void on success
 * @since SportsZone (1.2)
 */
function sz_dtheme_ajax_close_notice() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	$nonce_check = isset( $_POST['nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'sz_messages_close_notice' );

	if ( ! $nonce_check || ! isset( $_POST['notice_id'] ) ) {
		echo "-1<div id='message' class='error'><p>" . __( 'There was a problem closing the notice.', 'sportszone' ) . '</p></div>';

	} else {
		$user_id      = get_current_user_id();
		$notice_ids   = sz_get_user_meta( $user_id, 'closed_notices', true );
		$notice_ids[] = (int) $_POST['notice_id'];

		sz_update_user_meta( $user_id, 'closed_notices', $notice_ids );
	}

	exit;
}

/**
 * Send a private message reply to a thread via a POST request.
 *
 * @return string HTML
 * @since SportsZone (1.2)
 */
function sz_dtheme_ajax_messages_send_reply() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	check_ajax_referer( 'messages_send_message' );

	$result = messages_new_message( array( 'thread_id' => (int) $_REQUEST['thread_id'], 'content' => $_REQUEST['content'] ) );

	if ( $result ) { ?>
		<div class="message-box new-message">
			<div class="message-metadata">
				<?php do_action( 'sz_before_message_meta' ); ?>
				<?php echo sz_loggedin_user_avatar( 'type=thumb&width=30&height=30' ); ?>

				<strong><a href="<?php echo sz_loggedin_user_domain(); ?>"><?php sz_loggedin_user_fullname(); ?></a> <span class="activity"><?php printf( __( 'Sent %s', 'sportszone' ), sz_core_time_since( sz_core_current_time() ) ); ?></span></strong>

				<?php do_action( 'sz_after_message_meta' ); ?>
			</div>

			<?php do_action( 'sz_before_message_content' ); ?>

			<div class="message-content">
				<?php echo stripslashes( apply_filters( 'sz_get_the_thread_message_content', $_REQUEST['content'] ) ); ?>
			</div>

			<?php do_action( 'sz_after_message_content' ); ?>

			<div class="clear"></div>
		</div>
	<?php
	} else {
		echo "-1<div id='message' class='error'><p>" . __( 'There was a problem sending that reply. Please try again.', 'sportszone' ) . '</p></div>';
	}

	exit;
}

/**
 * Mark a private message as unread in your inbox via a POST request.
 *
 * @return mixed String on error, void on success
 * @since SportsZone (1.2)
 */
function sz_dtheme_ajax_message_markunread() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	$nonce_check = isset( $_POST['nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'sz_messages_mark_messages_unread' );

	if ( ! $nonce_check || ! isset( $_POST['thread_ids'] ) ) {
		echo "-1<div id='message' class='error'><p>" . __( 'There was a problem marking messages as unread.', 'sportszone' ) . '</p></div>';

	} else {
		$thread_ids = explode( ',', $_POST['thread_ids'] );

		for ( $i = 0, $count = count( $thread_ids ); $i < $count; ++$i ) {
			SZ_Messages_Thread::mark_as_unread( (int) $thread_ids[$i] );
		}
	}

	exit;
}

/**
 * Mark a private message as read in your inbox via a POST request.
 *
 * @return mixed String on error, void on success
 * @since SportsZone (1.2)
 */
function sz_dtheme_ajax_message_markread() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	$nonce_check = isset( $_POST['nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'sz_messages_mark_messages_read' );

	if ( ! $nonce_check || ! isset( $_POST['thread_ids'] ) ) {
		echo "-1<div id='message' class='error'><p>" . __('There was a problem marking messages as read.', 'sportszone' ) . '</p></div>';

	} else {
		$thread_ids = explode( ',', $_POST['thread_ids'] );

		for ( $i = 0, $count = count( $thread_ids ); $i < $count; ++$i ) {
			SZ_Messages_Thread::mark_as_read( (int) $thread_ids[$i] );
		}
	}

	exit;
}

/**
 * Delete a private message(s) in your inbox via a POST request.
 *
 * @return string HTML
 * @since SportsZone (1.2)
 */
function sz_dtheme_ajax_messages_delete() {
	// Bail if not a POST action
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	$nonce_check = isset( $_POST['nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'sz_messages_delete_selected' );

	if ( ! $nonce_check || ! isset($_POST['thread_ids']) ) {
		echo "-1<div id='message' class='error'><p>" . __( 'There was a problem deleting messages.', 'sportszone' ) . '</p></div>';

	} else {
		$thread_ids = explode( ',', $_POST['thread_ids'] );

		for ( $i = 0, $count = count( $thread_ids ); $i < $count; ++$i ) {
			SZ_Messages_Thread::delete( (int) $thread_ids[$i] );
		}

		_e( 'Messages deleted.', 'sportszone' );
	}

	exit;
}

/**
 * AJAX handler for autocomplete. Displays friends only, unless SZ_MESSAGES_AUTOCOMPLETE_ALL is defined.
 *
 * @return string HTML
 * @since SportsZone (1.2)
 */
function sz_dtheme_ajax_messages_autocomplete_results() {

	// Include everyone in the autocomplete, or just friends?
	if ( sz_is_current_component( sz_get_messages_slug() ) )
		$autocomplete_all = sportszone()->messages->autocomplete_all;

	$pag_page = 1;
	$limit    = (int) $_GET['limit'] ? $_GET['limit'] : apply_filters( 'sz_autocomplete_max_results', 10 );

	// Get the user ids based on the search terms
	if ( ! empty( $autocomplete_all ) ) {
		$users = SZ_Core_User::search_users( $_GET['q'], $limit, $pag_page );

		if ( ! empty( $users['users'] ) ) {
			// Build an array with the correct format
			$user_ids = array();
			foreach( $users['users'] as $user ) {
				if ( $user->id != sz_loggedin_user_id() ) {
					$user_ids[] = $user->id;
				}
			}

			$user_ids = apply_filters( 'sz_core_autocomplete_ids', $user_ids, $_GET['q'], $limit );
		}

	} else {
		if ( sz_is_active( 'friends' ) ) {
			$users = friends_search_friends( $_GET['q'], sz_loggedin_user_id(), $limit, 1 );

			// Keeping the sz_friends_autocomplete_list filter for backward compatibility
			$users = apply_filters( 'sz_friends_autocomplete_list', $users, $_GET['q'], $limit );

			if ( ! empty( $users['friends'] ) ) {
				$user_ids = apply_filters( 'sz_friends_autocomplete_ids', $users['friends'], $_GET['q'], $limit );
			}
		}
	}

	if ( ! empty( $user_ids ) ) {
		foreach ( $user_ids as $user_id ) {
			$ud = get_userdata( $user_id );
			if ( ! $ud ) {
				continue;
			}

			if ( sz_is_username_compatibility_mode() ) {
				// Sanitize for spaces
				$username = urlencode( $ud->user_login );
			} else {
				$username = $ud->user_nicename;
			}

			// Note that the final line break acts as a delimiter for the
			// autocomplete javascript and thus should not be removed
			echo '<span id="link-' . esc_attr( $username ) . '" href="' . sz_core_get_user_domain( $user_id ) . '"></span>' . sz_core_fetch_avatar( array( 'item_id' => $user_id, 'type' => 'thumb', 'width' => 15, 'height' => 15, 'alt' => $ud->display_name ) ) . ' &nbsp;' . sz_core_get_user_displayname( $user_id ) . ' (' . esc_html( $username ) . ')' . "\n";
		}
	}

	exit;
}
