<?php
/**
 * Staff Details
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version		2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Staff_Details
 */
class SZ_Meta_Box_Staff_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'sportszone_save_data', 'sportszone_meta_nonce' );
		$continents = SportsZone()->countries->continents;

		$nationalities = get_post_meta( $post->ID, 'sz_nationality', false );
		foreach ( $nationalities as $index => $nationality ):
			if ( 2 == strlen( $nationality ) ):
				$legacy = SportsZone()->countries->legacy;
				$nationality = strtolower( $nationality );
				$nationality = sz_array_value( $legacy, $nationality, null );
				$nationalities[ $index ] = $nationality;
			endif;
		endforeach;

		$leagues = get_the_terms( $post->ID, 'sz_league' );
		$league_ids = array();
		if ( $leagues ):
			foreach ( $leagues as $league ):
				$league_ids[] = $league->term_id;
			endforeach;
		endif;

		$seasons = get_the_terms( $post->ID, 'sz_season' );
		$season_ids = array();
		if ( $seasons ):
			foreach ( $seasons as $season ):
				$season_ids[] = $season->term_id;
			endforeach;
		endif;

		$roles = get_the_terms( $post->ID, 'sz_role' );
		$role_ids = wp_list_pluck( $roles, 'term_id' );
		
		$teams = get_posts( array( 'post_type' => 'sz_team', 'posts_per_page' => -1 ) );
		$past_teams = array_filter( get_post_meta( $post->ID, 'sz_past_team', false ) );
		$current_teams = array_filter( get_post_meta( $post->ID, 'sz_current_team', false ) );
		?>
		<p><strong><?php _e( 'Jobs', 'sportszone' ); ?></strong></p>
		<p><?php
		$args = array(
			'taxonomy' => 'sz_role',
			'name' => 'tax_input[sz_role][]',
			'selected' => $role_ids,
			'values' => 'term_id',
			'placeholder' => sprintf( __( 'Select %s', 'sportszone' ), __( 'Jobs', 'sportszone' ) ),
			'class' => 'widefat',
			'property' => 'multiple',
			'chosen' => true,
		);
		if ( ! sz_dropdown_taxonomies( $args ) ):
			sz_taxonomy_adder( 'sz_role', 'sz_staff', __( 'Add New', 'sportszone' )  );
		endif;
		?></p>

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
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		sz_update_post_meta_recursive( $post_id, 'sz_nationality', sz_array_value( $_POST, 'sz_nationality', array() ) );
		sz_update_post_meta_recursive( $post_id, 'sz_current_team', sz_array_value( $_POST, 'sz_current_team', array() ) );
		sz_update_post_meta_recursive( $post_id, 'sz_past_team', sz_array_value( $_POST, 'sz_past_team', array() ) );
		sz_update_post_meta_recursive( $post_id, 'sz_team', array_merge( array( sz_array_value( $_POST, 'sz_current_team', array() ) ), sz_array_value( $_POST, 'sz_past_team', array() ) ) );
	}
}