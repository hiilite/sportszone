<?php
/**
 * Deprecated functions.
 *
 * @deprecated 2.8.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Determines whether the current installation is running PHP 5.3 or greater.
 *
 * SportsZone 2.8 introduces a minimum PHP requirement of PHP 5.3.
 *
 * @since 2.7.0
 * @deprecated 2.8.0
 *
 * @return bool
 */
function sz_core_admin_is_running_php53_or_greater() {
	_deprecated_function( __FUNCTION__, '2.8' );
	return version_compare( PHP_VERSION, '5.3', '>=' );
}

/**
 * Replaces WP's default update notice on plugins.php with an error message, when site is not running PHP 5.3 or greater.
 *
 * Originally hooked to 'load-plugins.php' with priority 100.
 *
 * @since 2.7.0
 * @deprecated 2.8.0
 */
function sz_core_admin_maybe_disable_update_row_for_php53_requirement() {
	if ( sz_core_admin_is_running_php53_or_greater() ) {
		return;
	}

	$loader = basename( constant( 'SZ_PLUGIN_DIR' ) ) . '/sz-loader.php';

	remove_action( "after_plugin_row_{$loader}", 'wp_plugin_update_row', 10 );
	add_action( "after_plugin_row_{$loader}", 'sz_core_admin_php52_plugin_row', 10, 2 );
}

/**
 * On the "Dashboard > Updates" page, remove SportsZone from plugins list if PHP < 5.3.
 *
 * Originally hooked to 'load-update-core.php'.
 *
 * @since 2.7.0
 * @deprecated 2.8.0
 */
function sz_core_admin_maybe_remove_from_update_core() {
	if ( sz_core_admin_is_running_php53_or_greater() ) {
		return;
	}

	// Add filter to remove BP from the update plugins list.
	add_filter( 'site_transient_update_plugins', 'sz_core_admin_remove_sportszone_from_update_transient' );
}

/**
 * Filter callback to remove SportsZone from the update plugins list.
 *
 * Attached to the 'site_transient_update_plugins' filter.
 *
 * @since 2.7.0
 * @deprecated 2.8.0
 *
 * @param  object $retval Object of plugin update data.
 * @return object
 */
function sz_core_admin_remove_sportszone_from_update_transient( $retval ) {
	_deprecated_function( __FUNCTION__, '2.8' );

	$loader = basename( constant( 'SZ_PLUGIN_DIR' ) ) . '/sz-loader.php';

	// Remove BP from update plugins list.
	if ( isset( $retval->response[ $loader ] ) ) {
		unset( $retval->response[ $loader ] );
	}

	return $retval;
}

/**
 * Outputs a replacement for WP's default update notice, when site is not running PHP 5.3 or greater.
 *
 * When we see that a site is not running PHP 5.3 and is trying to update to
 * BP 2.8+, we replace WP's default notice with our own, which both provides a
 * link to our documentation of the requirement, and removes the link that
 * allows a single plugin to be updated.
 *
 * @since 2.7.0
 * @deprecated 2.8.0
 *
 * @param string $file        Plugin filename. sportszone/sz-loader.php.
 * @param array  $plugin_data Data about the SportsZone plugin, as returned by the
 *                            plugins API.
 */
function sz_core_admin_php52_plugin_row( $file, $plugin_data ) {
	_deprecated_function( __FUNCTION__, '2.8' );

	if ( is_multisite() && ! is_network_admin() ) {
		return;
	}

	$current = get_site_transient( 'update_plugins' );
	if ( ! isset( $current->response[ $file ] ) ) {
		return false;
	}

	$response = $current->response[ $file ];

	// No need to do this if update is for < BP 2.8.
	if ( version_compare( $response->new_version, '2.8', '<' ) ) {
		return false;
	}

	$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );

	if ( is_network_admin() ) {
		$active_class = is_plugin_active_for_network( $file ) ? ' active' : '';
	} else {
		$active_class = is_plugin_active( $file ) ? ' active' : '';
	}

	// WP 4.6 uses different markup for the plugin row notice.
	if ( function_exists( 'wp_get_ext_types' ) ) {
		$p = '<p>%s</p>';

	// WP < 4.6.
	} else {
		$p = '%s';

		// Ugh.
		$active_class .= ' not-shiny';
	}

	echo '<tr class="plugin-update-tr' . $active_class . '" id="' . esc_attr( $response->slug . '-update' ) . '" data-slug="' . esc_attr( $response->slug ) . '" data-plugin="' . esc_attr( $file ) . '"><td colspan="' . esc_attr( $wp_list_table->get_column_count() ) . '" class="plugin-update colspanchange"><div class="update-message inline notice notice-error notice-alt">';

	printf( $p,
		esc_html__( 'A SportsZone update is available, but your system is not compatible.', 'sportszone' ) . ' ' .
		sprintf( __( 'See <a href="%s">the Codex guide</a> for more information.', 'sportszone' ), 'https://codex.sportszone.org/getting-started/sportszone-2-8-will-require-php-5-3/' )
	);

	echo '</div></td></tr>';

	/*
	 * JavaScript to disable the bulk upgrade checkbox.
	 * See WP_Plugins_List_Table::single_row().
	 */
	$checkbox_id = 'checkbox_' . md5( $plugin_data['Name'] );
	echo "<script type='text/javascript'>document.getElementById('$checkbox_id').disabled = true;</script>";
}

/**
 * Add an admin notice to installations that are not running PHP 5.3+.
 *
 * @since 2.7.0
 * @deprecated 2.8.0
 */
function sz_core_admin_php53_admin_notice() {
	_deprecated_function( __FUNCTION__, '2.8' );

	// If not on the Plugins page, stop now.
	if ( 'plugins' !== get_current_screen()->parent_base ) {
		return;
	}

	if ( ! current_user_can( 'update_core' ) ) {
		return;
	}

	if ( sz_core_admin_is_running_php53_or_greater() ) {
		return;
	}

	$notice_id = 'bp28-php53';
	if ( sz_get_option( "sz-dismissed-notice-$notice_id" ) ) {
		return;
	}

	$sz  = sportszone();
	$min = sz_core_get_minified_asset_suffix();

	wp_enqueue_script(
		'sz-dismissible-admin-notices',
		"{$sz->plugin_url}sz-core/admin/js/dismissible-admin-notices{$min}.js",
		array( 'jquery' ),
		sz_get_version(),
		true
	);
	?>

	<div id="message" class="error notice is-dismissible sz-is-dismissible" data-noticeid="<?php echo esc_attr( $notice_id ); ?>">
		<p><strong><?php esc_html_e( 'Your site is not ready for SportsZone 2.8.', 'sportszone' ); ?></strong></p>
		<p><?php printf( esc_html__( 'Your site is currently running PHP version %s, while SportsZone 2.8 will require version 5.3+.', 'sportszone' ), esc_html( phpversion() ) ); ?> <?php printf( __( 'See <a href="%s">the Codex guide</a> for more information.', 'sportszone' ), 'https://codex.sportszone.org/getting-started/sportszone-2-8-will-require-php-5-3/' ); ?></p>
		<?php wp_nonce_field( "sz-dismissible-notice-$notice_id", "sz-dismissible-nonce-$notice_id" ); ?>
	</div>
	<?php
}

