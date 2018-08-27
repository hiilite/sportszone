<?php
/**
 * Performance Details
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     2.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SZ_Meta_Box_Config' ) )
	include( 'class-sz-meta-box-config.php' );

/**
 * SZ_Meta_Box_Performance_Details
 */
class SZ_Meta_Box_Performance_Details extends SZ_Meta_Box_Config {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'sportszone_save_data', 'sportszone_meta_nonce' );
		global $pagenow;
		if ( 'post.php' == $pagenow && 'draft' !== get_post_status() ) {
			$readonly = true;
		} else {
			$readonly = false;
		}
		
		// Post Meta
		$singular = get_post_meta( $post->ID, 'sz_singular', true );
		$section = get_post_meta( $post->ID, 'sz_section', true );
		if ( '' === $section ) {
			$section = -1;
		}
		$format = get_post_meta( $post->ID, 'sz_format', true );
		if ( '' === $format ) {
			$format = 'number';
		}
		$precision = get_post_meta( $post->ID, 'sz_precision', true );
		if ( '' === $precision ) {
			$precision = 0;
		}
		$timed = get_post_meta( $post->ID, 'sz_timed', true );
		if ( '' === $timed ) {
			$timed = true;
		}
		$sendoff = get_post_meta( $post->ID, 'sz_sendoff', true );
		if ( '' === $sendoff ) {
			$sendoff = false;
		}
		?>
		<p><strong><?php _e( 'Variable', 'sportszone' ); ?></strong></p>
		<p>
			<input name="sz_default_key" type="hidden" id="sz_default_key" value="<?php echo $post->post_name; ?>">
			<input name="sz_key" type="text" id="sz_key" value="<?php echo $post->post_name; ?>"<?php if ( $readonly ) { ?> readonly="readonly"<?php } ?>>
		</p>
		<p><strong><?php _e( 'Singular', 'sportszone' ); ?></strong></p>
		<p>
			<input name="sz_singular" type="text" id="sz_singular" placeholder="<?php echo $post->post_title; ?>" value="<?php echo $singular; ?>">
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
		<p class="sz-format-selector">
			<select name="sz_format">
				<?php
				$options = apply_filters( 'sportszone_performance_formats', array( 'number' => __( 'Number', 'sportszone' ), 'time' => __( 'Time', 'sportszone' ), 'text' => __( 'Text', 'sportszone' ), 'equation' => __( 'Equation', 'sportszone' ) ) );
				foreach ( $options as $key => $value ):
					printf( '<option value="%s" %s>%s</option>', $key, selected( $key == $format, true, false ), $value );
				endforeach;
				?>
			</select>
		</p>
		<div id="sz_precisiondiv">
			<p><strong><?php _e( 'Decimal Places', 'sportszone' ); ?></strong></p>
			<p>
				<input name="sz_precision" type="text" size="4" id="sz_precision" value="<?php echo $precision; ?>" placeholder="0">
			</p>
		</div>
		<div id="sz_timeddiv">
			<p>
				<strong><?php _e( 'Timed', 'sportszone' ); ?></strong>
				<i class="dashicons dashicons-editor-help sz-desc-tip" title="<?php _e( 'Record minutes?', 'sportszone' ); ?>"></i>
			</p>
			<ul class="sz-timed-selector">
				<li>
					<label class="selectit">
						<input name="sz_timed" id="sz_timed_yes" type="radio" value="1" <?php checked( $timed ); ?>>
						<?php _e( 'Yes', 'sportszone' ); ?>
					</label>
				</li>
				<li>
					<label class="selectit">
						<input name="sz_timed" id="sz_timed_no" type="radio" value="0" <?php checked( ! $timed ); ?>>
						<?php _e( 'No', 'sportszone' ); ?>
					</label>
				</li>
			</ul>
		</div>
		<div id="sz_sendoffdiv">
			<p>
				<strong><?php _e( 'Send Off', 'sportszone' ); ?></strong>
				<i class="dashicons dashicons-editor-help sz-desc-tip" title="<?php _e( "Don't count minutes after?", 'sportszone' ); ?>"></i>
			</p>
			<ul class="sz-sendoff-selector">
				<li>
					<label class="selectit">
						<input name="sz_sendoff" id="sz_sendoff_yes" type="radio" value="1" <?php checked( $sendoff ); ?>>
						<?php _e( 'Yes', 'sportszone' ); ?>
					</label>
				</li>
				<li>
					<label class="selectit">
						<input name="sz_sendoff" id="sz_sendoff_no" type="radio" value="0" <?php checked( ! $sendoff ); ?>>
						<?php _e( 'No', 'sportszone' ); ?>
					</label>
				</li>
			</ul>
		</div>
		<?php
		if ( 'auto' === get_option( 'sportszone_player_columns', 'auto' ) ) {
			$visible = get_post_meta( $post->ID, 'sz_visible', true );
			if ( '' === $visible ) {
				$visible = 1;
			}
			?>
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
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		self::delete_duplicate( $_POST );
		update_post_meta( $post_id, 'sz_singular', sz_array_value( $_POST, 'sz_singular', '' ) );
		update_post_meta( $post_id, 'sz_section', (int) sz_array_value( $_POST, 'sz_section', -1 ) );
		update_post_meta( $post_id, 'sz_format', sz_array_value( $_POST, 'sz_format', 'number' ) );
		update_post_meta( $post_id, 'sz_precision', sz_array_value( $_POST, 'sz_precision', 0 ) );
		update_post_meta( $post_id, 'sz_timed', sz_array_value( $_POST, 'sz_timed', 0 ) );
		update_post_meta( $post_id, 'sz_sendoff', sz_array_value( $_POST, 'sz_sendoff', 0 ) );
		if ( 'auto' === get_option( 'sportszone_player_columns', 'auto' ) ) {
			update_post_meta( $post_id, 'sz_visible', sz_array_value( $_POST, 'sz_visible', 1 ) );
		}
	}
}