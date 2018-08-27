<?php
/**
 * Player Shortcode
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     1.6.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Player_Shortcode
 */
class SZ_Meta_Box_Player_Shortcode {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		?>
		<p class="howto">
			<?php _e( 'Copy this code and paste it into your post, page or text widget content.', 'sportszone' ); ?>
		</p>
		<p>
			<strong><?php _e( 'Details', 'sportszone' ); ?></strong>
		</p>
		<p><input type="text" value="<?php sz_shortcode_template( 'player_details', $post->ID ); ?>" readonly="readonly" class="code widefat"></p>
		<p>
			<strong><?php _e( 'Statistics', 'sportszone' ); ?></strong>
		</p>
		<p><input type="text" value="<?php sz_shortcode_template( 'player_statistics', $post->ID ); ?>" readonly="readonly" class="code widefat"></p>
		<?php
	}
}