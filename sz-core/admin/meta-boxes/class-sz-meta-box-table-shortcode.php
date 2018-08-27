<?php
/**
 * Table Shortcode
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version   2.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Table_Shortcode
 */
class SZ_Meta_Box_Table_Shortcode {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$the_format = get_post_meta( $post->ID, 'sz_format', true );
		if ( ! $the_format ) $the_format = 'standings';
		?>
		<p class="howto">
			<?php _e( 'Copy this code and paste it into your post, page or text widget content.', 'sportszone' ); ?>
		</p>
		<p><input type="text" value="<?php sz_shortcode_template( 'team_' . $the_format, $post->ID ); ?>" readonly="readonly" class="code widefat"></p>
		<?php
	}
}