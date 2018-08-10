<?php
/**
 * SportsZone - Members Activate
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<div id="sportszone">

	<?php

	/**
	 * Fires before the display of the member activation page.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_before_activation_page' ); ?>

	<div class="page" id="activate-page">

		<div id="template-notices" role="alert" aria-atomic="true">
			<?php

			/** This action is documented in sz-templates/sz-legacy/sportszone/activity/index.php */
			do_action( 'template_notices' ); ?>

		</div>

		<?php

		/**
		 * Fires before the display of the member activation page content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_before_activate_content' ); ?>

		<?php if ( sz_account_was_activated() ) : ?>

			<?php if ( isset( $_GET['e'] ) ) : ?>
				<p><?php _e( 'Your account was activated successfully! Your account details have been sent to you in a separate email.', 'sportszone' ); ?></p>
			<?php else : ?>
				<p><?php printf( __( 'Your account was activated successfully! You can now <a href="%s">log in</a> with the username and password you provided when you signed up.', 'sportszone' ), wp_login_url( sz_get_root_domain() ) ); ?></p>
			<?php endif; ?>

		<?php else : ?>

			<p><?php _e( 'Please provide a valid activation key.', 'sportszone' ); ?></p>

			<form action="" method="post" class="standard-form" id="activation-form">

				<label for="key"><?php _e( 'Activation Key:', 'sportszone' ); ?></label>
				<input type="text" name="key" id="key" value="<?php echo esc_attr( sz_get_current_activation_key() ); ?>" />

				<p class="submit">
					<input type="submit" name="submit" value="<?php esc_attr_e( 'Activate', 'sportszone' ); ?>" />
				</p>

			</form>

		<?php endif; ?>

		<?php

		/**
		 * Fires after the display of the member activation page content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_after_activate_content' ); ?>

	</div><!-- .page -->

	<?php

	/**
	 * Fires after the display of the member activation page.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_after_activation_page' ); ?>

</div><!-- #sportszone -->
