<?php
/**
 * SportsZone Filters.
 *
 * This file contains the filters that are used throughout SportsZone. They are
 * consolidated here to make searching for them easier, and to help developers
 * understand at a glance the order in which things occur.
 *
 * There are a few common places that additional filters can currently be found.
 *
 *  - SportsZone: In {@link SportsZone::setup_actions()} in sportszone.php
 *  - Component: In {@link SZ_Component::setup_actions()} in
 *                sz-core/sz-core-component.php
 *  - Admin: More in {@link SZ_Admin::setup_actions()} in
 *            sz-core/sz-core-admin.php
 *
 * @package SportsZone
 * @subpackage Core
 * @since 1.5.0
 *
 * @see sz-core-actions.php
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Attach SportsZone to WordPress.
 *
 * SportsZone uses its own internal actions to help aid in third-party plugin
 * development, and to limit the amount of potential future code changes when
 * updates to WordPress core occur.
 *
 * These actions exist to create the concept of 'plugin dependencies'. They
 * provide a safe way for plugins to execute code *only* when SportsZone is
 * installed and activated, without needing to do complicated guesswork.
 *
 * For more information on how this works, see the 'Plugin Dependency' section
 * near the bottom of this file.
 *
 *           v--WordPress Actions       v--SportsZone Sub-actions
 */
add_filter( 'request',                 'sz_request',             10    );
add_filter( 'template_include',        'sz_template_include',    10    );
add_filter( 'login_redirect',          'sz_login_redirect',      10, 3 );
add_filter( 'map_meta_cap',            'sz_map_meta_caps',       10, 4 );

// Add some filters to feedback messages.
add_filter( 'sz_core_render_message_content', 'wptexturize'       );
add_filter( 'sz_core_render_message_content', 'convert_smilies'   );
add_filter( 'sz_core_render_message_content', 'convert_chars'     );
add_filter( 'sz_core_render_message_content', 'wpautop'           );
add_filter( 'sz_core_render_message_content', 'shortcode_unautop' );
add_filter( 'sz_core_render_message_content', 'wp_kses_data', 5   );

// Emails.
add_filter( 'sz_email_set_content_html', 'wp_filter_post_kses', 6 );
add_filter( 'sz_email_set_content_html', 'stripslashes', 8 );
add_filter( 'sz_email_set_content_plaintext', 'wp_strip_all_tags', 6 );
add_filter( 'sz_email_set_subject', 'sanitize_text_field', 6 );

/**
 * Template Compatibility.
 *
 * If you want to completely bypass this and manage your own custom SportsZone
 * template hierarchy, start here by removing this filter, then look at how
 * sz_template_include() works and do something similar. :)
 */
add_filter( 'sz_template_include',   'sz_template_include_theme_supports', 2, 1 );
add_filter( 'sz_template_include',   'sz_template_include_theme_compat',   4, 2 );

// Filter SportsZone template locations.
add_filter( 'sz_get_template_stack', 'sz_add_template_stack_locations' );

// Turn comments off for SportsZone pages.
add_filter( 'comments_open', 'sz_comments_open', 10, 2 );

// Prevent DB query for WP's main loop.
add_filter( 'posts_pre_query', 'sz_core_filter_wp_query', 10, 2 );

/**
 * Prevent specific pages (eg 'Activate') from showing on page listings.
 *
 * @since 1.5.0
 *
 * @param array $pages List of excluded page IDs, as passed to the
 *                     'wp_list_pages_excludes' filter.
 * @return array The exclude list, with BP's pages added.
 */
function sz_core_exclude_pages( $pages = array() ) {

	// Bail if not the root blog.
	if ( ! sz_is_root_blog() )
		return $pages;

	$sz = sportszone();

	if ( !empty( $sz->pages->activate ) )
		$pages[] = $sz->pages->activate->id;

	if ( !empty( $sz->pages->register ) )
		$pages[] = $sz->pages->register->id;

	/**
	 * Filters specific pages that shouldn't show up on page listings.
	 *
	 * @since 1.5.0
	 *
	 * @param array $pages Array of pages to exclude.
	 */
	return apply_filters( 'sz_core_exclude_pages', $pages );
}
add_filter( 'wp_list_pages_excludes', 'sz_core_exclude_pages' );

/**
 * Prevent specific pages (eg 'Activate') from showing in the Pages meta box of the Menu Administration screen.
 *
 * @since 2.0.0
 *
 * @param object|null $object The post type object used in the meta box.
 * @return object|null The $object, with a query argument to remove register and activate pages id.
 */
function sz_core_exclude_pages_from_nav_menu_admin( $object = null ) {

	// Bail if not the root blog.
	if ( ! sz_is_root_blog() ) {
		return $object;
	}

	if ( 'page' != $object->name ) {
		return $object;
	}

	$sz = sportszone();
	$pages = array();

	if ( ! empty( $sz->pages->activate ) ) {
		$pages[] = $sz->pages->activate->id;
	}

	if ( ! empty( $sz->pages->register ) ) {
		$pages[] = $sz->pages->register->id;
	}

	if ( ! empty( $pages ) ) {
		$object->_default_query['post__not_in'] = $pages;
	}

	return $object;
}
add_filter( 'nav_menu_meta_box_object', 'sz_core_exclude_pages_from_nav_menu_admin', 11, 1 );

/**
 * Adds current page CSS classes to the parent BP page in a WP Page Menu.
 *
 * Because SportsZone primarily uses virtual pages, we need a way to highlight
 * the BP parent page during WP menu generation.  This function checks the
 * current BP component against the current page in the WP menu to see if we
 * should highlight the WP page.
 *
 * @since 2.2.0
 *
 * @param array   $retval CSS classes for the current menu page in the menu.
 * @param WP_Post $page   The page properties for the current menu item.
 * @return array
 */
function sz_core_menu_highlight_parent_page( $retval, $page ) {
	if ( ! is_sportszone() ) {
		return $retval;
	}

	$page_id = false;

	// Loop against all BP component pages.
	foreach ( (array) sportszone()->pages as $component => $sz_page ) {
		// Handles the majority of components.
		if ( sz_is_current_component( $component ) ) {
			$page_id = (int) $sz_page->id;
		}

		// Stop if not on a user page.
		if ( ! sz_is_user() && ! empty( $page_id ) ) {
			break;
		}

		// Members component requires an explicit check due to overlapping components.
		if ( sz_is_user() && 'members' === $component ) {
			$page_id = (int) $sz_page->id;
			break;
		}
	}

	// Duplicate some logic from Walker_Page::start_el() to highlight menu items.
	if ( ! empty( $page_id ) ) {
		$_sz_page = get_post( $page_id );
		if ( in_array( $page->ID, $_sz_page->ancestors, true ) ) {
			$retval[] = 'current_page_ancestor';
		}
		if ( $page->ID === $page_id ) {
			$retval[] = 'current_page_item';
		} elseif ( $_sz_page && $page->ID === $_sz_page->post_parent ) {
			$retval[] = 'current_page_parent';
		}
	}

	$retval = array_unique( $retval );

	return $retval;
}
add_filter( 'page_css_class', 'sz_core_menu_highlight_parent_page', 10, 2 );

/**
 * Adds current page CSS classes to the parent BP page in a WP Nav Menu.
 *
 * When {@link wp_nav_menu()} is used, this function helps to highlight the
 * current BP parent page during nav menu generation.
 *
 * @since 2.2.0
 *
 * @param array   $retval CSS classes for the current nav menu item in the menu.
 * @param WP_Post $item   The properties for the current nav menu item.
 * @return array
 */
function sz_core_menu_highlight_nav_menu_item( $retval, $item ) {
	// If we're not on a BP page or if the current nav item is not a page, stop!
	if ( ! is_sportszone() || 'page' !== $item->object ) {
		return $retval;
	}

	// Get the WP page.
	$page   = get_post( $item->object_id );

	// See if we should add our highlight CSS classes for the page.
	$retval = sz_core_menu_highlight_parent_page( $retval, $page );

	return $retval;
}
add_filter( 'nav_menu_css_class', 'sz_core_menu_highlight_nav_menu_item', 10, 2 );

/**
 * Filter the blog post comments array and insert SportsZone URLs for users.
 *
 * @since 1.2.0
 *
 * @param array $comments The array of comments supplied to the comments template.
 * @param int   $post_id  The post ID.
 * @return array $comments The modified comment array.
 */
function sz_core_filter_comments( $comments, $post_id ) {
	global $wpdb;

	foreach( (array) $comments as $comment ) {
		if ( $comment->user_id )
			$user_ids[] = $comment->user_id;
	}

	if ( empty( $user_ids ) )
		return $comments;

	$user_ids = implode( ',', wp_parse_id_list( $user_ids ) );

	if ( !$userdata = $wpdb->get_results( "SELECT ID as user_id, user_login, user_nicename FROM {$wpdb->users} WHERE ID IN ({$user_ids})" ) )
		return $comments;

	foreach( (array) $userdata as $user )
		$users[$user->user_id] = sz_core_get_user_domain( $user->user_id, $user->user_nicename, $user->user_login );

	foreach( (array) $comments as $i => $comment ) {
		if ( !empty( $comment->user_id ) ) {
			if ( !empty( $users[$comment->user_id] ) )
				$comments[$i]->comment_author_url = $users[$comment->user_id];
		}
	}

	return $comments;
}
add_filter( 'comments_array', 'sz_core_filter_comments', 10, 2 );

/**
 * When a user logs in, redirect him in a logical way.
 *
 * @since 1.2.0
 *
 *       are redirected to on login.
 *
 * @param string  $redirect_to     The URL to be redirected to, sanitized in wp-login.php.
 * @param string  $redirect_to_raw The unsanitized redirect_to URL ($_REQUEST['redirect_to']).
 * @param WP_User $user            The WP_User object corresponding to a successfully
 *                                 logged-in user. Otherwise a WP_Error object.
 * @return string The redirect URL.
 */
function sz_core_login_redirect( $redirect_to, $redirect_to_raw, $user ) {

	// Only modify the redirect if we're on the main BP blog.
	if ( !sz_is_root_blog() ) {
		return $redirect_to;
	}

	// Only modify the redirect once the user is logged in.
	if ( !is_a( $user, 'WP_User' ) ) {
		return $redirect_to;
	}

	/**
	 * Filters whether or not to redirect.
	 *
	 * Allows plugins to have finer grained control of redirect upon login.
	 *
	 * @since 1.6.0
	 *
	 * @param bool    $value           Whether or not to redirect.
	 * @param string  $redirect_to     Sanitized URL to be redirected to.
	 * @param string  $redirect_to_raw Unsanitized URL to be redirected to.
	 * @param WP_User $user            The WP_User object corresponding to a
	 *                                 successfully logged in user.
	 */
	$maybe_redirect = apply_filters( 'sz_core_login_redirect', false, $redirect_to, $redirect_to_raw, $user );
	if ( false !== $maybe_redirect ) {
		return $maybe_redirect;
	}

	// If a 'redirect_to' parameter has been passed that contains 'wp-admin', verify that the
	// logged-in user has any business to conduct in the Dashboard before allowing the
	// redirect to go through.
	if ( !empty( $redirect_to ) && ( false === strpos( $redirect_to, 'wp-admin' ) || user_can( $user, 'edit_posts' ) ) ) {
		return $redirect_to;
	}

	if ( false === strpos( wp_get_referer(), 'wp-login.php' ) && false === strpos( wp_get_referer(), 'activate' ) && empty( $_REQUEST['nr'] ) ) {
		return wp_get_referer();
	}

	/**
	 * Filters the URL to redirect users to upon successful login.
	 *
	 * @since 1.9.0
	 *
	 * @param string $value URL to redirect to.
	 */
	return apply_filters( 'sz_core_login_redirect_to', sz_get_root_domain() );
}
add_filter( 'sz_login_redirect', 'sz_core_login_redirect', 10, 3 );

/**
 * Decode HTML entities for plain-text emails.
 *
 * @since 2.5.0
 *
 * @param string $retval    Current email content.
 * @param string $prop      Email property to check against.
 * @param string $transform Either 'raw' or 'replace-tokens'.
 * @return string|null $retval Modified email content.
 */
function sz_email_plaintext_entity_decode( $retval, $prop, $transform ) {
	switch ( $prop ) {
		case 'content_plaintext' :
		case 'subject' :
			// Only decode if 'replace-tokens' is the current type.
			if ( 'replace-tokens' === $transform ) {
				return html_entity_decode( $retval, ENT_QUOTES );
			} else {
				return $retval;
			}
			break;

		default :
			return $retval;
			break;
	}
}
add_filter( 'sz_email_get_property', 'sz_email_plaintext_entity_decode', 10, 3 );

/**
 * Replace the generated password in the welcome email with '[User Set]'.
 *
 * On a standard BP installation, users who register themselves also set their
 * own passwords. Therefore there is no need for the insecure practice of
 * emailing the plaintext password to the user in the welcome email.
 *
 * This filter will not fire when a user is registered by the site admin.
 *
 * @since 1.2.1
 *
 * @param string $welcome_email Complete email passed through WordPress.
 * @return string Filtered $welcome_email with the password replaced
 *                by '[User Set]'.
 */
function sz_core_filter_user_welcome_email( $welcome_email ) {

	// Don't touch the email when a user is registered by the site admin.
	if ( ( is_admin() || is_network_admin() ) && sportszone()->members->admin->signups_page != get_current_screen()->id ) {
		return $welcome_email;
	}

	if ( strpos( sz_get_requested_url(), 'wp-activate.php' ) !== false ) {
		return $welcome_email;
	}

	// Don't touch the email if we don't have a custom registration template.
	if ( ! sz_has_custom_signup_page() ) {
		return $welcome_email;
	}

	// [User Set] Replaces 'PASSWORD' in welcome email; Represents value set by user
	return str_replace( 'PASSWORD', __( '[User Set]', 'sportszone' ), $welcome_email );
}
add_filter( 'update_welcome_user_email', 'sz_core_filter_user_welcome_email' );

/**
 * Replace the generated password in the welcome email with '[User Set]'.
 *
 * On a standard BP installation, users who register themselves also set their
 * own passwords. Therefore there is no need for the insecure practice of
 * emailing the plaintext password to the user in the welcome email.
 *
 * This filter will not fire when a user is registered by the site admin.
 *
 * @since 1.2.1
 *
 * @param string $welcome_email Complete email passed through WordPress.
 * @param int    $blog_id       ID of the blog user is joining.
 * @param int    $user_id       ID of the user joining.
 * @param string $password      Password of user.
 * @return string Filtered $welcome_email with $password replaced by '[User Set]'.
 */
function sz_core_filter_blog_welcome_email( $welcome_email, $blog_id, $user_id, $password ) {

	// Don't touch the email when a user is registered by the site admin.
	if ( ( is_admin() || is_network_admin() ) && sportszone()->members->admin->signups_page != get_current_screen()->id ) {
		return $welcome_email;
	}

	// Don't touch the email if we don't have a custom registration template.
	if ( ! sz_has_custom_signup_page() )
		return $welcome_email;

	// [User Set] Replaces $password in welcome email; Represents value set by user
	return str_replace( $password, __( '[User Set]', 'sportszone' ), $welcome_email );
}
add_filter( 'update_welcome_email', 'sz_core_filter_blog_welcome_email', 10, 4 );

/**
 * Notify new users of a successful registration (with blog).
 *
 * This function filter's WP's 'wpmu_signup_blog_notification', and replaces
 * WP's default welcome email with a SportsZone-specific message.
 *
 * @since 1.0.0
 *
 * @see wpmu_signup_blog_notification() for a description of parameters.
 *
 * @param string $domain     The new blog domain.
 * @param string $path       The new blog path.
 * @param string $title      The site title.
 * @param string $user       The user's login name.
 * @param string $user_email The user's email address.
 * @param string $key        The activation key created in wpmu_signup_blog().
 * @return bool              Returns false to stop original WPMU function from continuing.
 */
function sz_core_activation_signup_blog_notification( $domain, $path, $title, $user, $user_email, $key ) {
	$args = array(
		'tokens' => array(
			'activate-site.url' => esc_url( sz_get_activation_page() . '?key=' . urlencode( $key ) ),
			'domain'            => $domain,
			'key_blog'          => $key,
			'path'              => $path,
			'user-site.url'     => esc_url( set_url_scheme( "http://{$domain}{$path}" ) ),
			'title'             => $title,
			'user.email'        => $user_email,
		),
	);
	sz_send_email( 'core-user-registration-with-blog', array( array( $user_email => $user ) ), $args );

	// Return false to stop the original WPMU function from continuing.
	return false;
}
add_filter( 'wpmu_signup_blog_notification', 'sz_core_activation_signup_blog_notification', 1, 6 );

/**
 * Notify new users of a successful registration (without blog).
 *
 * @since 1.0.0
 *
 * @see wpmu_signup_user_notification() for a full description of params.
 *
 * @param string $user       The user's login name.
 * @param string $user_email The user's email address.
 * @param string $key        The activation key created in wpmu_signup_user().
 * @param array  $meta       By default, an empty array.
 * @return false|string Returns false to stop original WPMU function from continuing.
 */
function sz_core_activation_signup_user_notification( $user, $user_email, $key, $meta ) {
	if ( is_admin() ) {

		// If the user is created from the WordPress Add User screen, don't send SportsZone signup notifications.
		if( in_array( get_current_screen()->id, array( 'user', 'user-network' ) ) ) {
			// If the Super Admin want to skip confirmation email.
			if ( isset( $_POST[ 'noconfirmation' ] ) && is_super_admin() ) {
				return false;

			// WordPress will manage the signup process.
			} else {
				return $user;
			}

		/*
		 * There can be a case where the user was created without the skip confirmation
		 * And the super admin goes in pending accounts to resend it. In this case, as the
		 * meta['password'] is not set, the activation url must be WordPress one.
		 */
		} elseif ( sportszone()->members->admin->signups_page == get_current_screen()->id ) {
			$is_hashpass_in_meta = maybe_unserialize( $meta );

			if ( empty( $is_hashpass_in_meta['password'] ) ) {
				return $user;
			}
		}
	}

	$user_id = 0;
	$user_object = get_user_by( 'login', $user );
	if ( $user_object ) {
		$user_id = $user_object->ID;
	}

	$args = array(
		'tokens' => array(
			'activate.url' => esc_url( trailingslashit( sz_get_activation_page() ) . "{$key}/" ),
			'key'          => $key,
			'user.email'   => $user_email,
			'user.id'      => $user_id,
		),
	);
	sz_send_email( 'core-user-registration', array( array( $user_email => $user ) ), $args );

	// Return false to stop the original WPMU function from continuing.
	return false;
}
add_filter( 'wpmu_signup_user_notification', 'sz_core_activation_signup_user_notification', 1, 4 );

/**
 * Filter the page title for SportsZone pages.
 *
 * @since 1.5.0
 *
 * @see wp_title()
 * @global object $sz SportsZone global settings.
 *
 * @param string $title       Original page title.
 * @param string $sep         How to separate the various items within the page title.
 * @param string $seplocation Direction to display title.
 * @return string              New page title.
 */
function sz_modify_page_title( $title = '', $sep = '&raquo;', $seplocation = 'right' ) {
	global $paged, $page, $_wp_theme_features;

	// Get the SportsZone title parts.
	$sz_title_parts = sz_get_title_parts( $seplocation );

	// If not set, simply return the original title.
	if ( ! $sz_title_parts ) {
		return $title;
	}

	// Get the blog name, so we can check if the original $title included it.
	$blogname = get_bloginfo( 'name', 'display' );

	/**
	 * Are we going to fake 'title-tag' theme functionality?
	 *
	 * @link https://sportszone.trac.wordpress.org/ticket/6107
	 * @see wp_title()
	 */
	$title_tag_compatibility = (bool) ( ! empty( $_wp_theme_features['title-tag'] ) || ( $blogname && strstr( $title, $blogname ) ) );

	// Append the site title to title parts if theme supports title tag.
	if ( true === $title_tag_compatibility ) {
		$sz_title_parts['site'] = $blogname;

		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() && ! sz_is_single_activity() ) {
			$sz_title_parts['page'] = sprintf( __( 'Page %s', 'sportszone' ), max( $paged, $page ) );
		}
	}

	// Pad the separator with 1 space on each side.
	$prefix = str_pad( $sep, strlen( $sep ) + 2, ' ', STR_PAD_BOTH );

	// Join the parts together.
	$new_title = join( $prefix, array_filter( $sz_title_parts ) );

	// Append the prefix for pre `title-tag` compatibility.
	if ( false === $title_tag_compatibility ) {
		$new_title = $new_title . $prefix;
	}

	/**
	 * Filters the older 'wp_title' page title for SportsZone pages.
	 *
	 * @since 1.5.0
	 *
	 * @param string $new_title   The SportsZone page title.
	 * @param string $title       The original WordPress page title.
	 * @param string $sep         The title parts separator.
	 * @param string $seplocation Location of the separator (left or right).
	 */
	return apply_filters( 'sz_modify_page_title', $new_title, $title, $sep, $seplocation );
}
add_filter( 'wp_title',             'sz_modify_page_title', 20, 3 );
add_filter( 'sz_modify_page_title', 'wptexturize'                 );
add_filter( 'sz_modify_page_title', 'convert_chars'               );
add_filter( 'sz_modify_page_title', 'esc_html'                    );

/**
 * Filter the document title for SportsZone pages.
 *
 * @since 2.4.3
 *
 * @param array $title The WordPress document title parts.
 * @return array the unchanged title parts or the SportsZone ones
 */
function sz_modify_document_title_parts( $title = array() ) {
	// Get the SportsZone title parts.
	$sz_title_parts = sz_get_title_parts();

	// If not set, simply return the original title.
	if ( ! $sz_title_parts ) {
		return $title;
	}

	// Get the separator used by wp_get_document_title().
	$sep = apply_filters( 'document_title_separator', '-' );

	// Build the SportsZone portion of the title.
	// We don't need to sanitize this as WordPress will take care of it.
	$sz_title = array(
		'title' => join( " $sep ", $sz_title_parts )
	);

	// Add the pagination number if needed (not sure if this is necessary).
	if ( isset( $title['page'] ) && ! sz_is_single_activity() ) {
		$sz_title['page'] = $title['page'];
	}

	// Add the sitename if needed.
	if ( isset( $title['site'] ) ) {
		$sz_title['site'] = $title['site'];
	}

	/**
	 * Filters SportsZone title parts that will be used into the document title.
	 *
	 * @since 2.4.3
	 *
	 * @param array $sz_title The SportsZone page title parts.
	 * @param array $title    The original WordPress title parts.
	 */
	return apply_filters( 'sz_modify_document_title_parts', $sz_title, $title );
}
add_filter( 'document_title_parts', 'sz_modify_document_title_parts', 20, 1 );

/**
 * Add SportsZone-specific items to the wp_nav_menu.
 *
 * @since 1.9.0
 *
 * @param WP_Post $menu_item The menu item.
 * @return WP_Post The modified WP_Post object.
 */
function sz_setup_nav_menu_item( $menu_item ) {
	if ( is_admin() ) {
		return $menu_item;
	}

	// Prevent a notice error when using the customizer.
	$menu_classes = $menu_item->classes;

	if ( is_array( $menu_classes ) ) {
		$menu_classes = implode( ' ', $menu_item->classes);
	}

	// We use information stored in the CSS class to determine what kind of
	// menu item this is, and how it should be treated.
	preg_match( '/\ssz-(.*)-nav/', $menu_classes, $matches );

	// If this isn't a BP menu item, we can stop here.
	if ( empty( $matches[1] ) ) {
		return $menu_item;
	}

	switch ( $matches[1] ) {
		case 'login' :
			if ( is_user_logged_in() ) {
				$menu_item->_invalid = true;
			} else {
				$menu_item->url = wp_login_url( sz_get_requested_url() );
			}

			break;

		case 'logout' :
			if ( ! is_user_logged_in() ) {
				$menu_item->_invalid = true;
			} else {
				$menu_item->url = wp_logout_url( sz_get_requested_url() );
			}

			break;

		// Don't show the Register link to logged-in users.
		case 'register' :
			if ( is_user_logged_in() ) {
				$menu_item->_invalid = true;
			}

			break;

		// All other BP nav items are specific to the logged-in user,
		// and so are not relevant to logged-out users.
		default:
			if ( is_user_logged_in() ) {
				$menu_item->url = sz_nav_menu_get_item_url( $matches[1] );
			} else {
				$menu_item->_invalid = true;
			}

			break;
	}

	// If component is deactivated, make sure menu item doesn't render.
	if ( empty( $menu_item->url ) ) {
		$menu_item->_invalid = true;

	// Highlight the current page.
	} else {
		$current = sz_get_requested_url();
		if ( strpos( $current, $menu_item->url ) !== false ) {
			if ( is_array( $menu_item->classes ) ) {
				$menu_item->classes[] = 'current_page_item';
				$menu_item->classes[] = 'current-menu-item';
			} else {
				$menu_item->classes = array( 'current_page_item', 'current-menu-item' );
			}
		}
	}

	return $menu_item;
}
add_filter( 'wp_setup_nav_menu_item', 'sz_setup_nav_menu_item', 10, 1 );

/**
 * Populate SportsZone user nav items for the customizer.
 *
 * @since 2.3.3
 *
 * @param array   $items  The array of menu items.
 * @param string  $type   The requested type.
 * @param string  $object The requested object name.
 * @param integer $page   The page num being requested.
 * @return array The paginated SportsZone user nav items.
 */
function sz_customizer_nav_menus_get_items( $items = array(), $type = '', $object = '', $page = 0 ) {
	if ( 'sz_loggedin_nav' === $object ) {
		$sz_items = sz_nav_menu_get_loggedin_pages();
	} elseif ( 'sz_loggedout_nav' === $object ) {
		$sz_items = sz_nav_menu_get_loggedout_pages();
	} else {
		return $items;
	}

	foreach ( $sz_items as $sz_item ) {
		$items[] = array(
			'id'         => "sz-{$sz_item->post_excerpt}",
			'title'      => html_entity_decode( $sz_item->post_title, ENT_QUOTES, get_bloginfo( 'charset' ) ),
			'type'       => $type,
			'url'        => esc_url_raw( $sz_item->guid ),
			'classes'    => "sz-menu sz-{$sz_item->post_excerpt}-nav",
			'type_label' => _x( 'Custom Link', 'customizer menu type label', 'sportszone' ),
			'object'     => $object,
			'object_id'  => -1,
		);
	}

	return array_slice( $items, 10 * $page, 10 );
}
add_filter( 'customize_nav_menu_available_items', 'sz_customizer_nav_menus_get_items', 10, 4 );

/**
 * Set SportsZone item navs for the customizer.
 *
 * @since 2.3.3
 *
 * @param array $item_types An associative array structured for the customizer.
 * @return array $item_types An associative array structured for the customizer.
 */
function sz_customizer_nav_menus_set_item_types( $item_types = array() ) {
	$item_types = array_merge( $item_types, array(
		'sz_loggedin_nav' => array(
			'title'  => _x( 'SportsZone (logged-in)', 'customizer menu section title', 'sportszone' ),
			'type'   => 'sz_nav',
			'object' => 'sz_loggedin_nav',
		),
		'sz_loggedout_nav' => array(
			'title'  => _x( 'SportsZone (logged-out)', 'customizer menu section title', 'sportszone' ),
			'type'   => 'sz_nav',
			'object' => 'sz_loggedout_nav',
		),
	) );

	return $item_types;
}
add_filter( 'customize_nav_menu_available_item_types', 'sz_customizer_nav_menus_set_item_types', 10, 1 );

/**
 * Filter SQL query strings to swap out the 'meta_id' column.
 *
 * WordPress uses the meta_id column for commentmeta and postmeta, and so
 * hardcodes the column name into its *_metadata() functions. SportsZone, on
 * the other hand, uses 'id' for the primary column. To make WP's functions
 * usable for SportsZone, we use this just-in-time filter on 'query' to swap
 * 'meta_id' with 'id.
 *
 * @since 2.0.0
 *
 * @access private Do not use.
 *
 * @param string $q SQL query.
 * @return string
 */
function sz_filter_metaid_column_name( $q ) {
	/*
	 * Replace quoted content with __QUOTE__ to avoid false positives.
	 * This regular expression will match nested quotes.
	 */
	$quoted_regex = "/'[^'\\\\]*(?:\\\\.[^'\\\\]*)*'/s";
	preg_match_all( $quoted_regex, $q, $quoted_matches );
	$q = preg_replace( $quoted_regex, '__QUOTE__', $q );

	$q = str_replace( 'meta_id', 'id', $q );

	// Put quoted content back into the string.
	if ( ! empty( $quoted_matches[0] ) ) {
		for ( $i = 0; $i < count( $quoted_matches[0] ); $i++ ) {
			$quote_pos = strpos( $q, '__QUOTE__' );
			$q = substr_replace( $q, $quoted_matches[0][ $i ], $quote_pos, 9 );
		}
	}

	return $q;
}

/**
 * Filter the edit post link to avoid its display in SportsZone pages.
 *
 * @since 2.1.0
 *
 * @param string $edit_link The edit link.
 * @param int    $post_id   Post ID.
 * @return false|string Will be a boolean (false) if $post_id is 0. Will be a string (the unchanged edit link)
 *                      otherwise
 */
function sz_core_filter_edit_post_link( $edit_link = '', $post_id = 0 ) {
	if ( 0 === $post_id ) {
		$edit_link = false;
	}

	return $edit_link;
}

/**
 * Should SportsZone load the mentions scripts and related assets, including results to prime the
 * mentions suggestions?
 *
 * @since 2.2.0
 *
 * @param bool $load_mentions    True to load mentions assets, false otherwise.
 * @param bool $mentions_enabled True if mentions are enabled.
 * @return bool True if mentions scripts should be loaded.
 */
function sz_maybe_load_mentions_scripts_for_blog_content( $load_mentions, $mentions_enabled ) {
	if ( ! $mentions_enabled ) {
		return $load_mentions;
	}

	if ( $load_mentions || ( sz_is_blog_page() && is_singular() && comments_open() ) ) {
		return true;
	}

	return $load_mentions;
}
add_filter( 'sz_activity_maybe_load_mentions_scripts', 'sz_maybe_load_mentions_scripts_for_blog_content', 10, 2 );

/**
 * Injects specific SportsZone CSS classes into a widget sidebar.
 *
 * Helps to standardize styling of SportsZone widgets within a theme that
 * does not use dynamic CSS classes in their widget sidebar's 'before_widget'
 * call.
 *
 * @since 2.4.0
 * @access private
 *
 * @global array $wp_registered_widgets Current registered widgets.
 *
 * @param array $params Current sidebar params.
 * @return array
 */
function _sz_core_inject_sz_widget_css_class( $params ) {
	global $wp_registered_widgets;

	$widget_id = $params[0]['widget_id'];

	// If callback isn't an array, bail.
	if ( false === is_array( $wp_registered_widgets[ $widget_id ]['callback'] ) ) {
		return $params;
	}

	// If the current widget isn't a SportsZone one, stop!
	// We determine if a widget is a SportsZone widget, if the widget class
	// begins with 'sz_'.
	if ( 0 !== strpos( $wp_registered_widgets[ $widget_id ]['callback'][0]->id_base, 'sz_' ) ) {
		return $params;
	}

	// Dynamically add our widget CSS classes for BP widgets if not already there.
	$classes = array();

	// Try to find 'widget' CSS class.
	if ( false === strpos( $params[0]['before_widget'], 'widget ' ) ) {
		$classes[] = 'widget';
	}

	// Try to find 'sportszone' CSS class.
	if ( false === strpos( $params[0]['before_widget'], ' sportszone' ) ) {
		$classes[] = 'sportszone';
	}

	// Stop if widget already has our CSS classes.
	if ( empty( $classes ) ) {
		return $params;
	}

	// CSS injection time!
	$params[0]['before_widget'] = str_replace( 'class="', 'class="' . implode( ' ', $classes ) . ' ', $params[0]['before_widget'] );

	return $params;
}
add_filter( 'dynamic_sidebar_params', '_sz_core_inject_sz_widget_css_class' );

/**
 * Add email link styles to rendered email template.
 *
 * This is only used when the email content has been merged into the email template.
 *
 * @since 2.5.0
 *
 * @param string $value         Property value.
 * @param string $property_name Email template property name.
 * @param string $transform     How the return value was transformed.
 * @return string Updated value.
 */
function sz_email_add_link_color_to_template( $value, $property_name, $transform ) {
	if ( $property_name !== 'template' || $transform !== 'add-content' ) {
		return $value;
	}

	$settings    = sz_email_get_appearance_settings();
	$replacement = 'style="color: ' . esc_attr( $settings['link_text_color'] ) . ';';

	// Find all links.
	preg_match_all( '#<a[^>]+>#i', $value, $links, PREG_SET_ORDER );
	foreach ( $links as $link ) {
		$new_link = $link = array_shift( $link );

		// Add/modify style property.
		if ( strpos( $link, 'style="' ) !== false ) {
			$new_link = str_replace( 'style="', $replacement, $link );
		} else {
			$new_link = str_replace( '<a ', "<a {$replacement}\" ", $link );
		}

		if ( $new_link !== $link ) {
			$value = str_replace( $link, $new_link, $value );
		}
	}

	return $value;
}
add_filter( 'sz_email_get_property', 'sz_email_add_link_color_to_template', 6, 3 );

/**
 * Add custom headers to outgoing emails.
 *
 * @since 2.5.0
 *
 * @param array    $headers   Array of email headers.
 * @param string   $property  Name of property. Unused.
 * @param string   $transform Return value transformation. Unused.
 * @param SZ_Email $email     Email object reference.
 * @return array
 */
function sz_email_set_default_headers( $headers, $property, $transform, $email ) {
	$headers['X-SportsZone']      = sz_get_version();
	$headers['X-SportsZone-Type'] = $email->get( 'type' );

	$tokens = $email->get_tokens();

	// Add 'List-Unsubscribe' header if applicable.
	if ( ! empty( $tokens['unsubscribe'] ) && $tokens['unsubscribe'] !== wp_login_url() ) {
		$user = get_user_by( 'email', $tokens['recipient.email'] );

		$link = sz_email_get_unsubscribe_link( array(
			'user_id'           => $user->ID,
			'notification_type' => $email->get( 'type' ),
		) );

		if ( ! empty( $link ) ) {
			$headers['List-Unsubscribe'] = sprintf( '<%s>', esc_url_raw( $link ) );
		}
	}

	return $headers;
}
add_filter( 'sz_email_get_headers', 'sz_email_set_default_headers', 6, 4 );

/**
 * Add default email tokens.
 *
 * @since 2.5.0
 *
 * @param array    $tokens        Email tokens.
 * @param string   $property_name Unused.
 * @param string   $transform     Unused.
 * @param SZ_Email $email         Email being sent.
 * @return array
 */
function sz_email_set_default_tokens( $tokens, $property_name, $transform, $email ) {
	$tokens['site.admin-email'] = sz_get_option( 'admin_email' );
	$tokens['site.url']         = home_url();
	$tokens['email.subject']    = $email->get_subject();

	// These options are escaped with esc_html on the way into the database in sanitize_option().
	$tokens['site.description'] = wp_specialchars_decode( sz_get_option( 'blogdescription' ), ENT_QUOTES );
	$tokens['site.name']        = wp_specialchars_decode( sz_get_option( 'blogname' ), ENT_QUOTES );

	// Default values for tokens set conditionally below.
	$tokens['email.preheader']     = '';
	$tokens['recipient.email']     = '';
	$tokens['recipient.name']      = '';
	$tokens['recipient.username']  = '';

	// Who is the email going to?
	$recipient = $email->get( 'to' );
	if ( $recipient ) {
		$recipient = array_shift( $recipient );
		$user_obj  = $recipient->get_user( 'search-email' );

		$tokens['recipient.email'] = $recipient->get_address();
		$tokens['recipient.name']  = $recipient->get_name();

		if ( ! $user_obj && $tokens['recipient.email'] ) {
			$user_obj = get_user_by( 'email', $tokens['recipient.email'] );
		}

		if ( $user_obj ) {
			$tokens['recipient.username'] = $user_obj->user_login;

			if ( sz_is_active( 'settings' ) && empty( $tokens['unsubscribe'] ) ) {
				$tokens['unsubscribe'] = esc_url( sprintf(
					'%s%s/notifications/',
					sz_core_get_user_domain( $user_obj->ID ),
					sz_get_settings_slug()
				) );
			}
		}
	}

	// Set default unsubscribe link if not passed.
	if ( empty( $tokens['unsubscribe'] ) ) {
		$tokens['unsubscribe'] = wp_login_url();
	}

	// Email preheader.
	$post = $email->get_post_object();
	if ( $post ) {
		$tokens['email.preheader'] = sanitize_text_field( get_post_meta( $post->ID, 'sz_email_preheader', true ) );
	}

	return $tokens;
}
add_filter( 'sz_email_get_tokens', 'sz_email_set_default_tokens', 6, 4 );

/**
 * Find and render the template for Email posts (the Customizer and admin previews).
 *
 * Misuses the `template_include` filter which expects a string, but as we need to replace
 * the `{{{content}}}` token with the post's content, we use object buffering to load the
 * template, replace the token, and render it.
 *
 * The function returns an empty string to prevent WordPress rendering another template.
 *
 * @since 2.5.0
 *
 * @param string $template Path to template (probably single.php).
 * @return string
 */
function sz_core_render_email_template( $template ) {
	if ( get_post_type() !== sz_get_email_post_type() || ! is_single() ) {
		return $template;
	}

	/**
	 * Filter template used to display Email posts.
	 *
	 * @since 2.5.0
	 *
	 * @param string $template Path to current template (probably single.php).
	 */
	$email_template = apply_filters( 'sz_core_render_email_template',
		sz_locate_template( sz_email_get_template( get_queried_object() ), false ),
		$template
	);

	if ( ! $email_template ) {
		return $template;
	}

	ob_start();
	include( $email_template );
	$template = ob_get_contents();
	ob_end_clean();

	// Make sure we add a <title> tag so WP Customizer picks it up.
	$template = str_replace( '<head>', '<head><title>' . esc_html_x( 'SportsZone Emails', 'screen heading', 'sportszone' ) . '</title>', $template );
	echo str_replace( '{{{content}}}', wpautop( get_post()->post_content ), $template );

	/*
	 * Link colours are applied directly in the email template before sending, so we
	 * need to add an extra style here to set the colour for the Customizer or preview.
	 */
	$settings = sz_email_get_appearance_settings();
	printf(
		'<style>a { color: %s; }</style>',
		esc_attr( $settings['highlight_color'] )
	);

	return '';
}
add_action( 'sz_template_include', 'sz_core_render_email_template', 12 );
