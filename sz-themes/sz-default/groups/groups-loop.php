<?php

/**
 * SportsZone - Groups Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - sz_dtheme_object_filter()
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<?php do_action( 'sz_before_groups_loop' ); ?>

<?php if ( sz_has_groups( sz_ajax_querystring( 'groups' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="group-dir-count-top">

			<?php sz_groups_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="group-dir-pag-top">

			<?php sz_groups_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'sz_before_directory_groups_list' ); ?>

	<ul id="groups-list" class="item-list" role="main">

	<?php while ( sz_groups() ) : sz_the_group(); ?>

		<li <?php sz_group_class(); ?>>
			<div class="item-avatar">
				<a href="<?php sz_group_permalink(); ?>"><?php sz_group_avatar( 'type=thumb&width=50&height=50' ); ?></a>
			</div>

			<div class="item">
				<div class="item-title"><a href="<?php sz_group_permalink(); ?>"><?php sz_group_name(); ?></a></div>
				<div class="item-meta"><span class="activity"><?php printf( __( 'active %s', 'sportszone' ), sz_get_group_last_active() ); ?></span></div>

				<div class="item-desc"><?php sz_group_description_excerpt(); ?></div>

				<?php do_action( 'sz_directory_groups_item' ); ?>

			</div>

			<div class="action">

				<?php do_action( 'sz_directory_groups_actions' ); ?>

				<div class="meta">

					<?php sz_group_type(); ?> / <?php sz_group_member_count(); ?>

				</div>

			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php do_action( 'sz_after_directory_groups_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="group-dir-count-bottom">

			<?php sz_groups_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="group-dir-pag-bottom">

			<?php sz_groups_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no groups found.', 'sportszone' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'sz_after_groups_loop' ); ?>
