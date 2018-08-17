<?php
/**
 * SportsZone - Events Request Membership
 *
 * @since 3.0.0
 * @version 3.1.0
 */

sz_nouveau_event_hook( 'before', 'request_membership_content' ); ?>

<?php if ( ! sz_event_has_requested_membership() ) : ?>
	<p>
		<?php
		echo esc_html(
			sprintf(
				/* translators: %s = event name */
				__( 'You are requesting to become a member of the event "%s".', 'sportszone' ),
				sz_get_event_name()
			)
		);
		?>
	</p>

	<form action="<?php sz_event_form_action( 'request-membership' ); ?>" method="post" name="request-membership-form" id="request-membership-form" class="standard-form">
		<label for="event-request-membership-comments"><?php esc_html( 'Comments (optional)', 'sportszone' ); ?></label>
		<textarea name="event-request-membership-comments" id="event-request-membership-comments"></textarea>

		<?php sz_nouveau_event_hook( '', 'request_membership_content' ); ?>

		<p><input type="submit" name="event-request-send" id="event-request-send" value="<?php echo esc_attr_x( 'Send Request', 'button', 'sportszone' ); ?>" />

		<?php wp_nonce_field( 'events_request_membership' ); ?>
	</form><!-- #request-membership-form -->
<?php endif; ?>

<?php
sz_nouveau_event_hook( 'after', 'request_membership_content' );
