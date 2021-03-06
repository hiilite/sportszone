<?php
/**
 * Core component template tag functions.
 *
 * @package SportsZone
 * @subpackage TemplateFunctions
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


/**
 * SportsZone templates
 *
 * The SportsZone templates class stores template layout data.
 *
 * @class 		SZ_Templates
 * @version     2.2
 * @package		SportsZone/Classes
 * @category	Class
 * @author 		ThemeBoy
  * Added Clubs
 */
class SZ_Templates {

	/** @var array Array of templates */
	private $data = array();

	/**
	 * Constructor for the templates class - defines all templates.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->data = array(
			'match' => array_merge(
				apply_filters( 'sportszone_before_match_template', array(
					'logos' => array(
						'title' => __( 'Teams', 'sportszone' ),
						'option' => 'sportszone_match_show_logos',
						'action' => 'sportszone_output_match_logos',
						'default' => 'yes',
					),
					'excerpt' => array(
						'title' => __( 'Excerpt', 'sportszone' ),
						'option' => 'sportszone_match_show_excerpt',
						'action' => 'sportszone_output_post_excerpt',
						'default' => 'yes',
					),
				) ),
				
				array(
					'content' => array(
						'title' => __( 'Article', 'sportszone' ),
						'option' => 'sportszone_match_show_content',
						'action' => 'sportszone_output_match_content',
						'default' => 'yes',
					),
				),
				
				apply_filters( 'sportszone_after_match_template', array(
					'video' => array(
						'title' => __( 'Video', 'sportszone' ),
						'option' => 'sportszone_match_show_video',
						'action' => 'sportszone_output_match_video',
						'default' => 'yes',
					),
					'details' => array(
						'title' => __( 'Details', 'sportszone' ),
						'option' => 'sportszone_match_show_details',
						'action' => 'sportszone_output_match_details',
						'default' => 'yes',
					),
					'venue' => array(
						'title' => __( 'Venue', 'sportszone' ),
						'option' => 'sportszone_match_show_venue',
						'action' => 'sportszone_output_match_venue',
						'default' => 'yes',
					),
					'results' => array(
						'title' => __( 'Results', 'sportszone' ),
						'option' => 'sportszone_match_show_results',
						'action' => 'sportszone_output_match_results',
						'default' => 'yes',
					),
					'performance' => array(
						'title' => __( 'Box Score', 'sportszone' ),
						'option' => 'sportszone_match_show_performance',
						'action' => 'sportszone_output_match_performance',
						'default' => 'yes',
					),
				) )
			),
			'calendar' => array_merge(
				apply_filters( 'sportszone_before_calendar_template', array() ),
				
				array(
					'content' => array(
						'title' => __( 'Description', 'sportszone' ),
						'option' => 'sportszone_calendar_show_content',
						'action' => 'sportszone_output_calendar_content',
						'default' => 'yes',
					),
				),
				
				apply_filters( 'sportszone_after_calendar_template', array(
					'data' => array(
						'title' => __( 'Calendar', 'sportszone' ),
						'option' => 'sportszone_calendar_show_data',
						'action' => 'sportszone_output_calendar',
						'default' => 'yes',
					),
				) )
			),
			'club' => array_merge(
				apply_filters( 'sportszone_before_club_template', array(
					'logo' => array(
						'title' => __( 'Logo', 'sportszone' ),
						'option' => 'sportszone_club_show_logo',
						'action' => 'sportszone_output_club_logo',
						'default' => 'yes',
					),
					'excerpt' => array(
						'title' => __( 'Excerpt', 'sportszone' ),
						'option' => 'sportszone_club_show_excerpt',
						'action' => 'sportszone_output_post_excerpt',
						'default' => 'yes',
					),
				) ),
				
				array(
					'content' => array(
						'title' => __( 'Profile', 'sportszone' ),
						'option' => 'sportszone_club_show_content',
						'action' => 'sportszone_output_club_content',
						'default' => 'yes',
					),
				),
				
				apply_filters( 'sportszone_after_club_template', array(
					'link' => array(
						'title' => __( 'Visit Site', 'sportszone' ),
						'label' => __( 'Link', 'sportszone' ),
						'option' => 'sportszone_club_show_link',
						'action' => 'sportszone_output_club_link',
						'default' => 'no',
					),
					'details' => array(
						'title' => __( 'Details', 'sportszone' ),
						'option' => 'sportszone_club_show_details',
						'action' => 'sportszone_output_club_details',
						'default' => 'no',
					),
					'staff' => array(
						'title' => __( 'Staff', 'sportszone' ),
						'option' => 'sportszone_club_show_staff',
						'action' => 'sportszone_output_club_staff',
						'default' => 'yes',
					),
				) )
			),
			'team' => array_merge(
				apply_filters( 'sportszone_before_team_template', array(
					'logo' => array(
						'title' => __( 'Logo', 'sportszone' ),
						'option' => 'sportszone_team_show_logo',
						'action' => 'sportszone_output_team_logo',
						'default' => 'yes',
					),
					'excerpt' => array(
						'title' => __( 'Excerpt', 'sportszone' ),
						'option' => 'sportszone_team_show_excerpt',
						'action' => 'sportszone_output_post_excerpt',
						'default' => 'yes',
					),
				) ),
				
				array(
					'content' => array(
						'title' => __( 'Profile', 'sportszone' ),
						'option' => 'sportszone_team_show_content',
						'action' => 'sportszone_output_team_content',
						'default' => 'yes',
					),
				),
				
				apply_filters( 'sportszone_after_team_template', array(
					'link' => array(
						'title' => __( 'Visit Site', 'sportszone' ),
						'label' => __( 'Link', 'sportszone' ),
						'option' => 'sportszone_team_show_link',
						'action' => 'sportszone_output_team_link',
						'default' => 'no',
					),
					'details' => array(
						'title' => __( 'Details', 'sportszone' ),
						'option' => 'sportszone_team_show_details',
						'action' => 'sportszone_output_team_details',
						'default' => 'no',
					),
					'staff' => array(
						'title' => __( 'Staff', 'sportszone' ),
						'option' => 'sportszone_team_show_staff',
						'action' => 'sportszone_output_team_staff',
						'default' => 'yes',
					),
				) )
			),
			'table' => array_merge(
				apply_filters( 'sportszone_before_table_template', array() ),
				
				array(
					'content' => array(
						'title' => __( 'Description', 'sportszone' ),
						'option' => 'sportszone_table_show_content',
						'action' => 'sportszone_output_table_content',
						'default' => 'yes',
					),
				),
				
				apply_filters( 'sportszone_after_table_template', array(
					'data' => array(
						'title' => __( 'League Table', 'sportszone' ),
						'option' => 'sportszone_table_show_data',
						'action' => 'sportszone_output_league_table',
						'default' => 'yes',
					),
				) )
			),
			'player' => array_merge(
				apply_filters( 'sportszone_before_player_template', array(
					'selector' => array(
						'title' => __( 'Dropdown', 'sportszone' ),
						'label' => __( 'Players', 'sportszone' ),
						'option' => 'sportszone_player_show_selector',
						'action' => 'sportszone_output_player_selector',
						'default' => 'yes',
					),
					'photo' => array(
						'title' => __( 'Photo', 'sportszone' ),
						'option' => 'sportszone_player_show_photo',
						'action' => 'sportszone_output_player_photo',
						'default' => 'yes',
					),
					'details' => array(
						'title' => __( 'Details', 'sportszone' ),
						'option' => 'sportszone_player_show_details',
						'action' => 'sportszone_output_player_details',
						'default' => 'yes',
					),
					'excerpt' => array(
						'title' => __( 'Excerpt', 'sportszone' ),
						'option' => 'sportszone_player_show_excerpt',
						'action' => 'sportszone_output_post_excerpt',
						'default' => 'yes',
					),
				) ),
				
				array(
					'content' => array(
						'title' => __( 'Profile', 'sportszone' ),
						'option' => 'sportszone_player_show_content',
						'action' => 'sportszone_output_player_content',
						'default' => 'yes',
					),
				),
				
				apply_filters( 'sportszone_after_player_template', array(
					'statistics' => array(
						'title' => __( 'Statistics', 'sportszone' ),
						'option' => 'sportszone_player_show_statistics',
						'action' => 'sportszone_output_player_statistics',
						'default' => 'yes',
					),
				) )
			),
			'list' => array_merge(
				apply_filters( 'sportszone_before_list_template', array() ),
				
				array(
					'content' => array(
						'title' => __( 'Description', 'sportszone' ),
						'option' => 'sportszone_list_show_content',
						'action' => 'sportszone_output_list_content',
						'default' => 'yes',
					),
				),
				
				apply_filters( 'sportszone_after_list_template', array(
					'data' => array(
						'title' => __( 'Player List', 'sportszone' ),
						'option' => 'sportszone_list_show_data',
						'action' => 'sportszone_output_player_list',
						'default' => 'yes',
					),
				) )
			),
			'staff' => array_merge(
				apply_filters( 'sportszone_before_staff_template', array(
					'selector' => array(
						'title' => __( 'Dropdown', 'sportszone' ),
						'label' => __( 'Staff', 'sportszone' ),
						'option' => 'sportszone_staff_show_selector',
						'action' => 'sportszone_output_staff_selector',
						'default' => 'yes',
					),
					'photo' => array(
						'title' => __( 'Photo', 'sportszone' ),
						'option' => 'sportszone_staff_show_photo',
						'action' => 'sportszone_output_staff_photo',
						'default' => 'yes',
					),
					'details' => array(
						'title' => __( 'Details', 'sportszone' ),
						'option' => 'sportszone_staff_show_details',
						'action' => 'sportszone_output_staff_details',
						'default' => 'yes',
					),
					'excerpt' => array(
						'title' => __( 'Excerpt', 'sportszone' ),
						'option' => 'sportszone_staff_show_excerpt',
						'action' => 'sportszone_output_post_excerpt',
						'default' => 'yes',
					),
				) ),
				
				array(
					'content' => array(
						'title' => __( 'Profile', 'sportszone' ),
						'option' => 'sportszone_staff_show_content',
						'action' => 'sportszone_output_staff_content',
						'default' => 'yes',
					),
				),
				
				apply_filters( 'sportszone_after_staff_template', array() )
			),
		);
	}

	public function __get( $key ) {
		return ( array_key_exists( $key, $this->data ) ? $this->data[ $key ] : array() );
	}

	public function __set( $key, $value ){
		$this->data[ $key ] = $value;
	}
}





/**
 * Output the "options nav", the secondary-level single item navigation menu.
 *
 * Uses the component's nav global to render out the sub navigation for the
 * current component. Each component adds to its sub navigation array within
 * its own setup_nav() function.
 *
 * This sub navigation array is the secondary level navigation, so for profile
 * it contains:
 *      [Public, Edit Profile, Change Avatar]
 *
 * The function will also analyze the current action for the current component
 * to determine whether or not to highlight a particular sub nav item.
 *
 * @since 1.0.0
 *
 *       viewed user.
 *
 * @param string $parent_slug Options nav slug.
 * @return string
 */
function sz_get_options_nav( $parent_slug = '' ) {
	$sz = sportszone();

	// If we are looking at a member profile, then the we can use the current
	// component as an index. Otherwise we need to use the component's root_slug.
	$component_index = !empty( $sz->displayed_user ) ? sz_current_component() : sz_get_root_slug( sz_current_component() );
	$selected_item   = sz_current_action();

	// Default to the Members nav.
	if ( ! sz_is_single_item() ) {
		// Set the parent slug, if not provided.
		if ( empty( $parent_slug ) ) {
			$parent_slug = $component_index;
		}

		$secondary_nav_items = $sz->members->nav->get_secondary( array( 'parent_slug' => $parent_slug ) );

		if ( ! $secondary_nav_items ) {
			return false;
		}

	// For a single item, try to use the component's nav.
	} else {
		$current_item = sz_current_item();
		$single_item_component = sz_current_component();

		// Adjust the selected nav item for the current single item if needed.
		if ( ! empty( $parent_slug ) ) {
			$current_item  = $parent_slug;
			$selected_item = sz_action_variable( 0 );
		}

		// If the nav is not defined by the parent component, look in the Members nav.
		if ( ! isset( $sz->{$single_item_component}->nav ) ) {
			$secondary_nav_items = $sz->members->nav->get_secondary( array( 'parent_slug' => $current_item ) );
		} else {
			$secondary_nav_items = $sz->{$single_item_component}->nav->get_secondary( array( 'parent_slug' => $current_item ) );
		}

		if ( ! $secondary_nav_items ) {
			return false;
		}
	}

	// Loop through each navigation item.
	foreach ( $secondary_nav_items as $subnav_item ) {
		if ( empty( $subnav_item->user_has_access ) ) {
			continue;
		}

		// If the current action or an action variable matches the nav item id, then add a highlight CSS class.
		if ( $subnav_item->slug === $selected_item ) {
			$selected = ' class="current selected"';
		} else {
			$selected = '';
		}

		// List type depends on our current component.
		$list_type = sz_is_group() ? 'groups' : 'personal';

		/**
		 * Filters the "options nav", the secondary-level single item navigation menu.
		 *
		 * This is a dynamic filter that is dependent on the provided css_id value.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value         HTML list item for the submenu item.
		 * @param array  $subnav_item   Submenu array item being displayed.
		 * @param string $selected_item Current action.
		 */
		echo apply_filters( 'sz_get_options_nav_' . $subnav_item->css_id, '<li id="' . esc_attr( $subnav_item->css_id . '-' . $list_type . '-li' ) . '" ' . $selected . '><a id="' . esc_attr( $subnav_item->css_id ) . '" href="' . esc_url( $subnav_item->link ) . '">' . $subnav_item->name . '</a></li>', $subnav_item, $selected_item );
	}
}

/**
 * Get the 'sz_options_title' property from the BP global.
 *
 * Not currently used in SportsZone.
 *
 * @todo Deprecate.
 */
function sz_get_options_title() {
	$sz = sportszone();

	if ( empty( $sz->sz_options_title ) ) {
		$sz->sz_options_title = __( 'Options', 'sportszone' );
	}

	echo apply_filters( 'sz_get_options_title', esc_attr( $sz->sz_options_title ) );
}

/**
 * Get the directory title for a component.
 *
 * Used for the <title> element and the page header on the component directory
 * page.
 *
 * @since 2.0.0
 *
 * @param string $component Component to get directory title for.
 * @return string
 */
function sz_get_directory_title( $component = '' ) {
	$title = '';

	// Use the string provided by the component.
	if ( ! empty( sportszone()->{$component}->directory_title ) ) {
		$title = sportszone()->{$component}->directory_title;

	// If none is found, concatenate.
	} elseif ( isset( sportszone()->{$component}->name ) ) {
		$title = sprintf( __( '%s Directory', 'sportszone' ), sportszone()->{$component}->name );
	}

	/**
	 * Filters the directory title for a component.
	 *
	 * @since 2.0.0
	 *
	 * @param string $title     Text to be used in <title> tag.
	 * @param string $component Current componet being displayed.
	 */
	return apply_filters( 'sz_get_directory_title', $title, $component );
}

/** Avatars *******************************************************************/

/**
 * Check to see if there is an options avatar.
 *
 * An options avatar is an avatar for something like a group, or a friend.
 * Basically an avatar that appears in the sub nav options bar.
 *
 * Not currently used in SportsZone.
 *
 * @return bool $value Returns true if an options avatar has been set, otherwise false.
 */
function sz_has_options_avatar() {
	return (bool) sportszone()->sz_options_avatar;
}

/**
 * Output the options avatar.
 *
 * Not currently used in SportsZone.
 *
 * @todo Deprecate.
 */
function sz_get_options_avatar() {
	echo apply_filters( 'sz_get_options_avatar', sportszone()->sz_options_avatar );
}

/**
 * Output a comment author's avatar.
 *
 * Not currently used in SportsZone.
 */
function sz_comment_author_avatar() {
	global $comment;

	if ( function_exists( 'sz_core_fetch_avatar' ) ) {
		echo apply_filters( 'sz_comment_author_avatar', sz_core_fetch_avatar( array(
			'item_id' => $comment->user_id,
			'type'    => 'thumb',
			'alt'     => sprintf( __( 'Profile photo of %s', 'sportszone' ), sz_core_get_user_displayname( $comment->user_id ) )
		) ) );
	} elseif ( function_exists( 'get_avatar' ) ) {
		get_avatar();
	}
}

/**
 * Output a post author's avatar.
 *
 * Not currently used in SportsZone.
 */
function sz_post_author_avatar() {
	global $post;

	if ( function_exists( 'sz_core_fetch_avatar' ) ) {
		echo apply_filters( 'sz_post_author_avatar', sz_core_fetch_avatar( array(
			'item_id' => $post->post_author,
			'type'    => 'thumb',
			'alt'     => sprintf( __( 'Profile photo of %s', 'sportszone' ), sz_core_get_user_displayname( $post->post_author ) )
		) ) );
	} elseif ( function_exists( 'get_avatar' ) ) {
		get_avatar();
	}
}

/**
 * Output the current avatar upload step.
 *
 * @since 1.1.0
 */
function sz_avatar_admin_step() {
	echo sz_get_avatar_admin_step();
}
	/**
	 * Return the current avatar upload step.
	 *
	 * @since 1.1.0
	 *
	 * @return string The current avatar upload step. Returns 'upload-image'
	 *         if none is found.
	 */
	function sz_get_avatar_admin_step() {
		$sz   = sportszone();
		$step = isset( $sz->avatar_admin->step )
			? $step = $sz->avatar_admin->step
			: 'upload-image';

		/**
		 * Filters the current avatar upload step.
		 *
		 * @since 1.1.0
		 *
		 * @param string $step The current avatar upload step.
		 */
		return apply_filters( 'sz_get_avatar_admin_step', $step );
	}

/**
 * Output the URL of the avatar to crop.
 *
 * @since 1.1.0
 */
function sz_avatar_to_crop() {
	echo sz_get_avatar_to_crop();
}
	/**
	 * Return the URL of the avatar to crop.
	 *
	 * @since 1.1.0
	 *
	 * @return string URL of the avatar awaiting cropping.
	 */
	function sz_get_avatar_to_crop() {
		$sz  = sportszone();
		$url = isset( $sz->avatar_admin->image->url )
			? $sz->avatar_admin->image->url
			: '';

		/**
		 * Filters the URL of the avatar to crop.
		 *
		 * @since 1.1.0
		 *
		 * @param string $url URL for the avatar.
		 */
		return apply_filters( 'sz_get_avatar_to_crop', $url );
	}

/**
 * Output the relative file path to the avatar to crop.
 *
 * @since 1.1.0
 */
function sz_avatar_to_crop_src() {
	echo sz_get_avatar_to_crop_src();
}
	/**
	 * Return the relative file path to the avatar to crop.
	 *
	 * @since 1.1.0
	 *
	 * @return string Relative file path to the avatar.
	 */
	function sz_get_avatar_to_crop_src() {
		$sz  = sportszone();
		$src = isset( $sz->avatar_admin->image->dir )
			? str_replace( WP_CONTENT_DIR, '', $sz->avatar_admin->image->dir )
			: '';

		/**
		 * Filters the relative file path to the avatar to crop.
		 *
		 * @since 1.1.0
		 *
		 * @param string $src Relative file path for the avatar.
		 */
		return apply_filters( 'sz_get_avatar_to_crop_src', $src );
	}

/**
 * Output the avatar cropper <img> markup.
 *
 * No longer used in SportsZone.
 *
 * @todo Deprecate.
 */
function sz_avatar_cropper() {
?>
	<img id="avatar-to-crop" class="avatar" src="<?php echo esc_url( sportszone()->avatar_admin->image ); ?>" />
<?php
}


/**
 * Output the current cover image upload step.
 *
 * @since 1.1.0
 */
function sz_cover_image_admin_step() {
	echo sz_get_cover_image_admin_step();
}
	/**
	 * Return the current avatar upload step.
	 *
	 * @since 1.1.0
	 *
	 * @return string The current cover image upload step. Returns 'upload-image'
	 *         if none is found.
	 */
	function sz_get_cover_image_admin_step() {
		$sz   = sportszone();
		$step = isset( $sz->cover_image_admin->step )
			? $step = $sz->cover_image_admin->step
			: 'upload-image';

		/**
		 * Filters the current avatar upload step.
		 *
		 * @since 1.1.0
		 *
		 * @param string $step The current avatar upload step.
		 */
		return apply_filters( 'sz_get_cover_image_admin_step', $step );
	}

/**
 * Output the URL of the cover image to crop.
 *
 * @since 1.1.0
 */
function sz_cover_image_to_crop() {
	echo sz_get_cover_image_to_crop();
}
	/**
	 * Return the URL of the cover image to crop.
	 *
	 * @since 1.1.0
	 *
	 * @return string URL of the cover image awaiting cropping.
	 */
	function sz_get_cover_image_to_crop() {
		$sz  = sportszone();
		$url = isset( $sz->cover_image_admin->image->url )
			? $sz->cover_image_admin->image->url
			: '';

		/**
		 * Filters the URL of the cover image to crop.
		 *
		 * @since 1.1.0
		 *
		 * @param string $url URL for the cover image.
		 */
		return apply_filters( 'sz_get_cover_image_to_crop', $url );
	}

/**
 * Output the relative file path to the cover image to crop.
 *
 * @since 1.1.0
 */
function sz_cover_image_to_crop_src() {
	echo sz_get_cover_image_to_crop_src();
}
	/**
	 * Return the relative file path to the cover image to crop.
	 *
	 * @since 1.1.0
	 *
	 * @return string Relative file path to the cover image.
	 */
	function sz_get_cover_image_to_crop_src() {
		$sz  = sportszone();
		$src = isset( $sz->cover_image_admin->image->dir )
			? str_replace( WP_CONTENT_DIR, '', $sz->cover_admin->image->dir )
			: '';

		/**
		 * Filters the relative file path to the avatar to crop.
		 *
		 * @since 1.1.0
		 *
		 * @param string $src Relative file path for the avatar.
		 */
		return apply_filters( 'sz_get_cover_image_to_crop_src', $src );
	}

/**
 * Output the avatar cropper <img> markup.
 *
 * No longer used in SportsZone.
 *
 * @todo Deprecate.
 */
function sz_cover_image_cropper() {
?>
	<img id="cover-to-crop" class="cover" src="<?php echo esc_url( sportszone()->cover_image_admin->image ); ?>" />
<?php
}


/**
 * Output the name of the BP site. Used in RSS headers.
 *
 * @since 1.0.0
 */
function sz_site_name() {
	echo sz_get_site_name();
}
	/**
	 * Returns the name of the BP site. Used in RSS headers.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	function sz_get_site_name() {

		/**
		 * Filters the name of the BP site. Used in RSS headers.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Current BP site name.
		 */
		return apply_filters( 'sz_site_name', get_bloginfo( 'name', 'display' ) );
	}

/**
 * Format a date based on a UNIX timestamp.
 *
 * This function can be used to turn a UNIX timestamp into a properly formatted
 * (and possibly localized) string, userful for ouputting the date & time an
 * action took place.
 *
 * Not to be confused with `sz_core_time_since()`, this function is best used
 * for displaying a more exact date and time vs. a human-readable time.
 *
 * Note: This function may be improved or removed at a later date, as it is
 * hardly used and adds an additional layer of complexity to calculating dates
 * and times together with timezone offsets and i18n.
 *
 * @since 1.1.0
 *
 * @param int|string $time         The UNIX timestamp to be formatted.
 * @param bool       $exclude_time Optional. True to return only the month + day, false
 *                                 to return month, day, and time. Default: false.
 * @param bool       $gmt          Optional. True to display in local time, false to
 *                                  leave in GMT. Default: true.
 * @return mixed A string representation of $time, in the format
 *               "March 18, 2014 at 2:00 pm" (or whatever your
 *               'date_format' and 'time_format' settings are
 *               on your root blog). False on failure.
 */
function sz_format_time( $time = '', $exclude_time = false, $gmt = true ) {

	// Bail if time is empty or not numeric
	// @todo We should output something smarter here.
	if ( empty( $time ) || ! is_numeric( $time ) ) {
		return false;
	}

	// Get GMT offset from root blog.
	if ( true === $gmt ) {

		// Use Timezone string if set.
		$timezone_string = sz_get_option( 'timezone_string' );
		if ( ! empty( $timezone_string ) ) {
			$timezone_object = timezone_open( $timezone_string );
			$datetime_object = date_create( "@{$time}" );
			$timezone_offset = timezone_offset_get( $timezone_object, $datetime_object ) / HOUR_IN_SECONDS;

		// Fall back on less reliable gmt_offset.
		} else {
			$timezone_offset = sz_get_option( 'gmt_offset' );
		}

		// Calculate time based on the offset.
		$calculated_time = $time + ( $timezone_offset * HOUR_IN_SECONDS );

	// No localizing, so just use the time that was submitted.
	} else {
		$calculated_time = $time;
	}

	// Formatted date: "March 18, 2014".
	$formatted_date = date_i18n( sz_get_option( 'date_format' ), $calculated_time, $gmt );

	// Should we show the time also?
	if ( true !== $exclude_time ) {

		// Formatted time: "2:00 pm".
		$formatted_time = date_i18n( sz_get_option( 'time_format' ), $calculated_time, $gmt );

		// Return string formatted with date and time.
		$formatted_date = sprintf( esc_html__( '%1$s at %2$s', 'sportszone' ), $formatted_date, $formatted_time );
	}

	/**
	 * Filters the date based on a UNIX timestamp.
	 *
	 * @since 1.0.0
	 *
	 * @param string $formatted_date Formatted date from the timestamp.
	 */
	return apply_filters( 'sz_format_time', $formatted_date );
}

/**
 * Select between two dynamic strings, according to context.
 *
 * This function can be used in cases where a phrase used in a template will
 * differ for a user looking at his own profile and a user looking at another
 * user's profile (eg, "My Friends" and "Joe's Friends"). Pass both versions
 * of the phrase, and sz_word_or_name() will detect which is appropriate, and
 * do the necessary argument swapping for dynamic phrases.
 *
 * @since 1.0.0
 *
 * @param string $youtext    The "you" version of the phrase (eg "Your Friends").
 * @param string $nametext   The other-user version of the phrase. Should be in
 *                           a format appropriate for sprintf() - use %s in place of the displayed
 *                           user's name (eg "%'s Friends").
 * @param bool   $capitalize Optional. Force into title case. Default: true.
 * @param bool   $echo       Optional. True to echo the results, false to return them.
 *                           Default: true.
 * @return string|null $nametext If ! $echo, returns the appropriate string.
 */
function sz_word_or_name( $youtext, $nametext, $capitalize = true, $echo = true ) {

	if ( ! empty( $capitalize ) ) {
		$youtext = sz_core_ucfirst( $youtext );
	}

	if ( sz_displayed_user_id() == sz_loggedin_user_id() ) {
		if ( true == $echo ) {

			/**
			 * Filters the text used based on context of own profile or someone else's profile.
			 *
			 * @since 1.0.0
			 *
			 * @param string $youtext Context-determined string to display.
			 */
			echo apply_filters( 'sz_word_or_name', $youtext );
		} else {

			/** This filter is documented in sz-core/sz-core-template.php */
			return apply_filters( 'sz_word_or_name', $youtext );
		}
	} else {
		$fullname = sz_get_displayed_user_fullname();
		$fullname = (array) explode( ' ', $fullname );
		$nametext = sprintf( $nametext, $fullname[0] );
		if ( true == $echo ) {

			/** This filter is documented in sz-core/sz-core-template.php */
			echo apply_filters( 'sz_word_or_name', $nametext );
		} else {

			/** This filter is documented in sz-core/sz-core-template.php */
			return apply_filters( 'sz_word_or_name', $nametext );
		}
	}
}

/**
 * Do the 'sz_styles' action, and call wp_print_styles().
 *
 * No longer used in SportsZone.
 *
 * @todo Deprecate.
 */
function sz_styles() {
	do_action( 'sz_styles' );
	wp_print_styles();
}

/** Search Form ***************************************************************/

/**
 * Return the "action" attribute for search forms.
 *
 * @since 1.0.0
 *
 * @return string URL action attribute for search forms, eg example.com/search/.
 */
function sz_search_form_action() {

	/**
	 * Filters the "action" attribute for search forms.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Search form action url.
	 */
	return apply_filters( 'sz_search_form_action', trailingslashit( sz_get_root_domain() . '/' . sz_get_search_slug() ) );
}

/**
 * Generate the basic search form as used in BP-Default's header.
 *
 * @since 1.0.0
 *
 * @return string HTML <select> element.
 */
function sz_search_form_type_select() {

	$options = array();

	if ( sz_is_active( 'xprofile' ) ) {
		$options['members'] = _x( 'Members', 'search form', 'sportszone' );
	}

	if ( sz_is_active( 'groups' ) ) {
		$options['groups']  = _x( 'Groups', 'search form', 'sportszone' );
	}

	if ( sz_is_active( 'blogs' ) && is_multisite() ) {
		$options['blogs']   = _x( 'Blogs', 'search form', 'sportszone' );
	}

	$options['posts'] = _x( 'Posts', 'search form', 'sportszone' );

	// Eventually this won't be needed and a page will be built to integrate all search results.
	$selection_box  = '<label for="search-which" class="accessibly-hidden">' . _x( 'Search these:', 'search form', 'sportszone' ) . '</label>';
	$selection_box .= '<select name="search-which" id="search-which" style="width: auto">';

	/**
	 * Filters all of the component options available for search scope.
	 *
	 * @since 1.5.0
	 *
	 * @param array $options Array of options to add to select field.
	 */
	$options = apply_filters( 'sz_search_form_type_select_options', $options );
	foreach( (array) $options as $option_value => $option_title ) {
		$selection_box .= sprintf( '<option value="%s">%s</option>', $option_value, $option_title );
	}

	$selection_box .= '</select>';

	/**
	 * Filters the complete <select> input used for search scope.
	 *
	 * @since 1.0.0
	 *
	 * @param string $selection_box <select> input for selecting search scope.
	 */
	return apply_filters( 'sz_search_form_type_select', $selection_box );
}

/**
 * Output the 'name' attribute for search form input element.
 *
 * @since 2.7.0
 *
 * @param string $component See sz_get_search_input_name().
 */
function sz_search_input_name( $component = '' ) {
	echo esc_attr( sz_get_search_input_name( $component ) );
}

/**
 * Get the 'name' attribute for the search form input element.
 *
 * @since 2.7.0
 *
 * @param string $component Component name. Defaults to current component.
 * @return string Text for the 'name' attribute.
 */
function sz_get_search_input_name( $component = '' ) {
	if ( ! $component ) {
		$component = sz_current_component();
	}

	$sz = sportszone();

	$name = '';
	if ( isset( $sz->{$component}->id ) ) {
		$name = $sz->{$component}->id . '_search';
	}

	return $name;
}

/**
 * Output the placeholder text for the search box for a given component.
 *
 * @since 2.7.0
 *
 * @param string $component See sz_get_search_placeholder().
 */
function sz_search_placeholder( $component = '' ) {
	echo esc_attr( sz_get_search_placeholder( $component ) );
}

/**
 * Get the placeholder text for the search box for a given component.
 *
 * @since 2.7.0
 *
 * @param string $component Component name. Defaults to current component.
 * @return string Placeholder text for the search field.
 */
function sz_get_search_placeholder( $component = '' ) {
	$query_arg = sz_core_get_component_search_query_arg( $component );

	if ( $query_arg && ! empty( $_REQUEST[ $query_arg ] ) ) {
		$placeholder = wp_unslash( $_REQUEST[ $query_arg ] );
	} else {
		$placeholder = sz_get_search_default_text( $component );
	}

	return $placeholder;
}

/**
 * Output the default text for the search box for a given component.
 *
 * @since 1.5.0
 *
 * @see sz_get_search_default_text()
 *
 * @param string $component See {@link sz_get_search_default_text()}.
 */
function sz_search_default_text( $component = '' ) {
	echo sz_get_search_default_text( $component );
}
	/**
	 * Return the default text for the search box for a given component.
	 *
	 * @since 1.5.0
	 *
	 * @param string $component Component name. Default: current component.
	 * @return string Placeholder text for search field.
	 */
	function sz_get_search_default_text( $component = '' ) {

		$sz = sportszone();

		if ( empty( $component ) ) {
			$component = sz_current_component();
		}

		$default_text = __( 'Search anything...', 'sportszone' );

		// Most of the time, $component will be the actual component ID.
		if ( !empty( $component ) ) {
			if ( !empty( $sz->{$component}->search_string ) ) {
				$default_text = $sz->{$component}->search_string;
			} else {
				// When the request comes through AJAX, we need to get the component
				// name out of $sz->pages.
				if ( !empty( $sz->pages->{$component}->slug ) ) {
					$key = $sz->pages->{$component}->slug;
					if ( !empty( $sz->{$key}->search_string ) ) {
						$default_text = $sz->{$key}->search_string;
					}
				}
			}
		}

		/**
		 * Filters the default text for the search box for a given component.
		 *
		 * @since 1.5.0
		 *
		 * @param string $default_text Default text for search box.
		 * @param string $component    Current component displayed.
		 */
		return apply_filters( 'sz_get_search_default_text', $default_text, $component );
	}

/**
 * Fire the 'sz_custom_profile_boxes' action.
 *
 * No longer used in SportsZone.
 *
 * @todo Deprecate.
 */
function sz_custom_profile_boxes() {
	do_action( 'sz_custom_profile_boxes' );
}

/**
 * Fire the 'sz_custom_profile_sidebar_boxes' action.
 *
 * No longer used in SportsZone.
 *
 * @todo Deprecate.
 */
function sz_custom_profile_sidebar_boxes() {
	do_action( 'sz_custom_profile_sidebar_boxes' );
}

/**
 * Output the attributes for a form field.
 *
 * @since 2.2.0
 *
 * @param string $name       The field name to output attributes for.
 * @param array  $attributes Array of existing attributes to add.
 */
function sz_form_field_attributes( $name = '', $attributes = array() ) {
	echo sz_get_form_field_attributes( $name, $attributes );
}
	/**
	 * Get the attributes for a form field.
	 *
	 * Primarily to add better support for touchscreen devices, but plugin devs
	 * can use the 'sz_get_form_field_extra_attributes' filter for further
	 * manipulation.
	 *
	 * @since 2.2.0
	 *
	 * @param string $name       The field name to get attributes for.
	 * @param array  $attributes Array of existing attributes to add.
	 * @return string
	 */
	function sz_get_form_field_attributes( $name = '', $attributes = array() ) {
		$retval = '';

		if ( empty( $attributes ) ) {
			$attributes = array();
		}

		$name = strtolower( $name );

		switch ( $name ) {
			case 'username' :
			case 'blogname' :
				$attributes['autocomplete']   = 'off';
				$attributes['autocapitalize'] = 'none';
				break;

			case 'email' :
				if ( wp_is_mobile() ) {
					$attributes['autocapitalize'] = 'none';
				}
				break;

			case 'password' :
				$attributes['spellcheck']   = 'false';
				$attributes['autocomplete'] = 'off';

				if ( wp_is_mobile() ) {
					$attributes['autocorrect']    = 'false';
					$attributes['autocapitalize'] = 'none';
				}
				break;
		}

		/**
		 * Filter the attributes for a field before rendering output.
		 *
		 * @since 2.2.0
		 *
		 * @param array  $attributes The field attributes.
		 * @param string $name       The field name.
		 */
		$attributes = (array) apply_filters( 'sz_get_form_field_attributes', $attributes, $name );

		foreach( $attributes as $attr => $value ) {
			// Numeric keyed array.
			if (is_numeric( $attr ) ) {
				$retval .= sprintf( ' %s', esc_attr( $value ) );

			// Associative keyed array.
			} else {
				$retval .= sprintf( ' %s="%s"', sanitize_key( $attr ), esc_attr( $value ) );
			}
		}

		return $retval;
	}

/**
 * Create and output a button.
 *
 * @since 1.2.6
 *
 * @see sz_get_button()
 *
 * @param array|string $args See {@link SZ_Button}.
 */
function sz_button( $args = '' ) {
	echo sz_get_button( $args );
}
	/**
	 * Create and return a button.
	 *
	 * @since 1.2.6
	 *
	 * @see SZ_Button for a description of arguments and return value.
	 *
	 * @param array|string $args See {@link SZ_Button}.
	 * @return string HTML markup for the button.
	 */
	function sz_get_button( $args = '' ) {
		$button = new SZ_Button( $args );

		/**
		 * Filters the requested button output.
		 *
		 * @since 1.2.6
		 *
		 * @param string    $contents  Button context to be used.
		 * @param array     $args      Array of args for the button.
		 * @param SZ_Button $button    SZ_Button object.
		 */
		return apply_filters( 'sz_get_button', $button->contents, $args, $button );
	}

/**
 * Truncate text.
 *
 * Cuts a string to the length of $length and replaces the last characters
 * with the ending if the text is longer than length.
 *
 * This function is borrowed from CakePHP v2.0, under the MIT license. See
 * http://book.cakephp.org/view/1469/Text#truncate-1625
 *
 * @since 1.0.0
 * @since 2.6.0 Added 'strip_tags' and 'remove_links' as $options args.
 *
 * @param string $text   String to truncate.
 * @param int    $length Optional. Length of returned string, including ellipsis.
 *                       Default: 225.
 * @param array  $options {
 *     An array of HTML attributes and options. Each item is optional.
 *     @type string $ending            The string used after truncation.
 *                                     Default: ' [&hellip;]'.
 *     @type bool   $exact             If true, $text will be trimmed to exactly $length.
 *                                     If false, $text will not be cut mid-word. Default: false.
 *     @type bool   $html              If true, don't include HTML tags when calculating
 *                                     excerpt length. Default: true.
 *     @type bool   $filter_shortcodes If true, shortcodes will be stripped.
 *                                     Default: true.
 *     @type bool   $strip_tags        If true, HTML tags will be stripped. Default: false.
 *                                     Only applicable if $html is set to false.
 *     @type bool   $remove_links      If true, URLs will be stripped. Default: false.
 *                                     Only applicable if $html is set to false.
 * }
 * @return string Trimmed string.
 */
function sz_create_excerpt( $text, $length = 225, $options = array() ) {

	// Backward compatibility. The third argument used to be a boolean $filter_shortcodes.
	$filter_shortcodes_default = is_bool( $options ) ? $options : true;

	$r = sz_parse_args( $options, array(
		'ending'            => __( ' [&hellip;]', 'sportszone' ),
		'exact'             => false,
		'html'              => true,
		'filter_shortcodes' => $filter_shortcodes_default,
		'strip_tags'        => false,
		'remove_links'      => false,
	), 'create_excerpt' );

	// Save the original text, to be passed along to the filter.
	$original_text = $text;

	/**
	 * Filters the excerpt length to trim text to.
	 *
	 * @since 1.5.0
	 *
	 * @param int $length Length of returned string, including ellipsis.
	 */
	$length = apply_filters( 'sz_excerpt_length',      $length      );

	/**
	 * Filters the excerpt appended text value.
	 *
	 * @since 1.5.0
	 *
	 * @param string $value Text to append to the end of the excerpt.
	 */
	$ending = apply_filters( 'sz_excerpt_append_text', $r['ending'] );

	// Remove shortcodes if necessary.
	if ( ! empty( $r['filter_shortcodes'] ) ) {
		$text = strip_shortcodes( $text );
	}

	// When $html is true, the excerpt should be created without including HTML tags in the
	// excerpt length.
	if ( ! empty( $r['html'] ) ) {

		// The text is short enough. No need to truncate.
		if ( mb_strlen( preg_replace( '/<.*?>/', '', $text ) ) <= $length ) {
			return $text;
		}

		$totalLength = mb_strlen( strip_tags( $ending ) );
		$openTags    = array();
		$truncate    = '';

		// Find all the tags and HTML comments and put them in a stack for later use.
		preg_match_all( '/(<\/?([\w+!]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER );

		foreach ( $tags as $tag ) {
			// Process tags that need to be closed.
			if ( !preg_match( '/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s',  $tag[2] ) ) {
				if ( preg_match( '/<[\w]+[^>]*>/s', $tag[0] ) ) {
					array_unshift( $openTags, $tag[2] );
				} elseif ( preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag ) ) {
					$pos = array_search( $closeTag[1], $openTags );
					if ( $pos !== false ) {
						array_splice( $openTags, $pos, 1 );
					}
				}
			}

			$truncate     .= $tag[1];
			$contentLength = mb_strlen( preg_replace( '/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3] ) );

			if ( $contentLength + $totalLength > $length ) {
				$left = $length - $totalLength;
				$entitiesLength = 0;
				if ( preg_match_all( '/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE ) ) {
					foreach ( $entities[0] as $entity ) {
						if ( $entity[1] + 1 - $entitiesLength <= $left ) {
							$left--;
							$entitiesLength += mb_strlen( $entity[0] );
						} else {
							break;
						}
					}
				}

				$truncate .= mb_substr( $tag[3], 0 , $left + $entitiesLength );
				break;
			} else {
				$truncate .= $tag[3];
				$totalLength += $contentLength;
			}
			if ( $totalLength >= $length ) {
				break;
			}
		}
	} else {
		// Strip HTML tags if necessary.
		if ( ! empty( $r['strip_tags'] ) ) {
			$text = strip_tags( $text );
		}

		// Remove links if necessary.
		if ( ! empty( $r['remove_links'] ) ) {
			$text = preg_replace( '#^\s*(https?://[^\s"]+)\s*$#im', '', $text );
		}

		if ( mb_strlen( $text ) <= $length ) {
			/**
			 * Filters the final generated excerpt.
			 *
			 * @since 1.1.0
			 *
			 * @param string $truncate      Generated excerpt.
			 * @param string $original_text Original text provided.
			 * @param int    $length        Length of returned string, including ellipsis.
			 * @param array  $options       Array of HTML attributes and options.
			 */
			return apply_filters( 'sz_create_excerpt', $text, $original_text, $length, $options );
		} else {
			$truncate = mb_substr( $text, 0, $length - mb_strlen( $ending ) );
		}
	}

	// If $exact is false, we can't break on words.
	if ( empty( $r['exact'] ) ) {
		// Find the position of the last space character not part of a tag.
		preg_match_all( '/<[a-z\!\/][^>]*>/', $truncate, $_truncate_tags, PREG_OFFSET_CAPTURE );

		// Rekey tags by the string index of their last character.
		$truncate_tags = array();
		if ( ! empty( $_truncate_tags[0] ) ) {
			foreach ( $_truncate_tags[0] as $_tt ) {
				$_tt['start'] = $_tt[1];
				$_tt['end']   = $_tt[1] + strlen( $_tt[0] );
				$truncate_tags[ $_tt['end'] ] = $_tt;
			}
		}

		$truncate_length = mb_strlen( $truncate );
		$spacepos = $truncate_length + 1;
		for ( $pos = $truncate_length - 1; $pos >= 0; $pos-- ) {
			// Word boundaries are spaces and the close of HTML tags, when the tag is preceded by a space.
			$is_word_boundary = ' ' === $truncate[ $pos ];
			if ( ! $is_word_boundary && isset( $truncate_tags[ $pos - 1 ] ) ) {
				$preceding_tag    = $truncate_tags[ $pos - 1 ];
				if ( ' ' === $truncate[ $preceding_tag['start'] - 1 ] ) {
					$is_word_boundary = true;
					break;
				}
			}

			if ( ! $is_word_boundary ) {
				continue;
			}

			// If there are no tags in the string, the first space found is the right one.
			if ( empty( $truncate_tags ) ) {
				$spacepos = $pos;
				break;
			}

			// Look at each tag to see if the space is inside of it.
			$intag = false;
			foreach ( $truncate_tags as $tt ) {
				if ( $pos > $tt['start'] && $pos < $tt['end'] ) {
					$intag = true;
					break;
				}
			}

			if ( ! $intag ) {
				$spacepos = $pos;
				break;
			}
		}

		if ( $r['html'] ) {
			$bits = mb_substr( $truncate, $spacepos );
			preg_match_all( '/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER );
			if ( !empty( $droppedTags ) ) {
				foreach ( $droppedTags as $closingTag ) {
					if ( !in_array( $closingTag[1], $openTags ) ) {
						array_unshift( $openTags, $closingTag[1] );
					}
				}
			}
		}

		$truncate = rtrim( mb_substr( $truncate, 0, $spacepos ) );
	}
	$truncate .= $ending;

	if ( !empty( $r['html'] ) ) {
		foreach ( $openTags as $tag ) {
			$truncate .= '</' . $tag . '>';
		}
	}

	/** This filter is documented in /sz-core/sz-core-template.php */
	return apply_filters( 'sz_create_excerpt', $truncate, $original_text, $length, $options );
}
add_filter( 'sz_create_excerpt', 'stripslashes_deep'  );
add_filter( 'sz_create_excerpt', 'force_balance_tags' );

/**
 * Output the total member count for the site.
 *
 * @since 1.2.0
 */
function sz_total_member_count() {
	echo sz_get_total_member_count();
}
	/**
	 * Return the total member count in your BP instance.
	 *
	 * Since SportsZone 1.6, this function has used sz_core_get_active_member_count(),
	 * which counts non-spam, non-deleted users who have last_activity.
	 * This value will correctly match the total member count number used
	 * for pagination on member directories.
	 *
	 * Before SportsZone 1.6, this function used sz_core_get_total_member_count(),
	 * which did not take into account last_activity, and thus often
	 * resulted in higher counts than shown by member directory pagination.
	 *
	 * @since 1.2.0
	 *
	 * @return int Member count.
	 */
	function sz_get_total_member_count() {

		/**
		 * Filters the total member count in your BP instance.
		 *
		 * @since 1.2.0
		 *
		 * @param int $value Member count.
		 */
		return apply_filters( 'sz_get_total_member_count', sz_core_get_active_member_count() );
	}
	add_filter( 'sz_get_total_member_count', 'sz_core_number_format' );

/**
 * Output whether blog signup is allowed.
 *
 * @todo Deprecate. It doesn't make any sense to echo a boolean.
 */
function sz_blog_signup_allowed() {
	echo sz_get_blog_signup_allowed();
}
	/**
	 * Is blog signup allowed?
	 *
	 * Returns true if is_multisite() and blog creation is enabled at
	 * Network Admin > Settings.
	 *
	 * @since 1.2.0
	 *
	 * @return bool True if blog signup is allowed, otherwise false.
	 */
	function sz_get_blog_signup_allowed() {

		if ( ! is_multisite() ) {
			return false;
		}

		$status = sz_core_get_root_option( 'registration' );
		if ( ( 'none' !== $status ) && ( 'user' !== $status ) ) {
			return true;
		}

		return false;
	}

/**
 * Check whether an activation has just been completed.
 *
 * @since 1.1.0
 *
 * @return bool True if the activation_complete global flag has been set,
 *              otherwise false.
 */
function sz_account_was_activated() {
	$activation_complete = ! empty( sportszone()->activation_complete ) || ( sz_is_current_component( 'activate' ) && ! empty( $_GET['activated'] ) );

	return $activation_complete;
}

/**
 * Check whether registrations require activation on this installation.
 *
 * On a normal SportsZone installation, all registrations require email
 * activation. This filter exists so that customizations that omit activation
 * can remove certain notification text from the registration screen.
 *
 * @since 1.2.0
 *
 * @return bool True by default.
 */
function sz_registration_needs_activation() {

	/**
	 * Filters whether registrations require activation on this installation.
	 *
	 * @since 1.2.0
	 *
	 * @param bool $value Whether registrations require activation. Default true.
	 */
	return apply_filters( 'sz_registration_needs_activation', true );
}

/**
 * Retrieve a client friendly version of the root blog name.
 *
 * The blogname option is escaped with esc_html on the way into the database in
 * sanitize_option, we want to reverse this for the plain text arena of emails.
 *
 * @since 1.7.0
 * @since 2.5.0 No longer used by SportsZone, but not deprecated in case any existing plugins use it.
 *
 * @see https://sportszone.trac.wordpress.org/ticket/4401
 *
 * @param array $args {
 *     Array of optional parameters.
 *     @type string $before  String to appear before the site name in the
 *                           email subject. Default: '['.
 *     @type string $after   String to appear after the site name in the
 *                           email subject. Default: ']'.
 *     @type string $default The default site name, to be used when none is
 *                           found in the database. Default: 'Community'.
 *     @type string $text    Text to append to the site name (ie, the main text of
 *                           the email subject).
 * }
 * @return string Sanitized email subject.
 */
function sz_get_email_subject( $args = array() ) {

	$r = sz_parse_args( $args, array(
		'before'  => '[',
		'after'   => ']',
		'default' => __( 'Community', 'sportszone' ),
		'text'    => ''
	), 'get_email_subject' );

	$subject = $r['before'] . wp_specialchars_decode( sz_get_option( 'blogname', $r['default'] ), ENT_QUOTES ) . $r['after'] . ' ' . $r['text'];

	/**
	 * Filters a client friendly version of the root blog name.
	 *
	 * @since 1.7.0
	 *
	 * @param string $subject Client friendy version of the root blog name.
	 * @param array  $r       Array of arguments for the email subject.
	 */
	return apply_filters( 'sz_get_email_subject', $subject, $r );
}

/**
 * Allow templates to pass parameters directly into the template loops via AJAX.
 *
 * For the most part this will be filtered in a theme's functions.php for
 * example in the default theme it is filtered via sz_dtheme_ajax_querystring().
 *
 * By using this template tag in the templates it will stop them from showing
 * errors if someone copies the templates from the default theme into another
 * WordPress theme without coping the functions from functions.php.
 *
 * @since 1.2.0
 *
 * @param string|bool $object Current template component.
 * @return string The AJAX querystring.
 */
function sz_ajax_querystring( $object = false ) {
	$sz = sportszone();

	if ( ! isset( $sz->ajax_querystring ) ) {
		$sz->ajax_querystring = '';
	}

	/**
	 * Filters the template paramenters to be used in the query string.
	 *
	 * Allows templates to pass parameters into the template loops via AJAX.
	 *
	 * @since 1.2.0
	 *
	 * @param string $ajax_querystring Current query string.
	 * @param string $object           Current template component.
	 */
	return apply_filters( 'sz_ajax_querystring', $sz->ajax_querystring, $object );
}

/** Template Classes and _is functions ****************************************/

/**
 * Return the name of the current component.
 *
 * @since 1.0.0
 *
 * @return string Component name.
 */
function sz_current_component() {
	global $post;
	$sz                = sportszone();
	$current_component = !empty( $sz->current_component )
		? $sz->current_component
		: false;
		
	/**
	 * Filters the name of the current component.
	 *
	 * @since 1.0.0
	 *
	 * @param string|bool $current_component Current component if available or false.
	 */
	return apply_filters( 'sz_current_component', $current_component );
}

/**
 * Return the name of the current action.
 *
 * @since 1.0.0
 *
 * @return string Action name.
 */
function sz_current_action() {
	$sz             = sportszone();
	$current_action = !empty( $sz->current_action )
		? $sz->current_action
		: '';

	/**
	 * Filters the name of the current action.
	 *
	 * @since 1.0.0
	 *
	 * @param string $current_action Current action.
	 */
	return apply_filters( 'sz_current_action', $current_action );
}

/**
 * Return the name of the current item.
 *
 * @since 1.1.0
 *
 * @return string|bool
 */
function sz_current_item() {
	$sz           = sportszone();
	$current_item = !empty( $sz->current_item )
		? $sz->current_item
		: false;

	/**
	 * Filters the name of the current item.
	 *
	 * @since 1.1.0
	 *
	 * @param string|bool $current_item Current item if available or false.
	 */
	return apply_filters( 'sz_current_item', $current_item );
}

/**
 * Return the value of $sz->action_variables.
 *
 * @since 1.0.0
 *
 * @return array|bool $action_variables The action variables array, or false
 *                                      if the array is empty.
 */
function sz_action_variables() {
	$sz               = sportszone();
	$action_variables = !empty( $sz->action_variables )
		? $sz->action_variables
		: false;

	/**
	 * Filters the value of $sz->action_variables.
	 *
	 * @since 1.0.0
	 *
	 * @param array|bool $action_variables Available action variables.
	 */
	return apply_filters( 'sz_action_variables', $action_variables );
}

/**
 * Return the value of a given action variable.
 *
 * @since 1.5.0
 *
 * @param int $position The key of the action_variables array that you want.
 * @return string|bool $action_variable The value of that position in the
 *                                      array, or false if not found.
 */
function sz_action_variable( $position = 0 ) {
	$action_variables = sz_action_variables();
	$action_variable  = isset( $action_variables[ $position ] )
		? $action_variables[ $position ]
		: false;

	/**
	 * Filters the value of a given action variable.
	 *
	 * @since 1.5.0
	 *
	 * @param string|bool $action_variable Requested action variable based on position.
	 * @param int         $position        The key of the action variable requested.
	 */
	return apply_filters( 'sz_action_variable', $action_variable, $position );
}

/**
 * Output the "root domain", the URL of the BP root blog.
 *
 * @since 1.1.0
 */
function sz_root_domain() {
	echo sz_get_root_domain();
}
	/**
	 * Return the "root domain", the URL of the BP root blog.
	 *
	 * @since 1.1.0
	 *
	 * @return string URL of the BP root blog.
	 */
	function sz_get_root_domain() {
		$sz = sportszone();

		if ( ! empty( $sz->root_domain ) ) {
			$domain = $sz->root_domain;
		} else {
			$domain          = sz_core_get_root_domain();
			$sz->root_domain = $domain;
		}

		/**
		 * Filters the "root domain", the URL of the BP root blog.
		 *
		 * @since 1.2.4
		 *
		 * @param string $domain URL of the BP root blog.
		 */
		return apply_filters( 'sz_get_root_domain', $domain );
	}

/**
 * Output the root slug for a given component.
 *
 * @since 1.5.0
 *
 * @param string $component The component name.
 */
function sz_root_slug( $component = '' ) {
	echo sz_get_root_slug( $component );
}
	/**
	 * Get the root slug for given component.
	 *
	 * The "root slug" is the string used when concatenating component
	 * directory URLs. For example, on an installation where the Groups
	 * component's directory is located at http://example.com/groups/, the
	 * root slug for the Groups component is 'groups'. This string
	 * generally corresponds to page_name of the component's directory
	 * page.
	 *
	 * In order to maintain backward compatibility, the following procedure
	 * is used:
	 * 1) Use the short slug to get the canonical component name from the
	 *    active component array.
	 * 2) Use the component name to get the root slug out of the
	 *    appropriate part of the $sz global.
	 * 3) If nothing turns up, it probably means that $component is itself
	 *    a root slug.
	 *
	 * Example: If your groups directory is at /community/companies, this
	 * function first uses the short slug 'companies' (ie the current
	 * component) to look up the canonical name 'groups' in
	 * $sz->active_components. Then it uses 'groups' to get the root slug,
	 * from $sz->groups->root_slug.
	 *
	 * @since 1.5.0
	 *
	 * @param string $component Optional. Defaults to the current component.
	 * @return string $root_slug The root slug.
	 */
	function sz_get_root_slug( $component = '' ) {
		$sz        = sportszone();
		$root_slug = '';

		// Use current global component if none passed.
		if ( empty( $component ) ) {
			$component = sz_current_component();
		}

		// Component is active.
		if ( ! empty( $sz->active_components[ $component ] ) ) {

			// Backward compatibility: in legacy plugins, the canonical component id
			// was stored as an array value in $sz->active_components.
			$component_name = ( '1' == $sz->active_components[ $component ] )
				? $component
				: $sz->active_components[$component];

			// Component has specific root slug.
			if ( ! empty( $sz->{$component_name}->root_slug ) ) {
				$root_slug = $sz->{$component_name}->root_slug;
			}
		}

		// No specific root slug, so fall back to component slug.
		if ( empty( $root_slug ) ) {
			$root_slug = $component;
		}

		/**
		 * Filters the root slug for given component.
		 *
		 * @since 1.5.0
		 *
		 * @param string $root_slug Root slug for given component.
		 * @param string $component Current component.
		 */
		return apply_filters( 'sz_get_root_slug', $root_slug, $component );
	}

/**
 * Return the component name based on a root slug.
 *
 * @since 1.5.0
 *
 * @param string $root_slug Needle to our active component haystack.
 * @return mixed False if none found, component name if found.
 */
function sz_get_name_from_root_slug( $root_slug = '' ) {
	$sz = sportszone();

	// If no slug is passed, look at current_component.
	if ( empty( $root_slug ) ) {
		$root_slug = sz_current_component();
	}

	// No current component or root slug, so flee.
	if ( empty( $root_slug ) ) {
		return false;
	}

	// Loop through active components and look for a match.
	foreach ( array_keys( $sz->active_components ) as $component ) {
		if ( ( ! empty( $sz->{$component}->slug ) && ( $sz->{$component}->slug == $root_slug ) ) || ( ! empty( $sz->{$component}->root_slug ) && ( $sz->{$component}->root_slug === $root_slug ) ) ) {
			return $sz->{$component}->name;
		}
	}

	return false;
}

/**
 * Returns whether or not a user has access.
 *
 * @since 1.2.4
 *
 * @return bool
 */
function sz_user_has_access() {
	$has_access = sz_current_user_can( 'sz_moderate' ) || sz_is_my_profile();

	/**
	 * Filters whether or not a user has access.
	 *
	 * @since 1.2.4
	 *
	 * @param bool $has_access Whether or not user has access.
	 */
	return (bool) apply_filters( 'sz_user_has_access', $has_access );
}

/**
 * Output the search slug.
 *
 * @since 1.5.0
 *
 */
function sz_search_slug() {
	echo sz_get_search_slug();
}
	/**
	 * Return the search slug.
	 *
	 * @since 1.5.0
	 *
	 * @return string The search slug. Default: 'search'.
	 */
	function sz_get_search_slug() {

		/**
		 * Filters the search slug.
		 *
		 * @since 1.5.0
		 *
		 * @const string SZ_SEARCH_SLUG The search slug. Default "search".
		 */
		return apply_filters( 'sz_get_search_slug', SZ_SEARCH_SLUG );
	}

/**
 * Get the ID of the currently displayed user.
 *
 * @since 1.0.0
 *
 * @return int $id ID of the currently displayed user.
 */
function sz_displayed_user_id() {
	$sz = sportszone();
	$id = !empty( $sz->displayed_user->id )
		? $sz->displayed_user->id
		: 0;

	/**
	 * Filters the ID of the currently displayed user.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the currently displayed user.
	 */
	return (int) apply_filters( 'sz_displayed_user_id', $id );
}

/**
 * Get the ID of the currently logged-in user.
 *
 * @since 1.0.0
 *
 * @return int ID of the logged-in user.
 */
function sz_loggedin_user_id() {
	$sz = sportszone();
	$id = !empty( $sz->loggedin_user->id )
		? $sz->loggedin_user->id
		: 0;

	/**
	 * Filters the ID of the currently logged-in user.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the currently logged-in user.
	 */
	return (int) apply_filters( 'sz_loggedin_user_id', $id );
}

/** The is_() functions to determine the current page *****************************/

/**
 * Check to see whether the current page belongs to the specified component.
 *
 * This function is designed to be generous, accepting several different kinds
 * of value for the $component parameter. It checks $component_name against:
 * - the component's root_slug, which matches the page slug in $sz->pages.
 * - the component's regular slug.
 * - the component's id, or 'canonical' name.
 *
 * @since 1.5.0
 *
 * @param string $component Name of the component being checked.
 * @return bool Returns true if the component matches, or else false.
 */
function sz_is_current_component( $component = '' ) {
	global $post;
	// Default is no match. We'll check a few places for matches.
	$is_current_component = false;

	// Always return false if a null value is passed to the function.
	if ( empty( $component ) ) {
		return false;
	}

	// Backward compatibility: 'xprofile' should be read as 'profile'.
	if ( 'xprofile' === $component ) {
		$component = 'profile';
	}
	$sz = sportszone();
	
	if(get_post_type( $post ) == 'sz_match'){
		$component = 'events';
		$is_current_component = true;
	}
	
	// Only check if SportsZone found a current_component.
	if ( ! empty( $sz->current_component ) ) {

		// First, check to see whether $component_name and the current
		// component are a simple match.
		if ( $sz->current_component == $component ) {
			$is_current_component = true;

		// Since the current component is based on the visible URL slug let's
		// check the component being passed and see if its root_slug matches.
		} elseif ( isset( $sz->{$component}->root_slug ) && $sz->{$component}->root_slug == $sz->current_component ) {
			$is_current_component = true;

		// Because slugs can differ from root_slugs, we should check them too.
		} elseif ( isset( $sz->{$component}->slug ) && $sz->{$component}->slug == $sz->current_component ) {
			$is_current_component = true;

		// Next, check to see whether $component is a canonical,
		// non-translatable component name. If so, we can return its
		// corresponding slug from $sz->active_components.
		} elseif ( $key = array_search( $component, $sz->active_components ) ) {
			if ( strstr( $sz->current_component, $key ) ) {
				$is_current_component = true;
			}

		// If we haven't found a match yet, check against the root_slugs
		// created by $sz->pages, as well as the regular slugs.
		} else {
			foreach ( $sz->active_components as $id ) {
				// If the $component parameter does not match the current_component,
				// then move along, these are not the droids you are looking for.
				if ( empty( $sz->{$id}->root_slug ) || $sz->{$id}->root_slug != $sz->current_component ) {
					continue;
				}

				if ( $id == $component ) {
					$is_current_component = true;
					break;
				}
			}
		}
	}

	/**
	 * Filters whether the current page belongs to the specified component.
	 *
	 * @since 1.5.0
	 *
	 * @param bool   $is_current_component Whether or not the current page belongs to specified component.
	 * @param string $component            Name of the component being checked.
	 */
	return apply_filters( 'sz_is_current_component', $is_current_component, $component );
}

/**
 * Check to see whether the current page matches a given action.
 *
 * Along with sz_is_current_component() and sz_is_action_variable(), this
 * function is mostly used to help determine when to use a given screen
 * function.
 *
 * In BP parlance, the current_action is the URL chunk that comes directly
 * after the current item slug. E.g., in
 *   http://example.com/groups/my-group/members
 * the current_action is 'members'.
 *
 * @since 1.5.0
 *
 * @param string $action The action being tested against.
 * @return bool True if the current action matches $action.
 */
function sz_is_current_action( $action = '' ) {
	return (bool) ( $action === sz_current_action() );
}

/**
 * Check to see whether the current page matches a given action_variable.
 *
 * Along with sz_is_current_component() and sz_is_current_action(), this
 * function is mostly used to help determine when to use a given screen
 * function.
 *
 * In BP parlance, action_variables are an array made up of the URL chunks
 * appearing after the current_action in a URL. For example,
 *   http://example.com/groups/my-group/admin/group-settings
 * $action_variables[0] is 'group-settings'.
 *
 * @since 1.5.0
 *
 * @param string   $action_variable The action_variable being tested against.
 * @param int|bool $position        Optional. The array key you're testing against. If you
 *                                  don't provide a $position, the function will return true if the
 *                                  $action_variable is found *anywhere* in the action variables array.
 * @return bool True if $action_variable matches at the $position provided.
 */
function sz_is_action_variable( $action_variable = '', $position = false ) {
	$is_action_variable = false;

	if ( false !== $position ) {
		// When a $position is specified, check that slot in the action_variables array.
		if ( $action_variable ) {
			$is_action_variable = $action_variable == sz_action_variable( $position );
		} else {
			// If no $action_variable is provided, we are essentially checking to see
			// whether the slot is empty.
			$is_action_variable = !sz_action_variable( $position );
		}
	} else {
		// When no $position is specified, check the entire array.
		$is_action_variable = in_array( $action_variable, (array)sz_action_variables() );
	}

	/**
	 * Filters whether the current page matches a given action_variable.
	 *
	 * @since 1.5.0
	 *
	 * @param bool   $is_action_variable Whether the current page matches a given action_variable.
	 * @param string $action_variable    The action_variable being tested against.
	 * @param int    $position           The array key tested against.
	 */
	return apply_filters( 'sz_is_action_variable', $is_action_variable, $action_variable, $position );
}

/**
 * Check against the current_item.
 *
 * @since 1.5.0
 *
 * @param string $item The item being checked.
 * @return bool True if $item is the current item.
 */
function sz_is_current_item( $item = '' ) {
	$retval = ( $item === sz_current_item() );

	/**
	 * Filters whether or not an item is the current item.
	 *
	 * @since 2.1.0
	 *
	 * @param bool   $retval Whether or not an item is the current item.
	 * @param string $item   The item being checked.
	 */
	return (bool) apply_filters( 'sz_is_current_item', $retval, $item );
}

/**
 * Are we looking at a single item? (group, user, etc).
 *
 * @since 1.1.0
 *
 * @return bool True if looking at a single item, otherwise false.
 */
function sz_is_single_item() {
	$sz     = sportszone();
	$retval = false;

	if ( isset( $sz->is_single_item ) ) {
		$retval = $sz->is_single_item;
	}

	/**
	 * Filters whether or not an item is the a single item. (group, user, etc)
	 *
	 * @since 2.1.0
	 *
	 * @param bool $retval Whether or not an item is a single item.
	 */
	return (bool) apply_filters( 'sz_is_single_item', $retval );
}

/**
 * Is the logged-in user an admin for the current item?
 *
 * @since 1.5.0
 *
 * @return bool True if the current user is an admin for the current item,
 *              otherwise false.
 */
function sz_is_item_admin() {
	$sz     = sportszone();
	$retval = false;

	if ( isset( $sz->is_item_admin ) ) {
		$retval = $sz->is_item_admin;
	}

	/**
	 * Filters whether or not the logged-in user is an admin for the current item.
	 *
	 * @since 2.1.0
	 *
	 * @param bool $retval Whether or not the logged-in user is an admin.
	 */
	return (bool) apply_filters( 'sz_is_item_admin', $retval );
}

/**
 * Is the logged-in user a mod for the current item?
 *
 * @since 1.5.0
 *
 * @return bool True if the current user is a mod for the current item,
 *              otherwise false.
 */
function sz_is_item_mod() {
	$sz     = sportszone();
	$retval = false;

	if ( isset( $sz->is_item_mod ) ) {
		$retval = $sz->is_item_mod;
	}

	/**
	 * Filters whether or not the logged-in user is a mod for the current item.
	 *
	 * @since 2.1.0
	 *
	 * @param bool $retval Whether or not the logged-in user is a mod.
	 */
	return (bool) apply_filters( 'sz_is_item_mod', $retval );
}

/**
 * Is this a component directory page?
 *
 * @since 1.0.0
 *
 * @return bool True if the current page is a component directory, otherwise false.
 */
function sz_is_directory() {
	$sz     = sportszone();
	$retval = false;

	if ( isset( $sz->is_directory ) ) {
		$retval = $sz->is_directory;
	}

	/**
	 * Filters whether or not user is on a component directory page.
	 *
	 * @since 2.1.0
	 *
	 * @param bool $retval Whether or not user is on a component directory page.
	 */
	return (bool) apply_filters( 'sz_is_directory', $retval );
}

/**
 * Check to see if a component's URL should be in the root, not under a member page.
 *
 * - Yes ('groups' is root)    : http://example.com/groups/the-group
 * - No  ('groups' is not-root): http://example.com/members/andy/groups/the-group
 *
 * This function is on the chopping block. It's currently only used by a few
 * already deprecated functions.
 *
 * @since 1.5.0
 *
 * @param string $component_name Component name to check.
 *
 * @return bool True if root component, else false.
 */
function sz_is_root_component( $component_name = '' ) {
	$sz     = sportszone();
	$retval = false;

	// Default to the current component if none is passed.
	if ( empty( $component_name ) ) {
		$component_name = sz_current_component();
	}

	// Loop through active components and check for key/slug matches.
	if ( ! empty( $sz->active_components ) ) {
		foreach ( (array) $sz->active_components as $key => $slug ) {
			if ( ( $key === $component_name ) || ( $slug === $component_name ) ) {
				$retval = true;
				break;
			}
		}
	}

	/**
	 * Filters whether or not a component's URL should be in the root, not under a member page.
	 *
	 * @since 2.1.0
	 *
	 * @param bool $retval Whether or not URL should be in the root.
	 */
	return (bool) apply_filters( 'sz_is_root_component', $retval );
}

/**
 * Check if the specified SportsZone component directory is set to be the front page.
 *
 * Corresponds to the setting in wp-admin's Settings > Reading screen.
 *
 * @since 1.5.0
 *
 * @global int $current_blog WordPress global for the current blog.
 *
 * @param string $component Optional. Name of the component to check for.
 *                          Default: current component.
 * @return bool True if the specified component is set to be the site's front
 *              page, otherwise false.
 */
function sz_is_component_front_page( $component = '' ) {
	global $current_blog;

	$sz = sportszone();

	// Default to the current component if none is passed.
	if ( empty( $component ) ) {
		$component = sz_current_component();
	}

	// Get the path for the current blog/site.
	$path = is_main_site()
		? sz_core_get_site_path()
		: $current_blog->path;

	// Get the front page variables.
	$show_on_front = get_option( 'show_on_front' );
	$page_on_front = get_option( 'page_on_front' );

	if ( ( 'page' !== $show_on_front ) || empty( $component ) || empty( $sz->pages->{$component} ) || ( $_SERVER['REQUEST_URI'] !== $path ) ) {
		return false;
	}

	/**
	 * Filters whether or not the specified SportsZone component directory is set to be the front page.
	 *
	 * @since 1.5.0
	 *
	 * @param bool   $value     Whether or not the specified component directory is set as front page.
	 * @param string $component Current component being checked.
	 */
	return (bool) apply_filters( 'sz_is_component_front_page', ( $sz->pages->{$component}->id == $page_on_front ), $component );
}

/**
 * Is this a blog page, ie a non-BP page?
 *
 * You can tell if a page is displaying BP content by whether the
 * current_component has been defined.
 *
 * @since 1.0.0
 *
 * @return bool True if it's a non-BP page, false otherwise.
 */
function sz_is_blog_page() {

	$is_blog_page = false;

	// Generally, we can just check to see that there's no current component.
	// The one exception is single user home tabs, where $sz->current_component
	// is unset. Thus the addition of the sz_is_user() check.
	if ( ! sz_current_component() && ! sz_is_user() ) {
		$is_blog_page = true;
	}

	/**
	 * Filters whether or not current page is a blog page or not.
	 *
	 * @since 1.5.0
	 *
	 * @param bool $is_blog_page Whether or not current page is a blog page.
	 */
	return (bool) apply_filters( 'sz_is_blog_page', $is_blog_page );
}

/**
 * Is this a SportsZone component?
 *
 * You can tell if a page is displaying BP content by whether the
 * current_component has been defined.
 *
 * Generally, we can just check to see that there's no current component.
 * The one exception is single user home tabs, where $sz->current_component
 * is unset. Thus the addition of the sz_is_user() check.
 *
 * @since 1.7.0
 *
 * @return bool True if it's a SportsZone page, false otherwise.
 */
function is_sportszone() {
	$retval = (bool) ( sz_current_component() || sz_is_user() );

	/**
	 * Filters whether or not this is a SportsZone component.
	 *
	 * @since 1.7.0
	 *
	 * @param bool $retval Whether or not this is a SportsZone component.
	 */
	return apply_filters( 'is_sportszone', $retval );
}

/** Components ****************************************************************/

/**
 * Check whether a given component (or feature of a component) is active.
 *
 * @since 1.2.0 See r2539.
 * @since 2.3.0 Added $feature as a parameter.
 *
 * @param string $component The component name.
 * @param string $feature   The feature name.
 * @return bool
 */
function sz_is_active( $component = '', $feature = '' ) {
	$retval = false;

	// Default to the current component if none is passed.
	if ( empty( $component ) ) {
		$component = sz_current_component();
	}

	// Is component in either the active or required components arrays.
	if ( isset( sportszone()->active_components[ $component ] ) || isset( sportszone()->required_components[ $component ] ) ) {
		$retval = true;

		// Is feature active?
		if ( ! empty( $feature ) ) {
			// The xProfile component is specific.
			if ( 'xprofile' === $component ) {
				$component = 'profile';
			}

			if ( empty( sportszone()->$component->features ) || false === in_array( $feature, sportszone()->$component->features, true ) ) {
				$retval = false;
			}

			/**
			 * Filters whether or not a given feature for a component is active.
			 *
			 * This is a variable filter that is based on the component and feature
			 * that you are checking of active status of.
			 *
			 * @since 2.3.0
			 *
			 * @param bool $retval
			 */
			$retval = apply_filters( "sz_is_{$component}_{$feature}_active", $retval );
		}
	}

	/**
	 * Filters whether or not a given component has been activated by the admin.
	 *
	 * @since 2.1.0
	 *
	 * @param bool   $retval    Whether or not a given component has been activated by the admin.
	 * @param string $component Current component being checked.
	 */
	return apply_filters( 'sz_is_active', $retval, $component );
}

/**
 * Check whether the current page is part of the Members component.
 *
 * @since 1.5.0
 *
 * @return bool True if the current page is part of the Members component.
 */
function sz_is_members_component() {
	return (bool) sz_is_current_component( 'members' );
}

/**
 * Check whether the current page is part of the Profile component.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of the Profile component.
 */
function sz_is_profile_component() {
	return (bool) sz_is_current_component( 'xprofile' );
}

/**
 * Check whether the current page is part of the Activity component.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of the Activity component.
 */
function sz_is_activity_component() {
	return (bool) sz_is_current_component( 'activity' );
}

/**
 * Check whether the current page is part of the Blogs component.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of the Blogs component.
 */
function sz_is_blogs_component() {
	return (bool) ( is_multisite() && sz_is_current_component( 'blogs' ) );
}

/**
 * Check whether the current page is part of the Messages component.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of the Messages component.
 */
function sz_is_messages_component() {
	return (bool) sz_is_current_component( 'messages' );
}

/**
 * Check whether the current page is part of the Friends component.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of the Friends component.
 */
function sz_is_friends_component() {
	return (bool) sz_is_current_component( 'friends' );
}

/**
 * Check whether the current page is part of the Groups component.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of the Groups component.
 */
function sz_is_groups_component() {
	return (bool) sz_is_current_component( 'groups' );
}

/**
 * Check whether the current page is part of the Events component.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of the Events component.
 */
function sz_is_events_component() {
	return (bool) sz_is_current_component( 'events' );
}

/**
 * Check whether the current page is part of the Events component.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of the Events component.
 */
function sz_is_matches_component() {
	return (bool) sz_is_current_component( 'events' );
}

/**
 * Check whether the current page is part of the Forums component.
 *
 * @since 1.5.0
 * @since 3.0.0 Required for bbPress 2 integration.
 *
 * @return bool True if the current page is part of the Forums component.
 */
function sz_is_forums_component() {
	return (bool) sz_is_current_component( 'forums' );
}

/**
 * Check whether the current page is part of the Notifications component.
 *
 * @since 1.9.0
 *
 * @return bool True if the current page is part of the Notifications component.
 */
function sz_is_notifications_component() {
	return (bool) sz_is_current_component( 'notifications' );
}

/**
 * Check whether the current page is part of the Settings component.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of the Settings component.
 */
function sz_is_settings_component() {
	return (bool) sz_is_current_component( 'settings' );
}

/**
 * Is the current component an active core component?
 *
 * Use this function when you need to check if the current component is an
 * active core component of SportsZone. If the current component is inactive, it
 * will return false. If the current component is not part of SportsZone core,
 * it will return false. If the current component is active, and is part of
 * SportsZone core, it will return true.
 *
 * @since 1.7.0
 *
 * @return bool True if the current component is active and is one of BP's
 *              packaged components.
 */
function sz_is_current_component_core() {
	$retval = false;

	foreach ( sz_core_get_packaged_component_ids() as $active_component ) {
		if ( sz_is_current_component( $active_component ) ) {
			$retval = true;
			break;
		}
	}

	return $retval;
}

/** Activity ******************************************************************/

/**
 * Is the current page the activity directory?
 *
 * @since 2.0.0
 *
 * @return bool True if the current page is the activity directory.
 */
function sz_is_activity_directory() {
	if ( ! sz_displayed_user_id() && sz_is_activity_component() && ! sz_current_action() ) {
		return true;
	}

	return false;
}

/**
 * Is the current page a single activity item permalink?
 *
 * @since 1.5.0
 *
 * @return bool True if the current page is a single activity item permalink.
 */
function sz_is_single_activity() {
	return (bool) ( sz_is_activity_component() && is_numeric( sz_current_action() ) );
}

/** User **********************************************************************/

/**
 * Is the current page the members directory?
 *
 * @since 2.0.0
 *
 * @return bool True if the current page is the members directory.
 */
function sz_is_members_directory() {
	if ( ! sz_is_user() && sz_is_members_component() ) {
		return true;
	}

	return false;
}

/**
 * Is the current page part of the profile of the logged-in user?
 *
 * Will return true for any subpage of the logged-in user's profile, eg
 * http://example.com/members/joe/friends/.
 *
 * @since 1.2.0
 *
 * @return bool True if the current page is part of the profile of the logged-in user.
 */
function sz_is_my_profile() {
	if ( is_user_logged_in() && sz_loggedin_user_id() == sz_displayed_user_id() ) {
		$my_profile = true;
	} else {
		$my_profile = false;
	}

	/**
	 * Filters whether or not current page is part of the profile for the logged-in user.
	 *
	 * @since 1.2.4
	 *
	 * @param bool $my_profile Whether or not current page is part of the profile for the logged-in user.
	 */
	return apply_filters( 'sz_is_my_profile', $my_profile );
}

/**
 * Is the current page a user page?
 *
 * Will return true anytime there is a displayed user.
 *
 * @since 1.5.0
 *
 * @return bool True if the current page is a user page.
 */
function sz_is_user() {
	return (bool) sz_displayed_user_id();
}

/**
 * Is the current page a user custom front page?
 *
 * Will return true anytime there is a custom front page for the displayed user.
 *
 * @since 2.6.0
 *
 * @return bool True if the current page is a user custom front page.
 */
function sz_is_user_front() {
	return (bool) ( sz_is_user() && sz_is_current_component( 'front' ) );
}

/**
 * Is the current page a user's activity stream page?
 *
 * Eg http://example.com/members/joe/activity/ (or any subpages thereof).
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a user's activity stream page.
 */
function sz_is_user_activity() {
	return (bool) ( sz_is_user() && sz_is_activity_component() );
}

/**
 * Is the current page a user's Friends activity stream?
 *
 * Eg http://example.com/members/joe/friends/
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a user's Friends activity stream.
 */
function sz_is_user_friends_activity() {

	if ( ! sz_is_active( 'friends' ) ) {
		return false;
	}

	$slug = sz_get_friends_slug();

	if ( empty( $slug ) ) {
		$slug = 'friends';
	}

	if ( sz_is_user_activity() && sz_is_current_action( $slug ) ) {
		return true;
	}

	return false;
}

/**
 * Is the current page a user's Groups activity stream?
 *
 * Eg http://example.com/members/joe/groups/
 *
 * @since 1.5.0
 *
 * @return bool True if the current page is a user's Groups activity stream.
 */
function sz_is_user_groups_activity() {

	if ( ! sz_is_active( 'groups' ) ) {
		return false;
	}

	$slug = ( sz_get_groups_slug() )
		? sz_get_groups_slug()
		: 'groups';

	if ( sz_is_user_activity() && sz_is_current_action( $slug ) ) {
		return true;
	}

	return false;
}

/**
 * Is the current page a user's Events activity stream?
 *
 *
 * @since 1.5.0
 *
 * @return bool True if the current page is a user's Events activity stream.
 */
function sz_is_user_events_activity() {

	if ( ! sz_is_active( 'events' ) ) {
		return false;
	}

	$slug = ( sz_get_events_slug() )
		? sz_get_events_slug()
		: 'events';

	if ( sz_is_user_activity() && sz_is_current_action( $slug ) ) {
		return true;
	}

	return false;
}

/**
 * Is the current page part of a user's extended profile?
 *
 * Eg http://example.com/members/joe/profile/ (or a subpage thereof).
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of a user's extended profile.
 */
function sz_is_user_profile() {
	return (bool) ( sz_is_profile_component() || sz_is_current_component( 'profile' ) );
}

/**
 * Is the current page part of a user's profile editing section?
 *
 * Eg http://example.com/members/joe/profile/edit/ (or a subpage thereof).
 *
 * @since 1.5.0
 *
 * @return bool True if the current page is a user's profile edit page.
 */
function sz_is_user_profile_edit() {
	return (bool) ( sz_is_profile_component() && sz_is_current_action( 'edit' ) );
}

/**
 * Is the current page part of a user's profile avatar editing section?
 *
 * Eg http://example.com/members/joe/profile/change-avatar/ (or a subpage thereof).
 *
 * @since 1.5.0
 *
 * @return bool True if the current page is the user's avatar edit page.
 */
function sz_is_user_change_avatar() {
	return (bool) ( sz_is_profile_component() && sz_is_current_action( 'change-avatar' ) );
}

/**
 * Is the current page the a user's change cover image profile page?
 *
 * Eg http://example.com/members/joe/profile/change-cover-image/ (or a subpage thereof).
 *
 * @since 2.4.0
 *
 * @return bool True if the current page is a user's profile edit cover image page.
 */
function sz_is_user_change_cover_image() {
	return (bool) ( sz_is_profile_component() && sz_is_current_action( 'change-cover-image' ) );
}

/**
 * Is the current page part of a user's Groups page?
 *
 * Eg http://example.com/members/joe/groups/ (or a subpage thereof).
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a user's Groups page.
 */
function sz_is_user_groups() {
	return (bool) ( sz_is_user() && sz_is_groups_component() );
}

/**
 * Is the current page part of a user's Events page?
 *
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a user's Events page.
 */
function sz_is_user_events() {
	return (bool) ( sz_is_user() && sz_is_events_component() );
}

/**
 * Is the current page part of a user's Blogs page?
 *
 * Eg http://example.com/members/joe/blogs/ (or a subpage thereof).
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a user's Blogs page.
 */
function sz_is_user_blogs() {
	return (bool) ( sz_is_user() && sz_is_blogs_component() );
}

/**
 * Is the current page a user's Recent Blog Posts page?
 *
 * Eg http://example.com/members/joe/blogs/recent-posts/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a user's Recent Blog Posts page.
 */
function sz_is_user_recent_posts() {
	return (bool) ( sz_is_user_blogs() && sz_is_current_action( 'recent-posts' ) );
}

/**
 * Is the current page a user's Recent Blog Comments page?
 *
 * Eg http://example.com/members/joe/blogs/recent-comments/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a user's Recent Blog Comments page.
 */
function sz_is_user_recent_commments() {
	return (bool) ( sz_is_user_blogs() && sz_is_current_action( 'recent-comments' ) );
}

/**
 * Is the current page a user's Friends page?
 *
 * Eg http://example.com/members/joe/blogs/friends/ (or a subpage thereof).
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a user's Friends page.
 */
function sz_is_user_friends() {
	return (bool) ( sz_is_user() && sz_is_friends_component() );
}

/**
 * Is the current page a user's Friend Requests page?
 *
 * Eg http://example.com/members/joe/friends/requests/.
 *
 * @since 1.5.0
 *
 * @return bool True if the current page is a user's Friends Requests page.
 */
function sz_is_user_friend_requests() {
	return (bool) ( sz_is_user_friends() && sz_is_current_action( 'requests' ) );
}

/**
 * Is this a user's notifications page?
 *
 * Eg http://example.com/members/joe/notifications/ (or a subpage thereof).
 *
 * @since 1.9.0
 *
 * @return bool True if the current page is a user's Notifications page.
 */
function sz_is_user_notifications() {
	return (bool) ( sz_is_user() && sz_is_notifications_component() );
}

/**
 * Is this a user's settings page?
 *
 * Eg http://example.com/members/joe/settings/ (or a subpage thereof).
 *
 * @since 1.5.0
 *
 * @return bool True if the current page is a user's Settings page.
 */
function sz_is_user_settings() {
	return (bool) ( sz_is_user() && sz_is_settings_component() );
}

/**
 * Is this a user's General Settings page?
 *
 * Eg http://example.com/members/joe/settings/general/.
 *
 * @since 1.5.0
 *
 * @return bool True if the current page is a user's General Settings page.
 */
function sz_is_user_settings_general() {
	return (bool) ( sz_is_user_settings() && sz_is_current_action( 'general' ) );
}

/**
 * Is this a user's Notification Settings page?
 *
 * Eg http://example.com/members/joe/settings/notifications/.
 *
 * @since 1.5.0
 *
 * @return bool True if the current page is a user's Notification Settings page.
 */
function sz_is_user_settings_notifications() {
	return (bool) ( sz_is_user_settings() && sz_is_current_action( 'notifications' ) );
}

/**
 * Is this a user's Account Deletion page?
 *
 * Eg http://example.com/members/joe/settings/delete-account/.
 *
 * @since 1.5.0
 *
 * @return bool True if the current page is a user's Delete Account page.
 */
function sz_is_user_settings_account_delete() {
	return (bool) ( sz_is_user_settings() && sz_is_current_action( 'delete-account' ) );
}

/**
 * Is this a user's profile settings?
 *
 * Eg http://example.com/members/joe/settings/profile/.
 *
 * @since 2.0.0
 *
 * @return bool True if the current page is a user's Profile Settings page.
 */
function sz_is_user_settings_profile() {
	return (bool) ( sz_is_user_settings() && sz_is_current_action( 'profile' ) );
}

/** Groups ********************************************************************/

/**
 * Is the current page the groups directory?
 *
 * @since 2.0.0
 *
 * @return bool True if the current page is the groups directory.
 */
function sz_is_groups_directory() {
	if ( sz_is_groups_component() && ! sz_is_group() && ( ! sz_current_action() || ( sz_action_variable() && sz_is_current_action( sz_get_groups_group_type_base() ) ) ) ) {
		return true;
	}

	return false;
}

/**
 * Does the current page belong to a single group?
 *
 * Will return true for any subpage of a single group.
 *
 * @since 1.2.0
 *
 * @return bool True if the current page is part of a single group.
 */
function sz_is_group() {
	$retval = sz_is_active( 'groups' );

	if ( ! empty( $retval ) ) {
		$retval = sz_is_groups_component() && groups_get_current_group();
	}

	return (bool) $retval;
}

/**
 * Is the current page a single group's home page?
 *
 * URL will vary depending on which group tab is set to be the "home". By
 * default, it's the group's recent activity.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a single group's home page.
 */
function sz_is_group_home() {
	if ( sz_is_single_item() && sz_is_groups_component() && ( ! sz_current_action() || sz_is_current_action( 'home' ) ) ) {
		return true;
	}

	return false;
}

/**
 * Is the current page part of the group creation process?
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of the group creation process.
 */
function sz_is_group_create() {
	return (bool) ( sz_is_groups_component() && sz_is_current_action( 'create' ) );
}

/**
 * Is the current page part of a single group's admin screens?
 *
 * Eg http://example.com/groups/mygroup/admin/settings/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of a single group's admin.
 */
function sz_is_group_admin_page() {
	return (bool) ( sz_is_single_item() && sz_is_groups_component() && sz_is_current_action( 'admin' ) );
}

/**
 * Is the current page a group's activity page?
 *
 * @since 1.2.1
 *
 * @return bool True if the current page is a group's activity page.
 */
function sz_is_group_activity() {
	$retval = false;

	if ( sz_is_single_item() && sz_is_groups_component() && sz_is_current_action( 'activity' ) ) {
		$retval = true;
	}

	if ( sz_is_group_home() && sz_is_active( 'activity' ) && ! sz_is_group_custom_front() ) {
		$retval = true;
	}

	return $retval;
}

/**
 * Is the current page a group forum topic?
 *
 * @since 1.1.0
 * @since 3.0.0 Required for bbPress 2 integration.
 *
 * @return bool True if the current page is part of a group forum topic.
 */
function sz_is_group_forum_topic() {
	return (bool) ( sz_is_single_item() && sz_is_groups_component() && sz_is_current_action( 'forum' ) && sz_is_action_variable( 'topic', 0 ) );
}

/**
 * Is the current page a group forum topic edit page?
 *
 * @since 1.2.0
 * @since 3.0.0 Required for bbPress 2 integration.
 *
 * @return bool True if the current page is part of a group forum topic edit page.
 */
function sz_is_group_forum_topic_edit() {
	return (bool) ( sz_is_single_item() && sz_is_groups_component() && sz_is_current_action( 'forum' ) && sz_is_action_variable( 'topic', 0 ) && sz_is_action_variable( 'edit', 2 ) );
}

/**
 * Is the current page a group's Members page?
 *
 * Eg http://example.com/groups/mygroup/members/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of a group's Members page.
 */
function sz_is_group_members() {
	$retval = false;

	if ( sz_is_single_item() && sz_is_groups_component() && sz_is_current_action( 'members' ) ) {
		$retval = true;
	}

	if ( sz_is_group_home() && ! sz_is_active( 'activity' ) && ! sz_is_group_custom_front() ) {
		$retval = true;
	}

	return $retval;
}

/**
 * Is the current page a group's Invites page?
 *
 * Eg http://example.com/groups/mygroup/send-invites/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a group's Send Invites page.
 */
function sz_is_group_invites() {
	return (bool) ( sz_is_groups_component() && sz_is_current_action( 'send-invites' ) );
}

/**
 * Is the current page a group's Request Membership page?
 *
 * Eg http://example.com/groups/mygroup/request-membership/.
 *
 * @since 1.2.0
 *
 * @return bool True if the current page is a group's Request Membership page.
 */
function sz_is_group_membership_request() {
	return (bool) ( sz_is_groups_component() && sz_is_current_action( 'request-membership' ) );
}

/**
 * Is the current page a leave group attempt?
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a Leave Group attempt.
 */
function sz_is_group_leave() {
	return (bool) ( sz_is_groups_component() && sz_is_single_item() && sz_is_current_action( 'leave-group' ) );
}

/**
 * Is the current page part of a single group?
 *
 * Not currently used by SportsZone.
 *
 * @todo How is this functionally different from sz_is_group()?
 *
 * @return bool True if the current page is part of a single group.
 */
function sz_is_group_single() {
	return (bool) ( sz_is_groups_component() && sz_is_single_item() );
}

/**
 * Is the current group page a custom front?
 *
 * @since 2.4.0
 *
 * @return bool True if the current group page is a custom front.
 */
function sz_is_group_custom_front() {
	$sz = sportszone();
	return (bool) sz_is_group_home() && ! empty( $sz->groups->current_group->front_template );
}

/**
 * Is the current page the Create a Blog page?
 *
 * Eg http://example.com/sites/create/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is the Create a Blog page.
 */
function sz_is_create_blog() {
	return (bool) ( sz_is_blogs_component() && sz_is_current_action( 'create' ) );
}

/**
 * Is the current page the blogs directory ?
 *
 * @since 2.0.0
 *
 * @return bool True if the current page is the blogs directory.
 */
function sz_is_blogs_directory() {
	if ( is_multisite() && sz_is_blogs_component() && ! sz_current_action() ) {
		return true;
	}

	return false;
}

/** Events ********************************************************************/

/**
 * Is the current page the events directory?
 *
 * @since 2.0.0
 *
 * @return bool True if the current page is the events directory.
 */
function sz_is_events_directory() {
	if ( sz_is_events_component() && ! sz_is_event() && ( ! sz_current_action() || ( sz_action_variable() && sz_is_current_action( sz_get_events_event_type_base() ) ) ) ) {
		return true;
	}

	return false;
}

/**
 * Does the current page belong to a single event?
 *
 * Will return true for any subpage of a single event.
 *
 * @since 1.2.0
 *
 * @return bool True if the current page is part of a single event.
 */
function sz_is_event() {
	global $post;
	$retval = sz_is_active( 'events' );
	
	if ( ! empty( $retval ) ) {
		$retval = sz_is_events_component() && events_get_current_event();
	}
	/*if(get_post_type( $post ) == 'sz_match'){
		$retval = true;
	}*/

	return (bool) $retval;
}

/**
 * Is the current page the matches directory?
 *
 * @since 2.0.0
 *
 * @return bool True if the current page is the events directory.
 */
function sz_is_matches_directory() {
	if ( sz_is_matches_component() && ! sz_is_match() && ( ! sz_current_action() || ( sz_action_variable() && sz_is_current_action( sz_get_matches_match_type_base() ) ) ) ) {
		return true;
	}

	return false;
}
/**
 * Does the current page belong to a single event?
 *
 * Will return true for any subpage of a single event.
 *
 * @since 1.2.0
 *
 * @return bool True if the current page is part of a single event.
 */
function sz_is_match() {
	$retval = sz_is_active( 'events' );

	if ( ! empty( $retval ) ) {
		$retval = sz_is_events_component() && events_get_current_event();
	}

	return (bool) $retval;
}

/**
 * Is the current page a single event's home page?
 *
 * URL will vary depending on which event tab is set to be the "home". By
 * default, it's the event's recent activity.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a single event's home page.
 */
function sz_is_event_home() {
	if ( sz_is_single_item() && sz_is_events_component() && ( ! sz_current_action() || sz_is_current_action( 'home' ) ) ) {
		return true;
	}

	return false;
}

/**
 * Is the current page a single event's home page?
 *
 * URL will vary depending on which event tab is set to be the "home". By
 * default, it's the event's recent activity.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a single event's home page.
 */
function sz_is_match_home() {
	if ( sz_is_single_item() && sz_is_matches_component() && ( ! sz_current_action() || sz_is_current_action( 'home' ) ) ) {
		return true;
	}

	return false;
}

/**
 * Is the current page part of the event creation process?
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of the event creation process.
 */
function sz_is_event_create() {
	return (bool) ( sz_is_events_component() && sz_is_current_action( 'create' ) );
}

/**
 * Is the current page part of a single event's admin screens?
 *
 * Eg http://example.com/events/myevent/admin/settings/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of a single event's admin.
 */
function sz_is_event_admin_page() {
	return (bool) ( sz_is_single_item() && sz_is_events_component() && sz_is_current_action( 'admin' ) );
}

/**
 * Is the current page a event's activity page?
 *
 * @since 1.2.1
 *
 * @return bool True if the current page is a event's activity page.
 */
function sz_is_match_activity() {
	$retval = false;

	if ( sz_is_single_item() && sz_is_events_component() && sz_is_current_action( 'activity' ) ) {
		$retval = true;
	}

	if ( sz_is_event_home() && sz_is_active( 'activity' ) && ! sz_is_event_custom_front() ) {
		$retval = true;
	}

	return $retval;
}

/**
 * Is the current page a event's activity page?
 *
 * @since 1.2.1
 *
 * @return bool True if the current page is a event's activity page.
 */
function sz_is_event_activity() {
	$retval = false;

	if ( sz_is_single_item() && sz_is_events_component() && sz_is_current_action( 'activity' ) ) {
		$retval = true;
	}

	if ( sz_is_event_home() && sz_is_active( 'activity' ) && ! sz_is_event_custom_front() ) {
		$retval = true;
	}

	return $retval;
}

/**
 * Is the current page a event forum topic?
 *
 * @since 1.1.0
 * @since 3.0.0 Required for bbPress 2 integration.
 *
 * @return bool True if the current page is part of a event forum topic.
 */
function sz_is_event_forum_topic() {
	return (bool) ( sz_is_single_item() && sz_is_events_component() && sz_is_current_action( 'forum' ) && sz_is_action_variable( 'topic', 0 ) );
}

/**
 * Is the current page a event forum topic edit page?
 *
 * @since 1.2.0
 * @since 3.0.0 Required for bbPress 2 integration.
 *
 * @return bool True if the current page is part of a event forum topic edit page.
 */
function sz_is_event_forum_topic_edit() {
	return (bool) ( sz_is_single_item() && sz_is_events_component() && sz_is_current_action( 'forum' ) && sz_is_action_variable( 'topic', 0 ) && sz_is_action_variable( 'edit', 2 ) );
}

/**
 * Is the current page a event's Members page?
 *
 * Eg http://example.com/events/myevent/members/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of a event's Members page.
 */
function sz_is_event_members() {
	$retval = false;

	if ( sz_is_single_item() && sz_is_events_component() && sz_is_current_action( 'members' ) ) {
		$retval = true;
	}

	if ( sz_is_event_home() && ! sz_is_active( 'activity' ) && ! sz_is_event_custom_front() ) {
		$retval = true;
	}

	return $retval;
}

/**
 * Is the current page a event's Members page?
 *
 * Eg http://example.com/events/myevent/members/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is part of a event's Members page.
 */
function sz_is_match_members() {
	$retval = false;

	if ( sz_is_single_item() && sz_is_matches_component() && sz_is_current_action( 'members' ) ) {
		$retval = true;
	}

	if ( sz_is_match_home() && ! sz_is_active( 'activity' ) && ! sz_is_match_custom_front() ) {
		$retval = true;
	}

	return $retval;
}

/**
 * Is the current page a event's Invites page?
 *
 * Eg http://example.com/events/myevent/send-invites/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a event's Send Invites page.
 */
function sz_is_event_invites() {
	return (bool) ( sz_is_events_component() && sz_is_current_action( 'send-invites' ) );
}

/**
 * Is the current page a match's Invites page?
 *
 * Eg http://example.com/events/myevent/send-invites/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a match's Send Invites page.
 */
function sz_is_match_invites() {
	return (bool) ( sz_is_matches_component() && sz_is_current_action( 'send-invites' ) );
}

/**
 * Is the current page a event's Request Membership page?
 *
 * Eg http://example.com/events/myevent/request-membership/.
 *
 * @since 1.2.0
 *
 * @return bool True if the current page is a event's Request Membership page.
 */
function sz_is_event_membership_request() {
	return (bool) ( sz_is_events_component() && sz_is_current_action( 'request-membership' ) );
}

/**
 * Is the current page a event's Request Membership page?
 *
 * Eg http://example.com/events/myevent/request-membership/.
 *
 * @since 1.2.0
 *
 * @return bool True if the current page is a event's Request Membership page.
 */
function sz_is_event_paid_team_request() {
	return (bool) ( sz_is_events_component() && sz_is_current_action( 'select-team' ) );
}

/**
 * Is the current page a leave event attempt?
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a Leave Group attempt.
 */
function sz_is_event_leave() {
	return (bool) ( sz_is_events_component() && sz_is_single_item() && sz_is_current_action( 'leave-event' ) );
}

/**
 * Is the current page part of a single event?
 *
 * Not currently used by SportsZone.
 *
 * @todo How is this functionally different from sz_is_event()?
 *
 * @return bool True if the current page is part of a single event.
 */
function sz_is_event_single() {
	return (bool) ( sz_is_events_component() && sz_is_single_item() );
}

/**
 * Is the current event page a custom front?
 *
 * @since 2.4.0
 *
 * @return bool True if the current event page is a custom front.
 */
function sz_is_event_custom_front() {
	$sz = sportszone();
	return (bool) sz_is_event_home() && ! empty( $sz->events->current_event->front_template );
}

/** Messages ******************************************************************/

/**
 * Is the current page part of a user's Messages pages?
 *
 * Eg http://example.com/members/joe/messages/ (or a subpage thereof).
 *
 * @since 1.2.0
 *
 * @return bool True if the current page is part of a user's Messages pages.
 */
function sz_is_user_messages() {
	return (bool) ( sz_is_user() && sz_is_messages_component() );
}

/**
 * Is the current page a user's Messages Inbox?
 *
 * Eg http://example.com/members/joe/messages/inbox/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a user's Messages Inbox.
 */
function sz_is_messages_inbox() {
	if ( sz_is_user_messages() && ( ! sz_current_action() || sz_is_current_action( 'inbox' ) ) ) {
		return true;
	}

	return false;
}

/**
 * Is the current page a user's Messages Sentbox?
 *
 * Eg http://example.com/members/joe/messages/sentbox/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a user's Messages Sentbox.
 */
function sz_is_messages_sentbox() {
	return (bool) ( sz_is_user_messages() && sz_is_current_action( 'sentbox' ) );
}

/**
 * Is the current page a user's Messages Compose screen??
 *
 * Eg http://example.com/members/joe/messages/compose/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is a user's Messages Compose screen.
 */
function sz_is_messages_compose_screen() {
	return (bool) ( sz_is_user_messages() && sz_is_current_action( 'compose' ) );
}

/**
 * Is the current page the Notices screen?
 *
 * Eg http://example.com/members/joe/messages/notices/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is the Notices screen.
 */
function sz_is_notices() {
	return (bool) ( sz_is_user_messages() && sz_is_current_action( 'notices' ) );
}

/**
 * Is the current page a single Messages conversation thread?
 *
 * @since 1.6.0
 *
 * @return bool True if the current page a single Messages conversation thread?
 */
function sz_is_messages_conversation() {
	return (bool) ( sz_is_user_messages() && ( sz_is_current_action( 'view' ) ) );
}

/**
 * Not currently used by SportsZone.
 *
 * @param string $component Current component to check for.
 * @param string $callback  Callback to invoke.
 * @return bool
 */
function sz_is_single( $component, $callback ) {
	return (bool) ( sz_is_current_component( $component ) && ( true === call_user_func( $callback ) ) );
}

/** Registration **************************************************************/

/**
 * Is the current page the Activate page?
 *
 * Eg http://example.com/activate/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is the Activate page.
 */
function sz_is_activation_page() {
	return (bool) sz_is_current_component( 'activate' );
}

/**
 * Is the current page the Register page?
 *
 * Eg http://example.com/register/.
 *
 * @since 1.1.0
 *
 * @return bool True if the current page is the Register page.
 */
function sz_is_register_page() {
	return (bool) sz_is_current_component( 'register' );
}

/**
 * Get the title parts of the SportsZone displayed page
 *
 * @since 2.4.3
 *
 * @param string $seplocation Location for the separator.
 * @return array the title parts
 */
function sz_get_title_parts( $seplocation = 'right' ) {
	$sz = sportszone();

	// Defaults to an empty array.
	$sz_title_parts = array();

	// If this is not a BP page, return the empty array.
	if ( sz_is_blog_page() ) {
		return $sz_title_parts;
	}

	// If this is a 404, return the empty array.
	if ( is_404() ) {
		return $sz_title_parts;
	}

	// If this is the front page of the site, return the empty array.
	if ( is_front_page() || is_home() ) {
		return $sz_title_parts;
	}

	// Return the empty array if not a SportsZone page.
	if ( ! is_sportszone() ) {
		return $sz_title_parts;
	}

	// Now we can build the BP Title Parts
	// Is there a displayed user, and do they have a name?
	$displayed_user_name = sz_get_displayed_user_fullname();

	// Displayed user.
	if ( ! empty( $displayed_user_name ) && ! is_404() ) {

		// Get the component's ID to try and get its name.
		$component_id = $component_name = sz_current_component();

		// Set empty subnav name.
		$component_subnav_name = '';

		if ( ! empty( $sz->members->nav ) ) {
			$primary_nav_item = $sz->members->nav->get_primary( array( 'slug' => $component_id ), false );
			$primary_nav_item = reset( $primary_nav_item );
		}

		// Use the component nav name.
		if ( ! empty( $primary_nav_item->name ) ) {
			$component_name = _sz_strip_spans_from_title( $primary_nav_item->name );

		// Fall back on the component ID.
		} elseif ( ! empty( $sz->{$component_id}->id ) ) {
			$component_name = ucwords( $sz->{$component_id}->id );
		}

		if ( ! empty( $sz->members->nav ) ) {
			$secondary_nav_item = $sz->members->nav->get_secondary( array(
				'parent_slug' => $component_id,
				'slug'        => sz_current_action()
			), false );

			if ( $secondary_nav_item ) {
				$secondary_nav_item = reset( $secondary_nav_item );
			}
		}

		// Append action name if we're on a member component sub-page.
		if ( ! empty( $secondary_nav_item->name ) && ! empty( $sz->canonical_stack['action'] ) ) {
			$component_subnav_name = $secondary_nav_item->name;
		}

		// If on the user profile's landing page, just use the fullname.
		if ( sz_is_current_component( $sz->default_component ) && ( sz_get_requested_url() === sz_displayed_user_domain() ) ) {
			$sz_title_parts[] = $displayed_user_name;

		// Use component name on member pages.
		} else {
			$sz_title_parts = array_merge( $sz_title_parts, array_map( 'strip_tags', array(
				$displayed_user_name,
				$component_name,
			) ) );

			// If we have a subnav name, add it separately for localization.
			if ( ! empty( $component_subnav_name ) ) {
				$sz_title_parts[] = strip_tags( $component_subnav_name );
			}
		}

	// A single item from a component other than Members.
	} elseif ( sz_is_single_item() ) {
		$component_id = sz_current_component();

		if ( ! empty( $sz->{$component_id}->nav ) ) {
			$secondary_nav_item = $sz->{$component_id}->nav->get_secondary( array(
				'parent_slug' => sz_current_item(),
				'slug'        => sz_current_action()
			), false );

			if ( $secondary_nav_item ) {
				$secondary_nav_item = reset( $secondary_nav_item );
			}
		}

		$single_item_subnav = '';

		if ( ! empty( $secondary_nav_item->name ) ) {
			$single_item_subnav = $secondary_nav_item->name;
		}

		$sz_title_parts = array( $sz->sz_options_title, $single_item_subnav );

	// An index or directory.
	} elseif ( sz_is_directory() ) {
		$current_component = sz_current_component();

		// No current component (when does this happen?).
		$sz_title_parts = array( _x( 'Directory', 'component directory title', 'sportszone' ) );

		if ( ! empty( $current_component ) ) {
			$sz_title_parts = array( sz_get_directory_title( $current_component ) );
		}

	// Sign up page.
	} elseif ( sz_is_register_page() ) {
		$sz_title_parts = array( __( 'Create an Account', 'sportszone' ) );

	// Activation page.
	} elseif ( sz_is_activation_page() ) {
		$sz_title_parts = array( __( 'Activate Your Account', 'sportszone' ) );

	// Group creation page.
	} elseif ( sz_is_group_create() ) {
		$sz_title_parts = array( __( 'Create a Group', 'sportszone' ) );
		
	// Event creation page.
	} elseif ( sz_is_event_create() ) {
		$sz_title_parts = array( __( 'Create a Event', 'sportszone' ) );

	// Blog creation page.
	} elseif ( sz_is_create_blog() ) {
		$sz_title_parts = array( __( 'Create a Site', 'sportszone' ) );
	}

	// Strip spans.
	$sz_title_parts = array_map( '_sz_strip_spans_from_title', $sz_title_parts );

	// Sep on right, so reverse the order.
	if ( 'right' === $seplocation ) {
		$sz_title_parts = array_reverse( $sz_title_parts );
	}

	/**
	 * Filter SportsZone title parts before joining.
	 *
	 * @since 2.4.3
	 *
	 * @param array $sz_title_parts Current SportsZone title parts.
	 * @return array
	 */
	return (array) apply_filters( 'sz_get_title_parts', $sz_title_parts );
}

/**
 * Customize the body class, according to the currently displayed BP content.
 *
 * @since 1.1.0
 */
function sz_the_body_class() {
	echo sz_get_the_body_class();
}
	/**
	 * Customize the body class, according to the currently displayed BP content.
	 *
	 * Uses the above is_() functions to output a body class for each scenario.
	 *
	 * @since 1.1.0
	 *
	 * @param array      $wp_classes     The body classes coming from WP.
	 * @param array|bool $custom_classes Classes that were passed to get_body_class().
	 * @return array $classes The BP-adjusted body classes.
	 */
	function sz_get_the_body_class( $wp_classes = array(), $custom_classes = false ) {

		$sz_classes = array();

		/* Pages *************************************************************/

		if ( is_front_page() ) {
			$sz_classes[] = 'home-page';
		}

		if ( sz_is_directory() ) {
			$sz_classes[] = 'directory';
		}

		if ( sz_is_single_item() ) {
			$sz_classes[] = 'single-item';
		}

		/* Components ********************************************************/

		if ( ! sz_is_blog_page() ) {
			if ( sz_is_user_profile() )  {
				$sz_classes[] = 'xprofile';
			}

			if ( sz_is_activity_component() ) {
				$sz_classes[] = 'activity';
			}

			if ( sz_is_blogs_component() ) {
				$sz_classes[] = 'blogs';
			}

			if ( sz_is_messages_component() ) {
				$sz_classes[] = 'messages';
			}

			if ( sz_is_friends_component() ) {
				$sz_classes[] = 'friends';
			}

			if ( sz_is_groups_component() ) {
				$sz_classes[] = 'groups';
			}

			if ( sz_is_settings_component()  ) {
				$sz_classes[] = 'settings';
			}
		}

		/* User **************************************************************/

		if ( sz_is_user() ) {
			$sz_classes[] = 'sz-user';

			// Add current user member types.
			if ( $member_types = sz_get_member_type( sz_displayed_user_id(), false ) ) {
				foreach( $member_types as $member_type ) {
					$sz_classes[] = sprintf( 'member-type-%s', esc_attr( $member_type ) );
				}
			}
		}

		if ( ! sz_is_directory() ) {
			if ( sz_is_user_blogs() ) {
				$sz_classes[] = 'my-blogs';
			}

			if ( sz_is_user_groups() ) {
				$sz_classes[] = 'my-groups';
			}
			
			if ( sz_is_user_events() ) {
				$sz_classes[] = 'my-events';
			}

			if ( sz_is_user_activity() ) {
				$sz_classes[] = 'my-activity';
			}
		} else {
			if ( sz_get_current_member_type() ) {
				$sz_classes[] = 'type';
			}
		}

		if ( sz_is_my_profile() ) {
			$sz_classes[] = 'my-account';
		}

		if ( sz_is_user_profile() ) {
			$sz_classes[] = 'my-profile';
		}

		if ( sz_is_user_friends() ) {
			$sz_classes[] = 'my-friends';
		}

		if ( sz_is_user_messages() ) {
			$sz_classes[] = 'my-messages';
		}

		if ( sz_is_user_recent_commments() ) {
			$sz_classes[] = 'recent-comments';
		}

		if ( sz_is_user_recent_posts() ) {
			$sz_classes[] = 'recent-posts';
		}

		if ( sz_is_user_change_avatar() ) {
			$sz_classes[] = 'change-avatar';
		}

		if ( sz_is_user_profile_edit() ) {
			$sz_classes[] = 'profile-edit';
		}

		if ( sz_is_user_friends_activity() ) {
			$sz_classes[] = 'friends-activity';
		}

		if ( sz_is_user_groups_activity() ) {
			$sz_classes[] = 'groups-activity';
		}
		
		if ( sz_is_user_events_activity() ) {
			$sz_classes[] = 'events-activity';
		}

		/* Messages **********************************************************/

		if ( sz_is_messages_inbox() ) {
			$sz_classes[] = 'inbox';
		}

		if ( sz_is_messages_sentbox() ) {
			$sz_classes[] = 'sentbox';
		}

		if ( sz_is_messages_compose_screen() ) {
			$sz_classes[] = 'compose';
		}

		if ( sz_is_notices() ) {
			$sz_classes[] = 'notices';
		}

		if ( sz_is_user_friend_requests() ) {
			$sz_classes[] = 'friend-requests';
		}

		if ( sz_is_create_blog() ) {
			$sz_classes[] = 'create-blog';
		}

		/* Groups ************************************************************/

		if ( sz_is_group() ) {
			$sz_classes[] = 'group-' . groups_get_current_group()->slug;

			// Add current group types.
			if ( $group_types = sz_groups_get_group_type( sz_get_current_group_id(), false ) ) {
				foreach ( $group_types as $group_type ) {
					$sz_classes[] = sprintf( 'group-type-%s', esc_attr( $group_type ) );
				}
			}
		}

		if ( sz_is_group_leave() ) {
			$sz_classes[] = 'leave-group';
		}

		if ( sz_is_group_invites() ) {
			$sz_classes[] = 'group-invites';
		}

		if ( sz_is_group_members() ) {
			$sz_classes[] = 'group-members';
		}

		if ( sz_is_group_admin_page() ) {
			$sz_classes[] = 'group-admin';
			$sz_classes[] = sz_get_group_current_admin_tab();
		}

		if ( sz_is_group_create() ) {
			$sz_classes[] = 'group-create';
			$sz_classes[] = sz_get_groups_current_create_step();
		}

		if ( sz_is_group_home() ) {
			$sz_classes[] = 'group-home';
		}

		if ( sz_is_single_activity() ) {
			$sz_classes[] = 'activity-permalink';
		}
		
		/* Events ************************************************************/

		if ( sz_is_event() ) {
			$sz_classes[] = 'event-' . events_get_current_event()->slug;

			// Add current event types.
			if ( $event_types = sz_events_get_event_type( sz_get_current_event_id(), false ) ) {
				foreach ( $event_types as $event_type ) {
					$sz_classes[] = sprintf( 'event-type-%s', esc_attr( $event_type ) );
				}
			}
		}

		if ( sz_is_event_leave() ) {
			$sz_classes[] = 'leave-event';
		}

		if ( sz_is_event_invites() ) {
			$sz_classes[] = 'event-invites';
		}

		if ( sz_is_event_members() ) {
			$sz_classes[] = 'event-members';
		}

		if ( sz_is_event_admin_page() ) {
			$sz_classes[] = 'event-admin';
			$sz_classes[] = sz_get_event_current_admin_tab();
		}

		if ( sz_is_event_create() ) {
			$sz_classes[] = 'event-create';
			$sz_classes[] = sz_get_events_current_create_step();
		}

		if ( sz_is_event_home() ) {
			$sz_classes[] = 'event-home';
		}

		if ( sz_is_single_activity() ) {
			$sz_classes[] = 'activity-permalink';
		}
		
		/* Matches ************************************************************/

		

		/* Registration ******************************************************/

		if ( sz_is_register_page() ) {
			$sz_classes[] = 'registration';
		}

		if ( sz_is_activation_page() ) {
			$sz_classes[] = 'activation';
		}

		/* Current Component & Action ****************************************/

		if ( ! sz_is_blog_page() ) {
			$sz_classes[] = sz_current_component();
			$sz_classes[] = sz_current_action();
		}

		/* Clean up ***********************************************************/

		// Add SportsZone class if we are within a SportsZone page.
		if ( ! sz_is_blog_page() ) {
			$sz_classes[] = 'sportszone';
		}

		// Add the theme name/id to the body classes
		$sz_classes[] = 'sz-' . sz_get_theme_compat_id();

		// Merge WP classes with SportsZone classes and remove any duplicates.
		$classes = array_unique( array_merge( (array) $sz_classes, (array) $wp_classes ) );

		/**
		 * Filters the SportsZone classes to be added to body_class()
		 *
		 * @since 1.1.0
		 *
		 * @param array $classes        Array of body classes to add.
		 * @param array $sz_classes     Array of SportsZone-based classes.
		 * @param array $wp_classes     Array of WordPress-based classes.
		 * @param array $custom_classes Array of classes that were passed to get_body_class().
		 */
		return apply_filters( 'sz_get_the_body_class', $classes, $sz_classes, $wp_classes, $custom_classes );
	}
	add_filter( 'body_class', 'sz_get_the_body_class', 10, 2 );

/**
 * Customizes the post CSS class according to SportsZone content.
 *
 * Hooked to the 'post_class' filter.
 *
 * @since 2.1.0
 *
 * @param array $wp_classes The post classes coming from WordPress.
 * @return array
 */
function sz_get_the_post_class( $wp_classes = array() ) {
	// Don't do anything if we're not on a BP page.
	if ( ! is_sportszone() ) {
		return $wp_classes;
	}

	$sz_classes = array();

	if ( sz_is_user() || sz_is_single_activity() ) {
		$sz_classes[] = 'sz_members';

	} elseif ( sz_is_group() ) {
		$sz_classes[] = 'sz_group';

	} elseif ( sz_is_event() ) {
		$sz_classes[] = 'sz_event';

	} elseif ( sz_is_activity_component() ) {
		$sz_classes[] = 'sz_activity';

	} elseif ( sz_is_blogs_component() ) {
		$sz_classes[] = 'sz_blogs';

	} elseif ( sz_is_register_page() ) {
		$sz_classes[] = 'sz_register';

	} elseif ( sz_is_activation_page() ) {
		$sz_classes[] = 'sz_activate';
	}

	if ( empty( $sz_classes ) ) {
		return $wp_classes;
	}

	// Emulate post type css class.
	foreach ( $sz_classes as $sz_class ) {
		$sz_classes[] = "type-{$sz_class}";
	}

	// Okay let's merge!
	return array_unique( array_merge( $sz_classes, $wp_classes ) );
}
add_filter( 'post_class', 'sz_get_the_post_class' );

/**
 * Sort SportsZone nav menu items by their position property.
 *
 * This is an internal convenience function and it will probably be removed in
 * a later release. Do not use.
 *
 * @access private
 * @since 1.7.0
 *
 * @param array $a First item.
 * @param array $b Second item.
 * @return int Returns an integer less than, equal to, or greater than zero if
 *             the first argument is considered to be respectively less than,
 *             equal to, or greater than the second.
 */
function _sz_nav_menu_sort( $a, $b ) {
	if ( $a['position'] == $b['position'] ) {
		return 0;
	} elseif ( $a['position'] < $b['position'] ) {
		return -1;
	} else {
		return 1;
	}
}

/**
 * Get the items registered in the primary and secondary SportsZone navigation menus.
 *
 * @since 1.7.0
 * @since 2.6.0 Introduced the `$component` parameter.
 *
 * @param string $component Optional. Component whose nav items are being fetched.
 * @return array A multidimensional array of all navigation items.
 */
function sz_get_nav_menu_items( $component = 'members' ) {
	$sz    = sportszone();
	$menus = array();

	if ( ! isset( $sz->{$component}->nav ) ) {
		return $menus;
	}

	// Get the item nav and build the menus.
	foreach ( $sz->{$component}->nav->get_item_nav() as $nav_menu ) {
		// Get the correct menu link. See https://sportszone.trac.wordpress.org/ticket/4624.
		$link = sz_loggedin_user_domain() ? str_replace( sz_loggedin_user_domain(), sz_displayed_user_domain(), $nav_menu->link ) : trailingslashit( sz_displayed_user_domain() . $nav_menu->link );

		// Add this menu.
		$menu         = new stdClass;
		$menu->class  = array( 'menu-parent' );
		$menu->css_id = $nav_menu->css_id;
		$menu->link   = $link;
		$menu->name   = $nav_menu->name;
		$menu->parent = 0;

		if ( ! empty( $nav_menu->children ) ) {
			$submenus = array();

			foreach( $nav_menu->children as $sub_menu ) {
				$submenu = new stdClass;
				$submenu->class  = array( 'menu-child' );
				$submenu->css_id = $sub_menu->css_id;
				$submenu->link   = $sub_menu->link;
				$submenu->name   = $sub_menu->name;
				$submenu->parent = $nav_menu->slug;

				// If we're viewing this item's screen, record that we need to mark its parent menu to be selected.
				if ( sz_is_current_action( $sub_menu->slug ) && sz_is_current_component( $nav_menu->slug ) ) {
					$menu->class[]    = 'current-menu-parent';
					$submenu->class[] = 'current-menu-item';
				}

				$submenus[] = $submenu;
			}
		}

		$menus[] = $menu;

		if ( ! empty( $submenus ) ) {
			$menus = array_merge( $menus, $submenus );
		}
	}

	/**
	 * Filters the items registered in the primary and secondary SportsZone navigation menus.
	 *
	 * @since 1.7.0
	 *
	 * @param array $menus Array of items registered in the primary and secondary SportsZone navigation.
	 */
	return apply_filters( 'sz_get_nav_menu_items', $menus );
}

/**
 * Display a navigation menu.
 *
 * @since 1.7.0
 *
 * @param string|array $args {
 *     An array of optional arguments.
 *
 *     @type string $after           Text after the link text. Default: ''.
 *     @type string $before          Text before the link text. Default: ''.
 *     @type string $container       The name of the element to wrap the navigation
 *                                   with. 'div' or 'nav'. Default: 'div'.
 *     @type string $container_class The class that is applied to the container.
 *                                   Default: 'menu-sz-container'.
 *     @type string $container_id    The ID that is applied to the container.
 *                                   Default: ''.
 *     @type int    $depth           How many levels of the hierarchy are to be included.
 *                                   0 means all. Default: 0.
 *     @type bool   $echo            True to echo the menu, false to return it.
 *                                   Default: true.
 *     @type bool   $fallback_cb     If the menu doesn't exist, should a callback
 *                                   function be fired? Default: false (no fallback).
 *     @type string $items_wrap      How the list items should be wrapped. Should be
 *                                   in the form of a printf()-friendly string, using numbered
 *                                   placeholders. Default: '<ul id="%1$s" class="%2$s">%3$s</ul>'.
 *     @type string $link_after      Text after the link. Default: ''.
 *     @type string $link_before     Text before the link. Default: ''.
 *     @type string $menu_class      CSS class to use for the <ul> element which
 *                                   forms the menu. Default: 'menu'.
 *     @type string $menu_id         The ID that is applied to the <ul> element which
 *                                   forms the menu. Default: 'menu-bp', incremented.
 *     @type string $walker          Allows a custom walker class to be specified.
 *                                   Default: 'SZ_Walker_Nav_Menu'.
 * }
 * @return string|null If $echo is false, returns a string containing the nav
 *                     menu markup.
 */
function sz_nav_menu( $args = array() ) {
	static $menu_id_slugs = array();

	$defaults = array(
		'after'           => '',
		'before'          => '',
		'container'       => 'div',
		'container_class' => '',
		'container_id'    => '',
		'depth'           => 0,
		'echo'            => true,
		'fallback_cb'     => false,
		'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		'link_after'      => '',
		'link_before'     => '',
		'menu_class'      => 'menu',
		'menu_id'         => '',
		'walker'          => '',
	);
	$args = wp_parse_args( $args, $defaults );

	/**
	 * Filters the parsed sz_nav_menu arguments.
	 *
	 * @since 1.7.0
	 *
	 * @param array $args Array of parsed arguments.
	 */
	$args = apply_filters( 'sz_nav_menu_args', $args );
	$args = (object) $args;

	$items = $nav_menu = '';
	$show_container = false;

	// Create custom walker if one wasn't set.
	if ( empty( $args->walker ) ) {
		$args->walker = new SZ_Walker_Nav_Menu;
	}

	// Sanitise values for class and ID.
	$args->container_class = sanitize_html_class( $args->container_class );
	$args->container_id    = sanitize_html_class( $args->container_id );

	// Whether to wrap the ul, and what to wrap it with.
	if ( $args->container ) {

		/**
		 * Filters the allowed tags for the wp_nav_menu_container.
		 *
		 * @since 1.7.0
		 *
		 * @param array $value Array of allowed tags. Default 'div' and 'nav'.
		 */
		$allowed_tags = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav', ) );

		if ( in_array( $args->container, $allowed_tags ) ) {
			$show_container = true;

			$class     = $args->container_class ? ' class="' . esc_attr( $args->container_class ) . '"' : ' class="menu-sz-container"';
			$id        = $args->container_id    ? ' id="' . esc_attr( $args->container_id ) . '"'       : '';
			$nav_menu .= '<' . $args->container . $id . $class . '>';
		}
	}

	/**
	 * Filters the SportsZone menu objects.
	 *
	 * @since 1.7.0
	 *
	 * @param array $value Array of nav menu objects.
	 * @param array $args  Array of arguments for the menu.
	 */
	$menu_items = apply_filters( 'sz_nav_menu_objects', sz_get_nav_menu_items(), $args );
	$items      = walk_nav_menu_tree( $menu_items, $args->depth, $args );
	unset( $menu_items );

	// Set the ID that is applied to the ul element which forms the menu.
	if ( ! empty( $args->menu_id ) ) {
		$wrap_id = $args->menu_id;

	} else {
		$wrap_id = 'menu-bp';

		// If a specific ID wasn't requested, and there are multiple menus on the same screen, make sure the autogenerated ID is unique.
		while ( in_array( $wrap_id, $menu_id_slugs ) ) {
			if ( preg_match( '#-(\d+)$#', $wrap_id, $matches ) ) {
				$wrap_id = preg_replace('#-(\d+)$#', '-' . ++$matches[1], $wrap_id );
			} else {
				$wrap_id = $wrap_id . '-1';
			}
		}
	}
	$menu_id_slugs[] = $wrap_id;

	/**
	 * Filters the SportsZone menu items.
	 *
	 * Allow plugins to hook into the menu to add their own <li>'s
	 *
	 * @since 1.7.0
	 *
	 * @param array $items Array of nav menu items.
	 * @param array $args  Array of arguments for the menu.
	 */
	$items = apply_filters( 'sz_nav_menu_items', $items, $args );

	// Build the output.
	$wrap_class  = $args->menu_class ? $args->menu_class : '';
	$nav_menu   .= sprintf( $args->items_wrap, esc_attr( $wrap_id ), esc_attr( $wrap_class ), $items );
	unset( $items );

	// If we've wrapped the ul, close it.
	if ( ! empty( $show_container ) ) {
		$nav_menu .= '</' . $args->container . '>';
	}

	/**
	 * Filters the final SportsZone menu output.
	 *
	 * @since 1.7.0
	 *
	 * @param string $nav_menu Final nav menu output.
	 * @param array  $args     Array of arguments for the menu.
	 */
	$nav_menu = apply_filters( 'sz_nav_menu', $nav_menu, $args );

	if ( ! empty( $args->echo ) ) {
		echo $nav_menu;
	} else {
		return $nav_menu;
	}
}

/**
 * Prints the Recipient Salutation.
 *
 * @since 2.5.0
 *
 * @param array $settings Email Settings.
 */
function sz_email_the_salutation( $settings = array() ) {
	echo sz_email_get_salutation( $settings );
}

	/**
	 * Gets the Recipient Salutation.
	 *
	 * @since 2.5.0
	 *
	 * @param array $settings Email Settings.
	 * @return string The Recipient Salutation.
	 */
	function sz_email_get_salutation( $settings = array() ) {
		$token = '{{recipient.name}}';

		/**
		 * Filters The Recipient Salutation inside the Email Template.
		 *
		 * @since 2.5.0
		 *
		 * @param string $value    The Recipient Salutation.
		 * @param array  $settings Email Settings.
		 * @param string $token    The Recipient token.
		 */
		return apply_filters( 'sz_email_get_salutation', sprintf( _x( 'Hi %s,', 'recipient salutation', 'sportszone' ), $token ), $settings, $token );
	}




/**
 * SportsZone Template
 *
 * Functions for the templating system.
 *
 * @author 		ThemeBoy
 * @category 	Core
 * @package 	SportsZone/Functions
 * @version   2.5.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Output generator tag to aid debugging.
 *
 * @access public
 * @return void
 */
function sz_generator_tag( $gen, $type ) {
	switch ( $type ) {
		case 'html':
			$gen .= "\n" . '<meta name="generator" content="SportsZone ' . esc_attr( SP_VERSION ) . '">';
			break;
		case 'xhtml':
			$gen .= "\n" . '<meta name="generator" content="SportsZone ' . esc_attr( SP_VERSION ) . '" />';
			break;
	}
	return $gen;
}

/**
 * Add body classes for SP pages
 *
 * @param  array $classes
 * @return array
 */
function sz_body_class( $classes ) {
	$classes = (array) $classes;

	if ( is_sportszone() ) {
		$classes[] = 'sportszone';
		$classes[] = 'sportszone-page';
	}

	$post_type = get_post_type();

	if ( 'sz_match' == $post_type ) {
		$id = get_the_ID();
		$show_venue = get_option( 'sportszone_match_show_venue', 'yes' ) == 'yes' ? true : false;
		if ( $show_venue && get_the_terms( $id, 'sz_venue' ) ) {
			if ( get_option( 'sportszone_match_show_maps', 'yes' ) == 'yes' ) {
				$classes[] = 'sp-has-venue';
			}
		}
		if ( 'results' == sz_get_status( $id ) ) {
			if ( get_option( 'sportszone_event_show_results', 'yes' ) == 'yes' ) {
				$classes[] = 'sp-has-results';
			}
		}
		$classes[] = 'sz-performance-sections-' . get_option( 'sportszone_match_performance_sections', -1 );
	} elseif ( 'sz_team' == $post_type && 'yes' == get_option( 'sportszone_team_show_logo', 'yes' ) ) {
		$classes[] = 'sp-show-image';
	} elseif ( 'sz_player' == $post_type && 'yes' == get_option( 'sportszone_player_show_photo', 'yes' ) ) {
		$classes[] = 'sp-show-image';
	} elseif ( 'sz_staff' == $post_type && 'yes' == get_option( 'sportszone_staff_show_photo', 'yes' ) ) {
		$classes[] = 'sz-show-image';
	}

	return array_unique( $classes );
}

/** Template pages ********************************************************/

if ( ! function_exists( 'sportszone_taxonomy_archive_description' ) ) {

	/**
	 * Show an archive description on taxonomy archives
	 *
	 * @access public
	 * @subpackage	Archives
	 * @return void
	 */
	function sportszone_taxonomy_archive_description() {
		if ( is_tax( array( 'sz_season', 'sz_league', 'sz_venue', 'sz_position' ) ) && get_query_var( 'paged' ) == 0 ) {
			$description = apply_filters( 'the_content', term_description() );
			if ( $description ) {
				echo '<div class="term-description">' . $description . '</div>';
			}
		}
	}
}

/** Single Post ********************************************************/

if ( ! function_exists( 'sportszone_output_post_excerpt' ) ) {

	/**
	 * Output the post excerpt.
	 *
	 * @access public
	 * @subpackage	Excerpt
	 * @return void
	 */
	function sportszone_output_post_excerpt() {
		sz_get_template( 'post-excerpt.php' );
	}
}

/** Single Event ********************************************************/

if ( ! function_exists( 'sportszone_output_match_logos' ) ) {

	/**
	 * Output the match logos.
	 *
	 * @access public
	 * @subpackage	Event/Logos
	 * @return void
	 */
	function sportszone_output_match_logos() {
		sz_get_template( 'match-logos.php' );
	}
}

if ( ! function_exists( 'sportszone_output_match_video' ) ) {

	/**
	 * Output the match video.
	 *
	 * @access public
	 * @subpackage	Event/Video
	 * @return void
	 */
	function sportszone_output_match_video() {
		sz_get_template( 'match-video.php' );
	}
}
if ( ! function_exists( 'sportszone_output_match_results' ) ) {

	/**
	 * Output the match results.
	 *
	 * @access public
	 * @subpackage	Event/Results
	 * @return void
	 */
	function sportszone_output_match_results() {
		sz_get_template( 'match-results.php' );
	}
}
if ( ! function_exists( 'sportszone_output_match_details' ) ) {

	/**
	 * Output the match details.
	 *
	 * @access public
	 * @subpackage	Event/Details
	 * @return void
	 */
	function sportszone_output_match_details() {
		sz_get_template( 'match-details.php' );
	}
}
if ( ! function_exists( 'sportszone_output_match_overview' ) ) {

	/**
	 * Output the match details, venue, and results.
	 *
	 * @access public
	 * @subpackage	Event/Overview
	 * @return void
	 */
	function sportszone_output_match_overview() {
		sz_get_template( 'match-overview.php' );
	}
}
if ( ! function_exists( 'sportszone_output_match_venue' ) ) {

	/**
	 * Output the match venue.
	 *
	 * @access public
	 * @subpackage	Event/Venue
	 * @return void
	 */
	function sportszone_output_match_venue() {
		sz_get_template( 'match-venue.php' );
	}
}
if ( ! function_exists( 'sportszone_output_match_performance' ) ) {

	/**
	 * Output the match performance.
	 *
	 * @access public
	 * @subpackage	Event/Performance
	 * @return void
	 */
	function sportszone_output_match_performance() {
		sz_get_template( 'match-performance.php' );
	}
}
if ( ! function_exists( 'sportszone_output_match_officials' ) ) {

	/**
	 * Output the match officials.
	 *
	 * @access public
	 * @subpackage	Event/Officials
	 * @return void
	 */
	function sportszone_output_match_officials() {
		sz_get_template( 'match-officials.php' );
	}
}

/** Single Calendar ********************************************************/

if ( ! function_exists( 'sportszone_output_calendar' ) ) {

	/**
	 * Output the calendar.
	 *
	 * @access public
	 * @subpackage	Calendar
	 * @return void
	 */
	function sportszone_output_calendar() {
        $id = get_the_ID();
        $format = get_post_meta( $id, 'sz_format', true );
        if ( array_key_exists( $format, SP()->formats->calendar ) )
			sz_get_template( 'match-' . $format . '.php', array( 'id' => $id ) );
        else
			sz_get_template( 'match-calendar.php', array( 'id' => $id ) );
	}
}
/** Single Club ********************************************************/

if ( ! function_exists( 'sportszone_output_club_link' ) ) {

	/**
	 * Output the club link.
	 *
	 * @access public
	 * @subpackage	Club/Link
	 * @return void
	 */
	function sportszone_output_club_link() {
		sz_get_template( 'club-link.php' );
	}
}
if ( ! function_exists( 'sportszone_output_club_logo' ) ) {

	/**
	 * Output the club logo.
	 *
	 * @access public
	 * @subpackage	Club/Logo
	 * @return void
	 */
	function sportszone_output_club_logo() {
		sz_get_template( 'club-logo.php' );
	}
}
if ( ! function_exists( 'sportszone_output_club_details' ) ) {

	/**
	 * Output the club details.
	 *
	 * @access public
	 * @subpackage	Club/Details
	 * @return void
	 */
	function sportszone_output_club_details() {
		sz_get_template( 'club-details.php' );
	}
}
if ( ! function_exists( 'sportszone_output_club_staff' ) ) {

	/**
	 * Output the club staff.
	 *
	 * @access public
	 * @subpackage	Club/Staff
	 * @return void
	 */
	function sportszone_output_club_staff() {
		sz_get_template( 'club-staff.php' );
	}
}
if ( ! function_exists( 'sportszone_output_club_tables' ) ) {

	/**
	 * Output the club tables.
	 *
	 * @access public
	 * @subpackage	Club/Tables
	 * @return void
	 */
	function sportszone_output_club_tables() {
		sz_get_template( 'club-tables.php' );
	}
}
if ( ! function_exists( 'sportszone_output_club_lists' ) ) {

	/**
	 * Output the club lists.
	 *
	 * @access public
	 * @subpackage	Club/Lists
	 * @return void
	 */
	function sportszone_output_club_lists() {
		sz_get_template( 'club-lists.php' );
	}
}
if ( ! function_exists( 'sportszone_output_club_matches' ) ) {

	/**
	 * Output the club matches.
	 *
	 * @access public
	 * @subpackage	Club/Events
	 * @return void
	 */
	function sportszone_output_club_matches() {
		sz_get_template( 'club-matches.php' );
	}
}


/** Single Team ********************************************************/

if ( ! function_exists( 'sportszone_output_team_link' ) ) {

	/**
	 * Output the team link.
	 *
	 * @access public
	 * @subpackage	Team/Link
	 * @return void
	 */
	function sportszone_output_team_link() {
		sz_get_template( 'team-link.php' );
	}
}
if ( ! function_exists( 'sportszone_output_team_logo' ) ) {

	/**
	 * Output the team logo.
	 *
	 * @access public
	 * @subpackage	Team/Logo
	 * @return void
	 */
	function sportszone_output_team_logo() {
		sz_get_template( 'team-logo.php' );
	}
}
if ( ! function_exists( 'sportszone_output_team_details' ) ) {

	/**
	 * Output the team details.
	 *
	 * @access public
	 * @subpackage	Team/Details
	 * @return void
	 */
	function sportszone_output_team_details() {
		sz_get_template( 'team-details.php' );
	}
}
if ( ! function_exists( 'sportszone_output_team_staff' ) ) {

	/**
	 * Output the team staff.
	 *
	 * @access public
	 * @subpackage	Team/Staff
	 * @return void
	 */
	function sportszone_output_team_staff() {
		sz_get_template( 'team-staff.php' );
	}
}
if ( ! function_exists( 'sportszone_output_team_tables' ) ) {

	/**
	 * Output the team tables.
	 *
	 * @access public
	 * @subpackage	Team/Tables
	 * @return void
	 */
	function sportszone_output_team_tables() {
		sz_get_template( 'team-tables.php' );
	}
}
if ( ! function_exists( 'sportszone_output_team_lists' ) ) {

	/**
	 * Output the team lists.
	 *
	 * @access public
	 * @subpackage	Team/Lists
	 * @return void
	 */
	function sportszone_output_team_lists() {
		sz_get_template( 'team-lists.php' );
	}
}
if ( ! function_exists( 'sportszone_output_team_matches' ) ) {

	/**
	 * Output the team matches.
	 *
	 * @access public
	 * @subpackage	Team/Events
	 * @return void
	 */
	function sportszone_output_team_matches() {
		sz_get_template( 'team-matches.php' );
	}
}

/** Single League Table ********************************************************/

if ( ! function_exists( 'sportszone_output_league_table' ) ) {

	/**
	 * Output the team columns.
	 *
	 * @access public
	 * @subpackage	Table
	 * @return void
	 */
	function sportszone_output_league_table() {
		$id = get_the_ID();
		$format = get_post_meta( $id, 'sz_format', true );
		if ( array_key_exists( $format, SP()->formats->table ) && 'standings' !== $format )
			sz_get_template( 'team-' . $format . '.php', array( 'id' => $id ) );
		else
			sz_get_template( 'league-table.php', array( 'id' => $id ) );
	}
}

/** Single Player ********************************************************/

if ( ! function_exists( 'sportszone_output_player_selector' ) ) {

	/**
	 * Output the player dropdown.
	 *
	 * @access public
	 * @subpackage	Player/Dropdown
	 * @return void
	 */
	function sportszone_output_player_selector() {
		sz_get_template( 'player-selector.php' );
	}
}
if ( ! function_exists( 'sportszone_output_player_photo' ) ) {

	/**
	 * Output the player photo.
	 *
	 * @access public
	 * @subpackage	Player/Photo
	 * @return void
	 */
	function sportszone_output_player_photo() {
		sz_get_template( 'player-photo.php' );
	}
}
if ( ! function_exists( 'sportszone_output_player_details' ) ) {

	/**
	 * Output the player details.
	 *
	 * @access public
	 * @subpackage	Player/Details
	 * @return void
	 */
	function sportszone_output_player_details() {
		sz_get_template( 'player-details.php' );
	}
}
if ( ! function_exists( 'sportszone_output_player_statistics' ) ) {

	/**
	 * Output the player statistics.
	 *
	 * @access public
	 * @subpackage	Player/Statistics
	 * @return void
	 */
	function sportszone_output_player_statistics() {
		sz_get_template( 'player-statistics.php' );
	}
}
if ( ! function_exists( 'sportszone_output_player_matches' ) ) {

	/**
	 * Output the player matches.
	 *
	 * @access public
	 * @subpackage	Player/Events
	 * @return void
	 */
	function sportszone_output_player_matches() {
		sz_get_template( 'player-matches.php' );
	}
}

/** Single Player List ********************************************************/

if ( ! function_exists( 'sportszone_output_player_list' ) ) {

	/**
	 * Output the player list.
	 *
	 * @access public
	 * @subpackage	List
	 * @return void
	 */
	function sportszone_output_player_list() {
        $id = get_the_ID();
        $format = get_post_meta( $id, 'sz_format', true );
        if ( array_key_exists( $format, SP()->formats->list ) )
			sz_get_template( 'player-' . $format . '.php', array( 'id' => $id ) );
        else
			sz_get_template( 'player-list.php', array( 'id' => $id ) );
	}
}

/** Single Staff ********************************************************/

if ( ! function_exists( 'sportszone_output_staff_selector' ) ) {

	/**
	 * Output the staff dropdown.
	 *
	 * @access public
	 * @subpackage	Staff/Dropdown
	 * @return void
	 */
	function sportszone_output_staff_selector() {
		sz_get_template( 'staff-selector.php' );
	}
}
if ( ! function_exists( 'sportszone_output_staff_photo' ) ) {

	/**
	 * Output the staff photo.
	 *
	 * @access public
	 * @subpackage	Staff/Photo
	 * @return void
	 */
	function sportszone_output_staff_photo() {
		sz_get_template( 'staff-photo.php' );
	}
}
if ( ! function_exists( 'sportszone_output_staff_details' ) ) {

	/**
	 * Output the staff details.
	 *
	 * @access public
	 * @subpackage	Staff/Details
	 * @return void
	 */
	function sportszone_output_staff_details() {
		sz_get_template( 'staff-details.php' );
	}
}

/** Venue Archive ********************************************************/

function sportszone_output_venue_map( $query ) {
    if ( ! is_tax( 'sz_venue' ) )
        return;

    $slug = sz_array_value( $query->query, 'sz_venue', null );

    if ( ! $slug )
        return;

    $venue = get_term_by( 'slug', $slug, 'sz_venue' );
    $t_id = $venue->term_id;
    $meta = get_option( "taxonomy_$t_id" );
	sz_get_template( 'venue-map.php', array( 'meta' => $meta ) );
}

/** Misc ********************************************************/

function sportszone_output_br_tag() {
	?>
	<br>
	<?php
}
if ( ! function_exists( 'sportszone_responsive_tables_css' ) ) {

	/**
	 * Output the inlince css code for responsive tables.
	 *
	 * @access public
	 * @subpackage	Responsive
	 * @return void
	 */
	function sportszone_responsive_tables_css( $identity ) {
		$custom_css = '/* 
		Max width before this PARTICULAR table gets nasty
		This query will take effect for any screen smaller than 760px
		and also iPads specifically.
		*/
		@media 
		only screen and (max-width: 800px) {
		
			/* Force table to not be like tables anymore */
			table.'.$identity.', table.'.$identity.' thead, table.'.$identity.' tfoot, table.'.$identity.' tbody, table.'.$identity.' th, table.'.$identity.' td, table.'.$identity.' tr { 
				display: block; 
			}
			
			/* Hide table headers (but not display: none;, for accessibility) */
			table.'.$identity.' thead tr { 
				position: absolute;
				top: -9999px;
				left: -9999px;
			}

			/* Add subtle border to table rows */
			table.'.$identity.' tbody tr { 
				border-top: 1px solid rgba(0, 0, 0, 0.1);
			}

			.sp-data-table .data-number, .sp-data-table .data-rank {
				width: auto !important;
			}
			
			.sp-data-table th,
			.sp-data-table td {
				text-align: center !important;
			}
			
			table.'.$identity.' td { 
				/* Behave  like a "row" */
				border: none;
				position: relative;
				padding-left: 50%;
				vertical-align: middle;
			}
			
			table.'.$identity.' td:before { 
				/* Now like a table header */
				position: absolute;
				/* Label the data */
				content: attr(data-label);
				/* Top/left values mimic padding */
				top: 6px;
				left: 6px;
				width: 45%; 
				padding-right: 10px; 
				white-space: nowrap;
			}
		}
			';
		
		$dummystyle = 'sportszone-style-inline-'.$identity;
		wp_register_style( $dummystyle, false );
		wp_enqueue_style( $dummystyle );
		wp_add_inline_style( $dummystyle, $custom_css );
	}
}