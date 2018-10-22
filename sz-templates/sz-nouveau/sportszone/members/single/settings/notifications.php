<?php
/**
 * SportsZone - Members Settings ( Notifications )
 *
 * @since 3.0.0
 * @version 3.0.0
 */

sz_nouveau_member_hook( 'before', 'settings_template' ); ?>

<div class="sz-info-box">
	<h2 class="screen-heading email-settings-screen">
		<?php _e( 'Email Notifications', 'sportszone' ); ?>
	</h2>
	
	<p class="sz-help-text email-notifications-info">
		<?php _e( 'Set your email notification preferences.', 'sportszone' ); ?>
	</p>
	
	<form action="<?php echo esc_url( sz_displayed_user_domain() . sz_get_settings_slug() . '/notifications' ); ?>" method="post" class="standard-form" id="settings-form">
	
		<?php sz_nouveau_member_email_notice_settings(); ?>
	
		<?php sz_nouveau_submit_button( 'member-notifications-settings' ); ?>
	
	</form>
</div>

<?php
sz_nouveau_member_hook( 'after', 'settings_template' );
