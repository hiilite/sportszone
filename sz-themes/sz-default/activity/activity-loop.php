<?php

/**
 * SportsZone - Activity Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - sz_dtheme_object_filter()
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<?php do_action( 'sz_before_activity_loop' ); ?>

<?php if ( sz_has_activities( sz_ajax_querystring( 'activity' ) ) ) : ?>

	<?php /* Show pagination if JS is not enabled, since the "Load More" link will do nothing */ ?>
	<noscript>
		<div class="pagination">
			<div class="pag-count"><?php sz_activity_pagination_count(); ?></div>
			<div class="pagination-links"><?php sz_activity_pagination_links(); ?></div>
		</div>
	</noscript>

	<?php if ( empty( $_POST['page'] ) ) : ?>

		<ul id="activity-stream" class="activity-list item-list">

	<?php endif; ?>

	<?php while ( sz_activities() ) : sz_the_activity(); ?>

		<?php locate_template( array( 'activity/entry.php' ), true, false ); ?>

	<?php endwhile; ?>

	<?php if ( sz_activity_has_more_items() ) : ?>

		<li class="load-more">
			<a href="#more"><?php _e( 'Load More', 'sportszone' ); ?></a>
		</li>

	<?php endif; ?>

	<?php if ( empty( $_POST['page'] ) ) : ?>

		</ul>

	<?php endif; ?>

<?php else : ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'sportszone' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'sz_after_activity_loop' ); ?>

<?php if ( empty( $_POST['page'] ) ) : ?>

	<form action="" name="activity-loop-form" id="activity-loop-form" method="post">

		<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); ?>

	</form>

<?php endif; ?>