<?php

/**
 * SportsZone - Create Blog
 *
 * @package SportsZone
 * @subpackage sz-default
 */

get_header( 'sportszone' ); ?>

	<?php do_action( 'sz_before_directory_blogs_content' ); ?>

	<div id="content">
		<div class="padder" role="main">
		
		<?php do_action( 'sz_before_create_blog_content_template' ); ?>

		<?php do_action( 'template_notices' ); ?>

			<h3><?php _e( 'Create a Site', 'sportszone' ); ?> &nbsp;<a class="button" href="<?php echo trailingslashit( sz_get_root_domain() . '/' . sz_get_blogs_root_slug() ); ?>"><?php _e( 'Site Directory', 'sportszone' ); ?></a></h3>

		<?php do_action( 'sz_before_create_blog_content' ); ?>

		<?php if ( sz_blog_signup_enabled() ) : ?>

			<?php sz_show_blog_signup_form(); ?>

		<?php else: ?>

			<div id="message" class="info">
				<p><?php _e( 'Site registration is currently disabled', 'sportszone' ); ?></p>
			</div>

		<?php endif; ?>

		<?php do_action( 'sz_after_create_blog_content' ); ?>
		
		<?php do_action( 'sz_after_create_blog_content_template' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php do_action( 'sz_after_directory_blogs_content' ); ?>

<?php get_sidebar( 'sportszone' ); ?>
<?php get_footer( 'sportszone' ); ?>

