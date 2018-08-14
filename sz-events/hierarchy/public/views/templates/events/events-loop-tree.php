<?php
/**
 * SportsZone - Groups Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - sz_legacy_theme_object_filter().
 *
 * @package SportsZone
 * @subpackage sz-legacy
 */

/**
 * Fires before the display of groups from the groups loop.
 *
 * @since 1.2.0 (SportsZone)
 */
do_action( 'sz_before_groups_loop' ); ?>

<?php if ( sz_get_current_group_directory_type() ) : ?>
	<p class="current-group-type"><?php sz_current_group_directory_type_message() ?></p>
<?php endif; ?>

<?php
	// Fire an action outside of the has groups loop, but after the directory type message.
	do_action( 'hgsz_before_groups_loop' );
?>

<?php if ( sz_has_groups( sz_ajax_querystring( 'groups' ) ) ) : ?>

	<?php

	/**
	 * Fires before the listing of the groups tree.
	 * Specific to the Hierarchical Groups for SZ plugin.
	 *
	 * @since 1.0.0
	 */
	do_action( 'hgsz_before_directory_groups_list_tree' ); ?>

	<?php

	/**
	 * Fires before the listing of the groups list.
	 *
	 * @since 1.1.0 (SportsZone)
	 */
	do_action( 'sz_before_directory_groups_list' ); ?>

	<ul id="groups-list" class="item-list" aria-live="assertive" aria-atomic="true" aria-relevant="all">

	<?php while ( sz_groups() ) : sz_the_group(); ?>

		<li <?php sz_group_class(); ?>>
			<?php if ( ! sz_disable_group_avatar_uploads() ) : ?>
				<div class="item-avatar">
					<a href="<?php sz_group_permalink(); ?>"><?php sz_group_avatar( 'type=thumb&width=50&height=50' ); ?></a>
				</div>
			<?php endif; ?>

			<div class="item">
				<div class="item-title"><a href="<?php sz_group_permalink(); ?>"><?php sz_group_name(); ?></a></div>
				<div class="item-meta"><span class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_group_last_active( 0, array( 'relative' => false ) ) ); ?>"><?php printf( __( 'active %s', 'sportszone' ), sz_get_group_last_active() ); ?></span></div>

				<div class="item-desc"><?php sz_group_description_excerpt(); ?></div>

				<?php

				/**
				 * Fires inside the listing of an individual group listing item.
				 *
				 * @since 1.1.0 (SportsZone)
				 */
				do_action( 'sz_directory_groups_item' ); ?>

			</div>

			<div class="action">

				<?php

				/**
				 * Fires inside the action section of an individual group listing item.
				 *
				 * @since 1.1.0 (SportsZone)
				 */
				do_action( 'sz_directory_groups_actions' ); ?>

				<div class="meta">

					<?php sz_group_type(); ?> / <?php sz_group_member_count(); ?>

				</div>

			</div>

			<?php hgsz_child_group_section(); ?>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php

	/**
	 * Fires after the listing of the groups list.
	 *
	 * @since 1.1.0 (SportsZone)
	 */
	do_action( 'sz_after_directory_groups_list' ); ?>

	<?php

	/**
	 * Fires before the listing of the groups tree.
	 * Specific to the Hierarchical Groups for SZ plugin.
	 *
	 * @since 1.0.0
	 */
	do_action( 'hgsz_after_directory_groups_list_tree' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no groups found.', 'sportszone' ); ?></p>
	</div>

<?php endif; ?>

<?php

/**
 * Fires after the display of groups from the groups loop.
 *
 * @since 1.2.0 (SportsZone)
 */
do_action( 'sz_after_groups_loop' ); ?>
