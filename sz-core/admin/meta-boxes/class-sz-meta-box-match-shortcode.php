<?php
/**
 * Event Shortcode
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Match_Shortcode
 */
class SZ_Meta_Box_Match_Shortcode {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$shortcodes = apply_filters( 'sportszone_event_shortcodes', array(
			'event_results' => __( 'Results', 'sportszone' ),
			'event_details' => __( 'Details', 'sportszone' ),
			'event_performance' => __( 'Box Score', 'sportszone' ),
		) );
		if ( $shortcodes ) {
		?>
		<p class="howto">
			<?php _e( 'Copy this code and paste it into your post, page or text widget content.', 'sportszone' ); ?>
		</p>
		<?php foreach ( $shortcodes as $id => $label ) { ?>
		<p>
			<strong><?php echo $label; ?></strong>
		</p>
		<p><input type="text" value="<?php sz_shortcode_template( $id, $post->ID ); ?>" readonly="readonly" class="code widefat"></p>
		<?php } ?>
		<?php
		}
	}
}