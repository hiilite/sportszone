<?php
/**
 * Column Details
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
 * SZ_Meta_Box_Column_Details
 */
class SZ_Meta_Box_Column_Details extends SZ_Meta_Box_Config {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'sportszone_save_data', 'sportszone_meta_nonce' );
		$equation = explode( ' ', get_post_meta( $post->ID, 'sz_equation', true ) );
		$order = get_post_meta( $post->ID, 'sz_order', true );
		$priority = get_post_meta( $post->ID, 'sz_priority', true );
		$precision = get_post_meta( $post->ID, 'sz_precision', true );

		// Defaults
		if ( $precision == '' ) $precision = 0;
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
		<p><strong><?php _e( 'Sort Order', 'sportszone' ); ?></strong></p>
		<p class="sz-order-selector">
			<select name="sz_priority">
				<?php
				$options = array( '0' => __( 'Disable', 'sportszone' ) );
				$count = wp_count_posts( 'sz_column' );
				for( $i = 1; $i <= $count->publish; $i++ ):
					$options[ $i ] = $i;
				endfor;
				foreach ( $options as $key => $value ):
					printf( '<option value="%s" %s>%s</option>', $key, selected( true, $key == $priority, false ), $value );
				endforeach;
				?>
			</select>
			<select name="sz_order">
				<?php
				$options = array( 'DESC' => __( 'Descending', 'sportszone' ), 'ASC' => __( 'Ascending', 'sportszone' ) );
				foreach ( $options as $key => $value ):
					printf( '<option value="%s" %s>%s</option>', $key, selected( true, $key == $order, false ), $value );
				endforeach;
				?>
			</select>
		</p>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		self::delete_duplicate( $_POST );
		update_post_meta( $post_id, 'sz_precision', (int) sz_array_value( $_POST, 'sz_precision', 1 ) );
		update_post_meta( $post_id, 'sz_priority', sz_array_value( $_POST, 'sz_priority', '0' ) );
		update_post_meta( $post_id, 'sz_order', sz_array_value( $_POST, 'sz_order', 'DESC' ) );
	}
}