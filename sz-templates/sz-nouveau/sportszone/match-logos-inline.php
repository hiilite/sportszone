<?php
/**
 * Event Logos Inline
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version     2.2
 */

$team_logos = array();
$i = 0;
echo '<!--sz-templates/sz-nouveau/sportszone/match-logos-inline-->';
foreach ( $teams[0] as $team ):
	$group = groups_get_group( array( 'group_id' => $team) );
	if ( sz_has_groups( array('id' => $team )) ) :
	while ( sz_groups() ) :
			sz_the_group();
			$logo = sz_core_fetch_avatar( array( "item_id" => $team, "object" => "group", "type" => "full" ) );
	endwhile;
	endif;
	
	$alt = sizeof( $teams ) == 2 && $i % 2;
	// Add team name
	if ( $show_team_names ) {
		$logo = '<strong class="sz-team-name">' . sz_get_team_name( $team, $abbreviate_teams ) . '</strong> ' . $logo;
	}

	// Add link
	$logo = '<a href="' . sz_get_group_permalink( $group ) . '">' . $logo . '</a>';

	// Add result
	if ( $show_results && ! empty( $results ) ) {
		$team_result = array_shift( $results );
		$team_result = apply_filters( 'sportszone_match_logos_team_result', $team_result, $id, $team );
		if ( $alt ) {
			$logo = '<strong class="sz-team-result">' . $team_result . '</strong> ' . $logo;
		} else {
			$logo .= ' <strong class="sz-team-result">' . $team_result . '</strong>';
		}
	}

	// Add logo to array
	if ( '' !== $logo ) {
		$team_logos[] = '<span class="sz-team-logo">' . $logo . '</span>';
		$i++;
	}
endforeach;
$team_logos = array_filter( $team_logos );
if ( ! empty( $team_logos ) ):
	echo '<div class="sz-template sz-template-match-logos sz-template-match-logos-inline"><div class="sz-match-logos sz-match-logos-' . sizeof( $teams ) . '">';

	// Assign delimiter
	if ( $show_time && sizeof( $teams ) <= 2 ) {
		$delimiter = '<strong class="sz-match-logos-time sz-team-result">' . apply_filters( 'sportszone_match_time', get_the_time( get_option('time_format'), $id ), $id ) . '</strong>';
	} else {
		$delimiter = get_option( 'sportszone_match_teams_delimiter', 'vs' );
	}

	echo implode( ' ' . $delimiter . ' ', $team_logos );
	echo '</div></div>';
endif;