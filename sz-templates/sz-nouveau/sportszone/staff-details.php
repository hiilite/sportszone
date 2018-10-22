<?php
/**
 * Staff Details
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version     2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( get_option( 'sportszone_staff_show_details', 'yes' ) === 'no' ) return;

if ( ! isset( $id ) )
	$id = get_the_ID();

$defaults = array(
	'show_nationality' => get_option( 'sportszone_staff_show_nationality', 'yes' ) == 'yes' ? true : false,
	'show_current_teams' => get_option( 'sportszone_staff_show_current_teams', 'yes' ) == 'yes' ? true : false,
	'show_past_teams' => get_option( 'sportszone_staff_show_past_teams', 'yes' ) == 'yes' ? true : false,
	'show_nationality_flags' => get_option( 'sportszone_staff_show_flags', 'yes' ) == 'yes' ? true : false,
	'link_teams' => get_option( 'sportszone_link_teams', 'no' ) == 'yes' ? true : false,
	'abbreviate_teams' => get_option( 'sportszone_abbreviate_teams', 'yes' ) === 'yes' ? true : false,
);

extract( $defaults, EXTR_SKIP );

$countries = SP()->countries->countries;

$staff = new SP_Staff( $id );

$nationalities = $staff->nationalities();
$current_teams = $staff->current_teams();
$past_teams = $staff->past_teams();

$data = array();
if ( $show_nationality && $nationalities && is_array( $nationalities ) ):
	$values = array();
	foreach ( $nationalities as $nationality ):
		if ( 2 == strlen( $nationality ) ):
			$legacy = SP()->countries->legacy;
			$nationality = strtolower( $nationality );
			$nationality = sz_array_value( $legacy, $nationality, null );
		endif;
		$country_name = sz_array_value( $countries, $nationality, null );
		$values[] = $country_name ? ( $show_nationality_flags ? '<img src="' . plugin_dir_url( SP_PLUGIN_FILE ) . 'assets/images/flags/' . strtolower( $nationality ) . '.png" alt="' . $nationality . '"> ' : '' ) . $country_name : '&mdash;';
	endforeach;
	$data[ __( 'Nationality', 'sportszone' ) ] = implode( '<br>', $values );
endif;

if ( $show_current_teams && $current_teams ):
	$teams = array();
	foreach ( $current_teams as $team ):
		$team_name = sz_get_team_name( $team, $abbreviate_teams );
		if ( $link_teams ) $team_name = '<a href="' . get_post_permalink( $team ) . '">' . $team_name . '</a>';
		$teams[] = $team_name;
	endforeach;
	$data[ __( 'Current Team', 'sportszone' ) ] = implode( ', ', $teams );
endif;

if ( $show_past_teams && $past_teams ):
	$teams = array();
	foreach ( $past_teams as $team ):
		$team_name = sz_get_team_name( $team, $abbreviate_teams );
		if ( $link_teams ) $team_name = '<a href="' . get_post_permalink( $team ) . '">' . $team_name . '</a>';
		$teams[] = $team_name;
	endforeach;
	$data[ __( 'Past Teams', 'sportszone' ) ] = implode( ', ', $teams );
endif;

$data = apply_filters( 'sportszone_staff_details', $data, $id );

if ( empty( $data ) )
	return;

$output = '<div class="sp-list-wrapper">' .
	'<dl class="sp-staff-details">';

foreach( $data as $label => $value ):

	$output .= '<dt>' . $label . '</dt><dd>' . $value . '</dd>';

endforeach;

$output .= '</dl></div>';
?>
<div class="sp-template sp-template-staff-details sp-template-details">
	<?php echo $output; ?>
</div>