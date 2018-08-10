<?php
/**
 * SportsZone - Members Single Profile Edit
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/**
 * Fires after the display of member profile edit content.
 *
 * @since 1.1.0
 */
do_action( 'sz_before_profile_edit_content' );

if ( sz_has_profile( 'profile_group_id=' . sz_get_current_profile_group_id() ) ) :
	while ( sz_profile_groups() ) : sz_the_profile_group(); ?>

<form action="<?php sz_the_profile_group_edit_form_action(); ?>" method="post" id="profile-edit-form" class="standard-form <?php sz_the_profile_group_slug(); ?>">

	<?php

		/** This action is documented in sz-templates/sz-legacy/sportszone/members/single/profile/profile-wp.php */
		do_action( 'sz_before_profile_field_content' ); ?>

		<h2><?php printf( __( "Editing '%s' Profile Group", 'sportszone' ), sz_get_the_profile_group_name() ); ?></h2>

		<?php if ( sz_profile_has_multiple_groups() ) : ?>
			<ul class="button-nav" aria-label="<?php esc_attr_e( 'Profile field groups', 'sportszone' ); ?>" role="navigation">

				<?php sz_profile_group_tabs(); ?>

			</ul>
		<?php endif ;?>

		<div class="clear"></div>

		<?php while ( sz_profile_fields() ) : sz_the_profile_field(); ?>

			<div<?php sz_field_css_class( 'editfield' ); ?>>
				<fieldset>

				<?php
				$field_type = sz_xprofile_create_field_type( sz_get_the_profile_field_type() );
				$field_type->edit_field_html();

				/**
				 * Fires before the display of visibility options for the field.
				 *
				 * @since 1.7.0
				 */
				do_action( 'sz_custom_profile_edit_fields_pre_visibility' );
				?>

				<?php if ( sz_current_user_can( 'sz_xprofile_change_field_visibility' ) ) : ?>
					<p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-<?php sz_the_profile_field_id() ?>"><span id="<?php sz_the_profile_field_input_name(); ?>-2">
						<?php
						printf(
							__( 'This field can be seen by: %s', 'sportszone' ),
							'<span class="current-visibility-level">' . sz_get_the_profile_field_visibility_level_label() . '</span>'
						);
						?>
						</span>
						<button type="button" class="visibility-toggle-link" aria-describedby="<?php sz_the_profile_field_input_name(); ?>-2" aria-expanded="false"><?php _ex( 'Change', 'Change profile field visibility level', 'sportszone' ); ?></button>
					</p>

					<div class="field-visibility-settings" id="field-visibility-settings-<?php sz_the_profile_field_id() ?>">
						<fieldset>
							<legend><?php _e( 'Who can see this field?', 'sportszone' ) ?></legend>

							<?php sz_profile_visibility_radio_buttons() ?>

						</fieldset>
						<button type="button" class="field-visibility-settings-close"><?php _e( 'Close', 'sportszone' ) ?></button>
					</div>
				<?php else : ?>
					<div class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php sz_the_profile_field_id() ?>">
						<?php
						printf(
							__( 'This field can be seen by: %s', 'sportszone' ),
							'<span class="current-visibility-level">' . sz_get_the_profile_field_visibility_level_label() . '</span>'
						);
						?>
					</div>
				<?php endif ?>

				<?php

				/**
				 * Fires after the visibility options for a field.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_custom_profile_edit_fields' ); ?>

				</fieldset>
			</div>

		<?php endwhile; ?>

	<?php

	/** This action is documented in sz-templates/sz-legacy/sportszone/members/single/profile/profile-wp.php */
	do_action( 'sz_after_profile_field_content' ); ?>

	<div class="submit">
		<input type="submit" name="profile-group-edit-submit" id="profile-group-edit-submit" value="<?php esc_attr_e( 'Save Changes', 'sportszone' ); ?> " />
	</div>

	<input type="hidden" name="field_ids" id="field_ids" value="<?php sz_the_profile_field_ids(); ?>" />

	<?php wp_nonce_field( 'sz_xprofile_edit' ); ?>

</form>

<?php endwhile; endif; ?>

<?php

/**
 * Fires after the display of member profile edit content.
 *
 * @since 1.1.0
 */
do_action( 'sz_after_profile_edit_content' );
