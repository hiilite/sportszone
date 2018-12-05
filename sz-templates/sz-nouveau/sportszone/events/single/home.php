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
		
		if ( $event_types = sz_events_get_event_type( sz_get_event_id(), false ) ) {
			$event_type = $event_types[0];
		}
	
		$bg = '/wp-content/uploads/2018/11/Rugby-Zone-Extra.jpg';
							
		if(isset($event_type)) {
			if($event_type == 'tour') {
				$bg = '/wp-content/uploads/2018/11/Rugby-Zone-Events-Tour.jpg';
			}
			elseif($event_type == 'tournament') {
				$bg = '/wp-content/uploads/2018/11/Rugby-Zone-Events-Tournament.jpg';
			}
			elseif($event_type == 'season') {
				$bg = '/wp-content/uploads/2018/11/Rugby-Zone-Events-Season.jpg';
			}
		}
	?>

		<?php sz_nouveau_event_hook( 'before', 'home_content' ); ?>

		<div id="item-header" role="complementary" data-sz-item-id="<?php sz_event_id(); ?>" data-sz-item-component="events" class="events-header single-headers" style="background-image: url(<?php echo $bg; ?>);">
			
			<h2 class="event-title"><?php the_title(); ?></h2>
			<?php sz_nouveau_event_header_template_part(); ?>

		</div><!-- #item-header -->
 
		<div class="sz-wrap">
			<?php 
				
				sz_get_template_part( 'events/single/parts/item-nav' ); ?>

			<div id="item-body" class="item-body event-single-home">

				<?php sz_nouveau_event_template_part(); ?>

			</div><!-- #item-body -->

		</div><!-- // .sz-wrap -->

		<?php sz_nouveau_event_hook( 'after', 'home_content' ); ?>

	<?php endwhile; ?>

<?php
endif;
