<?php
/**
 * Event Performance
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version		2.5.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Match_Performance
 */
class SZ_Meta_Box_Match_Performance {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$match = new SZ_Match( $post );
		list( $labels, $columns, $stats, $teams, $formats, $order, $timed, $stars ) = $match->performance( true );

		if ( 'yes' == get_option( 'sportszone_event_performance_show_minutes', 'no' ) )
			$timeline = $match->timeline( true );
		else
			$timeline = false;

		// Determine if columns are auto or manual
		if ( 'manual' == get_option( 'sportszone_event_performance_columns', 'auto' ) )
			$manual = true;
		else
			$manual = false;

		// Determine if we need checkboxes
		if ( $manual )
			$has_checkboxes = true;
		else
			$has_checkboxes = false;
		
		// Get player number option
		$numbers = 'yes' == get_option( 'sportszone_event_show_player_numbers', 'yes' ) ? true : false;

		// Get positions
		$positions = array();
		if ( 'yes' == get_option( 'sportszone_event_show_position', 'yes' ) && taxonomy_exists( 'sz_position' ) ):
			$args = array(
				'hide_empty' => false,
				'parent' => 0,
				'include_children' => true,
			);
			$positions = get_terms( 'sz_position', $args );
		endif;

		// Apply filters to labels
		$labels = apply_filters( 'sportszone_event_performance_labels_admin', $labels );
		
		// Check if individual mode
		$is_individual = 'player' === sz_get_post_mode( $post->ID );

		// Get status option
		$status = ! $is_individual;

		self::tables( $post->ID, $stats, $labels, $columns, $teams, $has_checkboxes, $positions, $status, $formats, $order, $numbers, $is_individual, $timeline, $timed, $stars );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_players', sz_array_value( $_POST, 'sz_players', array() ) );
		update_post_meta( $post_id, 'sz_order', sz_array_value( $_POST, 'sz_order', array() ) );
		update_post_meta( $post_id, 'sz_timeline', sz_array_value( $_POST, 'sz_timeline', array() ) );
		update_post_meta( $post_id, 'sz_stars', sz_array_value( $_POST, 'sz_stars', array() ) );

		if ( isset( $_POST['sz_columns'] ) ) {
			$columns = array_filter( (array) $_POST['sz_columns'] );
			update_post_meta( $post_id, 'sz_columns', $columns );
		}
	}

	/**
	 * Admin edit tables
	 */
	public static function tables( $post_id, $stats = array(), $labels = array(), $columns = array(), $teams = array(), $has_checkboxes = false, $positions = array(), $status = true, $formats = array(), $order = array(), $numbers = true, $is_individual = false, $timeline = array(), $timed = array(), $stars = array() ) {
		$sections = get_option( 'sportszone_event_performance_sections', -1 );
		global $pagenow;
		
		
		if ( $pagenow === 'post-new.php' || $is_individual ) {
			?>
			<div class="sz-data-table-container">
				<table class="widefat sz-data-table sz-performance-table sz-sortable-table">
					<?php self::header( $columns, $labels, $positions, $has_checkboxes, $status, false, $numbers, -1, $formats ); ?>
					<?php self::footer( sz_array_value( $stats, -1 ), $labels, 0, $positions, $status, false, $numbers, -1, $formats ); ?>
					<tbody>
						<?php
						foreach ( $teams as $key => $team_id ):
							if ( -1 == $team_id ) continue;

							// Get results for players in the team
							$players = sz_array_between( (array)get_post_meta( $post_id, 'sz_player', false ), 0, $key );
							$players[] = -1;
							$data = sz_array_combine( $players, sz_array_value( $stats, $team_id, array() ) );

							// Get team timeline
							if ( is_array( $timeline ) ):
								$team_timeline = (array) sz_array_value( $timeline, $team_id, array() );
							else:
								$team_timeline = false;
							endif;

							foreach ( $data as $player_id => $player_performance ):

								// Get player timeline
								if ( is_array( $team_timeline ) ):
									$player_timeline = (array) sz_array_value( $team_timeline, $player_id, array() );
								else:
									$player_timeline = false;
								endif;

								self::row( $labels, $player_id, $player_performance, $team_id, $data, ! empty( $positions ), $status, false, $numbers, -1, $formats, $player_timeline, $timed, $stars );
							endforeach;
						endforeach;
						?>
					</tbody>
				</table>
			</div>
			<?php
		} else {
			$i = 0;
		
			foreach ( $teams as $key => $team_id ):
				if ( -1 == $team_id ) continue;
				
				if ( -1 == $sections ) {
					$group = groups_get_group( array( 'group_id' => $team_id) );
					// Get results for players in the team
					$players = sz_array_between( (array)get_post_meta( $post_id, 'sz_player', false ), 0, $key );
					$players[] = -1;
					$data = sz_array_combine( $players, sz_array_value( $stats, $team_id, array() ) );

					// Get team timeline
					if ( is_array( $timeline ) ):
						$team_timeline = (array) sz_array_value( $timeline, $team_id, array() );
					else:
						$team_timeline = false;
					endif;
					?>
					<div>
						<p>
							<strong><?php echo $group->name; ?></strong>
							<a class="add-new-h2 sz-add-new-h2" href="<?php echo esc_url( admin_url( add_query_arg( array( 'import' => 'sz_match_performance_csv', 'event' => $post_id, 'team' => $team_id, 'teams' => sizeof( $teams ), 'index' => $key ), 'admin.php' ) ) ); ?>"><?php _e( 'Import', 'sportszone' ); ?></a>
						</p>
						<?php self::table( $labels, $columns, $data, $team_id, $has_checkboxes && $i === 0, $positions, $status, -1, $formats, $order, $numbers, $team_timeline, $timed, $stars ); ?>
						<?php do_action( 'sportszone_after_event_performance_table_admin', $labels, $columns, $data, $team_id ); ?>
					</div>
				<?php } else { ?>
					<?php
					// Get labels by section
					$args = array(
						'post_type' => 'sz_performance',
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
								'value' => 'equation',
								'compare' => '!=',
							),
						),
					);

					$performances = get_posts( $args );

					$labels = array( array(), array() );
					foreach ( $performances as $performance ):
						$section = get_post_meta( $performance->ID, 'sz_section', true );
						if ( '' === $section ) {
							$section = -1;
						}
						switch ( $section ):
							case 1:
								$labels[1][ $performance->post_name ] = $performance->post_title;
								break;
							default:
								$labels[0][ $performance->post_name ] = $performance->post_title;
						endswitch;
					endforeach;
					
					$offense = (array)get_post_meta( $post_id, 'sz_offense', false );
					$defense = (array)get_post_meta( $post_id, 'sz_defense', false );
					$data = array();
					if ( sizeof( $offense ) || sizeof( $defense ) ) {
						// Get results for offensive players in the team
						$offense = sz_array_between( $offense, 0, $key );
						$offense[] = -1;
						$data[0] = sz_array_combine( $offense, sz_array_value( $stats, $team_id, array() ) );
						
						// Get results for defensive players in the team
						$defense = sz_array_between( $defense, 0, $key );
						$defense[] = -1;
						$data[1] = sz_array_combine( $defense, sz_array_value( $stats, $team_id, array() ) );
					} else {
						// Get results for all players in the team
						$players = sz_array_between( (array)get_post_meta( $post_id, 'sz_player', false ), 0, $key );
						$players[] = -1;
						$data[0] = $data[1] = sz_array_combine( $players, sz_array_value( $stats, $team_id, array() ) );
					}

					// Get team timeline
					if ( is_array( $timeline ) ):
						$team_timeline = (array) sz_array_value( $timeline, $team_id, array() );
					else:
						$team_timeline = false;
					endif;
			
					// Determine order of sections
					if ( 1 == $sections ) {
						$section_order = array( 1 => __( 'Defense', 'sportszone' ), 0 => __( 'Offense', 'sportszone' ) );
					} else {
						$section_order = array( __( 'Offense', 'sportszone' ), __( 'Defense', 'sportszone' ) );
					}
					
					foreach ( $section_order as $section_id => $section_label ) {
						?>
						<div>
							<p>
								<strong><?php echo get_the_title( $team_id ); ?> &mdash; <?php echo $section_label; ?></strong>
								<a class="add-new-h2 sz-add-new-h2" href="<?php echo esc_url( admin_url( add_query_arg( array( 'import' => 'sz_match_performance_csv', 'event' => $post_id, 'team' => $team_id ), 'admin.php' ) ) ); ?>"><?php _e( 'Import', 'sportszone' ); ?></a>
							</p>
							<?php self::table( $labels[ $section_id ], $columns, $data[ $section_id ], $team_id, ( $has_checkboxes && 0 === $i ), $positions, $status, $section_id, $formats, $order, $numbers, $team_timeline, $timed, $stars ); ?>
							<?php do_action( 'sportszone_after_event_performance_table_admin', $labels[ $section_id ], $columns, $data[ $section_id ], $team_id ); ?>
						</div>
						<?php
					}
				}
				$i ++;
			endforeach;
		}
	}

	/**
	 * Admin edit table
	 */
	public static function table( $labels = array(), $columns = array(), $data = array(), $team_id, $has_checkboxes = false, $positions = array(), $status = true, $section = -1, $formats = array(), $order = array(), $numbers = true, $team_timeline = array(), $timed = array(), $stars = array() ) {
		?>
		<div class="sz-data-table-container">
			<table class="widefat sz-data-table sz-performance-table sz-sortable-table">
				<?php self::header( $columns, $labels, $positions, $has_checkboxes, $status, true, $numbers, $section, $formats ); ?>
				<?php self::footer( $data, $labels, $team_id, $positions, $status, true, $numbers, $section, $formats ); ?>
				<tbody>
					<?php
					if ( 1 == $section && is_array( $order ) && sizeof( $order ) ) {
						$players = array();
						$player_order = sz_array_value( $order, $team_id, array() );
						if ( is_array( $player_order ) && sizeof( $player_order ) ) {
							foreach ( $player_order as $key ) {
								if ( array_key_exists( $key, $data ) ):
									$players[ $key ] = $data[ $key ];
								endif;
							}
						}
						foreach ( $data as $key => $player ) {
							if ( ! array_key_exists( $key, $players ) ) {
								$players[ $key ] = $player;
							}
						}
						$data = $players;
					}

					foreach ( $data as $player_id => $player_performance ):

						// Get player timeline
						if ( is_array( $team_timeline ) ):
							$player_timeline = (array) sz_array_value( $team_timeline, $player_id, array() );
						else:
							$player_timeline = false;
						endif;

						self::row( $labels, $player_id, $player_performance, $team_id, $data, ! empty( $positions ), $status, true, $numbers, $section, $formats, $player_timeline, $timed, $stars );

					endforeach;
					?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Admin edit table header
	 */
	public static function header( $columns = array(), $labels = array(), $positions = array(), $has_checkboxes = false, $status = true, $sortable = true, $numbers = true, $section = -1, $formats = array() ) {
		$stars_type = get_option( 'sportszone_event_performance_stars_type', 0 );
		?>
		<thead>
			<tr>
				<?php if ( $sortable ) { ?>
					<th class="icon">&nbsp;</th>
				<?php } ?>
				<?php if ( apply_filters( 'sportszone_event_performance_show_numbers', $numbers, $section ) ) { ?>
					<th>#</th>
				<?php } ?>
				<th><?php _e( 'Player', 'sportszone' ); ?></th>
				<?php if ( ! empty( $positions ) ) { ?>
					<th class="column-position">
						<?php _e( 'Position', 'sportszone' ); ?>
					</th>
				<?php } ?>
				<?php $i = 0; foreach ( $labels as $key => $label ): ?>
					<?php if ( 'equation' === sz_array_value( $formats, $key, 'number' ) ) continue; ?>
					<th>
						<?php if ( $has_checkboxes ): ?>
							<?php if ( 0 == $i ): ?>
								<input type="hidden" name="sz_columns[]" value="">
							<?php endif; ?>
							<label for="sz_columns_<?php echo $key; ?>">
								<input type="checkbox" name="sz_columns[]" value="<?php echo $key; ?>" id="sz_columns_<?php echo $key; ?>" <?php checked( ! is_array( $columns ) || in_array( $key, $columns ) ); ?>>
								<?php echo $label; ?>
							</label>
						<?php else: ?>
							<?php echo $label; ?>
						<?php endif; ?>
					</th>
				<?php $i++; endforeach; ?>
				<?php if ( apply_filters( 'sportszone_event_performance_show_status', $status, $section ) ) { ?>
					<th>
						<?php _e( 'Status', 'sportszone' ); ?>
					</th>
				<?php } ?>
				<?php if ( $stars_type ) { ?>
					<th><i class="dashicons dashicons-star-filled" title="<?php 1 == $stars_type ? _e( 'Player of the Match', 'sportszone' ) : _e( 'Stars', 'sportszone' ); ?>"></i></th>
				<?php } ?>
			</tr>
		</thead>
		<?php
	}

	/**
	 * Admin edit table footer
	 */
	public static function footer( $data = array(), $labels = array(), $team_id = 0, $positions = array(), $status = true, $sortable = true, $numbers = true, $section = -1, $formats = array() ) {
		?>
		<tfoot>
			<?php do_action( 'sportszone_event_performance_meta_box_table_footer', $data, $labels, $team_id, $positions, $status, $sortable, $numbers, $section ); ?>
			<?php if ( $team_id ) { ?>
				<tr class="sz-row sz-total">
					<?php if ( $sortable ) { ?>
						<td>&nbsp;</td>
					<?php } ?>
					<?php if ( apply_filters( 'sportszone_event_performance_show_numbers', $numbers, $section ) ) { ?>
						<td>&nbsp;</td>
					<?php } ?>
					<td><strong><?php _e( 'Total', 'sportszone' ); ?></strong></td>
					<?php if ( ! empty( $positions ) ) { ?>
						<td>&nbsp;</td>
					<?php } ?>
					<?php foreach( $labels as $column => $label ):
						if ( 'equation' === sz_array_value( $formats, $column, 'number' ) ) continue;
						$player_id = 0;
						$player_performance = sz_array_value( $data, $player_id, array() );
						$value = sz_array_value( $player_performance, $column, '' );
						$format = sz_array_value( $formats, $column, 'number' );
						$placeholder = sz_get_format_placeholder( $format );
						?>
						<td><input type="text" name="sz_players[<?php echo $team_id; ?>][<?php echo $player_id; ?>][<?php echo $column; ?>]" placeholder="<?php echo trim( $placeholder ); ?>" value="<?php echo esc_attr( $value ); ?>" data-sz-format="<?php echo $format; ?>" /></td>
					<?php endforeach; ?>
					<?php if ( apply_filters( 'sportszone_event_performance_show_status', $status, $section ) ) { ?>
						<td>&nbsp;</td>
					<?php } ?>
					<?php if ( get_option( 'sportszone_event_performance_stars_type', 0 ) ) { ?>
						<td>&nbsp;</td>
					<?php } ?>
				</tr>
			<?php } ?>
		</tfoot>
		<?php
	}

	/**
	 * Admin edit table row
	 */
	public static function row( $labels = array(), $player_id = 0, $player_performance = array(), $team_id = 0, $data = array(), $positions = true, $status = true, $sortable = true, $numbers = true, $section = -1, $formats = array(), $player_timeline = array(), $timed = array(), $stars = array() ) {
		if ( $player_id <= 0 ) return;

		$stars_type = get_option( 'sportszone_event_performance_stars_type', 0 );
		$value = sz_array_value( $player_performance, 'number', '' );
		?>
		<tr class="sz-row sz-post" data-player="<?php echo $player_id; ?>">
			<?php if ( $sortable ) { ?>
				<td class="icon"><span class="dashicons dashicons-menu post-state-format"></span></td>
			<?php } ?>
			<?php if ( apply_filters( 'sportszone_event_performance_show_numbers', $numbers, $section ) ) { ?>
				<td>
					<input class="small-text sz-player-number-input sz-sync-input" type="text" name="sz_players[<?php echo $team_id; ?>][<?php echo $player_id; ?>][number]" value="<?php echo esc_attr( $value ); ?>" />
				</td>
			<?php } ?>
			<td>
				<?php echo apply_filters( 'sportszone_event_performance_player_selection', sz_get_player_name_then_number( $player_id ), $player_id ); ?>
				<?php if ( 1 == $section ) { ?>
					<input type="hidden" name="sz_order[<?php echo $team_id; ?>][]" value="<?php echo $player_id; ?>">
				<?php } ?>
			</td>
			<?php if ( $positions ) { ?>
				<td>
					<?php
					$selected = (array) sz_array_value( $player_performance, 'position', null );
					if ( $selected == null ):
						$selected = (array) sz_get_the_term_id( $player_id, 'sz_position', 0 );
					endif;
					$args = array(
						'taxonomy' => 'sz_position',
						'name' => 'sz_players[' . $team_id . '][' . $player_id . '][position][]',
						'values' => 'term_id',
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
						'selected' => $selected,
						'class' => 'sz-position',
						'property' => 'multiple',
						'chosen' => true,
						'include_children' => ( 'no' == get_option( 'sportszone_event_hide_child_positions', 'no' ) ),
					);
					sz_dropdown_taxonomies( $args );
					?>
				</td>
			<?php } ?>
			<?php foreach( $labels as $column => $label ):
				if ( 'equation' === sz_array_value( $formats, $column, 'number' ) ) continue;
				$value = sz_array_value( $player_performance, $column, '' );
				$intval = intval( $value );
				$placeholder = sz_get_format_placeholder( sz_array_value( $formats, $column, 'number' ) );
				?>
				<td>
					<?php if ( 'time' === sz_array_value( $formats, $column, 'number' ) ) { ?>
						<?php
							$intval = intval( $value );
							$timeval = gmdate( 'i:s', $intval );
							$hours = floor( $intval / 3600 );

							if ( '00' != $hours )
								$timeval = $hours . ':' . $timeval;

							$timeval = preg_replace( '/^0/', '', $timeval );
						?>

						<input class="sz-player-<?php echo $column; ?>-input sz-convert-time-input sz-sync-input" type="text" name="sz_times[<?php echo $team_id; ?>][<?php echo $player_id; ?>][<?php echo $column; ?>]" value="<?php echo esc_attr( $timeval ); ?>" placeholder="<?php echo $placeholder; ?>" />
						<input class="sz-convert-time-output" type="hidden" name="sz_players[<?php echo $team_id; ?>][<?php echo $player_id; ?>][<?php echo $column; ?>]" value="<?php echo esc_attr( $value ); ?>" />
					<?php } else { ?>
						<input class="sz-player-<?php echo $column; ?>-input sz-sync-input" type="text" name="sz_players[<?php echo $team_id; ?>][<?php echo $player_id; ?>][<?php echo $column; ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo $placeholder; ?>" />
					<?php } ?>

					<?php if ( $intval && in_array( $column, $timed ) ) { ?>
						<?php
						// Get performance times
						if ( is_array( $player_timeline ) ) {
							$times = sz_array_value( $player_timeline, $column, array() );
						} else {
							$times = false;
						}
						
						if ( is_array( $times ) ) {
							?>
							<hr>
							<?php for ( $i = 0; $i < $intval; $i++ ) { ?><input class="sz-sync-input small-text" type="text" name="sz_timeline[<?php echo $team_id; ?>][<?php echo $player_id; ?>][<?php echo $column; ?>][<?php echo $i; ?>]" value="<?php echo esc_attr( sz_array_value( $times, $i, '' ) ); ?>" placeholder="-" /><?php } ?>
							<span class="description"><?php _e( 'mins', 'sportszone' ); ?></span>
							<?php
						}
					}
					?>
				</td>
			<?php endforeach; ?>
			<?php if ( apply_filters( 'sportszone_event_performance_show_status', $status, $section ) ) { ?>
				<td class="sz-status-selector">
					<?php
					// Get substitute times
					if ( is_array( $player_timeline ) ) {
						$times = sz_array_value( $player_timeline, 'sub', array() );
					} else {
						$times = false;
					}
					?>
					<?php echo self::status_select( $team_id, $player_id, sz_array_value( $player_performance, 'status', null ) ); ?><br>
					<?php echo self::sub_select( $team_id, $player_id, sz_array_value( $player_performance, 'sub', null ), $data ); ?><br>
					<input class="sz-sync-input small-text" type="text" name="sz_timeline[<?php echo $team_id; ?>][<?php echo $player_id; ?>][sub][]" value="<?php echo esc_attr( sz_array_value( $times, 0, '' ) ); ?>" placeholder="-" />
					<span class="description"><?php _e( 'mins', 'sportszone' ); ?></span>
				</td>
			<?php } ?>
			<?php if ( $stars_type ) { ?>
				<td>
					<?php
					switch ( $stars_type ) {
						case 1:
							echo '<input type="checkbox" name="sz_stars[' . $player_id . ']" value="1" ' . checked( sz_array_value( $stars, $player_id, '' ) == '', false, false ) . '>';
							break;
						default:
							echo '<input type="text" name="sz_stars[' . $player_id . ']" class="tiny-text sz-player-stars-input sz-sync-input" value="' . sz_array_value( $stars, $player_id, '' ) . '">';
					}
					?>
				</td>
			<?php } ?>
		</tr>
		<?php
	}

	/**
	 * Status selector
	 */
	public static function status_select( $team_id, $player_id, $value = null ) {

		if ( ! $team_id || ! $player_id )
			return '&mdash;';

		$options = apply_filters( 'sportszone_event_performance_status_options', array(
			'lineup' => __( 'Starting Lineup', 'sportszone' ),
			'sub' => __( 'Substitute', 'sportszone' ),
		) );

		$output = '<select class="sz-sync-select" name="sz_players[' . $team_id . '][' . $player_id . '][status]">';

		foreach( $options as $key => $name ):
			$output .= '<option value="' . $key . '"' . ( $key == $value ? ' selected' : '' ) . '>' . $name . '</option>';
		endforeach;

		$output .= '</select>';

		return $output;

	}

	/**
	 * Substitute selector
	 */
	public static function sub_select( $team_id, $player_id, $value, $data = array() ) {

		if ( ! $team_id || ! $player_id )
			return '&mdash;';

		$output = '<select class="sz-sync-select" name="sz_players[' . $team_id . '][' . $player_id . '][sub]" style="display: none;">';

		$output .= '<option value="0">' . __( 'None', 'sportszone' ) . '</option>';

		// Add players as selectable options
		foreach( $data as $id => $performance ):
			if ( ! $id || $id == $player_id ) continue;
			$number = get_post_meta( $id, 'sz_number', true );
			$output .= '<option value="' . $id . '"' . ( $id == $value ? ' selected' : '' ) . '>' . ( $number ? $number . '. ' : '' ) . get_the_title( $id ) . '</option>';
		endforeach;

		$output .= '</select>';

		return $output;

	}
}
