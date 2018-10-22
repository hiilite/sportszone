<?php
/**
 * Staff Dropdown
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version     2.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( get_option( 'sportszone_staff_show_selector', 'yes' ) === 'no' ) return;

if ( ! isset( $id ) )
	$id = get_the_ID();

$league_ids = sz_get_the_term_ids( $id, 'sz_league' );
$season_ids = sz_get_the_term_ids( $id, 'sz_season' );
$team = get_post_meta( $id, 'sz_current_team', true );

$args = array(
	'post_type' => 'sz_staff',
	'numberposts' => 500,
	'posts_per_page' => 500,
	'orderby' => 'title',
	'order' => 'ASC',
	'tax_query' => array(
		'relation' => 'AND',
	),
);

if ( $league_ids ):
	$args['tax_query'][] = array(
		'taxonomy' => 'sz_league',
		'field' => 'term_id',
		'terms' => $league_ids
	);
endif;

if ( $season_ids ):
	$args['tax_query'][] = array(
		'taxonomy' => 'sz_season',
		'field' => 'term_id',
		'terms' => $season_ids
	);
endif;

if ( $team ):
	$args['meta_query'] = array(
		array(
			'key' => 'sz_team',
			'value' => $team
		),
	);
endif;

$staffs = get_posts( $args );

$options = array();

if ( $staffs && is_array( $staffs ) ):
	foreach ( $staffs as $staff ):
		$options[] = '<option value="' . get_post_permalink( $staff->ID ) . '" ' . selected( $staff->ID, $id, false ) . '>' . $staff->post_title . '</option>';
	endforeach;
endif;

if ( sizeof( $options ) > 1 ):
	?>
	<div class="sp-template sp-template-staff-selector sp-template-profile-selector">
		<select class="sp-profile-selector sp-staff-selector sp-selector-redirect">
			<?php echo implode( $options ); ?>
		</select>
	</div>
	<?php
endif;