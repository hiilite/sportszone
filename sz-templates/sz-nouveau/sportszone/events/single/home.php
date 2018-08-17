<?php
/**
 * SportsZone - Events Home
 *
 * @since 3.0.0
 * @version 3.0.0
 */

if ( sz_has_events() ) :
	while ( sz_events() ) :
		sz_the_event();
	?>

		<?php sz_nouveau_event_hook( 'before', 'home_content' ); ?>

		<div id="item-header" role="complementary" data-sz-item-id="<?php sz_event_id(); ?>" data-sz-item-component="events" class="events-header single-headers">

			<?php sz_nouveau_event_header_template_part(); ?>

		</div><!-- #item-header -->
 
		<div class="sz-wrap">
			<?php 
				/*$event_id = sz_get_event_id();
				
				$event_type = (array) sz_events_get_event_type( $event_id, false );
				var_dump($event_type);
				var_dump( sz_events_get_event_type( sz_get_event_id(), true ) );
				var_dump( sz_get_current_event_directory_type() );*/
				 ?>

				<?php sz_get_template_part( 'events/single/parts/item-nav' ); ?>

			<div id="item-body" class="item-body">

				<?php sz_nouveau_event_template_part(); ?>

			</div><!-- #item-body -->

		</div><!-- // .sz-wrap -->

		<?php sz_nouveau_event_hook( 'after', 'home_content' ); ?>

	<?php endwhile; ?>

<?php
endif;
