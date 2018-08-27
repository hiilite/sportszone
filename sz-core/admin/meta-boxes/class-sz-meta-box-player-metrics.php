<?php
/**
 * Player Metrics
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     1.9.7
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Player_Metrics
 */
class SZ_Meta_Box_Player_Metrics {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		$metrics = get_post_meta( $post->ID, 'sz_metrics', true );

		$args = array(
			'post_type' => 'sz_metric',
			'numberposts' => -1,
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		);

		$vars = get_posts( $args );

		if ( $vars ):
			foreach ( $vars as $var ):
			?>
			<p><strong><?php echo $var->post_title; ?></strong></p>
			<p><input type="text" name="sz_metrics[<?php echo $var->post_name; ?>]" value="<?php echo esc_attr( sz_array_value( $metrics, $var->post_name, '' ) ); ?>" /></p>
			<?php
			endforeach;
		else:
			sz_post_adder( 'sz_metric', __( 'Add New', 'sportszone' ) );
		endif;
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_metrics', sz_array_value( $_POST, 'sz_metrics', array() ) );
	}
}