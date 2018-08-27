<?php
/**
 * Outcome Details
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     2.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SZ_Meta_Box_Config' ) )
	include( 'class-sz-meta-box-config.php' );

/**
 * SZ_Meta_Box_Outcome_Details
 */
class SZ_Meta_Box_Outcome_Details extends SZ_Meta_Box_Config {

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
		$abbreviation = get_post_meta( $post->ID, 'sz_abbreviation', true );
		$color = get_post_meta( $post->ID, 'sz_color', true );
		$condition = get_post_meta( $post->ID, 'sz_condition', true );
		$main_result = get_option( 'sportszone_primary_result', null );
		$result = get_page_by_path( $main_result, ARRAY_A, 'sz_result' );
		$label = sz_array_value( $result, 'post_title', __( 'Primary', 'sportszone' ) );
		
		if ( '' === $color ) $color = '#888888';
		?>
		<p><strong><?php _e( 'Variable', 'sportszone' ); ?></strong></p>
		<p>
			<input name="sz_default_key" type="hidden" id="sz_default_key" value="<?php echo $post->post_name; ?>">
			<input name="sz_key" type="text" id="sz_key" value="<?php echo $post->post_name; ?>"<?php if ( $readonly ) { ?> readonly="readonly"<?php } ?>>
		</p>
		<p><strong><?php _e( 'Abbreviation', 'sportszone' ); ?></strong></p>
		<p>
			<input name="sz_abbreviation" type="text" id="sz_abbreviation" value="<?php echo $abbreviation; ?>" placeholder="<?php echo substr( $post->post_title, 0, 1 ); ?>">
		</p>
		<p><strong><?php _e( 'Color', 'sportszone' ); ?></strong></p>
		<p>
			<div class="sz-color-box">
				<input name="sz_color" id="sz_color" type="text" value="<?php echo $color; ?>" class="colorpick">
				<div id="sz_color" class="colorpickdiv"></div>
		    </div>
		</p>
		<p><strong><?php _e( 'Condition', 'sportszone' ); ?></strong></p>
		<p>
			<select name="sz_condition">
				<?php
				$options = array(
					'0' => '&mdash;',
					'>' => sprintf( __( 'Most %s', 'sportszone' ), $label ),
					'<' => sprintf( __( 'Least %s', 'sportszone' ), $label ),
					'=' => sprintf( __( 'Equal %s', 'sportszone' ), $label ),
					'else' => sprintf( __( 'Default', 'sportszone' ), $label ),
				);
				for( $i = 1; $i <= $count->publish; $i++ ):
					$options[ $i ] = $i;
				endfor;
				foreach ( $options as $key => $value ):
					printf( '<option value="%s" %s>%s</option>', $key, selected( true, $key == $condition, false ), $value );
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
		update_post_meta( $post_id, 'sz_abbreviation', sz_array_value( $_POST, 'sz_abbreviation', array() ) );
		update_post_meta( $post_id, 'sz_color', sz_array_value( $_POST, 'sz_color', array() ) );
		update_post_meta( $post_id, 'sz_condition', sz_array_value( $_POST, 'sz_condition', array() ) );
	}
}