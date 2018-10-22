<?php
/**
 * Event Details
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version   2.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( get_option( 'sportszone_match_show_details', 'yes' ) === 'no' ) return;

if ( ! isset( $id ) )
	$id = get_the_ID();

$scrollable = get_option( 'sportszone_enable_scrollable_tables', 'yes' ) == 'yes' ? true : false;

$data = array();

if ( 'yes' === get_option( 'sportszone_match_show_date', 'yes' ) ) {
	$date = get_the_time( get_option('date_format'), $id );
	$data[ __( 'Date', 'sportszone' ) ] = $date;
}

if ( 'yes' === get_option( 'sportszone_match_show_time', 'yes' ) ) {
	$time = get_the_time( get_option('time_format'), $id );
	$data[ __( 'Time', 'sportszone' ) ] = apply_filters( 'sportszone_match_time', $time, $id );
}

$taxonomies = apply_filters( 'sportszone_match_taxonomies', array( 'sz_league' => null ) );

foreach ( $taxonomies as $taxonomy => $post_type ):
	$terms = get_the_terms( $id, $taxonomy );
	if ( $terms ):
		$obj = get_taxonomy( $taxonomy );
		$term = array_shift( $terms );
		$data[ $obj->labels->singular_name ] = $term->name;
	endif;
endforeach;

if ( 'yes' === get_option( 'sportszone_match_show_day', 'yes' ) ) {
	$day = get_post_meta( $id, 'sz_day', true );
	if ( '' !== $day ) {
		$data[ __( 'Match Day', 'sportszone' ) ] = $day;
	}
}

if ( 'yes' === get_option( 'sportszone_match_show_full_time', 'yes' ) ) {
	$full_time = get_post_meta( $id, 'sz_minutes', true );
	if ( '' === $full_time ) {
		$full_time = get_option( 'sportszone_match_minutes', 90 );
	}
	$data[ __( 'Full Time', 'sportszone' ) ] = $full_time . '\'';
}

$data = apply_filters( 'sportszone_match_details', $data, $id );

if ( ! sizeof( $data ) ) return;
?>
<div class="sp-template sp-template-match-details">
	<h4 class="sp-table-caption"><?php _e( 'Details', 'sportszone' ); ?></h4>
	<div class="sp-table-wrapper">
		<table class="sp-match-details sp-data-table<?php if ( $scrollable ) { ?> sp-scrollable-table<?php } ?>">
			<thead>
				<tr>
					<?php $i = 0; foreach( $data as $label => $value ):	?>
						<th><?php echo $label; ?></th>
					<?php $i++; endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<tr class="odd">
					<?php $i = 0; foreach( $data as $value ):	?>
						<td><?php echo $value; ?></td>
					<?php $i++; endforeach; ?>
				</tr>
			</tbody>
		</table>
	</div>
</div>