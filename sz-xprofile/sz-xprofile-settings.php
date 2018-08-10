<?php
/**
 * SportsZone XProfile Settings.
 *
 * @package    SportsZone
 * @subpackage XProfileSettings
 * @since 2.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Query all profile fields and their visibility data for display in settings.
 *
 * @since 2.0.0
 *
 * @param array|string $args Array of args for the settings fields.
 * @return bool
 */
function sz_xprofile_get_settings_fields( $args = '' ) {

	// Parse the possible arguments.
	$r = sz_parse_args( $args, array(
		'user_id'                => sz_displayed_user_id(),
		'profile_group_id'       => false,
		'hide_empty_groups'      => false,
		'hide_empty_fields'      => false,
		'fetch_fields'           => true,
		'fetch_field_data'       => false,
		'fetch_visibility_level' => true,
		'exclude_groups'         => false,
		'exclude_fields'         => false
	), 'xprofile_get_settings_fields' );

	return sz_has_profile( $r );
}

/**
 * Adds feedback messages when successfully saving profile field settings.
 *
 * @since 2.0.0
 *
 */
function sz_xprofile_settings_add_feedback_message() {

	// Default message type is success.
	$type    = 'success';
	$message = __( 'Your profile settings have been saved.',        'sportszone' );

	// Community moderator editing another user's settings.
	if ( ! sz_is_my_profile() && sz_core_can_edit_settings() ) {
		$message = __( "This member's profile settings have been saved.", 'sportszone' );
	}

	// Add the message.
	sz_core_add_message( $message, $type );
}
add_action( 'sz_xprofile_settings_after_save', 'sz_xprofile_settings_add_feedback_message' );
