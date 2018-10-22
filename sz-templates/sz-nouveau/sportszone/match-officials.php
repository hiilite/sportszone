<?php
/**
 * Event Officials
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version   2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! isset( $id ) )
	$id = get_the_ID();

$event = new SZ_Match( $id );

// Get appointed officials from event
$data = $event->appointments();

// Return if no officials are in event
if ( empty( $data ) ) return;

// The first row should be column labels
$labels = $data[0];
unset( $data[0] );

$link_officials = get_option( 'sportszone_link_officials', 'no' ) == 'yes' ? true : false;
$format = get_option( 'sportszone_event_officials_format', 'table' );

switch ( $format ):
	case 'list':
		sz_get_template( 'event-officials-list.php', array(
			'labels' => $labels,
			'data' => $data,
			'link_officials' => $link_officials,
		) );
		break;
	default:
		sz_get_template( 'event-officials-table.php', array(
			'labels' => $labels,
			'data' => $data,
			'link_officials' => $link_officials,
		) );
endswitch;