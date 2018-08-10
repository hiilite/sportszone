<?php
/**
 * SportsZone - Members Single Messages Compose
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>
<h2 class="sz-screen-reader-text"><?php
	/* translators: accessibility text */
	_e( 'Compose Message', 'sportszone' );
?></h2>

<form action="<?php sz_messages_form_action('compose' ); ?>" method="post" id="send_message_form" class="standard-form" enctype="multipart/form-data">

	<?php

	/**
	 * Fires before the display of message compose content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_before_messages_compose_content' ); ?>

	<label for="send-to-input"><?php _e("Send To (Username or Friend's Name)", 'sportszone' ); ?></label>
	<ul class="first acfb-holder">
		<li>
			<?php sz_message_get_recipient_tabs(); ?>
			<input type="text" name="send-to-input" class="send-to-input" id="send-to-input" />
		</li>
	</ul>

	<?php if ( sz_current_user_can( 'sz_moderate' ) ) : ?>
		<p><label for="send-notice"><input type="checkbox" id="send-notice" name="send-notice" value="1" /> <?php _e( "This is a notice to all users.", 'sportszone' ); ?></label></p>
	<?php endif; ?>

	<label for="subject"><?php _e( 'Subject', 'sportszone' ); ?></label>
	<input type="text" name="subject" id="subject" value="<?php sz_messages_subject_value(); ?>" />

	<label for="message_content"><?php _e( 'Message', 'sportszone' ); ?></label>
	<textarea name="content" id="message_content" rows="15" cols="40"><?php sz_messages_content_value(); ?></textarea>

	<input type="hidden" name="send_to_usernames" id="send-to-usernames" value="<?php sz_message_get_recipient_usernames(); ?>" class="<?php sz_message_get_recipient_usernames(); ?>" />

	<?php

	/**
	 * Fires after the display of message compose content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_after_messages_compose_content' ); ?>

	<div class="submit">
		<input type="submit" value="<?php esc_attr_e( "Send Message", 'sportszone' ); ?>" name="send" id="send" />
	</div>

	<?php wp_nonce_field( 'messages_send_message' ); ?>
</form>

<script type="text/javascript">
	document.getElementById("send-to-input").focus();
</script>

