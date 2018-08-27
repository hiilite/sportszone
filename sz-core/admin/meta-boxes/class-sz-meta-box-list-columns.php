<?php
/**
 * List Columns
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     2.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_List_Columns
 */
class SZ_Meta_Box_List_Columns {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$selected = (array)get_post_meta( $post->ID, 'sz_columns', true );
		$orderby = get_post_meta( $post->ID, 'sz_orderby', true );
		?>
		<p><strong><?php _e( 'General', 'sportszone' ); ?></strong></p>
		<ul class="categorychecklist form-no-clear">
			<li>
				<label class="selectit">
					<input value="number" type="checkbox" name="sz_columns[]" id="sz_columns_number" <?php checked( in_array( 'number', $selected ) ); ?>>
					<?php
					if ( in_array( $orderby, array( 'number', 'name' ) ) ) {
						_e( 'Squad Number', 'sportszone' );
					} else {
						_e( 'Rank', 'sportszone' );
					}
					?>	
				</label>
			</li>
			<li>
				<label class="selectit">
					<input value="team" type="checkbox" name="sz_columns[]" id="sz_columns_team" <?php checked( in_array( 'team', $selected ) ); ?>>
					<?php _e( 'Team', 'sportszone' ); ?>
				</label>
			</li>
			<li>
				<label class="selectit">
					<input value="position" type="checkbox" name="sz_columns[]" id="sz_columns_position" <?php checked( in_array( 'position', $selected ) ); ?>>
					<?php _e( 'Position', 'sportszone' ); ?>
				</label>
			</li>
		</ul>
		<p><strong><?php _e( 'Data', 'sportszone' ); ?></strong></p>
		<div class="sz-instance">
			<ul id="sz_column-tabs" class="sz-tab-bar category-tabs">
				<li class="tabs"><a href="#sz_performance-all"><?php _e( 'Performance', 'sportszone' ); ?></a></li>
				<li><a href="#sz_metric-all"><?php _e( 'Metrics', 'sportszone' ); ?></a></li>
				<li><a href="#sz_statistic-all"><?php _e( 'Statistics', 'sportszone' ); ?></a></li>
			</ul>
			<?php
			sz_column_checklist( $post->ID, 'sz_performance', 'block', $selected );
			sz_column_checklist( $post->ID, 'sz_metric', 'none', $selected );
			sz_column_checklist( $post->ID, 'sz_statistic', 'none', $selected );
			?>
		</div>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_columns', sz_array_value( $_POST, 'sz_columns', array() ) );
	}
}