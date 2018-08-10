<?php
/**
 * SportsZone - Activity Loop
 *
 * @version 3.1.0
 */

sz_nouveau_before_loop(); ?>

<?php if ( sz_has_activities( sz_ajax_querystring( 'activity' ) ) ) : ?>

	<?php if ( empty( $_POST['page'] ) || 1 === (int) $_POST['page'] ) : ?>
		<ul class="activity-list item-list sz-list">
	<?php endif; ?>

	<?php
	while ( sz_activities() ) :
		sz_the_activity();
	?>

		<?php sz_get_template_part( 'activity/entry' ); ?>

	<?php endwhile; ?>

	<?php if ( sz_activity_has_more_items() ) : ?>

		<li class="load-more">
			<a href="<?php sz_activity_load_more_link(); ?>"><?php echo esc_html_x( 'Load More', 'button', 'sportszone' ); ?></a>
		</li>

	<?php endif; ?>

	<?php if ( empty( $_POST['page'] ) || 1 === (int) $_POST['page'] ) : ?>
		</ul>
	<?php endif; ?>

<?php else : ?>

		<?php sz_nouveau_user_feedback( 'activity-loop-none' ); ?>

<?php endif; ?>

<?php sz_nouveau_after_loop(); ?>
