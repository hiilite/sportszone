<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main SportsZone Class.
 *
 * Tap tap tap... Is this thing on?
 *
 * @since 1.6.0
 */
class SportsZone {

	/** Magic *****************************************************************/

	/**
	 * SportsZone uses many variables, most of which can be filtered to
	 * customize the way that it works. To prevent unauthorized access,
	 * these variables are stored in a private array that is magically
	 * updated using PHP 5.2+ methods. This is to prevent third party
	 * plugins from tampering with essential information indirectly, which
	 * would cause issues later.
	 *
	 * @see SportsZone::setup_globals()
	 * @var array
	 */
	private $data;

	/** Not Magic *************************************************************/

	/**
	 * @var array Primary SportsZone navigation.
	 */
	public $sz_nav = array();

	/**
	 * @var array Secondary SportsZone navigation to $sz_nav.
	 */
	public $sz_options_nav = array();

	/**
	 * @var array The unfiltered URI broken down into chunks.
	 * @see sz_core_set_uri_globals()
	 */
	public $unfiltered_uri = array();

	/**
	 * @var array The canonical URI stack.
	 * @see sz_redirect_canonical()
	 * @see sz_core_new_nav_item()
	 */
	public $canonical_stack = array();

	/**
	 * @var array Additional navigation elements (supplemental).
	 */
	public $action_variables = array();

	/**
	 * @var string Current member directory type.
	 */
	public $current_member_type = '';

	/**
	 * @var array Required components (core, members).
	 */
	public $required_components = array();

	/**
	 * @var array Additional active components.
	 */
	public $loaded_components = array();

	/**
	 * @var array Active components.
	 */
	public $active_components = array();

	/**
	 * Whether autoload is in use.
	 *
	 * @since 2.5.0
	 * @var bool
	 */
	public $do_autoload = true;

	/** Option Overload *******************************************************/

	/**
	 * @var array Optional Overloads default options retrieved from get_option().
	 */
	public $options = array();

	/** Singleton *************************************************************/

	/**
	 * Main SportsZone Instance.
	 *
	 * SportsZone is great.
	 * Please load it only one time.
	 * For this, we thank you.
	 *
	 * Insures that only one instance of SportsZone exists in memory at any
	 * one time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.7.0
	 *
	 * @static object $instance
	 * @see sportszone()
	 *
	 * @return SportsZone|null The one true SportsZone.
	 */
	public static function instance() {

		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance ) {
			$instance = new SportsZone;
			$instance->constants();
			$instance->setup_globals();
			$instance->legacy_constants();
			$instance->includes();
			$instance->setup_actions();
		}

		// Always return the instance
		return $instance;

		// The last metroid is in captivity. The galaxy is at peace.
	}

	/** Magic Methods *********************************************************/

	/**
	 * A dummy constructor to prevent SportsZone from being loaded more than once.
	 *
	 * @since 1.7.0
	 * @see SportsZone::instance()
	 * @see sportszone()
	 */
	private function __construct() { /* Do nothing here */ }

	/**
	 * A dummy magic method to prevent SportsZone from being cloned.
	 *
	 * @since 1.7.0
	 */
	public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'sportszone' ), '1.7' ); }

	/**
	 * A dummy magic method to prevent SportsZone from being unserialized.
	 *
	 * @since 1.7.0
	 */
	public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'sportszone' ), '1.7' ); }

	/**
	 * Magic method for checking the existence of a certain custom field.
	 *
	 * @since 1.7.0
	 *
	 * @param string $key Key to check the set status for.
	 *
	 * @return bool
	 */
	public function __isset( $key ) { return isset( $this->data[$key] ); }

	/**
	 * Magic method for getting SportsZone variables.
	 *
	 * @since 1.7.0
	 *
	 * @param string $key Key to return the value for.
	 *
	 * @return mixed
	 */
	public function __get( $key ) { return isset( $this->data[$key] ) ? $this->data[$key] : null; }

	/**
	 * Magic method for setting SportsZone variables.
	 *
	 * @since 1.7.0
	 *
	 * @param string $key   Key to set a value for.
	 * @param mixed  $value Value to set.
	 */
	public function __set( $key, $value ) { $this->data[$key] = $value; }

	/**
	 * Magic method for unsetting SportsZone variables.
	 *
	 * @since 1.7.0
	 *
	 * @param string $key Key to unset a value for.
	 */
	public function __unset( $key ) { if ( isset( $this->data[$key] ) ) unset( $this->data[$key] ); }

	/**
	 * Magic method to prevent notices and errors from invalid method calls.
	 *
	 * @since 1.7.0
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return null
	 */
	public function __call( $name = '', $args = array() ) { unset( $name, $args ); return null; }

	/** Private Methods *******************************************************/

	/**
	 * Bootstrap constants.
	 *
	 * @since 1.6.0
	 *
	 */
	private function constants() {

		// Place your custom code (actions/filters) in a file called
		// '/plugins/sz-custom.php' and it will be loaded before anything else.
		if ( file_exists( WP_PLUGIN_DIR . '/sz-custom.php' ) ) {
			require( WP_PLUGIN_DIR . '/sz-custom.php' );
		}

		// Path and URL
		if ( ! defined( 'SZ_PLUGIN_DIR' ) ) {
			define( 'SZ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		if ( ! defined( 'SZ_PLUGIN_URL' ) ) {
			define( 'SZ_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Legacy forum constant - supported for compatibility with bbPress 2.
		if ( ! defined( 'SZ_FORUMS_PARENT_FORUM_ID' ) ) {
			define( 'SZ_FORUMS_PARENT_FORUM_ID', 1 );
		}

		// Legacy forum constant - supported for compatibility with bbPress 2.
		if ( ! defined( 'SZ_FORUMS_SLUG' ) ) {
			define( 'SZ_FORUMS_SLUG', 'forums' );
		}

		// Only applicable to those running trunk
		if ( ! defined( 'SZ_SOURCE_SUBDIRECTORY' ) ) {
			define( 'SZ_SOURCE_SUBDIRECTORY', '' );
		}

		// Define on which blog ID SportsZone should run
		if ( ! defined( 'SZ_ROOT_BLOG' ) ) {

			// Default to use current blog ID
			// Fulfills non-network installs and SZ_ENABLE_MULTIBLOG installs
			$root_blog_id = get_current_blog_id();

			// Multisite check
			if ( is_multisite() ) {

				// Multiblog isn't enabled
				if ( ! defined( 'SZ_ENABLE_MULTIBLOG' ) || ( defined( 'SZ_ENABLE_MULTIBLOG' ) && (int) constant( 'SZ_ENABLE_MULTIBLOG' ) === 0 ) ) {
					// Check to see if BP is network-activated
					// We're not using is_plugin_active_for_network() b/c you need to include the
					// /wp-admin/includes/plugin.php file in order to use that function.

					// get network-activated plugins
					$plugins = get_site_option( 'active_sitewide_plugins');

					// basename
					$basename = basename( constant( 'SZ_PLUGIN_DIR' ) ) . '/sz-loader.php';

					// plugin is network-activated; use main site ID instead
					if ( isset( $plugins[ $basename ] ) ) {
						$current_site = get_current_site();
						$root_blog_id = $current_site->blog_id;
					}
				}

			}

			define( 'SZ_ROOT_BLOG', $root_blog_id );
		}

		// The search slug has to be defined nice and early because of the way
		// search requests are loaded
		//
		// @todo Make this better
		if ( ! defined( 'SZ_SEARCH_SLUG' ) ) {
			define( 'SZ_SEARCH_SLUG', 'search' );
		}
	}

	/**
	 * Component global variables.
	 *
	 * @since 1.6.0
	 *
	 */
	private function setup_globals() {

		/** Versions **********************************************************/

		$this->version    = '3.1.0';
		$this->db_version = 11105;

		/** Loading ***********************************************************/

		/**
		 * Should deprecated code be loaded?
		 *
		 * @since 2.0.0 Defaults to false always
		 * @since 2.8.0 Defaults to true on upgrades, false for new installs.
		 */
		$this->load_deprecated = false;

		/** Toolbar ***********************************************************/

		/**
		 * @var string The primary toolbar ID.
		 */
		$this->my_account_menu_id = '';

		/** URIs **************************************************************/

		/**
		 * @var int The current offset of the URI.
		 * @see sz_core_set_uri_globals()
		 */
		$this->unfiltered_uri_offset = 0;

		/**
		 * @var bool Are status headers already sent?
		 */
		$this->no_status_set = false;

		/** Components ********************************************************/

		/**
		 * @var string Name of the current SportsZone component (primary).
		 */
		$this->current_component = '';

		/**
		 * @var string Name of the current SportsZone item (secondary).
		 */
		$this->current_item = '';

		/**
		 * @var string Name of the current SportsZone action (tertiary).
		 */
		$this->current_action = '';

		/**
		 * @var bool Displaying custom 2nd level navigation menu (I.E a group).
		 */
		$this->is_single_item = false;

		/** Root **************************************************************/

		/**
		 * Filters the SportsZone Root blog ID.
		 *
		 * @since 1.5.0
		 *
		 * @const constant SZ_ROOT_BLOG SportsZone Root blog ID.
		 */
		$this->root_blog_id = (int) apply_filters( 'sz_get_root_blog_id', SZ_ROOT_BLOG );

		/** Paths**************************************************************/

		// SportsZone root directory
		$this->file           = constant( 'SZ_PLUGIN_DIR' ) . 'sz-loader.php';
		$this->basename       = basename( constant( 'SZ_PLUGIN_DIR' ) ) . '/sz-loader.php';
		$this->plugin_dir     = trailingslashit( constant( 'SZ_PLUGIN_DIR' ) . constant( 'SZ_SOURCE_SUBDIRECTORY' ) );
		$this->plugin_url     = trailingslashit( constant( 'SZ_PLUGIN_URL' ) . constant( 'SZ_SOURCE_SUBDIRECTORY' ) );

		// Languages
		$this->lang_dir       = $this->plugin_dir . 'sz-languages';

		// Templates (theme compatibility)
		$this->themes_dir     = $this->plugin_dir . 'sz-templates';
		$this->themes_url     = $this->plugin_url . 'sz-templates';

		// Themes (for sz-default)
		$this->old_themes_dir = $this->plugin_dir . 'sz-themes';
		$this->old_themes_url = $this->plugin_url . 'sz-themes';

		/** Theme Compat ******************************************************/

		$this->theme_compat   = new stdClass(); // Base theme compatibility class
		$this->filters        = new stdClass(); // Used when adding/removing filters

		/** Users *************************************************************/

		$this->current_user   = new stdClass();
		$this->displayed_user = new stdClass();

		/** Post types and taxonomies *****************************************/

		/**
		 * Filters the post type slug for the email component.
		 *
		 * since 2.5.0
		 *
		 * @param string $value Email post type slug.
		 */
		$this->email_post_type     = apply_filters( 'sz_email_post_type', 'sz-email' );

		/**
		 * Filters the taxonomy slug for the email type component.
		 *
		 * @since 2.5.0
		 *
		 * @param string $value Email type taxonomy slug.
		 */
		$this->email_taxonomy_type = apply_filters( 'sz_email_tax_type', 'sz-email-type' );
	}

	/**
	 * Legacy SportsZone constants.
	 *
	 * Try to avoid using these. Their values have been moved into variables
	 * in the instance, and have matching functions to get/set their values.
	 *
	 * @since 1.7.0
	 */
	private function legacy_constants() {

		// Define the SportsZone version
		if ( ! defined( 'SZ_VERSION' ) ) {
			define( 'SZ_VERSION', $this->version );
		}

		// Define the database version
		if ( ! defined( 'SZ_DB_VERSION' ) ) {
			define( 'SZ_DB_VERSION', $this->db_version );
		}

		// Define if deprecated functions should be ignored
		if ( ! defined( 'SZ_IGNORE_DEPRECATED' ) ) {
			define( 'SZ_IGNORE_DEPRECATED', true );
		}
	}

	/**
	 * Include required files.
	 *
	 * @since 1.6.0
	 *
	 */
	private function includes() {
		spl_autoload_register( array( $this, 'autoload' ) );

		// Load the WP abstraction file so SportsZone can run on all WordPress setups.
		require( $this->plugin_dir . 'sz-core/sz-core-wpabstraction.php' );

		// Setup the versions (after we include multisite abstraction above)
		$this->versions();

		/** Update/Install ****************************************************/

		// Theme compatibility
		require( $this->plugin_dir . 'sz-core/sz-core-template-loader.php'     );
		require( $this->plugin_dir . 'sz-core/sz-core-theme-compatibility.php' );

		// Require all of the SportsZone core libraries
		require( $this->plugin_dir . 'sz-core/sz-core-dependency.php'       );
		require( $this->plugin_dir . 'sz-core/sz-core-actions.php'          );
		require( $this->plugin_dir . 'sz-core/sz-core-caps.php'             );
		require( $this->plugin_dir . 'sz-core/sz-core-cache.php'            );
		require( $this->plugin_dir . 'sz-core/sz-core-cssjs.php'            );
		require( $this->plugin_dir . 'sz-core/sz-core-update.php'           );
		require( $this->plugin_dir . 'sz-core/sz-core-options.php'          );
		require( $this->plugin_dir . 'sz-core/sz-core-taxonomy.php'         );
		require( $this->plugin_dir . 'sz-core/sz-core-filters.php'          );
		require( $this->plugin_dir . 'sz-core/sz-core-attachments.php'      );
		require( $this->plugin_dir . 'sz-core/sz-core-avatars.php'          );
		require( $this->plugin_dir . 'sz-core/sz-core-cover-images.php'     );
		require( $this->plugin_dir . 'sz-core/sz-core-widgets.php'          );
		require( $this->plugin_dir . 'sz-core/sz-core-template.php'         );
		require( $this->plugin_dir . 'sz-core/sz-core-adminbar.php'         );
		require( $this->plugin_dir . 'sz-core/sz-core-buddybar.php'         );
		require( $this->plugin_dir . 'sz-core/sz-core-catchuri.php'         );
		require( $this->plugin_dir . 'sz-core/sz-core-functions.php'        );
		require( $this->plugin_dir . 'sz-core/sz-core-moderation.php'       );
		require( $this->plugin_dir . 'sz-core/sz-core-loader.php'           );
		require( $this->plugin_dir . 'sz-core/sz-core-customizer-email.php' );

		// Maybe load deprecated functionality (this double negative is proof positive!)
		if ( ! sz_get_option( '_sz_ignore_deprecated_code', ! $this->load_deprecated ) ) {
			require( $this->plugin_dir . 'sz-core/deprecated/1.2.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/1.5.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/1.6.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/1.7.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/1.9.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/2.0.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/2.1.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/2.2.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/2.3.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/2.4.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/2.5.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/2.6.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/2.7.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/2.8.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/2.9.php' );
			require( $this->plugin_dir . 'sz-core/deprecated/3.0.php' );
		}

		if ( defined( 'WP_CLI' ) && file_exists( $this->plugin_dir . 'cli/wp-cli-bp.php' ) ) {
			require( $this->plugin_dir . 'cli/wp-cli-bp.php' );
		}
	}

	/**
	 * Autoload classes.
	 *
	 * @since 2.5.0
	 *
	 * @param string $class
	 */
	public function autoload( $class ) {
		$class_parts = explode( '_', strtolower( $class ) );

		if ( 'sz' !== $class_parts[0] ) {
			return;
		}

		$components = array(
			'activity',
			'blogs',
			'core',
			'friends',
			'groups',
			'members',
			'messages',
			'notifications',
			'settings',
			'xprofile',
		);

		// These classes don't have a name that matches their component.
		$irregular_map = array(
			'SZ_Akismet' => 'activity',

			'SZ_Admin'                     => 'core',
			'SZ_Attachment_Avatar'         => 'core',
			'SZ_Attachment_Cover_Image'    => 'core',
			'SZ_Attachment'                => 'core',
			'SZ_Button'                    => 'core',
			'SZ_Component'                 => 'core',
			'SZ_Customizer_Control_Range'  => 'core',
			'SZ_Date_Query'                => 'core',
			'SZ_Email_Delivery'            => 'core',
			'SZ_Email_Recipient'           => 'core',
			'SZ_Email'                     => 'core',
			'SZ_Embed'                     => 'core',
			'SZ_Media_Extractor'           => 'core',
			'SZ_Members_Suggestions'       => 'core',
			'SZ_PHPMailer'                 => 'core',
			'SZ_Recursive_Query'           => 'core',
			'SZ_Suggestions'               => 'core',
			'SZ_Theme_Compat'              => 'core',
			'SZ_User_Query'                => 'core',
			'SZ_Walker_Category_Checklist' => 'core',
			'SZ_Walker_Nav_Menu_Checklist' => 'core',
			'SZ_Walker_Nav_Menu'           => 'core',

			'SZ_Core_Friends_Widget' => 'friends',

			'SZ_Group_Extension'    => 'groups',
			'SZ_Group_Member_Query' => 'groups',

			'SZ_Core_Members_Template'       => 'members',
			'SZ_Core_Members_Widget'         => 'members',
			'SZ_Core_Recently_Active_Widget' => 'members',
			'SZ_Core_Whos_Online_Widget'     => 'members',
			'SZ_Registration_Theme_Compat'   => 'members',
			'SZ_Signup'                      => 'members',
		);

		$component = null;

		// First check to see if the class is one without a properly namespaced name.
		if ( isset( $irregular_map[ $class ] ) ) {
			$component = $irregular_map[ $class ];

		// Next chunk is usually the component name.
		} elseif ( in_array( $class_parts[1], $components, true ) ) {
			$component = $class_parts[1];
		}

		if ( ! $component ) {
			return;
		}

		// Sanitize class name.
		$class = strtolower( str_replace( '_', '-', $class ) );

		$path = dirname( __FILE__ ) . "/sz-{$component}/classes/class-{$class}.php";

		// Sanity check.
		if ( ! file_exists( $path ) ) {
			return;
		}

		/*
		 * Sanity check 2 - Check if component is active before loading class.
		 * Skip if PHPUnit is running, or SportsZone is installing for the first time.
		 */
		if (
			! in_array( $component, array( 'core', 'members' ), true ) &&
			! sz_is_active( $component ) &&
			! function_exists( 'tests_add_filter' )
		) {
			return;
		}

		require $path;
	}

	/**
	 * Set up the default hooks and actions.
	 *
	 * @since 1.6.0
	 *
	 */
	private function setup_actions() {

		// Add actions to plugin activation and deactivation hooks
		add_action( 'activate_'   . $this->basename, 'sz_activation'   );
		add_action( 'deactivate_' . $this->basename, 'sz_deactivation' );

		// If SportsZone is being deactivated, do not add any actions
		if ( sz_is_deactivation( $this->basename ) ) {
			return;
		}

		// Array of SportsZone core actions
		$actions = array(
			'setup_theme',              // Setup the default theme compat
			'setup_current_user',       // Setup currently logged in user
			'register_post_types',      // Register post types
			'register_post_statuses',   // Register post statuses
			'register_taxonomies',      // Register taxonomies
			'register_views',           // Register the views
			'register_theme_directory', // Register the theme directory
			'register_theme_packages',  // Register bundled theme packages (sz-themes)
			'load_textdomain',          // Load textdomain
			'add_rewrite_tags',         // Add rewrite tags
			'generate_rewrite_rules'    // Generate rewrite rules
		);

		// Add the actions
		foreach( $actions as $class_action ) {
			if ( method_exists( $this, $class_action ) ) {
				add_action( 'sz_' . $class_action, array( $this, $class_action ), 5 );
			}
		}

		/**
		 * Fires after the setup of all SportsZone actions.
		 *
		 * Includes bsz-core-hooks.php.
		 *
		 * @since 1.7.0
		 *
		 * @param SportsZone $this. Current SportsZone instance. Passed by reference.
		 */
		do_action_ref_array( 'sz_after_setup_actions', array( &$this ) );
	}

	/**
	 * Private method to align the active and database versions.
	 *
	 * @since 1.7.0
	 */
	private function versions() {

		// Get the possible DB versions (boy is this gross)
		$versions               = array();
		$versions['1.6-single'] = get_blog_option( $this->root_blog_id, '_sz_db_version' );

		// 1.6-single exists, so trust it
		if ( !empty( $versions['1.6-single'] ) ) {
			$this->db_version_raw = (int) $versions['1.6-single'];

		// If no 1.6-single exists, use the max of the others
		} else {
			$versions['1.2']        = get_site_option(                      'sz-core-db-version' );
			$versions['1.5-multi']  = get_site_option(                           'sz-db-version' );
			$versions['1.6-multi']  = get_site_option(                          '_sz_db_version' );
			$versions['1.5-single'] = get_blog_option( $this->root_blog_id,      'sz-db-version' );

			// Remove empty array items
			$versions             = array_filter( $versions );
			$this->db_version_raw = (int) ( !empty( $versions ) ) ? (int) max( $versions ) : 0;
		}
	}

	/** Public Methods ********************************************************/

	/**
	 * Set up SportsZone's legacy theme directory.
	 *
	 * Starting with version 1.2, and ending with version 1.8, SportsZone
	 * registered a custom theme directory - sz-themes - which contained
	 * the sz-default theme. Since SportsZone 1.9, sz-themes is no longer
	 * registered (and sz-default no longer offered) on new installations.
	 * Sites using sz-default (or a child theme of sz-default) will
	 * continue to have sz-themes registered as before.
	 *
	 * @since 1.5.0
	 *
	 * @todo Move sz-default to wordpress.org/extend/themes and remove this.
	 */
	public function register_theme_directory() {
		if ( ! sz_do_register_theme_directory() ) {
			return;
		}

		register_theme_directory( $this->old_themes_dir );
	}

	/**
	 * Register bundled theme packages.
	 *
	 * Note that since we currently have complete control over sz-themes and
	 * the sz-legacy folders, it's fine to hardcode these here. If at a
	 * later date we need to automate this, an API will need to be built.
	 *
	 * @since 1.7.0
	 */
	public function register_theme_packages() {

		// Register the default theme compatibility package
		sz_register_theme_package( array(
			'id'      => 'legacy',
			'name'    => __( 'SportsZone Legacy', 'sportszone' ),
			'version' => sz_get_version(),
			'dir'     => trailingslashit( $this->themes_dir . '/sz-legacy' ),
			'url'     => trailingslashit( $this->themes_url . '/sz-legacy' )
		) );

		sz_register_theme_package( array(
			'id'      => 'nouveau',
			'name'    => __( 'SportsZone Nouveau', 'sportszone' ),
			'version' => sz_get_version(),
			'dir'     => trailingslashit( $this->themes_dir . '/sz-nouveau' ),
			'url'     => trailingslashit( $this->themes_url . '/sz-nouveau' )
		) );

		// Register the basic theme stack. This is really dope.
		sz_register_template_stack( 'get_stylesheet_directory', 10 );
		sz_register_template_stack( 'get_template_directory',   12 );
		sz_register_template_stack( 'sz_get_theme_compat_dir',  14 );
	}

	/**
	 * Set up the default SportsZone theme compatibility location.
	 *
	 * @since 1.7.0
	 */
	public function setup_theme() {

		// Bail if something already has this under control
		if ( ! empty( $this->theme_compat->theme ) ) {
			return;
		}

		// Setup the theme package to use for compatibility
		sz_setup_theme_compat( sz_get_theme_package_id() );
	}
}
