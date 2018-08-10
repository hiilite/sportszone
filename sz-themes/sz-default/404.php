<?php get_header(); ?>

	<div id="content">
		<div class="padder one-column">
			<?php do_action( 'sz_before_404' ); ?>
			<div id="post-0" class="post page-404 error404 not-found" role="main">
				<h2 class="posttitle"><?php _e( "Page not found", 'sportszone' ); ?></h2>

				<p><?php _e( "We're sorry, but we can't find the page that you're looking for. Perhaps searching will help.", 'sportszone' ); ?></p>
				<?php get_search_form(); ?>

				<?php do_action( 'sz_404' ); ?>
			</div>

			<?php do_action( 'sz_after_404' ); ?>
		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_footer(); ?>