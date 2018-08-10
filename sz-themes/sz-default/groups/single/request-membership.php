<?php do_action( 'sz_before_group_request_membership_content' ); ?>

<?php if ( !sz_group_has_requested_membership() ) : ?>
	<p><?php printf( __( "You are requesting to become a member of the group '%s'.", "sportszone" ), sz_get_group_name( false ) ); ?></p>

	<form action="<?php sz_group_form_action('request-membership'); ?>" method="post" name="request-membership-form" id="request-membership-form" class="standard-form">
		<label for="group-request-membership-comments"><?php _e( 'Comments (optional)', 'sportszone' ); ?></label>
		<textarea name="group-request-membership-comments" id="group-request-membership-comments"></textarea>

		<?php do_action( 'sz_group_request_membership_content' ); ?>

		<p><input type="submit" name="group-request-send" id="group-request-send" value="<?php esc_attr_e( 'Send Request', 'sportszone' ); ?>" />

		<?php wp_nonce_field( 'groups_request_membership' ); ?>
	</form><!-- #request-membership-form -->
<?php endif; ?>

<?php do_action( 'sz_after_group_request_membership_content' ); ?>
