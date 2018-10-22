<?php
/**
 * Event Teams
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     2.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Match_Teams
 */
class SZ_Meta_Box_Match_Teams {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$limit = get_option( 'sportszone_match_teams', 2 );
		$teams = (array) get_post_meta( $post->ID, 'sz_team', false );
		$teams = (is_array($teams[0]))?$teams[0]:$teams;
		$post_type = sz_get_post_mode_type( $post->ID );
		if ( $limit && 'sz_player' !== $post_type ) {
			for ( $i = 0; $i < $limit; $i ++ ):
				$team = $teams;
				
				?>
				<div class="sz-instance">
					<p class="sz-tab-select sz-title-generator">
					<?php
					$args = array(
						'post_type' => $post_type,
						'name' => 'sz_team[]',
						'class' => 'sportszone-pages',
						'show_option_none' => __( '&mdash; None &mdash;', 'sportszone' ),
						'values' => 'ID',
						'selected' => $team[$i],
						'chosen' => true,
						'tax_query' => array(),
					);
					
					if ( 'yes' == get_option( 'sportszone_event_filter_teams_by_league', 'no' ) ) {
						$league_id = sz_get_the_term_id( $post->ID, 'sz_league', 0 );
						if ( $league_id ) {
							$args['tax_query'][] = array(
								'taxonomy' => 'sz_league',
								'terms' => $league_id,
							);
						}
					}
					if ( 'yes' == get_option( 'sportszone_event_filter_teams_by_season', 'no' ) ) {
						$season_id = sz_get_the_term_id( $post->ID, 'sz_season', 0 );
						if ( $season_id ) {
							$args['tax_query'][] = array(
								'taxonomy' => 'sz_season',
								'terms' => $season_id,
							);
						}
					}
					if ( ! sz_dropdown_pages( $args ) ) {
						unset( $args['tax_query'] );
						sz_dropdown_pages( $args );
					}
					?>
					</p>
					<?php
					$tabs = array();
					$sections = get_option( 'sportszone_event_performance_sections', -1 );
					if ( 0 == $sections ) {
						$tabs['sz_offense'] = array(
							'label' => __( 'Offense', 'sportszone' ),
							'post_type' => 'sz_player',
						);
						$tabs['sz_defense'] = array(
							'label' => __( 'Defense', 'sportszone' ),
							'post_type' => 'sz_player',
						);
					} elseif ( 1 == $sections ) {
						$tabs['sz_defense'] = array(
							'label' => __( 'Defense', 'sportszone' ),
							'post_type' => 'sz_player',
						);
						$tabs['sz_offense'] = array(
							'label' => __( 'Offense', 'sportszone' ),
							'post_type' => 'sz_player',
						);
					} else {
						$tabs['sz_player'] = array(
							'label' => __( 'Players', 'sportszone' ),
							'post_type' => 'sz_player',
						);
					}
					$tabs['sz_staff'] = array(
						'label' => __( 'Staff', 'sportszone' ),
						'post_type' => 'sz_staff',
					);
					?>
					<?php if ( $tabs ) { ?>
					<ul id="sz_team-tabs" class="sz-tab-bar category-tabs">
						<?php
							$j = 0;
							foreach ( $tabs as $slug => $tab ) {
								?>
								<li class="<?php if ( 0 == $j ) { ?>tabs<?php } ?>"><a href="#<?php echo $slug; ?>-all"><?php echo $tab['label']; ?></a></li>
								<?php
								$j++;
							}
						?>
					</ul>
					<?php
						$j = 0;
						foreach ( $tabs as $slug => $tab ) {
							do_action( 'sz_match_teams_meta_box_checklist', $post->ID, $tab['post_type'], ( 0 == $j ? 'block' : 'none' ), $team[$i], $i, $slug );
							SZ_Core_Lazy_Loading::$already_run = false;
							$j++;
						}
					?>
					<?php } ?>
				</div>
				<?php
			endfor;
		} else {
			?>
			<p><strong><?php printf( __( 'Select %s:', 'sportszone' ), sz_get_post_mode_label( $post->ID ) ); ?></strong></p>
			<?php
			$args = array(
				'post_type' => $post_type,
				'name' => 'sz_team[]',
				'selected' => $teams,
				'values' => 'ID',
				'class' => 'widefat',
				'property' => 'multiple',
				'chosen' => true,
				'placeholder' => __( 'None', 'sportszone' ),
			);
			if ( ! sz_dropdown_pages( $args ) ):
				sz_post_adder( $post_type, __( 'Add New', 'sportszone' )  );
			endif;
		}
		wp_nonce_field( 'sz-get-players', 'sz-get-players-nonce', false );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		$teams = sz_array_value( $_POST, 'sz_team', array() );

		sz_update_post_meta_recursive( $post_id, 'sz_team', $teams );

		$post_type = sz_get_post_mode_type( $post->ID );

		if ( 'sz_player' === $post_type ) {
			$players = array();
			foreach ( $teams as $player ) {
				$players[] = array( 0, $player );
			}
			sz_update_post_meta_recursive( $post_id, 'sz_player', $players );
		} else {
			$tabs = array();
			$sections = get_option( 'sportszone_event_performance_sections', -1 );
			if ( -1 == $sections ) {
				sz_update_post_meta_recursive( $post_id, 'sz_player', sz_array_value( $_POST, 'sz_player', array() ) );
			} else {
				$players = array_merge( sz_array_value( $_POST, 'sz_offense', array() ), sz_array_value( $_POST, 'sz_defense', array() ) );
				sz_update_post_meta_recursive( $post_id, 'sz_offense', sz_array_value( $_POST, 'sz_offense', array() ) );
				sz_update_post_meta_recursive( $post_id, 'sz_defense', sz_array_value( $_POST, 'sz_defense', array() ) );
				sz_update_post_meta_recursive( $post_id, 'sz_player', $players );
			}
			sz_update_post_meta_recursive( $post_id, 'sz_staff', sz_array_value( $_POST, 'sz_staff', array() ) );
		}
	}
}
