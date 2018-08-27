<?php
/**
 * List Details
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version		2.5.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_List_Details
 */
class SZ_Meta_Box_List_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$taxonomies = get_object_taxonomies( 'sz_list' );
		$caption = get_post_meta( $post->ID, 'sz_caption', true );
		$team_id = get_post_meta( $post->ID, 'sz_team', true );
		$era = get_post_meta( $post->ID, 'sz_era', true );
		$grouping = get_post_meta( $post->ID, 'sz_grouping', true );
		$orderby = get_post_meta( $post->ID, 'sz_orderby', true );
		$order = get_post_meta( $post->ID, 'sz_order', true );
		$select = get_post_meta( $post->ID, 'sz_select', true );
		$number = get_post_meta( $post->ID, 'sz_number', true );
		$crop = get_post_meta( $post->ID, 'sz_crop', true );
		$date = get_post_meta( $post->ID, 'sz_date', true );
		$date_from = get_post_meta( $post->ID, 'sz_date_from', true );
		$date_to = get_post_meta( $post->ID, 'sz_date_to', true );
		$date_past = get_post_meta( $post->ID, 'sz_date_past', true );
		$date_relative = get_post_meta( $post->ID, 'sz_date_relative', true );
		?>
		<div>
			<p><strong><?php _e( 'Heading', 'sportszone' ); ?></strong></p>
			<p><input type="text" id="sz_caption" name="sz_caption" value="<?php echo esc_attr( $caption ); ?>" placeholder="<?php echo esc_attr( get_the_title() ); ?>"></p>

			<div class="sz-date-selector">
				<p><strong><?php _e( 'Date', 'sportszone' ); ?></strong></p>
				<p>
					<?php
					$args = array(
						'name' => 'sz_date',
						'id' => 'sz_date',
						'selected' => $date,
					);
					sz_dropdown_dates( $args );
					?>
				</p>
				<div class="sz-date-range">
					<p class="sz-date-range-absolute">
						<input type="text" class="sz-datepicker-from" name="sz_date_from" value="<?php echo $date_from ? $date_from : date_i18n( 'Y-m-d' ); ?>" size="10">
						:
						<input type="text" class="sz-datepicker-to" name="sz_date_to" value="<?php echo $date_to ? $date_to : date_i18n( 'Y-m-d' ); ?>" size="10">
					</p>

					<p class="sz-date-range-relative">
						<?php _e( 'Past', 'sportszone' ); ?>
						<input type="number" min="0" step="1" class="tiny-text" name="sz_date_past" value="<?php echo '' !== $date_past ? $date_past : 7; ?>">
						<?php _e( 'days', 'sportszone' ); ?>
					</p>

					<p class="sz-date-relative">
						<label>
							<input type="checkbox" name="sz_date_relative" value="1" id="sz_date_relative" <?php checked( $date_relative ); ?>>
							<?php _e( 'Relative', 'sportszone' ); ?>
						</label>
					</p>
				</div>
			</div>

			<?php
			foreach ( $taxonomies as $taxonomy ) {
				sz_taxonomy_field( $taxonomy, $post, true );
			}
			?>
			<p><strong><?php _e( 'Team', 'sportszone' ); ?></strong></p>
			<p class="sz-tab-select sz-team-era-selector">
				<?php
				$args = array(
					'post_type' => 'sz_team',
					'name' => 'sz_team',
					'show_option_all' => __( 'All', 'sportszone' ),
					'selected' => $team_id,
					'values' => 'ID',
				);
				if ( ! sz_dropdown_pages( $args ) ):
					sz_post_adder( 'sz_team', __( 'Add New', 'sportszone' ) );
				endif;
				?>
				<select name="sz_era">
					<option value="all" <?php selected( 'all', $era ); ?>><?php _e( 'All', 'sportszone' ); ?></option>
					<option value="current" <?php selected( 'current', $era ); ?>><?php _e( 'Current', 'sportszone' ); ?></option>
					<option value="past" <?php selected( 'past', $era ); ?>><?php _e( 'Past', 'sportszone' ); ?></option>
				</select>
			</p>
			<p><strong><?php _e( 'Grouping', 'sportszone' ); ?></strong></p>
			<p>
			<select name="sz_grouping">
				<option value="0"><?php _e( 'None', 'sportszone' ); ?></option>
				<option value="position" <?php selected( $grouping, 'position' ); ?>><?php _e( 'Position', 'sportszone' ); ?></option>
			</select>
			</p>
			<p><strong><?php _e( 'Sort by', 'sportszone' ); ?></strong></p>
			<p>
			<?php
			$args = array(
				'prepend_options' => array(
					'number' => __( 'Squad Number', 'sportszone' ),
					'name' => __( 'Name', 'sportszone' ),
				),
				'post_type' => array( 'sz_performance', 'sz_metric', 'sz_statistic' ),
				'name' => 'sz_orderby',
				'selected' => $orderby,
				'values' => 'slug',
			);
			sz_dropdown_pages( $args );
			?>
			</p>
			<p>
				<label class="selectit">
					<input type="checkbox" name="sz_crop" value="1" <?php checked( $crop ); ?>>
					<?php _e( 'Skip if zero?', 'sportszone' ); ?>
				</label>
			</p>
			<p><strong><?php _e( 'Sort Order', 'sportszone' ); ?></strong></p>
			<p>
				<select name="sz_order">
					<option value="ASC" <?php selected( 'ASC', $order ); ?>><?php _e( 'Ascending', 'sportszone' ); ?></option>
					<option value="DESC" <?php selected( 'DESC', $order ); ?>><?php _e( 'Descending', 'sportszone' ); ?></option>
				</select>
			</p>
			<p><strong><?php _e( 'Players', 'sportszone' ); ?></strong></p>
			<p class="sz-select-setting">
				<select name="sz_select">
					<option value="auto" <?php selected( 'auto', $select ); ?>><?php _e( 'Auto', 'sportszone' ); ?></option>
					<option value="manual" <?php selected( 'manual', $select ); ?>><?php _e( 'Manual', 'sportszone' ); ?></option>
				</select>
			</p>
			<?php
			if ( 'manual' == $select ) {
				sz_post_checklist( $post->ID, 'sz_player', ( 'auto' == $select ? 'none' : 'block' ), array( 'sz_league', 'sz_season', 'sz_current_team' ) );
				sz_post_adder( 'sz_player', __( 'Add New', 'sportszone' ) );
			} else {
				?>
				<p><strong><?php _e( 'Display', 'sportszone' ); ?></strong></p>
				<p><input name="sz_number" id="sz_number" type="number" step="1" min="0" class="small-text" placeholder="<?php _e( 'All', 'sportszone' ); ?>" value="<?php echo $number; ?>"> <?php _e( 'players', 'sportszone' ); ?></p>
				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_caption', esc_attr( sz_array_value( $_POST, 'sz_caption', 0 ) ) );
		update_post_meta( $post_id, 'sz_date', sz_array_value( $_POST, 'sz_date', 0 ) );
		update_post_meta( $post_id, 'sz_date_from', sz_array_value( $_POST, 'sz_date_from', null ) );
		update_post_meta( $post_id, 'sz_date_to', sz_array_value( $_POST, 'sz_date_to', null ) );
		update_post_meta( $post_id, 'sz_date_past', sz_array_value( $_POST, 'sz_date_past', 0 ) );
		update_post_meta( $post_id, 'sz_date_relative', sz_array_value( $_POST, 'sz_date_relative', 0 ) );
		$tax_input = sz_array_value( $_POST, 'tax_input', array() );
		update_post_meta( $post_id, 'sz_main_league', in_array( 'auto', sz_array_value( $tax_input, 'sz_league' ) ) );
		update_post_meta( $post_id, 'sz_current_season', in_array( 'auto', sz_array_value( $tax_input, 'sz_season' ) ) );
		update_post_meta( $post_id, 'sz_team', sz_array_value( $_POST, 'sz_team', array() ) );
		update_post_meta( $post_id, 'sz_era', sz_array_value( $_POST, 'sz_era', array() ) );
		update_post_meta( $post_id, 'sz_grouping', sz_array_value( $_POST, 'sz_grouping', array() ) );
		update_post_meta( $post_id, 'sz_orderby', sz_array_value( $_POST, 'sz_orderby', array() ) );
		update_post_meta( $post_id, 'sz_crop', sz_array_value( $_POST, 'sz_crop', 0 ) );
		update_post_meta( $post_id, 'sz_order', sz_array_value( $_POST, 'sz_order', array() ) );
		update_post_meta( $post_id, 'sz_select', sz_array_value( $_POST, 'sz_select', array() ) );
		update_post_meta( $post_id, 'sz_number', sz_array_value( $_POST, 'sz_number', array() ) );
		sz_update_post_meta_recursive( $post_id, 'sz_player', sz_array_value( $_POST, 'sz_player', array() ) );
	}
}