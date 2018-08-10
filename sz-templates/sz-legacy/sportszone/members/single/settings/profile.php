<?php
/**
 * SportsZone - Members Single Profile
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/**
 * Fires before the display of member settings template.
 *
 * @since 1.5.0
 */
do_action( 'sz_before_member_settings_template' ); ?>

<h2 class="sz-screen-reader-text"><?php
	/* translators: accessibility text */
	_e( 'Profile visibility settings', 'sportszone' );
?></h2>

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

	<?php

	/**
	 * Fires before the display of the submit button for user profile saving.
	 *
	 * @since 2.0.0
	 */
	do_action( 'sz_core_xprofile_settings_before_submit' ); ?>

	<div class="submit">
		<input id="submit" type="submit" name="xprofile-settings-submit" value="<?php esc_attr_e( 'Save Settings', 'sportszone' ); ?>" class="auto" />
	</div>

	<?php

	/**
	 * Fires after the display of the submit button for user profile saving.
	 *
	 * @since 2.0.0
	 */
	do_action( 'sz_core_xprofile_settings_after_submit' ); ?>

	<?php wp_nonce_field( 'sz_xprofile_settings' ); ?>

	<input type="hidden" name="field_ids" id="field_ids" value="<?php sz_the_profile_field_ids(); ?>" />

</form>

<?php

/**
 * Fires after the display of member settings template.
 *
 * @since 1.5.0
 */
do_action( 'sz_after_member_settings_template' );
