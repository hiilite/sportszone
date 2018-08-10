<?php
/**
 * SportsZone - Groups Request Membership
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.1.0
 */

/**
 * Fires before the display of the group membership request form.
 *
 * @since 1.1.0
 */
do_action( 'sz_before_group_request_membership_content' ); ?>

<?php if ( !sz_group_has_requested_membership() ) : ?>
	<h2 class="sz-screen-reader-text"><?php esc_html_e( 'Group membership request form', 'sportszone' ); ?></h2>

	<p>
		<?php
		echo esc_html(
			sprintf(
				/* translators:  %s =group name */
				__( 'You are requesting to become a member of the group "%s".', 'sportszone' ),
				sz_get_group_name()
			)
		);
		?>
	</p>

	<form action="<?php sz_group_form_action('request-membership' ); ?>" method="post" name="request-membership-form" id="request-membership-form" class="standard-form">
		<label for="group-request-membership-comments"><?php esc_html_e( 'Comments (optional)', 'sportszone' ); ?></label>
		<textarea name="group-request-membership-comments" id="group-request-membership-comments"></textarea>

		<?php

		/**
		 * Fires after the textarea for the group membership request form.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_group_request_membership_content' ); ?>

		<p><input type="submit" name="group-request-send" id="group-request-send" value="<?php echo esc_attr_x( 'Send Request', 'button', 'sportszone' ); ?>" />

		<?php wp_nonce_field( 'groups_request_membership' ); ?>
	</form><!-- #request-membership-form -->
<?php endif; ?>

<?php

/**
 * Fires after the display of the group membership request form.
 *
 * @since 1.1.0
 */
do_action( 'sz_after_group_request_membership_content' );
