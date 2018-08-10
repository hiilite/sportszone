<?php
/**
 * Deprecated functions
 *
 * @package SportsZone
 * @subpackage Core
 * @deprecated 2.1.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Register (not enqueue) scripts that used to be used by SportsZone.
 *
 * @since 2.1.0
 */
function sz_core_register_deprecated_scripts() {
	// Scripts undeprecated as of 2.5.0.
}

/**
 * Register (not enqueue) styles that used to be used by SportsZone.
 *
 * @since 2.1.0
 */
function sz_core_register_deprecated_styles() {
	// Scripts undeprecated as of 2.5.0.
}

/** BuddyBar *****************************************************************/

/**
 * Add a Sites menu to the BuddyBar.
 *
 * @since 1.0.0
 * @deprecated 2.1.0
 *
 * @return false|null Returns false on failure. Otherwise echoes the menu item.
 */
function sz_adminbar_blogs_menu() {

	if ( ! is_user_logged_in() || ! sz_is_active( 'blogs' ) ) {
		return false;
	}

	if ( ! is_multisite() ) {
		return false;
	}

	$blogs = wp_cache_get( 'sz_blogs_of_user_' . sz_loggedin_user_id() . '_inc_hidden', 'sz' );
	if ( empty( $blogs ) ) {
		$blogs = sz_blogs_get_blogs_for_user( sz_loggedin_user_id(), true );
		wp_cache_set( 'sz_blogs_of_user_' . sz_loggedin_user_id() . '_inc_hidden', $blogs, 'sz' );
	}

	$counter = 0;
	if ( is_array( $blogs['blogs'] ) && (int) $blogs['count'] ) {

		echo '<li id="sz-adminbar-blogs-menu"><a href="' . trailingslashit( sz_loggedin_user_domain() . sz_get_blogs_slug() ) . '">';

		_e( 'My Sites', 'sportszone' );

		echo '</a>';
		echo '<ul>';

		foreach ( (array) $blogs['blogs'] as $blog ) {
			$alt      = ( 0 == $counter % 2 ) ? ' class="alt"' : '';
			$site_url = esc_attr( $blog->siteurl );

			echo '<li' . $alt . '>';
			echo '<a href="' . $site_url . '">' . esc_html( $blog->name ) . '</a>';
			echo '<ul>';
			echo '<li class="alt"><a href="' . $site_url . 'wp-admin/">' . __( 'Dashboard', 'sportszone' ) . '</a></li>';
			echo '<li><a href="' . $site_url . 'wp-admin/post-new.php">' . __( 'New Post', 'sportszone' ) . '</a></li>';
			echo '<li class="alt"><a href="' . $site_url . 'wp-admin/edit.php">' . __( 'Manage Posts', 'sportszone' ) . '</a></li>';
			echo '<li><a href="' . $site_url . 'wp-admin/edit-comments.php">' . __( 'Manage Comments', 'sportszone' ) . '</a></li>';
			echo '</ul>';

			do_action( 'sz_adminbar_blog_items', $blog );

			echo '</li>';
			$counter++;
		}

		$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : '';

		if ( sz_blog_signup_enabled() ) {
			echo '<li' . $alt . '>';
			echo '<a href="' . trailingslashit( sz_get_blogs_directory_permalink() . 'create' ) . '">' . __( 'Create a Site!', 'sportszone' ) . '</a>';
			echo '</li>';
		}

		echo '</ul>';
		echo '</li>';
	}
}

/**
 * If user has upgraded to 1.6 and chose to retain their BuddyBar, offer then a switch to change over
 * to the WP Toolbar.
 *
 * @since 1.6.0
 * @deprecated 2.1.0
 */
function sz_admin_setting_callback_force_buddybar() {
?>

	<input id="_sz_force_buddybar" name="_sz_force_buddybar" type="checkbox" value="1" <?php checked( ! sz_force_buddybar( true ) ); ?> />
	<label for="_sz_force_buddybar"><?php _e( 'Switch to WordPress Toolbar', 'sportszone' ); ?></label>

<?php
}


/**
 * Sanitization for _sz_force_buddybar
 *
 * If upgraded to 1.6 and you chose to keep the BuddyBar, a checkbox asks if you want to switch to
 * the WP Toolbar. The option we store is 1 if the BuddyBar is forced on, so we use this function
 * to flip the boolean before saving the intval.
 *
 * @since 1.6.0
 * @deprecated 2.1.0
 * @access Private
 */
function sz_admin_sanitize_callback_force_buddybar( $value = false ) {
	return $value ? 0 : 1;
}

/**
 * Wrapper function for rendering the BuddyBar.
 *
 * @return false|null Returns false if the BuddyBar is disabled.
 * @deprecated 2.1.0
 */
function sz_core_admin_bar() {
	$sz = sportszone();

	if ( defined( 'SZ_DISABLE_ADMIN_BAR' ) && SZ_DISABLE_ADMIN_BAR ) {
		return false;
	}

	if ( (int) sz_get_option( 'hide-loggedout-adminbar' ) && !is_user_logged_in() ) {
		return false;
	}

	$sz->doing_admin_bar = true;

	echo '<div id="wp-admin-bar"><div class="padder">';

	// **** Do sz-adminbar-logo Actions ********
	do_action( 'sz_adminbar_logo' );

	echo '<ul class="main-nav">';

	// **** Do sz-adminbar-menus Actions ********
	do_action( 'sz_adminbar_menus' );

	echo '</ul>';
	echo "</div></div><!-- #wp-admin-bar -->\n\n";

	$sz->doing_admin_bar = false;
}

/**
 * Output the BuddyBar logo.
 *
 * @deprecated 2.1.0
 */
function sz_adminbar_logo() {
	echo '<a href="' . sz_get_root_domain() . '" id="admin-bar-logo">' . get_blog_option( sz_get_root_blog_id(), 'blogname' ) . '</a>';
}

/**
 * Output the "Log In" and "Sign Up" names to the BuddyBar.
 *
 * Visible only to visitors who are not logged in.
 *
 * @deprecated 2.1.0
 *
 * @return false|null Returns false if the current user is logged in.
 */
function sz_adminbar_login_menu() {

	if ( is_user_logged_in() ) {
		return false;
	}

	echo '<li class="sz-login no-arrow"><a href="' . wp_login_url() . '">' . __( 'Log In', 'sportszone' ) . '</a></li>';

	// Show "Sign Up" link if user registrations are allowed
	if ( sz_get_signup_allowed() ) {
		echo '<li class="sz-signup no-arrow"><a href="' . sz_get_signup_page() . '">' . __( 'Sign Up', 'sportszone' ) . '</a></li>';
	}
}

/**
 * Output the My Account BuddyBar menu.
 *
 * @deprecated 2.1.0
 *
 * @return false|null Returns false on failure.
 */
function sz_adminbar_account_menu() {
	$sz = sportszone();

	if ( empty( $sz->sz_nav ) || ! is_user_logged_in() ) {
		return false;
	}

	echo '<li id="sz-adminbar-account-menu"><a href="' . sz_loggedin_user_domain() . '">';
	echo __( 'My Account', 'sportszone' ) . '</a>';
	echo '<ul>';

	// Loop through each navigation item
	$counter = 0;
	foreach( (array) $sz->sz_nav as $nav_item ) {
		$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : '';

		if ( -1 == $nav_item['position'] ) {
			continue;
		}

		echo '<li' . $alt . '>';
		echo '<a id="sz-admin-' . $nav_item['css_id'] . '" href="' . $nav_item['link'] . '">' . $nav_item['name'] . '</a>';

		if ( isset( $sz->sz_options_nav[$nav_item['slug']] ) && is_array( $sz->sz_options_nav[$nav_item['slug']] ) ) {
			echo '<ul>';
			$sub_counter = 0;

			foreach( (array) $sz->sz_options_nav[$nav_item['slug']] as $subnav_item ) {
				$link = $subnav_item['link'];
				$name = $subnav_item['name'];

				if ( sz_displayed_user_domain() ) {
					$link = str_replace( sz_displayed_user_domain(), sz_loggedin_user_domain(), $subnav_item['link'] );
				}

				if ( isset( $sz->displayed_user->userdata->user_login ) ) {
					$name = str_replace( $sz->displayed_user->userdata->user_login, $sz->loggedin_user->userdata->user_login, $subnav_item['name'] );
				}

				$alt = ( 0 == $sub_counter % 2 ) ? ' class="alt"' : '';
				echo '<li' . $alt . '><a id="sz-admin-' . $subnav_item['css_id'] . '" href="' . $link . '">' . $name . '</a></li>';
				$sub_counter++;
			}
			echo '</ul>';
		}

		echo '</li>';

		$counter++;
	}

	$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : '';

	echo '<li' . $alt . '><a id="sz-admin-logout" class="logout" href="' . wp_logout_url( home_url() ) . '">' . __( 'Log Out', 'sportszone' ) . '</a></li>';
	echo '</ul>';
	echo '</li>';
}

function sz_adminbar_thisblog_menu() {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<li id="sz-adminbar-thisblog-menu"><a href="' . admin_url() . '">';
		_e( 'Dashboard', 'sportszone' );
		echo '</a>';
		echo '<ul>';

		echo '<li class="alt"><a href="' . admin_url() . 'post-new.php">' . __( 'New Post', 'sportszone' ) . '</a></li>';
		echo '<li><a href="' . admin_url() . 'edit.php">' . __( 'Manage Posts', 'sportszone' ) . '</a></li>';
		echo '<li class="alt"><a href="' . admin_url() . 'edit-comments.php">' . __( 'Manage Comments', 'sportszone' ) . '</a></li>';

		do_action( 'sz_adminbar_thisblog_items' );

		echo '</ul>';
		echo '</li>';
	}
}

/**
 * Output the Random BuddyBar menu.
 *
 * Not visible for logged-in users.
 *
 * @deprecated 2.1.0
 */
function sz_adminbar_random_menu() {
?>

	<li class="align-right" id="sz-adminbar-visitrandom-menu">
		<a href="#"><?php _e( 'Visit', 'sportszone' ) ?></a>
		<ul class="random-list">
			<li><a href="<?php sz_members_directory_permalink(); ?>?random-member" rel="nofollow"><?php _e( 'Random Member', 'sportszone' ) ?></a></li>

			<?php if ( sz_is_active( 'groups' ) ) : ?>

				<li class="alt"><a href="<?php sz_groups_directory_permalink(); ?>?random-group"  rel="nofollow"><?php _e( 'Random Group', 'sportszone' ) ?></a></li>

			<?php endif; ?>

			<?php if ( is_multisite() && sz_is_active( 'blogs' ) ) : ?>

				<li><a href="<?php sz_blogs_directory_permalink(); ?>?random-blog"  rel="nofollow"><?php _e( 'Random Site', 'sportszone' ) ?></a></li>

			<?php endif; ?>

			<?php do_action( 'sz_adminbar_random_menu' ) ?>

		</ul>
	</li>

	<?php
}

/**
 * Enqueue the BuddyBar CSS.
 *
 * @deprecated 2.1.0
 */
function sz_core_load_buddybar_css() {

	if ( sz_use_wp_admin_bar() || ( (int) sz_get_option( 'hide-loggedout-adminbar' ) && !is_user_logged_in() ) || ( defined( 'SZ_DISABLE_ADMIN_BAR' ) && SZ_DISABLE_ADMIN_BAR ) ) {
		return;
	}

	$min = sz_core_get_minified_asset_suffix();

	if ( file_exists( get_stylesheet_directory() . '/_inc/css/adminbar.css' ) ) { // Backwards compatibility
		$stylesheet = get_stylesheet_directory_uri() . '/_inc/css/adminbar.css';
	} else {
		$stylesheet = sportszone()->plugin_url . "sz-core/css/buddybar{$min}.css";
	}

	wp_enqueue_style( 'sz-admin-bar', apply_filters( 'sz_core_buddybar_rtl_css', $stylesheet ), array(), sz_get_version() );

	wp_style_add_data( 'sz-admin-bar', 'rtl', true );
	if ( $min ) {
		wp_style_add_data( 'sz-admin-bar', 'suffix', $min );
	}
}
add_action( 'sz_init', 'sz_core_load_buddybar_css' );

/**
 * Add menu items to the BuddyBar.
 *
 * @since 1.0.0
 *
 * @deprecated 2.1.0
 */
function sz_groups_adminbar_admin_menu() {
	$sz = sportszone();

	if ( empty( $sz->groups->current_group ) ) {
		return false;
	}

	// Only group admins and site admins can see this menu
	if ( !current_user_can( 'edit_users' ) && !sz_current_user_can( 'sz_moderate' ) && !sz_is_item_admin() ) {
		return false;
	} ?>

	<li id="sz-adminbar-adminoptions-menu">
		<a href="<?php sz_groups_action_link( 'admin' ); ?>"><?php _e( 'Admin Options', 'sportszone' ); ?></a>

		<ul>
			<li><a href="<?php sz_groups_action_link( 'admin/edit-details' ); ?>"><?php _e( 'Edit Details', 'sportszone' ); ?></a></li>

			<li><a href="<?php sz_groups_action_link( 'admin/group-settings' );  ?>"><?php _e( 'Group Settings', 'sportszone' ); ?></a></li>

			<?php if ( !(int)sz_get_option( 'sz-disable-avatar-uploads' ) && $sz->avatar->show_avatars ) : ?>

				<li><a href="<?php sz_groups_action_link( 'admin/group-avatar' ); ?>"><?php _e( 'Group Profile Photo', 'sportszone' ); ?></a></li>

			<?php endif; ?>

			<?php if ( sz_is_active( 'friends' ) ) : ?>

				<li><a href="<?php sz_groups_action_link( 'send-invites' ); ?>"><?php _e( 'Manage Invitations', 'sportszone' ); ?></a></li>

			<?php endif; ?>

			<li><a href="<?php sz_groups_action_link( 'admin/manage-members' ); ?>"><?php _e( 'Manage Members', 'sportszone' ); ?></a></li>

			<?php if ( $sz->groups->current_group->status == 'private' ) : ?>

				<li><a href="<?php sz_groups_action_link( 'admin/membership-requests' ); ?>"><?php _e( 'Membership Requests', 'sportszone' ); ?></a></li>

			<?php endif; ?>

			<li><a class="confirm" href="<?php echo wp_nonce_url( sz_get_group_permalink( $sz->groups->current_group ) . 'admin/delete-group/', 'groups_delete_group' ); ?>&amp;delete-group-button=1&amp;delete-group-understand=1"><?php _e( "Delete Group", 'sportszone' ) ?></a></li>

			<?php do_action( 'sz_groups_adminbar_admin_menu' ) ?>

		</ul>
	</li>

	<?php
}
add_action( 'sz_adminbar_menus', 'sz_groups_adminbar_admin_menu', 20 );

/**
 * Add the Notifications menu to the BuddyBar.
 *
 * @deprecated 2.1.0
 */
function sz_adminbar_notifications_menu() {

	// Bail if notifications is not active
	if ( ! sz_is_active( 'notifications' ) ) {
		return false;
	}

	sz_notifications_buddybar_menu();
}
add_action( 'sz_adminbar_menus', 'sz_adminbar_notifications_menu', 8 );

/**
 * Add the Blog Authors menu to the BuddyBar (visible when not logged in).
 *
 * @deprecated 2.1.0
 */
function sz_adminbar_authors_menu() {
	global $wpdb;

	// Only for multisite
	if ( ! is_multisite() ) {
		return false;
	}

	// Hide on root blog
	if ( sz_is_root_blog( $wpdb->blogid ) || ! sz_is_active( 'blogs' ) ) {
		return false;
	}

	$blog_prefix = $wpdb->get_blog_prefix( $wpdb->blogid );
	$authors     = $wpdb->get_results( "SELECT user_id, user_login, user_nicename, display_name, user_email, meta_value as caps FROM $wpdb->users u, $wpdb->usermeta um WHERE u.ID = um.user_id AND meta_key = '{$blog_prefix}capabilities' ORDER BY um.user_id" );

	if ( !empty( $authors ) ) {
		// This is a blog, render a menu with links to all authors
		echo '<li id="sz-adminbar-authors-menu"><a href="/">';
		_e('Blog Authors', 'sportszone');
		echo '</a>';

		echo '<ul class="author-list">';
		foreach( (array) $authors as $author ) {
			$caps = maybe_unserialize( $author->caps );
			if ( isset( $caps['subscriber'] ) || isset( $caps['contributor'] ) ) {
				continue;
			}

			echo '<li>';
			echo '<a href="' . sz_core_get_user_domain( $author->user_id, $author->user_nicename, $author->user_login ) . '">';
			echo sz_core_fetch_avatar( array(
				'item_id' => $author->user_id,
				'email'   => $author->user_email,
				'width'   => 15,
				'height'  => 15,
				'alt'     => sprintf( __( 'Profile picture of %s', 'sportszone' ), $author->display_name )
			) );
			echo ' ' . $author->display_name . '</a>';
			echo '<div class="admin-bar-clear"></div>';
			echo '</li>';
		}
		echo '</ul>';
		echo '</li>';
	}
}
add_action( 'sz_adminbar_menus', 'sz_adminbar_authors_menu', 12 );

/**
 * Add a member admin menu to the BuddyBar.
 *
 * Adds an Toolbar menu to any profile page providing site moderator actions
 * that allow capable users to clean up a users account.
 *
 * @deprecated 2.1.0
 */
function sz_members_adminbar_admin_menu() {

	// Only show if viewing a user
	if ( ! sz_displayed_user_id() ) {
		return false;
	}

	// Don't show this menu to non site admins or if you're viewing your own profile
	if ( !current_user_can( 'edit_users' ) || sz_is_my_profile() ) {
		return false;
	} ?>

	<li id="sz-adminbar-adminoptions-menu">

		<a href=""><?php _e( 'Admin Options', 'sportszone' ) ?></a>

		<ul>
			<?php if ( sz_is_active( 'xprofile' ) ) : ?>

				<li><a href="<?php sz_members_component_link( 'profile', 'edit' ); ?>"><?php printf( __( "Edit %s's Profile", 'sportszone' ), esc_attr( sz_get_displayed_user_fullname() ) ) ?></a></li>

			<?php endif ?>

			<li><a href="<?php sz_members_component_link( 'profile', 'change-avatar' ); ?>"><?php printf( __( "Edit %s's Profile Photo", 'sportszone' ), esc_attr( sz_get_displayed_user_fullname() ) ) ?></a></li>

			<li><a href="<?php sz_members_component_link( 'settings', 'capabilities' ); ?>"><?php _e( 'User Capabilities', 'sportszone' ); ?></a></li>

			<li><a href="<?php sz_members_component_link( 'settings', 'delete-account' ); ?>"><?php printf( __( "Delete %s's Account", 'sportszone' ), esc_attr( sz_get_displayed_user_fullname() ) ); ?></a></li>

			<?php do_action( 'sz_members_adminbar_admin_menu' ) ?>

		</ul>
	</li>

	<?php
}
add_action( 'sz_adminbar_menus', 'sz_members_adminbar_admin_menu', 20 );

/**
 * Create the Notifications menu for the BuddyBar.
 *
 * @since 1.9.0
 * @deprecated 2.1.0
 */
function sz_notifications_buddybar_menu() {

	if ( ! is_user_logged_in() ) {
		return false;
	}

	echo '<li id="sz-adminbar-notifications-menu"><a href="' . esc_url( sz_loggedin_user_domain() ) . '">';
	_e( 'Notifications', 'sportszone' );

	$notification_count = sz_notifications_get_unread_notification_count( sz_loggedin_user_id() );
	$notifications      = sz_notifications_get_notifications_for_user( sz_loggedin_user_id() );

	if ( ! empty( $notification_count ) ) : ?>
		<span><?php echo sz_core_number_format( $notification_count ); ?></span>
	<?php
	endif;

	echo '</a>';
	echo '<ul>';

	if ( ! empty( $notifications ) ) {
		$counter = 0;
		for ( $i = 0, $count = count( $notifications ); $i < $count; ++$i ) {
			$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : ''; ?>

			<li<?php echo $alt ?>><?php echo $notifications[$i] ?></li>

			<?php $counter++;
		}
	} else { ?>

		<li><a href="<?php echo esc_url( sz_loggedin_user_domain() ); ?>"><?php _e( 'No new notifications.', 'sportszone' ); ?></a></li>

	<?php
	}

	echo '</ul>';
	echo '</li>';
}
add_action( 'sz_adminbar_menus', 'sz_adminbar_notifications_menu', 8 );

/**
 * Output the base URL for subdomain installations of WordPress Multisite.
 *
 * @since 1.6.0
 *
 * @deprecated 2.1.0
 */
function sz_blogs_subdomain_base() {
	_deprecated_function( __FUNCTION__, '2.1', 'sz_signup_subdomain_base()' );
	echo sz_signup_get_subdomain_base();
}

/**
 * Return the base URL for subdomain installations of WordPress Multisite.
 *
 * @since 1.6.0
 *
 * @return string The base URL - eg, 'example.com' for site_url() example.com or www.example.com.
 *
 * @deprecated 2.1.0
 */
function sz_blogs_get_subdomain_base() {
	_deprecated_function( __FUNCTION__, '2.1', 'sz_signup_get_subdomain_base()' );
	return sz_signup_get_subdomain_base();
}

/**
 * Allegedly output an avatar upload form, but it hasn't done that since 2009.
 *
 * @since 1.0.0
 * @deprecated 2.1.0
 */
function sz_avatar_upload_form() {
	_deprecated_function(__FUNCTION__, '2.1', 'No longer used' );
}

