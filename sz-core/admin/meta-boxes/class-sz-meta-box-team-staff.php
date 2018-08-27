<?php
/**
 * Team Player Staff
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version		2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Team_Staff
 */
class SZ_Meta_Box_Team_Staff {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $pagenow;

		if ( $pagenow != 'post-new.php' ):

			$team = new SZ_Team( $post );
			list( $data, $checked ) = $team->staff( true );
			self::table( $data, $checked );

		else:

			printf( __( 'No results found.', 'sportszone' ) );

		endif;
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		sz_update_post_meta_recursive( $post_id, 'sz_staff', sz_array_value( $_POST, 'sz_staff', array() ) );
	}

	/**
	 * Admin edit table
	 */
	public static function table( $data = array(), $checked = array() ) {
		?>
		<div class="sz-data-table-container">
			<table class="widefat sz-data-table sz-team-staff-table sz-select-all-range">
				<thead>
					<tr>
						<th class="check-column"><input class="sz-select-all" type="checkbox"></th>
						<th class="column-staff">
							<?php _e( 'Staff', 'sportszone' ); ?>
						</th>
						<th class="column-role">
							<?php _e( 'Job', 'sportszone' ); ?>
						</th>
						<th class="column-league">
							<?php _e( 'League', 'sportszone' ); ?>
						</th>
						<th class="column-season">
							<?php _e( 'Season', 'sportszone' ); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ( is_array( $data ) ):
						if ( sizeof( $data ) > 0 ):
							$i = 0;
							foreach ( $data as $staff ):
								$role = get_post_meta( $staff->ID, 'sz_role', true );
								?>
								<tr class="sz-row sz-post<?php if ( $i % 2 == 0 ) echo ' alternate'; ?>">
									<td>
										<input type="checkbox" name="sz_staff[]" id="sz_staff_<?php echo $staff->ID; ?>" value="<?php echo $staff->ID; ?>" <?php checked( in_array( $staff->ID, $checked ) ); ?>>
									</td>
									<td>
										<a href="<?php echo get_edit_post_link( $staff->ID ); ?>">
											<?php echo $staff->post_title; ?>
										</a>
									</td>
									<td><?php echo get_the_terms ( $staff->ID, 'sz_role' ) ? the_terms( $staff->ID, 'sz_role' ) : '&mdash;'; ?></td>
									<td><?php echo get_the_terms ( $staff->ID, 'sz_league' ) ? the_terms( $staff->ID, 'sz_league' ) : '&mdash;'; ?></td>
									<td><?php echo get_the_terms ( $staff->ID, 'sz_season' ) ? the_terms( $staff->ID, 'sz_season' ) : '&mdash;'; ?></td>
								</tr>
								<?php
								$i++;
							endforeach;
						else:
							?>
							<tr class="sz-row alternate">
								<td colspan="5">
									<?php _e( 'No results found.', 'sportszone' ); ?>
								</td>
							</tr>
							<?php
						endif;
					else:
					?>
					<tr class="sz-row alternate">
						<td colspan="5">
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