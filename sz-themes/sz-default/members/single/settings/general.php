<?php

/**
 * SportsZone Member Settings
 *
 * @package SportsZone
 * @subpackage sz-default
 */

get_header( 'sportszone' ); ?>

	<div id="content">
		<div class="padder">

			<?php do_action( 'sz_before_member_settings_template' ); ?>

			<div id="item-header">

				<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>

						<?php sz_get_displayed_user_nav(); ?>

						<?php do_action( 'sz_member_options_nav' ); ?>

					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body" role="main">

				<?php do_action( 'sz_before_member_body' ); ?>

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>

						<?php sz_get_options_nav(); ?>

						<?php do_action( 'sz_member_plugin_options_nav' ); ?>

					</ul>
				</div><!-- .item-list-tabs -->

				<h3><?php _e( 'General Settings', 'sportszone' ); ?></h3>

				<?php do_action( 'sz_template_content' ); ?>

				<form action="<?php echo sz_displayed_user_domain() . sz_get_settings_slug() . '/general'; ?>" method="post" class="standard-form" id="settings-form">

					<?php if ( !is_super_admin() ) : ?>

						<label for="pwd"><?php _e( 'Current Password <span>(required to update email or change current password)</span>', 'sportszone' ); ?></label>
						<input type="password" name="pwd" id="pwd" size="16" value="" class="settings-input small" /> &nbsp;<a href="<?php echo wp_lostpassword_url(); ?>" title="<?php esc_attr_e( 'Password Lost and Found', 'sportszone' ); ?>"><?php _e( 'Lost your password?', 'sportszone' ); ?></a>

					<?php endif; ?>

					<label for="email"><?php _e( 'Account Email', 'sportszone' ); ?></label>
					<input type="text" name="email" id="email" value="<?php echo sz_get_displayed_user_email(); ?>" class="settings-input" />

					<label for="pass1"><?php _e( 'Change Password <span>(leave blank for no change)</span>', 'sportszone' ); ?></label>
					<input type="password" name="pass1" id="pass1" size="16" value="" class="settings-input small" /> &nbsp;<?php _e( 'New Password', 'sportszone' ); ?><br />
					<input type="password" name="pass2" id="pass2" size="16" value="" class="settings-input small" /> &nbsp;<?php _e( 'Repeat New Password', 'sportszone' ); ?>

					<?php do_action( 'sz_core_general_settings_before_submit' ); ?>

					<div class="submit">
						<input type="submit" name="submit" value="<?php esc_attr_e( 'Save Changes', 'sportszone' ); ?>" id="submit" class="auto" />
					</div>

					<?php do_action( 'sz_core_general_settings_after_submit' ); ?>

					<?php wp_nonce_field( 'sz_settings_general' ); ?>

				</form>

				<?php do_action( 'sz_after_member_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'sz_after_member_settings_template' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_sidebar( 'sportszone' ); ?>

<?php get_footer( 'sportszone' ); ?>