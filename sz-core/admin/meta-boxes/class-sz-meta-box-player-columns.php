<?php
/**
 * Player Columns
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     2.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Player_Columns
 */
class SZ_Meta_Box_Player_Columns {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$selected = (array) get_post_meta( $post->ID, 'sz_columns', true );
		$tabs = apply_filters( 'sportszone_player_column_tabs', array( 'sz_performance', 'sz_statistic' ) );
		?>
		<div class="sz-instance">
			<?php if ( $tabs ) { ?>
			<ul id="sz_column-tabs" class="sz-tab-bar category-tabs">
				<?php foreach ( $tabs as $index => $post_type ) { $object = get_post_type_object( $post_type ); ?>
				<li class="<?php if ( 0 == $index ) { ?>tabs<?php } ?>"><a href="#<?php echo $post_type; ?>-all"><?php echo $object->labels->menu_name; ?></a></li>
				<?php } ?>
			</ul>
			<?php
				foreach ( $tabs as $index => $post_type ) {
					sz_column_checklist( $post->ID, $post_type, ( 0 == $index ? 'block' : 'none' ), $selected );
				}
			?>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_columns', sz_array_value( $_POST, 'sz_columns', array() ) );
	}
}