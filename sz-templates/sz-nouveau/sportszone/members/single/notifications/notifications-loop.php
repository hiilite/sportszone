<?php
/**
 * SportsZone - Members Notifications Loop
 *
 * @since 3.0.0
 * @version 3.1.0
 */

if ( sz_has_notifications( sz_ajax_querystring( 'notifications' ) ) ) :

	sz_nouveau_pagination( 'top' ); ?>

	<form action="" method="post" id="notifications-bulk-management" class="standard-form">
		<table class="notifications sz-tables-user">
			<thead>
				<tr>
					<th class="icon"></th>
					<th class="bulk-select-all"><input id="select-all-notifications" type="checkbox"><label class="sz-screen-reader-text" for="select-all-notifications"><?php esc_html_e( 'Select all', 'sportszone' ); ?></label></th>
					<th class="title"><?php esc_html_e( 'Notification', 'sportszone' ); ?></th>
					<th class="date">
						<?php esc_html_e( 'Date Received', 'sportszone' ); ?>
						<?php sz_nouveau_notifications_sort_order_links(); ?>
					</th>
					<th class="actions"><?php esc_html_e( 'Actions', 'sportszone' ); ?></th>
				</tr>
			</thead>

			<tbody>

				<?php
				while ( sz_the_notifications() ) :
					sz_the_notification();
				?>

					<tr>
						<td></td>
						<td class="bulk-select-check"><label for="<?php sz_the_notification_id(); ?>"><input id="<?php sz_the_notification_id(); ?>" type="checkbox" name="notifications[]" value="<?php sz_the_notification_id(); ?>" class="notification-check"><span class="sz-screen-reader-text"><?php esc_html_e( 'Select this notification', 'sportszone' ); ?></span></label></td>
						<td class="notification-description"><?php sz_the_notification_description(); ?></td>
						<td class="notification-since"><?php sz_the_notification_time_since(); ?></td>
						<td class="notification-actions"><?php sz_the_notification_action_links(); ?></td>
					</tr>

				<?php endwhile; ?>

			</tbody>
		</table>

		<div class="notifications-options-nav">
			<?php sz_nouveau_notifications_bulk_management_dropdown(); ?>
		</div><!-- .notifications-options-nav -->

		<?php wp_nonce_field( 'notifications_bulk_nonce', 'notifications_bulk_nonce' ); ?>
	</form>

	<?php sz_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	<?php sz_nouveau_user_feedback( 'member-notifications-none' ); ?>

<?php endif;
