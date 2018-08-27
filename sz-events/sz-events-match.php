<?php
/**
 * Match Class
 *
 * The SportsPress event class handles individual event data.
 *
 * @class 		SZ_Event
 * @version		2.5.2
 * @package		SportsPress/Classes
 * @category	Class
 * @author 		ThemeBoy
 */
class SZ_Match extends SZ_Custom_Post {
	
	public function status() {
		$post_status = $this->post->post_status;
		$results = get_post_meta( $this->ID, 'sz_results', true );
		if ( is_array( $results ) ) {
			foreach( $results as $result ) {
				$result = array_filter( $result );
				if ( count( $result ) > 0 ) {
					return 'results';
				}
			}
		}
		return $post_status;
	}

	public function day() {
		$day = get_post_meta( $this->ID, 'sz_day', true );
		return $day;
  }
	
	public function minutes() {
		$minutes = get_post_meta( $this->ID, 'sz_minutes', true );
		if ( '' === $minutes ) $minutes = get_option( 'sportszone_event_minutes', 90 );
		return $minutes;
	}

	public function results( $admin = false ) {
		$teams = (array)get_post_meta( $this->ID, 'sz_team', false );
		$results = (array)get_post_meta( $this->ID, 'sz_results', true );

		// Get columns from result variables
		$columns = sz_get_var_labels( 'sz_result' );

		// Get result columns to display
		$usecolumns = get_post_meta( $this->ID, 'sz_result_columns', true );

		// Get results for all teams
		$data = sz_array_combine( $teams, $results, true );
		
		if ( 'yes' === get_option( 'sportszone_match_reverse_teams', 'no' ) ) {
			$data = array_reverse( $data, true );
		}

		if ( $admin ):
			return array( $columns, $usecolumns, $data );
		else:
			// Add outcome to result columns
			$columns['outcome'] = __( 'Outcome', 'sportszone' );
			if ( is_array( $usecolumns ) ):
				if ( 'manual' == get_option( 'sportszone_match_result_columns', 'auto' ) ):
					foreach ( $columns as $key => $label ):
						if ( ! in_array( $key, $usecolumns ) ):
							unset( $columns[ $key ] );
						endif;
					endforeach;
				else:
					$active_columns = array();
					foreach ( $data as $team_results ):
						foreach ( $team_results as $key => $result ):
							if ( is_string( $result ) && strlen( $result ) ):
								$active_columns[ $key ] = $key;
							endif;
						endforeach;
					endforeach;
					$columns = array_intersect_key( $columns, $active_columns );
				endif;

				if ( 'yes' == get_option( 'sportszone_match_show_outcome', 'no' ) ):
					$columns['outcome'] = __( 'Outcome', 'sportszone' );
				endif;
			endif;
			$data[0] = $columns;
			return $data;
		endif;
	}

	public function performance( $admin = false ) {
		$teams = get_post_meta( $this->ID, 'sz_team', false );
		$performance = (array)get_post_meta( $this->ID, 'sz_players', true );
		
		$args = array(
			'post_type' => 'sz_performance',
			'numberposts' => 100,
			'posts_per_page' => 100,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		);

		$vars = get_posts( $args );
		
		$labels = array();
		$formats = array();
		$timed = array();
		$stars = array();
		$equations = array();
		foreach ( $vars as $var ) {
			$labels[ $var->post_name ] = $var->post_title;

			$format = get_post_meta( $var->ID, 'sz_format', true );
			if ( '' === $format ) {
				$format = 'number';
			}
			$formats[ $var->post_name ] = $format;

			if ( 'number' === $format ) {
				$is_timed = get_post_meta( $var->ID, 'sz_timed', true );
				if ( '' === $is_timed || $is_timed ) {
					$timed[] = $var->post_name;
				}
			} elseif ( 'equation' === $format ) {
				$equation = get_post_meta( $var->ID, 'sz_equation', true );
				$precision = get_post_meta( $var->ID, 'sz_precision', true );
				
				if ( empty( $equation ) ) $equation = 0;
				if ( empty( $precision ) ) $precision = 0;
				
				$equations[ $var->post_name ] = array(
					'equation' => $equation,
					'precision' => $precision,
				);
			}
		}
		
		$order = (array)get_post_meta( $this->ID, 'sz_order', true );

		if ( get_option( 'sportszone_event_performance_stars_type', 0 ) ) {
			$stars = (array)get_post_meta( $this->ID, 'sz_stars', true );
		}
		
		$labels = apply_filters( 'sportszone_event_performance_labels', $labels, $this );
		$columns = get_post_meta( $this->ID, 'sz_columns', true );
		if ( is_array( $teams ) ):
			foreach( $teams as $i => $team_id ):
				$players = sz_array_between( (array)get_post_meta( $this->ID, 'sz_player', false ), 0, $i );
				$data = sz_array_combine( $players, sz_array_value( $performance, $team_id, array() ) );

				foreach( $data as $player_id => $player_performance ):
					if ( ! $player_id ) continue;

					if ( ! array_key_exists( 'number', $player_performance ) ):
						$performance[ $team_id ][ $player_id ]['number'] = apply_filters( 'sportszone_event_performance_default_squad_number', get_post_meta( $player_id, 'sz_number', true ) );
					endif;
					if ( ! array_key_exists( 'position', $player_performance ) || $player_performance['position'] == null ):
						$performance[ $team_id ][ $player_id ]['position'] = sz_get_the_term_id( $player_id, 'sz_position', null );
					endif;
				endforeach;
			endforeach;
		endif;

		if ( $admin ):
			return array( $labels, $columns, $performance, $teams, $formats, $order, $timed, $stars );
		else:
			// Add position to performance labels
			if ( taxonomy_exists( 'sz_position' ) ):
				$labels = array_merge( array( 'position' => __( 'Position', 'sportszone' )  ), $labels );
			endif;
			if ( 'manual' == get_option( 'sportszone_event_performance_columns', 'auto' ) && is_array( $columns ) ):
				foreach ( $labels as $key => $label ):
					if ( ! in_array( $key, $columns ) ):
						unset( $labels[ $key ] );
					endif;
				endforeach;
			endif;

			if ( 'no' == get_option( 'sportszone_event_show_position', 'yes' ) ):
				unset( $labels['position'] );
			endif;
			if ( 'no' == get_option( 'sportszone_event_show_player_numbers', 'yes' ) ):
				unset( $labels['number'] );
			endif;

			// Calculate equation-based performance
			if ( sizeof( $equations ) ):
				foreach ( $performance as $team => $players ):
					if ( ! is_array( $players ) ) continue;

					foreach ( $players as $player => $player_performance ):
						if ( ! is_array( $player_performance ) ) continue;

						// Prepare existing values for equation calculation
						$vars = $player_performance;

						foreach ( $vars as $key => $var ):
							if ( empty( $var ) ) $vars[ $key ] = 0;
						endforeach;
						$vars = array_merge( $vars, array( 'eventsplayed' => 1 ) );

						foreach ( $equations as $key => $equation ):
							$performance[ $team ][ $player ][ $key ] = sz_solve( $equation['equation'], $vars, $equation['precision'] );
						endforeach;
					endforeach;
				endforeach;
			endif;

			// Convert to time notation
			if ( in_array( 'time', $formats ) ):
				foreach ( $performance as $team => $players ):
					if ( ! is_array( $players ) ) continue;

					foreach ( $players as $player => $player_performance ):
						if ( ! $player ) continue;

						foreach ( $player_performance as $performance_key => $performance_value ):

							// Continue if not time format
							if ( 'time' !== sz_array_value( $formats, $performance_key ) ) continue;

							$intval = intval( $performance_value );
							$timeval = gmdate( 'i:s', $intval );
							$hours = floor( $intval / 3600 );

							if ( '00' != $hours )
								$timeval = $hours . ':' . $timeval;

							$timeval = preg_replace( '/^0/', '', $timeval );

							$performance[ $team ][ $player ][ $performance_key ] = $timeval;
						endforeach;
					endforeach;
				endforeach;
			endif;

			// Add minutes to box score values
			if ( in_array( 'number', $formats ) && 'yes' == get_option( 'sportszone_event_performance_show_minutes', 'no' ) ):
				$timeline = $this->timeline();
				if ( ! empty( $timeline ) ):
					foreach ( $performance as $team => $players ):

						// Get team timeline
						$team_timeline = sz_array_value( $timeline, $team, array() );
						if ( empty( $team_timeline ) ) continue;

						foreach ( $players as $player => $player_performance ):
							if ( ! $player ) continue;

							// Get player timeline
							$player_timeline = sz_array_value( $team_timeline, $player, array() );
							if ( empty( $player_timeline ) ) continue;

							foreach ( $player_performance as $performance_key => $performance_value ):

								// Continue if not timed
								if ( ! in_array( $performance_key, $timed ) ) continue;

								// Get performance times
								$times = sz_array_value( $player_timeline, $performance_key, array() );
								$times = array_filter( $times );
								if ( empty( $times ) ) continue;

								$performance[ $team ][ $player ][ $performance_key ] .= ' (' . implode( '\', ', $times ) . '\')';
							endforeach;
						endforeach;
					endforeach;
				endif;
			endif;

			// Add labels to box score
			$performance[0] = $labels;
			
			return apply_filters( 'sportszone_get_event_performance', $performance );
		endif;
	}
	
	public function timeline( $admin = false, $linear = false ) {
		$timeline = (array) get_post_meta( $this->ID, 'sz_timeline', true );

		if ( ! $linear ) return $timeline;

		$performance = (array) get_post_meta( $this->ID, 'sz_players', true );
		if ( empty( $timeline ) ) return array();

		$stats = array();
		$player_ids = array();
		$performance_keys = array();

		// Clean up timeline
		foreach ( $timeline as $team => $players ) {
			if ( ! $team ) continue;

			// Set home team
			if ( ! isset( $home_team ) ) $home_team = $team;

			// Determine side
			if ( $home_team === $team ) {
				$side = 'home';
			} else {
				$side = 'away';
			}

			$stats[] = array(
				'time' => -1,
				'id' => $team,
				'team' => $team,
				'side' => $side,
				'key' => 'team',
			);

			if ( ! is_array( $players ) ) continue;

			foreach ( $players as $player => $keys ) {
				if ( ! $player ) continue;
				if ( ! is_array( $keys ) ) continue;

				$player_ids[] = $player;

				foreach ( $keys as $key => $times ) {
					if ( ! is_array( $times ) || empty( $times ) ) continue;

					foreach ( $times as $time ) {
						if ( '' === $time ) continue;

						$entry = array(
							'time' => $time,
							'id' => $player,
							'team' => $team,
							'side' => $side,
							'key' => $key,
						);

						if ( 'sub' === $key ) {
							$sub = sz_array_value( sz_array_value( sz_array_value( $performance, $team ), $player ), 'sub', 0 );
							$entry['sub'] = $sub;
							$player_ids[] = $sub;
						}

						$stats[] = $entry;
					}

					$performance_keys[] = $key;
				}
			}
		}

		// Filter out duplicate player IDs and performance keys
		$player_ids = array_unique( $player_ids );
		$performance_keys = array_unique( $performance_keys );

		// Get player names and numbers
		$posts = get_posts( array(
			'post_type' => 'sz_player',
			'posts_per_page' => -1,
			'post__in' => $player_ids
		) );

		$player_names = array();
		$player_numbers = array();

		if ( $posts ) {
			foreach ( $posts as $post ) {
				$player_names[ $post->ID ] = $post->post_title;
				$player_numbers[ $post->ID ] = get_post_meta( $post->ID, 'sz_number', true );
			}
		}

		// Get performance labels and icons
		$posts = get_posts( array(
			'post_type' => 'sz_performance',
			'posts_per_page' => -1,
			'post_name__in' => $performance_keys
		) );

		$performance_labels = array();
		$performance_icons = array();

		if ( $posts ) {
			foreach ( $posts as $post ) {
				$performance_labels[ $post->post_name ] = $post->post_title;

				$icon = '';
				if ( has_post_thumbnail( $post->ID ) ) {
					$icon = get_the_post_thumbnail( $post->ID, 'sportszone-fit-mini', array( 'title' => sz_get_singular_name( $post->ID ) ) );
				}
				$performance_icons[ $post->post_name ] = apply_filters( 'sportszone_event_performance_icons', $icon, $post->ID, 1 );
			}
		}

		// Add missing info to stats
		foreach ( $stats as $index => $details ) {
			$stats[ $index ]['name'] = sz_array_value( $player_names, $details['id'] );
			$stats[ $index ]['number'] = sz_array_value( $player_numbers, $details['id'] );

			if ( 'team' === $details['key'] ) {
				$name = sz_get_team_name( $details['team'] );
				$stats[ $index ]['name'] = $name;
				$stats[ $index ]['label'] = $name;
				$stats[ $index ]['icon'] = sz_get_logo( $details['team'] );
			} elseif ( 'sub' === $details['key'] ) {
				$sub_name = sz_array_value( $player_names, $details['sub'], __( 'Substitute', 'sportszone' ) );
				$sub_number = sz_array_value( $player_numbers, $details['sub'] );

				if ( '' !== $sub_number ) {
					$icon_title = $sub_number . '. ' . $sub_name;
				} else {
					$icon_title = $sub_name;
				}

				$stats[ $index ]['sub_name'] = $sub_name;
				$stats[ $index ]['sub_number'] = $sub_number;
				$stats[ $index ]['label'] = __( 'Substitute', 'sportszone' );
				$stats[ $index ]['icon'] = '<i class="sp-icon-sub" title="' . $icon_title . '"></i>';
			} else {
				$stats[ $index ]['label'] = sz_array_value( $performance_labels, $details['key'] );
				$stats[ $index ]['icon'] = sz_array_value( $performance_icons, $details['key'] );
			}
		}

		usort( $stats, array( $this, 'sort_timeline' ) );

		return $stats;
	}

	public function main_results() {
		// Get main result option
		$main_result = get_option( 'sportszone_primary_result', null );

		// Get teams from event
		$teams = get_post_meta( $this->ID, 'sz_team', false );
		
		// Initialize output
		$output = array();

		// Return empty array if there are no teams
		if ( ! $teams ) return $output;

		// Get results from event
		$results = get_post_meta( $this->ID, 'sz_results', true );

		// Loop through teams			
		foreach ( $teams as $team_id ) {

			// Skip if not a team
			if ( ! $team_id ) continue;

			// Get team results from all results
			$team_results = sz_array_value( $results, $team_id, null );

			// Get main or last result
			if ( $main_result ) {
			
				// Get main result from team results
				$team_result = sz_array_value( $team_results, $main_result, null );
			} else {

				// If there are any team results available
				if ( is_array( $team_results ) ) {

					// Get last result that is not outcome
					unset( $team_results['outcome'] );
					$team_result = end( $team_results );
				} else {

					// Give team null result
					$team_result = null;
				}
			}

			if ( null != $team_result ) {
				$output[] = $team_result;
			}
		}

		return $output;
	}

	public function outcome( $single = true ) {
		// Get teams from event
		$teams = get_post_meta( $this->ID, 'sz_team', false );
		
		// Initialize output
		$output = array();

		// Return empty array if there are no teams
		if ( ! $teams ) return $output;

		// Get results from event
		$results = get_post_meta( $this->ID, 'sz_results', true );

		// Loop through teams			
		foreach ( $teams as $team_id ) {

			// Skip if not a team
			if ( ! $team_id ) continue;

			// Get team results from all results
			$team_results = sz_array_value( $results, $team_id, null );

			// Get outcome from team results
			$team_outcome = sz_array_value( $team_results, 'outcome', null );

			if ( null != $team_outcome ) {

				// Make sure that we have an array of outcomes
				$team_outcome = (array) $team_outcome;

				// Use only first outcome if single
				if ( $single ) {
					$team_outcome = reset( $team_outcome );
				}

				// Add outcome to output
				$output[ $team_id ] = $team_outcome;
			}
		}

		return $output;
	}

	public function winner() {
		// Get the first configured outcome
		$outcome = get_posts( array(
			'post_type' => 'sz_outcome',
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		) );

		// Return if no outcomes available
		if ( ! $outcome ) return null;

		$outcome = reset( $outcome );

		// Get event outcomes
		$outcomes = self::outcome( false );

		// Look for a team that meets the criteria
		foreach ( $outcomes as $team_id => $team_outcomes ) {
			if ( in_array( $outcome->post_name, $team_outcomes ) ) {
				return $team_id;
			}
		}

		// Return if no teams meet criteria
		return null;
	}
	
	public function appointments( $include_empty = false, $placeholder = '-' ) {
		$officials = (array) get_post_meta( $this->ID, 'sz_officials', true );
		$officials = array_filter( $officials );

		if ( ! $include_empty && empty( $officials ) ) return null;

		$duties = get_terms( array(
		  'taxonomy' => 'sz_duty',
		  'hide_empty' => false,
			'orderby' => 'meta_value_num',
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => 'sz_order',
					'compare' => 'NOT EXISTS'
				),
				array(
					'key' => 'sz_order',
					'compare' => 'EXISTS'
				),
			),
		) );

		if ( ! $include_empty && empty( $duties ) ) return null;

		$labels = array();
		$appointments = array();

		foreach ( $duties as $duty ) {
			$duty_appointments = sz_array_value( $officials, $duty->term_id, null );

			if ( ! $include_empty && empty( $duty_appointments ) ) continue;

			$appointed_officials = array();
			foreach ( $duty_appointments as $duty_appointment ) {
				$appointed_officials[ $duty_appointment ] = get_the_title( $duty_appointment );
			}

			if ( $include_empty && empty( $appointed_officials ) ) $appointed_officials[] = $placeholder;

			$appointments[ $duty->slug ] = $appointed_officials;
			$labels[ $duty->slug ] = $duty->name;
		}

		$appointments[0] = $labels;

		return $appointments;
	}

	public function update_main_results( $results ) {
		$main_result = sz_get_main_result_option();

		if ( ! $this->ID || ! is_array( $results ) || null === $main_result ) {
			return false;
		}

		// Get current results meta
		$meta =(array) get_post_meta( $this->ID, 'sz_results', true );

		$primary_results = array();
		foreach ( $results as $id => $result ) {
			$primary_results[ $id ] = $result;

			if ( ! $id ) continue;

			$meta[ $id ][ $main_result ] = $result;
		}

		arsort( $primary_results );

		if ( count( $primary_results ) && ! in_array( null, $primary_results ) ) {
			if ( count( array_unique( $primary_results ) ) === 1 ) {
				$args = array(
					'post_type' => 'sz_outcome',
					'numberposts' => -1,
					'posts_per_page' => -1,
					'meta_key' => 'sz_condition',
					'meta_value' => '=',
				);
				$outcomes = get_posts( $args );
				foreach ( $meta as $team => $team_results ) {
					if ( $outcomes ) {
						$meta[ $team ][ 'outcome' ] = array();
						foreach ( $outcomes as $outcome ) {
							$meta[ $team ][ 'outcome' ][] = $outcome->post_name;
						}
					}
				}
			} else {
				// Get default outcomes
				$args = array(
					'post_type' => 'sz_outcome',
					'numberposts' => -1,
					'posts_per_page' => -1,
					'meta_key' => 'sz_condition',
					'meta_value' => 'else',
				);
				$default_outcomes = get_posts( $args );

				// Get greater than outcomes
				$args = array(
					'post_type' => 'sz_outcome',
					'numberposts' => -1,
					'posts_per_page' => -1,
					'meta_key' => 'sz_condition',
					'meta_value' => '>',
				);
				$gt_outcomes = get_posts( $args );
				if ( empty ( $gt_outcomes ) ) $gt_outcomes = $default_outcomes;

				// Get less than outcomes
				$args = array(
					'post_type' => 'sz_outcome',
					'numberposts' => -1,
					'posts_per_page' => -1,
					'meta_key' => 'sz_condition',
					'meta_value' => '<',
				);
				$lt_outcomes = get_posts( $args );
				if ( empty ( $lt_outcomes ) ) $lt_outcomes = $default_outcomes;

				// Get min and max values
				$min = min( $primary_results );
				$max = max( $primary_results );

				foreach ( $primary_results as $key => $value ) {
					if ( $min == $value ) {
						$outcomes = $lt_outcomes;
					} elseif ( $max == $value ) {
						$outcomes = $gt_outcomes;
					} else {
						$outcomes = $default_outcomes;
					}
					$meta[ $key ][ 'outcome' ] = array();
					foreach ( $outcomes as $outcome ) {
						$meta[ $key ][ 'outcome' ][] = $outcome->post_name;
					}
				}
			}
		}

		// Update results
		update_post_meta( $this->ID, 'sz_results', $meta );
	}

	public function stars() {
		return get_post_meta( $this->ID, 'sz_stars', true );
	}

	public function lineup_filter( $v ) {
		return sz_array_value( $v, 'status', 'lineup' ) == 'lineup';
	}

	public function sub_filter( $v ) {
		return sz_array_value( $v, 'status', 'lineup' ) == 'sub';
	}

	public function sort_timeline( $a, $b ) {
		return $a['time'] - $b['time'];
	}
}
