<?php
/**
 * Statistic Details
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version   2.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SZ_Meta_Box_Config' ) )
	include( 'class-sz-meta-box-config.php' );

/**
 * SZ_Meta_Box_Statistic_Details
 */
class SZ_Meta_Box_Statistic_Details extends SZ_Meta_Box_Config {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'sportszone_save_data', 'sportszone_meta_nonce' );
		$precision = get_post_meta( $post->ID, 'sz_precision', true );
		$section = get_post_meta( $post->ID, 'sz_section', true );
		$format = get_post_meta( $post->ID, 'sz_format', true );
		$total = get_post_meta( $post->ID, 'sz_type', true );
		$visible = get_post_meta( $post->ID, 'sz_visible', true );

		// Defaults
		if ( '' === $precision ) $precision = 0;
		if ( '' === $section ) $section = -1;
		if ( '' === $format ) $format = 'number';
		if ( '' === $visible ) $visible = 1;
		?>
		<p><strong><?php _e( 'Key', 'sportszone' ); ?></strong></p>
		<p>
			<input name="sz_default_key" type="hidden" id="sz_default_key" value="<?php echo $post->post_name; ?>">
			<input name="sz_key" type="text" id="sz_key" value="<?php echo $post->post_name; ?>">
		</p>
		<p><strong><?php _e( 'Decimal Places', 'sportszone' ); ?></strong></p>
		<p class="sz-precision-selector">
			<input name="sz_precision" type="text" size="4" id="sz_precision" value="<?php echo $precision; ?>" placeholder="0">
		</p>
		<p><strong><?php _e( 'Category', 'sportszone' ); ?></strong></p>
		<p class="sz-section-selector">
			<select name="sz_section">
				<?php
				$options = apply_filters( 'sportszone_performance_sections', array( -1 => __( 'All', 'sportszone' ), 0 => __( 'Offense', 'sportszone' ), 1 => __( 'Defense', 'sportszone' ) ) );
				foreach ( $options as $key => $value ):
					printf( '<option value="%s" %s>%s</option>', $key, selected( $key == $section, true, false ), $value );
				endforeach;
				?>
			</select>
		</p>
		<p><strong><?php _e( 'Format', 'sportszone' ); ?></strong></p>
		<p>
			<select name="sz_format">
				<?php
				$options = apply_filters( 'sportszone_statistic_formats', array( 'number' => __( 'Number', 'sportszone' ), 'time' => __( 'Time', 'sportszone' ) ) );
				foreach ( $options as $key => $value ):
					printf( '<option value="%s" %s>%s</option>', $key, selected( $key == $format, true, false ), $value );
				endforeach;
				?>
			</select>
		</p>
		<p><strong><?php _e( 'Type', 'sportszone' ); ?></strong></p>
		<p>
			<select name="sz_type">
				<?php
				$options = apply_filters( 'sportszone_statistic_total_types', array( 'total' => __( 'Total', 'sportszone' ), 'average' => __( 'Average', 'sportszone' ) ) );
				foreach ( $options as $key => $value ):
					printf( '<option value="%s" %s>%s</option>', $key, selected( $key == $total, true, false ), $value );
				endforeach;
				?>
			</select>
		</p>
		<p>
			<strong><?php _e( 'Visible', 'sportszone' ); ?></strong>
			<i class="dashicons dashicons-editor-help sz-desc-tip" title="<?php _e( 'Display in player profile?', 'sportszone' ); ?>"></i>
		</p>
		<ul class="sz-visible-selector">
			<li>
				<label class="selectit">
					<input name="sz_visible" id="sz_visible_yes" type="radio" value="1" <?php checked( $visible ); ?>>
					<?php _e( 'Yes', 'sportszone' ); ?>
				</label>
			</li>
			<li>
				<label class="selectit">
					<input name="sz_visible" id="sz_visible_no" type="radio" value="0" <?php checked( ! $visible ); ?>>
					<?php _e( 'No', 'sportszone' ); ?>
				</label>
			</li>
		</ul>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		self::delete_duplicate( $_POST );
		update_post_meta( $post_id, 'sz_section', (int) sz_array_value( $_POST, 'sz_section', -1 ) );
		update_post_meta( $post_id, 'sz_type', sz_array_value( $_POST, 'sz_type', 'total' ) );
		update_post_meta( $post_id, 'sz_format', sz_array_value( $_POST, 'sz_format', 'number' ) );
		update_post_meta( $post_id, 'sz_precision', (int) sz_array_value( $_POST, 'sz_precision', 1 ) );
		update_post_meta( $post_id, 'sz_visible', sz_array_value( $_POST, 'sz_visible', 1 ) );
	}

}