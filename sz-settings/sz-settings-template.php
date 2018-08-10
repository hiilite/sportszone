<?php
/**
 * SportsZone Settings Template Functions.
 *
 * @package SportsZone
 * @subpackage SettingsTemplate
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Output the settings component slug.
 *
 * @since 1.5.0
 *
 */
function sz_settings_slug() {
	echo sz_get_settings_slug();
}
	/**
	 * Return the settings component slug.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	function sz_get_settings_slug() {

		/**
		 * Filters the Settings component slug.
		 *
		 * @since 1.5.0
		 *
		 * @param string $slug Settings component slug.
		 */
		return apply_filters( 'sz_get_settings_slug', sportszone()->settings->slug );
	}

/**
 * Output the settings component root slug.
 *
 * @since 1.5.0
 *
 */
function sz_settings_root_slug() {
	echo sz_get_settings_root_slug();
}
	/**
	 * Return the settings component root slug.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	function sz_get_settings_root_slug() {

		/**
		 * Filters the Settings component root slug.
		 *
		 * @since 1.5.0
		 *
		 * @param string $root_slug Settings component root slug.
		 */
		return apply_filters( 'sz_get_settings_root_slug', sportszone()->settings->root_slug );
	}

/**
 * Add the 'pending email change' message to the settings page.
 *
 * @since 2.1.0
 */
function sz_settings_pending_email_notice() {
	$pending_email = sz_get_user_meta( sz_displayed_user_id(), 'pending_email_change', true );

	if ( empty( $pending_email['newemail'] ) ) {
		return;
	}

	if ( sz_get_displayed_user_email() == $pending_email['newemail'] ) {
		return;
	}

	?>

	<div id="message" class="sz-template-notice error">
		<p><?php printf(
			__( 'There is a pending change of your email address to %s.', 'sportszone' ),
			'<code>' . esc_html( $pending_email['newemail'] ) . '</code>'
		); ?>
		<br />
		<?php printf(
			__( 'Check your email (%1$s) for the verification link, or <a href="%2$s">cancel the pending change</a>.', 'sportszone' ),
			'<code>' . esc_html( sz_get_displayed_user_email() ) . '</code>',
			esc_url( wp_nonce_url( sz_displayed_user_domain() . sz_get_settings_slug() . '/?dismiss_email_change=1', 'sz_dismiss_email_change' ) )
		); ?></p>
	</div>

	<?php
}
add_action( 'sz_before_member_settings_template', 'sz_settings_pending_email_notice' );
