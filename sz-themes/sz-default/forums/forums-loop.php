<?php

/**
 * SportsZone - Forums Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - sz_dtheme_object_filter()
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<?php do_action( 'sz_before_forums_loop' ); ?>

<?php if ( sz_has_forum_topics( sz_ajax_querystring( 'forums' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="topic-count-top">

			<?php sz_forum_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="topic-pag-top">

			<?php sz_forum_pagination(); ?>

		</div>

	</div>

	<?php do_action( 'sz_before_directory_forums_list' ); ?>

	<table class="forum">
		<thead>
			<tr>
				<th id="th-title"><?php _e( 'Topic', 'sportszone' ); ?></th>
				<th id="th-postcount"><?php _e( 'Posts', 'sportszone' ); ?></th>
				<th id="th-freshness"><?php _e( 'Freshness', 'sportszone' ); ?></th>

				<?php do_action( 'sz_directory_forums_extra_cell_head' ); ?>

			</tr>
		</thead>

		<tbody>

			<?php while ( sz_forum_topics() ) : sz_the_forum_topic(); ?>

			<tr class="<?php sz_the_topic_css_class(); ?>">
				<td class="td-title">
					<a class="topic-title" href="<?php sz_the_topic_permalink(); ?>" title="<?php esc_attr_e( 'Permanent link to this post', 'sportszone' ); ?>">

						<?php sz_the_topic_title(); ?>

					</a>

					<p class="topic-meta">
						<span class="topic-by"><?php /* translators: "started by [poster] in [forum]" */ printf( __( 'Started by %1$s', 'sportszone' ), sz_get_the_topic_poster_avatar( 'height=20&width=20') . sz_get_the_topic_poster_name() ); ?></span>

						<?php if ( !sz_is_group_forum() ) : ?>

							<span class="topic-in">

								<?php
									$topic_in = '<a href="' . sz_get_the_topic_object_permalink() . '">' . sz_get_the_topic_object_avatar( 'type=thumb&width=20&height=20' ) . '</a>' .
													'<a href="' . sz_get_the_topic_object_permalink() . '" title="' . sz_get_the_topic_object_name() . '">' . sz_get_the_topic_object_name() .'</a>';

									/* translators: "started by [poster] in [forum]" */
									printf( __( 'in %1$s', 'sportszone' ), $topic_in );
								?>

							</span>

						<?php endif; ?>

					</p>
				</td>
				<td class="td-postcount">
					<?php sz_the_topic_total_posts(); ?>
				</td>
				<td class="td-freshness">
					<span class="time-since"><?php sz_the_topic_time_since_last_post(); ?></span>
					<p class="topic-meta">
						<span class="freshness-author">
							<a href="<?php sz_the_topic_permalink(); ?>"><?php sz_the_topic_last_poster_avatar( 'type=thumb&width=20&height=20' ); ?></a>
							<?php sz_the_topic_last_poster_name(); ?>
						</span>
					</p>
				</td>

				<?php do_action( 'sz_directory_forums_extra_cell' ); ?>

			</tr>

			<?php do_action( 'sz_directory_forums_extra_row' ); ?>

			<?php endwhile; ?>

		</tbody>
	</table>

	<?php do_action( 'sz_after_directory_forums_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="topic-count-bottom">
			<?php sz_forum_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="topic-pag-bottom">
			<?php sz_forum_pagination(); ?>
		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, there were no forum topics found.', 'sportszone' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'sz_after_forums_loop' ); ?>
