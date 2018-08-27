<?php
/**
 * Metric Details
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SZ_Meta_Box_Config' ) )
	include( 'class-sz-meta-box-config.php' );

/**
 * SZ_Meta_Box_Metric_Details
 */
class SZ_Meta_Box_Metric_Details extends SZ_Meta_Box_Config {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'sportszone_save_data', 'sportszone_meta_nonce' );
		$visible = get_post_meta( $post->ID, 'sz_visible', true );
		if ( '' === $visible ) $visible = 1;
		?>
		<p><strong><?php _e( 'Variable', 'sportszone' ); ?></strong></p>
		<p>
			<input name="sz_default_key" type="hidden" id="sz_default_key" value="<?php echo $post->post_name; ?>">
			<input name="sz_key" type="text" id="sz_key" value="<?php echo $post->post_name; ?>">
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
		update_post_meta( $post_id, 'sz_visible', sz_array_value( $_POST, 'sz_visible', 1 ) );
	}
}