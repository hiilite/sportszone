<?php do_action( 'sz_before_profile_edit_content' );

if ( sz_has_profile( 'profile_group_id=' . sz_get_current_profile_group_id() ) ) :
	while ( sz_profile_groups() ) : sz_the_profile_group(); ?>

<form action="<?php sz_the_profile_group_edit_form_action(); ?>" method="post" id="profile-edit-form" class="standard-form <?php sz_the_profile_group_slug(); ?>">

	<?php do_action( 'sz_before_profile_field_content' ); ?>

		<h4><?php printf( __( "Editing '%s' Profile Group", "sportszone" ), sz_get_the_profile_group_name() ); ?></h4>

		<ul class="button-nav">

			<?php sz_profile_group_tabs(); ?>

		</ul>

		<div class="clear"></div>

		<?php while ( sz_profile_fields() ) : sz_the_profile_field(); ?>

			<div<?php sz_field_css_class( 'editfield' ); ?>>

				<?php if ( 'textbox' == sz_get_the_profile_field_type() ) : ?>

					<label for="<?php sz_the_profile_field_input_name(); ?>"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></label>
					<input type="text" name="<?php sz_the_profile_field_input_name(); ?>" id="<?php sz_the_profile_field_input_name(); ?>" value="<?php sz_the_profile_field_edit_value(); ?>" <?php if ( sz_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>/>

				<?php endif; ?>

				<?php if ( 'textarea' == sz_get_the_profile_field_type() ) : ?>

					<label for="<?php sz_the_profile_field_input_name(); ?>"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></label>
					<textarea rows="5" cols="40" name="<?php sz_the_profile_field_input_name(); ?>" id="<?php sz_the_profile_field_input_name(); ?>" <?php if ( sz_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>><?php sz_the_profile_field_edit_value(); ?></textarea>

				<?php endif; ?>

				<?php if ( 'selectbox' == sz_get_the_profile_field_type() ) : ?>

					<label for="<?php sz_the_profile_field_input_name(); ?>"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></label>
					<select name="<?php sz_the_profile_field_input_name(); ?>" id="<?php sz_the_profile_field_input_name(); ?>" <?php if ( sz_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>
						<?php sz_the_profile_field_options(); ?>
					</select>

				<?php endif; ?>

				<?php if ( 'multiselectbox' == sz_get_the_profile_field_type() ) : ?>

					<label for="<?php sz_the_profile_field_input_name(); ?>"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></label>
					<select name="<?php sz_the_profile_field_input_name(); ?>" id="<?php sz_the_profile_field_input_name(); ?>" multiple="multiple" <?php if ( sz_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>

						<?php sz_the_profile_field_options(); ?>

					</select>

					<?php if ( !sz_get_the_profile_field_is_required() ) : ?>

						<a class="clear-value" href="javascript:clear( '<?php sz_the_profile_field_input_name(); ?>' );"><?php _e( 'Clear', 'sportszone' ); ?></a>

					<?php endif; ?>

				<?php endif; ?>

				<?php if ( 'radio' == sz_get_the_profile_field_type() ) : ?>

					<div class="radio">
						<span class="label"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></span>

						<?php sz_the_profile_field_options(); ?>

						<?php if ( !sz_get_the_profile_field_is_required() ) : ?>

							<a class="clear-value" href="javascript:clear( '<?php sz_the_profile_field_input_name(); ?>' );"><?php _e( 'Clear', 'sportszone' ); ?></a>

						<?php endif; ?>
					</div>

				<?php endif; ?>

				<?php if ( 'checkbox' == sz_get_the_profile_field_type() ) : ?>

					<div class="checkbox">
						<span class="label"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></span>

						<?php sz_the_profile_field_options(); ?>
					</div>

				<?php endif; ?>

				<?php if ( 'datebox' == sz_get_the_profile_field_type() ) : ?>

					<div class="datebox">
						<label for="<?php sz_the_profile_field_input_name(); ?>_day"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></label>

						<select name="<?php sz_the_profile_field_input_name(); ?>_day" id="<?php sz_the_profile_field_input_name(); ?>_day" <?php if ( sz_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>

							<?php sz_the_profile_field_options( 'type=day' ); ?>

						</select>

						<select name="<?php sz_the_profile_field_input_name(); ?>_month" id="<?php sz_the_profile_field_input_name(); ?>_month" <?php if ( sz_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>

							<?php sz_the_profile_field_options( 'type=month' ); ?>

						</select>

						<select name="<?php sz_the_profile_field_input_name(); ?>_year" id="<?php sz_the_profile_field_input_name(); ?>_year" <?php if ( sz_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>

							<?php sz_the_profile_field_options( 'type=year' ); ?>

						</select>
					</div>

				<?php endif; ?>

				<?php if ( 'url' == sz_get_the_profile_field_type() ) : ?>

					<label for="<?php sz_the_profile_field_input_name(); ?>"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></label>
					<input type="text" name="<?php sz_the_profile_field_input_name(); ?>" id="<?php sz_the_profile_field_input_name(); ?>" value="<?php sz_the_profile_field_edit_value(); ?>" <?php if ( sz_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>/>

				<?php endif; ?>

				<?php do_action( 'sz_custom_profile_edit_fields_pre_visibility' ); ?>

				<?php if ( sz_current_user_can( 'sz_xprofile_change_field_visibility' ) ) : ?>
					<p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-<?php sz_the_profile_field_id() ?>">
						<?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'sportszone' ), sz_get_the_profile_field_visibility_level_label() ) ?> <a href="#" class="visibility-toggle-link"><?php _e( 'Change', 'sportszone' ); ?></a>
					</p>

					<div class="field-visibility-settings" id="field-visibility-settings-<?php sz_the_profile_field_id() ?>">
						<fieldset>
							<legend><?php _e( 'Who can see this field?', 'sportszone' ) ?></legend>

							<?php sz_profile_visibility_radio_buttons() ?>

						</fieldset>
						<a class="field-visibility-settings-close" href="#"><?php _e( 'Close', 'sportszone' ) ?></a>
					</div>
				<?php else : ?>
					<div class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php sz_the_profile_field_id() ?>">
						<?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'sportszone' ), sz_get_the_profile_field_visibility_level_label() ) ?>
					</div>
				<?php endif ?>

				<?php do_action( 'sz_custom_profile_edit_fields' ); ?>

				<p class="description"><?php sz_the_profile_field_description(); ?></p>
			</div>

		<?php endwhile; ?>

	<?php do_action( 'sz_after_profile_field_content' ); ?>

	<div class="submit">
		<input type="submit" name="profile-group-edit-submit" id="profile-group-edit-submit" value="<?php esc_attr_e( 'Save Changes', 'sportszone' ); ?> " />
	</div>

	<input type="hidden" name="field_ids" id="field_ids" value="<?php sz_the_profile_group_field_ids(); ?>" />

	<?php wp_nonce_field( 'sz_xprofile_edit' ); ?>

</form>

<?php endwhile; endif; ?>

<?php do_action( 'sz_after_profile_edit_content' ); ?>
