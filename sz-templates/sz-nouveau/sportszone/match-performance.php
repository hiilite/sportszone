<?php
/**
 * Event Performance
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version   2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$show_players = get_option( 'sportszone_match_show_players', 'yes' ) === 'yes' ? true : false;
$show_staff = get_option( 'sportszone_match_show_staff', 'yes' ) === 'yes' ? true : false;
$show_total = get_option( 'sportszone_match_show_total', 'yes' ) === 'yes' ? true : false;
$show_numbers = get_option( 'sportszone_match_show_player_numbers', 'yes' ) === 'yes' ? true : false;
$show_position = get_option( 'sportszone_match_show_position', 'yes' ) === 'yes' ? true : false;
$show_minutes = get_option( 'sportszone_match_performance_show_minutes', 'no' ) === 'yes' ? true : false;
$sections = get_option( 'sportszone_match_performance_sections', -1 );
$abbreviate_teams = get_option( 'sportszone_abbreviate_teams', 'yes' ) === 'yes' ? true : false;
$reverse_teams = get_option( 'sportszone_match_reverse_teams', 'no' ) === 'yes' ? true : false;
$primary = sz_get_main_performance_option();
$total = get_option( 'sportszone_match_total_performance', 'all');

if ( ! $show_players && ! $show_staff && ! $show_total ) return;

if ( ! isset( $id ) ) 
	$id = get_the_ID();

$teams = get_post_meta( $id, 'sz_team', true );

$is_individual = sz_get_post_mode( $id ) === 'player' ? true : false;

if ( is_array( $teams ) ):
	ob_start();
	

	
	$match = new SZ_Match( $id );

	$performance = $match->performance();
	
	
	$stars = $match->stars();
	
	
	$link_posts = get_option( 'sportszone_link_players', 'yes' ) == 'yes' ? true : false;
	$scrollable = get_option( 'sportszone_enable_scrollable_tables', 'yes' ) == 'yes' ? true : false;
	$sortable = get_option( 'sportszone_enable_sortable_tables', 'yes' ) == 'yes' ? true : false;
	$mode = get_option( 'sportszone_match_performance_mode', 'values' );

	// The first row should be column labels
	$labels =  apply_filters( 'sportszone_match_box_score_labels', $performance[0], $match, $mode );

	// Add position to labels if selected
	if ( $show_position ) {
		$labels = array_merge( array( 'position' => __( 'Position', 'sportszone' ) ), $labels );
	}

	// Remove the first row to leave us with the actual data
	unset( $performance[0] );
	
	$performance = array_filter( $performance );

	
	
	$status = $match->status();
	
	if ( 'future' == $status ) {
		$show_total = false;
	}

	// Get performance ids for icons
	if ( $mode == 'icons' ):
		$performance_ids = array();
		$performance_posts = get_posts( array( 'posts_per_page' => -1, 'post_type' => 'sz_performance' ) );
		foreach ( $performance_posts as $post ):
			$performance_ids[ $post->post_name ] = $post->ID;
		endforeach;
	endif;

	if ( $reverse_teams ) {
		$teams = array_reverse( $teams, true );
	}
	
	// Get performance columns
	$args = array(
		'post_type' => 'sz_performance',
		'numberposts' => 100,
		'posts_per_page' => 100,
		'orderby' => 'menu_order',
		'order' => 'ASC',
	);

	$columns = get_posts( $args );
	
	// Get formats
	$formats = array();
	
	// Add to formats
	foreach ( $columns as $column ) {
		$format = get_post_meta( $column->ID, 'sz_format', true );
		if ( '' === $format ) {
			$format = 'number';
		}
		$formats[ $column->post_name ] = $format;
	}

	do_action( 'sportszone_before_match_performance' );
	
	if ( $is_individual ) {
		// Combined table
		$data = array();
		foreach($performance as $team_performance):
			foreach ( $team_performance as $players ) {
				foreach ( $players as $player_id => $player ) {
					if ( $player_id == 0 ) continue;
					$data[ $player_id ] = $player;
				}
			}
		endforeach;
		
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		sz_get_template( 'match-performance-table.php', array(
			'scrollable' => $scrollable,
			'sortable' => $sortable,
			'show_players' => $show_players,
			'show_numbers' => $show_numbers,
			'show_minutes' => $show_minutes,
			'show_total' => $show_total,
			'caption' => __( 'Box Score', 'sportszone' ),
			'labels' => $labels,
			'formats' => $formats,
			'mode' => $mode,
			'data' => $data,
			'match' => $match,
			'stars' => $stars,
			'link_posts' => $link_posts,
			'performance_ids' => isset( $performance_ids ) ? $performance_ids : null,
			'primary' => 'primary' == $total ? $primary : null,
		) );
	} else {
		// Prepare for offense and defense sections
		if ( -1 != $sections ) {
			
			// Determine order of sections
			if ( 1 == $sections ) {
				$section_order = array( 1 => __( 'Defense', 'sportszone' ), 0 => __( 'Offense', 'sportszone' ) );
			} else {
				$section_order = array( __( 'Offense', 'sportszone' ), __( 'Defense', 'sportszone' ) );
			}
			
			// Initialize labels
			$selected = $labels;
			$labels = array( array(), array() );
			
			// Add positions if applicable
			if ( $show_position ) {
				$labels[0]['position'] = $labels[1]['position'] = __( 'Position', 'sportszone' );
			}

			// Get labels by section
			foreach ( $columns as $column ):
				if ( ! array_key_exists( $column->post_name, $selected ) ) continue;
				$section = get_post_meta( $column->ID, 'sz_section', true );
				if ( '' === $section ) {
					$section = -1;
				}
				switch ( $section ):
					case 1:
						$labels[1][ $column->post_name ] = $column->post_title;
						break;
					case 0:
						$labels[0][ $column->post_name ] = $column->post_title;
						break;
					default:
						$labels[0][ $column->post_name ] = $column->post_title;
						$labels[1][ $column->post_name ] = $column->post_title;
				endswitch;
			endforeach;
		}

		foreach( $teams as $index => $team_id ) {
			if ( -1 == $team_id ) continue;

			// Get results for players in the team
			$sz_players = get_post_meta( $id, 'sz_player', true );
			$players = $sz_players[$index];
			$has_players = sizeof( $players ) > 1;

			$players = apply_filters( 'sportszone_match_performance_split_team_players', $players );

			$show_team_players = $show_players && $has_players;

			if ( ! $show_team_players && ! $show_staff && ! $show_total ) continue;

			if ( $show_team_players || $show_total ) {

				if ( -1 != $sections ) {

					$data = array();
					
					$offense = (array)get_post_meta( $id, 'sz_offense', false );
					$defense = (array)get_post_meta( $id, 'sz_defense', false );
					
					if ( sizeof( $offense ) || sizeof( $defense ) ) {
						// Get results for offensive players in the team
						$offense = sz_array_between( $offense, 0, $index );
						$data[0] = sz_array_combine( $offense, sz_array_value( $performance, $team_id, array() ) );
						
						// Get results for defensive players in the team
						$defense = sz_array_between( $defense, 0, $index );
						$data[1] = sz_array_combine( $defense, sz_array_value( $performance, $team_id, array() ) );
					} else {					
						// Get results for all players in the team
						
						$sz_players = get_post_meta( $id, 'sz_player', true );
						$players = $sz_players[$index];
						print_r($performance);
						$data[0] = $data[1] = sz_array_combine( $players, sz_array_value( $performance, $team_id, array() ) );
					}
					
					$s = 0;
						
					foreach ( $section_order as $section_id => $section_label ) {
						if ( sizeof( $data[ $section_id ] ) ) {
							
							if ( 1 == $section_id ) {
								$order = (array)get_post_meta( $id, 'sz_order', true );
								if ( is_array( $order ) && sizeof( $order ) ) {
									$player_order = sz_array_value( $order, $team_id, array() );
									if ( is_array( $player_order ) && sizeof( $player_order ) ) {
										$data[1] = sz_array_combine( $player_order, $data[1], true );
									}
								}
							}
							
							sz_get_template( 'match-performance-table.php', array(
								'section' => $section_id,
								'section_label' => $section_label,
								'scrollable' => $scrollable,
								'sortable' => $sortable,
								'show_players' => $show_team_players,
								'show_numbers' => $show_numbers,
								'show_minutes' => $show_minutes,
								'show_total' => $show_total,
								'caption' => 0 == $s && $team_id ? sz_get_team_name( $team_id, $abbreviate_teams ) : null,
								'labels' => $labels[ $section_id ],
								'formats' => $formats,
								'mode' => $mode,
								'data' => $data[ $section_id ],
								'match' => $match,
								'stars' => $stars,
								'link_posts' => $link_posts,
								'performance_ids' => isset( $performance_ids ) ? $performance_ids : null,
								'primary' => 'primary' == $total ? $primary : null,
								'class' => 'sp-template-match-performance-team-' . $index . ' sp-template-match-performance-section sp-template-match-performance-section-' . $section_id . ' sp-template-match-performance-team-' . $index . '-section-' . $section_id,
							) );
						}
						
						$s++;
					}
				} else {

					if ( 0 < $team_id ) {
						$data = sz_array_combine( $players, sz_array_value( $performance, $team_id, array() ) );
					} elseif ( 0 == $team_id ) {
						$data = array();
						foreach ( $players as $player_id ) {
							if ( isset( $performance[ $player_id ][ $player_id ] ) ) {
								echo '<!--MATCH-PERFORMANCE > gather player data-->';
								$data[ $player_id ] = $performance[ $player_id ][ $player_id ];
							}
						}
					} else {
						$data = sz_array_value( array_values( $performance ), $index );
					}
					sz_get_template( 'match-performance-table.php', array(
						'scrollable' => $scrollable,
						'sortable' => $sortable,
						'show_players' => $show_team_players,
						'show_numbers' => $show_numbers,
						'show_minutes' => $show_minutes,
						'show_total' => $show_total,
						'caption' => $team_id ? sz_get_team_name( $team_id, $abbreviate_teams ) : null,
						'labels' => $labels,
						'formats' => $formats,
						'mode' => $mode,
						'data' => $data,
						'match' => $match,
						'stars' => $stars,
						'link_posts' => $link_posts,
						'performance_ids' => isset( $performance_ids ) ? $performance_ids : null,
						'primary' => 'primary' == $total ? $primary : null,

					) );
				}
			}
		
			if ( $show_staff ):
				sz_get_template( 'match-staff.php', array( 'id' => $id, 'index' => $index ) );
			endif;
		}
		?>
		<?php
	}

	do_action( 'sportszone_match_performance' );

	$content = ob_get_clean();

	$content = trim( $content );

	if ( ! empty( $content ) ):
	echo '<!--sz-templates/sz-nouveau/sportszone/match-performance-->';
		?>
		<div class="sp-match-performance-tables sp-match-performance-teams">
			<?php echo $content; ?>
		</div><!-- .sp-match-performance-tables -->
		<?php
	endif;
endif;
