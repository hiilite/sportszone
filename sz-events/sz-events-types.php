<?php
/**
 *
 * @link              https://wbcomdesigns.com/
 * @since             1.0.0
 * @package           SZ_Add_Event_Types
 *
 * @wordpress-plugin
 * Based on Plugin:       BuddyPress Create Event Types
 * Plugin URI:        https://wbcomdesigns.com/
 * Description:       This plugin adds a new feature to SportsZone, <strong>Event Types</strong>. This allows an easy <strong>categorization</strong> of <strong>BP Events</strong>.
 * Version:           1.1.0
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sz-add-event-types
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'SZ_EVENT_TYPE_PLUGIN_BASENAME',  plugin_basename( __FILE__ ) );

function activate_sz_add_event_types() {
	add_option( 'szet_event_types', array() );
}

register_activation_hook( __FILE__, 'activate_sz_add_event_types' );

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
 * @package    SZ_Add_Event_Types
 * @subpackage SZ_Add_Event_Types/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class SZ_Add_Event_Types {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      SZ_Add_Event_Types_Loader    $loader    Maintains and registers all hooks for the plugin.
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

		$this->plugin_name = 'sz-add-event-types';
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
	 * - SZ_Add_Event_Types_Loader. Orchestrates the hooks of the plugin.
	 * - SZ_Add_Event_Types_I18n. Defines internationalization functionality.
	 * - SZ_Add_Event_Types_Admin. Defines all hooks for the admin area.
	 * - SZ_Add_Event_Types_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'sz-events/types/includes/class-sz-add-event-types-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'sz-events/types/admin/class-sz-add-event-types-admin.php';

		/**
		 * The class responsible for defining the global variable of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'sz-events/types/includes/class-sz-add-event-types-globals.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'sz-events/types/public/class-sz-add-event-types-public.php';

		$this->loader = new SZ_Add_Event_Types_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin  = new SZ_Add_Event_Types_Admin( $this->get_plugin_name(), $this->get_version() );
		$szet_settings = get_site_option( 'szet_general_settings' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( sz_core_admin_hook(), $plugin_admin, 'szet_add_submenu_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'szet_register_general_settings' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'szet_register_event_types_listing_settings' );

		if ( isset( $szet_settings['event_type_search_enabled'] ) && 'yes' === $szet_settings['event_type_search_enabled'] ) {
			$this->loader->add_action( 'admin_init', $plugin_admin, 'szet_register_event_type_search_settings' );
		}

		$this->loader->add_action( 'admin_init', $plugin_admin, 'szet_register_type_display_settings' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'szet_register_support_settings' );

		$this->loader->add_action( 'sz_events_register_event_types', $plugin_admin, 'szet_register_event_types' );

		// Ajax call for deleting the event type.
		$this->loader->add_action( 'wp_ajax_szet_delete_event_type', $plugin_admin, 'szet_delete_event_type' );
		$this->loader->add_action( 'wp_ajax_nopriv_szet_delete_event_type', $plugin_admin, 'szet_delete_event_type' );

		// Ajax call for deleting the event type.
		$this->loader->add_action( 'wp_ajax_szet_update_event_type', $plugin_admin, 'szet_update_event_type' );
		$this->loader->add_action( 'wp_ajax_nopriv_szet_update_event_type', $plugin_admin, 'szet_update_event_type' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new SZ_Add_Event_Types_Public( $this->get_plugin_name(), $this->get_version() );
		global $sz_evt_types;
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		if ( isset( $sz_evt_types->event_type_search_enabled ) && 'yes' === $sz_evt_types->event_type_search_enabled ) {
			$this->loader->add_action( 'sz_directory_events_search_form', $plugin_public, 'szet_modified_event_search_form', 10, 1 );
		}

		$this->loader->add_action( 'sz_ajax_querystring', $plugin_public, 'szet_alter_sz_ajax_querystring', 100, 2 );

		$this->loader->add_action( 'sz_events_directory_event_types', $plugin_public, 'sz_display_directory_select' );
		//$this->loader->add_action( 'sz_events_directory_event_types', $plugin_public, 'bb_display_directory_tabs' );
		$this->loader->add_filter( 'sz_before_has_events_parse_args', $plugin_public, 'bb_set_has_events_type_arg', 10, 2 );
		$this->loader->add_action( 'sz_directory_events_item', $plugin_public, 'bb_event_directory_show_event_type' );
	}

	/**
	 * Registers a global variable of the plugin - sz-event-types
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function define_globals() {
		global $sz_evt_types;
		$sz_evt_types = new SZ_Add_Event_Types_Globals( $this->get_plugin_name(), $this->get_version() );
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
	 * @return    SZ_Add_Event_Types_Loader    Orchestrates the hooks of the plugin.
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
function run_sz_add_event_types() {

	$plugin = new SZ_Add_Event_Types();
	$plugin->run();

}

add_action( 'sz_loaded', 'szet_plugin_init' );
/**
 * Check plugin requirement on plugins loaded
 * this plugin requires SportsZone to be installed and active
 */
function szet_plugin_init() {
	if ( sz_event_type_check_config() ){
		run_sz_add_event_types();
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'szet_plugin_links' );
	}
}
function sz_event_type_check_config(){
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
	$check[] = SZ_EVENT_TYPE_PLUGIN_BASENAME;

	// Are they active on the network ?
	$network_active = array_diff( $check, array_keys( $network_plugins ) );
	
	// If result is 1, your plugin is network activated
	// and not SportsZone or vice & versa. Config is not ok
	if ( count( $network_active ) == 1 )
		$config['network_status'] = false;

	// We need to know if the plugin is network activated to choose the right
	// notice ( admin or network_admin ) to display the warning message.
	$config['network_active'] = isset( $network_plugins[ SZ_EVENT_TYPE_PLUGIN_BASENAME ] );

	// if SportsZone config is different than sz-activity plugin
	if ( !$config['blog_status'] || !$config['network_status'] ) {

		$warnings = array();
		if ( !sz_core_do_network_admin() && !$config['blog_status'] ) {
			add_action( 'admin_notices', 'szet_same_blog' );
			$warnings[] = __( 'Sportszone Create Event Types requires to be activated on the blog where SportsZone is activated.', 'sz-add-event-types' );
		}

		if ( sz_core_do_network_admin() && !$config['network_status'] ) {
			add_action( 'admin_notices', 'szet_same_network_config' );
			$warnings[] = __( 'SportsZone Create Event Types and SportsZone need to share the same network configuration.', 'sz-add-event-types' );
		}

		if ( ! empty( $warnings ) ) :
			return false;
		endif;
		$szgs_active = in_array( 'buddypress-event-type-search/buddypress-events-search.php', get_site_option( 'active_sitewide_plugins' ), true );
		if ( current_user_can( 'activate_plugins' ) && true === $szgs_active ) {
			add_action( $config['network_active'] ? 'network_admin_notices' : 'admin_notices', 'szets_remove_plugin_admin_notice' );
		}
		if ( !sz_is_active( 'events' ) ) {
			add_action( $config['network_active'] ? 'network_admin_notices' : 'admin_notices', 'szet_plugin_require_event_component_admin_notice' );
		}
		
		// Display a warning message in network admin or admin
	} 
	return true;
}

function szet_same_blog(){
	echo '<div class="error"><p>'
	. esc_html( __( 'SportsZone Create Event Types requires to be activated on the blog where SportsZone is activated.', 'sz-add-event-types' ) )
	. '</p></div>';
}

function szet_same_network_config(){
	echo '<div class="error"><p>'
	. esc_html( __( 'SportsZone Create Event Types and SportsZone need to share the same network configuration.', 'sz-add-event-types' ) )
	. '</p></div>';
}
/**
 * Function to through admin notice if SportsZone Event Type Search is active.
 */
function szets_remove_plugin_admin_notice() {
	$szet_plugin  = 'SportsZone Create Event Types';
	$szets_plugin = 'SportsZone Event Type Search';

	echo '<div class="error"><p>'
	. sprintf( esc_html( __( '%1$s do not require %2$s to be installed and active as it contains functions of %3$s plugin.', 'sz-add-event-types' ) ), '<strong>' . esc_html( $szet_plugin ) . '</strong>', '<strong>' . esc_html( $szets_plugin ) . '</strong>', '<strong>' . esc_html( $szets_plugin ) . '</strong>' )
	. '</p></div>';
}

/**
 * Function to through admin notice if SportsZone is not active.
 */
function szet_plugin_admin_notice() {
	$szet_plugin = 'SportsZone Create Event Types';
	$sz_plugin   = 'SportsZone';
	echo '<div class="error"><p>'
	. sprintf( esc_html( __( '%1$s is ineffective as it requires %2$s to be installed and active.', 'sz-add-event-types' ) ), '<strong>' . esc_html( $szet_plugin ) . '</strong>', '<strong>' . esc_html( $sz_plugin ) . '</strong>' )
	. '</p></div>';
	if ( null !== filter_input( INPUT_GET, 'activate' ) ) {
		$activate = filter_input( INPUT_GET, 'activate' );
			unset( $activate );
	}
}

/**
 * Function to through admin notice if SportsZone event components is not active.
 */
function szet_plugin_require_event_component_admin_notice() {
	$szet_plugin  = 'SportsZone Create Event Types';
	$sz_component = 'Events Component';
		if( !sz_is_active( 'events' ) ){
			echo '<div class="error"><p>'
		. sprintf( esc_html( __( '%1$s is ineffective now as it requires %2$s to be active.', 'sz-add-event-types' ) ), '<strong>' . esc_html( $szet_plugin ) . '</strong>', '<strong>' . esc_html( $sz_component ) . '</strong>' )
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
function szet_plugin_links( $links ) {
	$szet_links = array(
		'<a href="' . admin_url( 'admin.php?page=sz-add-event-types' ) . '">' . __( 'Settings', 'sz-add-event-types' ) . '</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank">' . __( 'Support', 'sz-add-event-types' ) . '</a>',
	);
	return array_merge( $links, $szet_links );
}
