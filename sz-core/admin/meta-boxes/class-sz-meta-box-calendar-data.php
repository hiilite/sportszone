<?php
/**
 * Calendar Events
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version		2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Calendar_Data
 */
class SZ_Meta_Box_Calendar_Data {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$calendar = new SZ_Calendar( $post );
		$data = $calendar->data();
		$usecolumns = $calendar->columns;
		self::table( $data, $usecolumns );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_columns', sz_array_value( $_POST, 'sz_columns', array() ) );
	}

	/**
	 * Admin edit table
	 */
	public static function table( $data = array(), $usecolumns = null ) {
		$title_format = get_option( 'sportszone_event_list_title_format', 'title' );
		$time_format = get_option( 'sportszone_event_list_time_format', 'combined' );

		if ( is_array( $usecolumns ) )
			$usecolumns = array_filter( $usecolumns );
		?>
		<div class="sz-data-table-container">
			<table class="widefat sz-data-table sz-calendar-table">
				<thead>
					<tr>
						<th class="column-date">
							<?php _e( 'Date', 'sportszone' ); ?>
						</th>
						<?php if ( is_array( $usecolumns ) && in_array( 'event', $usecolumns ) ) { ?>
						<th class="column-event">
							<label for="sz_columns_event">
								<?php
								if ( 'teams' == $title_format ) {
									_e( 'Home', 'sportszone' ); ?> | <?php _e( 'Away', 'sportszone' );
								} elseif ( 'homeaway' == $title_format ) {
									_e( 'Teams', 'sportszone' );
								} else {
									_e( 'Title', 'sportszone' );
								}
								?>
							</label>
						</th>
						<?php } ?>
						<?php if ( ( is_array( $usecolumns ) && in_array( 'time', $usecolumns ) ) && in_array( $time_format, array( 'combined', 'separate', 'time' ) ) ) { ?>
							<th class="column-time">
								<label for="sz_columns_time">
									<?php
									if ( 'time' == $time_format || 'separate' == $time_format ) {
										_e( 'Time', 'sportszone' );
									} else {
										_e( 'Time/Results', 'sportszone' );
									}
									?>
								</label>
							</th>
						<?php } ?>
						<?php if ( ( is_array( $usecolumns ) && in_array( 'results', $usecolumns ) ) && in_array( $time_format, array( 'separate', 'results' ) ) ) { ?>
							<th class="column-results">
								<label for="sz_columns_results">
									<?php _e( 'Results', 'sportszone' ); ?>
								</label>
							</th>
						<?php } ?>
						<?php if ( is_array( $usecolumns ) && in_array( 'league', $usecolumns ) ) { ?>
							<th class="column-league">
								<label for="sz_columns_league">
									<?php _e( 'League', 'sportszone' ); ?>
								</label>
							</th>
						<?php } ?>
						<?php if ( is_array( $usecolumns ) && in_array( 'season', $usecolumns ) ) { ?>
							<th class="column-season">
								<label for="sz_columns_season">
									<?php _e( 'Season', 'sportszone' ); ?>
								</label>
							</th>
						<?php } ?>
						<?php if ( is_array( $usecolumns ) && in_array( 'venue', $usecolumns ) ) { ?>
							<th class="column-venue">
								<label for="sz_columns_venue">
									<?php _e( 'Venue', 'sportszone' ); ?>
								</label>
							</th>
						<?php } ?>
						<?php if ( is_array( $usecolumns ) && in_array( 'article', $usecolumns ) ) { ?>
							<th class="column-article">
								<label for="sz_columns_article">
									<?php _e( 'Article', 'sportszone' ); ?>
								</label>
							</th>
						<?php } ?>
						<?php if ( is_array( $usecolumns ) && in_array( 'day', $usecolumns ) ) { ?>
							<th class="column-day">
								<label for="sz_columns_day">
									<?php _e( 'Match Day', 'sportszone' ); ?>
								</label>
							</th>
						<?php } ?>
						<?php do_action( 'sportszone_calendar_data_meta_box_table_head_row', $usecolumns ); ?>
					</tr>
				</thead>
				<tbody>
					<?php
					if ( is_array( $data ) ):
						if ( sizeof( $data ) > 0 ):
							$main_result = get_option( 'sportszone_primary_result', null );
							$i = 0;
							foreach ( $data as $match ):
								$teams = get_post_meta( $match->ID, 'sz_team' );
								$results = get_post_meta( $match->ID, 'sz_results', true );
								$video = get_post_meta( $match->ID, 'sz_video', true );
								$main_results = array();
								?>
								<tr class="sz-row sz-post<?php if ( $i % 2 == 0 ) echo ' alternate'; ?>">
									<td><?php echo get_post_time( get_option( 'date_format' ), false, $match, true ); ?></td>
									<?php if ( is_array( $usecolumns ) && in_array( 'event', $usecolumns ) ) { ?>
										<td>
											<div class="sz-title-format sz-title-format-title<?php if ( $title_format && $title_format != 'title' ): ?> hidden<?php endif; ?>"><?php echo $match->post_title; ?></div>
											<div class="sz-title-format sz-title-format-teams sz-title-format-homeaway<?php if ( ! in_array( $title_format, array( 'teams', 'homeaway' ) ) ): ?> hidden<?php endif; ?>">
												<?php
												if ( $teams ): foreach ( $teams as $team ):
													$name = get_the_title( $team );
													if ( $name ):
														$team_results = sz_array_value( $results, $team, null );

														if ( $main_result ):
															$team_result = sz_array_value( $team_results, $main_result, null );
														else:
															if ( is_array( $team_results ) ):
																end( $team_results );
																$team_result = prev( $team_results );
															else:
																$team_result = null;
															endif;
														endif;

														if ( $team_result != null ):
															$team_result = apply_filters( 'sportszone_calendar_team_result_admin', $team_result, $match->ID, $team );
															$main_results[] = $team_result;
															unset( $team_results['outcome'] );
															$team_results = implode( ' | ', $team_results );
															echo '<a class="result sz-tip" title="' . $team_results . '" href="' . get_edit_post_link( $match->ID ) . '">' . $team_result . '</a> ';
														endif;

														echo $name . '<br>';
													endif;
												endforeach; else:
													echo '&mdash;';
												endif;
												?>
											</div>
										</td>
									<?php } ?>
									<?php if ( ( is_array( $usecolumns ) && in_array( 'time', $usecolumns ) ) && in_array( $time_format, array( 'combined', 'separate', 'time' ) ) ) { ?>
										<?php if ( 'time' == $time_format || 'separate' == $time_format ) { ?>
											<td>
												<?php echo apply_filters( 'sportszone_event_time_admin', get_post_time( get_option( 'time_format' ), false, $match, true ), $match->ID ); ?>
											</td>
										<?php } else { ?>
											<td>
												<?php
													if ( ! empty( $main_results ) ):
														echo implode( ' - ', $main_results );
													else:
														echo apply_filters( 'sportszone_event_time_admin', get_post_time( get_option( 'time_format' ), false, $match, true ), $match->ID );
													endif;
												?>
											</td>
										<?php } ?>
									<?php } ?>
									<?php if ( ( is_array( $usecolumns ) && in_array( 'results', $usecolumns ) ) && in_array( $time_format, array( 'separate', 'results' ) ) ) { ?>
										<td>
											<?php
												if ( ! empty( $main_results ) ):
													echo implode( ' - ', $main_results );
												else:
													echo '-';
												endif;
											?>
										</td>
									<?php } ?>
									<?php if ( is_array( $usecolumns ) && in_array( 'league', $usecolumns ) ) { ?>
										<td><?php the_terms( $match->ID, 'sz_league' ); ?></td>
									<?php } ?>
									<?php if ( is_array( $usecolumns ) && in_array( 'season', $usecolumns ) ) { ?>
										<td><?php the_terms( $match->ID, 'sz_season' ); ?></td>
									<?php } ?>
									<?php if ( is_array( $usecolumns ) && in_array( 'venue', $usecolumns ) ) { ?>
										<td><?php the_terms( $match->ID, 'sz_venue' ); ?></td>
									<?php } ?>
									<?php if ( is_array( $usecolumns ) && in_array( 'article', $usecolumns ) ) { ?>
										<td>
											<a href="<?php echo get_edit_post_link( $match->ID ); ?>#sz_articlediv">
												<?php if ( $video ): ?>
													<div class="dashicons dashicons-video-alt"></div>
												<?php elseif ( has_post_thumbnail( $match->ID ) ): ?>
													<div class="dashicons dashicons-camera"></div>
												<?php endif; ?>
												<?php
												if ( $match->post_content == null ):
													_e( 'None', 'sportszone' );
												elseif ( $match->post_status == 'publish' ):
													_e( 'Recap', 'sportszone' );
												else:
													_e( 'Preview', 'sportszone' );
												endif;
												?>
											</a>
										</td>
									<?php } ?>
									<?php if ( is_array( $usecolumns ) && in_array( 'day', $usecolumns ) ) { ?>
										<td>
											<?php
											$day = get_post_meta( $match->ID, 'sz_day', true );
											if ( '' == $day ) {
												echo '&mdash;';
											} else {
												echo $day;
											}
											?>
										</td>
									<?php } ?>
									<?php do_action( 'sportszone_calendar_data_meta_box_table_row', $match, $usecolumns ); ?>
								</tr>
								<?php
								$i++;
							endforeach;
						else:
							?>
							<tr class="sz-row alternate">
								<td colspan="<?php echo sizeof( $usecolumns ); ?>">
									<?php _e( 'No results found.', 'sportszone' ); ?>
								</td>
							</tr>
							<?php
						endif;
					else:
					?>
					<tr class="sz-row alternate">
						<td colspan="<?php echo sizeof( $usecolumns ); ?>">
							<?php printf( __( 'Select %s', 'sportszone' ), __( 'Details', 'sportszone' ) ); ?>
						</td>
					</tr>
					<?php
					endif;
					?>
				</tbody>
			</table>
		</div>
		<?php
	}
}