<?php
/**
 * SportsZone Messages Filters.
 *
 * Apply WordPress defined filters to private messages.
 *
 * @package SportsZone
 * @subpackage MessagesFilters
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_filter( 'sz_get_message_notice_subject',        'wp_filter_kses', 1 );
add_filter( 'sz_get_message_notice_text',           'wp_filter_kses', 1 );
add_filter( 'sz_get_message_thread_subject',        'wp_filter_kses', 1 );
add_filter( 'sz_get_message_thread_excerpt',        'wp_filter_kses', 1 );
add_filter( 'sz_get_messages_subject_value',        'wp_filter_kses', 1 );
add_filter( 'sz_get_messages_content_value',        'wp_filter_kses', 1 );
add_filter( 'messages_message_subject_before_save', 'wp_filter_kses', 1 );
add_filter( 'messages_notice_subject_before_save',  'wp_filter_kses', 1 );
add_filter( 'sz_get_the_thread_subject',            'wp_filter_kses', 1 );

add_filter( 'sz_get_the_thread_message_content',    'sz_messages_filter_kses', 1 );
add_filter( 'messages_message_content_before_save', 'sz_messages_filter_kses', 1 );
add_filter( 'messages_notice_message_before_save',  'sz_messages_filter_kses', 1 );
add_filter( 'sz_get_message_thread_content',        'sz_messages_filter_kses', 1 );

add_filter( 'messages_message_content_before_save', 'force_balance_tags' );
add_filter( 'messages_message_subject_before_save', 'force_balance_tags' );
add_filter( 'messages_notice_message_before_save',  'force_balance_tags' );
add_filter( 'messages_notice_subject_before_save',  'force_balance_tags' );

if ( function_exists( 'wp_encode_emoji' ) ) {
	add_filter( 'messages_message_subject_before_save', 'wp_encode_emoji' );
	add_filter( 'messages_message_content_before_save', 'wp_encode_emoji' );
	add_filter( 'messages_notice_message_before_save',  'wp_encode_emoji' );
	add_filter( 'messages_notice_subject_before_save',  'wp_encode_emoji' );
}

add_filter( 'sz_get_message_notice_subject',     'wptexturize' );
add_filter( 'sz_get_message_notice_text',        'wptexturize' );
add_filter( 'sz_get_message_thread_subject',     'wptexturize' );
add_filter( 'sz_get_message_thread_excerpt',     'wptexturize' );
add_filter( 'sz_get_the_thread_message_content', 'wptexturize' );
add_filter( 'sz_get_message_thread_content',     'wptexturize' );

add_filter( 'sz_get_message_notice_subject',     'convert_smilies', 2 );
add_filter( 'sz_get_message_notice_text',        'convert_smilies', 2 );
add_filter( 'sz_get_message_thread_subject',     'convert_smilies', 2 );
add_filter( 'sz_get_message_thread_excerpt',     'convert_smilies', 2 );
add_filter( 'sz_get_the_thread_message_content', 'convert_smilies', 2 );
add_filter( 'sz_get_message_thread_content',     'convert_smilies', 2 );

add_filter( 'sz_get_message_notice_subject',     'convert_chars' );
add_filter( 'sz_get_message_notice_text',        'convert_chars' );
add_filter( 'sz_get_message_thread_subject',     'convert_chars' );
add_filter( 'sz_get_message_thread_excerpt',     'convert_chars' );
add_filter( 'sz_get_the_thread_message_content', 'convert_chars' );
add_filter( 'sz_get_message_thread_content',     'convert_chars' );

add_filter( 'sz_get_message_notice_text',        'make_clickable', 9 );
add_filter( 'sz_get_the_thread_message_content', 'make_clickable', 9 );
add_filter( 'sz_get_message_thread_content',     'make_clickable', 9 );

add_filter( 'sz_get_message_notice_text',        'wpautop' );
add_filter( 'sz_get_the_thread_message_content', 'wpautop' );
add_filter( 'sz_get_message_thread_content',     'wpautop' );

add_filter( 'sz_get_message_notice_subject',          'stripslashes_deep'    );
add_filter( 'sz_get_message_notice_text',             'stripslashes_deep'    );
add_filter( 'sz_get_message_thread_subject',          'stripslashes_deep'    );
add_filter( 'sz_get_message_thread_excerpt',          'stripslashes_deep'    );
add_filter( 'sz_get_message_get_recipient_usernames', 'stripslashes_deep'    );
add_filter( 'sz_get_messages_subject_value',          'stripslashes_deep'    );
add_filter( 'sz_get_messages_content_value',          'stripslashes_deep'    );
add_filter( 'sz_get_the_thread_message_content',      'stripslashes_deep'    );
add_filter( 'sz_get_the_thread_subject',              'stripslashes_deep'    );
add_filter( 'sz_get_message_thread_content',          'stripslashes_deep', 1 );

/**
 * Enforce limitations on viewing private message contents
 *
 * @since 2.3.2
 *
 * @see sz_has_message_threads() for description of parameters
 *
 * @param array|string $args See {@link sz_has_message_threads()}.
 * @return array|string
 */
function sz_messages_enforce_current_user( $args = array() ) {

	// Non-community moderators can only ever see their own messages.
	if ( is_user_logged_in() && ! sz_current_user_can( 'sz_moderate' ) ) {
		$_user_id = (int) sz_loggedin_user_id();
		if ( $_user_id !== (int) $args['user_id'] ) {
			$args['user_id'] = $_user_id;
		}
	}

	// Return possibly modified $args array.
	return $args;
}
add_filter( 'sz_after_has_message_threads_parse_args', 'sz_messages_enforce_current_user', 5 );

/**
 * Custom kses filtering for message content.
 *
 * @since 3.0.0
 *
 * @param string $content The message content.
 * @return string         The filtered message content.
 */
function sz_messages_filter_kses( $content ) {
	$messages_allowedtags      = sz_get_allowedtags();
	$messages_allowedtags['p'] = array();

	/**
	 * Filters the allowed HTML tags for SportsZone Messages content.
	 *
	 * @since 3.0.0
	 *
	 * @param array $value Array of allowed HTML tags and attributes.
	 */
	$messages_allowedtags = apply_filters( 'sz_messages_allowed_tags', $messages_allowedtags );
	return wp_kses( $content, $messages_allowedtags );
}
