<?php
/**
 * SportsZone - Members Settings Capabilities
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/** This action is documented in sz-templates/sz-legacy/sportszone/members/single/settings/profile.php */
do_action( 'sz_before_member_settings_template' ); ?>

<form action="<?php echo sz_displayed_user_domain() . sz_get_settings_slug() . '/capabilities/'; ?>" name="account-capabilities-form" id="account-capabilities-form" class="standard-form" method="post">

	<?php

	/**
	 * Fires before the display of the submit button for user capabilities saving.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_members_capabilities_account_before_submit' ); ?>

	<label for="user-spammer">
		<input type="checkbox" name="user-spammer" id="user-spammer" value="1" <?php checked( sz_is_user_spammer( sz_displayed_user_id() ) ); ?> />
		 <?php _e( 'This user is a spammer.', 'sportszone' ); ?>
	</label>

	<div class="submit">
		<input type="submit" value="<?php esc_attr_e( 'Save', 'sportszone' ); ?>" id="capabilities-submit" name="capabilities-submit" />
	</div>

	<?php

	/**
	 * Fires after the display of the submit button for user capabilities saving.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_members_capabilities_account_after_submit' ); ?>

	<?php wp_nonce_field( 'capabilities' ); ?>

</form>

<?php

/** This action is documented in sz-templates/sz-legacy/sportszone/members/single/settings/profile.php */
do_action( 'sz_after_member_settings_template' );
