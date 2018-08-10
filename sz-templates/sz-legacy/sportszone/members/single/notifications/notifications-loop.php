<?php
/**
 * SportsZone - Members Notifications Loop
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>
<form action="" method="post" id="notifications-bulk-management">
	<table class="notifications">
		<thead>
			<tr>
				<th class="icon"></th>
				<th class="bulk-select-all"><input id="select-all-notifications" type="checkbox"><label class="sz-screen-reader-text" for="select-all-notifications"><?php
					/* translators: accessibility text */
					_e( 'Select all', 'sportszone' );
				?></label></th>
				<th class="title"><?php _e( 'Notification', 'sportszone' ); ?></th>
				<th class="date"><?php _e( 'Date Received', 'sportszone' ); ?></th>
				<th class="actions"><?php _e( 'Actions',    'sportszone' ); ?></th>
			</tr>
		</thead>

		<tbody>

			<?php while ( sz_the_notifications() ) : sz_the_notification(); ?>

				<tr>
					<td></td>
					<td class="bulk-select-check"><label for="<?php sz_the_notification_id(); ?>"><input id="<?php sz_the_notification_id(); ?>" type="checkbox" name="notifications[]" value="<?php sz_the_notification_id(); ?>" class="notification-check"><span class="sz-screen-reader-text"><?php
						/* translators: accessibility text */
						_e( 'Select this notification', 'sportszone' );
					?></span></label></td>
					<td class="notification-description"><?php sz_the_notification_description();  ?></td>
					<td class="notification-since"><?php sz_the_notification_time_since();   ?></td>
					<td class="notification-actions"><?php sz_the_notification_action_links(); ?></td>
				</tr>

			<?php endwhile; ?>

		</tbody>
	</table>

	<div class="notifications-options-nav">
		<?php sz_notifications_bulk_management_dropdown(); ?>
	</div><!-- .notifications-options-nav -->

	<?php wp_nonce_field( 'notifications_bulk_nonce', 'notifications_bulk_nonce' ); ?>
</form>
