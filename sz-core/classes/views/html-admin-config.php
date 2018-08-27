<?php
$columns = get_option( 'sportszone_player_columns', 'auto' );
?>

<div class="wrap sportszone sportszone-config-wrap">
	<h2>
		<?php _e( 'Configure', 'sportszone' ); ?>
	</h2>
	<table class="form-table">
		<tbody>
			<?php
			$args = array(
				'post_type' => 'sz_outcome',
				'numberposts' => -1,
				'posts_per_page' => -1,
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);
			$data = get_posts( $args );
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Event Outcomes', 'sportszone' ) ?>
					<p class="description"><?php _e( 'Used for events.', 'sportszone' ); ?></p>
				</th>
			    <td class="forminp">
					<table class="widefat sz-admin-config-table">
						<thead>
							<tr>
								<th scope="col"><?php _e( 'Label', 'sportszone' ); ?></th>
								<th scope="col"><?php _e( 'Variable', 'sportszone' ); ?></th>
								<th scope="col"><?php _e( 'Abbreviation', 'sportszone' ); ?></th>
								<th scope="col"><?php _e( 'Condition', 'sportszone' ); ?></th>
								<th scope="col"><?php _e( 'Description', 'sportszone' ); ?></th>
								<th scope="col" class="edit"></th>
							</tr>
						</thead>
						<?php if ( $data ): $i = 0; foreach ( $data as $row ): ?>
							<tr<?php if ( $i % 2 == 0 ) echo ' class="alternate"'; ?>>
								<td class="row-title"><?php echo $row->post_title; ?></td>
								<td><code><?php echo $row->post_name; ?></code></td>
								<td><?php echo sz_get_post_abbreviation( $row->ID ); ?></td>
								<td><?php echo sz_get_post_condition( $row->ID ); ?></td>
								<td><p class="description"><?php echo $row->post_excerpt; ?></p></td>
								<td class="edit"><a class="button" href="<?php echo get_edit_post_link( $row->ID ); ?>"><?php _e( 'Edit', 'sportszone' ); ?></s></td>
							</tr>
						<?php $i++; endforeach; else: ?>
							<tr class="alternate">
								<td colspan="6"><?php _e( 'No results found.', 'sportszone' ); ?></td>
							</tr>
						<?php endif; ?>
					</table>
					<div class="tablenav bottom">
						<a class="button alignleft" href="<?php echo admin_url( 'edit.php?post_type=sz_outcome' ); ?>"><?php _e( 'View All', 'sportszone' ); ?></a>
						<a class="button button-primary alignright" href="<?php echo admin_url( 'post-new.php?post_type=sz_outcome' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
						<br class="clear">
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="form-table">
		<tbody>
			<?php
			$selection = get_option( 'sportszone_primary_result', 0 );

			$args = array(
				'post_type' => 'sz_result',
				'numberposts' => -1,
				'posts_per_page' => -1,
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);
			$data = get_posts( $args );
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Event Results', 'sportszone' ) ?>
					<p class="description"><?php _e( 'Used for events.', 'sportszone' ); ?></p>
				</th>
			    <td class="forminp">
					<legend class="screen-reader-text"><span><?php _e( 'Event Results', 'sportszone' ) ?></span></legend>
					<form>
						<?php wp_nonce_field( 'sz-save-primary-result', 'sz-primary-result-nonce', false ); ?>
						<table class="widefat sz-admin-config-table">
							<thead>
								<tr>
									<th class="radio" scope="col"><?php _e( 'Primary', 'sportszone' ); ?></th>
									<th scope="col"><?php _e( 'Label', 'sportszone' ); ?></th>
									<th scope="col"><?php _e( 'Variables', 'sportszone' ); ?></th>
									<th scope="col"><?php _e( 'Equation', 'sportszone' ); ?></th>
									<th scope="col"><?php _e( 'Decimal Places', 'sportszone' ); ?></th>
									<th scope="col"><?php _e( 'Description', 'sportszone' ); ?></th>
									<th scope="col" class="edit"></th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th class="radio"><input type="radio" class="sz-primary-result-option" id="sportszone_primary_result_0" name="sportszone_primary_result" value="0" <?php checked( $selection, 0 ); ?>></th>
									<th colspan="6"><label for="sportszone_primary_result_0">
										<?php
										if ( sizeof( $data ) > 0 ):
											$default = end( $data );
											reset( $data );
											printf( __( 'Default (%s)', 'sportszone' ), $default->post_title );
										else:
											_e( 'Default', 'sportszone' );
										endif;
										?>
									</label></th>
								</tr>
							</tfoot>
							<?php if ( $data ): $i = 0; foreach ( $data as $row ): ?>
								<tr<?php if ( $i % 2 == 0 ) echo ' class="alternate"'; ?>>
									<td class="radio"><input type="radio" class="sz-primary-result-option" id="sportszone_primary_result_<?php echo $row->post_name; ?>" name="sportszone_primary_result" value="<?php echo $row->post_name; ?>" <?php checked( $selection, $row->post_name ); ?>></td>
									<td class="row-title"><label for="sportszone_primary_result_<?php echo $row->post_name; ?>"><?php echo $row->post_title; ?></label></td>
									<td><code><?php echo $row->post_name; ?>for</code>, <code><?php echo $row->post_name; ?>against</code></td>
									<td><?php echo sz_get_post_equation( $row->ID ); ?></td>
									<td><?php echo sz_get_post_precision( $row->ID ); ?></td>
									<td><p class="description"><?php echo $row->post_excerpt; ?></p></td>
									<td class="edit"><a class="button" href="<?php echo get_edit_post_link( $row->ID ); ?>"><?php _e( 'Edit', 'sportszone' ); ?></s></td>
								</tr>
							<?php $i++; endforeach; else: ?>
							<tr class="alternate">
								<td colspan="7"><?php _e( 'No results found.', 'sportszone' ); ?></td>
							</tr>
						<?php endif; ?>
						</table>
					</form>
					<div class="tablenav bottom">
						<a class="button alignleft" href="<?php echo admin_url( 'edit.php?post_type=sz_result' ); ?>"><?php _e( 'View All', 'sportszone' ); ?></a>
						<a class="button button-primary alignright" href="<?php echo admin_url( 'post-new.php?post_type=sz_result' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
						<br class="clear">
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="form-table">
		<tbody>
			<?php
			$selection = get_option( 'sportszone_primary_performance', 0 );
			$colspan = 8;

			if ( 'auto' === $columns ) $colspan ++;

			$args = array(
				'post_type' => 'sz_performance',
				'numberposts' => -1,
				'posts_per_page' => -1,
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);
			$data = get_posts( $args );
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Player Performance', 'sportszone' ) ?>
					<p class="description"><?php _e( 'Used for events.', 'sportszone' ); ?></p>
				</th>
			    <td class="forminp">
					<legend class="screen-reader-text"><span><?php _e( 'Player Performance', 'sportszone' ) ?></span></legend>
					<form>
						<?php wp_nonce_field( 'sz-save-primary-performance', 'sz-primary-performance-nonce', false ); ?>
						<table class="widefat sz-admin-config-table">
							<thead>
								<tr>
									<th class="radio" scope="col"><?php _e( 'Primary', 'sportszone' ); ?></th>
									<th class="icon" scope="col"><?php _e( 'Icon', 'sportszone' ); ?></th>
									<th scope="col"><?php _e( 'Label', 'sportszone' ); ?></th>
									<th scope="col"><?php _e( 'Variable', 'sportszone' ); ?></th>
									<th scope="col"><?php _e( 'Category', 'sportszone' ); ?></th>
									<th scope="col"><?php _e( 'Format', 'sportszone' ); ?></th>
									<?php if ( 'auto' === $columns ) { ?>
										<th scope="col">
											<?php _e( 'Visible', 'sportszone' ); ?>
											<i class="dashicons dashicons-editor-help sz-desc-tip" title="<?php _e( 'Display in player profile?', 'sportszone' ); ?>"></i>
										</th>
									<?php } ?>
									<th scope="col"><?php _e( 'Description', 'sportszone' ); ?></th>
									<th scope="col" class="edit"></th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th class="radio"><input type="radio" class="sz-primary-performance-option" id="sportszone_primary_performance_0" name="sportszone_primary_performance" value="0" <?php checked( $selection, 0 ); ?>></th>
									<th class="icon">&nbsp;</td>
									<th colspan="<?php echo $colspan - 1; ?>"><label for="sportszone_primary_performance_0">
										<?php
										if ( sizeof( $data ) > 0 ):
											$default = reset( $data );
											printf( __( 'Default (%s)', 'sportszone' ), $default->post_title );
										else:
											_e( 'Default', 'sportszone' );
										endif;
										?>
									</label></th>
								</tr>
							</tfoot>
							<?php if ( $data ): $i = 0; foreach ( $data as $row ): ?>
								<?php
								$visible = get_post_meta( $row->ID, 'sz_visible', true );
								if ( '' === $visible ) $visible = 1;
								?>
								<tr<?php if ( $i % 2 == 0 ) echo ' class="alternate"'; ?>>
									<td class="radio"><input type="radio" class="sz-primary-performance-option" id="sportszone_primary_performance_<?php echo $row->post_name; ?>" name="sportszone_primary_performance" value="<?php echo $row->post_name; ?>" <?php checked( $selection, $row->post_name ); ?>></td>
									<td class="icon">
										<?php
										if ( has_post_thumbnail( $row->ID ) )
											$icon = get_the_post_thumbnail( $row->ID, 'sportszone-fit-mini' );
										else
											$icon = '&nbsp;';

										echo apply_filters( 'sportszone_performance_icon', $icon, $row->ID );
										?>
									</td>
									<td class="row-title"><?php echo $row->post_title; ?></td>
									<td><code><?php echo $row->post_name; ?></code></td>
									<td><?php echo sz_get_post_section( $row->ID ); ?></td>
									<td><?php echo sz_get_post_format( $row->ID ); ?></td>
									<?php if ( 'auto' === $columns ) { ?>
										<td>
											<?php if ( $visible ) { ?><i class="dashicons dashicons-yes"></i><?php } else { ?>&nbsp;<?php } ?>
										</td>
									<?php } ?>
									<td><p class="description"><?php echo $row->post_excerpt; ?></p></td>
									<td class="edit"><a class="button" href="<?php echo get_edit_post_link( $row->ID ); ?>"><?php _e( 'Edit', 'sportszone' ); ?></s></td>
								</tr>
							<?php $i++; endforeach; else: ?>
								<tr class="alternate">
									<td colspan="<?php echo $colspan; ?>"><?php _e( 'No results found.', 'sportszone' ); ?></td>
								</tr>
							<?php endif; ?>
						</table>
					</form>
					<div class="tablenav bottom">
						<a class="button alignleft" href="<?php echo admin_url( 'edit.php?post_type=sz_performance' ); ?>"><?php _e( 'View All', 'sportszone' ); ?></a>
						<a class="button button-primary alignright" href="<?php echo admin_url( 'post-new.php?post_type=sz_performance' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
						<br class="clear">
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="form-table">
		<tbody>
			<?php
			$args = array(
				'post_type' => 'sz_column',
				'numberposts' => -1,
				'posts_per_page' => -1,
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);
			$data = get_posts( $args );
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Table Columns', 'sportszone' ) ?>
					<p class="description"><?php _e( 'Used for league tables.', 'sportszone' ); ?></p>
				</th>
			    <td class="forminp">
					<table class="widefat sz-admin-config-table">
						<thead>
							<tr>
								<th scope="col"><?php _e( 'Label', 'sportszone' ); ?></th>
								<th scope="col"><?php _e( 'Equation', 'sportszone' ); ?></th>
								<th scope="col"><?php _e( 'Decimal Places', 'sportszone' ); ?></th>
								<th scope="col"><?php _e( 'Sort Order', 'sportszone' ); ?></th>
								<th scope="col"><?php _e( 'Description', 'sportszone' ); ?></th>
								<th scope="col" class="edit"></th>
							</tr>
						</thead>
						<?php if ( $data ): $i = 0; foreach ( $data as $row ): ?>
							<tr<?php if ( $i % 2 == 0 ) echo ' class="alternate"'; ?>>
								<td class="row-title"><?php echo $row->post_title; ?></td>
								<td><?php echo sz_get_post_equation( $row->ID ); ?></td>
								<td><?php echo sz_get_post_precision( $row->ID ); ?></td>
								<td><?php echo sz_get_post_order( $row->ID ); ?></td>
								<td><p class="description"><?php echo $row->post_excerpt; ?></p></td>
								<td class="edit"><a class="button" href="<?php echo get_edit_post_link( $row->ID ); ?>"><?php _e( 'Edit', 'sportszone' ); ?></s></td>
							</tr>
						<?php $i++; endforeach; else: ?>
							<tr class="alternate">
								<td colspan="7"><?php _e( 'No results found.', 'sportszone' ); ?></td>
							</tr>
						<?php endif; ?>
					</table>
					<div class="tablenav bottom">
						<a class="button alignleft" href="<?php echo admin_url( 'edit.php?post_type=sz_column' ); ?>"><?php _e( 'View All', 'sportszone' ); ?></a>
						<a class="button button-primary alignright" href="<?php echo admin_url( 'post-new.php?post_type=sz_column' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
						<br class="clear">
					</div>
				</fieldset>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="form-table">
		<tbody>
			<?php
			$args = array(
				'post_type' => 'sz_metric',
				'numberposts' => -1,
				'posts_per_page' => -1,
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);
			$data = get_posts( $args );
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Player Metrics', 'sportszone' ) ?>
					<p class="description"><?php _e( 'Used for player lists.', 'sportszone' ); ?></p>
				</th>
			    <td class="forminp">
					<table class="widefat sz-admin-config-table">
						<thead>
							<tr>
								<th scope="col"><?php _e( 'Label', 'sportszone' ); ?></th>
								<th scope="col"><?php _e( 'Variable', 'sportszone' ); ?></th>
								<th scope="col"><?php _e( 'Description', 'sportszone' ); ?></th>
								<th scope="col" class="edit"></th>
							</tr>
						</thead>
						<?php if ( $data ): $i = 0; foreach ( $data as $row ): ?>
							<tr<?php if ( $i % 2 == 0 ) echo ' class="alternate"'; ?>>
								<td class="row-title"><?php echo $row->post_title; ?></td>
								<td><code><?php echo $row->post_name; ?></code></td>
								<td><p class="description"><?php echo $row->post_excerpt; ?></p></td>
								<td class="edit"><a class="button" href="<?php echo get_edit_post_link( $row->ID ); ?>"><?php _e( 'Edit', 'sportszone' ); ?></s></td>
							</tr>
						<?php $i++; endforeach; else: ?>
							<tr class="alternate">
								<td colspan="4"><?php _e( 'No results found.', 'sportszone' ); ?></td>
							</tr>
						<?php endif; ?>
					</table>
					<div class="tablenav bottom">
						<a class="button alignleft" href="<?php echo admin_url( 'edit.php?post_type=sz_metric' ); ?>"><?php _e( 'View All', 'sportszone' ); ?></a>
						<a class="button button-primary alignright" href="<?php echo admin_url( 'post-new.php?post_type=sz_metric' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
						<br class="clear">
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="form-table">
		<tbody>
			<?php
			$args = array(
				'post_type' => 'sz_statistic',
				'numberposts' => -1,
				'posts_per_page' => -1,
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);
			$data = get_posts( $args );
			
			$colspan = 6;

			if ( 'auto' === $columns ) $colspan ++;
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Player Statistics', 'sportszone' ) ?>
					<p class="description"><?php _e( 'Used for player lists.', 'sportszone' ); ?></p>
				</th>
			    <td class="forminp">
					<table class="widefat sz-admin-config-table">
						<thead>
							<tr>
								<th scope="col"><?php _e( 'Label', 'sportszone' ); ?></th>
								<th scope="col"><?php _e( 'Equation', 'sportszone' ); ?></th>
								<th scope="col"><?php _e( 'Decimal Places', 'sportszone' ); ?></th>
								<th scope="col"><?php _e( 'Category', 'sportszone' ); ?></th>
								<?php if ( 'auto' === $columns ) { ?>
									<th scope="col">
										<?php _e( 'Visible', 'sportszone' ); ?>
										<i class="dashicons dashicons-editor-help sz-desc-tip" title="<?php _e( 'Display in player profile?', 'sportszone' ); ?>"></i>
									</th>
								<?php } ?>
								<th scope="col"><?php _e( 'Description', 'sportszone' ); ?></th>
								<th scope="col" class="edit"></th>
							</tr>
						</thead>
						<?php if ( $data ): $i = 0; foreach ( $data as $row ): ?>
							<?php
							$visible = get_post_meta( $row->ID, 'sz_visible', true );
							if ( '' === $visible ) $visible = 1;
							?>
							<tr<?php if ( $i % 2 == 0 ) echo ' class="alternate"'; ?>>
								<td class="row-title"><?php echo $row->post_title; ?></td>
								<td><?php echo sz_get_post_equation( $row->ID ); ?></td>
								<td><?php echo sz_get_post_precision( $row->ID ); ?></td>
								<td><?php echo sz_get_post_section( $row->ID ); ?></td>
								<?php if ( 'auto' === $columns ) { ?>
									<td>
										<?php if ( $visible ) { ?><i class="dashicons dashicons-yes"></i><?php } else { ?>&nbsp;<?php } ?>
									</td>
								<?php } ?>
								<td><p class="description"><?php echo $row->post_excerpt; ?></p></td>
								<td class="edit"><a class="button" href="<?php echo get_edit_post_link( $row->ID ); ?>"><?php _e( 'Edit', 'sportszone' ); ?></s></td>
							</tr>
						<?php $i++; endforeach; else: ?>
							<tr class="alternate">
								<td colspan="<?php echo $colspan; ?>"><?php _e( 'No results found.', 'sportszone' ); ?></td>
							</tr>
						<?php endif; ?>
					</table>
					<div class="tablenav bottom">
						<a class="button alignleft" href="<?php echo admin_url( 'edit.php?post_type=sz_statistic' ); ?>"><?php _e( 'View All', 'sportszone' ); ?></a>
						<a class="button button-primary alignright" href="<?php echo admin_url( 'post-new.php?post_type=sz_statistic' ); ?>"><?php _e( 'Add New', 'sportszone' ); ?></a>
						<br class="clear">
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<?php do_action( 'sportszone_config_page' ); ?>
</div>