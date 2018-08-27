<?php
/**
 * Setup menus in WP admin.
 *
 * @author 		ThemeBoy
 * @category 	Admin
 * @package 	SportsZone/Admin
 * @version		2.5.1
  * Added Clubs
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SZ_Admin_Menus' ) ) :

/**
 * SZ_Admin_Menus Class
 */
class SZ_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_filter( 'admin_menu', array( $this, 'menu_clean' ), 5 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 6 );
		add_action( 'admin_menu', array( $this, 'config_menu' ), 7 );
		add_action( 'admin_menu', array( $this, 'leagues_menu' ), 20 );
		add_action( 'admin_menu', array( $this, 'seasons_menu' ), 21 );

		add_action( 'admin_head', array( $this, 'menu_highlight' ) );
		add_action( 'admin_head', array( $this, 'menu_rename' ) );
		add_action( 'parent_file', array( $this, 'parent_file' ) );
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );
		add_filter( 'sportszone_sitemap_taxonomy_post_types', array( $this, 'sitemap_taxonomy_post_types' ), 10, 2 );
	}

	/**
	 * Add menu item
	 */
	public function admin_menu() {
		global $menu;

	    if ( current_user_can( 'manage_options' ) )
	    	$menu[] = array( '', 'read', 'separator-sportszone', '', 'wp-menu-separator sportszone' );
		
		$main_page = add_menu_page( __( 'SportsZone', 'sportszone' ), __( 'SportsZone', 'sportszone' ), 'manage_options', 'sportszone', array( $this, 'settings_page' ), apply_filters( 'sportszone_menu_icon', null ), '2.5' );
	}

	/**
	 * Add menu item
	 */
	public function config_menu() {
		add_submenu_page( 'sportszone', __( 'Configure', 'sportszone' ), __( 'Configure', 'sportszone' ), 'manage_options', 'sportszone-config', array( $this, 'config_page' ) );
	}

	/**
	 * Add menu item
	 */
	public function leagues_menu() {
		add_submenu_page( 'sportszone', __( 'Leagues', 'sportszone' ), __( 'Leagues', 'sportszone' ), 'manage_options', 'edit-tags.php?taxonomy=sz_league');
	}

	/**
	 * Add menu item
	 */
	public function seasons_menu() {
		add_submenu_page( 'sportszone', __( 'Seasons', 'sportszone' ), __( 'Seasons', 'sportszone' ), 'manage_options', 'edit-tags.php?taxonomy=sz_season');
	}

	/**
	 * Highlights the correct top level admin menu item for post type add screens.
	 *
	 * @access public
	 * @return void
	 */
	public function menu_highlight() {
		global $typenow;
		$screen = get_current_screen();
		if ( ! is_object( $screen ) ) return;
		if ( $screen->id == 'sz_role' ) {
			$this->highlight_admin_menu( 'edit.php?post_type=sz_staff', 'edit-tags.php?taxonomy=sz_role&post_type=sz_staff' );
		} elseif ( is_sz_config_type( $typenow ) ) {
			$this->highlight_admin_menu( 'sportszone', 'sportszone-config' );
		} elseif ( $typenow == 'sz_calendar' ) {
			$this->highlight_admin_menu( 'edit.php?post_type=sz_event', 'edit.php?post_type=sz_calendar' );
		} elseif ( $typenow == 'sz_table' ) {
			$this->highlight_admin_menu( 'edit.php?post_type=sz_team', 'edit.php?post_type=sz_table' );
		} elseif ( $typenow == 'sz_list' ) {
			$this->highlight_admin_menu( 'edit.php?post_type=sz_player', 'edit.php?post_type=sz_list' );
		}
	}

	/**
	 * Renames admin menu items.
	 *
	 * @access public
	 * @return void
	 */
	public function menu_rename() {
		global $menu, $submenu;

		if ( isset( $submenu['sportszone'] ) && isset( $submenu['sportszone'][0] ) && isset( $submenu['sportszone'][0][0] ) )
			$submenu['sportszone'][0][0] = __( 'Settings', 'sportszone' );
	}

	public function parent_file( $parent_file ) {
		global $current_screen;
		$taxonomy = $current_screen->taxonomy;
		if ( in_array( $taxonomy, array( 'sz_league', 'sz_season' ) ) )
			$parent_file = 'sportszone';
		return $parent_file;
	}

	/**
	 * Reorder the SP menu items in admin.
	 *
	 * @param mixed $menu_order
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array
		$sportszone_menu_order = array();

		// Get the index of our custom separator
		$sportszone_separator = array_search( 'separator-sportszone', $menu_order );

		// Get index of menu items
		$sportszone_event = array_search( 'edit.php?post_type=sz_event', $menu_order );
		$sportszone_club = array_search( 'edit.php?post_type=sz_club', $menu_order );
		$sportszone_team = array_search( 'edit.php?post_type=sz_team', $menu_order );
		$sportszone_player = array_search( 'edit.php?post_type=sz_player', $menu_order );
		$sportszone_staff = array_search( 'edit.php?post_type=sz_staff', $menu_order );

		// Loop through menu order and do some rearranging
		foreach ( $menu_order as $index => $item ):

			if ( ( ( 'sportszone' ) == $item ) ):
				$sportszone_menu_order[] = 'separator-sportszone';
				$sportszone_menu_order[] = $item;
				$sportszone_menu_order[] = 'edit.php?post_type=sz_event';
				$sportszone_menu_order[] = 'edit.php?post_type=sz_club';
				$sportszone_menu_order[] = 'edit.php?post_type=sz_team';
				$sportszone_menu_order[] = 'edit.php?post_type=sz_player';
				$sportszone_menu_order[] = 'edit.php?post_type=sz_staff';
				unset( $menu_order[ $sportszone_separator ] );
				unset( $menu_order[ $sportszone_event ] );
				unset( $menu_order[ $sportszone_club ] );
				unset( $menu_order[ $sportszone_team ] );
				unset( $menu_order[ $sportszone_player ] );
				unset( $menu_order[ $sportszone_staff ] );

				// Apply to added menu items
				$menu_items = apply_filters( 'sportszone_menu_items', array() );
				foreach ( $menu_items as $menu_item ):
					$sportszone_menu_order[] = $menu_item;
					$index = array_search( $menu_item, $menu_order );
					unset( $menu_order[ $index ] );
				endforeach;

			elseif ( !in_array( $item, array( 'separator-sportszone' ) ) ) :
				$sportszone_menu_order[] = $item;
			endif;

		endforeach;

		// Return order
		return $sportszone_menu_order;
	}

	/**
	 * custom_menu_order
	 * @return bool
	 */
	public function custom_menu_order() {
		if ( ! current_user_can( 'manage_options' ) )
			return false;
		return true;
	}

	/**
	 * Clean the SP menu items in admin.
	 */
	public function menu_clean() {
		global $menu, $submenu, $current_user;

		// Find where our separator is in the menu
		foreach( $menu as $key => $data ):
			if ( is_array( $data ) && array_key_exists( 2, $data ) && $data[2] == 'edit.php?post_type=sz_separator' )
				$separator_position = $key;
		endforeach;

		// Swap our separator post type with a menu separator
		if ( isset( $separator_position ) ):
			$menu[ $separator_position ] = array( '', 'read', 'separator-sportszone', '', 'wp-menu-separator sportszone' );
		endif;

	    // Remove "Leagues" and "Seasons" links from Events submenu
		if ( isset( $submenu['edit.php?post_type=sz_event'] ) ):
			$submenu['edit.php?post_type=sz_event'] = array_filter( $submenu['edit.php?post_type=sz_event'], array( $this, 'remove_leagues' ) );
			$submenu['edit.php?post_type=sz_event'] = array_filter( $submenu['edit.php?post_type=sz_event'], array( $this, 'remove_seasons' ) );
		endif;

		// Remove "Venues", "Leagues" and "Seasons" links from Teams submenu
		if ( isset( $submenu['edit.php?post_type=sz_club'] ) ):
			$submenu['edit.php?post_type=sz_club'] = array_filter( $submenu['edit.php?post_type=sz_club'], array( $this, 'remove_venues' ) );
			$submenu['edit.php?post_type=sz_club'] = array_filter( $submenu['edit.php?post_type=sz_club'], array( $this, 'remove_leagues' ) );
			$submenu['edit.php?post_type=sz_club'] = array_filter( $submenu['edit.php?post_type=sz_club'], array( $this, 'remove_seasons' ) );
		endif;
		
	    // Remove "Venues", "Leagues" and "Seasons" links from Teams submenu
		if ( isset( $submenu['edit.php?post_type=sz_team'] ) ):
			$submenu['edit.php?post_type=sz_team'] = array_filter( $submenu['edit.php?post_type=sz_team'], array( $this, 'remove_venues' ) );
			$submenu['edit.php?post_type=sz_team'] = array_filter( $submenu['edit.php?post_type=sz_team'], array( $this, 'remove_leagues' ) );
			$submenu['edit.php?post_type=sz_team'] = array_filter( $submenu['edit.php?post_type=sz_team'], array( $this, 'remove_seasons' ) );
		endif;

	    // Remove "Leagues" and "Seasons" links from Players submenu
		if ( isset( $submenu['edit.php?post_type=sz_player'] ) ):
			$submenu['edit.php?post_type=sz_player'] = array_filter( $submenu['edit.php?post_type=sz_player'], array( $this, 'remove_leagues' ) );
			$submenu['edit.php?post_type=sz_player'] = array_filter( $submenu['edit.php?post_type=sz_player'], array( $this, 'remove_seasons' ) );
		endif;

	    // Remove "Leagues" and "Seasons" links from Staff submenu
		if ( isset( $submenu['edit.php?post_type=sz_staff'] ) ):
			$submenu['edit.php?post_type=sz_staff'] = array_filter( $submenu['edit.php?post_type=sz_staff'], array( $this, 'remove_leagues' ) );
			$submenu['edit.php?post_type=sz_staff'] = array_filter( $submenu['edit.php?post_type=sz_staff'], array( $this, 'remove_seasons' ) );
		endif;

		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);

		if ( in_array( $user_role, array( 'sz_player', 'sz_staff', 'sz_event_manager', 'sz_team_manager', 'sz_club_manager' ) ) ):
			remove_menu_page( 'upload.php' );
			remove_menu_page( 'edit-comments.php' );
			remove_menu_page( 'tools.php' );
		endif;
	}

	/**
	 * Init the config page
	 */
	public function config_page() {
		include( 'views/html-admin-config.php' );
	}

	/**
	 * Init the settings page
	 */
	public function settings_page() {
		include_once( 'class-sz-admin-settings.php' );
		SZ_Admin_Settings::output();
	}

	public function remove_add_new( $arr = array() ) {
		return $arr[0] != __( 'Add New', 'sportszone' );
	}

	public function remove_leagues( $arr = array() ) {
		return $arr[0] != __( 'Leagues', 'sportszone' );
	}

	public function remove_positions( $arr = array() ) {
		return $arr[0] != __( 'Positions', 'sportszone' );
	}

	public function remove_seasons( $arr = array() ) {
		return $arr[0] != __( 'Seasons', 'sportszone' );
	}

	public function remove_venues( $arr = array() ) {
		return $arr[0] != __( 'Venues', 'sportszone' );
	}

	public static function highlight_admin_menu( $p = 'sportszone', $s = 'sportszone' ) {
		global $parent_file, $submenu_file;
		$parent_file = $p;
		$submenu_file = $s;
	}

	public static function sitemap_taxonomy_post_types( $post_types = array(), $taxonomy ) {
		$post_types = array_intersect( $post_types, sz_primary_post_types() );
		// Remove teams from venues taxonomy post type array
		if ( $taxonomy === 'sz_venue' && ( $key = array_search( 'sz_team', $post_types ) ) !== false ):
			unset( $post_types[ $key ] );
		endif;

		return $post_types;
	}
}

endif;

//return new SZ_Admin_Menus();
