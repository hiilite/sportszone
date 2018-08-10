<?php
/**
 * SportsZone - Blogs Loop
 *
 * @since 3.0.0
 * @version 3.0.0
 */

sz_nouveau_before_loop(); ?>

<?php if ( sz_has_blogs( sz_ajax_querystring( 'blogs' ) ) ) : ?>

	<?php sz_nouveau_pagination( 'top' ); ?>

	<ul id="blogs-list" class="<?php sz_nouveau_loop_classes(); ?>">

	<?php
	while ( sz_blogs() ) :
		sz_the_blog();
	?>

		<li <?php sz_blog_class( array( 'item-entry' ) ); ?>>
			<div class="list-wrap">

				<div class="item-avatar">
					<a href="<?php sz_blog_permalink(); ?>"><?php sz_blog_avatar( sz_nouveau_avatar_args() ); ?></a>
				</div>

				<div class="item">

					<div class="item-block">

						<h2 class="list-title blogs-title"><a href="<?php sz_blog_permalink(); ?>"><?php sz_blog_name(); ?></a></h2>

						<p class="last-activity item-meta"><?php sz_blog_last_active(); ?></p>

						<?php if ( sz_nouveau_blog_has_latest_post() ) : ?>
							<p class="meta last-post">

								<?php sz_blog_latest_post(); ?>

							</p>
						<?php endif; ?>

						<?php sz_nouveau_blogs_loop_buttons( array( 'container' => 'ul' ) ); ?>

					</div>

					<?php sz_nouveau_blogs_loop_item(); ?>

				</div>



			</div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php sz_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	sz_nouveau_user_feedback( 'blogs-loop-none' );

<?php endif; ?>

<?php
sz_nouveau_after_loop();
