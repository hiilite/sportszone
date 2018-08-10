<?php do_action( 'sz_before_profile_loop_content' ); ?>

<?php if ( sz_has_profile() ) : ?>

	<?php while ( sz_profile_groups() ) : sz_the_profile_group(); ?>

		<?php if ( sz_profile_group_has_fields() ) : ?>

			<?php do_action( 'sz_before_profile_field_content' ); ?>

			<div class="sz-widget <?php sz_the_profile_group_slug(); ?>">

				<h4><?php sz_the_profile_group_name(); ?></h4>

				<table class="profile-fields">

					<?php while ( sz_profile_fields() ) : sz_the_profile_field(); ?>

						<?php if ( sz_field_has_data() ) : ?>

							<tr<?php sz_field_css_class(); ?>>

								<td class="label"><?php sz_the_profile_field_name(); ?></td>

								<td class="data"><?php sz_the_profile_field_value(); ?></td>

							</tr>

						<?php endif; ?>

						<?php do_action( 'sz_profile_field_item' ); ?>

					<?php endwhile; ?>

				</table>
			</div>

			<?php do_action( 'sz_after_profile_field_content' ); ?>

		<?php endif; ?>

	<?php endwhile; ?>

	<?php do_action( 'sz_profile_field_buttons' ); ?>

<?php endif; ?>

<?php do_action( 'sz_after_profile_loop_content' ); ?>
