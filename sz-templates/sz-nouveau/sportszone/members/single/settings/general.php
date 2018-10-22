<?php
/**
 * SportsZone - Members Settings ( General )
 *
 * @since 3.0.0
 * @version 3.1.0
 */

sz_nouveau_member_hook( 'before', 'settings_template' ); ?>

<div class="sz-info-box">
	<h2 class="screen-heading general-settings-screen">
		<?php _e( 'Email & Password', 'sportszone' ); ?>
	</h2>
	
	<p class="info email-pwd-info">
		<?php _e( 'Update your email and or password.', 'sportszone' ); ?>
	</p>
	
	<form action="<?php echo esc_url( sz_displayed_user_domain() . sz_get_settings_slug() . '/general' ); ?>" method="post" class="standard-form" id="settings-form">
	
		<?php if ( ! is_super_admin() ) : ?>
	
			<label for="pwd"><?php _e( 'Current Password <span>(required to update email or change current password)</span>', 'sportszone' ); ?></label>
			<input type="password" name="pwd" id="pwd" size="16" value="" class="settings-input small" <?php sz_form_field_attributes( 'password' ); ?>/> &nbsp;<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'sportszone' ); ?></a>
	
		<?php endif; ?>
	
		<label for="email"><?php _e( 'Account Email', 'sportszone' ); ?></label>
		<input type="email" name="email" id="email" value="<?php echo esc_attr( sz_get_displayed_user_email() ); ?>" class="settings-input" <?php sz_form_field_attributes( 'email' ); ?>/>
	
		<div class="info sz-feedback">
			<span class="sz-icon" aria-hidden="true"></span>
			<p class="text"><?php esc_html_e( 'Leave password fields blank for no change', 'sportszone' ); ?></p>
		</div>
	
		<label for="pass1"><?php esc_html_e( 'Add Your New Password', 'sportszone' ); ?></label>
		<input type="password" name="pass1" id="pass1" size="16" value="" class="settings-input small password-entry" <?php sz_form_field_attributes( 'password' ); ?>/>
	
		<label for="pass2" class="repeated-pwd"><?php esc_html_e( 'Repeat Your New Password', 'sportszone' ); ?></label>
		<input type="password" name="pass2" id="pass2" size="16" value="" class="settings-input small password-entry-confirm" <?php sz_form_field_attributes( 'password' ); ?>/>
	
		<div id="pass-strength-result"></div>
	
		<?php sz_nouveau_submit_button( 'members-general-settings' ); ?>
	
	</form>
</div>
<?php
sz_nouveau_member_hook( 'after', 'settings_template' );
