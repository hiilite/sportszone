<?php
/**
 * Calendar Details
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version		2.5.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Calendar_Details
 */
class SZ_Meta_Box_Calendar_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$taxonomies = get_object_taxonomies( 'sz_calendar' );
		$caption = get_post_meta( $post->ID, 'sz_caption', true );
		$status = get_post_meta( $post->ID, 'sz_status', true );
		$date = get_post_meta( $post->ID, 'sz_date', true );
		$date_from = get_post_meta( $post->ID, 'sz_date_from', true );
		$date_to = get_post_meta( $post->ID, 'sz_date_to', true );
		$date_past = get_post_meta( $post->ID, 'sz_date_past', true );
		$date_future = get_post_meta( $post->ID, 'sz_date_future', true );
		$date_relative = get_post_meta( $post->ID, 'sz_date_relative', true );
		$day = get_post_meta( $post->ID, 'sz_day', true );
		$teams = get_post_meta( $post->ID, 'sz_team', false );
		$table_id = get_post_meta( $post->ID, 'sz_table', true );
		$orderby = get_post_meta( $post->ID, 'sz_orderby', true );
		$order = get_post_meta( $post->ID, 'sz_order', true );
		?>
		<div>
			<p><strong><?php _e( 'Heading', 'sportszone' ); ?></strong></p>
			<p><input type="text" id="sz_caption" name="sz_caption" value="<?php echo esc_attr( $caption ); ?>" placeholder="<?php echo esc_attr( get_the_title() ); ?>"></p>

			<p><strong><?php _e( 'Status', 'sportszone' ); ?></strong></p>
			<p>
				<?php
				$args = array(
					'name' => 'sz_status',
					'id' => 'sz_status',
					'selected' => $status,
				);
				sz_dropdown_statuses( $args );
				?>
			</p>
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
						&rarr;
						<?php _e( 'Next', 'sportszone' ); ?>
						<input type="number" min="0" step="1" class="tiny-text" name="sz_date_future" value="<?php echo '' !== $date_future ? $date_future : 7; ?>">
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
			<div class="sz-event-day-field">
				<p><strong><?php _e( 'Match Day', 'sportszone' ); ?></strong></p>
				<p>
					<input name="sz_day" type="text" class="medium-text" placeholder="<?php _e( 'All', 'sportszone' ); ?>" value="<?php echo esc_attr( $day ); ?>">
				</p>
			</div>
			<?php
			foreach ( $taxonomies as $taxonomy ) {
				sz_taxonomy_field( $taxonomy, $post, true );
			}
			?>
			<p><strong><?php _e( 'Team', 'sportszone' ); ?></strong></p>
			<p>
				<?php
				$args = array(
					'post_type' => 'sz_team',
					'name' => 'sz_team[]',
					'selected' => $teams,
					'values' => 'ID',
					'class' => 'widefat',
					'property' => 'multiple',
					'chosen' => true,
					'placeholder' => __( 'All', 'sportszone' ),
				);
				if ( ! sz_dropdown_pages( $args ) ):
					sz_post_adder( 'sz_team', __( 'Add New', 'sportszone' )  );
				endif;
				?>
			</p>
			<p><strong><?php _e( 'Sort by', 'sportszone' ); ?></strong></p>
			<p>
				<select name="sz_orderby">
					<option value="date" <?php selected( 'date', $orderby ); ?>><?php _e( 'Date', 'sportszone' ); ?></option>
					<option value="day" <?php selected( 'day', $orderby ); ?>><?php _e( 'Match Day', 'sportszone' ); ?></option>
				</select>
			</p>
			<p><strong><?php _e( 'Sort Order', 'sportszone' ); ?></strong></p>
			<p>
				<select name="sz_order">
					<option value="ASC" <?php selected( 'ASC', $order ); ?>><?php _e( 'Ascending', 'sportszone' ); ?></option>
					<option value="DESC" <?php selected( 'DESC', $order ); ?>><?php _e( 'Descending', 'sportszone' ); ?></option>
				</select>
			</p>
		</div>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_caption', esc_attr( sz_array_value( $_POST, 'sz_caption', 0 ) ) );
		update_post_meta( $post_id, 'sz_status', sz_array_value( $_POST, 'sz_status', 0 ) );
		update_post_meta( $post_id, 'sz_date', sz_array_value( $_POST, 'sz_date', 0 ) );
		update_post_meta( $post_id, 'sz_date_from', sz_array_value( $_POST, 'sz_date_from', null ) );
		update_post_meta( $post_id, 'sz_date_to', sz_array_value( $_POST, 'sz_date_to', null ) );
		update_post_meta( $post_id, 'sz_date_past', sz_array_value( $_POST, 'sz_date_past', 0 ) );
		update_post_meta( $post_id, 'sz_date_future', sz_array_value( $_POST, 'sz_date_future', 0 ) );
		update_post_meta( $post_id, 'sz_date_relative', sz_array_value( $_POST, 'sz_date_relative', 0 ) );
		update_post_meta( $post_id, 'sz_day', sz_array_value( $_POST, 'sz_day', null ) );
		$tax_input = sz_array_value( $_POST, 'tax_input', array() );
		update_post_meta( $post_id, 'sz_main_league', in_array( 'auto', sz_array_value( $tax_input, 'sz_league' ) ) );
		update_post_meta( $post_id, 'sz_current_season', in_array( 'auto', sz_array_value( $tax_input, 'sz_season' ) ) );
		update_post_meta( $post_id, 'sz_orderby', sz_array_value( $_POST, 'sz_orderby', null ) );
		update_post_meta( $post_id, 'sz_order', sz_array_value( $_POST, 'sz_order', null ) );
		sz_update_post_meta_recursive( $post_id, 'sz_team', sz_array_value( $_POST, 'sz_team', array() ) );
	}
}