<?php
/**
 * Event Officials
 *
 * @author    Rob Tucker <rtucker-scs>
 * @author    ThemeBoy
 * @category  Admin
 * @package   SportsZone/Admin/Meta_Boxes
 * @version   2.5.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Match_Officials
 */
class SZ_Meta_Box_Match_Officials {

  /**
   * Output the metabox
   */
  public static function output( $post ) {
    $duties = get_terms( array(
      'taxonomy' => 'sz_duty',
      'hide_empty' => false,
      'orderby' => 'meta_value_num',
      'meta_query' => array(
        'relation' => 'OR',
        array(
          'key' => 'sz_order',
          'compare' => 'NOT EXISTS'
        ),
        array(
          'key' => 'sz_order',
          'compare' => 'EXISTS'
        ),
      ),
    ) );

    $officials = (array) get_post_meta( $post->ID, 'sz_officials', true );

    if ( is_array( $duties ) && sizeof( $duties ) ) {
      foreach ( $duties as $duty ) {
        ?>
      	<p><strong><?php echo $duty->name; ?></strong></p>
        <p><?php
        $args = array(
          'post_type' => 'sz_official',
          'name' => 'sz_officials[' . $duty->term_id . '][]',
          'selected' => sz_array_value( $officials, $duty->term_id, array() ),
          'values' => 'ID',
          'placeholder' => sprintf( __( 'Select %s', 'sportszone' ), __( 'Officials', 'sportszone' ) ),
          'class' => 'widefat',
          'property' => 'multiple',
          'chosen' => true,
        );

        if ( ! sz_dropdown_pages( $args ) ) {
          sz_post_adder( 'sz_official', __( 'Add New', 'sportszone' )  );
        }
        ?></p>
        <?php
      }
    } else {
      sz_taxonomy_adder( 'sz_duty', 'sz_official', __( 'Duty', 'sportszone' ) );
    }
  }

  /**
   * Save meta box data
   */
  public static function save( $post_id, $post ) {
    update_post_meta( $post_id, 'sz_officials', sz_array_value( $_POST, 'sz_officials', array() ) );
  }
}
