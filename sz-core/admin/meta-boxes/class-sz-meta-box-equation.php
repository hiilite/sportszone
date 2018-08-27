<?php
/**
 * Equation meta box functions
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version     2.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Equation
 */
class SZ_Meta_Box_Equation {

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_equation', implode( ' ', sz_array_value( $_POST, 'sz_equation', array() ) ) );
	}

	public static function builder( $title = 'f(x)', $equation = '', $groups = array() ) {
		if ( $title == '' ) $title = 'f(x)';
		$options = array(); // Multidimensional equation part options
		$parts = array(); // Flattened equation part options

		// Add groups to options
		foreach ( $groups as $group ):
			switch ( $group ):
				case 'player_event':
					$options[ 'Events' ] = array( '$matchesattended' => __( 'Attended', 'sportszone' ), '$matchesplayed' => __( 'Played', 'sportszone' ), '$matchesstarted' => __( 'Started', 'sportszone' ), '$matchssubbed' => __( 'Substituted', 'sportszone' ), '$matchminutes' => __( 'Minutes', 'sportszone' ) );
					break;
				case 'team_event':
					$options[ 'Events' ] = array( '$matchesplayed' => __( 'Played', 'sportszone' ), '$matchminutes' => __( 'Minutes', 'sportszone' ) );
					break;
				case 'result':
					$options[ 'Results' ] = self::optgroup( 'sz_result', array( 'for' => '(' . __( 'for', 'sportszone' ) . ')', 'against' => '(' . __( 'against', 'sportszone' ) . ')' ), null, false );
					break;
				case 'outcome':
					$options[ 'Outcomes' ] = self::optgroup( 'sz_outcome' );
					break;
				case 'preset':
					$options[ 'Presets' ] = array( '$gamesback' => __( 'Games Back', 'sportszone' ), '$homerecord' => __( 'Home Record', 'sportszone' ), '$awayrecord' => __( 'Away Record', 'sportszone' ), '$streak' => __( 'Streak', 'sportszone' ), '$form' => __( 'Form', 'sportszone' ), '$last5' => __( 'Last 5', 'sportszone' ), '$last10' => __( 'Last 10', 'sportszone' ) );
					break;
				case 'subset':
					$options[ 'Subsets' ] = array( '_home' => '@' . __( 'Home', 'sportszone' ), '_away' => '@' . __( 'Away', 'sportszone' ), '_venue' => '@' . __( 'Venue', 'sportszone' ) );
					break;
				case 'performance':
					$options[ 'Performance' ] = self::optgroup( 'sz_performance' );
					break;
				case 'metric':
					$options[ 'Metrics' ] = self::optgroup( 'sz_metric' );
					break;
			endswitch;
		endforeach;

		// Add operators to options
		$options[ 'Operators' ] = array( '+' => '&plus;', '-' => '&minus;', '*' => '&times;', '/' => '&divide;', '(' => '(', ')' => ')' );

		// Create array of constants
		$max = 10;
		$constants = array();
		for ( $i = 0; $i <= $max; $i ++ ):
			$constants[$i] = $i;
		endfor;

		// Add 100 to constants
		$constants[100] = 100;

		// Add constants to options
		$options[ 'Constants' ] = (array) $constants;

		$options = apply_filters( 'sportszone_equation_options', $options );
		?>
		<div class="sz-equation-builder">
			<div class="sz-data-table-container sz-equation-parts">
				<table class="widefat sz-data-table">
					<?php $i = 0; foreach ( $options as $label => $option ): ?>
						<tr<?php if ( $i % 2 == 0 ): ?> class="alternate"<?php endif; ?>>
							<th><?php _e( $label, 'sportszone' ); ?></th>
							<td>
								<?php foreach ( $option as $key => $value ): $parts[ $key ] = $value;
									?><span class="button" data-variable="<?php echo $key; ?>"><?php echo $value; ?></span><?php
								endforeach; ?>
							</td>
						</tr>
					<?php $i++; endforeach; ?>
				</table>
			</div>
			<div class="sz-equation">
				<span class="sz-equation-variable"><?php echo $title; ?> = </span>
				<span class="sz-equation-formula"><?php
					$equation = trim( $equation );
					if ( $equation !== '' ):
						$equation = explode( ' ', $equation );
						foreach ( $equation as $part ):
							if ( array_key_exists( $part, $parts ) ) {
								$name = $parts[ $part ];
							} else {
								$name = $part;
							} ?><span class="button"><?php echo $name; ?><span class="remove">&times;</span><input type="hidden" name="sz_equation[]" value="<?php echo $part; ?>"></span><?php
						endforeach;
					endif;
				?></span>
			</div>
		</div>
		<?php
	}

	public static function optgroup( $type = null, $variations = null, $defaults = null, $totals = true ) {
		$arr = array();

		// Get posts
		$args = array(
			'post_type' => $type,
			'numberposts' => -1,
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'meta_query' => array(
        		'relation' => 'OR',
				array(
					'key' => 'sz_format',
					'value' => 'number',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key' => 'sz_format',
					'value' => array( 'equation', 'text' ),
					'compare' => 'NOT IN',
				),
			),
		);
		$vars = get_posts( $args );

		// Add extra vars to the array
		if ( isset( $defaults ) && is_array( $defaults ) ):
			foreach ( $defaults as $key => $value ):
				$arr[ $key ] = $value;
			endforeach;
		endif;

		// Add vars to the array
		if ( isset( $variations ) && is_array( $variations ) ):
			foreach ( $vars as $var ):
				if ( $totals ) $arr[ '$' . $var->post_name ] = $var->post_title;
				foreach ( $variations as $key => $value ):
					$arr[ '$' . $var->post_name . $key ] = $var->post_title . ' ' . $value;
				endforeach;
			endforeach;
		else:
			foreach ( $vars as $var ):
				$arr[ '$' . $var->post_name ] = $var->post_title;
			endforeach;
		endif;

		return (array) $arr;
	}

	/**
	 * Equation part labels for localization
	 * @return null
	 */
	public static function equation_part_labels() {
		__( 'Presets', 'sportszone' );
		__( 'Operators', 'sportszone' );
		__( 'Subsets', 'sportszone' );
		__( 'Constants', 'sportszone' );
	}
}