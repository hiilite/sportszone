<?php
/**
 * Table Data
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin/Meta_Boxes
 * @version		2.5.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SZ_Meta_Box_Table_Data
 */
class SZ_Meta_Box_Table_Data {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$table = new SZ_League_Table( $post );
		list( $columns, $usecolumns, $data, $placeholders, $merged ) = $table->data( true );
		$adjustments = $table->adjustments;
		$highlight = get_post_meta( $table->ID, 'sz_highlight', true );
		self::table( $table->ID, $columns, $usecolumns, $data, $placeholders, $adjustments, $highlight );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sz_highlight', sz_array_value( $_POST, 'sz_highlight', array() ) );
		update_post_meta( $post_id, 'sz_columns', sz_array_value( $_POST, 'sz_columns', array() ) );
		update_post_meta( $post_id, 'sz_adjustments', sz_array_value( $_POST, 'sz_adjustments', array() ) );
		update_post_meta( $post_id, 'sz_teams', sz_array_value( $_POST, 'sz_teams', array() ) );
	}

	/**
	 * Admin edit table
	 */
	public static function table( $id, $columns = array(), $usecolumns = null, $data = array(), $placeholders = array(), $adjustments = array(), $highlight = null, $readonly = false ) {
		if ( is_array( $usecolumns ) )
			$usecolumns = array_filter( $usecolumns );

		$mode = sz_get_post_mode( $id );

		if ( 'player' === $mode ) {
			$show_team_logo = get_option( 'sportszone_list_show_photos', 'no' ) == 'yes' ? true : false;
			$icon_class = 'sz-icon-tshirt';
		} else {
			$show_team_logo = get_option( 'sportszone_table_show_logos', 'no' ) == 'yes' ? true : false;
			$icon_class = 'sz-icon-shield';
		}
		?>

		<?php if ( $readonly ) { ?>
			<p>
				<strong><?php echo get_the_title( $id ); ?></strong>
				<a class="add-new-h2 sz-add-new-h2" href="<?php echo esc_url( admin_url( add_query_arg( array( 'post' => $id, 'action' => 'edit' ), 'post.php' ) ) ); ?>"><?php _e( 'Edit', 'sportszone' ); ?></a>
			</p>
		<?php } else { ?>
			<input type="hidden" name="sz_highlight" value="0">
			<ul class="subsubsub sz-table-bar">
				<li><a href="#sz-table-values" class="current"><?php _e( 'Values', 'sportszone' ); ?></a></li> | 
				<li><a href="#sz-table-adjustments" class=""><?php _e( 'Adjustments', 'sportszone' ); ?></a></li>
			</ul>
		<?php } ?>

		<div class="sz-data-table-container sz-table-panel sz-table-values" id="sz-table-values">
			<table class="widefat sz-data-table sz-league-table">
				<thead>
					<tr>
						<?php if ( ! $readonly ) { ?>
							<th class="radio"><span class="dashicons <?php echo $icon_class; ?> sz-tip" title="<?php _e( 'Highlight', 'sportszone' ); ?>"></span></th>
						<?php } ?>
						<th><?php _e( 'Team', 'sportszone' ); ?></th>
						<?php foreach ( $columns as $key => $label ): ?>
							<th><label for="sz_columns_<?php echo $key; ?>">
								<?php if ( ! $readonly ) { ?>
									<input type="checkbox" name="sz_columns[]" value="<?php echo $key; ?>" id="sz_columns_<?php echo $key; ?>" <?php checked( ! is_array( $usecolumns ) || in_array( $key, $usecolumns ) ); ?>>
								<?php } ?>
								<?php echo $label; ?>
							</label></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php
					if ( is_array( $data ) && sizeof( $data ) > 0 ):
						$i = 0;
						foreach ( $data as $team_id => $team_stats ):
							if ( !$team_id )
								continue;

							$default_name = sz_array_value( $team_stats, 'name', '' );
							if ( $default_name == null )
								$default_name = get_the_title( $team_id );
							?>
							<tr class="sz-row sz-post<?php if ( $i % 2 == 0 ) echo ' alternate'; ?>">
								<?php if ( ! $readonly ) { ?>
									<td><input type="radio" class="sz-radio-toggle" name="sz_highlight" value="<?php echo $team_id; ?>" <?php checked( $highlight, $team_id ); ?> <?php disabled( $readonly ); ?>></td>
								<?php } ?>
								<td>
									<?php if ( $show_team_logo ) echo get_the_post_thumbnail( $team_id, 'sportszone-fit-mini' ); ?>
									<?php if ( $readonly ) { ?>
										<?php echo $default_name; ?>
									<?php } else { ?>
										<span class="sz-default-value">
											<span class="sz-default-value-input"><?php echo $default_name; ?></span>
											<a class="dashicons dashicons-edit sz-edit" title="<?php _e( 'Edit', 'sportszone' ); ?>"></a>
										</span>
										<span class="hidden sz-custom-value">
											<input type="text" name="sz_teams[<?php echo $team_id; ?>][name]" class="name sz-custom-value-input" value="<?php echo esc_attr( sz_array_value( $team_stats, 'name', '' ) ); ?>" placeholder="<?php echo esc_attr( get_the_title( $team_id ) ); ?>" size="6">
											<a class="button button-secondary sz-cancel"><?php _e( 'Cancel', 'sportszone' ); ?></a>
											<a class="button button-primary sz-save"><?php _e( 'Save', 'sportszone' ); ?></a>
										</span>
									<?php } ?>
								</td>
								<?php foreach( $columns as $column => $label ):
									$value = sz_array_value( $team_stats, $column, '' );
									$placeholder = sz_array_value( sz_array_value( $placeholders, $team_id, array() ), $column, 0 );
									$placeholder = wp_strip_all_tags( $placeholder );
									?>
									<td><input type="text" name="sz_teams[<?php echo $team_id; ?>][<?php echo $column; ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" data-placeholder="<?php echo esc_attr( $placeholder ); ?>" data-matrix="<?php echo $team_id; ?>_<?php echo $column; ?>" data-adjustment="<?php echo esc_attr( sz_array_value( sz_array_value( $adjustments, $team_id, array() ), $column, 0 ) ); ?>" <?php disabled( $readonly ); ?> /></td>
								<?php endforeach; ?>
							</tr>
							<?php
							$i++;
						endforeach;
					else:
					?>
					<tr class="sz-row alternate">
						<td colspan="<?php $colspan = sizeof( $columns ) + ( $readonly ? 1 : 2 ); echo $colspan; ?>">
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
			<table class="widefat sz-data-table sz-league-table">
				<thead>
					<tr>
						<th><?php _e( 'Team', 'sportszone' ); ?></th>
						<?php foreach ( $columns as $key => $label ): ?>
							<th><?php echo $label; ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php
					if ( is_array( $data ) && sizeof( $data ) > 0 ):
						$i = 0;
						foreach ( $data as $team_id => $team_stats ):
							if ( !$team_id )
								continue;
							?>
							<tr class="sz-row sz-post<?php if ( $i % 2 == 0 ) echo ' alternate'; ?>">
								<td>
									<?php echo get_the_title( $team_id ); ?>
								</td>
								<?php foreach( $columns as $column => $label ):
									$value = sz_array_value( sz_array_value( $adjustments, $team_id, array() ), $column, '' );
									?>
									<td><input type="text" name="sz_adjustments[<?php echo $team_id; ?>][<?php echo $column; ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="0" data-matrix="<?php echo $team_id; ?>_<?php echo $column; ?>" /></td>
								<?php endforeach; ?>
							</tr>
							<?php
							$i++;
						endforeach;
					else:
					?>
					<tr class="sz-row alternate">
						<td colspan="<?php $colspan = sizeof( $columns ) + 1; echo $colspan; ?>">
							<?php printf( __( 'Select %s', 'sportszone' ), __( 'Data', 'sportszone' ) ); ?>
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