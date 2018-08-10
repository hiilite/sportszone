<?php do_action( 'sz_before_sidebar' ); ?>

<div id="sidebar" role="complementary">
	<div class="padder">

	<?php do_action( 'sz_inside_before_sidebar' ); ?>

	<?php if ( is_user_logged_in() ) : ?>

		<?php do_action( 'sz_before_sidebar_me' ); ?>

		<div id="sidebar-me">
			<a href="<?php echo sz_loggedin_user_domain(); ?>">
				<?php sz_loggedin_user_avatar( 'type=thumb&width=40&height=40' ); ?>
			</a>

			<h4><?php echo sz_core_get_userlink( sz_loggedin_user_id() ); ?></h4>
			<a class="button logout" href="<?php echo wp_logout_url( wp_guess_url() ); ?>"><?php _e( 'Log Out', 'sportszone' ); ?></a>

			<?php do_action( 'sz_sidebar_me' ); ?>
		</div>

		<?php do_action( 'sz_after_sidebar_me' ); ?>

		<?php if ( sz_is_active( 'messages' ) ) : ?>
			<?php sz_message_get_notices(); /* Site wide notices to all users */ ?>
		<?php endif; ?>

	<?php else : ?>

		<?php do_action( 'sz_before_sidebar_login_form' ); ?>

		<?php if ( sz_get_signup_allowed() ) : ?>
		
			<p id="login-text">

				<?php printf( __( 'Please <a href="%s" title="Create an account">create an account</a> to get started.', 'sportszone' ), sz_get_signup_page() ); ?>

			</p>

		<?php endif; ?>

		<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ); ?>" method="post">
			<label><?php _e( 'Username', 'sportszone' ); ?><br />
			<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php if ( isset( $user_login) ) echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>

			<label><?php _e( 'Password', 'sportszone' ); ?><br />
			<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" /></label>

			<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" /> <?php _e( 'Remember Me', 'sportszone' ); ?></label></p>

			<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php esc_attr_e( 'Log In', 'sportszone' ); ?>" tabindex="100" />

			<?php do_action( 'sz_sidebar_login_form' ); ?>

		</form>

		<?php do_action( 'sz_after_sidebar_login_form' ); ?>

	<?php endif; ?>

	<?php /* Show forum tags on the forums directory */
	if ( sz_is_active( 'forums' ) && sz_is_forums_component() && sz_is_directory() ) : ?>
		<div id="forum-directory-tags" class="widget tags">
			<h3 class="widgettitle"><?php _e( 'Forum Topic Tags', 'sportszone' ); ?></h3>
			<div id="tag-text"><?php sz_forums_tag_heat_map(); ?></div>
		</div>
	<?php endif; ?>

	<?php dynamic_sidebar( 'sidebar-1' ); ?>

	<?php do_action( 'sz_inside_after_sidebar' ); ?>

	<?php wp_meta(); ?>

	</div><!-- .padder -->
</div><!-- #sidebar -->

<?php do_action( 'sz_after_sidebar' ); ?>
