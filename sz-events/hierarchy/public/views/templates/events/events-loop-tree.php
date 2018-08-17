<?php
/**
 * SportsZone - Events Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - sz_legacy_theme_object_filter().
 *
 * @package SportsZone
 * @subpackage sz-legacy
 */

/**
 * Fires before the display of events from the events loop.
 *
 * @since 1.2.0 (SportsZone)
 */
do_action( 'sz_before_events_loop' ); ?>

<?php if ( sz_get_current_event_directory_type() ) : ?>
	<p class="current-event-type"><?php sz_current_event_directory_type_message() ?></p>
<?php endif; ?>

<?php
	// Fire an action outside of the has events loop, but after the directory type message.
	do_action( 'hgsz_before_events_loop' );
?>

<?php if ( sz_has_events( sz_ajax_querystring( 'events' ) ) ) : ?>

	<?php

	/**
	 * Fires before the listing of the events tree.
	 * Specific to the Hierarchical Events for SZ plugin.
	 *
	 * @since 1.0.0
	 */
	do_action( 'hgsz_before_directory_events_list_tree' ); ?>

	<?php

	/**
	 * Fires before the listing of the events list.
	 *
	 * @since 1.1.0 (SportsZone)
	 */
	do_action( 'sz_before_directory_events_list' ); ?>

	<ul id="events-list" class="item-list" aria-live="assertive" aria-atomic="true" aria-relevant="all">

	<?php while ( sz_events() ) : sz_the_event(); ?>

		<li <?php sz_event_class(); ?>>

			<div class="item">
				<div class="item-title"><a href="<?php sz_event_permalink(); ?>"><?php sz_event_name(); ?></a></div>
				<div class="item-meta"><span class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_event_last_active( 0, array( 'relative' => false ) ) ); ?>"><?php printf( __( 'active %s', 'sportszone' ), sz_get_event_last_active() ); ?></span></div>

				<div class="item-desc"><?php sz_event_description_excerpt(); ?></div>

				<?php

				/**
				 * Fires inside the listing of an individual event listing item.
				 *
				 * @since 1.1.0 (SportsZone)
				 */
				do_action( 'sz_directory_events_item' ); ?>

			</div>

			<div class="action">

				<?php

				/**
				 * Fires inside the action section of an individual event listing item.
				 *
				 * @since 1.1.0 (SportsZone)
				 */
				do_action( 'sz_directory_events_actions' ); ?>

				<div class="meta">

					<?php sz_event_type(); ?> / <?php sz_event_member_count(); ?>

				</div>

			</div>

			<?php hgsz_child_event_section(); ?>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php

	/**
	 * Fires after the listing of the events list.
	 *
	 * @since 1.1.0 (SportsZone)
	 */
	do_action( 'sz_after_directory_events_list' ); ?>

	<?php

	/**
	 * Fires before the listing of the events tree.
	 * Specific to the Hierarchical Events for SZ plugin.
	 *
	 * @since 1.0.0
	 */
	do_action( 'hgsz_after_directory_events_list_tree' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no events found.', 'sportszone' ); ?></p>
	</div>

<?php endif; ?>

<?php

/**
 * Fires after the display of events from the events loop.
 *
 * @since 1.2.0 (SportsZone)
 */
do_action( 'sz_after_events_loop' ); ?>
