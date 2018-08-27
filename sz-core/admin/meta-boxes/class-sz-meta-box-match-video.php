<?php
/**
 * Event Video
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     0.7
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Match_Video
 */
class SZ_Meta_Box_Match_Video {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$video = get_post_meta( $post->ID, 'sz_video', true );
		if ( $video ):
		?>
		<fieldset class="sz-video-embed">
			<?php echo apply_filters( 'the_content', '[embed width="254"]' . $video . '[/embed]' ); ?>
			<p><a href="#" class="sz-remove-video"><?php _e( 'Remove video', 'sportszone' ); ?></a></p>
		</fieldset>
		<?php endif; ?>
		<fieldset class="sz-video-field hidden">
			<p><strong><?php _e( 'URL', 'sportszone' ); ?></strong></p>
			<p><input class="widefat" type="text" name="sz_video" id="sz_video" value="<?php echo $video; ?>"></p>
			<p><a href="#" class="sz-remove-video"><?php _e( 'Cancel', 'sportszone' ); ?></a></p>
		</fieldset>
		<fieldset class="sz-video-adder<?php if ( $video ): ?> hidden<?php endif; ?>">
			<p><a href="#" class="sz-add-video"><?php _e( 'Add video', 'sportszone' ); ?></a></p>
		</fieldset>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_video', sz_array_value( $_POST, 'sz_video', null ) );
	}
}