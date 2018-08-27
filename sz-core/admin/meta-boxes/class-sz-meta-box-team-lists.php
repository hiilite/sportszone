<?php
/**
 * Team Player Lists
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version		2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Team_Lists
 */
class SZ_Meta_Box_Team_Lists {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $pagenow;

		if ( $pagenow != 'post-new.php' ):

			$team = new SZ_Team( $post );
			list( $data, $checked ) = $team->lists( true );
			self::table( $data, $checked );

		else:

			printf( __( 'No results found.', 'sportszone' ) );

		endif;
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		sz_update_post_meta_recursive( $post_id, 'sz_list', sz_array_value( $_POST, 'sz_list', array() ) );
	}

	/**
	 * Admin edit table
	 */
	public static function table( $data = array(), $checked = array() ) {
		?>
		<div class="sz-data-table-container">
			<table class="widefat sz-data-table sz-team-list-table sz-select-all-range">
				<thead>
					<tr>
						<th class="check-column"><input class="sz-select-all" type="checkbox"></th>
						<th class="column-list">
							<?php _e( 'Player List', 'sportszone' ); ?>
						</th>
						<th class="column-players">
							<?php _e( 'Players', 'sportszone' ); ?>
						</th>
						<th class="column-league">
							<?php _e( 'League', 'sportszone' ); ?>
						</th>
						<th class="column-season">
							<?php _e( 'Season', 'sportszone' ); ?>
						</th>
						<th class="column-layout">
							<?php _e( 'Layout', 'sportszone' ); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ( is_array( $data ) ):
						if ( sizeof( $data ) > 0 ):
							$i = 0;
							foreach ( $data as $list ):
								$players = array_filter( get_post_meta( $list->ID, 'sz_player' ) );
								$format = get_post_meta( $list->ID, 'sz_format', true );
								?>
								<tr class="sz-row sz-post<?php if ( $i % 2 == 0 ) echo ' alternate'; ?>">
									<td>
										<input type="checkbox" name="sz_list[]" id="sz_list_<?php echo $list->ID; ?>" value="<?php echo $list->ID; ?>" <?php checked( in_array( $list->ID, $checked ) ); ?>>
									</td>
									<td>
										<a href="<?php echo get_edit_post_link( $list->ID ); ?>">
											<?php echo $list->post_title; ?>
										</a>
									</td>
									<td><?php echo sizeof( $players ); ?></td>
									<td><?php echo get_the_terms ( $list->ID, 'sz_league' ) ? the_terms( $list->ID, 'sz_league' ) : '&mdash;'; ?></td>
									<td><?php echo get_the_terms ( $list->ID, 'sz_season' ) ? the_terms( $list->ID, 'sz_season' ) : '&mdash;'; ?></td>
									<td><?php echo sz_array_value( SP()->formats->list, $format, '&mdash;' ); ?></td>
								</tr>
								<?php
								$i++;
							endforeach;
						else:
							?>
							<tr class="sz-row alternate">
								<td colspan="6">
									<?php _e( 'No results found.', 'sportszone' ); ?>
								</td>
							</tr>
							<?php
						endif;
					else:
					?>
					<tr class="sz-row alternate">
						<td colspan="6">
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