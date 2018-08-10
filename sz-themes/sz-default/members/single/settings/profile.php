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

				<h3><?php _e( 'Profile Settings', 'sportszone' ); ?></h3>

				<?php do_action( 'sz_template_content' ); ?>

				<form action="<?php echo trailingslashit( sz_displayed_user_domain() . sz_get_settings_slug() . '/profile' ); ?>" method="post" class="standard-form" id="settings-form">

					<?php if ( sz_xprofile_get_settings_fields() ) : ?>

						<?php while ( sz_profile_groups() ) : sz_the_profile_group(); ?>

							<?php if ( sz_profile_fields() ) : ?>

								<table class="profile-settings" id="xprofile-settings-<?php sz_the_profile_group_slug(); ?>">
									<thead>
										<tr>
											<th class="title field-group-name"><?php sz_the_profile_group_name(); ?></th>
											<th class="title"><?php _e( 'Visibility', 'sportszone' ); ?></th>
										</tr>
									</thead>

									<tbody>

										<?php while ( sz_profile_fields() ) : sz_the_profile_field(); ?>

											<tr <?php sz_field_css_class(); ?>>
												<td class="field-name"><?php sz_the_profile_field_name(); ?></td>
												<td class="field-visibility"><?php sz_profile_settings_visibility_select(); ?></td>
											</tr>

										<?php endwhile; ?>

									</tbody>
								</table>

							<?php endif; ?>

						<?php endwhile; ?>

					<?php endif; ?>

					<?php do_action( 'sz_core_xprofile_settings_before_submit' ); ?>

					<div class="submit">
						<input id="submit" type="submit" name="xprofile-settings-submit" value="<?php esc_attr_e( 'Save Settings', 'sportszone' ); ?>" class="auto" />
					</div>

					<?php do_action( 'sz_core_xprofile_settings_after_submit' ); ?>

					<?php wp_nonce_field( 'sz_xprofile_settings' ); ?>

					<input type="hidden" name="field_ids" id="field_ids" value="<?php sz_the_profile_group_field_ids(); ?>" />

				</form>

			</div><!-- #item-body -->

			<?php do_action( 'sz_after_member_settings_template' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_sidebar( 'sportszone' ); ?>

<?php get_footer( 'sportszone' ); ?>