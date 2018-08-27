<?php
/**
 * Statistic Equation
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     1.9
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SZ_Meta_Box_Equation' ) )
	include( 'class-sz-meta-box-equation.php' );

/**
 * SZ_Meta_Box_Statistic_Equation
 */
class SZ_Meta_Box_Statistic_Equation extends SZ_Meta_Box_Equation {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$equation = get_post_meta( $post->ID, 'sz_equation', true );
		$groups = array( 'player_event', 'outcome', 'result', 'performance', 'metric' );
		self::builder( $post->post_title, $equation, $groups );
	}
}
