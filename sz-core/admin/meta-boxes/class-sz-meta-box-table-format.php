<?php
/**
 * Table Format
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version   2.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Table_Format
 */
class SZ_Meta_Box_Table_Format {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'sportszone_save_data', 'sportszone_meta_nonce' );
		$the_format = get_post_meta( $post->ID, 'sz_format', true );
		?>
		<div id="post-formats-select">
			<?php foreach ( SportsZone()->formats->table as $key => $format ): ?>
				<input type="radio" name="sz_format" class="post-format" id="post-format-<?php echo $key; ?>" value="<?php echo $key; ?>" <?php checked( true, ( $key == 'standings' && ! $the_format ) || $the_format == $key ); ?>> <label for="post-format-<?php echo $key; ?>" class="post-format-icon post-format-<?php echo $key; ?>"><?php echo $format; ?></label><br>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_format', sz_array_value( $_POST, 'sz_format', 'standings' ) );
	}
}