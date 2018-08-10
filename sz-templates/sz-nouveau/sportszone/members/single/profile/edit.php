<?php
/**
 * SportsZone - Members Single Profile Edit
 *
 * @since 3.0.0
 * @version 3.1.0
 */

sz_nouveau_xprofile_hook( 'before', 'edit_content' ); ?>

<h2 class="screen-heading edit-profile-screen"><?php esc_html_e( 'Edit Profile', 'sportszone' ); ?></h2>

<?php if ( sz_has_profile( 'profile_group_id=' . sz_get_current_profile_group_id() ) ) :
	while ( sz_profile_groups() ) :
		sz_the_profile_group();
	?>

		<form action="<?php sz_the_profile_group_edit_form_action(); ?>" method="post" id="profile-edit-form" class="standard-form profile-edit <?php sz_the_profile_group_slug(); ?>">

			<?php sz_nouveau_xprofile_hook( 'before', 'field_content' ); ?>

				<?php if ( sz_profile_has_multiple_groups() ) : ?>
					<ul class="button-tabs button-nav">

						<?php sz_profile_group_tabs(); ?>

					</ul>
				<?php endif; ?>

				<h3 class="screen-heading profile-group-title edit">
					<?php
					printf(
						/* translators: %s = profile field group name */
						__( 'Editing "%s" Profile Group', 'sportszone' ),
						sz_get_the_profile_group_name()
					)
					?>
				</h3>

				<?php
				while ( sz_profile_fields() ) :
					sz_the_profile_field();
				?>

					<div<?php sz_field_css_class( 'editfield' ); ?>>
						<fieldset>

						<?php
						$field_type = sz_xprofile_create_field_type( sz_get_the_profile_field_type() );
						$field_type->edit_field_html();
						?>

						<?php sz_nouveau_xprofile_edit_visibilty(); ?>

						</fieldset>
					</div>

				<?php endwhile; ?>

			<?php sz_nouveau_xprofile_hook( 'after', 'field_content' ); ?>

			<input type="hidden" name="field_ids" id="field_ids" value="<?php sz_the_profile_field_ids(); ?>" />

			<?php sz_nouveau_submit_button( 'member-profile-edit' ); ?>

		</form>

	<?php endwhile; ?>

<?php endif; ?>

<?php
sz_nouveau_xprofile_hook( 'after', 'edit_content' );
