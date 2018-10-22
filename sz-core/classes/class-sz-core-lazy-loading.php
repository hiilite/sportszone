<?php
/**
 * Core Lazy Loading class.
 *
 * @package SportsZone
 * @subpackage Core
 * @since 3.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'SZ_Core_Lazy_Loading' ) ) :

/**
 * Main SportsPress Lazy Loading Class
 *
 * @class SportsPress_Lazy_Loading
 * @version	2.3
 */
class SZ_Core_Lazy_Loading {

	public static $already_run = false;
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_sz-get-players', array( $this, 'get_players' ) );
		add_action( 'sz_match_teams_meta_box_checklist', array( $this, 'checklist' ), 10, 6 );
		add_filter( 'sportszone_localized_strings', array( $this, 'strings' ) );
	}

	/**
	 * Get players.
	 */
	public function get_players() {
		$team = sz_array_value( $_POST, 'team' );
		
		$index = sz_array_value( $_POST, 'index', 1 );
		$selected = sz_array_value( $_POST, 'selected', array() );
		$match_id = sz_array_value( $_POST, 'match_id' );
		
		$args = array(
			'orderby' => 'menu_order',
		);

		if ( $team ) {
			$args['meta_query'] = array(
				array(
					'key' => 'sz_current_team',
					'value' => sz_array_value( $_POST, 'team' ),
				),
			);
		}

		$players = array();

		$data = array( 'index' => $index );

		if ( sz_group_has_members("group_id=".$team) ) :
			$i = 0;
			while ( sz_group_members() ) : sz_group_the_member();
				$member_id = sz_get_group_member_id();
				
				$players[$i]->post_title = sz_get_player_name_with_number( $member_id );
				$players[$i]->ID =  $member_id ;
				$i++;
			endwhile;
		endif;
		$selected_players = get_post_meta( $match_id, 'sz_player', true );
		$data['match_id']	= $match_id;
		$data['selected'] = $selected_players[$index];
		$data['players'] = $players;
		$data['sections'] = get_option( 'sportszone_event_performance_sections', -1 );
		
		wp_send_json_success( $data );
	}

	/**
	 * Ajax checklist.
	 */
	public function checklist( $post_id = null, $post_type = 'post', $display = 'block', $team = null, $index = null, $slug = null ) {
		if( ! self::$already_run ):
		
			// TODO: Rewrite to use new variable storage
			
			if ( ! isset( $slug ) ):
				$slug = $post_type;
			endif;
			
			$selected = (array)get_post_meta( $post_id, $slug, false );
			if ( sizeof( $selected ) ) {
				$selected = sz_array_between( $selected, 0, $index );
			} else {
				$selected = sz_array_between( (array)get_post_meta( $post_id, $post_type, false ), 0, $index );
			}
	
			$args = array(
				'orderby' => 'menu_order',
			);
	
			$player_sort = get_option( 'sportszone_match_player_sort', 'jersey' );
			if ( 'sz_player' == $post_type )
			{
				if( $player_sort == 'name' )
				{
					$args['order'] = 'ASC';
					$args['orderby'] = 'title';
				}
				else // default 'jersey'
				{
					$args['meta_key'] = 'sz_number';
					$args['orderby'] = 'meta_value_num';
					$args['order'] = 'ASC';
				}
			}
	
			$args['meta_query'] = array(
				array(
					'key' => 'sz_current_team',
					'value' => $team,
				),
			);
	
			/////////////////
			// TODO: Rewrite to accect other types
			// TODO: Change to only show members with a Player profile or Management Profile.
			// TESTING
			////////////////

			if ( sz_group_has_members("group_id=".$team) ) :
				$selected_players = get_post_meta( $post_id, 'sz_player', true);
				echo "<div id='$slug-all' class='posttypediv tabs-panel wp-tab-panel sz-tab-panel sz-ajax-checklist sz-select-all-range' style='display: $display; '>";
				//echo "<input type='hidden' value='0' name='$slug"; if ( isset( $index ) ) echo '[' . $index . ']'; echo "[]' />";
				echo "<ul class='categorychecklist form-no-clear'>";
				
				/*echo "<li class='sz-select-all-container'>
							<label class='selectit'>
								<input type='checkbox' class='sz-select-all' ";
								checked( empty( $diff ) );
								echo ">
								<strong>Select All</strong>
							</label>
						</li>";*/
				while ( sz_group_members() ) : sz_group_the_member();
				
					$member_id = sz_get_group_member_id();
					
					?>
					<li>
						<label class="selectit">
							<input type="checkbox" value="<?php echo $member_id; ?>" name="<?php echo 'sz_player'; if ( isset( $index ) ) echo '[' . $index . ']'; ?>[]" <?php checked( in_array( $member_id, $selected_players[$index] ) ); ?>>
							<?php
								switch( $player_sort )
								{
								case 'name':
									echo sz_get_player_name_then_number( $member_id );
									break;
								default:  // 'jersey'
									echo sz_get_player_name_with_number( $member_id );
								}
							?>
						</label>
					</li>
					<?php //unset( $selected[ $member_id ] );
				endwhile;
	
				
				echo "</ul>";
				echo "</div>";
				
			endif;
			/////////////////
			// END TESTING
			////////////////
			
			
			self::$already_run = true; // prevents action from running twice
		endif;
	}

	/*
	 * Localized strings.
	 */
	public function strings( $strings ) {
		$strings = array_merge( $strings, array(
			'no_results_found' => __( 'No results found.', 'sportszone' ),
			'select_all' => __( 'Select All', 'sportszone' ),
			'show_all' => __( 'Show all', 'sportszone' ),
			'loading' => __( 'Loading&hellip;', 'sportszone' ),
			'option_filter_by_league' => get_option( 'sportszone_event_filter_teams_by_league', 'no' ),
			'option_filter_by_season' => get_option( 'sportszone_event_filter_teams_by_season', 'no' ),
		) ) ;
		return $strings;
	}
}

endif;

new SZ_Core_Lazy_Loading();