<?php
/**
 * SportsZone Tools panel.
 *
 * @package SportsZone
 * @subpackage Core
 * @since 2.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Render the SportsZone Tools page.
 *
 * @since 2.0.0
 */
function sz_core_admin_tools() {
	?>
	<div class="wrap">

		<h1><?php esc_html_e( 'SportsZone Tools', 'sportszone' ) ?></h1>

		<p><?php esc_html_e( 'SportsZone keeps track of various relationships between members, groups, and activity items. Occasionally these relationships become out of sync, most often after an import, update, or migration.', 'sportszone' ); ?></p>
		<p><?php esc_html_e( 'Use the tools below to manually recalculate these relationships.', 'sportszone' ); ?>
		</p>
		<p class="description"><?php esc_html_e( 'Some of these tools create substantial database overhead. Avoid running more than one repair job at a time.', 'sportszone' ); ?></p>

		<form class="settings" method="post" action="">

			<fieldset>
				<legend><?php esc_html_e( 'Repair tools', 'sportszone' ) ?></legend>

				<div class="checkbox">
				<?php foreach ( sz_admin_repair_list() as $item ) : ?>
					<label for="<?php echo esc_attr( str_replace( '_', '-', $item[0] ) ); ?>"><input type="checkbox" class="checkbox" name="<?php echo esc_attr( $item[0] ) . '" id="' . esc_attr( str_replace( '_', '-', $item[0] ) ); ?>" value="1" /> <?php echo esc_html( $item[1] ); ?></label>
				<?php endforeach; ?>
				</div>

				<p class="submit">
					<input class="button-primary" type="submit" name="sz-tools-submit" value="<?php esc_attr_e( 'Repair Items', 'sportszone' ); ?>" />
					<?php wp_nonce_field( 'sz-do-counts' ); ?>
				</p>

			</fieldset>

		</form>

	</div>

	<?php
}

/**
 * Handle the processing and feedback of the admin tools page.
 *
 * @since 2.0.0
 */
function sz_admin_repair_handler() {
	if ( ! sz_is_post_request() || empty( $_POST['sz-tools-submit'] ) ) {
		return;
	}

	check_admin_referer( 'sz-do-counts' );

	// Bail if user cannot moderate.
	$capability = sz_core_do_network_admin() ? 'manage_network_options' : 'manage_options';
	if ( ! sz_current_user_can( $capability ) ) {
		return;
	}

	wp_cache_flush();
	$messages = array();

	foreach ( (array) sz_admin_repair_list() as $item ) {
		if ( isset( $item[2] ) && isset( $_POST[$item[0]] ) && 1 === absint( $_POST[$item[0]] ) && is_callable( $item[2] ) ) {
			$messages[] = call_user_func( $item[2] );
		}
	}

	if ( count( $messages ) ) {
		foreach ( $messages as $message ) {
			sz_admin_tools_feedback( $message[1] );
		}
	}
}
add_action( sz_core_admin_hook(), 'sz_admin_repair_handler' );

/**
 * Get the array of the repair list.
 *
 * @return array
 */
function sz_admin_repair_list() {
	$repair_list = array();

	// Members:
	// - member count
	// - last_activity migration (2.0).
	$repair_list[20] = array(
		'sz-total-member-count',
		__( 'Repair total members count.', 'sportszone' ),
		'sz_admin_repair_count_members',
	);

	$repair_list[25] = array(
		'sz-last-activity',
		__( 'Repair member "last activity" data.', 'sportszone' ),
		'sz_admin_repair_last_activity',
	);

	// Friends:
	// - user friend count.
	if ( sz_is_active( 'friends' ) ) {
		$repair_list[0] = array(
			'sz-user-friends',
			__( 'Repair total friends count for each member.', 'sportszone' ),
			'sz_admin_repair_friend_count',
		);
	}

	// Groups:
	// - user group count.
	if ( sz_is_active( 'groups' ) ) {
		$repair_list[10] = array(
			'sz-group-count',
			__( 'Repair total groups count for each member.', 'sportszone' ),
			'sz_admin_repair_group_count',
		);
	}

	// Blogs:
	// - user blog count.
	if ( sz_is_active( 'blogs' ) ) {
		$repair_list[90] = array(
			'sz-blog-records',
			__( 'Repopulate site tracking records.', 'sportszone' ),
			'sz_admin_repair_blog_records',
		);
	}

	// Emails:
	// - reinstall emails.
	$repair_list[100] = array(
		'sz-reinstall-emails',
		__( 'Reinstall emails (delete and restore from defaults).', 'sportszone' ),
		'sz_admin_reinstall_emails',
	);

	ksort( $repair_list );

	/**
	 * Filters the array of the repair list.
	 *
	 * @since 2.0.0
	 *
	 * @param array $repair_list Array of values for the Repair list options.
	 */
	return (array) apply_filters( 'sz_repair_list', $repair_list );
}

/**
 * Recalculate friend counts for each user.
 *
 * @since 2.0.0
 *
 * @return array
 */
function sz_admin_repair_friend_count() {
	global $wpdb;

	if ( ! sz_is_active( 'friends' ) ) {
		return;
	}

	$statement = __( 'Counting the number of friends for each user&hellip; %s', 'sportszone' );
	$result    = __( 'Failed!', 'sportszone' );

	$sql_delete = "DELETE FROM {$wpdb->usermeta} WHERE meta_key IN ( 'total_friend_count' );";
	if ( is_wp_error( $wpdb->query( $sql_delete ) ) ) {
		return array( 1, sprintf( $statement, $result ) );
	}

	$sz = sportszone();

	// Walk through all users on the site.
	$total_users = $wpdb->get_row( "SELECT count(ID) as c FROM {$wpdb->users}" )->c;

	$updated = array();
	if ( $total_users > 0 ) {
		$per_query = 500;
		$offset = 0;
		while ( $offset < $total_users ) {
			// Only bother updating counts for users who actually have friendships.
			$friendships = $wpdb->get_results( $wpdb->prepare( "SELECT initiator_user_id, friend_user_id FROM {$sz->friends->table_name} WHERE is_confirmed = 1 AND ( ( initiator_user_id > %d AND initiator_user_id <= %d ) OR ( friend_user_id > %d AND friend_user_id <= %d ) )", $offset, $offset + $per_query, $offset, $offset + $per_query ) );

			// The previous query will turn up duplicates, so we
			// filter them here.
			foreach ( $friendships as $friendship ) {
				if ( ! isset( $updated[ $friendship->initiator_user_id ] ) ) {
					SZ_Friends_Friendship::total_friend_count( $friendship->initiator_user_id );
					$updated[ $friendship->initiator_user_id ] = 1;
				}

				if ( ! isset( $updated[ $friendship->friend_user_id ] ) ) {
					SZ_Friends_Friendship::total_friend_count( $friendship->friend_user_id );
					$updated[ $friendship->friend_user_id ] = 1;
				}
			}

			$offset += $per_query;
		}
	} else {
		return array( 2, sprintf( $statement, $result ) );
	}

	return array( 0, sprintf( $statement, __( 'Complete!', 'sportszone' ) ) );
}

/**
 * Recalculate group counts for each user.
 *
 * @since 2.0.0
 *
 * @return array
 */
function sz_admin_repair_group_count() {
	global $wpdb;

	if ( ! sz_is_active( 'groups' ) ) {
		return;
	}

	$statement = __( 'Counting the number of groups for each user&hellip; %s', 'sportszone' );
	$result    = __( 'Failed!', 'sportszone' );

	$sql_delete = "DELETE FROM {$wpdb->usermeta} WHERE meta_key IN ( 'total_group_count' );";
	if ( is_wp_error( $wpdb->query( $sql_delete ) ) ) {
		return array( 1, sprintf( $statement, $result ) );
	}

	$sz = sportszone();

	// Walk through all users on the site.
	$total_users = $wpdb->get_row( "SELECT count(ID) as c FROM {$wpdb->users}" )->c;

	if ( $total_users > 0 ) {
		$per_query = 500;
		$offset = 0;
		while ( $offset < $total_users ) {
			// But only bother to update counts for users that have groups.
			$users = $wpdb->get_col( $wpdb->prepare( "SELECT user_id FROM {$sz->groups->table_name_members} WHERE is_confirmed = 1 AND is_banned = 0 AND user_id > %d AND user_id <= %d", $offset, $offset + $per_query ) );

			foreach ( $users as $user ) {
				SZ_Groups_Member::refresh_total_group_count_for_user( $user );
			}

			$offset += $per_query;
		}
	} else {
		return array( 2, sprintf( $statement, $result ) );
	}

	return array( 0, sprintf( $statement, __( 'Complete!', 'sportszone' ) ) );
}

/**
 * Recalculate user-to-blog relationships and useful blog meta data.
 *
 * @since 2.1.0
 *
 * @return array
 */
function sz_admin_repair_blog_records() {

	// Description of this tool, displayed to the user.
	$statement = __( 'Repopulating Blogs records&hellip; %s', 'sportszone' );

	// Default to failure text.
	$result    = __( 'Failed!',   'sportszone' );

	// Default to unrepaired.
	$repair    = false;

	// Run function if blogs component is active.
	if ( sz_is_active( 'blogs' ) ) {
		$repair = sz_blogs_record_existing_blogs();
	}

	// Setup success/fail messaging.
	if ( true === $repair ) {
		$result = __( 'Complete!', 'sportszone' );
	}

	// All done!
	return array( 0, sprintf( $statement, $result ) );
}

/**
 * Recalculate the total number of active site members.
 *
 * @since 2.0.0
 */
function sz_admin_repair_count_members() {
	$statement = __( 'Counting the number of active members on the site&hellip; %s', 'sportszone' );
	delete_transient( 'sz_active_member_count' );
	sz_core_get_active_member_count();
	return array( 0, sprintf( $statement, __( 'Complete!', 'sportszone' ) ) );
}

/**
 * Repair user last_activity data.
 *
 * Re-runs the migration from usermeta introduced in BP 2.0.
 *
 * @since 2.0.0
 */
function sz_admin_repair_last_activity() {
	$statement = __( 'Determining last activity dates for each user&hellip; %s', 'sportszone' );
	sz_last_activity_migrate();
	return array( 0, sprintf( $statement, __( 'Complete!', 'sportszone' ) ) );
}

/**
 * Assemble admin notices relating success/failure of repair processes.
 *
 * @since 2.0.0
 *
 * @param string      $message Feedback message.
 * @param string|bool $class   Unused.
 * @return false|Closure
 */
function sz_admin_tools_feedback( $message, $class = false ) {
	if ( is_string( $message ) ) {
		$message = '<p>' . $message . '</p>';
		$class = $class ? $class : 'updated';
	} elseif ( is_wp_error( $message ) ) {
		$errors = $message->get_error_messages();

		switch ( count( $errors ) ) {
			case 0:
				return false;

			case 1:
				$message = '<p>' . $errors[0] . '</p>';
				break;

			default:
				$message = '<ul>' . "\n\t" . '<li>' . implode( '</li>' . "\n\t" . '<li>', $errors ) . '</li>' . "\n" . '</ul>';
				break;
		}

		$class = $class ? $class : 'error';
	} else {
		return false;
	}

	$message = '<div id="message" class="' . esc_attr( $class ) . '">' . $message . '</div>';
	$message = str_replace( "'", "\'", $message );
	$lambda  = function() use ( $message ) { echo $message; };

	add_action( sz_core_do_network_admin() ? 'network_admin_notices' : 'admin_notices', $lambda );

	return $lambda;
}

/**
 * Render the Available Tools page.
 *
 * We register this page on Network Admin as a top-level home for our
 * SportsZone tools. This displays the default content.
 *
 * @since 2.0.0
 */
function sz_core_admin_available_tools_page() {
	?>
	<div class="wrap">
		<h1><?php esc_attr_e( 'Tools', 'sportszone' ) ?></h1>

		<?php

		/**
		 * Fires inside the markup used to display the Available Tools page.
		 *
		 * @since 2.0.0
		 */
		do_action( 'sz_network_tool_box' ); ?>

	</div>
	<?php
}

/**
 * Render an introduction of SportsZone tools on Available Tools page.
 *
 * @since 2.0.0
 */
function sz_core_admin_available_tools_intro() {
	$query_arg = array(
		'page' => 'sz-tools'
	);

	$page = sz_core_do_network_admin() ? 'admin.php' : 'tools.php' ;
	$url  = add_query_arg( $query_arg, sz_get_admin_url( $page ) );
	?>
	<div class="card tool-box">
		<h2><?php esc_html_e( 'SportsZone Tools', 'sportszone' ) ?></h2>
		<p>
			<?php esc_html_e( 'SportsZone keeps track of various relationships between users, groups, and activity items. Occasionally these relationships become out of sync, most often after an import, update, or migration.', 'sportszone' ); ?>
			<?php printf( esc_html_x( 'Use the %s to repair these relationships.', 'sportszone tools intro', 'sportszone' ), '<a href="' . esc_url( $url ) . '">' . esc_html__( 'SportsZone Tools', 'sportszone' ) . '</a>' ); ?>
		</p>
	</div>
	<?php
}

/**
 * Delete emails and restore from defaults.
 *
 * @since 2.5.0
 *
 * @return array
 */
function sz_admin_reinstall_emails() {
	$switched = false;

	// Switch to the root blog, where the email posts live.
	if ( ! sz_is_root_blog() ) {
		switch_to_blog( sz_get_root_blog_id() );
		sz_register_taxonomies();

		$switched = true;
	}

	$emails = get_posts( array(
		'fields'           => 'ids',
		'post_status'      => 'publish',
		'post_type'        => sz_get_email_post_type(),
		'posts_per_page'   => 200,
		'suppress_filters' => false,
	) );

	if ( $emails ) {
		foreach ( $emails as $email_id ) {
			wp_trash_post( $email_id );
		}
	}

	// Make sure we have no orphaned email type terms.
	$email_types = get_terms( sz_get_email_tax_type(), array(
		'fields'                 => 'ids',
		'hide_empty'             => false,
		'update_term_meta_cache' => false,
	) );

	if ( $email_types ) {
		foreach ( $email_types as $term_id ) {
			wp_delete_term( (int) $term_id, sz_get_email_tax_type() );
		}
	}

	require_once( sportszone()->plugin_dir . '/sz-core/admin/sz-core-admin-schema.php' );
	sz_core_install_emails();

	if ( $switched ) {
		restore_current_blog();
	}

	return array( 0, __( 'Emails have been successfully reinstalled.', 'sportszone' ) );
}

/**
 * Add notice on the "Tools > SportsZone" page if more sites need recording.
 *
 * This notice only shows up in the network admin dashboard.
 *
 * @since 2.6.0
 */
function sz_core_admin_notice_repopulate_blogs_resume() {
	$screen = get_current_screen();
	if ( 'tools_page_sz-tools-network' !== $screen->id ) {
		return;
	}

	if ( '' === sz_get_option( '_sz_record_blogs_offset' ) ) {
		return;
	}

	echo '<div class="error"><p>' . __( 'It looks like you have more sites to record. Resume recording by checking the "Repopulate site tracking records" option.', 'sportszone' ) . '</p></div>';
}
add_action( 'network_admin_notices', 'sz_core_admin_notice_repopulate_blogs_resume' );
