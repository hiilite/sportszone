<?php

/**
 * SportsZone Delete Account
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

				<h3><?php _e( 'Capabilities', 'sportszone' ); ?></h3>

				<form action="<?php echo sz_displayed_user_domain() . sz_get_settings_slug() . '/capabilities/'; ?>" name="account-capabilities-form" id="account-capabilities-form" class="standard-form" method="post">

					<?php do_action( 'sz_members_capabilities_account_before_submit' ); ?>

					<label>
						<input type="checkbox" name="user-spammer" id="user-spammer" value="1" <?php checked( sz_is_user_spammer( sz_displayed_user_id() ) ); ?> />
						 <?php _e( 'This user is a spammer.', 'sportszone' ); ?>
					</label>

					<div class="submit">
						<input type="submit" value="<?php esc_attr_e( 'Save', 'sportszone' ); ?>" id="capabilities-submit" name="capabilities-submit" />
					</div>

					<?php do_action( 'sz_members_capabilities_account_after_submit' ); ?>

					<?php wp_nonce_field( 'capabilities' ); ?>

				</form>

				<?php do_action( 'sz_after_member_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'sz_after_member_settings_template' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_sidebar( 'sportszone' ); ?>

<?php get_footer( 'sportszone' ); ?>