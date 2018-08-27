<?php
/**
 * List Data
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version		2.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_List_Data
 */
class SZ_Meta_Box_List_Data {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$list = new SZ_Player_List( $post );
		list( $columns, $data, $placeholders, $merged, $orderby ) = $list->data( true );
		$adjustments = $list->adjustments;
		self::table( $columns, $data, $placeholders, $adjustments, $orderby );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_adjustments', sz_array_value( $_POST, 'sz_adjustments', array() ) );
		update_post_meta( $post_id, 'sz_players', sz_array_value( $_POST, 'sz_players', array() ) );
	}

	/**
	 * Admin edit table
	 */
	public static function table( $columns = array(), $data = array(), $placeholders = array(), $adjustments = array(), $orderby = 'number' ) {
		$show_player_photo = get_option( 'sportszone_list_show_photos', 'no' ) == 'yes' ? true : false;
		?>
		<ul class="subsubsub sz-table-bar">
			<li><a href="#sz-table-values" class="current"><?php _e( 'Values', 'sportszone' ); ?></a></li> | 
			<li><a href="#sz-table-adjustments" class=""><?php _e( 'Adjustments', 'sportszone' ); ?></a></li>
		</ul>
		<div class="sz-data-table-container sz-table-panel sz-table-values" id="sz-table-values">
			<table class="widefat sz-data-table sz-player-list-table">
				<thead>
					<tr>
						<?php if ( array_key_exists( 'number', $columns ) ) { ?>
							<th><?php echo in_array( $orderby, array( 'number', 'name' ) ) ? '#' : __( 'Rank', 'sportszone' ); ?></th>
						<?php } ?>
						<th><?php _e( 'Player', 'sportszone' ); ?></th>
						<?php if ( array_key_exists( 'team', $columns ) ) { ?>
							<th><?php _e( 'Team', 'sportszone' ); ?></th>
						<?php } ?>
						<?php if ( array_key_exists( 'position', $columns ) ) { ?>
							<th><?php _e( 'Position', 'sportszone' ); ?></th>
						<?php } ?>
						<?php foreach ( $columns as $key => $label ): ?>
							<?php if ( in_array( $key, array( 'number', 'team', 'position' ) ) ) continue; ?>
							<th><label for="sz_columns_<?php echo $key; ?>">
								<?php echo $label; ?>
							</label></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php
					if ( is_array( $data ) && sizeof( $data ) > 0 ):
						$i = 0;
						foreach ( $data as $player_id => $player_stats ):
							if ( !$player_id ) continue;
							$teams = get_post_meta( $player_id, 'sz_team', false );
							$div = get_term( $player_id, 'sz_season' );
							$number = get_post_meta( $player_id, 'sz_number', true );

							$default_name = sz_array_value( $player_stats, 'name', '' );
							if ( $default_name == null )
								$default_name = get_the_title( $player_id );
							?>
							<tr class="sz-row sz-post<?php if ( $i % 2 == 0 ) echo ' alternate'; ?>">
								<?php if ( array_key_exists( 'number', $columns ) ) { ?>
									<td>
										<?php
										if ( 'number' == $orderby ) {
											echo ( $number ? $number : '&nbsp;' );
										} else {
											echo $i + 1;
										}
										?>
									</td>
								<?php } ?>
								<td>
									<?php if ( $show_player_photo ) echo get_the_post_thumbnail( $player_id, 'sportszone-fit-mini' ); ?>
									<span class="sz-default-value">
										<span class="sz-default-value-input"><?php echo $default_name; ?></span>
										<a class="dashicons dashicons-edit sz-edit" title="<?php _e( 'Edit', 'sportszone' ); ?>"></a>
									</span>
									<span class="hidden sz-custom-value">
										<input type="text" name="sz_players[<?php echo $player_id; ?>][name]" class="name sz-custom-value-input" value="<?php echo esc_attr( sz_array_value( $player_stats, 'name', '' ) ); ?>" placeholder="<?php echo esc_attr( get_the_title( $player_id ) ); ?>" size="6">
										<a class="button button-secondary sz-cancel"><?php _e( 'Cancel', 'sportszone' ); ?></a>
										<a class="button button-primary sz-save"><?php _e( 'Save', 'sportszone' ); ?></a>
									</span>
								</td>
								<?php if ( array_key_exists( 'team', $columns ) ) { ?>
									<td>
										<?php
										$selected = sz_array_value( $player_stats, 'team', get_post_meta( get_the_ID(), 'sz_team', true ) );
										if ( ! $selected ) $selected = get_post_meta( $player_id, 'sz_team', true );
										$include = get_post_meta( $player_id, 'sz_team' );
										$args = array(
											'post_type' => 'sz_team',
											'name' => 'sz_players[' . $player_id . '][team]',
											'include' => $include,
											'selected' => $selected,
											'values' => 'ID',
										);
										wp_dropdown_pages( $args );
										?>
									</td>
								<?php } ?>
								<?php if ( array_key_exists( 'position', $columns ) ) { ?>
									<td>
										<?php
										$selected = sz_array_value( $player_stats, 'position', null );
										$args = array(
											'taxonomy' => 'sz_position',
											'name' => 'sz_players[' . $player_id . '][position]',
											'show_option_blank' => __( '(Auto)', 'sportszone' ),
											'values' => 'term_id',
											'orderby' => 'meta_value_num',
											'meta_query' => array(
												'relation' => 'OR',
												array(
													'key' => 'sz_order',
													'compare' => 'NOT EXISTS'
												),
												array(
													'key' => 'sz_order',
													'compare' => 'EXISTS'
												),
											),
											'selected' => $selected,
											'include_children' => ( 'no' == get_option( 'sportszone_event_hide_child_positions', 'no' ) ),
										);
										sz_dropdown_taxonomies( $args );
										?>
									</td>
								<?php } ?>
								<?php foreach( $columns as $column => $label ):
									if ( in_array( $column, array( 'number', 'team', 'position' ) ) ) continue;
									$value = sz_array_value( $player_stats, $column, '' );
									$placeholder = sz_array_value( sz_array_value( $placeholders, $player_id, array() ), $column, 0 );
									?>
									<td><input type="text" name="sz_players[<?php echo $player_id; ?>][<?php echo $column; ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" data-placeholder="<?php echo esc_attr( $placeholder ); ?>" data-matrix="<?php echo $player_id; ?>_<?php echo $column; ?>" data-adjustment="<?php echo sz_array_value( sz_array_value( $adjustments, $player_id, array() ), $column, 0 ); ?>" /></td>
								<?php endforeach; ?>
							</tr>
							<?php
							$i++;
						endforeach;
					else:
					?>
					<tr class="sz-row alternate">
						<td colspan="<?php $colspan = sizeof( $columns ) + ( apply_filters( 'sportszone_has_teams', true ) ? 3 : 2 ); echo $colspan; ?>">
							<?php printf( __( 'Select %s', 'sportszone' ), __( 'Data', 'sportszone' ) ); ?>
						</td>
					</tr>
					<?php
					endif;
					?>
				</tbody>
			</table>
		</div>
		<div class="sz-data-table-container sz-table-panel sz-table-adjustments hidden" id="sz-table-adjustments">
			<table class="widefat sz-data-table sz-player-list-table">
				<thead>
					<tr>
						<th>#</th>
						<th><?php _e( 'Player', 'sportszone' ); ?></th>
						<?php foreach ( $columns as $key => $label ): if ( in_array( $key, array( 'number', 'team', 'position' ) ) ) continue; ?>
							<th><?php echo $label; ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php
					if ( is_array( $data ) && sizeof( $data ) > 0 ):
						$i = 0;
						foreach ( $data as $player_id => $player_stats ):
							if ( !$player_id ) continue;
							$div = get_term( $player_id, 'sz_season' );
							$number = get_post_meta( $player_id, 'sz_number', true );
							?>
							<tr class="sz-row sz-post<?php if ( $i % 2 == 0 ) echo ' alternate'; ?>">
								<td><?php echo ( $number ? $number : '&nbsp;' ); ?></td>
								<td>
									<?php echo get_the_title( $player_id ); ?>
								</td>
								<?php foreach( $columns as $column => $label ):
									if ( in_array( $column, array( 'number', 'team', 'position' ) ) ) continue;
									$value = sz_array_value( sz_array_value( $adjustments, $player_id, array() ), $column, '' );
									?>
									<td><input type="text" name="sz_adjustments[<?php echo $player_id; ?>][<?php echo $column; ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="0" data-matrix="<?php echo $player_id; ?>_<?php echo $column; ?>" /></td>
								<?php endforeach; ?>
							</tr>
							<?php
							$i++;
						endforeach;
					else:
					?>
					<tr class="sz-row alternate">
						<td colspan="<?php $colspan = sizeof( $columns ) + 3; echo $colspan; ?>">
							<?php printf( __( 'Select %s', 'sportszone' ), __( 'Details', 'sportszone' ) ); ?>
						</td>
					</tr>
					<?php
					endif;
					?>
				</tbody>
			</table>
		</div>
		<?php
	}
}