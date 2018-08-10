<?php
/**
 * Deprecated functions
 *
 * @package SportsZone
 * @subpackage Core
 * @deprecated 2.2.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Detect a change in post status, and initiate an activity update if necessary.
 *
 * Posts get new activity updates when (a) they are being published, and (b)
 * they have not already been published. This enables proper posting for
 * regular posts as well as scheduled posts, while preventing post bumping.
 *
 * See #4090, #3746, #2546 for background.
 *
 * @since 2.0.0
 * @deprecated 2.2.0
 *
 * @todo Support untrashing better
 *
 * @param string $new_status New status for the post.
 * @param string $old_status Old status for the post.
 * @param object $post Post data.
 */
function sz_blogs_catch_transition_post_status( $new_status, $old_status, $post ) {
	_deprecated_function( __FUNCTION__, '2.2', 'sz_activity_catch_transition_post_type_status()' );
	sz_activity_catch_transition_post_type_status( $new_status, $old_status, $post );
}

/**
 * Record a new blog post in the SportsZone activity stream.
 *
 * @deprecated 2.2.0
 *
 * @param int $post_id ID of the post being recorded.
 * @param object $post The WP post object passed to the 'save_post' action.
 * @param int $user_id Optional. The user to whom the activity item will be
 *        associated. Defaults to the post_author.
 * @return bool|null Returns false on failure.
 */
function sz_blogs_record_post( $post_id, $post, $user_id = 0 ) {
	_deprecated_function( __FUNCTION__, '2.2', 'sz_activity_post_type_publish()' );
	sz_activity_post_type_publish( $post_id, $post, $user_id );
}

/**
 * Updates a blog post's corresponding activity entry during a post edit.
 *
 * @since 2.0.0
 * @deprecated 2.2.0
 *
 * @see sz_blogs_catch_transition_post_status()
 *
 * @param WP_Post $post
 */
function sz_blogs_update_post( $post ) {
	_deprecated_function( __FUNCTION__, '2.2', 'sz_activity_post_type_update()' );
	sz_activity_post_type_update( $post );
}

/**
 * Clear cache when a new blog is created.
 *
 * @since 1.0.0
 * @deprecated 2.2.0
 *
 * @param SZ_Blogs_Blog $recorded_blog_obj The recorded blog, passed by
 *        'sz_blogs_new_blog'.
 */
function sz_blogs_format_clear_blog_cache( $recorded_blog_obj ) {
	_deprecated_function( __FUNCTION__, '2.2', 'sz_blogs_clear_blog_object_cache()' );
	sz_blogs_clear_blog_object_cache( false, $recorded_blog_obj->user_id );
}

/**
 * Format 'new_member' activity actions.
 *
 * @since 2.0.0
 * @deprecated 2.2.0
 *
 * @param string $action Static activity action.
 * @param object $activity Activity object.
 * @return string
 */
function sz_xprofile_format_activity_action_new_member( $action, $activity ) {
	_deprecated_function( __FUNCTION__, '2.2', 'sz_members_format_activity_action_new_member()' );

	$action = apply_filters( 'sz_xprofile_format_activity_action_new_member', $action, $activity );
	return sz_members_format_activity_action_new_member( $action, $activity );
}

/**
 * Add 'sz' to global group of network wide cachable objects.
 *
 * @since 1.1.0
 * @deprecated 2.2.0
 */
function sz_core_add_global_group() {
	_deprecated_function( __FUNCTION__, '2.2', 'This function has no replacement' );
}

/**
 * Add a piece of message metadata.
 *
 * @deprecated 2.2.2
 */
function sz_message_add_meta( $message_id, $meta_key, $meta_value, $unique = false ) {
	_deprecated_function( __FUNCTION__, '2.3.0', 'sz_messages_add_meta()' );
	return sz_messages_add_meta( $message_id, $meta_key, $meta_value, $unique );
}
