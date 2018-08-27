<?php
/**
 * Player Details
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version		2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Player_Details
 */
class SZ_Meta_Box_Player_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'sportszone_save_data', 'sportszone_meta_nonce' );
		$continents = SP()->countries->continents;

		$number = get_post_meta( $post->ID, 'sz_number', true );
		$nationalities = get_post_meta( $post->ID, 'sz_nationality', false );
		foreach ( $nationalities as $index => $nationality ):
			if ( 2 == strlen( $nationality ) ):
				$legacy = SP()->countries->legacy;
				$nationality = strtolower( $nationality );
				$nationality = sz_array_value( $legacy, $nationality, null );
				$nationalities[ $index ] = $nationality;
			endif;
		endforeach;

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

		if ( taxonomy_exists( 'sz_position' ) ):
			$positions = get_the_terms( $post->ID, 'sz_position' );
			$position_ids = array();
			if ( $positions ):
				foreach ( $positions as $position ):
					$position_ids[] = $position->term_id;
				endforeach;
			endif;
		endif;
		
		$teams = get_posts( array( 'post_type' => 'sz_team', 'posts_per_page' => -1 ) );
		$past_teams = array_filter( get_post_meta( $post->ID, 'sz_past_team', false ) );
		$current_teams = array_filter( get_post_meta( $post->ID, 'sz_current_team', false ) );
		?>

		<p><strong><?php _e( 'Squad Number', 'sportszone' ); ?></strong></p>
		<p><input type="text" size="4" id="sz_number" name="sz_number" value="<?php echo $number; ?>"></p>

		<p><strong><?php _e( 'Nationality', 'sportszone' ); ?></strong></p>
		<p><select id="sz_nationality" name="sz_nationality[]" data-placeholder="<?php printf( __( 'Select %s', 'sportszone' ), __( 'Nationality', 'sportszone' ) ); ?>" class="widefat chosen-select<?php if ( is_rtl() ): ?> chosen-rtl<?php endif; ?>" multiple="multiple">
			<option value=""></option>
			<?php foreach ( $continents as $continent => $countries ): ?>
				<optgroup label="<?php echo $continent; ?>">
					<?php foreach ( $countries as $code => $country ): ?>
						<option value="<?php echo $code; ?>" <?php selected ( in_array( $code, $nationalities ) ); ?>><?php echo $country; ?></option>
					<?php endforeach; ?>
				</optgroup>
			<?php endforeach; ?>
		</select></p>

		<?php if ( taxonomy_exists( 'sz_position' ) ) { ?>
			<p><strong><?php _e( 'Positions', 'sportszone' ); ?></strong></p>
			<p><?php
			$args = array(
				'taxonomy' => 'sz_position',
				'name' => 'tax_input[sz_position][]',
				'selected' => $position_ids,
				'values' => 'term_id',
				'placeholder' => sprintf( __( 'Select %s', 'sportszone' ), __( 'Positions', 'sportszone' ) ),
				'class' => 'widefat',
				'property' => 'multiple',
				'chosen' => true,
			);
			sz_dropdown_taxonomies( $args );
			?></p>
		<?php } ?>

		<p><strong><?php _e( 'Current Teams', 'sportszone' ); ?></strong></p>
		<p><?php
		$args = array(
			'post_type' => 'sz_team',
			'name' => 'sz_current_team[]',
			'selected' => $current_teams,
			'values' => 'ID',
			'placeholder' => sprintf( __( 'Select %s', 'sportszone' ), __( 'Teams', 'sportszone' ) ),
			'class' => 'sz-current-teams widefat',
			'property' => 'multiple',
			'chosen' => true,
		);
		sz_dropdown_pages( $args );
		?></p>

		<p><strong><?php _e( 'Past Teams', 'sportszone' ); ?></strong></p>
		<p><?php
		$args = array(
			'post_type' => 'sz_team',
			'name' => 'sz_past_team[]',
			'selected' => $past_teams,
			'values' => 'ID',
			'placeholder' => sprintf( __( 'Select %s', 'sportszone' ), __( 'Teams', 'sportszone' ) ),
			'class' => 'sz-past-teams widefat',
			'property' => 'multiple',
			'chosen' => true,
		);
		sz_dropdown_pages( $args );
		?></p>

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
		<?php
			echo "<p><a id='request-link' href='/request-a-league/'>Don’t see your league? Request it to be added</a></p>"	
		?>
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
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		
		update_post_meta( $post_id, 'sz_number', esc_attr( sz_array_value( $_POST, 'sz_number', '' ) ) );
		
		wp_set_post_terms( $post_id, $_POST['tax_input']['sz_position'], 'sz_position', false);
		wp_set_post_terms( $post_id, $_POST['tax_input']['sz_league'], 'sz_league', false);
		wp_set_post_terms( $post_id, $_POST['tax_input']['sz_season'], 'sz_season', false);
		
		sz_update_post_meta_recursive( $post_id, 'sz_nationality', sz_array_value( $_POST, 'sz_nationality', array() ) );
		sz_update_post_meta_recursive( $post_id, 'sz_current_team', sz_array_value( $_POST, 'sz_current_team', array() ) );
		sz_update_post_meta_recursive( $post_id, 'sz_past_team', sz_array_value( $_POST, 'sz_past_team', array() ) );
		sz_update_post_meta_recursive( $post_id, 'sz_team', array_merge( array( sz_array_value( $_POST, 'sz_current_team', array() ) ), sz_array_value( $_POST, 'sz_past_team', array() ) ) );
	}
}
