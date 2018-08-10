<table class="notifications">
	<thead>
		<tr>
			<th class="icon"></th>
			<th class="title"><?php _e( 'Notification', 'sportszone' ); ?></th>
			<th class="date"><?php _e( 'Date Received', 'sportszone' ); ?></th>
			<th class="actions"><?php _e( 'Actions',    'sportszone' ); ?></th>
		</tr>
	</thead>

	<tbody>

		<?php while ( sz_the_notifications() ) : sz_the_notification(); ?>

			<tr>
				<td></td>
				<td><?php sz_the_notification_description();  ?></td>
				<td><?php sz_the_notification_time_since();   ?></td>
				<td><?php sz_the_notification_action_links(); ?></td>
			</tr>

		<?php endwhile; ?>

	</tbody>
</table>
