<?php
/**
 * SportsZone Messages CSS and JS.
 *
 * @package SportsZone
 * @subpackage MessagesScripts
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Enqueue the JS for messages autocomplete.
 */
function messages_add_autocomplete_js() {

	// Include the autocomplete JS for composing a message.
	if ( sz_is_messages_component() && sz_is_current_action( 'compose' ) ) {
		add_action( 'wp_head', 'messages_autocomplete_init_jsblock' );

		$min = sz_core_get_minified_asset_suffix();
		$url = sportszone()->plugin_url . 'sz-messages/js/';

		wp_enqueue_script( 'sz-jquery-autocomplete', "{$url}autocomplete/jquery.autocomplete{$min}.js", array( 'jquery' ), sz_get_version() );
		wp_enqueue_script( 'sz-jquery-autocomplete-fb', "{$url}autocomplete/jquery.autocompletefb{$min}.js", array( 'jquery' ), sz_get_version() );
		wp_enqueue_script( 'sz-jquery-bgiframe', "{$url}autocomplete/jquery.bgiframe{$min}.js", array( 'jquery' ), sz_get_version() );
		wp_enqueue_script( 'sz-jquery-dimensions', "{$url}autocomplete/jquery.dimensions{$min}.js", array( 'jquery' ), sz_get_version() );
	}
}
add_action( 'sz_enqueue_scripts', 'messages_add_autocomplete_js' );

/**
 * Enqueue the CSS for messages autocomplete.
 *
 * @todo Why do we call wp_print_styles()?
 */
function messages_add_autocomplete_css() {
	if ( sz_is_messages_component() && sz_is_current_action( 'compose' ) ) {
		$min = sz_core_get_minified_asset_suffix();
		$url = sportszone()->plugin_url . 'sz-messages/css/';

		wp_enqueue_style( 'sz-messages-autocomplete', "{$url}autocomplete/jquery.autocompletefb{$min}.css", array(), sz_get_version() );

		wp_style_add_data( 'sz-messages-autocomplete', 'rtl', true );
		if ( $min ) {
			wp_style_add_data( 'sz-messages-autocomplete', 'suffix', $min );
		}

		wp_print_styles();
	}
}
add_action( 'wp_head', 'messages_add_autocomplete_css' );

/**
 * Print inline JS for initializing the messages autocomplete.
 *
 * @todo Why is this here and not in a properly enqueued file?
 */
function messages_autocomplete_init_jsblock() {
?>

	<script type="text/javascript">
		jQuery(document).ready(function() {
			var acfb = jQuery("ul.first").autoCompletefb({urlLookup: ajaxurl});

			jQuery('#send_message_form').submit( function() {
				var users = document.getElementById('send-to-usernames').className;
				document.getElementById('send-to-usernames').value = String(users);
			});
		});
	</script>

<?php
}
