<?php
/**
 * SportsZone - Members/Blogs Registration forms
 *
 * @since 3.0.0
 * @version 3.1.0
 */

?>

	<?php sz_nouveau_signup_hook( 'before', 'page' ); ?>

	<div id="register-page"class="page register-page">

		<?php sz_nouveau_template_notices(); ?>

			<?php sz_nouveau_user_feedback( sz_get_current_signup_step() ); ?>

			<form action="" name="signup_form" id="signup-form" class="standard-form signup-form clearfix" method="post" enctype="multipart/form-data">

			<div class="layout-wrap">

			<?php if ( 'request-details' === sz_get_current_signup_step() ) : ?>

				<?php sz_nouveau_signup_hook( 'before', 'account_details' ); ?>

				<div class="register-section default-profile" id="basic-details-section">

					<?php /***** Basic Account Details ******/ ?>

					<h2 class="sz-heading"><?php esc_html_e( 'Account Details', 'sportszone' ); ?></h2>

					<?php sz_nouveau_signup_form(); ?>

				</div><!-- #basic-details-section -->

				<?php sz_nouveau_signup_hook( 'after', 'account_details' ); ?>

				<?php /***** Extra Profile Details ******/ ?>

				<?php if ( sz_is_active( 'xprofile' ) && sz_nouveau_base_account_has_xprofile() ) : ?>

					<?php sz_nouveau_signup_hook( 'before', 'signup_profile' ); ?>

					<div class="register-section extended-profile" id="profile-details-section">

						<h2 class="sz-heading"><?php esc_html_e( 'Profile Details', 'sportszone' ); ?></h2>

						<?php /* Use the profile field loop to render input fields for the 'base' profile field group */ ?>
						<?php while ( sz_profile_groups() ) : sz_the_profile_group(); ?>

							<?php while ( sz_profile_fields() ) : sz_the_profile_field(); ?>

								<div<?php sz_field_css_class( 'editfield' ); ?>>
									<fieldset>

									<?php
									$field_type = sz_xprofile_create_field_type( sz_get_the_profile_field_type() );
									$field_type->edit_field_html();

									sz_nouveau_xprofile_edit_visibilty();
									?>

									</fieldset>
								</div>

							<?php endwhile; ?>

						<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php sz_the_profile_field_ids(); ?>" />

						<?php endwhile; ?>

						<?php sz_nouveau_signup_hook( '', 'signup_profile' ); ?>

					</div><!-- #profile-details-section -->

					<?php sz_nouveau_signup_hook( 'after', 'signup_profile' ); ?>

				<?php endif; ?>

				<?php if ( sz_get_blog_signup_allowed() ) : ?>

					<?php sz_nouveau_signup_hook( 'before', 'blog_details' ); ?>

					<?php /***** Blog Creation Details ******/ ?>

					<div class="register-section blog-details" id="blog-details-section">

						<h2><?php esc_html_e( 'Site Details', 'sportszone' ); ?></h2>

						<p><label for="signup_with_blog"><input type="checkbox" name="signup_with_blog" id="signup_with_blog" value="1" <?php checked( (int) sz_get_signup_with_blog_value(), 1 ); ?>/> <?php esc_html_e( "Yes, i'd like to create a new site", 'sportszone' ); ?></label></p>

						<div id="blog-details"<?php if ( (int) sz_get_signup_with_blog_value() ) : ?>class="show"<?php endif; ?>>

							<?php sz_nouveau_signup_form( 'blog_details' ); ?>

						</div>

					</div><!-- #blog-details-section -->

					<?php sz_nouveau_signup_hook( 'after', 'blog_details' ); ?>

				<?php endif; ?>

				</div><!-- //.layout-wrap -->

				<?php sz_nouveau_submit_button( 'register' ); ?>

			<?php endif; // request-details signup step ?>

			<?php sz_nouveau_signup_hook( 'custom', 'steps' ); ?>

			</form>

	</div>

	<?php sz_nouveau_signup_hook( 'after', 'page' ); ?>
