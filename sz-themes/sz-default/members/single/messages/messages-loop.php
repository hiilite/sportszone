<?php do_action( 'sz_before_member_messages_loop' ); ?>

<?php if ( sz_has_message_threads( sz_ajax_querystring( 'messages' ) ) ) : ?>

	<div class="pagination no-ajax" id="user-pag">

		<div class="pag-count" id="messages-dir-count">
			<?php sz_messages_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="messages-dir-pag">
			<?php sz_messages_pagination(); ?>
		</div>

	</div><!-- .pagination -->

	<?php do_action( 'sz_after_member_messages_pagination' ); ?>

	<?php do_action( 'sz_before_member_messages_threads'   ); ?>

	<table id="message-threads" class="messages-notices">
		<?php while ( sz_message_threads() ) : sz_message_thread(); ?>

			<tr id="m-<?php sz_message_thread_id(); ?>" class="<?php sz_message_css_class(); ?><?php if ( sz_message_thread_has_unread() ) : ?> unread<?php else: ?> read<?php endif; ?>">
				<td width="1%" class="thread-count">
					<span class="unread-count"><?php sz_message_thread_unread_count(); ?></span>
				</td>
				<td width="1%" class="thread-avatar"><?php sz_message_thread_avatar(); ?></td>

				<?php if ( 'sentbox' != sz_current_action() ) : ?>
					<td width="30%" class="thread-from">
						<?php _e( 'From:', 'sportszone' ); ?> <?php sz_message_thread_from(); ?><br />
						<span class="activity"><?php sz_message_thread_last_post_date(); ?></span>
					</td>
				<?php else: ?>
					<td width="30%" class="thread-from">
						<?php _e( 'To:', 'sportszone' ); ?> <?php sz_message_thread_to(); ?><br />
						<span class="activity"><?php sz_message_thread_last_post_date(); ?></span>
					</td>
				<?php endif; ?>

				<td width="50%" class="thread-info">
					<p><a href="<?php sz_message_thread_view_link(); ?>" title="<?php esc_attr_e( "View Message", "sportszone" ); ?>"><?php sz_message_thread_subject(); ?></a></p>
					<p class="thread-excerpt"><?php sz_message_thread_excerpt(); ?></p>
				</td>

				<?php do_action( 'sz_messages_inbox_list_item' ); ?>

				<td width="13%" class="thread-options">
					<input type="checkbox" name="message_ids[]" value="<?php sz_message_thread_id(); ?>" />
					<a class="button confirm" href="<?php sz_message_thread_delete_link(); ?>" title="<?php esc_attr_e( "Delete Message", "sportszone" ); ?>"><?php _e( 'Delete', 'sportszone' ); ?></a> &nbsp;
				</td>
			</tr>

		<?php endwhile; ?>
	</table><!-- #message-threads -->

	<div class="messages-options-nav">
		<?php sz_messages_options(); ?>
	</div><!-- .messages-options-nav -->

	<?php do_action( 'sz_after_member_messages_threads' ); ?>

	<?php do_action( 'sz_after_member_messages_options' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, no messages were found.', 'sportszone' ); ?></p>
	</div>

<?php endif;?>

<?php do_action( 'sz_after_member_messages_loop' ); ?>
