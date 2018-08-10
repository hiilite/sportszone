<?php
/**
 * SportsZone - Groups Request Membership
 *
 * @since 3.0.0
 * @version 3.1.0
 */

sz_nouveau_group_hook( 'before', 'request_membership_content' ); ?>

<?php if ( ! sz_group_has_requested_membership() ) : ?>
	<p>
		<?php
		echo esc_html(
			sprintf(
				/* translators: %s = group name */
				__( 'You are requesting to become a member of the group "%s".', 'sportszone' ),
				sz_get_group_name()
			)
		);
		?>
	</p>

	<form action="<?php sz_group_form_action( 'request-membership' ); ?>" method="post" name="request-membership-form" id="request-membership-form" class="standard-form">
		<label for="group-request-membership-comments"><?php esc_html( 'Comments (optional)', 'sportszone' ); ?></label>
		<textarea name="group-request-membership-comments" id="group-request-membership-comments"></textarea>

		<?php sz_nouveau_group_hook( '', 'request_membership_content' ); ?>

		<p><input type="submit" name="group-request-send" id="group-request-send" value="<?php echo esc_attr_x( 'Send Request', 'button', 'sportszone' ); ?>" />

		<?php wp_nonce_field( 'groups_request_membership' ); ?>
	</form><!-- #request-membership-form -->
<?php endif; ?>

<?php
sz_nouveau_group_hook( 'after', 'request_membership_content' );
