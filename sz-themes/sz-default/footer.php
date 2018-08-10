		</div> <!-- #container -->

		<?php do_action( 'sz_after_container' ); ?>
		<?php do_action( 'sz_before_footer'   ); ?>

		<div id="footer">
			<?php if ( is_active_sidebar( 'first-footer-widget-area' ) || is_active_sidebar( 'second-footer-widget-area' ) || is_active_sidebar( 'third-footer-widget-area' ) || is_active_sidebar( 'fourth-footer-widget-area' ) ) : ?>
				<div id="footer-widgets">
					<?php get_sidebar( 'footer' ); ?>
				</div>
			<?php endif; ?>

			<div id="site-generator" role="contentinfo">
				<?php do_action( 'sz_dtheme_credits' ); ?>
				<p><?php printf( __( 'Proudly powered by <a href="%1$s">WordPress</a> and <a href="%2$s">SportsZone</a>.', 'sportszone' ), 'http://wordpress.org', 'http://sportszone.org' ); ?></p>
			</div>

			<?php do_action( 'sz_footer' ); ?>

		</div><!-- #footer -->

		<?php do_action( 'sz_after_footer' ); ?>

		<?php wp_footer(); ?>

	</body>

</html>