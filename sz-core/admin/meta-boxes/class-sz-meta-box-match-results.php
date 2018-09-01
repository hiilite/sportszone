<?php
/**
 * Event Results
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     2.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Match_Results
 */
class SZ_Meta_Box_Match_Results {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		// Determine if we need checkboxes
		if ( 'manual' == get_option( 'sportszone_event_result_columns', 'auto' ) )
			$has_checkboxes = true;
		else
			$has_checkboxes = false;

		$match = new SZ_Match( $post );
		list( $columns, $usecolumns, $data ) = $match->results( true );
		self::table( $columns, $usecolumns, $data, $has_checkboxes );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		$results = (array)sz_array_value( $_POST, 'sz_results', array() );
		$main_result = get_option( 'sportszone_primary_result', null );

		// Get player performance
		$performance = sz_array_value( $_POST, 'sz_players', array() );

		// Initialize finished
		$finished = false;

		// Check if any results are recorded
		foreach ( $results as $team => $team_results ) {
			foreach ( $team_results as $result ) {
				if ( '' !== $result ) {
					$finished = true;
					break;
				}
			}
		}

		// Check if any performance is recorded
		if ( ! $finished ) {
			foreach ( $performance as $team => $players ) {
				foreach ( $players as $player => $pp ) {
					if ( 0 >= $player ) continue;
					foreach ( $pp as $pk => $pv ) {
						if ( in_array( $pk, apply_filters( 'sportszone_event_auto_result_bypass_keys', array( 'number', 'status', 'sub' ) ) ) ) continue;

						if ( is_array( $pv ) ) continue;

						$pv = trim( $pv );
						if ( '' == $pv ) continue;
						if ( ! ctype_digit( $pv ) ) continue;

						$finished = true;
						break;
					}
				}
			}
		}
		
		if ( $finished ) {
			// Get results with equations
			$args = array(
				'post_type' => 'sz_result',
				'numberposts' => -1,
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
						'key' => 'sz_equation',
						'compare' => 'EXISTS',
					),
				),
			);
			$dynamic_results = get_posts( $args );

			$equations = array();
			$precision = array();
			foreach ( $dynamic_results as $result ) {
				$equations[ $result->post_name ] = get_post_meta( $result->ID, 'sz_equation', true );
				$precision[ $result->post_name ] = (int) get_post_meta( $result->ID, 'sz_precision', true );
			}


			// Apply equations to empty results
			foreach ( $equations as $key => $equation ) {
				if ( '' == $equation ) continue;
				foreach ( $results as $team => $team_results ) {
					 if ( '' === sz_array_value( $team_results, $key, '' ) ) {
					 	$totals = array();
						$players = sz_array_value( $performance, $team, array() );
						foreach ( $players as $player => $pp ) {
							foreach ( $pp as $pk => $pv ) {
								$value = sz_array_value( $totals, $pk, 0 );
								$value += floatval( $pv );
								$totals[ $pk ] = $value;
							}
						}
						$totals[ 'eventsplayed' ] = 1;
						$totals = apply_filters( 'sportszone_event_result_equation_vars', $totals, $performance, $team );
					 	$results[ $team ][ $key ] = sz_solve( $equation, $totals, sz_array_value( $precision, $key, 0 ), '' );
					 }
				}
			}
		}

		// Auto outcome
		$primary_results = array();
		foreach ( $results as $team => $team_results ) {
			if ( $main_result ) {
				$primary_results[ $team ] = sz_array_value( $team_results, $main_result, null );
			} else {
				if ( is_array( $team_results ) ) {
					end( $team_results );
					$primary_results[ $team ] = prev( $team_results );
				} else {
					$primary_results[ $team ] = null;
				}
			}
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
				foreach ( $results as $team => $team_results ) {
					if ( array_key_exists( 'outcome', $team_results ) ) continue;
					if ( $outcomes ) {
						$results[ $team ][ 'outcome' ] = array();
						foreach ( $outcomes as $outcome ) {
							$results[ $team ][ 'outcome' ][] = $outcome->post_name;
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
					if ( ! array_key_exists( 'outcome', $results[ $key ] ) ) {
						if ( $min == $value ) {
							$outcomes = $lt_outcomes;
						} elseif ( $max == $value ) {
							$outcomes = $gt_outcomes;
						} else {
							$outcomes = $default_outcomes;
						}
						$results[ $key ][ 'outcome' ] = array();
						foreach ( $outcomes as $outcome ) {
							$results[ $key ][ 'outcome' ][] = $outcome->post_name;
						}
					}
				}
			}
		}

		// Update meta
		update_post_meta( $post_id, 'sz_results', $results );
		update_post_meta( $post_id, 'sz_result_columns', sz_array_value( $_POST, 'sz_result_columns', array() ) );
	}

	/**
	 * Admin edit table
	 */
	public static function table( $columns = array(), $usecolumns = array(), $data = array(), $has_checkboxes = false ) {
		// Get results with equations
		$args = array(
			'post_type' => 'sz_result',
			'numberposts' => -1,
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'sz_equation',
					'compare' => 'NOT IN',
					'value' => null
				),
			),
		);
		$dynamic_results = get_posts( $args );
		$auto_columns = wp_list_pluck( $dynamic_results, 'post_name' );
		?>
		<div class="sz-data-table-container">
			<table class="widefat sz-data-table sz-results-table">
				<thead>
					<tr>
						<th class="column-team">
							<?php _e( 'Team', 'sportszone' ); ?>
						</th>
						<?php foreach ( $columns as $key => $label ): ?>
							<th class="column-<?php echo $key; ?>">
								<?php if ( $has_checkboxes ): ?>
									<label for="sz_result_columns_<?php echo $key; ?>">
										<input type="checkbox" name="sz_result_columns[]" value="<?php echo $key; ?>" id="sz_result_columns_<?php echo $key; ?>" <?php checked( ! is_array( $usecolumns ) || in_array( $key, $usecolumns ) ); ?>>
										<?php echo $label; ?>
									</label>
								<?php else: ?>
									<?php echo $label; ?>
								<?php endif; ?>
							</th>
						<?php endforeach; ?>
						<th class="column-outcome">
							<?php _e( 'Outcome', 'sportszone' ); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 0;
					foreach ( $data as $team_id => $team_results ):
						if ( ! $team_id || -1 == $team_id ) continue;
						
						$group = groups_get_group( array( 'group_id' => $team_id) );
						?>
						<tr class="sz-row sz-post<?php if ( $i % 2 == 0 ) echo ' alternate'; ?>" data-team="<?php echo $team_id; ?>">
							<td>
								<?php echo $group->name; ?>
							</td>
							<?php foreach( $columns as $column => $label ):
								$value = sz_array_value( $team_results, $column, '' );
								?>
								<td><input class="sz-team-<?php echo $column; ?>-input" type="text" name="sz_results[<?php echo $team_id; ?>][<?php echo $column; ?>]" value="<?php echo esc_attr( $value ); ?>"<?php if ( in_array( $column, $auto_columns ) ) { ?> placeholder="<?php _e( '(Auto)', 'sportszone' ); ?>"<?php } ?> /></td>
							<?php endforeach; ?>
							<td>
								<?php
								$values = sz_array_value( $team_results, 'outcome', '' );
								if ( ! is_array( $values ) )
									$values = array( $values );

								$args = array(
									'post_type' => 'sz_outcome',
									'name' => 'sz_results[' . $team_id . '][outcome][]',
									'option_none_value' => '',
								    'sort_order'   => 'ASC',
								    'sort_column'  => 'menu_order',
									'selected' => $values,
									'class' => 'sz-outcome',
									'property' => 'multiple',
									'chosen' => true,
									'placeholder' => __( '(Auto)', 'sportszone' ),
								);
								sz_dropdown_pages( $args );
								?>
							</td>
						</tr>
						<?php
						$i++;
					endforeach;
					?>
				</tbody>
			</table>
		</div>
		<?php
	}
}