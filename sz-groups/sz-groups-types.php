<?php
/**
 *
 * @link              https://wbcomdesigns.com/
 * @since             1.0.0
 * @package           SZ_Add_Group_Types
 *
 * @wordpress-plugin
 * Based on Plugin:       BuddyPress Create Group Types
 * Plugin URI:        https://wbcomdesigns.com/
 * Description:       This plugin adds a new feature to SportsZone, <strong>Group Types</strong>. This allows an easy <strong>categorization</strong> of <strong>BP Groups</strong>.
 * Version:           1.1.0
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sz-add-group-types
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'SZ_GROUP_TYPE_PLUGIN_BASENAME',  plugin_basename( __FILE__ ) );

function activate_sz_add_group_types() {
	add_option( 'szgt_group_types', array() );
}

register_activation_hook( __FILE__, 'activate_sz_add_group_types' );

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    SZ_Add_Group_Types
 * @subpackage SZ_Add_Group_Types/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class SZ_Add_Group_Types {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      SZ_Add_Group_Types_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'sz-add-group-types';
		$this->version     = '1.0.0';
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_globals();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - SZ_Add_Group_Types_Loader. Orchestrates the hooks of the plugin.
	 * - SZ_Add_Group_Types_I18n. Defines internationalization functionality.
	 * - SZ_Add_Group_Types_Admin. Defines all hooks for the admin area.
	 * - SZ_Add_Group_Types_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'sz-groups/types/includes/class-sz-add-group-types-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'sz-groups/types/admin/class-sz-add-group-types-admin.php';

		/**
		 * The class responsible for defining the global variable of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'sz-groups/types/includes/class-sz-add-group-types-globals.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'sz-groups/types/public/class-sz-add-group-types-public.php';

		$this->loader = new SZ_Add_Group_Types_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin  = new SZ_Add_Group_Types_Admin( $this->get_plugin_name(), $this->get_version() );
		$szgt_settings = get_site_option( 'szgt_general_settings' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( sz_core_admin_hook(), $plugin_admin, 'szgt_add_submenu_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'szgt_register_general_settings' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'szgt_register_group_types_listing_settings' );

		if ( isset( $szgt_settings['group_type_search_enabled'] ) && 'yes' === $szgt_settings['group_type_search_enabled'] ) {
			$this->loader->add_action( 'admin_init', $plugin_admin, 'szgt_register_group_type_search_settings' );
		}

		$this->loader->add_action( 'admin_init', $plugin_admin, 'szgt_register_type_display_settings' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'szgt_register_support_settings' );

		$this->loader->add_action( 'sz_groups_register_group_types', $plugin_admin, 'szgt_register_group_types' );

		// Ajax call for deleting the group type.
		$this->loader->add_action( 'wp_ajax_szgt_delete_group_type', $plugin_admin, 'szgt_delete_group_type' );
		$this->loader->add_action( 'wp_ajax_nopriv_szgt_delete_group_type', $plugin_admin, 'szgt_delete_group_type' );

		// Ajax call for deleting the group type.
		$this->loader->add_action( 'wp_ajax_szgt_update_group_type', $plugin_admin, 'szgt_update_group_type' );
		$this->loader->add_action( 'wp_ajax_nopriv_szgt_update_group_type', $plugin_admin, 'szgt_update_group_type' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new SZ_Add_Group_Types_Public( $this->get_plugin_name(), $this->get_version() );
		global $sz_grp_types;
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		if ( isset( $sz_grp_types->group_type_search_enabled ) && 'yes' === $sz_grp_types->group_type_search_enabled ) {
			$this->loader->add_action( 'sz_directory_groups_search_form', $plugin_public, 'szgt_modified_group_search_form', 10, 1 );
		}

		$this->loader->add_action( 'sz_ajax_querystring', $plugin_public, 'szgt_alter_sz_ajax_querystring', 100, 2 );

		//$this->loader->add_action( 'sz_groups_directory_group_types', $plugin_public, 'bb_display_directory_tabs' );
		$this->loader->add_action( 'sz_groups_directory_group_types', $plugin_public, 'sz_display_directory_select' );
		$this->loader->add_filter( 'sz_before_has_groups_parse_args', $plugin_public, 'bb_set_has_groups_type_arg', 10, 2 );
		$this->loader->add_action( 'sz_directory_groups_item', $plugin_public, 'bb_group_directory_show_group_type' );
	}

	/**
	 * Registers a global variable of the plugin - sz-group-types
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function define_globals() {
		global $sz_grp_types;
		$sz_grp_types = new SZ_Add_Group_Types_Globals( $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    SZ_Add_Group_Types_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sz_add_group_types() {

	$plugin = new SZ_Add_Group_Types();
	$plugin->run();

}

add_action( 'sz_loaded', 'szgt_plugin_init' );
/**
 * Check plugin requirement on plugins loaded
 * this plugin requires SportsZone to be installed and active
 */
function szgt_plugin_init() {
	if ( sz_group_type_check_config() ){
		run_sz_add_group_types();
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'szgt_plugin_links' );
	}
}
function sz_group_type_check_config(){
	global $sz;
	
	$config = array(
		'blog_status'    => false, 
		'network_active' => false, 
		'network_status' => true 
	);
	if ( get_current_blog_id() == sz_get_root_blog_id() ) {
		$config['blog_status'] = true;
	}
	
	$network_plugins = get_site_option( 'active_sitewide_plugins', array() );

	// No Network plugins
	if ( empty( $network_plugins ) )

	// Looking for SportsZone and sz-activity plugin
	$check[] = $sz->basename;
	$check[] = SZ_GROUP_TYPE_PLUGIN_BASENAME;

	// Are they active on the network ?
	$network_active = array_diff( $check, array_keys( $network_plugins ) );
	
	// If result is 1, your plugin is network activated
	// and not SportsZone or vice & versa. Config is not ok
	if ( count( $network_active ) == 1 )
		$config['network_status'] = false;

	// We need to know if the plugin is network activated to choose the right
	// notice ( admin or network_admin ) to display the warning message.
	$config['network_active'] = isset( $network_plugins[ SZ_GROUP_TYPE_PLUGIN_BASENAME ] );

	// if SportsZone config is different than sz-activity plugin
	if ( !$config['blog_status'] || !$config['network_status'] ) {

		$warnings = array();
		if ( !sz_core_do_network_admin() && !$config['blog_status'] ) {
			add_action( 'admin_notices', 'szgt_same_blog' );
			$warnings[] = __( 'Sportszone Create Group Types requires to be activated on the blog where SportsZone is activated.', 'sz-add-group-types' );
		}

		if ( sz_core_do_network_admin() && !$config['network_status'] ) {
			add_action( 'admin_notices', 'szgt_same_network_config' );
			$warnings[] = __( 'SportsZone Create Group Types and SportsZone need to share the same network configuration.', 'sz-add-group-types' );
		}

		if ( ! empty( $warnings ) ) :
			return false;
		endif;
		$szgs_active = in_array( 'buddypress-group-type-search/buddypress-groups-search.php', get_site_option( 'active_sitewide_plugins' ), true );
		if ( current_user_can( 'activate_plugins' ) && true === $szgs_active ) {
			add_action( $config['network_active'] ? 'network_admin_notices' : 'admin_notices', 'szgts_remove_plugin_admin_notice' );
		}
		if ( !sz_is_active( 'groups' ) ) {
			add_action( $config['network_active'] ? 'network_admin_notices' : 'admin_notices', 'szgt_plugin_require_group_component_admin_notice' );
		}
		
		// Display a warning message in network admin or admin
	} 
	return true;
}

function szgt_same_blog(){
	echo '<div class="error"><p>'
	. esc_html( __( 'SportsZone Create Group Types requires to be activated on the blog where SportsZone is activated.', 'sz-add-group-types' ) )
	. '</p></div>';
}

function szgt_same_network_config(){
	echo '<div class="error"><p>'
	. esc_html( __( 'SportsZone Create Group Types and SportsZone need to share the same network configuration.', 'sz-add-group-types' ) )
	. '</p></div>';
}
/**
 * Function to through admin notice if SportsZone Group Type Search is active.
 */
function szgts_remove_plugin_admin_notice() {
	$szgt_plugin  = 'SportsZone Create Group Types';
	$szgts_plugin = 'SportsZone Group Type Search';

	echo '<div class="error"><p>'
	. sprintf( esc_html( __( '%1$s do not require %2$s to be installed and active as it contains functions of %3$s plugin.', 'sz-add-group-types' ) ), '<strong>' . esc_html( $szgt_plugin ) . '</strong>', '<strong>' . esc_html( $szgts_plugin ) . '</strong>', '<strong>' . esc_html( $szgts_plugin ) . '</strong>' )
	. '</p></div>';
}

/**
 * Function to through admin notice if SportsZone is not active.
 */
function szgt_plugin_admin_notice() {
	$szgt_plugin = 'SportsZone Create Group Types';
	$sz_plugin   = 'SportsZone';
	echo '<div class="error"><p>'
	. sprintf( esc_html( __( '%1$s is ineffective as it requires %2$s to be installed and active.', 'sz-add-group-types' ) ), '<strong>' . esc_html( $szgt_plugin ) . '</strong>', '<strong>' . esc_html( $sz_plugin ) . '</strong>' )
	. '</p></div>';
	if ( null !== filter_input( INPUT_GET, 'activate' ) ) {
		$activate = filter_input( INPUT_GET, 'activate' );
			unset( $activate );
	}
}

/**
 * Function to through admin notice if SportsZone group components is not active.
 */
function szgt_plugin_require_group_component_admin_notice() {
	$szgt_plugin  = 'SportsZone Create Group Types';
	$sz_component = 'Groups Component';
		if( !sz_is_active( 'groups' ) ){
			echo '<div class="error"><p>'
		. sprintf( esc_html( __( '%1$s is ineffective now as it requires %2$s to be active.', 'sz-add-group-types' ) ), '<strong>' . esc_html( $szgt_plugin ) . '</strong>', '<strong>' . esc_html( $sz_component ) . '</strong>' )
		. '</p></div>';
		if ( null !== filter_input( INPUT_GET, 'activate' ) ) {
			$activate = filter_input( INPUT_GET, 'activate' );
			unset( $activate );
		}
	}
}

/**
 * Function to set plugin action links.
 *
 * @param array $links Plugin settings links array.
 */
function szgt_plugin_links( $links ) {
	$szgt_links = array(
		'<a href="' . admin_url( 'admin.php?page=sz-add-group-types' ) . '">' . __( 'Settings', 'sz-add-group-types' ) . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank">' . __( 'Support', 'sz-add-group-types' ) . '</a>',
	);
	return array_merge( $links, $szgt_links );
}
