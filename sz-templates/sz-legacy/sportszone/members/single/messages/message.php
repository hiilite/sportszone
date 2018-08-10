<?php
/**
 * SportsZone - Private Message Content.
 *
 * This template is used in /messages/single.php during the message loop to
 * display each message and when a new message is created via AJAX.
 *
 * @since 2.4.0
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

			<div class="message-box <?php sz_the_thread_message_css_class(); ?>">

				<div class="message-metadata">

					<?php

					/**
					 * Fires before the single message header is displayed.
					 *
					 * @since 1.1.0
					 */
					do_action( 'sz_before_message_meta' ); ?>

					<?php sz_the_thread_message_sender_avatar( 'type=thumb&width=30&height=30' ); ?>

					<?php if ( sz_get_the_thread_message_sender_link() ) : ?>

						<strong><a href="<?php sz_the_thread_message_sender_link(); ?>"><?php sz_the_thread_message_sender_name(); ?></a></strong>

					<?php else : ?>

						<strong><?php sz_the_thread_message_sender_name(); ?></strong>

					<?php endif; ?>

					<span class="activity"><?php sz_the_thread_message_time_since(); ?></span>

					<?php if ( sz_is_active( 'messages', 'star' ) ) : ?>
						<div class="message-star-actions">
							<?php sz_the_message_star_action_link(); ?>
						</div>
					<?php endif; ?>

					<?php

					/**
					 * Fires after the single message header is displayed.
					 *
					 * @since 1.1.0
					 */
					do_action( 'sz_after_message_meta' ); ?>

				</div><!-- .message-metadata -->

				<?php

				/**
				 * Fires before the message content for a private message.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_before_message_content' ); ?>

				<div class="message-content">

					<?php sz_the_thread_message_content(); ?>

				</div><!-- .message-content -->

				<?php

				/**
				 * Fires after the message content for a private message.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_after_message_content' ); ?>

				<div class="clear"></div>

			</div><!-- .message-box -->
