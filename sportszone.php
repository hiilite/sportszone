<?php
/**
 * Plugin Name: SportsZone
 * Plugin URI:  https://sportszone.org/
 * Description: SportsZone adds community features to WordPress. Member Profiles, Activity Streams, Direct Messaging, Notifications, and more!
 * Author:      The SportsZone Community
 * Author URI:  https://sportszone.org/
 * GitHub Plugin URI: https://github.com/hiilite/sportszone
 * Version:     3.1.2
 * Text Domain: sportszone
 * Domain Path: /sz-languages/
 * License:     GPLv2 or later (license.txt)
 */

/**
 * This files should always remain compatible with the minimum version of
 * PHP supported by WordPress.
 */
 
/**
 * The SportsZone Plugin.
 *
 * SportsZone is social networking software with a twist from the creators of WordPress.
 *
 * @package SportsZone
 * @subpackage Main
 * @since 1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Required PHP version. 
define( 'SZ_REQUIRED_PHP_VERSION', '5.3.0' );

/**
 * The main function responsible for returning the one true SportsZone Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $sz = sportszone(); ?>
 *
 * @return SportsZone|null The one true SportsZone Instance.
 */
function sportszone() {
	return SportsZone::instance();
}

/**
 * Adds an admin notice to installations that don't meet BP's minimum PHP requirement.
 *
 * @since 2.8.0
 */
function sz_php_requirements_notice() {
	if ( ! current_user_can( 'update_core' ) ) {
		return;
	}

	?>

	<div id="message" class="error notice">
		<p><strong><?php esc_html_e( 'Your site does not support SportsZone.', 'sportszone' ); ?></strong></p>
		<?php /* translators: 1: current PHP version, 2: required PHP version */ ?>
		<p><?php printf( esc_html__( 'Your site is currently running PHP version %1$s, while SportsZone requires version %2$s or greater.', 'sportszone' ), esc_html( phpversion() ), esc_html( SZ_REQUIRED_PHP_VERSION ) ); ?> <?php printf( __( 'See <a href="%s">the Codex guide</a> for more information.', 'sportszone' ), 'https://codex.sportszone.org/getting-started/sportszone-2-8-will-require-php-5-3/' ); ?></p>
		<p><?php esc_html_e( 'Please update your server or deactivate SportsZone.', 'sportszone' ); ?></p>
	</div>

	<?php
}

if ( version_compare( phpversion(), SZ_REQUIRED_PHP_VERSION, '<' ) ) {
	add_action( 'admin_notices', 'sz_php_requirements_notice' );
	add_action( 'network_admin_notices', 'sz_php_requirements_notice' );
	return;
} else {
	require dirname( __FILE__ ) . '/class-sportszone.php';

	/*
	 * Hook SportsZone early onto the 'plugins_loaded' action.
	 *
	 * This gives all other plugins the chance to load before SportsZone,
	 * to get their actions, filters, and overrides setup without
	 * SportsZone being in the way.
	 */
	if ( defined( 'SPORTSZONE_LATE_LOAD' ) ) {
		add_action( 'plugins_loaded', 'sportszone', (int) SPORTSZONE_LATE_LOAD );

	// "And now here's something we hope you'll really like!"
	} else {
		$GLOBALS['sz'] = sportszone();
	}
}
