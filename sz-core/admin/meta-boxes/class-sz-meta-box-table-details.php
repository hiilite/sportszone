<?php
/**
 * Table Details
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version   2.5.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Table_Details
 */
class SZ_Meta_Box_Table_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'sportszone_save_data', 'sportszone_meta_nonce' );
		$taxonomies = get_object_taxonomies( 'sz_table' );
		$caption = get_post_meta( $post->ID, 'sz_caption', true );
		$select = get_post_meta( $post->ID, 'sz_select', true );
		$post_type = sz_get_post_mode_type( $post->ID );
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
			do_action( 'sportszone_meta_box_table_details', $post->ID );
			?>
			<p><strong>
				<?php echo sz_get_post_mode_label( $post->ID ); ?>
			</strong></p>
			<p class="sz-select-setting">
				<select name="sz_select">
					<option value="auto" <?php selected( 'auto', $select ); ?>><?php _e( 'Auto', 'sportszone' ); ?></option>
					<option value="manual" <?php selected( 'manual', $select ); ?>><?php _e( 'Manual', 'sportszone' ); ?></option>
				</select>
			</p>
			<?php
			if ( 'manual' == $select ) {
				sz_post_checklist( $post->ID, $post_type, ( 'auto' == $select ? 'none' : 'block' ), array( 'sz_league', 'sz_season' ), null, 'sz_team' );
				sz_post_adder( $post_type, __( 'Add New', 'sportszone' ) );
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
		update_post_meta( $post_id, 'sz_select', sz_array_value( $_POST, 'sz_select', array() ) );
		sz_update_post_meta_recursive( $post_id, 'sz_team', sz_array_value( $_POST, 'sz_team', array() ) );
	}
}