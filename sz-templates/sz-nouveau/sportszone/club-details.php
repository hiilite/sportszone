<?php
/**
 * Team Details
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version   2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( get_option( 'sportszone_team_show_details', 'no' ) === 'no' ) return;

if ( ! isset( $id ) )
	$id = get_the_ID();

$data = array();

$terms = get_the_terms( $id, 'sz_league' );
if ( $terms ):
	$leagues = array();
	foreach ( $terms as $term ):
		$leagues[] = $term->name;
	endforeach;
	$data[ __( 'Leagues', 'sportszone' ) ] = implode( ', ', $leagues );
endif;

$terms = get_the_terms( $id, 'sz_season' );
if ( $terms ):
	$seasons = array();
	foreach ( $terms as $term ):
		$seasons[] = $term->name;
	endforeach;
	$data[ __( 'Seasons', 'sportszone' ) ] = implode( ', ', $seasons );
endif;

$terms = get_the_terms( $id, 'sz_venue' );
if ( $terms ):
	if ( get_option( 'sportszone_club_link_venues', 'no' ) === 'yes' ):
		$data[ __( 'Home', 'sportszone' ) ] = get_the_term_list( $id, 'sz_venue' );
	else:
		$venues = array();
		foreach ( $terms as $term ):
			$venues[] = $term->name;
		endforeach;
		$data[ __( 'Home', 'sportszone' ) ] = implode( ', ', $venues );
	endif;
endif;

$output = '<div class="sp-list-wrapper">' .
	'<dl class="sp-club-details">';

foreach( $data as $label => $value ):

	$output .= '<dt>' . $label . '</dt><dd>' . $value . '</dd>';

endforeach;

$output .= '</dl></div>';
?>
<div class="sp-template sp-template-club-details sp-template-details">
	<?php echo $output; ?>
</div>