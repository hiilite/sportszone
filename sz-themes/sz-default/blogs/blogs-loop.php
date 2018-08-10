<?php

/**
 * SportsZone - Blogs Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - sz_dtheme_object_filter()
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<?php do_action( 'sz_before_blogs_loop' ); ?>

<?php if ( sz_has_blogs( sz_ajax_querystring( 'blogs' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="blog-dir-count-top">
			<?php sz_blogs_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="blog-dir-pag-top">
			<?php sz_blogs_pagination_links(); ?>
		</div>

	</div>

	<?php do_action( 'sz_before_directory_blogs_list' ); ?>

	<ul id="blogs-list" class="item-list" role="main">

	<?php while ( sz_blogs() ) : sz_the_blog(); ?>

		<li>
			<div class="item-avatar">
				<a href="<?php sz_blog_permalink(); ?>"><?php sz_blog_avatar( 'type=thumb' ); ?></a>
			</div>

			<div class="item">
				<div class="item-title"><a href="<?php sz_blog_permalink(); ?>"><?php sz_blog_name(); ?></a></div>
				<div class="item-meta"><span class="activity"><?php sz_blog_last_active(); ?></span></div>

				<?php do_action( 'sz_directory_blogs_item' ); ?>
			</div>

			<div class="action">

				<?php do_action( 'sz_directory_blogs_actions' ); ?>

				<div class="meta">

					<?php sz_blog_latest_post(); ?>

				</div>

			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php do_action( 'sz_after_directory_blogs_list' ); ?>

	<?php sz_blog_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="blog-dir-count-bottom">

			<?php sz_blogs_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="blog-dir-pag-bottom">

			<?php sz_blogs_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, there were no sites found.', 'sportszone' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'sz_after_blogs_loop' ); ?>
