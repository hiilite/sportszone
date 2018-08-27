<?php
/**
 * League Table Mode
 *
 * @author     ThemeBoy
 * @category   Admin
 * @package   SportsZone/Admin/Meta_Boxes
 * @version   2.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Table_Mode
 */
class SZ_Meta_Box_Table_Mode {

  /**
   * Output the metabox
   */
  public static function output( $post ) {
    $the_mode = sz_get_post_mode( $post->ID );
    ?>
    <div id="post-formats-select">
      <?php foreach ( array( 'team' => __( 'Team vs team', 'sportszone' ), 'player' => __( 'Player vs player', 'sportszone' ) ) as $key => $mode ): ?>
        <input type="radio" name="sz_mode" class="post-format" id="post-format-<?php echo $key; ?>" value="<?php echo $key; ?>" <?php checked( $the_mode, $key ); ?>> <label for="post-format-<?php echo $key; ?>" class="post-format-icon post-format-<?php echo $key; ?>"><?php echo $mode; ?></label><br>
      <?php endforeach; ?>
    </div>
    <?php
  }

  /**
   * Save meta box data
   */
  public static function save( $post_id, $post ) {
    update_post_meta( $post_id, 'sz_mode', sz_array_value( $_POST, 'sz_mode', 'team' ) );
  }
}