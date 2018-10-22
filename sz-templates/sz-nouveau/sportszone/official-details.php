<?php
/**
 * Official Details
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version   2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( get_option( 'sportszone_official_show_details', 'yes' ) === 'no' ) return;

if ( ! isset( $id ) )
	$id = get_the_ID();

$defaults = array(
	'show_number' => get_option( 'sportszone_official_show_number', 'no' ) == 'yes' ? true : false,
	'show_name' => get_option( 'sportszone_official_show_name', 'no' ) == 'yes' ? true : false,
	'show_nationality' => get_option( 'sportszone_official_show_nationality', 'yes' ) == 'yes' ? true : false,
	'show_positions' => get_option( 'sportszone_official_show_positions', 'yes' ) == 'yes' ? true : false,
	'show_current_teams' => get_option( 'sportszone_official_show_current_teams', 'yes' ) == 'yes' ? true : false,
	'show_past_teams' => get_option( 'sportszone_official_show_past_teams', 'yes' ) == 'yes' ? true : false,
	'show_leagues' => get_option( 'sportszone_official_show_leagues', 'no' ) == 'yes' ? true : false,
	'show_seasons' => get_option( 'sportszone_official_show_seasons', 'no' ) == 'yes' ? true : false,
	'show_nationality_flags' => get_option( 'sportszone_official_show_flags', 'yes' ) == 'yes' ? true : false,
	'abbreviate_teams' => get_option( 'sportszone_abbreviate_teams', 'yes' ) === 'yes' ? true : false,
	'link_teams' => get_option( 'sportszone_link_teams', 'no' ) == 'yes' ? true : false,
);

extract( $defaults, EXTR_SKIP );

$countries = SP()->countries->countries;

$official = new SP_Official( $id );

$metrics_before = $official->metrics( true );
$metrics_after = $official->metrics( false );

$common = array();

if ( $show_number ):
	$common[ '#' ] = $official->number;
endif;

if ( $show_name ):
	$common[ __( 'Name', 'sportszone' ) ] = $official->post->post_title;
endif;

if ( $show_nationality ):
	$nationalities = $official->nationalities();
	if ( $nationalities && is_array( $nationalities ) ):
		$values = array();
		foreach ( $nationalities as $nationality ):
			$country_name = sz_array_value( $countries, $nationality, null );
			$values[] = $country_name ? ( $show_nationality_flags ? '<img src="' . plugin_dir_url( SP_PLUGIN_FILE ) . 'assets/images/flags/' . strtolower( $nationality ) . '.png" alt="' . $nationality . '"> ' : '' ) . $country_name : '&mdash;';
		endforeach;
		$common[ __( 'Nationality', 'sportszone' ) ] = implode( '<br>', $values );
	endif;
endif;

if ( $show_positions ):
	$positions = $official->positions();
	if ( $positions && is_array( $positions ) ):
		$position_names = array();
		foreach ( $positions as $position ):
			$position_names[] = $position->name;
		endforeach;
		$common[ __( 'Position', 'sportszone' ) ] = implode( ', ', $position_names );
	endif;
endif;

$data = array_merge( $metrics_before, $common, $metrics_after );

if ( $show_current_teams ):
	$current_teams = $official->current_teams();
	if ( $current_teams ):
		$teams = array();
		foreach ( $current_teams as $team ):
			$team_name = sz_get_team_name( $team, $abbreviate_teams );
			if ( $link_teams ) $team_name = '<a href="' . get_post_permalink( $team ) . '">' . $team_name . '</a>';
			$teams[] = $team_name;
		endforeach;
		$data[ __( 'Current Team', 'sportszone' ) ] = implode( ', ', $teams );
	endif;
endif;

if ( $show_past_teams ):
	$past_teams = $official->past_teams();
	if ( $past_teams ):
		$teams = array();
		foreach ( $past_teams as $team ):
			$team_name = sz_get_team_name( $team, $abbreviate_teams );
			if ( $link_teams ) $team_name = '<a href="' . get_post_permalink( $team ) . '">' . $team_name . '</a>';
			$teams[] = $team_name;
		endforeach;
		$data[ __( 'Past Teams', 'sportszone' ) ] = implode( ', ', $teams );
	endif;
endif;

if ( $show_leagues ):
	$leagues = $official->leagues();
	if ( $leagues && ! is_wp_error( $leagues ) ):
		$terms = array();
		foreach ( $leagues as $league ) {
			$terms[] = $league->name;
		}
		$data[ __( 'Leagues', 'sportszone' ) ] = implode( ', ', $terms );
	endif;
endif;

if ( $show_seasons ):
	$seasons = $official->seasons();
	if ( $seasons && ! is_wp_error( $seasons ) ):
		$terms = array();
		foreach ( $seasons as $season ) {
			$terms[] = $season->name;
		}
		$data[ __( 'Seasons', 'sportszone' ) ] = implode( ', ', $terms );
	endif;
endif;

$data = apply_filters( 'sportszone_official_details', $data, $id );

if ( empty( $data ) )
	return;

$output = '<div class="sp-template sp-template-official-details sp-template-details"><div class="sp-list-wrapper"><dl class="sp-official-details">';

foreach( $data as $label => $value ):

	$output .= '<dt>' . $label . '</dt><dd>' . $value . '</dd>';

endforeach;

$output .= '</dl></div></div>';

echo $output;
