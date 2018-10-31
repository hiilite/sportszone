<?php
/**
 * Player Class
 *
 * The SportsPress player class handles individual player data.
 *
 * @class 		SZ_Player
 * @version   2.5
 * @package		SportsPress/Classes
 * @category	Class
 * @author 		ThemeBoy
 */
class SZ_Player {
	/** @var int The post ID. */
	public $ID;

	/** @var object The actual post object. */
	public $user;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $post
	 */
	public function __construct( $user ) {
		if ( $user instanceof WP_User ):
			$this->ID   = absint( $user->ID );
			$this->user = $user;
		else:
			$this->ID  = absint( $user );
			$this->user = get_userdata( $this->ID );
		endif;
	}

	/**
	 * __isset function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return bool
	 */
	public function __isset( $key ) {
		return metadata_exists( 'post', $this->ID, 'sz_' . $key );
	}

	/**
	 * __get function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return bool
	 */
	public function __get( $key ) {
		if ( ! isset( $key ) ):
			return $this->post;
		else:
			$value = get_post_meta( $this->ID, 'sz_' . $key, true );
		endif;

		return $value;
	}

	/**
	 * Get the post data.
	 *
	 * @access public
	 * @return object
	 */
	public function get_post_data() {
		return $this->post;
	}

	/**
	 * Returns positions
	 *
	 * @access public
	 * @return array
	 */
	public function positions() {
		return get_the_terms( $this->ID, 'sz_position' );
	}

	/**
	 * Returns leagues
	 *
	 * @access public
	 * @return array
	 */
	public function leagues() {
		return get_the_terms( $this->ID, 'sz_league' );
	}

	/**
	 * Returns seasons
	 *
	 * @access public
	 * @return array
	 */
	public function seasons() {
		return get_the_terms( $this->ID, 'sz_season' );
	}

	/**
	 * Returns current teams
	 *
	 * @access public
	 * @return array
	 */
	public function current_teams() {
		return get_post_meta( $this->ID, 'sz_current_team', false );
	}

	/**
	 * Returns past teams
	 *
	 * @access public
	 * @return array
	 */
	public function past_teams() {
		return get_post_meta( $this->ID, 'sz_past_team', false );
	}

	/**
	 * Returns nationalities
	 *
	 * @access public
	 * @return array
	 */
	public function nationalities() {
		$nationalities = get_post_meta( $this->ID, 'sz_nationality', false );
		if ( empty ( $nationalities ) ) return array();
		foreach ( $nationalities as $nationality ):
			if ( 2 == strlen( $nationality ) ):
				$legacy = SP()->countries->legacy;
				$nationality = strtolower( $nationality );
				$nationality = sz_array_value( $legacy, $nationality, null );
			endif;
		endforeach;
		return $nationalities;
	}

	/**
	 * Returns formatted player metrics
	 *
	 * @access public
	 * @return array
	 */
	public function metrics( $neg = null ) {
		$metrics = (array)get_post_meta( $this->ID, 'sz_metrics', true );
		$metric_labels = (array)sz_get_var_labels( 'sz_metric', $neg, false );
		$data = array();
		
		foreach ( $metric_labels as $key => $value ):
			$metric = sz_array_value( $metrics, $key, null );
			if ( $metric == null )
				continue;
			$data[ $value ] = sz_array_value( $metrics, $key, '&nbsp;' );
		endforeach;
		return $data;
	}

	/**
	 * Returns formatted data
	 *
	 * @access public
	 * @param int $league_id
	 * @param bool $admin
	 * @return array
	 */
	public function data( $league_id, $admin = false, $section = -1 ) {

		$seasons = (array)get_the_terms( $this->ID, 'sz_season' );
		$metrics = (array)get_post_meta( $this->ID, 'sz_metrics', true );
		$stats = (array)get_post_meta( $this->ID, 'sz_statistics', true );
		$leagues = sz_array_value( (array)get_post_meta( $this->ID, 'sz_leagues', true ), $league_id, array() );
		$abbreviate_teams = get_option( 'sportspress_abbreviate_teams', 'yes' ) === 'yes' ? true : false;
		$manual_columns = 'manual' == get_option( 'sportspress_player_columns', 'auto' ) ? true : false;
		
		// Get performance labels
		$args = array(
			'post_type' => array( 'sz_performance' ),
			'numberposts' => 100,
			'posts_per_page' => 100,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'meta_query' => array(
        		'relation' => 'OR',
				array(
					'key' => 'sz_format',
					'value' => 'number',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key' => 'sz_format',
					'value' => array( 'equation', 'text' ),
					'compare' => 'NOT IN',
				),
			),
		);

		$posts = get_posts( $args );
		
		if ( $manual_columns ) {
			$usecolumns = (array)get_post_meta( $this->ID, 'sz_columns', true );
			$has_checkboxes = true;
		} else {
			$usecolumns = array();
			if ( is_array( $posts ) ) {
				foreach ( $posts as $post ) {
					// Get visibility
					$visible = get_post_meta( $post->ID, 'sz_visible', true );
					if ( '' === $visible || $visible ) {
						$usecolumns[] = $post->post_name;
					}
				}
			}
			$has_checkboxes = false;
		}
		
		$performance_labels = array();
		$formats = array();
		$sendoffs = array();
		
		foreach ( $posts as $post ):
			if ( -1 === $section ) {
				$performance_labels[ $post->post_name ] = $post->post_title;
			} else {
				$post_section = get_post_meta( $post->ID, 'sz_section', true );
				
				if ( '' === $post_section ) {
					$post_section = -1;
				}
				
				if ( $section == $post_section || -1 == $post_section ) {
					$performance_labels[ $post->post_name ] = $post->post_title;
				}
			}

			$format = get_post_meta( $post->ID, 'sz_format', true );
			if ( '' === $format ) {
				$format = 'number';
			}
			$formats[ $post->post_name ] = $format;

			$sendoff = get_post_meta( $post->ID, 'sz_sendoff', true );
			if ( $sendoff ) {
				$sendoffs[] = $post->post_name;
			}
		endforeach;
		
		// TODO: Rewrite to use new statistics system
		// Get statistic labels
		$args = array(
			'post_type' => array( 'sz_statistic' ),
			'numberposts' => 100,
			'posts_per_page' => 100,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		);

		$posts = get_posts( $args );
		
		
		if ( is_array( $posts ) ) {
			foreach ( $posts as $post ) {
				// Get visibility
				$visible = get_post_meta( $post->ID, 'sz_visible', true );
				
				if ( '' === $visible || $visible ) {
					$usecolumns[] = $post->post_name;
				}
			}
		}
		
		
		// Get labels from outcome variables
		$outcome_labels = (array)sz_get_var_labels( 'sz_outcome' );

		// Get labels from result variables
		$result_labels = (array)sz_get_var_labels( 'sz_result' );
		
		// Generate array of all season ids and season names
		$div_ids = array();
		$season_names = array();
		foreach ( $seasons as $season ):
			if ( is_object( $season ) && property_exists( $season, 'term_id' ) && property_exists( $season, 'name' ) ):
				$div_ids[] = $season->term_id;
				$season_names[ $season->term_id ] = $season->name;
			endif;
		endforeach;
		
		$div_ids[] = 0;
		$season_names[0] = __( 'Total', 'sportspress' );

		$data = array();

		// Get all seasons populated with data where available
		$data = sz_array_combine( $div_ids, sz_array_value( $stats, $league_id, array() ) );

		// Get equations from statistic variables
		$equations = sz_get_var_equations( 'sz_statistic' );

		// Initialize placeholders array
		$placeholders = array();
		
		foreach ( $div_ids as $div_id ):

			$totals = array( 'eventsattended' => 0, 'eventsplayed' => 0, 'eventsstarted' => 0, 'eventssubbed' => 0, 'eventminutes' => 0, 'streak' => 0, 'last5' => null, 'last10' => null );
			
			foreach ( $performance_labels as $key => $value ):
				$totals[ $key ] = 0;
			endforeach;

			foreach ( $outcome_labels as $key => $value ):
				$totals[ $key ] = 0;
			endforeach;

			foreach ( $result_labels as $key => $value ):
				$totals[ $key . 'for' ] = $totals[ $key . 'against' ] = 0;
			endforeach;

			// Initialize streaks counter
			$streak = array( 'name' => '', 'count' => 0, 'fire' => 1 );

			// Initialize last counters
			$last5 = array();
			$last10 = array();
			
			// Add outcome types to last counters
			foreach( $outcome_labels as $key => $value ):
				$last5[ $key ] = 0;
				$last10[ $key ] = 0;
			endforeach;

			// Get all events involving the player in current season
			$args = array(
				'post_type' => 'sz_event',
				'numberposts' => -1,
				'posts_per_page' => -1,
				'order' => 'DESC',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'sz_player',
						'value' => $this->ID
					),
					array(
						'key' => 'sz_format',
						'value' => apply_filters( 'sportspress_competitive_event_formats', array( 'league' ) ),
						'compare' => 'IN',
					),
				),
				'tax_query' => array(
					'relation' => 'AND',
				),
			);
			
			if ( -1 !== $section ):
				$args['meta_query'][] = array(
					'key' => ( 1 === $section ? 'sz_defense' : 'sz_offense' ),
					'value' => $this->ID
				);
			endif;

			if ( $league_id ):
				$args['tax_query'][] = array(
					'taxonomy' => 'sz_league',
					'field' => 'term_id',
					'terms' => $league_id
				);
			endif;

			if ( $div_id ):
				$args['tax_query'][] = array(
					'taxonomy' => 'sz_season',
					'field' => 'term_id',
					'terms' => $div_id
				);
			endif;

			$args = apply_filters( 'sportspress_player_data_event_args', $args );

			$events = get_posts( $args );
			
			// Event loop
			foreach( $events as $i => $event ):
				$results = (array)get_post_meta( $event->ID, 'sz_results', true );
				$team_performance = (array)get_post_meta( $event->ID, 'sz_players', true );
				$timeline = (array)get_post_meta( $event->ID, 'sz_timeline', true );
				$minutes = get_post_meta( $event->ID, 'sz_minutes', true );
				if ( $minutes === '' ) $minutes = get_option( 'sportspress_event_minutes', 90 );

				// Add all team performance
				foreach ( $team_performance as $team_id => $players ):
					if ( is_array( $players ) && array_key_exists( $this->ID, $players ) ):

						$player_performance = sz_array_value( $players, $this->ID, array() );

						foreach ( $player_performance as $key => $value ):
							if ( array_key_exists( $key, $totals ) ):
								$value = floatval( $value );
								$totals[ $key ] += $value;
							endif;
						endforeach;

						$team_results = sz_array_value( $results, $team_id, array() );
						unset( $results[ $team_id ] );

						// Loop through home team
						foreach ( $team_results as $result_slug => $team_result ):
							if ( 'outcome' == $result_slug ):

								// Increment events attended
								$totals['eventsattended'] ++;

								// Continue with incrementing values if active in event
								if ( sz_array_value( $player_performance, 'status' ) != 'sub' || sz_array_value( $player_performance, 'sub', 0 ) ): 
									$totals['eventsplayed'] ++;
									$played_minutes = $minutes;

									// Adjust for substitution time
									if ( sz_array_value( $player_performance, 'status' ) === 'sub' ):

										// Substituted for another player
										$timeline_performance = sz_array_value( sz_array_value( $timeline, $team_id, array() ), $this->ID, array() );
										if ( empty( $timeline_performance ) ) continue;
										foreach ( $sendoffs as $sendoff_key ):
											if ( ! array_key_exists( $sendoff_key, $timeline_performance ) ) continue;
											$sendoff_times = sz_array_value( sz_array_value( sz_array_value( $timeline, $team_id ), $this->ID ), $sendoff_key );
											$sendoff_times = array_filter( $sendoff_times );
											$sendoff_time = end( $sendoff_times );
											if ( ! $sendoff_time ) $sendoff_time = 0;

											// Count minutes until being sent off
											$played_minutes = $sendoff_time;
										endforeach;

										// Subtract minutes prior to substitution
										$substitution_time = sz_array_value( sz_array_value( sz_array_value( sz_array_value( $timeline, $team_id ), $this->ID ), 'sub' ), 0, 0 );
										$played_minutes -= $substitution_time;
									else:

										// Starting lineup with possible substitution
										$subbed_out = false;
										foreach ( $timeline as $timeline_team => $timeline_players ):
											if ( ! is_array( $timeline_players ) ) continue;
											foreach ( $timeline_players as $timeline_player => $timeline_performance ):
												if ( 'sub' === sz_array_value( sz_array_value( $players, $timeline_player, array() ), 'status' ) && $this->ID === (int) sz_array_value( sz_array_value( $players, $timeline_player, array() ), 'sub', 0 ) ):
													$substitution_time = sz_array_value( sz_array_value( sz_array_value( sz_array_value( $timeline, $team_id ), $timeline_player ), 'sub' ), 0, 0 );
													if ( $substitution_time ):

														// Count minutes until substitution
														$played_minutes = $substitution_time;
														$subbed_out = true;
													endif;
												endif;
											endforeach;

											// No need to check for sendoffs if subbed out
											if ( $subbed_out ) continue;

											// Check for sendoffs
											$timeline_performance = sz_array_value( $timeline_players, $this->ID, array() );
											if ( empty( $timeline_performance ) ) continue;
											foreach ( $sendoffs as $sendoff_key ):
												if ( ! array_key_exists( $sendoff_key, $timeline_performance ) ) continue;
												$sendoff_times = (array) sz_array_value( sz_array_value( sz_array_value( $timeline, $team_id ), $this->ID ), $sendoff_key, array() );
												$sendoff_times = array_filter( $sendoff_times );
												$sendoff_time = end( $sendoff_times );
												if ( false === $sendoff_time ) continue;

												// Count minutes until being sent off
												$played_minutes = $sendoff_time;
											endforeach;
										endforeach;
									endif;

									$totals['eventminutes'] += max( 0, $played_minutes );

									if ( sz_array_value( $player_performance, 'status' ) == 'lineup' ):
										$totals['eventsstarted'] ++;
									elseif ( sz_array_value( $player_performance, 'status' ) == 'sub' && sz_array_value( $player_performance, 'sub', 0 ) ):
										$totals['eventssubbed'] ++;
									endif;

									$value = $team_result;

									// Convert to array
									if ( ! is_array( $value ) ):
										$value = array( $value );
									endif;

									foreach ( $value as $outcome ):
										if ( $outcome && $outcome != '-1' ):

											// Increment outcome count
											if ( array_key_exists( $outcome, $totals ) ):
												$totals[ $outcome ] ++;
											endif;

											// Add to streak counter
											if ( $streak['fire'] && ( $streak['name'] == '' || $streak['name'] == $outcome ) ):
												$streak['name'] = $outcome;
												$streak['count'] ++;
											else:
												$streak['fire'] = 0;
											endif;

											// Add to last 5 counter if sum is less than 5
											if ( array_key_exists( $outcome, $last5 ) && array_sum( $last5 ) < 5 ):
												$last5[ $outcome ] ++;
											endif;

											// Add to last 10 counter if sum is less than 10
											if ( array_key_exists( $outcome, $last10 ) && array_sum( $last10 ) < 10 ):
												$last10[ $outcome ] ++;
											endif;
										endif;
									endforeach;
								endif;
							else:

								// Add to total
								$value = sz_array_value( $totals, $result_slug . 'for', 0 );
								$value += floatval( $team_result );
								$totals[ $result_slug . 'for' ] = $value;

								// Add subset
								$totals[ $result_slug . 'for' . ( $i + 1 ) ] = $team_result;
							endif;
						endforeach;

						// Loop through away teams
						if ( sizeof( $results ) ):
							foreach ( $results as $team_results ):
								unset( $team_results['outcome'] );
								foreach ( $team_results as $result_slug => $team_result ):

									// Add to total
									$value = sz_array_value( $totals, $result_slug . 'against', 0 );
									$value += floatval( $team_result );
									$totals[ $result_slug . 'against' ] = $value;

									// Add subset
									$totals[ $result_slug . 'against' . ( $i + 1 ) ] = $team_result;
								endforeach;
							endforeach;
						endif;
					endif;
				endforeach;
				$i++;
			endforeach; 
			
			// Compile streaks counter and add to totals
			$args = array(
				'name' => $streak['name'],
				'post_type' => 'sz_outcome',
				'post_status' => 'publish',
				'posts_per_page' => 1
			);
			$outcomes = get_posts( $args );
			
			if ( $outcomes ):
				$outcome = reset( $outcomes );
				$abbreviation = sz_get_abbreviation( $outcome->ID );
				if ( empty( $abbreviation ) ) $abbreviation = strtoupper( substr( $outcome->post_title, 0, 1 ) );
				$totals['streak'] = $abbreviation . $streak['count'];
			endif;

			// Add last counters to totals
			$totals['last5'] = $last5;
			$totals['last10'] = $last10;
			
			// Add metrics to totals
			$totals = array_merge( $metrics, $totals );
			
			// Generate array of placeholder values for each league
			$placeholders[ $div_id ] = array();
			foreach ( $equations as $key => $value ):
				// TODO: Solve for sz_solve
				//$placeholders[ $div_id ][ $key ] = sz_solve( $value['equation'], $totals, $value['precision'] );
			endforeach;
			
			foreach ( $performance_labels as $key => $label ):
				$placeholders[ $div_id ][ $key ] = sz_array_value( $totals, $key, 0 );
			endforeach;
			
		endforeach;
		
		// Get labels by section
		$args = array(
			'post_type' => 'sz_statistic',
			'numberposts' => 100,
			'posts_per_page' => 100,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		);

		$posts = get_posts( $args );
		
		$stats = array();

		foreach ( $posts as $post ):
			if ( -1 === $section ) {
				$stats[ $post->post_name ] = $post->post_title;
			} else {
				$post_section = get_post_meta( $post->ID, 'sz_section', true );
				
				if ( '' === $post_section ) {
					$post_section = -1;
				}
				
				if ( $admin ) {
					if ( 1 == $section ) {
						if ( 1 == $post_section ) {
							$stats[ $post->post_name ] = $post->post_title;
						}
					} else {
						if ( 1 != $post_section ) {
							$stats[ $post->post_name ] = $post->post_title;
						}
					}
				} elseif ( $section == $post_section || -1 == $post_section ) {
					$stats[ $post->post_name ] = $post->post_title;
				}
			}
		endforeach;

		// Merge the data and placeholders arrays
		$merged = array();

		foreach( $placeholders as $season_id => $season_data ):

			$team_id = sz_array_value( $leagues, $season_id, -1 );

			if ( -1 == $team_id )
				continue;

			$season_name = sz_array_value( $season_names, $season_id, '&nbsp;' );

			if ( $team_id ):
				$team_name = sz_get_team_name( $team_id, $abbreviate_teams );
				
				if ( get_option( 'sportspress_link_teams', 'no' ) == 'yes' ? true : false ):
					$team_permalink = get_permalink( $team_id );
					$team_name = '<a href="' . $team_permalink . '">' . $team_name . '</a>';
				endif;
			else:
				$team_name = __( 'Total', 'sportspress' );
			endif;

			// Add season name to row
			$merged[ $season_id ] = array(
				'name' => $season_name,
				'team' => $team_name
			);

			foreach( $season_data as $key => $value ):

				// Use static data if key exists and value is not empty, else use placeholder
				if ( array_key_exists( $season_id, $data ) && array_key_exists( $key, $data[ $season_id ] ) && $data[ $season_id ][ $key ] != '' ):
					$value = $data[ $season_id ][ $key ];
				endif;
				
				$merged[ $season_id ][ $key ] = $value;

			endforeach;

		endforeach;

		$columns = array_merge( $performance_labels, $stats );

		$formats = array();
		$total_types = array();

		$args = array(
			'post_type' => array( 'sz_performance', 'sz_statistic' ),
			'numberposts' => 100,
			'posts_per_page' => 100,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		);

		$posts = get_posts( $args );

		if ( $posts ) {
			$column_order = array();
			$usecolumn_order = array();
			foreach ( $posts as $post ) {
				if ( array_key_exists( $post->post_name, $columns ) ) {
					$column_order[ $post->post_name ] = $columns[ $post->post_name ];
				}
				if ( in_array( $post->post_name, $usecolumns ) ) {
					$usecolumn_order[] = $post->post_name;
				}

				$format = get_post_meta( $post->ID, 'sz_format', true );
				if ( '' === $format ) {
					$format = 'number';
				}
				$formats[ $post->post_name ] = $format;

				$total_type = get_post_meta( $post->ID, 'sz_type', true );
				if ( '' === $total_type ) {
					$total_type = 'total';
				}
				$total_types[ $post->post_name ] = $total_type;
			}
			$columns = array_merge( $column_order, $columns );
			$usecolumns = array_merge( $usecolumn_order, $usecolumns );
		}

		// Calculate total statistics
		$career = array(
			'name' => __( 'Total', 'sportspress' ),
			'team' => '-',
		);

		// Add values from all seasons for total-based statistics
		foreach ( $merged as $season => $stats ):
			if ( ! is_array( $stats ) ) continue;
			foreach ( $stats as $key => $value ):
				if ( in_array( $key, array( 'name', 'team' ) ) ) continue;
				$value = floatval( $value );
				$career[ $key ] = sz_array_value( $career, $key, 0 ) + $value;
			endforeach;
		endforeach;

		// Calculate average-based statistics from performance
		foreach ( $posts as $post ) {
			$type = get_post_meta( $post->ID, 'sz_type', 'total' );
			if ( 'average' !== $type ) continue;
			$value = sz_array_value( $equations, $post->post_name, null );
			if ( null === $value || ! isset( $value['equation'] ) ) continue;
			$precision = sz_array_value( $value, 'precision', 0 );
			$career[ $post->post_name ] = sz_solve( $value['equation'], $totals, $precision );
		}

		// Get manually entered career totals
		$manual_career = sz_array_value( $data, 0, array() );
		$manual_career = array_filter( $manual_career, 'sz_filter_non_empty' );

		// Add career totals to merged array
		$merged[-1] = array_merge( $career, $manual_career );

		if ( $admin ):
			$labels = array();
			if ( is_array( $usecolumns ) ): foreach ( $usecolumns as $key ):
				if ( $key == 'team' ):
					$labels[ $key ] = __( 'Team', 'sportspress' );
				elseif ( array_key_exists( $key, $columns ) ):
					$labels[ $key ] = $columns[ $key ];
				endif;
			endforeach; endif;
			$placeholders[0] = $merged[-1];
			return array( $labels, $data, $placeholders, $merged, $leagues, $has_checkboxes, $formats, $total_types );
		else:
			if ( is_array( $usecolumns ) ):
				foreach ( $columns as $key => $label ):
					if ( ! in_array( $key, $usecolumns ) ):
						unset( $columns[ $key ] );
					endif;
				endforeach;
			endif;
			
			$labels = array();
			
			if ( 'no' === get_option( 'sportspress_player_show_statistics', 'yes' ) ) {
				$merged = array();
			} else {
				$labels['name'] = __( 'Season', 'sportspress' );
				$labels['team'] = __( 'Team', 'sportspress' );
			}

			if ( 'no' === get_option( 'sportspress_player_show_total', 'no' ) ) {
				unset( $merged[-1] );
			}

			// Convert to time notation
			if ( in_array( 'time', $formats ) ):
				foreach ( $merged as $season => $season_performance ):
					foreach ( $season_performance as $performance_key => $performance_value ):

						// Continue if not time format
						if ( 'time' !== sz_array_value( $formats, $performance_key ) ) continue;

						$merged[ $season ][ $performance_key ] = sz_time_value( $performance_value );

					endforeach;
				endforeach;
			endif;
			
			$merged[0] = array_merge( $labels, $columns );

			return $merged;
		endif;
	}

	/**
	 * Returns formatted data for all leagues
	 *
	 * @access public
	 * @param int $league_id
	 * @param bool $admin
	 * @return array
	 */
	public function statistics() {
		$terms = get_the_terms( $this->ID, 'sz_league' );
		
		$statistics = array();
		
		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$statistics[ $term->term_id ] = $this->data( $term->term_id );
			}
		}
		
		return $statistics;
	}

}
