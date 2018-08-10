<?php
/**
 * Activity: Reply action
 *
 * @package SportsZone
 * @subpackage ActivityActions
 * @since 3.0.0
 */

/**
 * Post new activity comment.
 *
 * @since 1.2.0
 *
 * @return bool False on failure.
 */
function sz_activity_action_post_comment() {
	if ( !is_user_logged_in() || !sz_is_activity_component() || !sz_is_current_action( 'reply' ) )
		return false;

	// Check the nonce.
	check_admin_referer( 'new_activity_comment', '_wpnonce_new_activity_comment' );

	/**
	 * Filters the activity ID a comment will be in reply to.
	 *
	 * @since 1.2.0
	 *
	 * @param string $value ID of the activity being replied to.
	 */
	$activity_id = apply_filters( 'sz_activity_post_comment_activity_id', $_POST['comment_form_id'] );

	/**
	 * Filters the comment content for a comment reply.
	 *
	 * @since 1.2.0
	 *
	 * @param string $value Comment content being posted.
	 */
	$content = apply_filters( 'sz_activity_post_comment_content', $_POST['ac_input_' . $activity_id] );

	if ( empty( $content ) ) {
		sz_core_add_message( __( 'Please do not leave the comment area blank.', 'sportszone' ), 'error' );
		sz_core_redirect( wp_get_referer() . '#ac-form-' . $activity_id );
	}

	$comment_id = sz_activity_new_comment( array(
		'content'     => $content,
		'activity_id' => $activity_id,
		'parent_id'   => false
	));

	if ( !empty( $comment_id ) )
		sz_core_add_message( __( 'Reply Posted!', 'sportszone' ) );
	else
		sz_core_add_message( __( 'There was an error posting that reply. Please try again.', 'sportszone' ), 'error' );

	sz_core_redirect( wp_get_referer() . '#ac-form-' . $activity_id );
}
add_action( 'sz_actions', 'sz_activity_action_post_comment' );