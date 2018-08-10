<?php get_header( 'sportszone' ); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'sz_before_register_page' ); ?>

		<div class="page" id="register-page">

			<form action="" name="signup_form" id="signup_form" class="standard-form" method="post" enctype="multipart/form-data">

			<?php if ( 'registration-disabled' == sz_get_current_signup_step() ) : ?>
				<?php do_action( 'template_notices' ); ?>
				<?php do_action( 'sz_before_registration_disabled' ); ?>

					<p><?php _e( 'User registration is currently not allowed.', 'sportszone' ); ?></p>

				<?php do_action( 'sz_after_registration_disabled' ); ?>
			<?php endif; // registration-disabled signup setp ?>

			<?php if ( 'request-details' == sz_get_current_signup_step() ) : ?>

				<h2><?php _e( 'Create an Account', 'sportszone' ); ?></h2>

				<?php do_action( 'template_notices' ); ?>

				<p><?php _e( 'Registering for this site is easy, just fill in the fields below and we\'ll get a new account set up for you in no time.', 'sportszone' ); ?></p>

				<?php do_action( 'sz_before_account_details_fields' ); ?>

				<div class="register-section" id="basic-details-section">

					<?php /***** Basic Account Details ******/ ?>

					<h4><?php _e( 'Account Details', 'sportszone' ); ?></h4>

					<label for="signup_username"><?php _e( 'Username', 'sportszone' ); ?> <?php _e( '(required)', 'sportszone' ); ?></label>
					<?php do_action( 'sz_signup_username_errors' ); ?>
					<input type="text" name="signup_username" id="signup_username" value="<?php sz_signup_username_value(); ?>" />

					<label for="signup_email"><?php _e( 'Email Address', 'sportszone' ); ?> <?php _e( '(required)', 'sportszone' ); ?></label>
					<?php do_action( 'sz_signup_email_errors' ); ?>
					<input type="text" name="signup_email" id="signup_email" value="<?php sz_signup_email_value(); ?>" />

					<label for="signup_password"><?php _e( 'Choose a Password', 'sportszone' ); ?> <?php _e( '(required)', 'sportszone' ); ?></label>
					<?php do_action( 'sz_signup_password_errors' ); ?>
					<input type="password" name="signup_password" id="signup_password" value="" />

					<label for="signup_password_confirm"><?php _e( 'Confirm Password', 'sportszone' ); ?> <?php _e( '(required)', 'sportszone' ); ?></label>
					<?php do_action( 'sz_signup_password_confirm_errors' ); ?>
					<input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" />

					<?php do_action( 'sz_account_details_fields' ); ?>

				</div><!-- #basic-details-section -->

				<?php do_action( 'sz_after_account_details_fields' ); ?>

				<?php /***** Extra Profile Details ******/ ?>

				<?php if ( sz_is_active( 'xprofile' ) ) : ?>

					<?php do_action( 'sz_before_signup_profile_fields' ); ?>

					<div class="register-section" id="profile-details-section">

						<h4><?php _e( 'Profile Details', 'sportszone' ); ?></h4>

						<?php /* Use the profile field loop to render input fields for the 'base' profile field group */ ?>
						<?php if ( sz_is_active( 'xprofile' ) ) : if ( sz_has_profile( array( 'profile_group_id' => 1, 'fetch_field_data' => false ) ) ) : while ( sz_profile_groups() ) : sz_the_profile_group(); ?>

						<?php while ( sz_profile_fields() ) : sz_the_profile_field(); ?>

							<div class="editfield">

								<?php if ( 'textbox' == sz_get_the_profile_field_type() ) : ?>

									<label for="<?php sz_the_profile_field_input_name(); ?>"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></label>
									<?php do_action( sz_get_the_profile_field_errors_action() ); ?>
									<input type="text" name="<?php sz_the_profile_field_input_name(); ?>" id="<?php sz_the_profile_field_input_name(); ?>" value="<?php sz_the_profile_field_edit_value(); ?>" />

								<?php endif; ?>

								<?php if ( 'textarea' == sz_get_the_profile_field_type() ) : ?>

									<label for="<?php sz_the_profile_field_input_name(); ?>"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></label>
									<?php do_action( sz_get_the_profile_field_errors_action() ); ?>
									<textarea rows="5" cols="40" name="<?php sz_the_profile_field_input_name(); ?>" id="<?php sz_the_profile_field_input_name(); ?>"><?php sz_the_profile_field_edit_value(); ?></textarea>

								<?php endif; ?>

								<?php if ( 'selectbox' == sz_get_the_profile_field_type() ) : ?>

									<label for="<?php sz_the_profile_field_input_name(); ?>"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></label>
									<?php do_action( sz_get_the_profile_field_errors_action() ); ?>
									<select name="<?php sz_the_profile_field_input_name(); ?>" id="<?php sz_the_profile_field_input_name(); ?>">
										<?php sz_the_profile_field_options(); ?>
									</select>

								<?php endif; ?>

								<?php if ( 'multiselectbox' == sz_get_the_profile_field_type() ) : ?>

									<label for="<?php sz_the_profile_field_input_name(); ?>"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></label>
									<?php do_action( sz_get_the_profile_field_errors_action() ); ?>
									<select name="<?php sz_the_profile_field_input_name(); ?>" id="<?php sz_the_profile_field_input_name(); ?>" multiple="multiple">
										<?php sz_the_profile_field_options(); ?>
									</select>

								<?php endif; ?>

								<?php if ( 'radio' == sz_get_the_profile_field_type() ) : ?>

									<div class="radio">
										<span class="label"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></span>

										<?php do_action( sz_get_the_profile_field_errors_action() ); ?>
										<?php sz_the_profile_field_options(); ?>

										<?php if ( !sz_get_the_profile_field_is_required() ) : ?>
											<a class="clear-value" href="javascript:clear( '<?php sz_the_profile_field_input_name(); ?>' );"><?php _e( 'Clear', 'sportszone' ); ?></a>
										<?php endif; ?>
									</div>

								<?php endif; ?>

								<?php if ( 'checkbox' == sz_get_the_profile_field_type() ) : ?>

									<div class="checkbox">
										<span class="label"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></span>

										<?php do_action( sz_get_the_profile_field_errors_action() ); ?>
										<?php sz_the_profile_field_options(); ?>
									</div>

								<?php endif; ?>

								<?php if ( 'datebox' == sz_get_the_profile_field_type() ) : ?>

									<div class="datebox">
										<label for="<?php sz_the_profile_field_input_name(); ?>_day"><?php sz_the_profile_field_name(); ?> <?php if ( sz_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'sportszone' ); ?><?php endif; ?></label>
										<?php do_action( sz_get_the_profile_field_errors_action() ); ?>

										<select name="<?php sz_the_profile_field_input_name(); ?>_day" id="<?php sz_the_profile_field_input_name(); ?>_day">
											<?php sz_the_profile_field_options( 'type=day' ); ?>
										</select>

										<select name="<?php sz_the_profile_field_input_name(); ?>_month" id="<?php sz_the_profile_field_input_name(); ?>_month">
											<?php sz_the_profile_field_options( 'type=month' ); ?>
										</select>

										<select name="<?php sz_the_profile_field_input_name(); ?>_year" id="<?php sz_the_profile_field_input_name(); ?>_year">
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
										<?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'sportszone' ), sz_get_the_profile_field_visibility_level_label() ) ?> <a href="#" class="visibility-toggle-link"><?php _ex( 'Change', 'Change profile field visibility level', 'sportszone' ); ?></a>
									</p>

									<div class="field-visibility-settings" id="field-visibility-settings-<?php sz_the_profile_field_id() ?>">
										<fieldset>
											<legend><?php _e( 'Who can see this field?', 'sportszone' ) ?></legend>

											<?php sz_profile_visibility_radio_buttons() ?>

										</fieldset>
										<a class="field-visibility-settings-close" href="#"><?php _e( 'Close', 'sportszone' ) ?></a>

									</div>
								<?php else : ?>
									<p class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php sz_the_profile_field_id() ?>">
										<?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'sportszone' ), sz_get_the_profile_field_visibility_level_label() ) ?>
									</p>
								<?php endif ?>

								<?php do_action( 'sz_custom_profile_edit_fields' ); ?>

								<p class="description"><?php sz_the_profile_field_description(); ?></p>

							</div>

						<?php endwhile; ?>

						<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php sz_the_profile_group_field_ids(); ?>" />

						<?php endwhile; endif; endif; ?>

						<?php do_action( 'sz_signup_profile_fields' ); ?>

					</div><!-- #profile-details-section -->

					<?php do_action( 'sz_after_signup_profile_fields' ); ?>

				<?php endif; ?>

				<?php if ( sz_get_blog_signup_allowed() ) : ?>

					<?php do_action( 'sz_before_blog_details_fields' ); ?>

					<?php /***** Blog Creation Details ******/ ?>

					<div class="register-section" id="blog-details-section">

						<h4><?php _e( 'Blog Details', 'sportszone' ); ?></h4>

						<p><input type="checkbox" name="signup_with_blog" id="signup_with_blog" value="1"<?php if ( (int) sz_get_signup_with_blog_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes, I\'d like to create a new site', 'sportszone' ); ?></p>

						<div id="blog-details"<?php if ( (int) sz_get_signup_with_blog_value() ) : ?>class="show"<?php endif; ?>>

							<label for="signup_blog_url"><?php _e( 'Blog URL', 'sportszone' ); ?> <?php _e( '(required)', 'sportszone' ); ?></label>
							<?php do_action( 'sz_signup_blog_url_errors' ); ?>

							<?php if ( is_subdomain_install() ) : ?>
								http:// <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php sz_signup_blog_url_value(); ?>" /> .<?php sz_blogs_subdomain_base(); ?>
							<?php else : ?>
								<?php echo home_url( '/' ); ?> <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php sz_signup_blog_url_value(); ?>" />
							<?php endif; ?>

							<label for="signup_blog_title"><?php _e( 'Site Title', 'sportszone' ); ?> <?php _e( '(required)', 'sportszone' ); ?></label>
							<?php do_action( 'sz_signup_blog_title_errors' ); ?>
							<input type="text" name="signup_blog_title" id="signup_blog_title" value="<?php sz_signup_blog_title_value(); ?>" />

							<span class="label"><?php _e( 'I would like my site to appear in search engines, and in public listings around this network.', 'sportszone' ); ?>:</span>
							<?php do_action( 'sz_signup_blog_privacy_errors' ); ?>

							<label><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_public" value="public"<?php if ( 'public' == sz_get_signup_blog_privacy_value() || !sz_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes', 'sportszone' ); ?></label>
							<label><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_private" value="private"<?php if ( 'private' == sz_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'No', 'sportszone' ); ?></label>

							<?php do_action( 'sz_blog_details_fields' ); ?>

						</div>

					</div><!-- #blog-details-section -->

					<?php do_action( 'sz_after_blog_details_fields' ); ?>

				<?php endif; ?>

				<?php do_action( 'sz_before_registration_submit_buttons' ); ?>

				<div class="submit">
					<input type="submit" name="signup_submit" id="signup_submit" value="<?php esc_attr_e( 'Complete Sign Up', 'sportszone' ); ?>" />
				</div>

				<?php do_action( 'sz_after_registration_submit_buttons' ); ?>

				<?php wp_nonce_field( 'sz_new_signup' ); ?>

			<?php endif; // request-details signup step ?>

			<?php if ( 'completed-confirmation' == sz_get_current_signup_step() ) : ?>

				<h2><?php _e( 'Check Your Email To Activate Your Account!', 'sportszone' ); ?></h2>

				<?php do_action( 'template_notices' ); ?>
				<?php do_action( 'sz_before_registration_confirmed' ); ?>

				<?php if ( sz_registration_needs_activation() ) : ?>
					<p><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'sportszone' ); ?></p>
				<?php else : ?>
					<p><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'sportszone' ); ?></p>
				<?php endif; ?>

				<?php do_action( 'sz_after_registration_confirmed' ); ?>

			<?php endif; // completed-confirmation signup step ?>

			<?php do_action( 'sz_custom_signup_steps' ); ?>

			</form>

		</div>

		<?php do_action( 'sz_after_register_page' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar( 'sportszone' ); ?>

	<script type="text/javascript">
		jQuery(document).ready( function() {
			if ( jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show') )
				jQuery('div#blog-details').toggle();

			jQuery( 'input#signup_with_blog' ).click( function() {
				jQuery('div#blog-details').fadeOut().toggle();
			});
		});
	</script>

<?php get_footer( 'sportszone' ); ?>
