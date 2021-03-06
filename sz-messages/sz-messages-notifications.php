<?php
/**
 * SportsZone Messages Notifications.
 *
 * @package SportsZone
 * @subpackage MessagesNotifications
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Format notifications for the Messages component.
 *
 * @since 1.0.0
 *
 * @param string $action            The kind of notification being rendered.
 * @param int    $item_id           The primary item id.
 * @param int    $secondary_item_id The secondary item id.
 * @param int    $total_items       The total number of messaging-related notifications
 *                                  waiting for the user.
 * @param string $format            Return value format. 'string' for BuddyBar-compatible
 *                                  notifications; 'array' for WP Toolbar. Default: 'string'.
 * @return string|array Formatted notifications.
 */
function messages_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {
	$total_items = (int) $total_items;
	$text        = '';
	$link        = trailingslashit( sz_loggedin_user_domain() . sz_get_messages_slug() . '/inbox' );
	$title       = __( 'Inbox', 'sportszone' );
	$amount      = 'single';

	if ( 'new_message' === $action ) {
		if ( $total_items > 1 ) {
			$amount = 'multiple';
			$text   = sprintf( __( 'You have %d new messages', 'sportszone' ), $total_items );

		} else {
			// Get message thread ID.
			$message   = new SZ_Messages_Message( $item_id );
			$thread_id = $message->thread_id;
			$link      = ( ! empty( $thread_id ) )
				? sz_get_message_thread_view_link( $thread_id )
				: false;

			if ( ! empty( $secondary_item_id ) ) {
				$text = sprintf( __( '%s sent you a new private message', 'sportszone' ), sz_core_get_user_displayname( $secondary_item_id ) );
			} else {
				$text = sprintf( _n( 'You have %s new private message', 'You have %s new private messages', $total_items, 'sportszone' ), sz_core_number_format( $total_items ) );
			}
		}

		if ( 'string' === $format ) {
			if ( ! empty( $link ) ) {
				$return = '<a href="' . esc_url( $link ) . '">' . esc_html( $text ) . '</a>';
			} else {
				$return = esc_html( $text );
			}

			/**
			 * Filters the new message notification text before the notification is created.
			 *
			 * This is a dynamic filter. Possible filter names are:
			 *   - 'sz_messages_multiple_new_message_notification'.
			 *   - 'sz_messages_single_new_message_notification'.
			 *
			 * @param string $return            Notification text.
			 * @param int    $total_items       Number of messages referred to by the notification.
			 * @param string $text              The raw notification test (ie, not wrapped in a link).
			 * @param int    $item_id           ID of the associated item.
			 * @param int    $secondary_item_id ID of the secondary associated item.
			 */
			$return = apply_filters( 'sz_messages_' . $amount . '_new_message_notification', $return, (int) $total_items, $text, $link, $item_id, $secondary_item_id );
		} else {
			/** This filter is documented in sz-messages/sz-messages-notifications.php */
			$return = apply_filters( 'sz_messages_' . $amount . '_new_message_notification', array(
				'text' => $text,
				'link' => $link
			), $link, (int) $total_items, $text, $link, $item_id, $secondary_item_id );
		}

	// Custom notification action for the Messages component
	} else {
		if ( 'string' === $format ) {
			$return = $text;
		} else {
			$return = array(
				'text' => $text,
				'link' => $link
			);
		}

		/**
		 * Backcompat for plugins that used to filter sz_messages_single_new_message_notification
		 * for their custom actions. These plugins should now use 'sz_messages_' . $action . '_notification'
		 */
		if ( has_filter( 'sz_messages_single_new_message_notification' ) ) {
			if ( 'string' === $format ) {
				/** This filter is documented in sz-messages/sz-messages-notifications.php */
				$return = apply_filters( 'sz_messages_single_new_message_notification', $return, (int) $total_items, $text, $link, $item_id, $secondary_item_id );

			// Notice that there are seven parameters instead of six? Ugh...
			} else {
				/** This filter is documented in sz-messages/sz-messages-notifications.php */
				$return = apply_filters( 'sz_messages_single_new_message_notification', $return, $link, (int) $total_items, $text, $link, $item_id, $secondary_item_id );
			}
		}

		/**
		 * Filters the custom action notification before the notification is created.
		 *
		 * This is a dynamic filter based on the message notification action.
		 *
		 * @since 2.6.0
		 *
		 * @param array  $value             An associative array containing the text and the link of the notification
		 * @param int    $item_id           ID of the associated item.
		 * @param int    $secondary_item_id ID of the secondary associated item.
		 * @param int    $total_items       Number of messages referred to by the notification.
		 * @param string $format            Return value format. 'string' for BuddyBar-compatible
		 *                                  notifications; 'array' for WP Toolbar. Default: 'string'.
		 */
		$return = apply_filters( "sz_messages_{$action}_notification", $return, $item_id, $secondary_item_id, $total_items, $format );
	}

	/**
	 * Fires right before returning the formatted message notifications.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action            The type of message notification.
	 * @param int    $item_id           The primary item ID.
	 * @param int    $secondary_item_id The secondary item ID.
	 * @param int    $total_items       Total amount of items to format.
	 */
	do_action( 'messages_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return $return;
}

/**
 * Send notifications to message recipients.
 *
 * @since 1.9.0
 *
 * @param SZ_Messages_Message $message Message object.
 */
function sz_messages_message_sent_add_notification( $message ) {
	if ( ! empty( $message->recipients ) ) {
		foreach ( (array) $message->recipients as $recipient ) {
			sz_notifications_add_notification( array(
				'user_id'           => $recipient->user_id,
				'item_id'           => $message->id,
				'secondary_item_id' => $message->sender_id,
				'component_name'    => sportszone()->messages->id,
				'component_action'  => 'new_message',
				'date_notified'     => sz_core_current_time(),
				'is_new'            => 1,
			) );
		}
	}
}
add_action( 'messages_message_sent', 'sz_messages_message_sent_add_notification', 10 );

/**
 * Mark new message notification when member reads a message thread directly.
 *
 * @since 1.9.0
 */
function sz_messages_screen_conversation_mark_notifications() {
	global $thread_template;

	/*
	 * Only run on the logged-in user's profile.
	 * If an admin visits a thread, it shouldn't change the read status.
	 */
	if ( ! sz_is_my_profile() ) {
		return;
	}

	// Get unread PM notifications for the user.
	$new_pm_notifications = SZ_Notifications_Notification::get( array(
		'user_id'           => sz_loggedin_user_id(),
		'component_name'    => sportszone()->messages->id,
		'component_action'  => 'new_message',
		'is_new'            => 1,
	) );
	$unread_message_ids = wp_list_pluck( $new_pm_notifications, 'item_id' );

	// No unread PMs, so stop!
	if ( empty( $unread_message_ids ) ) {
		return;
	}

	// Get the unread message ids for this thread only.
	$message_ids = array_intersect( $unread_message_ids, wp_list_pluck( $thread_template->thread->messages, 'id' ) );

	// Mark each notification for each PM message as read.
	foreach ( $message_ids as $message_id ) {
		sz_notifications_mark_notifications_by_item_id( sz_loggedin_user_id(), (int) $message_id, sportszone()->messages->id, 'new_message' );
	}
}
add_action( 'thread_loop_start', 'sz_messages_screen_conversation_mark_notifications', 10 );

/**
 * Mark new message notification as read when the corresponding message is mark read.
 *
 * This callback covers mark-as-read bulk actions.
 *
 * @since 3.0.0
 *
 * @param int $thread_id ID of the thread being marked as read.
 */
function sz_messages_mark_notification_on_mark_thread( $thread_id ) {
	$thread_messages = SZ_Messages_Thread::get_messages( $thread_id );

	foreach ( $thread_messages as $thread_message ) {
		sz_notifications_mark_notifications_by_item_id( sz_loggedin_user_id(), $thread_message->id, sportszone()->messages->id, 'new_message' );
	}
}
add_action( 'messages_thread_mark_as_read', 'sz_messages_mark_notification_on_mark_thread' );

/**
 * When a message is deleted, delete corresponding notifications.
 *
 * @since 2.0.0
 *
 * @param int   $thread_id   ID of the thread.
 * @param array $message_ids IDs of the messages.
 */
function sz_messages_message_delete_notifications( $thread_id, $message_ids ) {
	// For each recipient, delete notifications corresponding to each message.
	$thread = new SZ_Messages_Thread( $thread_id );
	foreach ( $thread->get_recipients() as $recipient ) {
		foreach ( $message_ids as $message_id ) {
			sz_notifications_delete_notifications_by_item_id( $recipient->user_id, (int) $message_id, sportszone()->messages->id, 'new_message' );
		}
	}
}
add_action( 'sz_messages_thread_after_delete', 'sz_messages_message_delete_notifications', 10, 2 );

/**
 * Render the markup for the Messages section of Settings > Notifications.
 *
 * @since 1.0.0
 */
function messages_screen_notification_settings() {

	if ( sz_action_variables() ) {
		sz_do_404();
		return;
	}

	if ( !$new_messages = sz_get_user_meta( sz_displayed_user_id(), 'notification_messages_new_message', true ) ) {
		$new_messages = 'yes';
	} ?>

	<table class="notification-settings" id="messages-notification-settings">
		<thead>
			<tr>
				<th class="icon"></th>
				<th class="title"><?php _e( 'Messages', 'sportszone' ) ?></th>
				<th class="yes"><?php _e( 'Yes', 'sportszone' ) ?></th>
				<th class="no"><?php _e( 'No', 'sportszone' )?></th>
			</tr>
		</thead>

		<tbody>
			<tr id="messages-notification-settings-new-message">
				<td></td>
				<td><?php _e( 'A member sends you a new message', 'sportszone' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_messages_new_message]" id="notification-messages-new-messages-yes" value="yes" <?php checked( $new_messages, 'yes', true ) ?>/><label for="notification-messages-new-messages-yes" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Yes, send email', 'sportszone' );
				?></label></td>
				<td class="no"><input type="radio" name="notifications[notification_messages_new_message]" id="notification-messages-new-messages-no" value="no" <?php checked( $new_messages, 'no', true ) ?>/><label for="notification-messages-new-messages-no" class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'No, do not send email', 'sportszone' );
				?></label></td>
			</tr>

			<?php

			/**
			 * Fires inside the closing </tbody> tag for messages screen notification settings.
			 *
			 * @since 1.0.0
			 */
			do_action( 'messages_screen_notification_settings' ); ?>
		</tbody>
	</table>

<?php
}
add_action( 'sz_notification_settings', 'messages_screen_notification_settings', 2 );
