<?php

/**
 * SportsZone Notification Settings
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

				<h3><?php _e( 'Email Notification', 'sportszone' ); ?></h3>

				<?php do_action( 'sz_template_content' ); ?>

				<form action="<?php echo sz_displayed_user_domain() . sz_get_settings_slug() . '/notifications'; ?>" method="post" class="standard-form" id="settings-form">
					<p><?php _e( 'Send an email notice when:', 'sportszone' ); ?></p>

					<?php do_action( 'sz_notification_settings' ); ?>

					<?php do_action( 'sz_members_notification_settings_before_submit' ); ?>

					<div class="submit">
						<input type="submit" name="submit" value="<?php esc_attr_e( 'Save Changes', 'sportszone' ); ?>" id="submit" class="auto" />
					</div>

					<?php do_action( 'sz_members_notification_settings_after_submit' ); ?>

					<?php wp_nonce_field('sz_settings_notifications'); ?>

				</form>

				<?php do_action( 'sz_after_member_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'sz_after_member_settings_template' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_sidebar( 'sportszone' ); ?>

<?php get_footer( 'sportszone' ); ?>