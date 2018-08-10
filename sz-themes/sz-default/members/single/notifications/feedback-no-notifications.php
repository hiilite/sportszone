<div id="message" class="info">

	<?php if ( sz_is_current_action( 'unread' ) ) : ?>

		<?php if ( sz_is_my_profile() ) : ?>

			<p><?php _e( 'You have no unread notifications.', 'sportszone' ); ?></p>

		<?php else : ?>

			<p><?php _e( 'This member has no unread notifications.', 'sportszone' ); ?></p>

		<?php endif; ?>
			
	<?php else : ?>
			
		<?php if ( sz_is_my_profile() ) : ?>

			<p><?php _e( 'You have no notifications.', 'sportszone' ); ?></p>

		<?php else : ?>

			<p><?php _e( 'This member has no notifications.', 'sportszone' ); ?></p>

		<?php endif; ?>

	<?php endif; ?>

</div>
