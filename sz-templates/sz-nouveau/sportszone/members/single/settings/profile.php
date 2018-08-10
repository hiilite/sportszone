<?php
/**
 * SportsZone - Members Settings ( Profile )
 *
 * @since 3.0.0
 * @version 3.1.0
 */

sz_nouveau_member_hook( 'before', 'settings_template' ); ?>

<h2 class="screen-heading profile-settings-screen">
	<?php esc_html_e( 'Profile Visibility Settings', 'sportszone' ); ?>
</h2>

<p class="sz-help-text profile-visibility-info">
	<?php esc_html_e( 'Select who may see your profile details.', 'sportszone' ); ?>
</p>

<form action="<?php echo esc_url( sz_displayed_user_domain() . sz_get_settings_slug() . '/profile/' ); ?>" method="post" class="standard-form" id="settings-form">

	<?php if ( sz_xprofile_get_settings_fields() ) : ?>

		<?php
		while ( sz_profile_groups() ) :
			sz_the_profile_group();
		?>

			<?php if ( sz_profile_fields() ) : ?>

				<table class="profile-settings sz-tables-user" id="<?php echo esc_attr( 'xprofile-settings-' . sz_get_the_profile_group_slug() ); ?>">
					<thead>
						<tr>
							<th class="title field-group-name"><?php sz_the_profile_group_name(); ?></th>
							<th class="title"><?php esc_html_e( 'Visibility', 'sportszone' ); ?></th>
						</tr>
					</thead>

					<tbody>

						<?php
						while ( sz_profile_fields() ) :
							sz_the_profile_field();
						?>

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

	<input type="hidden" name="field_ids" id="field_ids" value="<?php sz_the_profile_field_ids(); ?>" />

	<?php sz_nouveau_submit_button( 'members-profile-settings' ); ?>

</form>

<?php
sz_nouveau_member_hook( 'after', 'settings_template' );
