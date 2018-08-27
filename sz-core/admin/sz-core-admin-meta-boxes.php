<?php
/**
 * SportsZone Meta Boxes
 *
 * Sets up the write panels used by custom post types
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/sz-core/admin/meta_boxes
 * @version		2.5.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Admin_Meta_Boxes
 */
class SZ_Admin_Meta_Boxes {

	/**
	 * @var array
	 */
	public $meta_boxes = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		$meta_boxes = array(
			'sz_outcome' => array(
				'details' => array(
					'title' => __( 'Details', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Outcome_Details::save',
					'output' => 'SZ_Meta_Box_Outcome_Details::output',
					'context' => 'normal',
					'priority' => 'high',
				),
			),
			'sz_result' => array(
				'details' => array(
					'title' => __( 'Details', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Result_Details::save',
					'output' => 'SZ_Meta_Box_Result_Details::output',
					'context' => 'side',
					'priority' => 'default',
				),
				'equation' => array(
					'title' => __( 'Equation', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Result_Equation::save',
					'output' => 'SZ_Meta_Box_Result_Equation::output',
					'context' => 'normal',
					'priority' => 'high',
				),
			),
			'sz_performance' => array(
				'details' => array(
					'title' => __( 'Details', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Performance_Details::save',
					'output' => 'SZ_Meta_Box_Performance_Details::output',
					'context' => 'normal',
					'priority' => 'high',
				),
				'equation' => array(
					'title' => __( 'Equation', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Performance_Equation::save',
					'output' => 'SZ_Meta_Box_Performance_Equation::output',
					'context' => 'normal',
					'priority' => 'high',
				),
			),
			'sz_column' => array(
				'details' => array(
					'title' => __( 'Details', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Column_Details::save',
					'output' => 'SZ_Meta_Box_Column_Details::output',
					'context' => 'side',
					'priority' => 'default',
				),
				'equation' => array(
					'title' => __( 'Equation', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Column_Equation::save',
					'output' => 'SZ_Meta_Box_Column_Equation::output',
					'context' => 'normal',
					'priority' => 'high',
				),
			),
			'sz_metric' => array(
				'details' => array(
					'title' => __( 'Details', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Metric_Details::save',
					'output' => 'SZ_Meta_Box_Metric_Details::output',
					'context' => 'normal',
					'priority' => 'high',
				),
			),
			'sz_statistic' => array(
				'details' => array(
					'title' => __( 'Details', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Statistic_Details::save',
					'output' => 'SZ_Meta_Box_Statistic_Details::output',
					'context' => 'side',
					'priority' => 'default',
				),
				'equation' => array(
					'title' => __( 'Equation', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Statistic_Equation::save',
					'output' => 'SZ_Meta_Box_Statistic_Equation::output',
					'context' => 'normal',
					'priority' => 'high',
				),
			),
			'sz_match' => array(
				/*'shortcode' => array(
					'title' => __( 'Shortcodes', 'sportszone' ),
					'output' => 'SZ_Meta_Box_Event_Shortcode::output',
					'context' => 'side',
					'priority' => 'default',
				),*/
				/*'format' => array(
					'title' => __( 'Format', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Match_Format::save',
					'output' => 'SZ_Meta_Box_Match_Format::output',
					'context' => 'side',
					'priority' => 'default',
				),*/
				/*'mode' => array(
					'title' => __( 'Mode', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Event_Mode::save',
					'output' => 'SZ_Meta_Box_Event_Mode::output',
					'context' => 'side',
					'priority' => 'default',
				),*/
				'details' => array(
					'title' => __( 'Details', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Match_Details::save',
					'output' => 'SZ_Meta_Box_Match_Details::output',
					'context' => 'side',
					'priority' => 'default',
				),
				'team' => array(
					'title'		=> __( 'Teams', 'sportszone' ),
					'save' 		=> 'SZ_Meta_Box_Match_Teams::save',
					'output' 	=> 'SZ_Meta_Box_Match_Teams::output',
					'context' 	=> 'side',
					'priority' 	=> 'default',
				),
				'results' => array(
					'title' => __( 'Results', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Match_Results::save',
					'output' => 'SZ_Meta_Box_Match_Results::output',
					'context' => 'normal',
					'priority' => 'high',
				),
				'performance' => array(
					'title' => __( 'Box Score', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Match_Performance::save',
					'output' => 'SZ_Meta_Box_Match_Performance::output',
					'context' => 'normal',
					'priority' => 'high',
				),
			),
			/*'sz_team' => array(
				'details' => array(
					'title' => __( 'Details', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Team_Details::save',
					'output' => 'SZ_Meta_Box_Team_Details::output',
					'context' => 'side',
					'priority' => 'default',
				),
				'staff' => array(
					'title' => __( 'Staff', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Team_Staff::save',
					'output' => 'SZ_Meta_Box_Team_Staff::output',
					'context' => 'normal',
					'priority' => 'high',
				),
			),
			'sz_player' => array(
				'shortcode' => array(
					'title' => __( 'Shortcodes', 'sportszone' ),
					'output' => 'SZ_Meta_Box_Player_Shortcode::output',
					'context' => 'side',
					'priority' => 'default',
				),
				'columns' => array(
					'title' => __( 'Columns', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Player_Columns::save',
					'output' => 'SZ_Meta_Box_Player_Columns::output',
					'context' => 'side',
					'priority' => 'default',
				),
				'details' => array(
					'title' => __( 'Details', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Player_Details::save',
					'output' => 'SZ_Meta_Box_Player_Details::output',
					'context' => 'side',
					'priority' => 'default',
				),
				'metrics' => array(
					'title' => __( 'Metrics', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Player_Metrics::save',
					'output' => 'SZ_Meta_Box_Player_Metrics::output',
					'context' => 'side',
					'priority' => 'default',
				),
				'statistics' => array(
					'title' => __( 'Statistics', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Player_Statistics::save',
					'output' => 'SZ_Meta_Box_Player_Statistics::output',
					'context' => 'normal',
					'priority' => 'high',
				),
			),
			'sz_staff' => array(
				'shortcode' => array(
					'title' => __( 'Shortcode', 'sportszone' ),
					'output' => 'SZ_Meta_Box_Staff_Shortcode::output',
					'context' => 'side',
					'priority' => 'default',
				),
				'details' => array(
					'title' => __( 'Details', 'sportszone' ),
					'save' => 'SZ_Meta_Box_Staff_Details::save',
					'output' => 'SZ_Meta_Box_Staff_Details::output',
					'context' => 'side',
					'priority' => 'default',
				),
			),*/
		);

		$this->meta_boxes = apply_filters( 'sportszone_meta_boxes', $meta_boxes );

		foreach ( $this->meta_boxes as $post_type => $meta_boxes ) {
			$i = 0;
			foreach ( $meta_boxes as $id => $meta_box ) {
				if ( array_key_exists( 'save', $meta_box ) ) {
					add_action( 'sportszone_process_' . $post_type . '_meta', $meta_box['save'], ( $i + 1 ) * 10, 2 );
				}
				$i++;
			}
		}

		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'rename_meta_boxes' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );
	}

	/**
	 * Add SZ Meta boxes
	 */
	public function add_meta_boxes() {
		foreach ( $this->meta_boxes as $post_type => $meta_boxes ) {
			foreach ( $meta_boxes as $id => $meta_box ) {
				if ( array_key_exists( 'output', $meta_box ) ) {
					add_meta_box( 'sz_' . $id . 'div', $meta_box['title'], $meta_box['output'], $post_type, $meta_box['context'], $meta_box['priority'] );
				}
			}
		}
	}

	/**
	 * Remove bloat
	 */
	public function remove_meta_boxes() {

		// Events
		remove_meta_box( 'sz_venuediv', 'sz_match', 'side' );
		remove_meta_box( 'sz_leaguediv', 'sz_match', 'side' );
		remove_meta_box( 'sz_seasondiv', 'sz_match', 'side' );

		// Teams
		remove_meta_box( 'sz_leaguediv', 'sz_team', 'side' );
		remove_meta_box( 'sz_seasondiv', 'sz_team', 'side' );
		remove_meta_box( 'sz_venuediv', 'sz_team', 'side' );

		// Players
		remove_meta_box( 'sz_seasondiv', 'sz_player', 'side' );
		remove_meta_box( 'sz_leaguediv', 'sz_player', 'side' );
		remove_meta_box( 'sz_positiondiv', 'sz_player', 'side' );

		// Staff
		remove_meta_box( 'sz_rolediv', 'sz_staff', 'side' );
		remove_meta_box( 'sz_seasondiv', 'sz_staff', 'side' );
		remove_meta_box( 'sz_leaguediv', 'sz_staff', 'side' );
	}

	/**
	 * Rename core meta boxes
	 */
	public function rename_meta_boxes() {
		remove_meta_box( 'submitdiv', 'sz_match', 'side' );
		add_meta_box( 'submitdiv', __( 'Match', 'sportszone' ), 'post_submit_meta_box', 'sz_match', 'side', 'high' );

		remove_meta_box( 'postimagediv', 'sz_team', 'side' );
		add_meta_box( 'postimagediv', __( 'Logo', 'sportszone' ), 'post_thumbnail_meta_box', 'sz_team', 'side', 'low' );

		remove_meta_box( 'postimagediv', 'sz_player', 'side' );
		add_meta_box( 'postimagediv', __( 'Photo', 'sportszone' ), 'post_thumbnail_meta_box', 'sz_player', 'side', 'low' );

		remove_meta_box( 'postimagediv', 'sz_staff', 'side' );
		add_meta_box( 'postimagediv', __( 'Photo', 'sportszone' ), 'post_thumbnail_meta_box', 'sz_staff', 'side', 'low' );

		remove_meta_box( 'postimagediv', 'sz_performance', 'side' );
		add_meta_box( 'postimagediv', __( 'Icon', 'sportszone' ), 'post_thumbnail_meta_box', 'sz_performance', 'side', 'low' );
	}

	/**
	 * Check if we're saving, then trigger an action based on the post type
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		
		if ( empty( $post_id ) || empty( $post ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		//if ( empty( $_POST['sportszone_meta_nonce'] ) || ! wp_verify_nonce( $_POST['sportszone_meta_nonce'], 'sportszone_save_data' ) ) return;
		if ( ! apply_filters( 'sportszone_user_can', current_user_can( 'edit_post', $post_id  ), $post_id ) ) return;
		//if ( ! is_sz_post_type( $post->post_type ) && ! is_sz_config_type( $post->post_type ) ) return;
		
		do_action( 'sportszone_process_' . $post->post_type . '_meta', $post_id, $post );
	}

}

new SZ_Admin_Meta_Boxes();
