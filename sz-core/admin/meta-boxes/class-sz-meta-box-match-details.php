<?php
/**
 * Event Details
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     2.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Match_Details
 */
class SZ_Meta_Box_Match_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$day = get_post_meta( $post->ID, 'sz_day', true );
		$taxonomies = get_object_taxonomies( 'sz_match' );
		$minutes = get_post_meta( $post->ID, 'sz_minutes', true );
		?>
		<?php do_action( 'sportszone_event_details_meta_box', $post ); ?>
		<div class="sz-event-day-field">
			<p><strong><?php _e( 'Match Day', 'sportszone' ); ?></strong> <span class="dashicons dashicons-editor-help sz-desc-tip" title="<?php _e( 'Optional', 'sportszone' ); ?>"></span></p>
			<p>
				<input name="sz_day" type="text" class="medium-text" placeholder="<?php _e( 'Default', 'sportszone' ); ?>" value="<?php echo esc_attr( $day ); ?>">
			</p>
		</div>
		<div class="sz-event-minutes-field">
			<p><strong><?php _e( 'Full Time', 'sportszone' ); ?></strong></p>
			<p>
				<input name="sz_minutes" type="number" step="1" min="0" class="small-text" placeholder="<?php echo get_option( 'sportszone_event_minutes', 90 ); ?>" value="<?php echo esc_attr( $minutes ); ?>">
				<?php _e( 'mins', 'sportszone' ); ?>
			</p>
		</div>
		<?php
		foreach ( $taxonomies as $taxonomy ) {
			if ( 'sz_venue' == $taxonomy ) continue;
			sz_taxonomy_field( $taxonomy, $post, true, true, __( 'None', 'sportszone' ) );
		}
		?>
		<div class="sz-event-sz_venue-field">
			<p><strong><?php _e( 'Venue', 'sportszone' ); ?></strong></p>
			<p>
				<?php
				$terms = get_the_terms( $post->ID, 'sz_venue' );
				$args = array(
					'taxonomy' => 'sz_venue',
					'name' => 'tax_input[sz_venue][]',
					'class' => 'sz-has-dummy',
					'selected' => sz_get_the_term_id_or_meta( $post->ID, 'sz_venue' ),
					'values' => 'term_id',
					'show_option_none' => __( '&mdash; Not set &mdash;', 'sportszone' ),
					'chosen' => true,
				);
				if ( in_array( 'sz_venue', apply_filters( 'sportszone_event_auto_taxonomies', array( 'sz_venue' ) ) ) ) {
					$args['show_option_all'] = __( '(Auto)', 'sportszone' );
				}
				if ( ! sz_dropdown_taxonomies( $args ) ) {
					sz_taxonomy_adder( 'sz_venue', 'sz_match', __( 'Add New', 'sportszone' ) );
				}
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_day', sz_array_value( $_POST, 'sz_day', null ) );
		update_post_meta( $post_id, 'sz_minutes', sz_array_value( $_POST, 'sz_minutes', get_option( 'sportszone_event_minutes', 90 ) ) );
   		$venues = array_filter( sz_array_value( sz_array_value( $_POST, 'tax_input', array() ), 'sz_venue', array() ) );
		if ( empty( $venues ) ) {
			$teams = sz_array_value( $_POST, 'sz_team', array() );
			$team = reset( $teams );
			$venue = sz_get_the_term_id( $team, 'sz_venue' );
			wp_set_post_terms( $post_id, $venue, 'sz_venue' );
		}
	}
}
