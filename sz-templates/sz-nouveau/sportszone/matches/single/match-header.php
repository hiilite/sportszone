<?php
/**
 * SportsZone - Events Header
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<?php sz_get_template_part( 'events/single/parts/header-item-actions' ); ?>


<div id="item-header-content">

	<p class="highlight event-status"><strong><?php echo esc_html( sz_nouveau_event_meta()->status ); ?></strong></p>

	<p class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_event_last_active( 0, array( 'relative' => false ) ) ); ?>">
		<?php
		echo esc_html(
			sprintf(
				/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
				__( 'active %s', 'sportszone' ),
				sz_get_event_last_active()
			)
		);
		?>
	</p>

	<?php sz_nouveau_event_hook( 'before', 'header_meta' ); ?>

	<?php if ( sz_nouveau_event_has_meta_extra() ) : ?>
		<div class="item-meta">

			<?php echo sz_nouveau_event_meta()->extra; ?>

		</div><!-- .item-meta -->
	<?php endif; ?>


		<?php if ( ! sz_nouveau_events_front_page_description() ) { ?>
			<?php if ( sz_nouveau_event_meta()->description ) { ?>
				<div class="event-description">
					<?php echo sz_nouveau_event_meta()->description; ?>
				</div><!-- //.event_description -->
			<?php	} ?>
		<?php } ?>

</div><!-- #item-header-content -->

<?php sz_nouveau_event_header_buttons(); ?>
