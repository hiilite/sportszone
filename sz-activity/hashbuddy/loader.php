<?php
/*
Plugin Based on: HashBuddy
Plugin URI: http://taptappress.com
Description: Hashtags for WordPress, SportsZone and bbPress
Version: 1.5.2
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: modemlooper
Author URI: http://twitter.com/modemlooper
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

require( dirname( __FILE__ ) . '/includes/hashbuddy.php' );
define('HASHBUDDY_URL', plugin_dir_url( __FILE__ ) );


function hashbuddy_bsz_hashtags_init() {

	add_filter( 'bsz_new_topic_pre_content', 'hashbuddy_bbpress_hashtags_filter' );
	add_filter( 'bsz_edit_topic_pre_content', 'hashbuddy_bbpress_hashtags_filter' );
	add_filter( 'bsz_new_reply_pre_content', 'hashbuddy_bbpress_hashtags_filter' );
	add_filter( 'bsz_edit_reply_pre_content', 'hashbuddy_bbpress_hashtags_filter' );

}
add_action( 'wp', 'hashbuddy_bsz_hashtags_init', 88 );


function hashbuddy_activity_hashtags_init() {

	if ( !sz_is_active( 'activity' ) )
		return;

	add_filter( 'sz_activity_comment_content', 'hashbuddy_activity_hashtags_filter' );
	add_filter( 'sz_activity_new_update_content', 'hashbuddy_activity_hashtags_filter' );
	add_filter( 'groups_activity_new_update_content', 'hashbuddy_activity_hashtags_filter' );
	add_filter( 'events_activity_new_update_content', 'hashbuddy_activity_hashtags_filter' );

	add_filter( 'sz_blogs_activity_new_post_content', 'hashbuddy_activity_hashtags_filter' );
	add_filter( 'sz_blogs_activity_new_comment_content', 'hashbuddy_activity_hashtags_filter' );

	//support edit activity stream plugin
	add_filter( 'sz_edit_activity_action_edit_content', 'hashbuddy_activity_hashtags_filter' );

}
add_action( 'sz_include', 'hashbuddy_activity_hashtags_init', 88 );