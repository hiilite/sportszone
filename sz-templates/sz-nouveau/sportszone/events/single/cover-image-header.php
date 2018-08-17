<?php
/**
 * SportsZone - Events Cover Image Header.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<div id="cover-image-container">
	<div id="header-cover-image">
		<?php sz_event_cover_image(); ?>
	</div>

	<div id="item-header-cover-image">

<?php	if ( ! sz_nouveau_events_front_page_description() ) : ?>
		<div id="item-header-content">

			<p class="highlight event-status"><strong><?php echo esc_html( sz_nouveau_event_meta()->status ); ?></strong></p>
			<p class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_event_last_active( 0, array( 'relative' => false ) ) ); ?>">
				<?php
				/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
				printf( __( 'active %s', 'sportszone' ), sz_get_event_last_active() );
				?>
			</p>

			<?php echo sz_nouveau_event_meta()->event_type_list; ?>
			<?php sz_nouveau_event_hook( 'before', 'header_meta' ); ?>

			<?php if ( sz_nouveau_event_has_meta_extra() ) : ?>
				<div class="item-meta">

					<?php echo sz_nouveau_event_meta()->extra; ?>

				</div><!-- .item-meta -->
			<?php endif; ?>

			<?php sz_nouveau_event_header_buttons(); ?>

		</div><!-- #item-header-content -->
<?php endif; ?>

		<?php sz_get_template_part( 'events/single/parts/header-item-actions' ); ?>

	</div><!-- #item-header-cover-image -->


</div><!-- #cover-image-container -->

<?php if ( ! sz_nouveau_events_front_page_description() ) : ?>
	<?php if ( ! empty( sz_nouveau_event_meta()->description ) ) : ?>
		<div class="desc-wrap">
			<div class="event-description">
			<?php echo esc_html( sz_nouveau_event_meta()->description ); ?>
		</div><!-- //.event_description -->
	</div>
	<?php endif; ?>
<?php endif; ?>
