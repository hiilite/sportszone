<?php
/**
 * SportsZone - Members Single Profile
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/** This action is documented in sz-templates/sz-legacy/sportszone/members/single/settings/profile.php */
do_action( 'sz_before_member_settings_template' ); ?>

<h2 class="sz-screen-reader-text"><?php
	/* translators: accessibility text */
	_e( 'Account settings', 'sportszone' );
?></h2>

<form action="<?php echo sz_displayed_user_domain() . sz_get_settings_slug() . '/general'; ?>" method="post" class="standard-form" id="settings-form">

	<?php if ( !is_super_admin() ) : ?>

		<label for="pwd"><?php _e( 'Current Password <span>(required to update email or change current password)</span>', 'sportszone' ); ?></label>
		<input type="password" name="pwd" id="pwd" size="16" value="" class="settings-input small" <?php sz_form_field_attributes( 'password' ); ?>/> &nbsp;<a href="<?php echo wp_lostpassword_url(); ?>"><?php _e( 'Lost your password?', 'sportszone' ); ?></a>

	<?php endif; ?>

	<label for="email"><?php _e( 'Account Email', 'sportszone' ); ?></label>
	<input type="email" name="email" id="email" value="<?php echo sz_get_displayed_user_email(); ?>" class="settings-input" <?php sz_form_field_attributes( 'email' ); ?>/>

	<label for="pass1"><?php _e( 'Change Password <span>(leave blank for no change)</span>', 'sportszone' ); ?></label>
	<input type="password" name="pass1" id="pass1" size="16" value="" class="settings-input small password-entry" <?php sz_form_field_attributes( 'password' ); ?>/>
	<div id="pass-strength-result"></div>
	<label for="pass2"><?php _e( 'Repeat New Password', 'sportszone' );
	?></label>
	<input type="password" name="pass2" id="pass2" size="16" value="" class="settings-input small password-entry-confirm" <?php sz_form_field_attributes( 'password' ); ?>/>

	<?php

	/**
	 * Fires before the display of the submit button for user general settings saving.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_core_general_settings_before_submit' ); ?>

	<div class="submit">
		<input type="submit" name="submit" value="<?php esc_attr_e( 'Save Changes', 'sportszone' ); ?>" id="submit" class="auto" />
	</div>

	<?php

	/**
	 * Fires after the display of the submit button for user general settings saving.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_core_general_settings_after_submit' ); ?>

	<?php wp_nonce_field( 'sz_settings_general' ); ?>

</form>

<?php

/** This action is documented in sz-templates/sz-legacy/sportszone/members/single/settings/profile.php */
do_action( 'sz_after_member_settings_template' );
