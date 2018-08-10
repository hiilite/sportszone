<?php
/**
 * SportsZone - Members Messages Loop
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/**
 * Fires before the members messages loop.
 *
 * @since 1.2.0
 */
do_action( 'sz_before_member_messages_loop' ); ?>

<?php if ( sz_has_message_threads( sz_ajax_querystring( 'messages' ) ) ) : ?>

	<h2 class="sz-screen-reader-text"><?php
		/* translators: accessibility text */
		_e( 'Starred messages', 'sportszone' );
	?></h2>

	<div class="pagination no-ajax" id="user-pag">

		<div class="pag-count" id="messages-dir-count">
			<?php sz_messages_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="messages-dir-pag">
			<?php sz_messages_pagination(); ?>
		</div>

	</div><!-- .pagination -->

	<?php

	/**
	 * Fires after the members messages pagination display.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_member_messages_pagination' ); ?>

	<?php

	/**
	 * Fires before the members messages threads.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_member_messages_threads' ); ?>

	<form action="<?php echo sz_displayed_user_domain() . sz_get_messages_slug() . '/' . sz_current_action() ?>/bulk-manage/" method="post" id="messages-bulk-management">

		<table id="message-threads" class="messages-notices">

			<thead>
				<tr>
					<th scope="col" class="thread-checkbox bulk-select-all"><input id="select-all-messages" type="checkbox"><label class="sz-screen-reader-text" for="select-all-messages"><?php
						/* translators: accessibility text */
						_e( 'Select all', 'sportszone' );
					?></label></th>
					<th scope="col" class="thread-from"><?php _e( 'From', 'sportszone' ); ?></th>
					<th scope="col" class="thread-info"><?php _e( 'Subject', 'sportszone' ); ?></th>

					<?php

					/**
					 * Fires inside the messages box table header to add a new column.
					 *
					 * This is to primarily add a <th> cell to the messages box table header. Use
					 * the related 'sz_messages_inbox_list_item' hook to add a <td> cell.
					 *
					 * @since 2.3.0
					 */
					do_action( 'sz_messages_inbox_list_header' ); ?>

					<?php if ( sz_is_active( 'messages', 'star' ) ) : ?>
						<th scope="col" class="thread-star"><span class="message-action-star"><span class="icon"></span> <span class="screen-reader-text"><?php
							/* translators: accessibility text */
							_e( 'Star', 'sportszone' );
						?></span></span></th>
					<?php endif; ?>

					<th scope="col" class="thread-options"><?php _e( 'Actions', 'sportszone' ); ?></th>
				</tr>
			</thead>

			<tbody>

				<?php while ( sz_message_threads() ) : sz_message_thread(); ?>

					<tr id="m-<?php sz_message_thread_id(); ?>" class="<?php sz_message_css_class(); ?><?php if ( sz_message_thread_has_unread() ) : ?> unread<?php else: ?> read<?php endif; ?>">
						<td class="bulk-select-check">
							<label for="sz-message-thread-<?php sz_message_thread_id(); ?>"><input type="checkbox" name="message_ids[]" id="sz-message-thread-<?php sz_message_thread_id(); ?>" class="message-check" value="<?php sz_message_thread_id(); ?>" /><span class="sz-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Select this message', 'sportszone' );
							?></span></label>
						</td>

						<?php if ( 'sentbox' != sz_current_action() ) : ?>
							<td class="thread-from">
								<?php sz_message_thread_avatar( array( 'width' => 25, 'height' => 25 ) ); ?>
								<span class="from"><?php _e( 'From:', 'sportszone' ); ?></span> <?php sz_message_thread_from(); ?>
								<?php sz_message_thread_total_and_unread_count(); ?>
								<span class="activity"><?php sz_message_thread_last_post_date(); ?></span>
							</td>
						<?php else: ?>
							<td class="thread-from">
								<?php sz_message_thread_avatar( array( 'width' => 25, 'height' => 25 ) ); ?>
								<span class="to"><?php _e( 'To:', 'sportszone' ); ?></span> <?php sz_message_thread_to(); ?>
								<?php sz_message_thread_total_and_unread_count(); ?>
								<span class="activity"><?php sz_message_thread_last_post_date(); ?></span>
							</td>
						<?php endif; ?>

						<td class="thread-info">
							<p><a href="<?php sz_message_thread_view_link( sz_get_message_thread_id(), sz_displayed_user_id() ); ?>" class="sz-tooltip" data-sz-tooltip="<?php esc_attr_e( "View Message", 'sportszone' ); ?>" aria-label="<?php esc_attr_e( "View Message", 'sportszone' ); ?>"><?php sz_message_thread_subject(); ?></a></p>
							<p class="thread-excerpt"><?php sz_message_thread_excerpt(); ?></p>
						</td>

						<?php

						/**
						 * Fires inside the messages box table row to add a new column.
						 *
						 * This is to primarily add a <td> cell to the message box table. Use the
						 * related 'sz_messages_inbox_list_header' hook to add a <th> header cell.
						 *
						 * @since 1.1.0
						 */
						do_action( 'sz_messages_inbox_list_item' ); ?>

						<?php if ( sz_is_active( 'messages', 'star' ) ) : ?>
							<td class="thread-star">
								<?php sz_the_message_star_action_link( array( 'thread_id' => sz_get_message_thread_id() ) ); ?>
							</td>
						<?php endif; ?>

						<td class="thread-options">
							<?php if ( sz_message_thread_has_unread() ) : ?>
								<a class="read" href="<?php sz_the_message_thread_mark_read_url( sz_displayed_user_id() );?>"><?php _e( 'Read', 'sportszone' ); ?></a>
							<?php else : ?>
								<a class="unread" href="<?php sz_the_message_thread_mark_unread_url( sz_displayed_user_id() );?>"><?php _e( 'Unread', 'sportszone' ); ?></a>
							<?php endif; ?>
							 |
							<a class="delete" href="<?php sz_message_thread_delete_link( sz_displayed_user_id() ); ?>"><?php _e( 'Delete', 'sportszone' ); ?></a>

							<?php

							/**
							 * Fires after the thread options links for each message in the messages loop list.
							 *
							 * @since 2.5.0
							 */
							do_action( 'sz_messages_thread_options' ); ?>
						</td>
					</tr>

				<?php endwhile; ?>

			</tbody>

		</table><!-- #message-threads -->

		<div class="messages-options-nav">
			<?php sz_messages_bulk_management_dropdown(); ?>
		</div><!-- .messages-options-nav -->

		<?php wp_nonce_field( 'messages_bulk_nonce', 'messages_bulk_nonce' ); ?>
	</form>

	<?php

	/**
	 * Fires after the members messages threads.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_member_messages_threads' ); ?>

	<?php

	/**
	 * Fires and displays member messages options.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_member_messages_options' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, no messages were found.', 'sportszone' ); ?></p>
	</div>

<?php endif;?>

<?php

/**
 * Fires after the members messages loop.
 *
 * @since 1.2.0
 */
do_action( 'sz_after_member_messages_loop' );
