<?php
/**
 * Groups: Single group "Manage > Delete" screen handler
 *
 * @package SportsZone
 * @subpackage GroupsScreens
 * @since 3.0.0
 */

/**
 * Handle the display of the Delete Group page.
 *
 * @since 1.0.0
 */
function groups_screen_group_admin_delete_group() {

	if ( 'delete-group' != sz_get_group_current_admin_tab() )
		return false;

	if ( ! sz_is_item_admin() && !sz_current_user_can( 'sz_moderate' ) )
		return false;

	$sz = sportszone();

	if ( isset( $_REQUEST['delete-group-button'] ) && isset( $_REQUEST['delete-group-understand'] ) ) {

		// Check the nonce first.
		if ( !check_admin_referer( 'groups_delete_group' ) ) {
			return false;
		}

		/**
		 * Fires before the deletion of a group from the Delete Group page.
		 *
		 * @since 1.5.0
		 *
		 * @param int $id ID of the group being deleted.
		 */
		do_action( 'groups_before_group_deleted', $sz->groups->current_group->id );

		// Group admin has deleted the group, now do it.
		if ( !groups_delete_group( $sz->groups->current_group->id ) ) {
			sz_core_add_message( __( 'There was an error deleting the group. Please try again.', 'sportszone' ), 'error' );
		} else {
			sz_core_add_message( __( 'The group was deleted successfully.', 'sportszone' ) );

			/**
			 * Fires after the deletion of a group from the Delete Group page.
			 *
			 * @since 1.0.0
			 *
			 * @param int $id ID of the group being deleted.
			 */
			do_action( 'groups_group_deleted', $sz->groups->current_group->id );

			sz_core_redirect( trailingslashit( sz_loggedin_user_domain() . sz_get_groups_slug() ) );
		}

		sz_core_redirect( trailingslashit( sz_loggedin_user_domain() . sz_get_groups_slug() ) );
	}

	/**
	 * Fires before the loading of the Delete Group page template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the group that is being displayed.
	 */
	do_action( 'groups_screen_group_admin_delete_group', $sz->groups->current_group->id );

	/**
	 * Filters the template to load for the Delete Group page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Path to the Delete Group template.
	 */
	sz_core_load_template( apply_filters( 'groups_template_group_admin_delete_group', 'groups/single/home' ) );
}
add_action( 'sz_screens', 'groups_screen_group_admin_delete_group' );