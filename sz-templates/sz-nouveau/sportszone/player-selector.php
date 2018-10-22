<?php
/**
 * Player Dropdown
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version     2.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( get_option( 'sportszone_player_show_selector', 'yes' ) === 'no' ) return;

if ( ! isset( $id ) )
	$id = get_the_ID();

$league_ids = sz_get_the_term_ids( $id, 'sz_league' );
$season_ids = sz_get_the_term_ids( $id, 'sz_season' );
$team = get_post_meta( $id, 'sz_current_team', true );

$args = array(
	'post_type' => 'sz_player',
	'numberposts' => 500,
	'posts_per_page' => 500,
	'meta_key' => 'sz_number',
	'orderby' => 'meta_value_num',
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

$players = get_posts( $args );

$options = array();

if ( $players && is_array( $players ) ):
	foreach ( $players as $player ):
		$name = $player->post_title;
		$number = get_post_meta( $player->ID, 'sz_number', true );
		if ( isset( $number ) && '' !== $number ):
			$name = $number . '. ' . $name;
		endif;
		$options[] = '<option value="' . get_post_permalink( $player->ID ) . '" ' . selected( $player->ID, $id, false ) . '>' . $name . '</option>';
	endforeach;
endif;

if ( sizeof( $options ) > 1 ):
	?>
	<div class="sp-template sp-template-player-selector sp-template-profile-selector">
		<select class="sp-profile-selector sp-player-selector sp-selector-redirect">
			<?php echo implode( $options ); ?>
		</select>
	</div>
	<?php
endif;