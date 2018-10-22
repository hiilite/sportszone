<?php
/**
 * Event Logos
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version     2.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( get_option( 'sportszone_match_show_logos', 'yes' ) === 'no' ) return;

if ( ! isset( $id ) )
	$id = get_the_ID();

$teams = (array) get_post_meta( $id, 'sz_team' );
$teams = array_filter( $teams, 'sz_filter_positive' );

if ( ! $teams ) return;

$layout = get_option( 'sportszone_match_logos_format', 'inline' );

$show_team_names = get_option( 'sportszone_match_logos_show_team_names', 'yes' ) === 'yes' ? true : false;
$show_time = get_option( 'sportszone_match_logos_show_time', 'no' ) === 'yes' ? true : false;
$show_results = get_option( 'sportszone_match_logos_show_results', 'no' ) === 'yes' ? true : false;
$abbreviate_teams = get_option( 'sportszone_abbreviate_teams', 'yes' ) === 'yes' ? true : false;
$link_teams = get_option( 'sportszone_link_teams', 'no' ) === 'yes' ? true : false;

if ( $show_results ) {
	$results = sz_get_main_results( $id );
	if ( empty( $results ) ) {
		$show_results = false;
	} else {
		$show_time = false;
	}
} else {
	$results = array();
}

sz_get_template( 'match-logos-' . $layout . '.php', array(
	'id' => $id,
	'teams' => $teams,
	'results' => $results,
	'show_team_names' => $show_team_names,
	'show_time' => $show_time,
	'show_results' => $show_results,
	'abbreviate_teams' => $abbreviate_teams,
	'link_teams' => $link_teams,
) );

do_action( 'sportszone_after_match_logos', $id );