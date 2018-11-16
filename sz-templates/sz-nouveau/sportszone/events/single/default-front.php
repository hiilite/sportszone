<?php
/**
 * BP Nouveau Default event's front template.
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>
<?php
function sz_get_event_info($id) {
	if(isset($id)) {
		
		$data = array();

		/* Get Matches */
		$matches_args = array(
			'post_type'	=> 'sz_match',
			'posts_per_page'	=> -1,
			'meta_query'	=> array(
				array(
					'key'	=> 'sz_event',
					'value'	=> $id
				)
			)
		);
		
		$matches = new WP_Query($matches_args);	
		
		$matche_list = $matches->posts;
		
		/*
		/* Get dates
		*/
		$matche_dates = array();
		foreach($matche_list as $match) {
			$match_meta = get_post_meta($match->ID);
			$matche_dates[] = $match_meta['sz_day'][0];
		}
		
		/* Put dates in order */
		function date_sort($a, $b) {
		    return strtotime($a) - strtotime($b);
		}
		usort($matche_dates, "date_sort");
		
		/* If first and last date are different - set date as range */
		if($matche_dates[0] != end($matche_dates)) {
			$data['date'] = date("F j", strtotime($matche_dates[0])).' - '.date("F j", strtotime(end($matche_dates)));
		} else {
			$data['date'] = date("F j", strtotime($matche_dates[0]));
		}
		
		/*
		/* Get Venues 
		*/
		$match_venues = array();
		if($matches->have_posts()):
			while($matches->have_posts()):
				$matches->the_post();
				$match_id = get_the_id();
				$match_venues[] = get_post_meta($match_id, 'sz_venue'); 
			endwhile;
		endif;
		
		$data['venue'] = $match_venues[0][0];
		$i = 0;
		if(count($match_venues) > 1) {
			foreach($match_venues as $venue) {
				$i++;
				if($match_venues[$i] != $match_venues[0]) {
					$data['venue'] = 'Various Venues';
					break;	
				}	
			}
		}
		
		/*
		/* Get Sponsors 
		*/
		$match_sponsors = array();
		if($matches->have_posts()):
			while($matches->have_posts()):
				$matches->the_post();
				$match_id = get_the_id();
				$sponsors = events_get_eventmeta( $id, 'sz_matches_group' );
				
				foreach($sponsors as $sponsor) {
					$match_sponsors[] = $sponsor['match_sponsor'];
				}
			endwhile;
		endif;
		
		$data['sponsors'] = array_unique($match_sponsors);

		return $data;
	}
}
?>

<div class="event-front-page">

	<?php if ( ! is_active_sidebar( 'sidebar-sportszone-events' ) || ! sz_nouveau_events_do_event_boxes() ) : ?>
		<?php if ( ! is_customize_preview() && sz_current_user_can( 'sz_moderate' ) ) : ?>

			<div class="sz-feedback custom-homepage-info info no-icon">
				<strong><?php esc_html_e( 'Manage the Events default front page', 'sportszone' ); ?></strong>

				<p>
				<?php
				printf(
					esc_html__( 'You can set your preferences for the %1$s or add %2$s to it.', 'sportszone' ),
					sz_nouveau_events_get_customizer_option_link(),
					sz_nouveau_events_get_customizer_widgets_link()
				);
				?>
				</p>

			</div>

		<?php endif; ?>
	<?php endif; ?>

<?php
	$data = sz_get_event_info(sz_get_event_id());
?>

	<div class="sz-info-box">
		<h3 class="screen-heading"><?php echo __('About','sportszone'); ?></h3>
		
		<div>
			<h4 class="label"><?php echo __('City:','sportszone'); ?></h4>
			<?php echo events_get_eventmeta(sz_get_event_id(), 'sz_event_city'); ?>
		</div>
		<div>
			<h4 class="label"><?php echo __('Venue:','sportszone'); ?></h4>
			<?php echo '</pre>'.print_r($data['venue'],true).'</pre>'; ?>
		</div>
		<div>
			<h4 class="label"><?php echo __('Date:','sportszone'); ?></h4>
			<?php echo '</pre>'.print_r($data['date'],true).'</pre>'; ?>
		</div>
		
		<hr>
		
		<h4 class="label"><?php echo __('Event Description:','sportszone'); ?></h4>
	<?php if ( sz_nouveau_events_front_page_description() ) : ?>
		<div class="event-description">

			<?php sz_event_description(); ?>

		</div><!-- .event-description -->
	<?php endif; ?>
	
	</div>

	<?php if ( sz_nouveau_events_do_event_boxes() ) : ?>
		<div class="sz-plugin-widgets">

			<?php sz_custom_event_boxes(); ?>

		</div><!-- .sz-plugin-widgets -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-sportszone-events' ) ) : ?>
		<div id="event-front-widgets" class="sz-sidebar sz-widget-area" role="complementary">

			<?php dynamic_sidebar( 'sidebar-sportszone-events' ); ?>

		</div><!-- .sz-sidebar.sz-widget-area -->
	<?php endif; ?>

</div>

<?php
if(count($data['sponsors']) > 0): 
?>
	<h3><?php echo __('Sponsors','sportszone'); ?></h3>
	<div class="card-deck">
		<?php 
		$s = 1;
		foreach($data['sponsors'] as $sponsor_id){
			$sponsor = groups_get_group( array('group_id' => $sponsor_id) );
			$avatar = sz_core_fetch_avatar(array( 'item_id' => $sponsor_id, 'object'=>'group','type'=>'full', 'class'=>'card-img-top'));
			$group_url = sz_get_group_permalink($sponsor);

			echo "<div class='card'>
				<div class='card-body'><a href='$group_url'>$avatar</a></div>
			</div>";	
			$s++;	
			if($s > 6)
		    {
		         break; 
		    }
		}

		$username = sz_core_get_username(sz_displayed_user_id());
		echo '<div class="view-more"><a href="/members/'.$username.'/groups/" class="vert-btn">View More</a></div>';
		?>
	</div>

<?php 
endif;	
?>
