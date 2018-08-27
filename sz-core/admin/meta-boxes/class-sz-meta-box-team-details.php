<?php
/**
 * Team Details
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version		2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Team_Details
 */
class SZ_Meta_Box_Team_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'sportszone_save_data', 'sportszone_meta_nonce' );

		if ( taxonomy_exists( 'sz_league' ) ):
			$leagues = get_the_terms( $post->ID, 'sz_league' );
			$league_ids = array();
			if ( $leagues ):
				foreach ( $leagues as $league ):
					$league_ids[] = $league->term_id;
				endforeach;
			endif;
		endif;

		if ( taxonomy_exists( 'sz_season' ) ):
			$seasons = get_the_terms( $post->ID, 'sz_season' );
			$season_ids = array();
			if ( $seasons ):
				foreach ( $seasons as $season ):
					$season_ids[] = $season->term_id;
				endforeach;
			endif;
		endif;

		if ( taxonomy_exists( 'sz_venue' ) ):
			$venues = get_the_terms( $post->ID, 'sz_venue' );
			$venue_ids = array();
			if ( $venues ):
				foreach ( $venues as $venue ):
					$venue_ids[] = $venue->term_id;
				endforeach;
			endif;
		endif;

		$abbreviation = get_post_meta( $post->ID, 'sz_abbreviation', true );
		$redirect = get_post_meta( $post->ID, 'sz_redirect', true );
		$url = get_post_meta( $post->ID, 'sz_url', true );
		?>

		<?php if ( taxonomy_exists( 'sz_league' ) ) { ?>
		<p><strong><?php _e( 'Leagues', 'sportszone' ); ?></strong></p>
		<p><?php
		$args = array(
			'taxonomy' => 'sz_league',
			'name' => 'tax_input[sz_league][]',
			'selected' => $league_ids,
			'values' => 'term_id',
			'placeholder' => sprintf( __( 'Select %s', 'sportszone' ), __( 'Leagues', 'sportszone' ) ),
			'class' => 'widefat',
			'property' => 'multiple',
			'chosen' => true,
		);
		sz_dropdown_taxonomies( $args );
		?></p>
		<?php } ?>

		<?php if ( taxonomy_exists( 'sz_season' ) ) { ?>
		<p><strong><?php _e( 'Seasons', 'sportszone' ); ?></strong></p>
		<p><?php
		$args = array(
			'taxonomy' => 'sz_season',
			'name' => 'tax_input[sz_season][]',
			'selected' => $season_ids,
			'values' => 'term_id',
			'placeholder' => sprintf( __( 'Select %s', 'sportszone' ), __( 'Seasons', 'sportszone' ) ),
			'class' => 'widefat',
			'property' => 'multiple',
			'chosen' => true,
		);
		sz_dropdown_taxonomies( $args );
		?></p>
		<?php } ?>

		<?php if ( taxonomy_exists( 'sz_venue' ) ) { ?>
		<p><strong><?php _e( 'Home', 'sportszone' ); ?></strong></p>
		<p><?php
		$args = array(
			'taxonomy' => 'sz_venue',
			'name' => 'tax_input[sz_venue][]',
			'selected' => $venue_ids,
			'values' => 'term_id',
			'placeholder' => sprintf( __( 'Select %s', 'sportszone' ), __( 'Venue', 'sportszone' ) ),
			'class' => 'widefat',
			'property' => 'multiple',
			'chosen' => true,
		);
		sz_dropdown_taxonomies( $args );
		?></p>
		<?php } ?>

		<p><strong><?php _e( 'Site URL', 'sportszone' ); ?></strong></p>
		<p><input type="text" class="widefat" id="sz_url" name="sz_url" value="<?php echo esc_url( $url ); ?>"></p>
		<p><label class="selectit"><input type="checkbox" name="sz_redirect" value="1" <?php checked( $redirect ); ?>> <?php _e( 'Redirect', 'sportszone' ); ?></label></p>

		<p><strong><?php _e( 'Abbreviation', 'sportszone' ); ?></strong></p>
		<p><input type="text" id="sz_abbreviation" name="sz_abbreviation" value="<?php echo esc_attr( $abbreviation ); ?>"></p>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_url', esc_url( sz_array_value( $_POST, 'sz_url', '' ) ) );
		update_post_meta( $post_id, 'sz_redirect', sz_array_value( $_POST, 'sz_redirect', 0 ) );
		update_post_meta( $post_id, 'sz_abbreviation', esc_attr( sz_array_value( $_POST, 'sz_abbreviation', '' ) ) );
	}
}
