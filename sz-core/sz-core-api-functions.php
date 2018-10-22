<?php
/**
 * SportsZone API Functions
 *
 * API functions for admin and front-end templates.
 *
 * @author 		ThemeBoy
 * @category 	Core
 * @package 	SportsZone/Functions
 * @version   2.5.5
 * Added Clubs
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * General functions
 */

function sz_post_exists( $post = 0 ) {
	return is_string( get_post_status( $post ) );
}

function sz_get_time( $post = 0, $format = null ) {
	if ( null == $format ) $format = get_option( 'time_format' );
	return get_post_time( $format, false, $post, true );
}

function sz_the_time( $post = 0, $format = null ) {
	echo sz_get_time( $post, $format );
}

function sz_get_date( $post = 0, $format = null ) {
	if ( null == $format ) $format = get_option( 'date_format' );
	return get_post_time( $format, false, $post, true );
}

function sz_the_date( $post = 0, $format = null ) {
	echo sz_get_date( $post, $format );
}

function sz_get_posts( $post_type = 'post', $args = array() ) {
	$args = array_merge( array(
		'post_type' => $post_type,
		'numberposts' => -1,
		'posts_per_page' => -1,
	), $args );
	return get_posts( $args );
}

function sz_get_leagues( $post = 0, $ids = true ) {
	$terms = get_the_terms( $post, 'sz_league' );
	if ( $terms && $ids ) $terms = wp_list_pluck( $terms, 'term_id' );
	return $terms;
}

function sz_get_seasons( $post = 0, $ids = true ) {
	$terms = get_the_terms( $post, 'sz_season' );
	if ( $terms && $ids ) $terms = wp_list_pluck( $terms, 'term_id' );
	return $terms;
}

function sz_the_leagues( $post = 0, $delimiter = ', ' ) {
	$terms = sz_get_leagues( $post, false );
	$arr = array();
	if ( $terms ) {
		foreach ( $terms as $term ):
			$arr[] = $term->name;
		endforeach;
	}
	echo implode( $delimiter, $arr ); 
}

function sz_the_seasons( $post = 0, $delimiter = ', ' ) {
	$terms = sz_get_seasons( $post, false );
	$arr = array();
	if ( $terms ) {
		foreach ( $terms as $term ):
			$arr[] = $term->name;
		endforeach;
	}
	echo implode( $delimiter, $arr ); 
}

/*
 * Event functions
 */

function sz_get_status( $post = 0 ) {
	$event = new SZ_Match( $post );
	return $event->status();
}

function sz_get_results( $post = 0 ) {
	$event = new SZ_Match( $post );
	return $event->results();
}

function sz_get_clubs( $post = 0 ) {
	return get_post_meta( $post, 'sz_clubs' );
}

function sz_get_teams( $post = 0 ) {
	return get_post_meta( $post, 'sz_team' );
}

function sz_get_main_result_option() {
	$main_result = get_option( 'sportszone_primary_result', null );
	if ( $main_result ) return $main_result;
	$results = get_posts( array( 'post_type' => 'sz_result', 'posts_per_page' => 1, 'orderby' => 'menu_order', 'order' => 'DESC' ) );
	if ( ! $results ) return null;
	$result = reset( $results );
	$slug = $result->post_name;
	return $slug;
}

function sz_get_main_results( $post = 0 ) {
	$event = new SZ_Match( $post );
	return $event->main_results();
}

function sz_the_main_results( $post = 0, $delimiter = '-' ) {
	$results = sz_get_main_results( $post );
	echo implode( $delimiter, $results );
}

function sz_update_main_results( $post = 0, $results = array() ) {
	$event = new SZ_Match( $post );
	return $event->update_main_results ( $results );
}

function sz_get_main_results_or_time( $post = 0 ) {
	$results = sz_get_main_results( $post );
	if ( sizeof( $results ) ) {
		return $results;
	} else {
		return array( sz_get_time( $post ) );
	}
}

function sz_the_main_results_or_time( $post = 0, $delimiter = '-' ) {
	echo implode( $delimiter, sz_get_main_results_or_time( $post ) );
}

function sz_get_outcome( $post = 0 ) {
	$event = new SZ_Match( $post );
	return $event->outcome( true );
}

function sz_get_outcomes( $post = 0 ) {
	$event = new SZ_Match( $post );
	return $event->outcome( false );
}

function sz_get_winner( $post = 0 ) {
	$event = new SZ_Match( $post );
	return $event->winner();
}

function sz_get_main_performance_option() {
	$main_performance = get_option( 'sportszone_primary_performance', null );
	if ( $main_performance ) return $main_performance;
	$options = get_posts( array( 'post_type' => 'sz_performance', 'posts_per_page' => 1, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
	if ( ! $options ) return null;
	$performance = reset( $options );
	$slug = $performance->post_name;
	return $slug;
}

function sz_get_performance( $post = 0 ) {
	$event = new SZ_Match( $post );
	return $event->performance();
}

function sz_get_singular_name( $post = 0 ) {
	$singular = get_post_meta( $post, 'sz_singular', true );
	if ( '' !== $singular ) {
		return $singular;
	} else {
		return get_the_title( $post );
	}
}

function sz_event_logos( $post = 0 ) {
	sz_get_template( 'event-logos.php', array( 'id' => $post ) );
}

function sz_event_video( $post = 0 ) {
	sz_get_template( 'event-video.php', array( 'id' => $post ) );
}

function sz_event_results( $post = 0 ) {
	sz_get_template( 'event-results.php', array( 'id' => $post ) );
}

function sz_event_details( $post = 0 ) {
	sz_get_template( 'event-details.php', array( 'id' => $post ) );
}

function sz_event_venue( $post = 0 ) {
	sz_get_template( 'event-venue.php', array( 'id' => $post ) );
}

function sz_event_staff( $post = 0 ) {
	sz_get_template( 'event-staff.php', array( 'id' => $post ) );
}

function sz_event_performance( $post = 0 ) {
	sz_get_template( 'event-performance.php', array( 'id' => $post ) );
}

/*
 * Calendar functions
 */

function sz_get_calendar( $post = 0 ) {
	$calendar = new SP_Calendar( $post );
	return $calendar->data();
}

function sz_event_calendar( $post = 0 ) {
	sz_get_template( 'event-calendar.php', array( 'id' => $post ) );
}

function sz_event_list( $post = 0 ) {
	sz_get_template( 'event-list.php', array( 'id' => $post ) );
}

function sz_event_blocks( $post = 0 ) {
	sz_get_template( 'event-blocks.php', array( 'id' => $post ) );
}

/*
 * Team functions
 */

function sz_has_logo( $post = 0 ) {
	// TODO: Change to pull group avatar
	return has_post_thumbnail ( $post );
}

function sz_get_logo( $post = 0, $size = 'icon', $attr = array() ) {
	return get_the_post_thumbnail( $post, 'sportszone-fit-' . $size, $attr );
}

function sz_get_logo_url( $post = 0, $size = 'icon' ) {
	$thumbnail_id = get_post_thumbnail_id( $post );
	$src = wp_get_attachment_image_src( $thumbnail_id, $size, false );
	return $src[0];
}

function sz_get_abbreviation( $post = 0 ) {
	// TODO: Add abreviations to groups
	return get_post_meta( $post, 'sz_abbreviation', true );
}

function sz_get_venues( $post = 0, $ids = true ) {
	$terms = get_the_terms( $post, 'sz_venue' );
	if ( $terms && $ids ) $terms = wp_list_pluck( $terms, 'term_id' );
	return $terms;
}

function sz_the_venues( $post = 0, $delimiter = ', ' ) {
	$terms = sz_get_venues( $post, false );
	$arr = array();
	if ( $terms ) {
		foreach ( $terms as $term ):
			$arr[] = $term->name;
		endforeach;
	}
	echo implode( $delimiter, $arr ); 
}

function sz_is_home_venue( $post = 0, $event = 0 ) {
	$pv = sz_get_venues( $post );
	$ev = sz_get_venues( $event );
	if ( is_array( $pv ) && is_array( $ev ) && sizeof( array_intersect( $pv, $ev ) ) ) {
		return true;
	} else {
		return false;
	}
}

function sz_the_abbreviation( $post = 0 ) {
	echo sz_get_abbreviation( $post );
}

function sz_the_logo( $post = 0, $size = 'icon', $attr = array() ) {
	echo sz_get_logo( $post, $size, $attr );
}

function sz_club_logo( $post = 0 ) {
	sz_get_template( 'club-logo.php', array( 'id' => $post ) );
}

function sz_team_logo( $post = 0 ) {
	sz_get_template( 'team-logo.php', array( 'id' => $post ) );
}

function sz_get_short_name( $post = 0 ) {
	// TODO: make a abbreviation system
	/*$abbreviation = sz_get_abbreviation( $post, 'sz_abbreviation', true );
	if ( $abbreviation ) {
		return $abbreviation;
	} else {
		return get_the_title( $post );
	}*/
	return sz_get_team_name( $post );
}

function sz_short_name( $post = 0 ) {
	echo sz_get_short_name( $post );
}
 
function sz_get_club_name( $post = 0, $short = true ) {
	return sz_get_team_name( $post );
}

function sz_get_team_name( $post = 0, $short = true ) {
	$group = groups_get_group( array( 'group_id' => $post) );
	if ( $short ) {
		return $group->name;
	} else {
		return $group->name;
	}
}

function sz_club_details( $post = 0 ) {
	sz_get_template( 'club-details.php', array( 'id' => $post ) );
}

function sz_team_details( $post = 0 ) {
	sz_get_template( 'team-details.php', array( 'id' => $post ) );
}

function sz_club_link( $post = 0 ) {
	sz_get_template( 'club-link.php', array( 'id' => $post ) );
}

function sz_team_link( $post = 0 ) {
	sz_get_template( 'team-link.php', array( 'id' => $post ) );
}

function sz_club_lists( $post = 0 ) {
	sz_get_template( 'club-lists.php', array( 'id' => $post ) );
}

function sz_team_lists( $post = 0 ) {
	sz_get_template( 'team-lists.php', array( 'id' => $post ) );
}

function sz_club_tables( $post = 0 ) {
	sz_get_template( 'club-tables.php', array( 'id' => $post ) );
}

function sz_team_tables( $post = 0 ) {
	sz_get_template( 'team-tables.php', array( 'id' => $post ) );
}

/*
 * League Table functions
 */

function sz_get_table( $post = 0 ) {
	$table = new SP_League_Table( $post );
	return $table->data();
}

function sz_league_table( $post = 0 ) {
	sz_get_template( 'league-table.php', array( 'id' => $post ) );
}

/*
 * Player functions
 */

function sz_get_player_number( $post = 0 ) {
	return get_post_meta( $post, 'sz_number', true );
}

function sz_get_player_name( $post = 0 ) {
	// TODO: pull username  
	return apply_filters( 'sportszone_player_name', sz_core_get_user_displayname( $post ), $post );
}

function sz_get_player_name_with_number( $post = 0, $prepend = '', $append = '. ' ) {
	$name = sz_get_player_name( $post );
	$number = sz_get_player_number( $post );
	if ( isset( $number ) && '' !== $number ) {
		return apply_filters( 'sportszone_player_name_with_number', $prepend . $number . $append . $name, $post );
	} else {
		return $name;
	}
}

function sz_get_player_name_then_number( $post = 0, $prepend = ' (', $append = ')' ) {
	$name = sz_get_player_name( $post );
	$number = sz_get_player_number( $post );
	if ( isset( $number ) && '' !== $number ) {
		return apply_filters( 'sportszone_player_name_then_number', $name . $prepend . $number . $append, $post );
	} else {
		return $name;
	}
}

function sz_player_details( $post = 0 ) {
	sz_get_template( 'player-details.php', array( 'id' => $post ) );
}

function sz_player_photo( $post = 0 ) {
	sz_get_template( 'player-photo.php', array( 'id' => $post ) );
}

function sz_player_statistics( $post = 0 ) {
	sz_get_template( 'player-statistics.php', array( 'id' => $post ) );
}

/*
 * Player List functions
 */

function sz_get_list( $post = 0 ) {
	$list = new SP_Player_List( $post );
	return $list->data();
}

function sz_player_list( $post = 0 ) {
	sz_get_template( 'player-list.php', array( 'id' => $post ) );
}

/*
 * Staff functions
 */

function sz_staff_details( $post = 0 ) {
	sz_get_template( 'staff-details.php', array( 'id' => $post ) );
}

function sz_staff_photo( $post = 0 ) {
	sz_get_template( 'staff-photo.php', array( 'id' => $post ) );
}

/*
 * Venue functions
 */

function sz_venue_map( $term = 0 ) {
    $meta = get_option( "taxonomy_$term" );
	sz_get_template( 'venue-map.php', array( 'meta' => $meta ) );
}

/*
 *
 */

function sz_get_position_caption( $term = 0 ) {
    $meta = get_option( "taxonomy_$term" );
	$caption = sz_array_value( $meta, 'sz_caption', '' );
	if ( $caption ) {
		return $caption;
	} else {
		$term = get_term( $term, 'sz_position' );
		return $term->name;
	}

}
