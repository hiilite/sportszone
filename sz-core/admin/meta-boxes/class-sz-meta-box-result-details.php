<?php
/**
 * Result Details
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     1.9
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SZ_Meta_Box_Config' ) )
	include( 'class-sz-meta-box-config.php' );

/**
 * SZ_Meta_Box_Result_Details
 */
class SZ_Meta_Box_Result_Details extends SZ_Meta_Box_Config {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'sportszone_save_data', 'sportszone_meta_nonce' );
		$precision = get_post_meta( $post->ID, 'sz_precision', true );
		global $pagenow;
		if ( 'post.php' == $pagenow && 'draft' !== get_post_status() ) {
			$readonly = true;
		} else {
			$readonly = false;
		}
		?>
		<p><strong><?php _e( 'Variable', 'sportszone' ); ?></strong></p>
		<p>
			<input name="sz_default_key" type="hidden" id="sz_default_key" value="<?php echo $post->post_name; ?>">
			<input name="sz_key" type="text" id="sz_key" value="<?php echo $post->post_name; ?>"<?php if ( $readonly ) { ?> readonly="readonly"<?php } ?>> <span class="description">(for, against)</span>
		</p>
		<p><strong><?php _e( 'Decimal Places', 'sportszone' ); ?></strong></p>
		<p class="sz-precision-selector">
			<input name="sz_precision" type="text" size="4" id="sz_precision" value="<?php echo $precision; ?>" placeholder="0">
		</p>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		self::delete_duplicate( $_POST );
		update_post_meta( $post_id, 'sz_precision', (int) sz_array_value( $_POST, 'sz_precision', 1 ) );
	}
}