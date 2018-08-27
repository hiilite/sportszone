<?php
/**
 * Calendar Columns
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version		2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Calendar_Columns
 */
class SZ_Meta_Box_Calendar_Columns {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$selected = (array) get_post_meta( $post->ID, 'sz_columns', true );
		$title_format = get_option( 'sportszone_event_list_title_format', 'title' );
		$time_format = get_option( 'sportszone_event_list_time_format', 'combined' );

		if ( is_array( $selected ) ) {
			$selected = array_filter( $selected );
		}

		$columns = array();

		if ( 'teams' === $title_format ) {
			$columns[ 'event' ] = __( 'Home', 'sportszone' ) . ' | ' . __( 'Away', 'sportszone' );
		} elseif ( 'homeaway' === $title_format ) {
			$columns[ 'event' ] = __( 'Teams', 'sportszone' );
		} else {
			$columns[ 'event' ] = __( 'Title', 'sportszone' );
		}

		if ( 'time' === $time_format || 'separate' === $time_format ) {
			$columns['time'] = __( 'Time', 'sportszone' );
		} elseif ( 'combined' === $time_format ) {
			$columns['time'] = __( 'Time/Results', 'sportszone' );
		}

		if ( 'results' === $time_format || 'separate' === $time_format ) {
			$columns['results'] = __( 'Results', 'sportszone' );
		}

		$columns['league'] = __( 'League', 'sportszone' );
		$columns['season'] = __( 'Season', 'sportszone' );
		$columns['venue'] = __( 'Venue', 'sportszone' );
		$columns['article'] = __( 'Article', 'sportszone' );
		$columns['day'] = __( 'Match Day', 'sportszone' );

		$columns = apply_filters( 'sportszone_calendar_columns', $columns );
		?>
		<div class="sz-instance">
			<ul class="categorychecklist form-no-clear">
			<?php
				foreach ( $columns as $key => $label ) {
					?>
					<li>
						<label>
							<input type="checkbox" name="sz_columns[]" value="<?php echo $key; ?>" id="sz_columns_<?php echo $key; ?>" <?php checked( ! is_array( $selected ) || in_array( $key, $selected ) ); ?>>
							<?php echo $label; ?>
						</label>
					</li>
					<?php
				}
			?>
		</div>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_columns', sz_array_value( $_POST, 'sz_columns', array() ) );
	}
}