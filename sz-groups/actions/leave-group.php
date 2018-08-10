<?php
/**
 * Groups: Leave action
 *
 * @package SportsZone
 * @subpackage GroupActions
 * @since 3.0.0
 */

/**
 * Catch and process "Leave Group" button clicks.
 *
 * When a group member clicks on the "Leave Group" button from a group's page,
 * this function is run.
 *
 * Note: When leaving a group from the group directory, AJAX is used and
 * another function handles this. See {@link sz_legacy_theme_ajax_joinleave_group()}.
 *
 * @since 1.2.4
 *
 * @return bool
 */
function groups_action_leave_group() {
	if ( ! sz_is_single_item() || ! sz_is_groups_component() || ! sz_is_current_action( 'leave-group' ) ) {
		return false;
	}

	// Nonce check.
	if ( ! check_admin_referer( 'groups_leave_group' ) ) {
		return false;
	}

	// User wants to leave any group.
	if ( groups_is_user_member( sz_loggedin_user_id(), sz_get_current_group_id() ) ) {
		$sz = sportszone();

		// Stop sole admins from abandoning their group.
		$group_admins = groups_get_group_admins( sz_get_current_group_id() );

		if ( 1 == count( $group_admins ) && $group_admins[0]->user_id == sz_loggedin_user_id() ) {
			sz_core_add_message( __( 'This group must have at least one admin', 'sportszone' ), 'error' );
		} elseif ( ! groups_leave_group( $sz->groups->current_group->id ) ) {
			sz_core_add_message( __( 'There was an error leaving the group.', 'sportszone' ), 'error' );
		} else {
			sz_core_add_message( __( 'You successfully left the group.', 'sportszone' ) );
		}

		$group = groups_get_current_group();
		$redirect = sz_get_group_permalink( $group );

		if ( ! $group->is_visible ) {
			$redirect = trailingslashit( sz_loggedin_user_domain() . sz_get_groups_slug() );
		}

		sz_core_redirect( $redirect );
	}

	/** This filter is documented in sz-groups/sz-groups-actions.php */
	sz_core_load_template( apply_filters( 'groups_template_group_home', 'groups/single/home' ) );
}
add_action( 'sz_actions', 'groups_action_leave_group' );