<?php
/**
 * The file that defines the global variable of the plugin
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    SZ_Add_Event_Types
 * @subpackage SZ_Add_Event_Types/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */

/**
 * The file that defines the global variable of the plugin
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    SZ_Add_Event_Types
 * @subpackage SZ_Add_Event_Types/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class SZ_Add_Event_Types_Globals {
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
	 * Whether the event types appear as pre selected.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $event_types_pre_selected
	 */
	public $event_types_pre_selected;

	/**
	 * Enable the event type search functionality on front-end.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $event_type_search_enabled
	 */
	public $event_type_search_enabled;

	/**
	 * List of all the saved event types
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $event_types
	 */
	public $event_types;

	/**
	 * The change in the search template
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $event_type_search_template
	 */
	public $event_type_search_template;

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
		$this->setup_plugin_global();
	}

	/**
	 * Include the following files that make up the plugin:
	 *
	 * - SZ_Add_Event_Types_Globals.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function setup_plugin_global() {
		global $sz_evt_types;

		$szet_settings = get_site_option( 'szet_general_settings' );

		$this->event_types_pre_selected = 'no';
		if ( isset( $szet_settings['event_types_pre_selected'] ) ) {
			$this->event_types_pre_selected = $szet_settings['event_types_pre_selected'];
		}

		$this->event_type_search_enabled = 'no';
		if ( isset( $szet_settings['event_type_search_enabled'] ) ) {
			$this->event_type_search_enabled = $szet_settings['event_type_search_enabled'];
		}

		$this->event_types = array();
		$event_types       = get_site_option( 'szet_event_types' );
		if ( ! empty( $event_types ) ) {
			$this->event_types = $event_types;
		}

		$szet_search_settings             =get_site_option( 'szet_event_type_search_settings' );
		$this->event_type_search_template = 'both';
		if ( isset( $szet_search_settings['event_type_search_template'] ) ) {
			$this->event_type_search_template = $szet_search_settings['event_type_search_template'];
		}

		$szet_type_display_settings = get_site_option( 'szet_type_display_settings' );

		if ( ! is_array( $szet_type_display_settings ) && empty( $szet_type_display_settings ) ) {
			$dis_event_types      = $this->event_types;
			$display_default_type = array();
			$count                = 0;
			foreach ( $dis_event_types as $type ) {
				if ( $count < 2 ) {
					array_push( $display_default_type, $type['slug'] );
					$count++;
				}
			}
			if ( $count > 0 ) {
				update_site_option( 'szet_type_display_settings', $display_default_type );
			}
		}
	}
}
