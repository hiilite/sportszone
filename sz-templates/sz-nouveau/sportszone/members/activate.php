<?php
/**
 * SportsZone - Members Activate
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

	<?php sz_nouveau_activation_hook( 'before', 'page' ); ?>

	<div class="page" id="activate-page">

		<?php sz_nouveau_template_notices(); ?>

		<?php sz_nouveau_activation_hook( 'before', 'content' ); ?>

		<?php if ( sz_account_was_activated() ) : ?>

			<?php if ( isset( $_GET['e'] ) ) : ?>
				<p><?php esc_html_e( 'Your account was activated successfully! Your account details have been sent to you in a separate email.', 'sportszone' ); ?></p>
			<?php else : ?>
				<p>
					<?php
					echo esc_html(
						sprintf(
							__( 'Your account was activated successfully! You can now <a href="%s">log in</a> with the username and password you provided when you signed up.', 'sportszone' ),
							wp_login_url( sz_get_root_domain() )
						)
					);
					?>
				</p>
			<?php endif; ?>

		<?php else : ?>

			<p><?php esc_html_e( 'Please provide a valid activation key.', 'sportszone' ); ?></p>

			<form action="" method="post" class="standard-form" id="activation-form">

				<label for="key"><?php esc_html_e( 'Activation Key:', 'sportszone' ); ?></label>
				<input type="text" name="key" id="key" value="<?php echo esc_attr( sz_get_current_activation_key() ); ?>" />

				<p class="submit">
					<input type="submit" name="submit" value="<?php echo esc_attr_x( 'Activate', 'button', 'sportszone' ); ?>" />
				</p>

			</form>

		<?php endif; ?>

		<?php sz_nouveau_activation_hook( 'after', 'content' ); ?>

	</div><!-- .page -->

	<?php sz_nouveau_activation_hook( 'after', 'page' ); ?>
