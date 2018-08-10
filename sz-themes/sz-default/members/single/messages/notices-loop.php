<?php do_action( 'sz_before_notices_loop' ); ?>

<?php if ( sz_has_message_threads() ) : ?>

	<div class="pagination no-ajax" id="user-pag">

		<div class="pag-count" id="messages-dir-count">
			<?php sz_messages_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="messages-dir-pag">
			<?php sz_messages_pagination(); ?>
		</div>

	</div><!-- .pagination -->

	<?php do_action( 'sz_after_notices_pagination' ); ?>
	<?php do_action( 'sz_before_notices' ); ?>

	<table id="message-threads" class="messages-notices">
		<?php while ( sz_message_threads() ) : sz_message_thread(); ?>
			<tr id="notice-<?php sz_message_notice_id(); ?>" class="<?php sz_message_css_class(); ?>">
				<td width="1%"></td>
				<td width="38%">
					<strong><?php sz_message_notice_subject(); ?></strong>
					<?php sz_message_notice_text(); ?>
				</td>
				<td width="21%">

					<?php if ( sz_messages_is_active_notice() ) : ?>

						<strong><?php sz_messages_is_active_notice(); ?></strong>

					<?php endif; ?>

					<span class="activity"><?php _e( 'Sent:', 'sportszone' ); ?> <?php sz_message_notice_post_date(); ?></span>
				</td>

				<?php do_action( 'sz_notices_list_item' ); ?>

				<td width="10%">
					<a class="button" href="<?php sz_message_activate_deactivate_link(); ?>" class="confirm"><?php sz_message_activate_deactivate_text(); ?></a>
					<a class="button" href="<?php sz_message_notice_delete_link(); ?>" class="confirm" title="<?php esc_attr_e( "Delete Message", "sportszone" ); ?>">x</a>
				</td>
			</tr>
		<?php endwhile; ?>
	</table><!-- #message-threads -->

	<?php do_action( 'sz_after_notices' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, no notices were found.', 'sportszone' ); ?></p>
	</div>

<?php endif;?>

<?php do_action( 'sz_after_notices_loop' ); ?>