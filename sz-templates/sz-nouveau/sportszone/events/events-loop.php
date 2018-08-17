<?php
/**
 * SportsZone - Events Loop
 *
 * @since 3.0.0
 * @version 3.1.0
 */

sz_nouveau_before_loop(); ?>

<?php if ( sz_get_current_event_directory_type() ) : ?>
	<p class="current-event-type"><?php sz_current_event_directory_type_message(); ?></p>
<?php endif; ?>

<?php if ( sz_has_events( sz_ajax_querystring( 'events' ) ) ) : ?>

	<?php sz_nouveau_pagination( 'top' ); ?>

	<ul id="events-list" class="<?php sz_nouveau_loop_classes(); ?>">

	<?php
	while ( sz_events() ) :
		sz_the_event();
	?>

		<li <?php sz_event_class( array( 'item-entry' ) ); ?> data-sz-item-id="<?php sz_event_id(); ?>" data-sz-item-component="events">
			<div class="list-wrap">

				

				<div class="item">

					<div class="item-block">

						<h2 class="list-title events-title"><?php sz_event_link(); ?></h2>

						<?php if ( sz_nouveau_event_has_meta() ) : ?>

							<p class="item-meta event-details"><?php sz_nouveau_event_meta(); ?></p>

						<?php endif; ?>

						<p class="last-activity item-meta">
							<?php
							printf(
								/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
								__( 'active %s', 'sportszone' ),
								sz_get_event_last_active()
							);
							?>
						</p>

					</div>

					<div class="event-desc"><p><?php sz_nouveau_event_description_excerpt(); ?></p></div>

					<?php sz_nouveau_events_loop_item(); ?>

					<?php sz_nouveau_events_loop_buttons(); ?>

				</div>


			</div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php sz_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	<?php sz_nouveau_user_feedback( 'events-loop-none' ); ?>

<?php endif; ?>

<?php
sz_nouveau_after_loop();
