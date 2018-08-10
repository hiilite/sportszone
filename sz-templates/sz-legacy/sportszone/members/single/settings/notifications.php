<?php
/**
 * SportsZone - Members Settings Notifications
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/** This action is documented in sz-templates/sz-legacy/sportszone/members/single/settings/profile.php */
do_action( 'sz_before_member_settings_template' ); ?>

<h2 class="sz-screen-reader-text"><?php
	/* translators: accessibility text */
	_e( 'Notification settings', 'sportszone' );
?></h2>

<form action="<?php echo sz_displayed_user_domain() . sz_get_settings_slug() . '/notifications'; ?>" method="post" class="standard-form" id="settings-form">
	<p><?php _e( 'Send an email notice when:', 'sportszone' ); ?></p>

	<?php

	/**
	 * Fires at the top of the member template notification settings form.
	 *
	 * @since 1.0.0
	 */
	do_action( 'sz_notification_settings' ); ?>

	<?php

	/**
	 * Fires before the display of the submit button for user notification saving.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_members_notification_settings_before_submit' ); ?>

	<div class="submit">
		<input type="submit" name="submit" value="<?php esc_attr_e( 'Save Changes', 'sportszone' ); ?>" id="submit" class="auto" />
	</div>

	<?php

	/**
	 * Fires after the display of the submit button for user notification saving.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_members_notification_settings_after_submit' ); ?>

	<?php wp_nonce_field('sz_settings_notifications' ); ?>

</form>

<?php

/** This action is documented in sz-templates/sz-legacy/sportszone/members/single/settings/profile.php */
do_action( 'sz_after_member_settings_template' );
