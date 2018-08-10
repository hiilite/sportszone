<?php
/**
 * SportsZone Friends Activity Functions.
 *
 * These functions handle the recording, deleting and formatting of activity
 * for the user and for this specific component.
 *
 * @package SportsZone
 * @subpackage FriendsNotifications
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Notification formatting callback for sz-friends notifications.
 *
 * @since 1.0.0
 *
 * @param string $action            The kind of notification being rendered.
 * @param int    $item_id           The primary item ID.
 * @param int    $secondary_item_id The secondary item ID.
 * @param int    $total_items       The total number of messaging-related notifications
 *                                  waiting for the user.
 * @param string $format            'string' for BuddyBar-compatible notifications;
 *                                  'array' for WP Toolbar. Default: 'string'.
 * @return array|string
 */
function friends_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

	switch ( $action ) {
		case 'friendship_accepted':
			$link = trailingslashit( sz_loggedin_user_domain() . sz_get_friends_slug() . '/my-friends' );

			// $action and $amount are used to generate dynamic filter names.
			$action = 'accepted';

			// Set up the string and the filter.
			if ( (int) $total_items > 1 ) {
				$text = sprintf( __( '%d friends accepted your friendship requests', 'sportszone' ), (int) $total_items );
				$amount = 'multiple';
			} else {
				$text = sprintf( __( '%s accepted your friendship request', 'sportszone' ),  sz_core_get_user_displayname( $item_id ) );
				$amount = 'single';
			}

			break;

		case 'friendship_request':
			$link = sz_loggedin_user_domain() . sz_get_friends_slug() . '/requests/?new';

			$action = 'request';

			// Set up the string and the filter.
			if ( (int) $total_items > 1 ) {
				$text = sprintf( __( 'You have %d pending friendship requests', 'sportszone' ), (int) $total_items );
				$amount = 'multiple';
			} else {
				$text = sprintf( __( 'You have a friendship request from %s', 'sportszone' ),  sz_core_get_user_displayname( $item_id ) );
				$amount = 'single';
			}

			break;
	}

	// Return either an HTML link or an array, depending on the requested format.
	if ( 'string' == $format ) {

		/**
		 * Filters the format of friendship notifications based on type and amount * of notifications pending.
		 *
		 * This is a variable filter that has four possible versions.
		 * The four possible versions are:
		 *   - sz_friends_single_friendship_accepted_notification
		 *   - sz_friends_multiple_friendship_accepted_notification
		 *   - sz_friends_single_friendship_request_notification
		 *   - sz_friends_multiple_friendship_request_notification
		 *
		 * @since 1.0.0
		 *
		 * @param string|array $value       Depending on format, an HTML link to new requests profile
		 *                                  tab or array with link and text.
		 * @param int          $total_items The total number of messaging-related notifications
		 *                                  waiting for the user.
		 * @param int          $item_id     The primary item ID.
		 */
		$return = apply_filters( 'sz_friends_' . $amount . '_friendship_' . $action . '_notification', '<a href="' . esc_url( $link ) . '">' . esc_html( $text ) . '</a>', (int) $total_items, $item_id );
	} else {
		/** This filter is documented in sz-friends/sz-friends-notifications.php */
		$return = apply_filters( 'sz_friends_' . $amount . '_friendship_' . $action . '_notification', array(
			'link' => $link,
			'text' => $text
		), (int) $total_items, $item_id );
	}

	/**
	 * Fires at the end of the sz-friends notification format callback.
	 *
	 * @since 1.0.0
	 *
	 * @param string       $action            The kind of notification being rendered.
	 * @param int          $item_id           The primary item ID.
	 * @param int          $secondary_item_id The secondary item ID.
	 * @param int          $total_items       The total number of messaging-related notifications
	 *                                        waiting for the user.
	 * @param array|string $return            Notification text string or array of link and text.
	 */
	do_action( 'friends_format_notifications', $action, $item_id, $secondary_item_id, $total_items, $return );

	return $return;
}

/**
 * Clear friend-related notifications when ?new=1
 *
 * @since 1.2.0
 */
function friends_clear_friend_notifications() {
	if ( isset( $_GET['new'] ) ) {
		sz_notifications_mark_notifications_by_type( sz_loggedin_user_id(), sportszone()->friends->id, 'friendship_accepted' );
	}
}
add_action( 'sz_activity_screen_my_activity', 'friends_clear_friend_notifications' );

/**
 * Delete any friendship request notifications for the logged in user.
 *
 * @since 1.9.0
 */
function sz_friends_mark_friendship_request_notifications_by_type() {
	if ( isset( $_GET['new'] ) ) {
		sz_notifications_mark_notifications_by_type( sz_loggedin_user_id(), sportszone()->friends->id, 'friendship_request' );
	}
}
add_action( 'friends_screen_requests', 'sz_friends_mark_friendship_request_notifications_by_type' );

/**
 * Delete any friendship acceptance notifications for the logged in user.
 *
 * @since 1.9.0
 */
function sz_friends_mark_friendship_accepted_notifications_by_type() {
	sz_notifications_mark_notifications_by_type( sz_loggedin_user_id(), sportszone()->friends->id, 'friendship_accepted' );
}
add_action( 'friends_screen_my_friends', 'sz_friends_mark_friendship_accepted_notifications_by_type' );

/**
 * Notify one use that another user has requested their virtual friendship.
 *
 * @since 1.9.0
 *
 * @param int $friendship_id     The unique ID of the friendship.
 * @param int $initiator_user_id The friendship initiator user ID.
 * @param int $friend_user_id    The friendship request receiver user ID.
 */
function sz_friends_friendship_requested_notification( $friendship_id, $initiator_user_id, $friend_user_id ) {
	sz_notifications_add_notification( array(
		'user_id'           => $friend_user_id,
		'item_id'           => $initiator_user_id,
		'secondary_item_id' => $friendship_id,
		'component_name'    => sportszone()->friends->id,
		'component_action'  => 'friendship_request',
		'date_notified'     => sz_core_current_time(),
		'is_new'            => 1,
	) );
}
add_action( 'friends_friendship_requested', 'sz_friends_friendship_requested_notification', 10, 3 );

/**
 * Remove friend request notice when a member rejects another members
 *
 * @since 1.9.0
 *
 * @param int    $friendship_id Friendship ID (not used).
 * @param object $friendship    Friendship object.
 */
function sz_friends_mark_friendship_rejected_notifications_by_item_id( $friendship_id, $friendship ) {
	sz_notifications_mark_notifications_by_item_id( $friendship->friend_user_id, $friendship->initiator_user_id, sportszone()->friends->id, 'friendship_request' );
}
add_action( 'friends_friendship_rejected', 'sz_friends_mark_friendship_rejected_notifications_by_item_id', 10, 2 );

/**
 * Notify a member when another member accepts their virtual friendship request.
 *
 * @since 1.9.0
 *
 * @param int $friendship_id     The unique ID of the friendship.
 * @param int $initiator_user_id The friendship initiator user ID.
 * @param int $friend_user_id    The friendship request receiver user ID.
 */
function sz_friends_add_friendship_accepted_notification( $friendship_id, $initiator_user_id, $friend_user_id ) {
	// Remove the friend request notice.
	sz_notifications_mark_notifications_by_item_id( $friend_user_id, $initiator_user_id, sportszone()->friends->id, 'friendship_request' );

	// Add a friend accepted notice for the initiating user.
	sz_notifications_add_notification(  array(
		'user_id'           => $initiator_user_id,
		'item_id'           => $friend_user_id,
		'secondary_item_id' => $friendship_id,
		'component_name'    => sportszone()->friends->id,
		'component_action'  => 'friendship_accepted',
		'date_notified'     => sz_core_current_time(),
		'is_new'            => 1,
	) );
}
add_action( 'friends_friendship_accepted', 'sz_friends_add_friendship_accepted_notification', 10, 3 );

/**
 * Remove friend request notice when a member withdraws their friend request.
 *
 * @since 1.9.0
 *
 * @param int    $friendship_id Friendship ID (not used).
 * @param object $friendship    Friendship Object.
 */
function sz_friends_mark_friendship_withdrawn_notifications_by_item_id( $friendship_id, $friendship ) {
	sz_notifications_delete_notifications_by_item_id( $friendship->friend_user_id, $friendship->initiator_user_id, sportszone()->friends->id, 'friendship_request' );
}
add_action( 'friends_friendship_withdrawn', 'sz_friends_mark_friendship_withdrawn_notifications_by_item_id', 10, 2 );

/**
 * Remove friendship requests FROM user, used primarily when a user is deleted.
 *
 * @since 1.9.0
 *
 * @param int $user_id ID of the user whose notifications are removed.
 */
function sz_friends_remove_notifications_data( $user_id = 0 ) {
	sz_notifications_delete_notifications_from_user( $user_id, sportszone()->friends->id, 'friendship_request' );
}
add_action( 'friends_remove_data', 'sz_friends_remove_notifications_data', 10, 1 );

/**
 * Add Friends-related settings to the Settings > Notifications page.
 *
 * @since 1.0.0
 */
function friends_screen_notification_settings() {

	if ( !$send_requests = sz_get_user_meta( sz_displayed_user_id(), 'notification_friends_friendship_request', true ) )
		$send_requests   = 'yes';

	if ( !$accept_requests = sz_get_user_meta( sz_displayed_user_id(), 'notification_friends_friendship_accepted', true ) )
		$accept_requests = 'yes'; ?>

	<table class="notification-settings" id="friends-notification-settings">
		<thead>
			<tr>
				<th class="icon"></th>
				<th class="title"><?php _ex( 'Friends', 'Friend settings on notification settings page', 'sportszone' ) ?></th>
				<th class="yes"><?php _e( 'Yes', 'sportszone' ) ?></th>
				<th class="no"><?php _e( 'No', 'sportszone' )?></th>
			</tr>
		</thead>

		<tbody>
			<tr id="friends-notification-settings-request">
				<td></td>
				<td><?php _ex( 'A member sends you a friendship request', 'Friend settings on notification settings page', 'sportszone' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_friends_friendship_request]" id="notification-friends-friendship-request-yes" value="yes" <?php checked( $send_requests, 'yes', true ) ?>/><label for="notification-friends-friendship-request-yes" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Yes, send email', 'sportszone' );
				?></label></td>
				<td class="no"><input type="radio" name="notifications[notification_friends_friendship_request]" id="notification-friends-friendship-request-no" value="no" <?php checked( $send_requests, 'no', true ) ?>/><label for="notification-friends-friendship-request-no" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'No, do not send email', 'sportszone' );
				?></label></td>
			</tr>
			<tr id="friends-notification-settings-accepted">
				<td></td>
				<td><?php _ex( 'A member accepts your friendship request', 'Friend settings on notification settings page', 'sportszone' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_friends_friendship_accepted]" id="notification-friends-friendship-accepted-yes" value="yes" <?php checked( $accept_requests, 'yes', true ) ?>/><label for="notification-friends-friendship-accepted-yes" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Yes, send email', 'sportszone' );
				?></label></td>
				<td class="no"><input type="radio" name="notifications[notification_friends_friendship_accepted]" id="notification-friends-friendship-accepted-no" value="no" <?php checked( $accept_requests, 'no', true ) ?>/><label for="notification-friends-friendship-accepted-no" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'No, do not send email', 'sportszone' );
				?></label></td>
			</tr>

			<?php

			/**
			 * Fires after the last table row on the friends notification screen.
			 *
			 * @since 1.0.0
			 */
			do_action( 'friends_screen_notification_settings' ); ?>

		</tbody>
	</table>

<?php
}
add_action( 'sz_notification_settings', 'friends_screen_notification_settings' );
