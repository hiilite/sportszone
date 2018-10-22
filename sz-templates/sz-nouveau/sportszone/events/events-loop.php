<?php
/**
 * SportsZone - Events Loop
 *
 * @since 3.0.0
 * @version 3.1.0
 */
function sz_get_event_brief_data($id, $type) {
	if(isset($id) && isset($type)) {
		
		$data = array();

		/* If tour, tournament or season */
		if($type == 'tour' || $type == 'tournament' || $type == 'season') {

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

			/* If more than 1 match */
			if(count($matche_list) > 1):

				/* Get dates */
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
				
				/* Get venues and teams */
				$match_venues = array();
				$teams = array();
				if($matches->have_posts()):
					while($matches->have_posts()):
						$matches->the_post();
						$match_id = get_the_id();
						
						$match_venues[] = get_post_meta($match_id, 'sz_venue'); 
						
						if($type == 'tour' || $type == 'tournament') {
							$team_ids = get_post_meta($match_id, 'sz_team');
							if($team_ids[0][0] != '') {
								$teams[] = $team_ids[0][0];
							}
							if($team_ids[0][1] != '') {
								$teams[] = $team_ids[0][1];
							}
						}
					endwhile;
				endif;
				
				/* Compare venues - if more than one, set to Various Vanues */
				$data['venue'] = $match_venues[0];
				$i = 0;
				foreach($match_venues as $venue) {
					$i++;
					if($match_venues[$i] != $match_venues[0]) {
						$data['venue'] = 'Various Venues';
						break;	
					}	
				}
				
				if($type == 'tour' || $type == 'tournament') {
					/* Remove duplicate teams */
					$data['teams'] = array_unique($teams);	
				} else {
					/* Get main team */
					$data['teams'] = json_decode(events_get_eventmeta($id, 'event-main-team'));	
				}

			else:
				/* Get date */
				if(isset($matche_list[0]->sz_day) && $matche_list[0]->sz_day != '') {
					$data['date'] = date("F j", strtotime($matche_list[0]->sz_day));
				}
				
				/* Get venue and teams */
				if($matches->have_posts()):
					while($matches->have_posts()):
						$matches->the_post();
						$match_id = get_the_id();
						
						$venue = get_post_meta($match_id, 'sz_venue');
						$data['venue'] = $venue[0]; 
					
						if($type == 'tour' || $type == 'tournament') {
							$data['teams'] = get_post_meta($match_id, 'sz_team');
						}
					endwhile;
				endif;
				
				if($type == 'season') {
					$data['teams'] = events_get_eventmeta($id, 'event-main-team');	
				}
			endif;
		}
		else {
			/* IF we add other event types */
			
			/* Get Venue */
			
			/* Get Date */
		}
		
		/* Set fallback values */
		if(!isset($data['date']) || $data['date'] == '') {
			$data['date'] = 'TBD';	
		}
		if(!isset($data['venue']) || $data['venue'] == '') {
			$data['venue'] = 'TBD';	
		}
		if(!isset($data['teams']) || $data['teams'] == '') {
			$data['teams'] = 'TBD';	
		}

		return $data;
	}
}


sz_nouveau_before_loop(); ?>
<!--sportszone > sz-templates > sz-nouveau > sportszone > events > events-loop-->
<?php if ( sz_get_current_event_directory_type() ) : ?>
	<p class="current-event-type"><?php sz_current_event_directory_type_message(); ?></p>
<?php endif; ?>

<?php if ( sz_has_events( sz_ajax_querystring( 'events' ) ) ) : ?>

	<?php //sz_nouveau_pagination( 'top' ); ?>

	<ul id="events-list" class="<?php sz_nouveau_loop_classes(); ?>">

	<?php
	while ( sz_events() ) :
	
		sz_the_event();

		if ( $event_types = sz_events_get_event_type( sz_get_event_id(), false ) ) {
			$event_type = $event_types[0];
		}	
		
		$event_data = sz_get_event_brief_data(sz_get_event_id(),$event_type);
		//echo '<pre>'.print_r($event_data,true).'</pre>';
	?>

		<li <?php sz_event_class( array( 'item-entry' ) ); ?> data-sz-item-id="<?php sz_event_id(); ?>" data-sz-item-component="events">
			<div class="list-wrap">

				<div class="item">

					<div class="item-block">
						<div class="event-breif-featured-img" style="background-image:url('/wp-content/uploads/2018/10/The-Rugby-Zone-Events.jpg');">
							<?php
							/*if(sz_get_event_has_cover_image()) {
								sz_get_event_cover_image();
							}*/
							?>
						</div>
						
						<div class="event-brief-info">
							<h3 class="list-title events-title"><?php sz_event_link(); ?></h3>
							<span class="event-type"><?php echo $event_type; ?></span>

							<?php sz_nouveau_events_loop_buttons(); ?>
						</div>
						<?php 
						$city = events_get_eventmeta(sz_get_event_id(), 'sz_event_city');
						$province = events_get_eventmeta(sz_get_event_id(), 'sz_event_province');
						$country = events_get_eventmeta(sz_get_event_id(), 'sz_event_country');
						$location = '';
						$location .= ($city) ? $city : '';
						$location .= ($city && $province['province']) ? ', '.$province['province'] : $province['province'];
						$location .= ($city && $province['province'] && $country['country']) ? ', '.$country['country'] : $country['country'];
						

						?>
						<div class="event-brief-venue">
							<h3 class="list-title events-venue"><?php echo $event_data['venue']; ?></h3>
							<span class="event-region"><?php echo $location; ?></span>
							<span class="event-dates"><?php echo $event_data['date']; ?></span>
						</div>
						
						<div class="event-brief-teams">			
							<?php 

							if($event_type == 'tour' || $event_type == 'tournament') {
								if(count($event_data['teams']) > 3) {
									$teams = array_rand($event_data['teams'], 4);
								} else {
									$teams = $event_data['teams'];
								}
		
								foreach($teams as $team) {
									$group = groups_get_group( array( 'group_id' => $team ) );
									$slug = $group->slug;
									echo '<a href="'.site_url().'/groups/'.$slug.'/" class="mini-avatar">'.sz_core_fetch_avatar ( array( "item_id" => $team, "object" => "group", "type" => "full" ) ).'</a>';
								}
								
								if(count($event_data['teams']) > 3) {
									echo '<a href="'.sz_get_event_slug().'/matches/" alt="View Matches" class="mini-ellipsis">...</a>';
								}
							}
							elseif($event_type == 'season') {
								$group = groups_get_group( array( 'group_id' => $event_data['teams']['team'] ) );
								$slug = $group->slug;
								echo '<a href="'.site_url().'/groups/'.$slug.'/" class="med-avatar">'.sz_core_fetch_avatar ( array( "item_id" => $event_data['teams']['team'], "object" => "group", "type" => "full" ) ).'</a>';
							}
							?>
						</div>
					</div>

					<?php //sz_nouveau_events_loop_item(); ?>

				</div>


			</div>
		</li>

	<?php
		
		endwhile; ?>

	</ul>

	<?php sz_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	<?php sz_nouveau_user_feedback( 'events-loop-none' ); ?>

<?php endif; ?>

<?php
sz_nouveau_after_loop();
