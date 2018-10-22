<div class="wrap sportszone">
	<h2><?php _e( 'System Status', 'sportszone' ); ?></h2>
</div>
<div class="updated sportszone-message">
	<p><?php _e( 'Please include this information when requesting support:', 'sportszone' ); ?> </p>
	<p class="submit"><a href="#" class="button-primary debug-report"><?php _e( 'Get System Report', 'sportszone' ); ?></a></p>
	<div id="debug-report"><textarea readonly="readonly"></textarea></div>
</div>
<br/>
<table class="sp-status-table widefat" cellspacing="0">

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'Environment', 'sportszone' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td><?php _e( 'Home URL','sportszone' ); ?>:</td>
			<td><?php echo home_url(); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Site URL','sportszone' ); ?>:</td>
			<td><?php echo site_url(); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'SP Version','sportszone' ); ?>:</td>
			<td><?php echo esc_html( SportsZone()->version ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Version','sportszone' ); ?>:</td>
			<td><?php bloginfo('version'); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Multisite Enabled','sportszone' ); ?>:</td>
			<td><?php if ( is_multisite() ) _e( 'Yes', 'sportszone' ); else _e( 'No', 'sportszone' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Web Server Info','sportszone' ); ?>:</td>
			<td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'PHP Version','sportszone' ); ?>:</td>
			<td><?php if ( function_exists( 'phpversion' ) ) echo esc_html( phpversion() ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Memory Limit','sportszone' ); ?>:</td>
			<td><?php
				$memory = sz_let_to_num( WP_MEMORY_LIMIT );

				if ( $memory < 67108864 ) {
					echo '<mark class="error">' . sprintf( __( '%s - We recommend setting memory to at least 64MB. See: <a href="%s">Increasing memory allocated to PHP</a>', 'sportszone' ), size_format( $memory ), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ) . '</mark>';
				} else {
					echo '<mark class="yes">' . size_format( $memory ) . '</mark>';
				}
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Debug Mode', 'sportszone' ); ?>:</td>
			<td><?php if ( defined('WP_DEBUG') && WP_DEBUG ) echo '<mark class="yes">' . __( 'Yes', 'sportszone' ) . '</mark>'; else echo '<mark class="no">' . __( 'No', 'sportszone' ) . '</mark>'; ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Language', 'sportszone' ); ?>:</td>
			<td><?php if ( defined( 'WPLANG' ) && WPLANG ) echo WPLANG; else  _e( 'Default', 'sportszone' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Max Upload Size','sportszone' ); ?>:</td>
			<td><?php echo size_format( wp_max_upload_size() ); ?></td>
		</tr>
		<?php if ( function_exists( 'ini_get' ) ) : ?>
			<tr>
				<td><?php _e('PHP Post Max Size','sportszone' ); ?>:</td>
				<td><?php echo size_format( sz_let_to_num( ini_get('post_max_size') ) ); ?></td>
			</tr>
			<tr>
				<td><?php _e('PHP Time Limit','sportszone' ); ?>:</td>
				<td><?php echo ini_get('max_execution_time'); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'PHP Max Input Vars','sportszone' ); ?>:</td>
				<td><?php echo ini_get('max_input_vars'); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'SUHOSIN Installed','sportszone' ); ?>:</td>
				<td><?php echo extension_loaded( 'suhosin' ) ? __( 'Yes', 'sportszone' ) : __( 'No', 'sportszone' ); ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<td><?php _e( 'Default Timezone','sportszone' ); ?>:</td>
			<td><?php
				$default_timezone = date_default_timezone_get();
				if ( 'UTC' !== $default_timezone ) {
					echo '<mark class="error">' . sprintf( __( 'Default timezone is %s - it should be UTC', 'sportszone' ), $default_timezone ) . '</mark>';
				} else {
					echo '<mark class="yes">' . sprintf( __( 'Default timezone is %s', 'sportszone' ), $default_timezone ) . '</mark>';
				} ?>
			</td>
		</tr>
		<?php
			$posting = array();

			// fsockopen/cURL
			$posting['fsockopen_curl']['name'] = __( 'fsockopen/cURL','sportszone');
			if ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ) {
				if ( function_exists( 'fsockopen' ) && function_exists( 'curl_init' )) {
					$posting['fsockopen_curl']['note'] = __('Your server has fsockopen and cURL enabled.', 'sportszone' );
				} elseif ( function_exists( 'fsockopen' )) {
					$posting['fsockopen_curl']['note'] = __( 'Your server has fsockopen enabled, cURL is disabled.', 'sportszone' );
				} else {
					$posting['fsockopen_curl']['note'] = __( 'Your server has cURL enabled, fsockopen is disabled.', 'sportszone' );
				}
				$posting['fsockopen_curl']['success'] = true;
			} else {
				$posting['fsockopen_curl']['note'] = __( 'Your server does not have fsockopen or cURL enabled - PayPal IPN and other scripts which communicate with other servers will not work. Contact your hosting provider.', 'sportszone' ). '</mark>';
				$posting['fsockopen_curl']['success'] = false;
			}

			// SOAP
			$posting['soap_client']['name'] = __( 'SOAP Client','sportszone' );
			if ( class_exists( 'SoapClient' ) ) {
				$posting['soap_client']['note'] = __('Your server has the SOAP Client class enabled.', 'sportszone' );
				$posting['soap_client']['success'] = true;
			} else {
				$posting['soap_client']['note'] = sprintf( __( 'Your server does not have the <a href="%s">SOAP Client</a> class enabled - some gateway plugins which use SOAP may not work as expected.', 'sportszone' ), 'http://php.net/manual/en/class.soapclient.php' ) . '</mark>';
				$posting['soap_client']['success'] = false;
			}

			$posting = apply_filters( 'sportszone_debug_posting', $posting );

			foreach( $posting as $post ) { $mark = ( isset( $post['success'] ) && $post['success'] == true ) ? 'yes' : 'error';
				?>
				<tr>
					<td><?php echo esc_html( $post['name'] ); ?>:</td>
					<td>
						<mark class="<?php echo $mark; ?>">
							<?php echo wp_kses_data( $post['note'] ); ?>
						</mark>
					</td>
				</tr>
				<?php
			}
		?>
	</tbody>

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'Plugins', 'sportszone' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td><?php _e( 'Installed Plugins','sportszone' ); ?>:</td>
			<td><?php
				$active_plugins = (array) get_option( 'active_plugins', array() );

				if ( is_multisite() )
					$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );

				$sz_plugins = array();

				foreach ( $active_plugins as $plugin ) {

					$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
					$dirname        = dirname( $plugin );
					$version_string = '';

					if ( ! empty( $plugin_data['Name'] ) ) {

						// link the plugin name to the plugin url if available
						$plugin_name = $plugin_data['Name'];
						if ( ! empty( $plugin_data['PluginURI'] ) ) {
							$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . __( 'Visit plugin homepage' , 'sportszone' ) . '">' . $plugin_name . '</a>';
						}

						if ( strstr( $dirname, 'sportszone' ) ) {

							if ( false === ( $version_data = get_transient( md5( $plugin ) . '_version_data' ) ) ) {
								$changelog = wp_remote_get( 'http://dzv365zjfbd8v.cloudfront.net/changelogs/' . $dirname . '/changelog.txt' );
								$cl_lines  = explode( "\n", wp_remote_retrieve_body( $changelog ) );
								if ( ! empty( $cl_lines ) ) {
									foreach ( $cl_lines as $line_num => $cl_line ) {
										if ( preg_match( '/^[0-9]/', $cl_line ) ) {

											$date         = str_replace( '.' , '-' , trim( substr( $cl_line , 0 , strpos( $cl_line , '-' ) ) ) );
											$version      = preg_replace( '~[^0-9,.]~' , '' ,stristr( $cl_line , "version" ) );
											$update       = trim( str_replace( "*" , "" , $cl_lines[ $line_num + 1 ] ) );
											$version_data = array( 'date' => $date , 'version' => $version , 'update' => $update , 'changelog' => $changelog );
											set_transient( md5( $plugin ) . '_version_data', $version_data, 60*60*12 );
											break;
										}
									}
								}
							}

							if ( ! empty( $version_data['version'] ) && version_compare( $version_data['version'], $plugin_data['Version'], '>' ) )
								$version_string = ' &ndash; <strong style="color:red;">' . $version_data['version'] . ' ' . __( 'is available', 'sportszone' ) . '</strong>';
						}

						$sz_plugins[] = $plugin_name . ' ' . __( 'by', 'sportszone' ) . ' ' . $plugin_data['Author'] . ' ' . __( 'version', 'sportszone' ) . ' ' . $plugin_data['Version'] . $version_string;

					}
				}

				if ( sizeof( $sz_plugins ) == 0 )
					echo '-';
				else
					echo implode( ', <br/>', $sz_plugins );

			?></td>
		</tr>
	</tbody>

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'SP Configuration', 'sportszone' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td><?php _e( 'Sport', 'sportszone' ); ?>:</td>
			<td><?php echo get_option( 'sportszone_sport', __( 'None', 'sportszone' ) ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Event Outcomes', 'sportszone' ); ?>:</td>
			<td><?php
				$display_posts = array();
				$posts = get_posts( array( 'post_type' => 'sz_outcome', 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => -1, 'post_status' => 'any' ) );
				foreach ( $posts as $post )
					$display_posts[] = $post->post_title . ' (' . $post->post_name . ') [' . $post->menu_order . ']';
				echo implode( ', ', array_map( 'esc_html', $display_posts ) );
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'Event Results', 'sportszone' ); ?>:</td>
			<td><?php
				$display_posts = array();
				$posts = get_posts( array( 'post_type' => 'sz_result', 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => -1, 'post_status' => 'any' ) );
				foreach ( $posts as $post )
					$display_posts[] = $post->post_title . ' (' . $post->post_name . ') [' . $post->menu_order . ']';
				echo implode( ', ', array_map( 'esc_html', $display_posts ) );
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'Player Performance', 'sportszone' ); ?>:</td>
			<td><?php
				$display_posts = array();
				$posts = get_posts( array( 'post_type' => 'sz_performance', 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => -1, 'post_status' => 'any' ) );
				foreach ( $posts as $post )
					$display_posts[] = $post->post_title . ' (' . $post->post_name . ') [' . $post->menu_order . ']';
				echo implode( ', ', array_map( 'esc_html', $display_posts ) );
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'Table Columns', 'sportszone' ); ?>:</td>
			<td><?php
				$display_posts = array();
				$posts = get_posts( array( 'post_type' => 'sz_column', 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => -1, 'post_status' => 'any' ) );
				foreach ( $posts as $post )
					$display_posts[] = $post->post_title . ' (' . $post->post_name . ' = ' . get_post_meta( $post->ID, 'sz_equation', true ) . ') [' . $post->menu_order . ']';
				echo implode( ', ', array_map( 'esc_html', $display_posts ) );
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'Player Metrics', 'sportszone' ); ?>:</td>
			<td><?php
				$display_posts = array();
				$posts = get_posts( array( 'post_type' => 'sz_metric', 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => -1, 'post_status' => 'any' ) );
				foreach ( $posts as $post )
					$display_posts[] = $post->post_title . ' (' . $post->post_name . ') [' . $post->menu_order . ']';
				echo implode( ', ', array_map( 'esc_html', $display_posts ) );
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'Player Statistics', 'sportszone' ); ?>:</td>
			<td><?php
				$display_posts = array();
				$posts = get_posts( array( 'post_type' => 'sz_statistic', 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => -1, 'post_status' => 'any' ) );
				foreach ( $posts as $post )
					$display_posts[] = $post->post_title . ' (' . $post->post_name . ' = ' . get_post_meta( $post->ID, 'sz_equation', true ) . ') [' . $post->menu_order . ']';
				echo implode( ', ', array_map( 'esc_html', $display_posts ) );
			?></td>
		</tr>
	</tbody>

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'SP Taxonomies', 'sportszone' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td><?php _e( 'Leagues', 'sportszone' ); ?>:</td>
			<td><?php
				$display_terms = array();
				$terms = get_terms( 'sz_league', array( 'hide_empty' => 0 ) );
				foreach ( $terms as $term )
					$display_terms[] = $term->name . ' (' . $term->slug . ')';
				echo implode( ', ', array_map( 'esc_html', $display_terms ) );
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'Seasons', 'sportszone' ); ?>:</td>
			<td><?php
				$display_terms = array();
				$terms = get_terms( 'sz_season', array( 'hide_empty' => 0 ) );
				foreach ( $terms as $term )
					$display_terms[] = $term->name . ' (' . $term->slug . ')';
				echo implode( ', ', array_map( 'esc_html', $display_terms ) );
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'Venues', 'sportszone' ); ?>:</td>
			<td><?php
				$display_terms = array();
				$terms = get_terms( 'sz_venue', array( 'hide_empty' => 0 ) );
				foreach ( $terms as $term )
					$display_terms[] = $term->name . ' (' . $term->slug . ')';
				echo implode( ', ', array_map( 'esc_html', $display_terms ) );
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'Positions', 'sportszone' ); ?>:</td>
			<td><?php
				$display_terms = array();
				$terms = get_terms( 'sz_position', array( 'hide_empty' => 0 ) );
				foreach ( $terms as $term )
					$display_terms[] = $term->name . ' (' . $term->slug . ')';
				echo implode( ', ', array_map( 'esc_html', $display_terms ) );
			?></td>
		</tr>
	</tbody>

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'SP Post Types', 'sportszone' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		$post_types = sz_post_types();
		foreach ( $post_types as $post_type ):
		?>
		<tr>
			<td>
				<?php
				$object = get_post_type_object( $post_type );
				echo $object->labels->name;
				?>:
			</td>
			<td>
				<?php $count = wp_count_posts( $post_type ); ?>
				<?php echo $count->publish; ?> publish, <?php echo $count->future; ?> future, <?php echo $count->draft; ?> draft, <?php echo $count->private; ?> private, <?php echo $count->trash; ?> trash, <?php echo $count->{'auto-draft'}; ?> auto-draft, <?php echo $count->inherit; ?> inherit
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'Theme', 'sportszone' ); ?></th>
		</tr>
	</thead>

        <?php
        $active_theme = wp_get_theme();
        if ( $active_theme->{'Author URI'} == 'http://themeboy.com' ) :

			$theme_dir = substr( strtolower( str_replace( ' ','', $active_theme->Name ) ), 0, 45 );

			if ( false === ( $theme_version_data = get_transient( $theme_dir . '_version_data' ) ) ) :

        		$theme_changelog = wp_remote_get( 'http://dzv365zjfbd8v.cloudfront.net/changelogs/' . $theme_dir . '/changelog.txt' );
				$cl_lines  = explode( "\n", wp_remote_retrieve_body( $theme_changelog ) );
				if ( ! empty( $cl_lines ) ) :

					foreach ( $cl_lines as $line_num => $cl_line ) {
						if ( preg_match( '/^[0-9]/', $cl_line ) ) :

							$theme_date    		= str_replace( '.' , '-' , trim( substr( $cl_line , 0 , strpos( $cl_line , '-' ) ) ) );
							$theme_version      = preg_replace( '~[^0-9,.]~' , '' ,stristr( $cl_line , "version" ) );
							$theme_update       = trim( str_replace( "*" , "" , $cl_lines[ $line_num + 1 ] ) );
							$theme_version_data = array( 'date' => $theme_date , 'version' => $theme_version , 'update' => $theme_update , 'changelog' => $theme_changelog );
							set_transient( $theme_dir . '_version_data', $theme_version_data , 60*60*12 );
							break;

						endif;
					}

				endif;

			endif;

		endif;
		?>
	<tbody>
            <tr>
                <td><?php _e( 'Theme Name', 'sportszone' ); ?>:</td>
                <td><?php
					echo $active_theme->Name;
                ?></td>
            </tr>
            <tr>
                <td><?php _e( 'Theme Version', 'sportszone' ); ?>:</td>
                <td><?php
					echo $active_theme->Version;

					if ( ! empty( $theme_version_data['version'] ) && version_compare( $theme_version_data['version'], $active_theme->Version, '!=' ) )
						echo ' &ndash; <strong style="color:red;">' . $theme_version_data['version'] . ' ' . __( 'is available', 'sportszone' ) . '</strong>';
                ?></td>
            </tr>
            <tr>
                <td><?php _e( 'Author URL', 'sportszone' ); ?>:</td>
                <td><?php
					echo $active_theme->{'Author URI'};
                ?></td>
            </tr>
	</tbody>

	<thead>
		<tr>
			<th colspan="2"><?php _e( 'Templates', 'sportszone' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<?php

				$template_paths = apply_filters( 'sportszone_template_overrides_scan_paths', array( 'SportsPress' => SportsZone()->plugin_path() . '/templates/' ) );
				$found_files    = array();

				foreach ( $template_paths as $plugin_name => $template_path )
					$scanned_files[ $plugin_name ] = $this->scan_template_files( $template_path );

				foreach ( $scanned_files as $plugin_name => $files ) {
					foreach ( $files as $file ) {
						if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
							$theme_file = get_stylesheet_directory() . '/' . $file;
						} elseif ( file_exists( get_stylesheet_directory() . '/sportszone/' . $file ) ) {
							$theme_file = get_stylesheet_directory() . '/sportszone/' . $file;
						} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
							$theme_file = get_template_directory() . '/' . $file;
						} elseif( file_exists( get_template_directory() . '/sportszone/' . $file ) ) {
							$theme_file = get_template_directory() . '/sportszone/' . $file;
						} else {
							$theme_file = false;
						}

						if ( $theme_file ) {
							$core_version  = $this->get_file_version( SportsZone()->plugin_path() . '/templates/' . $file );
							$theme_version = $this->get_file_version( $theme_file );

							if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
								$found_files[ $plugin_name ][] = sprintf( __( '<code>%s</code> version <strong style="color:red">%s</strong> is out of date. The core version is %s', 'sportszone' ), basename( $theme_file ), $theme_version ? $theme_version : '-', $core_version );
							} else {
								$found_files[ $plugin_name ][] = sprintf( '<code>%s</code>', basename( $theme_file ) );
							}
						}
					}
				}

				if ( $found_files ) {
					foreach ( $found_files as $plugin_name => $found_plugin_files ) {
						?>
						<td><?php _e( 'Template Overrides', 'sportszone' ); ?> (<?php echo $plugin_name; ?>):</td>
						<td><?php echo implode( ', <br/>', $found_plugin_files ); ?></td>
						<?php
					}
				} else {
					?>
					<td><?php _e( 'Template Overrides', 'sportszone' ); ?>:</td>
					<td><?php _e( 'No overrides present in theme.', 'sportszone' ); ?></td>
					<?php
				}
			?>
		</tr>
	</tbody>

</table>

<script type="text/javascript">
	/*
	@var i string default
	@var l how many repeat s
	@var s string to repeat
	@var w where s should indent
	*/
	jQuery.sz_strPad = function(i,l,s,w) {
		var o = i.toString();
		if (!s) { s = '0'; }
		while (o.length < l) {
			// empty
			if(w == 'undefined'){
				o = s + o;
			}else{
				o = o + s;
			}
		}
		return o;
	};


	jQuery('a.debug-report').click(function(){

		var report = "";

		jQuery('.sp-status-table thead, .sp-status-table tbody').each(function(){

			if ( jQuery( this ).is('thead') ) {

				report = report + "\n### " + jQuery.trim( jQuery( this ).text() ) + " ###\n\n";

			} else {

				jQuery('tr', jQuery( this )).each(function(){

					var the_name    = jQuery.sz_strPad( jQuery.trim( jQuery( this ).find('td:eq(0)').text() ), 25, ' ' );
					var the_value   = jQuery.trim( jQuery( this ).find('td:eq(1)').text() );
					var value_array = the_value.split( ', ' );

					if ( value_array.length > 1 ){

						// if value have a list of plugins ','
						// split to add new line
						var output = '';
						var temp_line ='';
						jQuery.each( value_array, function(key, line){
							var tab = ( key == 0 )?0:25;
							temp_line = temp_line + jQuery.sz_strPad( '', tab, ' ', 'f' ) + line +'\n';
						});

						the_value = temp_line;
					}

					report = report +''+ the_name + the_value + "\n";
				});

			}
		} );

		try {
			jQuery("#debug-report").slideDown();
			jQuery("#debug-report textarea").val( report ).focus().select();
			jQuery(this).fadeOut();
			return false;
		} catch(e){ console.log( e ); }

		return false;
	});
</script>
