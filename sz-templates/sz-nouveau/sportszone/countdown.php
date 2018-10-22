<?php
/**
 * Countdown
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version   2.5.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$defaults = array(
	'team' => null,
	'calendar' => null,
	'league' => null,
	'season' => null,
	'id' => null,
	'title' => null,
	'live' => get_option( 'sportszone_enable_live_countdowns', 'yes' ) == 'yes' ? true : false,
	'link_events' => get_option( 'sportszone_link_events', 'yes' ) == 'yes' ? true : false,
	'link_teams' => get_option( 'sportszone_link_teams', 'no' ) == 'yes' ? true : false,
	'link_venues' => get_option( 'sportszone_link_venues', 'no' ) == 'yes' ? true : false,
	'show_logos' => get_option( 'sportszone_countdown_show_logos', 'no' ) == 'yes' ? true : false,
);
if ( isset( $id ) ):
	$post = get_post( $id );
elseif ( $calendar ):
	$calendar = new SP_Calendar( $calendar );
	if ( $team )
		$calendar->team = $team;
	$calendar->status = 'future';
	$calendar->number = 1;
	$calendar->order = 'ASC';
	$data = $calendar->data();
	$post = array_shift( $data );
else:
	$args = array();
	if ( isset( $team ) ) {
		$args['meta_query'] = array(
			array(
				'key' => 'sz_team',
				'value' => $team,
			)
		);
	}
	if ( isset( $league ) || isset( $season ) ) {
		$args['tax_query'] = array( 'relation' => 'AND' );
		
		if ( isset( $league ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'sz_league',
				'terms' => $league,
			);
		}
		
		if ( isset( $season ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'sz_season',
				'terms' => $season,
			);
		}
	}
	$post = sz_get_next_event( $args );
endif;

extract( $defaults, EXTR_SKIP );

if ( ! isset( $post ) || ! $post ) return;

if ( $title )
	echo '<h4 class="sp-table-caption">' . $title . '</h4>';

$title = $post->post_title;
if ( $link_events ) $title = '<a href="' . get_post_permalink( $post->ID, false, true ) . '">' . $title . '</a>';
?>
<div class="sp-template sp-template-countdown">
	<div class="sp-countdown-wrapper">
		<h3 class="event-name sp-event-name">
			<?php
			if ( $show_logos ) {
				$teams = array_unique( (array) get_post_meta( $post->ID, 'sz_team' ) );
				$i = 0;

				if ( is_array( $teams ) ) {
					foreach ( $teams as $team ) {
						$i++;
						if ( has_post_thumbnail ( $team ) ) {
							if ( $link_teams ) {
								echo '<a class="team-logo logo-' . ( $i % 2 ? 'odd' : 'even' ) . '" href="' . get_post_permalink( $team ) . '" title="' . get_the_title( $team ) . '">' . get_the_post_thumbnail( $team, 'sportszone-fit-icon' ) . '</a>';
							} else {
								echo get_the_post_thumbnail( $team, 'sportszone-fit-icon', array( 'class' => 'team-logo logo-' . ( $i % 2 ? 'odd' : 'even' ) ) );
							}
						}
					}
				}
			}
			?>
			<?php echo $title; ?>
		</h3>
		<?php
		if ( isset( $show_venue ) && $show_venue ):
			$venues = get_the_terms( $post->ID, 'sz_venue' );
			if ( $venues ):
				?>
				<h5 class="event-venue sp-event-venue">
					<?php
					if ( $link_venues ) {
						the_terms( $post->ID, 'sz_venue' );
					} else {
						$venue_names = array();
						foreach ( $venues as $venue ) {
							$venue_names[] = $venue->name;
						}
						echo implode( '/', $venue_names );
					}
					?>
				</h5>
				<?php
			endif;
		endif;

		if ( isset( $show_league ) && $show_league ):
			$leagues = get_the_terms( $post->ID, 'sz_league' );
			if ( $leagues ):
				foreach( $leagues as $league ):
					$term = get_term( $league->term_id, 'sz_league' );
					?>
					<h5 class="event-league sp-event-league"><?php echo $term->name; ?></h5>
					<?php
				endforeach;
			endif;
		endif;

		$now = new DateTime( current_time( 'mysql', 0 ) );
		$date = new DateTime( $post->post_date );
		$interval = date_diff( $now, $date );

		$days = $interval->invert ? 0 : $interval->days;
		$h = $interval->invert ? 0 : $interval->h;
		$i = $interval->invert ? 0 : $interval->i;
		$s = $interval->invert ? 0 : $interval->s;
		?>
		<p class="countdown sp-countdown<?php if ( $days >= 10 ): ?> long-countdown<?php endif; ?>">
			<time datetime="<?php echo $post->post_date; ?>"<?php if ( $live ): ?> data-countdown="<?php echo str_replace( '-', '/', $post->post_date ); ?>"<?php endif; ?>>
				<span><?php echo sprintf( '%02s', $days ); ?> <small><?php _e( 'days', 'sportszone' ); ?></small></span>
				<span><?php echo sprintf( '%02s', $h ); ?> <small><?php _e( 'hrs', 'sportszone' ); ?></small></span>
				<span><?php echo sprintf( '%02s', $i ); ?> <small><?php _e( 'mins', 'sportszone' ); ?></small></span>
				<span><?php echo sprintf( '%02s', $s ); ?> <small><?php _e( 'secs', 'sportszone' ); ?></small></span>
			</time>
		</p>
	</div>
</div>