<?php
/**
 * Main SportsZone Admin Class.
 *
 * @package SportsZone
 * @subpackage CoreAdministration
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup SportsZone Admin.
 *
 * @since 1.6.0
 *
 */
function sz_admin() {
	sportszone()->admin = new SZ_Admin();
	return;


	// These are strings we may use to describe maintenance/security releases, where we aim for no new strings.
	_n_noop( 'Maintenance Release', 'Maintenance Releases', 'sportszone' );
	_n_noop( 'Security Release', 'Security Releases', 'sportszone' );
	_n_noop( 'Maintenance and Security Release', 'Maintenance and Security Releases', 'sportszone' );

	/* translators: 1: SportsZone version number. */
	_n_noop(
		'<strong>Version %1$s</strong> addressed a security issue.',
		'<strong>Version %1$s</strong> addressed some security issues.',
		'sportszone'
	);

	/* translators: 1: SportsZone version number, 2: plural number of bugs. */
	_n_noop(
		'<strong>Version %1$s</strong> addressed %2$s bug.',
		'<strong>Version %1$s</strong> addressed %2$s bugs.',
		'sportszone'
	);

	/* translators: 1: SportsZone version number, 2: plural number of bugs. Singular security issue. */
	_n_noop(
		'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
		'<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.',
		'sportszone'
	);

	/* translators: 1: SportsZone version number, 2: plural number of bugs. More than one security issue. */
	_n_noop(
		'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
		'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.',
		'sportszone'
	);

	__( 'For more information, see <a href="%s">the release notes</a>.', 'sportszone' );
}
