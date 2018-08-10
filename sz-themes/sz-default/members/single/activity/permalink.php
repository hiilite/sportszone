<?php get_header( 'sportszone' ); ?>

<?php do_action( 'template_notices' ); ?>

<div class="activity no-ajax" role="main">
	<?php if ( sz_has_activities( 'display_comments=threaded&show_hidden=true&include=' . sz_current_action() ) ) : ?>

		<ul id="activity-stream" class="activity-list item-list">
		<?php while ( sz_activities() ) : sz_the_activity(); ?>

			<?php locate_template( array( 'activity/entry.php' ), true ); ?>

		<?php endwhile; ?>
		</ul>

	<?php endif; ?>
</div>

<?php get_footer( 'sportszone' ); ?>