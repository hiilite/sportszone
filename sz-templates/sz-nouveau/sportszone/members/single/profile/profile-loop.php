<?php
/**
 * SportsZone - Members Profile Loop
 *
 * @since 3.0.0
 * @version 3.1.0
 */

?>

<h2 class="screen-heading view-profile-screen"><?php esc_html_e( 'View Profile', 'sportszone' ); ?></h2>

<?php sz_nouveau_xprofile_hook( 'before', 'loop_content' ); ?>

<?php if ( sz_has_profile() ) : ?>

	<?php
	while ( sz_profile_groups() ) :
		sz_the_profile_group();
	?>

		<?php if ( sz_profile_group_has_fields() ) : ?>

			<?php sz_nouveau_xprofile_hook( 'before', 'field_content' ); ?>

			<div class="sz-widget <?php sz_the_profile_group_slug(); ?>">

				<h3 class="screen-heading profile-group-title">
					<?php sz_the_profile_group_name(); ?>
				</h3>

				<table class="profile-fields sz-tables-user">

					<?php
					while ( sz_profile_fields() ) :
						sz_the_profile_field();
					?>

						<?php if ( sz_field_has_data() ) : ?>

							<tr<?php sz_field_css_class(); ?>>

								<td class="label"><?php sz_the_profile_field_name(); ?></td>

								<td class="data"><?php sz_the_profile_field_value(); ?></td>

							</tr>

						<?php endif; ?>

						<?php sz_nouveau_xprofile_hook( '', 'field_item' ); ?>

					<?php endwhile; ?>

				</table>
			</div>

			<?php sz_nouveau_xprofile_hook( 'after', 'field_content' ); ?>

		<?php endif; ?>

	<?php endwhile; ?>

	<?php sz_nouveau_xprofile_hook( '', 'field_buttons' ); ?>

<?php endif; ?>

<?php
sz_nouveau_xprofile_hook( 'after', 'loop_content' );
