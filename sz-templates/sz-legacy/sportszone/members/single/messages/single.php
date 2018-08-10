<?php
/**
 * SportsZone - Members Single Message
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>
<div id="message-thread">

	<?php

	/**
	 * Fires before the display of a single member message thread content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_before_message_thread_content' ); ?>

	<?php if ( sz_thread_has_messages() ) : ?>

		<h2 id="message-subject"><?php sz_the_thread_subject(); ?></h2>

		<p id="message-recipients">
			<span class="highlight">

				<?php if ( sz_get_thread_recipients_count() <= 1 ) : ?>

					<?php _e( 'You are alone in this conversation.', 'sportszone' ); ?>

				<?php elseif ( sz_get_max_thread_recipients_to_list() <= sz_get_thread_recipients_count() ) : ?>

					<?php printf( __( 'Conversation between %s recipients.', 'sportszone' ), number_format_i18n( sz_get_thread_recipients_count() ) ); ?>

				<?php else : ?>

					<?php printf( __( 'Conversation between %s.', 'sportszone' ), sz_get_thread_recipients_list() ); ?>

				<?php endif; ?>

			</span>

			<a class="button confirm" href="<?php sz_the_thread_delete_link(); ?>"><?php _e( 'Delete', 'sportszone' ); ?></a>

			<?php

			/**
			 * Fires after the action links in the header of a single message thread.
			 *
			 * @since 2.5.0
			 */
			do_action( 'sz_after_message_thread_recipients' ); ?>
		</p>

		<?php

		/**
		 * Fires before the display of the message thread list.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_before_message_thread_list' ); ?>

		<?php while ( sz_thread_messages() ) : sz_thread_the_message(); ?>
			<?php sz_get_template_part( 'members/single/messages/message' ); ?>
		<?php endwhile; ?>

		<?php

		/**
		 * Fires after the display of the message thread list.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_after_message_thread_list' ); ?>

		<?php

		/**
		 * Fires before the display of the message thread reply form.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_before_message_thread_reply' ); ?>

		<form id="send-reply" action="<?php sz_messages_form_action(); ?>" method="post" class="standard-form">

			<div class="message-box">

				<div class="message-metadata">

					<?php

					/** This action is documented in sz-templates/sz-legacy/sportszone-functions.php */
					do_action( 'sz_before_message_meta' ); ?>

					<div class="avatar-box">
						<?php sz_loggedin_user_avatar( 'type=thumb&height=30&width=30' ); ?>

						<strong><?php _e( 'Send a Reply', 'sportszone' ); ?></strong>
					</div>

					<?php

					/** This action is documented in sz-templates/sz-legacy/sportszone-functions.php */
					do_action( 'sz_after_message_meta' ); ?>

				</div><!-- .message-metadata -->

				<div class="message-content">

					<?php

					/**
					 * Fires before the display of the message reply box.
					 *
					 * @since 1.1.0
					 */
					do_action( 'sz_before_message_reply_box' ); ?>

					<label for="message_content" class="sz-screen-reader-text"><?php
						/* translators: accessibility text */
						_e( 'Reply to Message', 'sportszone' );
					?></label>
					<textarea name="content" id="message_content" rows="15" cols="40"></textarea>

					<?php

					/**
					 * Fires after the display of the message reply box.
					 *
					 * @since 1.1.0
					 */
					do_action( 'sz_after_message_reply_box' ); ?>

					<div class="submit">
						<input type="submit" name="send" value="<?php esc_attr_e( 'Send Reply', 'sportszone' ); ?>" id="send_reply_button"/>
					</div>

					<input type="hidden" id="thread_id" name="thread_id" value="<?php sz_the_thread_id(); ?>" />
					<input type="hidden" id="messages_order" name="messages_order" value="<?php sz_thread_messages_order(); ?>" />
					<?php wp_nonce_field( 'messages_send_message', 'send_message_nonce' ); ?>

				</div><!-- .message-content -->

			</div><!-- .message-box -->

		</form><!-- #send-reply -->

		<?php

		/**
		 * Fires after the display of the message thread reply form.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_after_message_thread_reply' ); ?>

	<?php endif; ?>

	<?php

	/**
	 * Fires after the display of a single member message thread content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_after_message_thread_content' ); ?>

</div>
