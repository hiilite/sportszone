<?php if ( sz_has_notifications() ) : ?>

	<div id="pag-top" class="pagination no-ajax">
		<div class="pag-count" id="notifications-count-top">
			<?php sz_notifications_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="notifications-pag-top">
			<?php sz_notifications_pagination_links(); ?>
		</div>
	</div>

	<?php sz_get_template_part( 'members/single/notifications/notifications-loop' ); ?>

	<div id="pag-bottom" class="pagination no-ajax">
		<div class="pag-count" id="notifications-count-bottom">
			<?php sz_notifications_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="notifications-pag-bottom">
			<?php sz_notifications_pagination_links(); ?>
		</div>
	</div>

<?php else : ?>

	<?php sz_get_template_part( 'members/single/notifications/feedback-no-notifications' ); ?>

<?php endif;