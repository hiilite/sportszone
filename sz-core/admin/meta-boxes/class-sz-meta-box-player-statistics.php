<?php
/**
 * Player Statistics
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version   2.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Player_Statistics
 */
class SZ_Meta_Box_Player_Statistics {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$player = new SZ_Player( $post );
		$leagues = get_the_terms( $post->ID, 'sz_league' );
		$league_num = sizeof( $leagues );
		$sections = get_option( 'sportszone_player_performance_sections', -1 );
		$show_career_totals = 'yes' === get_option( 'sportszone_player_show_career_total', 'no' ) ? true : false;

		if ( $leagues ) {
			if ( -1 == $sections ) {
				// Loop through statistics for each league
				$i = 0;
				foreach ( $leagues as $league ):
					?>
					<p><strong><?php echo $league->name; ?></strong></p>
					<?php
					list( $columns, $data, $placeholders, $merged, $seasons_teams, $has_checkboxes, $formats, $total_types ) = $player->data( $league->term_id, true );
					self::table( $post->ID, $league->term_id, $columns, $data, $placeholders, $merged, $seasons_teams, $has_checkboxes && $i == 0, true, $formats, $total_types );
					$i ++;
				endforeach;
				if ( $show_career_totals ) {
					?>
					<p><strong><?php _e( 'Career Total', 'sportszone' ); ?></strong></p>
					<?php
					list( $columns, $data, $placeholders, $merged, $seasons_teams, $has_checkboxes, $formats, $total_types ) = $player->data( 0, true );
					self::table( $post->ID, 0, $columns, $data, $placeholders, $merged, $seasons_teams, false, false, $formats, $total_types );
				}
			} else {
				// Determine order of sections
				if ( 1 == $sections ) {
					$section_order = array( 1 => __( 'Defense', 'sportszone' ), 0 => __( 'Offense', 'sportszone' ) );
				} else {
					$section_order = array( __( 'Offense', 'sportszone' ), __( 'Defense', 'sportszone' ) );
				}
				
				$s = 0;
				foreach ( $section_order as $section_id => $section_label ) {
					// Loop through statistics for each league
					$i = 0;
					foreach ( $leagues as $league ):
						?>
						<p><strong><?php echo $league->name; ?> &mdash; <?php echo $section_label; ?></strong></p>
						<?php
						list( $columns, $data, $placeholders, $merged, $seasons_teams, $has_checkboxes, $formats, $total_types ) = $player->data( $league->term_id, true, $section_id );
						self::table( $post->ID, $league->term_id, $columns, $data, $placeholders, $merged, $seasons_teams, $has_checkboxes && $i == 0 && $s == 0, $s == 0, $formats, $total_types );
						$i ++;
					endforeach;
					if ( $show_career_totals ) {
						?>
						<p><strong><?php _e( 'Career Total', 'sportszone' ); ?> &mdash; <?php echo $section_label; ?></strong></p>
						<?php
						list( $columns, $data, $placeholders, $merged, $seasons_teams, $has_checkboxes, $formats, $total_types ) = $player->data( 0, true, $section_id );
						self::table( $post->ID, 0, $columns, $data, $placeholders, $merged, $seasons_teams, $has_checkboxes && $i == 0 && $s == 0, $s == 0, $formats, $total_types );
					}
					$s ++;
				}
			}
		}
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_leagues', sz_array_value( $_POST, 'sz_leagues', array() ) );
		update_post_meta( $post_id, 'sz_statistics', sz_array_value( $_POST, 'sz_statistics', array() ) );
	}

	/**
	 * Admin edit table
	 */
	public static function table( $id = null, $league_id, $columns = array(), $data = array(), $placeholders = array(), $merged = array(), $leagues = array(), $has_checkboxes = false, $team_select = false, $formats = array(), $total_types = array() ) {
		$readonly = false;
		$teams = array_filter( get_post_meta( $id, 'sz_team', false ) );
		?>
		<div class="sz-data-table-container">
			<table class="widefat sz-data-table">
				<thead>
					<tr>
						<th><?php _e( 'Season', 'sportszone' ); ?></th>
						<?php if ( $team_select && apply_filters( 'sportszone_player_team_statistics', $league_id ) ): ?>
							<th>
								<?php _e( 'Team', 'sportszone' ); ?>
							</th>
						<?php endif; ?>
						<?php foreach ( $columns as $key => $label ): if ( $key == 'team' ) continue; ?>
							<th><?php echo $label; ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tfoot>
					<?php $div_stats = sz_array_value( $data, 0, array() ); ?>
					<tr class="sz-row sz-total">
						<td>
							<label><strong><?php _e( 'Total', 'sportszone' ); ?></strong></label>
						</td>
						<?php if ( $team_select && apply_filters( 'sportszone_player_team_statistics', $league_id ) ) { ?>
							<td>&nbsp;</td>
						<?php } ?>
						<?php foreach ( $columns as $column => $label ): if ( $column == 'team' ) continue;
							?>
							<td><?php
								$value = sz_array_value( sz_array_value( $data, 0, array() ), $column, null );
								$placeholder = sz_array_value( sz_array_value( $placeholders, 0, array() ), $column, 0 );

								// Convert value and placeholder to time format
								if ( 'time' === sz_array_value( $formats, $column, 'number' ) ) {
									$timeval = sz_time_value( $value );
									$placeholder = sz_time_value( $placeholder );
								}

								if ( $readonly ) {
									echo $value ? $value : $placeholder;
								} else {
									if ( 'time' === sz_array_value( $formats, $column, 'number' ) ) {
										echo '<input class="sz-convert-time-input" type="text" name="sz_times[' . $league_id . '][0][' . $column . ']" value="' . ( '' === $value ? '' : esc_attr( $timeval ) ) . '" placeholder="' . esc_attr( $placeholder ) . '"' . ( $readonly ? ' disabled="disabled"' : '' ) . '  />';
										echo '<input class="sz-convert-time-output" type="hidden" name="sz_statistics[' . $league_id . '][0][' . $column . ']" value="' . esc_attr( $value ) . '" data-sz-format="' . sz_array_value( $formats, $column, 'number' ) . '" data-sz-total-type="' . sz_array_value( $total_types, $column, 'total' ) . '" />';
									} else {
										echo '<input type="text" name="sz_statistics[' . $league_id . '][0][' . $column . ']" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '"' . ( $readonly ? ' disabled="disabled"' : '' ) . ' data-sz-format="' . sz_array_value( $formats, $column, 'number' ) . '" data-sz-total-type="' . sz_array_value( $total_types, $column, 'total' ) . '" />';
									}
								}
							?></td>
						<?php endforeach; ?>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$i = 0;
					foreach ( $data as $div_id => $div_stats ):
						if ( $div_id === 'statistics' ) continue;
						if ( $div_id === 0 ) continue;
						$div = get_term( $div_id, 'sz_season' );
						?>
						<tr class="sz-row sz-post<?php if ( $i % 2 == 0 ) echo ' alternate'; ?>">
							<td>
								<label>
									<?php if ( ! apply_filters( 'sportszone_player_team_statistics', $league_id ) ): ?>
										<?php $value = sz_array_value( $leagues, $div_id, '-1' ); ?>
										<input type="hidden" name="sz_leagues[<?php echo $league_id; ?>][<?php echo $div_id; ?>]" value="-1">
										<input type="checkbox" name="sz_leagues[<?php echo $league_id; ?>][<?php echo $div_id; ?>]" value="1" <?php checked( $value ); ?>>
									<?php endif; ?>
									<?php
									if ( 0 === $div_id ) _e( 'Total', 'sportszone' );
									elseif ( 'WP_Error' != get_class( $div ) ) echo $div->name;
									?>
								</label>
							</td>
							<?php if ( $team_select && apply_filters( 'sportszone_player_team_statistics', $league_id ) ): ?>
								<?php if ( $div_id == 0 ): ?>
									<td>&nbsp;</td>
								<?php else: ?>
									<td>
										<?php $value = sz_array_value( $leagues, $div_id, '-1' ); ?>
										<?php
										$args = array(
											'post_type' => 'sz_team',
											'name' => 'sz_leagues[' . $league_id . '][' . $div_id . ']',
											'show_option_none' => __( '&mdash; None &mdash;', 'sportszone' ),
										    'sort_order'   => 'ASC',
										    'sort_column'  => 'menu_order',
											'selected' => $value,
											'values' => 'ID',
											'include' => $teams,
											'tax_query' => array(
												'relation' => 'AND',
												array(
													'taxonomy' => 'sz_league',
													'terms' => $league_id,
													'field' => 'term_id',
												),
												array(
													'taxonomy' => 'sz_season',
													'terms' => $div_id,
													'field' => 'term_id',
												),
											),
										);
										if ( ! sz_dropdown_pages( $args ) ):
											_e( '&mdash; None &mdash;', 'sportszone' );
										endif;
										?>
									</td>
								<?php endif; ?>
							<?php endif; ?>
							<?php foreach ( $columns as $column => $label ): if ( $column == 'team' ) continue;
								?>
								<td><?php
									$value = sz_array_value( sz_array_value( $data, $div_id, array() ), $column, null );
									$placeholder = sz_array_value( sz_array_value( $placeholders, $div_id, array() ), $column, 0 );

									// Convert value and placeholder to time format
									if ( 'time' === sz_array_value( $formats, $column, 'number' ) ) {
										$timeval = sz_time_value( $value );
										$placeholder = sz_time_value( $placeholder );
									}

									if ( $readonly ) {
										echo $timeval ? $timeval : $placeholder;
									} else {
										if ( 'time' === sz_array_value( $formats, $column, 'number' ) ) {
											echo '<input class="sz-convert-time-input" type="text" name="sz_times[' . $league_id . '][' . $div_id . '][' . $column . ']" value="' . ( '' === $value ? '' : esc_attr( $timeval ) ) . '" placeholder="' . esc_attr( $placeholder ) . '"' . ( $readonly ? ' disabled="disabled"' : '' ) . '  />';
											echo '<input class="sz-convert-time-output" type="hidden" name="sz_statistics[' . $league_id . '][' . $div_id . '][' . $column . ']" value="' . esc_attr( $value ) . '" />';
										} else {
											echo '<input type="text" name="sz_statistics[' . $league_id . '][' . $div_id . '][' . $column . ']" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '"' . ( $readonly ? ' disabled="disabled"' : '' ) . '  />';
										}
									}
								?></td>
							<?php endforeach; ?>
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