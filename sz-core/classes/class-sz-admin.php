<?php
/**
 * Main SportsZone Admin Class.
 *
 * @package SportsZone
 * @subpackage CoreAdministration
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'SZ_Admin' ) ) :

/**
 * Load SportsZone plugin admin area.
 *
 * @todo Break this apart into each applicable Component.
 *
 * @since 1.6.0
 */
class SZ_Admin {

	/** Directory *************************************************************/

	/**
	 * Path to the SportsZone admin directory.
	 *
	 * @since 1.6.0
	 * @var string $admin_dir
	 */
	public $admin_dir = '';

	/** URLs ******************************************************************/

	/**
	 * URL to the SportsZone admin directory.
	 *
	 * @since 1.6.0
	 * @var string $admin_url
	 */
	public $admin_url = '';

	/**
	 * URL to the SportsZone images directory.
	 *
	 * @since 1.6.0
	 * @var string $images_url
	 */
	public $images_url = '';

	/**
	 * URL to the SportsZone admin CSS directory.
	 *
	 * @since 1.6.0
	 * @var string $css_url
	 */
	public $css_url = '';

	/**
	 * URL to the SportsZone admin JS directory.
	 *
	 * @since 1.6.0
	 * @var string
	 */
	public $js_url = '';

	/** Other *****************************************************************/

	/**
	 * Notices used for user feedback, like saving settings.
	 *
	 * @since 1.9.0
	 * @var array()
	 */
	public $notices = array();

	/** Methods ***************************************************************/

	/**
	 * The main SportsZone admin loader.
	 *
	 * @since 1.6.0
	 *
	 */
	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
	}

	/**
	 * Set admin-related globals.
	 *
	 * @since 1.6.0
	 */
	private function setup_globals() {
		$sz = sportszone();

		// Paths and URLs
		$this->admin_dir  = trailingslashit( $sz->plugin_dir  . 'sz-core/admin' ); // Admin path.
		$this->admin_url  = trailingslashit( $sz->plugin_url  . 'sz-core/admin' ); // Admin url.
		$this->images_url = trailingslashit( $this->admin_url . 'images'        ); // Admin images URL.
		$this->css_url    = trailingslashit( $this->admin_url . 'css'           ); // Admin css URL.
		$this->js_url     = trailingslashit( $this->admin_url . 'js'            ); // Admin css URL.

		// Main settings page.
		$this->settings_page = sz_core_do_network_admin() ? 'settings.php' : 'options-general.php';

		// Main capability.
		$this->capability = sz_core_do_network_admin() ? 'manage_network_options' : 'manage_options';
	}

	/**
	 * Include required files.
	 *
	 * @since 1.6.0
	 */
	private function includes() {
		require( $this->admin_dir . 'sz-core-admin-actions.php'    );
		require( $this->admin_dir . 'sz-core-admin-settings.php'   );
		require( $this->admin_dir . 'sz-core-admin-functions.php'  );
		require( $this->admin_dir . 'sz-core-admin-components.php' );
		require( $this->admin_dir . 'sz-core-admin-slugs.php'      );
		require( $this->admin_dir . 'sz-core-admin-tools.php'      );
	}

	/**
	 * Set up the admin hooks, actions, and filters.
	 *
	 * @since 1.6.0
	 *
	 */
	private function setup_actions() {

		/* General Actions ***************************************************/

		// Add some page specific output to the <head>.
		add_action( 'sz_admin_head',            array( $this, 'admin_head'  ), 999 );

		// Add menu item to settings menu.
		add_action( 'admin_menu',               array( $this, 'site_admin_menus' ), 5 );
		add_action( sz_core_admin_hook(),       array( $this, 'admin_menus' ), 5 );

		// Enqueue all admin JS and CSS.
		add_action( 'sz_admin_enqueue_scripts', array( $this, 'admin_register_styles' ), 1 );
		add_action( 'sz_admin_enqueue_scripts', array( $this, 'admin_register_scripts' ), 1 );
		add_action( 'sz_admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* SportsZone Actions ************************************************/

		// Load the SportsZone metabox in the WP Nav Menu Admin UI.
		add_action( 'load-nav-menus.php', 'sz_admin_wp_nav_menu_meta_box' );

		// Add settings.
		add_action( 'sz_register_admin_settings', array( $this, 'register_admin_settings' ) );

		// Add a link to SportsZone Hello in the admin bar.
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_about_link' ), 100 );

		// Add a description of new SportsZone tools in the available tools page.
		add_action( 'tool_box',            'sz_core_admin_available_tools_intro' );
		add_action( 'sz_network_tool_box', 'sz_core_admin_available_tools_intro' );

		// On non-multisite, catch.
		add_action( 'load-users.php', 'sz_core_admin_user_manage_spammers' );

		// Emails.
		add_filter( 'manage_' . sz_get_email_post_type() . '_posts_columns',       array( $this, 'emails_register_situation_column' ) );
		add_action( 'manage_' . sz_get_email_post_type() . '_posts_custom_column', array( $this, 'emails_display_situation_column_data' ), 10, 2 );

		// SportsZone Hello.
		add_action( 'admin_footer', array( $this, 'about_screen' ) );

		/* Filters ***********************************************************/

		// Add link to settings page.
		add_filter( 'plugin_action_links',               array( $this, 'modify_plugin_action_links' ), 10, 2 );
		add_filter( 'network_admin_plugin_action_links', array( $this, 'modify_plugin_action_links' ), 10, 2 );

		// Add "Mark as Spam" row actions on users.php.
		add_filter( 'ms_user_row_actions', 'sz_core_admin_user_row_actions', 10, 2 );
		add_filter( 'user_row_actions',    'sz_core_admin_user_row_actions', 10, 2 );

		// Emails
		add_filter( 'sz_admin_menu_order', array( $this, 'emails_admin_menu_order' ), 20 );
	}

	/**
	 * Register site- or network-admin nav menu elements.
	 *
	 * Contextually hooked to site or network-admin depending on current configuration.
	 *
	 * @since 1.6.0
	 */
	public function admin_menus() {

		// Bail if user cannot moderate.
		if ( ! sz_current_user_can( 'manage_options' ) ) {
			return;
		}

		$hooks = array();

		// Changed in BP 1.6 . See sz_core_admin_backpat_menu().
		$hooks[] = add_menu_page(
			__( 'SportsZone', 'sportszone' ),
			__( 'SportsZone', 'sportszone' ),
			$this->capability,
			'sz-general-settings',
			'sz_core_admin_backpat_menu',
			'div'
		);

		$hooks[] = add_submenu_page(
			'sz-general-settings',
			__( 'SportsZone Help', 'sportszone' ),
			__( 'Help', 'sportszone' ),
			$this->capability,
			'sz-general-settings',
			'sz_core_admin_backpat_page'
		);

		// Add the option pages.
		$hooks[] = add_submenu_page(
			$this->settings_page,
			__( 'SportsZone Components', 'sportszone' ),
			__( 'SportsZone', 'sportszone' ),
			$this->capability,
			'sz-components',
			'sz_core_admin_components_settings'
		);

		$hooks[] = add_submenu_page(
			$this->settings_page,
			__( 'SportsZone Pages', 'sportszone' ),
			__( 'SportsZone Pages', 'sportszone' ),
			$this->capability,
			'sz-page-settings',
			'sz_core_admin_slugs_settings'
		);

		$hooks[] = add_submenu_page(
			$this->settings_page,
			__( 'SportsZone Options', 'sportszone' ),
			__( 'SportsZone Options', 'sportszone' ),
			$this->capability,
			'sz-settings',
			'sz_core_admin_settings'
		);

		// Credits.
		$hooks[] = add_submenu_page(
			$this->settings_page,
			__( 'SportsZone Credits', 'sportszone' ),
			__( 'SportsZone Credits', 'sportszone' ),
			$this->capability,
			'sz-credits',
			array( $this, 'credits_screen' )
		);

		// For consistency with non-Multisite, we add a Tools menu in
		// the Network Admin as a home for our Tools panel.
		if ( is_multisite() && sz_core_do_network_admin() ) {
			$tools_parent = 'network-tools';

			$hooks[] = add_menu_page(
				__( 'Tools', 'sportszone' ),
				__( 'Tools', 'sportszone' ),
				$this->capability,
				$tools_parent,
				'sz_core_tools_top_level_item',
				'',
				24 // Just above Settings.
			);

			$hooks[] = add_submenu_page(
				$tools_parent,
				__( 'Available Tools', 'sportszone' ),
				__( 'Available Tools', 'sportszone' ),
				$this->capability,
				'available-tools',
				'sz_core_admin_available_tools_page'
			);
		} else {
			$tools_parent = 'tools.php';
		}

		$hooks[] = add_submenu_page(
			$tools_parent,
			__( 'SportsZone Tools', 'sportszone' ),
			__( 'SportsZone', 'sportszone' ),
			$this->capability,
			'sz-tools',
			'sz_core_admin_tools'
		);

		// For network-wide configs, add a link to (the root site's) Emails screen.
		if ( is_network_admin() && sz_is_network_activated() ) {
			$email_labels = sz_get_email_post_type_labels();
			$email_url    = get_admin_url( sz_get_root_blog_id(), 'edit.php?post_type=' . sz_get_email_post_type() );

			$hooks[] = add_menu_page(
				$email_labels['name'],
				$email_labels['menu_name'],
				$this->capability,
				'',
				'',
				'dashicons-email',
				26
			);

			// Hack: change the link to point to the root site's admin, not the network admin.
			$GLOBALS['menu'][26][2] = esc_url_raw( $email_url );
		}

		foreach( $hooks as $hook ) {
			add_action( "admin_head-$hook", 'sz_core_modify_admin_menu_highlight' );
		}
	}

	/**
	 * Register site-admin nav menu elements.
	 *
	 * @since 2.5.0
	 */
	public function site_admin_menus() {
		if ( ! sz_current_user_can( 'manage_options' ) ) {
			return;
		}

		$hooks = array();

		// Appearance > Emails.
		$hooks[] = add_theme_page(
			_x( 'Emails', 'screen heading', 'sportszone' ),
			_x( 'Emails', 'screen heading', 'sportszone' ),
			$this->capability,
			'sz-emails-customizer-redirect',
			'sz_email_redirect_to_customizer'
		);

		// Emails > Customize.
		$hooks[] = add_submenu_page(
			'edit.php?post_type=' . sz_get_email_post_type(),
			_x( 'Customize', 'email menu label', 'sportszone' ),
			_x( 'Customize', 'email menu label', 'sportszone' ),
			$this->capability,
			'sz-emails-customizer-redirect',
			'sz_email_redirect_to_customizer'
		);

		foreach( $hooks as $hook ) {
			add_action( "admin_head-$hook", 'sz_core_modify_admin_menu_highlight' );
		}
	}

	/**
	 * Register the settings.
	 *
	 * @since 1.6.0
	 *
	 */
	public function register_admin_settings() {

		/* Main Section ******************************************************/

		// Add the main section.
		add_settings_section( 'sz_main', __( 'Main Settings', 'sportszone' ), 'sz_admin_setting_callback_main_section', 'sportszone' );

		// Hide toolbar for logged out users setting.
		add_settings_field( 'hide-loggedout-adminbar', __( 'Toolbar', 'sportszone' ), 'sz_admin_setting_callback_admin_bar', 'sportszone', 'sz_main' );
		register_setting( 'sportszone', 'hide-loggedout-adminbar', 'intval' );

		// Only show 'switch to Toolbar' option if the user chose to retain the BuddyBar during the 1.6 upgrade.
		if ( (bool) sz_get_option( '_sz_force_buddybar', false ) ) {
			// Load deprecated code if not available.
			if ( ! function_exists( 'sz_admin_setting_callback_force_buddybar' ) ) {
				require sportszone()->plugin_dir . 'sz-core/deprecated/2.1.php';
			}

			add_settings_field( '_sz_force_buddybar', __( 'Toolbar', 'sportszone' ), 'sz_admin_setting_callback_force_buddybar', 'sportszone', 'sz_main' );
			register_setting( 'sportszone', '_sz_force_buddybar', 'sz_admin_sanitize_callback_force_buddybar' );
		}

		// Allow account deletion.
		add_settings_field( 'sz-disable-account-deletion', __( 'Account Deletion', 'sportszone' ), 'sz_admin_setting_callback_account_deletion', 'sportszone', 'sz_main' );
		register_setting( 'sportszone', 'sz-disable-account-deletion', 'intval' );

		// Template pack picker.
		add_settings_field( '_sz_theme_package_id', __( 'Template Pack', 'sportszone' ), 'sz_admin_setting_callback_theme_package_id', 'sportszone', 'sz_main', array( 'label_for' => '_sz_theme_package_id' ) );
		register_setting( 'sportszone', '_sz_theme_package_id', 'sanitize_text_field' );

		/* XProfile Section **************************************************/

		if ( sz_is_active( 'xprofile' ) ) {

			// Add the main section.
			add_settings_section( 'sz_xprofile', _x( 'Profile Settings', 'SportsZone setting tab', 'sportszone' ), 'sz_admin_setting_callback_xprofile_section', 'sportszone' );

			// Avatars.
			add_settings_field( 'sz-disable-avatar-uploads', __( 'Profile Photo Uploads', 'sportszone' ), 'sz_admin_setting_callback_avatar_uploads', 'sportszone', 'sz_xprofile' );
			register_setting( 'sportszone', 'sz-disable-avatar-uploads', 'intval' );

			// Cover images.
			if ( sz_is_active( 'xprofile', 'cover_image' ) ) {
				add_settings_field( 'sz-disable-cover-image-uploads', __( 'Cover Image Uploads', 'sportszone' ), 'sz_admin_setting_callback_cover_image_uploads', 'sportszone', 'sz_xprofile' );
				register_setting( 'sportszone', 'sz-disable-cover-image-uploads', 'intval' );
			}

			// Profile sync setting.
			add_settings_field( 'sz-disable-profile-sync',   __( 'Profile Syncing',  'sportszone' ), 'sz_admin_setting_callback_profile_sync', 'sportszone', 'sz_xprofile' );
			register_setting  ( 'sportszone', 'sz-disable-profile-sync', 'intval' );
		}

		/* Groups Section ****************************************************/

		if ( sz_is_active( 'groups' ) ) {

			// Add the main section.
			add_settings_section( 'sz_groups', __( 'Groups Settings',  'sportszone' ), 'sz_admin_setting_callback_groups_section', 'sportszone' );

			// Allow subscriptions setting.
			add_settings_field( 'sz_restrict_group_creation', __( 'Group Creation', 'sportszone' ), 'sz_admin_setting_callback_group_creation',   'sportszone', 'sz_groups' );
			register_setting( 'sportszone', 'sz_restrict_group_creation', 'intval' );

			// Allow group avatars.
			add_settings_field( 'sz-disable-group-avatar-uploads', __( 'Group Photo Uploads', 'sportszone' ), 'sz_admin_setting_callback_group_avatar_uploads', 'sportszone', 'sz_groups' );
			register_setting( 'sportszone', 'sz-disable-group-avatar-uploads', 'intval' );

			// Allow group cover images.
			if ( sz_is_active( 'groups', 'cover_image' ) ) {
				add_settings_field( 'sz-disable-group-cover-image-uploads', __( 'Group Cover Image Uploads', 'sportszone' ), 'sz_admin_setting_callback_group_cover_image_uploads', 'sportszone', 'sz_groups' );
				register_setting( 'sportszone', 'sz-disable-group-cover-image-uploads', 'intval' );
			}
		}

		/* Activity Section **************************************************/

		if ( sz_is_active( 'activity' ) ) {

			// Add the main section.
			add_settings_section( 'sz_activity', __( 'Activity Settings', 'sportszone' ), 'sz_admin_setting_callback_activity_section', 'sportszone' );

			// Activity commenting on post and comments.
			add_settings_field( 'sz-disable-blogforum-comments', __( 'Post Comments', 'sportszone' ), 'sz_admin_setting_callback_blogforum_comments', 'sportszone', 'sz_activity' );
			register_setting( 'sportszone', 'sz-disable-blogforum-comments', 'sz_admin_sanitize_callback_blogforum_comments' );

			// Activity Heartbeat refresh.
			add_settings_field( '_sz_enable_heartbeat_refresh', __( 'Activity auto-refresh', 'sportszone' ), 'sz_admin_setting_callback_heartbeat', 'sportszone', 'sz_activity' );
			register_setting( 'sportszone', '_sz_enable_heartbeat_refresh', 'intval' );

			// Allow activity akismet.
			if ( is_plugin_active( 'akismet/akismet.php' ) && defined( 'AKISMET_VERSION' ) ) {
				add_settings_field( '_sz_enable_akismet', __( 'Akismet', 'sportszone' ), 'sz_admin_setting_callback_activity_akismet', 'sportszone', 'sz_activity' );
				register_setting( 'sportszone', '_sz_enable_akismet', 'intval' );
			}
		}
	}

	/**
	 * Add a link to SportsZone Hello to the admin bar.
	 *
	 * @since 1.9.0
	 * @since 3.0.0 Hooked at priority 100 (was 15).
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	public function admin_bar_about_link( $wp_admin_bar ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$wp_admin_bar->add_menu( array(
			'parent' => 'wp-logo',
			'id'     => 'sz-about',
			'title'  => esc_html_x( 'Hello, SportsZone!', 'Colloquial alternative to "learn about SportsZone"', 'sportszone' ),
			'href'   => sz_get_admin_url( '?hello=sportszone' ),
			'meta'   => array(
				'class' => 'say-hello-sportszone',
			),
		) );
	}

	/**
	 * Add Settings link to plugins area.
	 *
	 * @since 1.6.0
	 *
	 * @param array  $links Links array in which we would prepend our link.
	 * @param string $file  Current plugin basename.
	 * @return array Processed links.
	 */
	public function modify_plugin_action_links( $links, $file ) {

		// Return normal links if not SportsZone.
		if ( plugin_basename( sportszone()->basename ) != $file ) {
			return $links;
		}

		// Add a few links to the existing links array.
		return array_merge( $links, array(
			'settings' => '<a href="' . esc_url( add_query_arg( array( 'page' => 'sz-components' ), sz_get_admin_url( $this->settings_page ) ) ) . '">' . esc_html__( 'Settings', 'sportszone' ) . '</a>',
			'about'    => '<a href="' . esc_url( sz_get_admin_url( '?hello=sportszone' ) ) . '">' . esc_html_x( 'Hello, SportsZone!', 'Colloquial alternative to "learn about SportsZone"', 'sportszone' ) . '</a>'
		) );
	}

	/**
	 * Add some general styling to the admin area.
	 *
	 * @since 1.6.0
	 */
	public function admin_head() {

		// Settings pages.
		remove_submenu_page( $this->settings_page, 'sz-page-settings' );
		remove_submenu_page( $this->settings_page, 'sz-settings'      );
		remove_submenu_page( $this->settings_page, 'sz-credits'       );

		// Network Admin Tools.
		remove_submenu_page( 'network-tools', 'network-tools' );

		// About and Credits pages.
		remove_submenu_page( 'index.php', 'sz-about'   );
		remove_submenu_page( 'index.php', 'sz-credits' );
	}

	/**
	 * Add some general styling to the admin area.
	 *
	 * @since 1.6.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'sz-admin-common-css' );

		// SportsZone Hello
		if ( 0 === strpos( get_current_screen()->id, 'dashboard' ) && ! empty( $_GET['hello'] ) && $_GET['hello'] === 'sportszone' ) {
			wp_enqueue_style( 'sz-hello-css' );
			wp_enqueue_script( 'sz-hello-js' );
		}
	}

	/** About *****************************************************************/

	/**
	 * Output the SportsZone Hello template.
	 *
	 * @since 1.7.0 Screen content.
	 * @since 3.0.0 Now outputs SportsZone Hello template.
	 */
	public function about_screen() {
		if ( 0 !== strpos( get_current_screen()->id, 'dashboard' ) || empty( $_GET['hello'] ) || $_GET['hello'] !== 'sportszone' ) {
			return;
		}
	?>

		<div id="sz-hello-backdrop" style="display: none;">
		</div>

		<div id="sz-hello-container" role="dialog" aria-labelledby="sz-hello-title" style="display: none;">
			<div class="sz-hello-header" role="document">
				<div class="sz-hello-close">
					<button type="button" class="close-modal button sz-tooltip" data-sz-tooltip="<?php echo esc_attr( 'Close pop-up', 'sportszone' ); ?>">
						<span class="screen-reader-text"><?php esc_html_e( 'Close pop-up', 'sportszone' ); ?></span>
					</button>
				</div>

				<div class="sz-hello-title">
					<h1 id="sz-hello-title" tabindex="-1"><?php esc_html_e( _x( 'New in SportsZone', 'section heading', 'sportszone' ) ); ?></h1>
				</div>
			</div>

			<div class="sz-hello-content">
				<h2><?php echo esc_html( _n( 'Maintenance Release', 'Maintenance Releases', 1, 'sportszone' ) ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: 1: SportsZone version number, 2: plural number of bugs. */
						_n(
							'<strong>Version %1$s</strong> addressed %2$s bug.',
							'<strong>Version %1$s</strong> addressed %2$s bugs.',
							23,
							'sportszone'
						),
						self::display_version(),
						number_format_i18n( 23 )
					);
					?>
				</p>

				<hr>
				<h2><?php esc_html_e( __( 'Say hello to &ldquo;Nouveau&rdquo;!', 'sportszone' ) ); ?></h2>
				<p>
					<?php
					printf(
						__( 'A bold reimagining of our legacy templates, Nouveau is our celebration of <a href="%s">10 years of SportsZone</a>! Nouveau delivers modern markup with fresh JavaScript-powered templates, and full integration with WordPress\' Customizer, allowing more out-of-the-box control of your SportsZone content than ever before.', 'sportszone' ),
						esc_url( 'https://sportszone.org/2018/03/10-years/' )
					);
					?>
				</p>
				<p><?php esc_html_e( 'Nouveau provides vertical and horizontal layout options for SportsZone navigation, and for the component directories, you can choose between a grid layout, and a classic flat list.', 'sportszone' ); ?></p>
				<p>
					<?php
					printf(
						__( 'Nouveau is fully compatible with WordPress. Existing SportsZone themes have been written for our legacy template pack, and until they are updated, resolve any compatibility issues by choosing the legacy template pack option in <a href="%s">Settings &gt; SportsZone</a>.', 'sportszone' ),
						esc_url( sz_get_admin_url( 'admin.php?page=sz-settings' ) )
					);
					?>
				</p>

				<?php echo $GLOBALS['wp_embed']->autoembed( 'https://player.vimeo.com/video/270507360' ); ?>

				<h2><?php esc_html_e( __( 'Support for WP-CLI', 'sportszone' ) ); ?></h2>
				<p>
					<?php
					printf(
						__( '<a href="%s">WP-CLI</a> is the command-line interface for WordPress. You can update plugins, configure multisite installs, and much more, without using a web browser. With this version of SportsZone, you can now manage your SportsZone content from WP-CLI.', 'sportszone' ),
						esc_url( 'https://wp-cli.org' )
					);
					?>
				</p>

				<h2><?php esc_html_e( _x( 'Control site-wide notices from your dashboard', 'section heading', 'sportszone' ) ); ?></h2>
				<p><?php esc_html_e( 'Site Notices are a feature within the Private Messaging component that allows community managers to share important messages with all members of their community. With Nouveau, the management interface for Site Notices has been removed from the front-end theme templates.', 'sportszone' ); ?></p>

				<?php if ( sz_is_active( 'messages' ) ) : ?>
				<p>
					<?php
					printf(
						__( 'Explore the new management interface at <a href="%s">Users &gt; Site Notices</a>.', 'sportszone' ),
						esc_url( sz_get_admin_url( 'users.php?page=sz-notices' ) )
					);
					?>
				</p>
				<?php endif; ?>

				<h2><?php esc_html_e( __( 'New profile field type: telephone numbers', 'sportszone' ) ); ?></h2>
				<p><?php esc_html_e( 'A new telephone number field type has been added to the Extended Profiles component, with support for all international number formats. With a modern web browser, your members can use this field type to touch-to-dial a number directly.', 'sportszone' ); ?></p>

				<h2><?php esc_html_e( __( "SportsZone: leaner, faster, stronger", 'sportszone' ) ); ?></h2>
				<p><?php esc_html_e( 'With every SportsZone version, we strive to make performance improvements alongside new features and fixes; this version is no exception. Memory use has been optimised &mdash; within active components, we now only load each individual code file when it\'s needed, not before.', 'sportszone' ); ?></p>
				<p>
					<?php
					printf(
						__( 'Most notably, the <a href="%s">Legacy Forums component has been removed</a> after 9 years of service. If your site was using Legacy Forums, you need to <a href="%s">migrate to the bbPress plugin</a>.', 'sportszone' ),
						esc_url( 'https://bpdevel.wordpress.com/2017/12/07/legacy-forums-support-will-be/' ),
						esc_url( 'https://codex.sportszone.org/getting-started/guides/migrating-from-old-forums-to-bbpress-2/' )
					);
					?>
				</p>

				<p><em>
					<?php
					printf(
						__( 'To read the full list of features, fixes, and changes in this version of SportsZone, <a href="%s">visit Trac</a>.', 'sportszone' ),
						esc_url( 'https://sportszone.trac.wordpress.org/query?group=status&milestone=3.0' )
					);
					?>
				</em></p>

				<h2><?php esc_html_e( _x( 'Your feedback', 'screen heading', 'sportszone' ) ); ?></h2>
				<p>
					<?php
					printf(
						__( ' How are you using SportsZone? Receiving your feedback and suggestions for future versions of SportsZone genuinely motivates and encourages our contributors. Please <a href="%s">share your feedback</a> about this version of SportsZone on our website. ', 'sportszone' ),
						esc_url( 'https://sportszone.org/support/' )
					);
					?>
				</p>
				<p><?php esc_html_e( 'Thank you for using SportsZone! 😊', 'sportszone' ); ?></p>

				<br /><br />
			</div>

			<div class="sz-hello-footer">
				<div class="sz-hello-social-cta">
					<p>
						<?php
						printf(
							_n( 'Built by <a href="%s">%s volunteer</a>.', 'Built by <a href="%s">%s volunteers</a>.', 57, 'sportszone' ),
							esc_url( sz_get_admin_url( 'admin.php?page=sz-credits' ) ),
							number_format_i18n( 57 )
						);
						?>
					</p>
				</div>

				<div class="sz-hello-social-links">
					<ul class="sz-hello-social">
						<li>
							<?php
							printf(
								'<a class="twitter sz-tooltip" data-sz-tooltip="%1$s" href="%2$s"><span class="screen-reader-text">%3$s</span></a>',
								esc_attr( 'Follow SportsZone on Twitter', 'sportszone' ),
								esc_url( 'https://twitter.com/sportszone' ),
								esc_html( 'Follow SportsZone on Twitter', 'sportszone' )
							);
							?>
						</li>

						<li>
							<?php
							printf(
								'<a class="support sz-tooltip" data-sz-tooltip="%1$s" href="%2$s"><span class="screen-reader-text">%3$s</span></a>',
								esc_attr( 'Visit the Support Forums', 'sportszone' ),
								esc_url( 'https://sportszone.org/support/' ),
								esc_html( 'Visit the Support Forums', 'sportszone' )
							);
							?>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Output the credits screen.
	 *
	 * Hardcoding this in here is pretty janky. It's fine for now, but we'll
	 * want to leverage api.wordpress.org eventually.
	 *
	 * @since 1.7.0
	 */
	public function credits_screen() {
	?>

		<div class="wrap sz-about-wrap">

		<h1><?php _e( 'SportsZone Settings', 'sportszone' ); ?> </h1>

		<h2 class="nav-tab-wrapper"><?php sz_core_admin_tabs( __( 'Credits', 'sportszone' ) ); ?></h2>

			<p class="about-description"><?php _e( 'Meet the contributors behind SportsZone:', 'sportszone' ); ?></p>

			<h3 class="wp-people-group"><?php _e( 'Project Leaders', 'sportszone' ); ?></h3>
			<ul class="wp-people-group " id="wp-people-group-project-leaders">
				<li class="wp-person" id="wp-person-johnjamesjacoby">
					<a class="web" href="https://profiles.wordpress.org/johnjamesjacoby"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/7a2644fb53ae2f7bfd7143b504af396c?s=120">
					John James Jacoby</a>
					<span class="title"><?php _e( 'Project Lead', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-boonebgorges">
					<a class="web" href="https://profiles.wordpress.org/boonebgorges"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/9cf7c4541a582729a5fc7ae484786c0c?s=120">
					Boone B. Gorges</a>
					<span class="title"><?php _e( 'Lead Developer', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-djpaul">
					<a class="web" href="https://profiles.wordpress.org/djpaul"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/3bc9ab796299d67ce83dceb9554f75df?s=120">
					Paul Gibbs</a>
					<span class="title"><?php _e( 'Release Lead', 'sportszone' ); ?></span>
				</li>
			</ul>

			<h3 class="wp-people-group"><?php _e( 'SportsZone Team', 'sportszone' ); ?></h3>
			<ul class="wp-people-group " id="wp-people-group-core-team">
				<li class="wp-person" id="wp-person-r-a-y">
					<a class="web" href="https://profiles.wordpress.org/r-a-y"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/3bfa556a62b5bfac1012b6ba5f42ebfa?s=120">
					Ray</a>
					<span class="title"><?php _e( 'Core Developer', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-hnla">
					<a class="web" href="https://profiles.wordpress.org/hnla"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/3860c955aa3f79f13b92826ae47d07fe?s=120">
					Hugo Ashmore</a>
					<span class="title"><?php _e( 'Core Developer', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-imath">
					<a class="web" href="https://profiles.wordpress.org/imath"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/8b208ca408dad63888253ee1800d6a03?s=120">
					Mathieu Viet</a>
					<span class="title"><?php _e( 'Core Developer', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-mercime">
					<a class="web" href="https://profiles.wordpress.org/mercime"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/fae451be6708241627983570a1a1817a?s=120">
					Mercime</a>
					<span class="title"><?php _e( 'Navigator', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-dcavins">
					<a class="web" href="https://profiles.wordpress.org/dcavins"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/a5fa7e83d59cb45ebb616235a176595a?s=120">
					David Cavins</a>
					<span class="title"><?php _e( 'Core Developer', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-tw2113">
					<a class="web" href="https://profiles.wordpress.org/tw2113"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/a5d7c934621fa1c025b83ee79bc62366?s=120">
					Michael Beckwith</a>
					<span class="title"><?php _e( 'Core Developer', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-henry-wright">
					<a class="web" href="https://profiles.wordpress.org/henry.wright"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/0da2f1a9340d6af196b870f6c107a248?s=120">
					Henry Wright</a>
					<span class="title"><?php _e( 'Community Support', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-danbp">
					<a class="web" href="https://profiles.wordpress.org/danbp"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/0deae2e7003027fbf153500cd3fa5501?s=120">
					danbp</a>
					<span class="title"><?php _e( 'Community Support', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-shanebp">
					<a class="web" href="https://profiles.wordpress.org/shanebp"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/ffd294ab5833ba14aaf175f9acc71cc4?s=120">
					shanebp</a>
					<span class="title"><?php _e( 'Community Support', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-slaffik">
					<a class="web" href="https://profiles.wordpress.org/r-a-y"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/61fb07ede3247b63f19015f200b3eb2c?s=120">
					Slava Abakumov</a>
					<span class="title"><?php _e( 'Core Developer', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-offereins">
					<a class="web" href="https://profiles.wordpress.org/Offereins"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/2404ed0a35bb41aedefd42b0a7be61c1?s=120">
					Laurens Offereins</a>
					<span class="title"><?php _e( 'Core Developer', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-netweb">
					<a class="web" href="https://profiles.wordpress.org/netweb"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/97e1620b501da675315ba7cfb740e80f?s=120">
					Stephen Edgar</a>
					<span class="title"><?php _e( 'Core Developer', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-espellcaste">
					<a class="web" href="https://profiles.wordpress.org/espellcaste"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/b691e67be0ba5cad6373770656686bc3?s=120">
					Renato Alves</a>
					<span class="title"><?php _e( 'Core Developer', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-venutius">
					<a class="web" href="https://profiles.wordpress.org/venutius"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/6a7c42a77fd94b82b217a7a97afdddbc?s=120">
					Venutius</a>
					<span class="title"><?php _e( 'Community Support', 'sportszone' ); ?></span>
				</li>
			</ul>

			<h3 class="wp-people-group"><?php _e( 'Recent Rockstars', 'sportszone' ); ?></h3>
			<ul class="wp-people-group " id="wp-people-group-rockstars">
				<li class="wp-person" id="wp-person-dimensionmedia">
					<a class="web" href="https://profiles.wordpress.org/dimensionmedia"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/7735aada1ec39d0c1118bd92ed4551f1?s=120">
					David Bisset</a>
				</li>
				<li class="wp-person" id="wp-person-garrett-eclipse">
					<a class="web" href="https://profiles.wordpress.org/garrett-eclipse"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/7f68f24441c61514d5d0e1451bb5bc9d?s=120">
					Garrett Hyder</a>
				</li>
				<li class="wp-person" id="wp-person-thebrandonallen">
					<a class="web" href="https://profiles.wordpress.org/thebrandonallen"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/6d3f77bf3c9ca94c406dea401b566950?s=120">
					Brandon Allen</a>
				</li>
				<li class="wp-person" id="wp-person-ramiy">
					<a class="web" href="https://profiles.wordpress.org/ramiy"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/ce2a269e424156d79cb0c4e1d4d82db1?s=120">
					Rami Yushuvaev</a>
				</li>
				<li class="wp-person" id="wp-person-vapvarun">
					<a class="web" href="https://profiles.wordpress.org/vapvarun"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/78a3bf7eb3a1132fc667f96f2631e448?s=120">
					Vapvarun</a>
				</li>
			</ul>

			<h3 class="wp-people-group"><?php printf( esc_html__( 'Contributors to SportsZone %s', 'sportszone' ), self::display_version() ); ?></h3>
			<p class="wp-credits-list">
				<a href="https://profiles.wordpress.org/1naveengiri">1naveengiri</a>,
				<a href="https://profiles.wordpress.org/abhishekfdd/">Abhishek Kumar (abhishekfdd)</a>,
				<a href="https://profiles.wordpress.org/andrewteg/">Andrew Tegenkamp (andrewteg)</a>,
				<a href="https://profiles.wordpress.org/ankit-k-gupta/">Ankit K Gupta (ankit-k-gupta)</a>,
				<a href="https://profiles.wordpress.org/antonioeatgoat/">Antonio Mangiacapra (antonioeatgoat)</a>,
				<a href="https://profiles.wordpress.org/boonebgorges/">Boone B Gorges (boonebgorges)</a>,
				<a href="https://profiles.wordpress.org/thebrandonallen/">Brandon Allen (thebrandonallen)</a>,
				<a href="https://profiles.wordpress.org/brandonliles/">brandonliles</a>,
				<a href="https://profiles.wordpress.org/sbrajesh/">Brajesh Singh (sbrajesh)</a>,
				<a href="https://profiles.wordpress.org/ketuchetan/">chetansatasiya (ketuchetan)</a>,
				<a href="https://profiles.wordpress.org/chherbst/">chherbst</a>,
				<a href="https://profiles.wordpress.org/needle/">Christian Wach (needle)</a>,
				<a href="https://profiles.wordpress.org/coach-afrane/">Coach Afrane</a>,
				<a href="https://profiles.wordpress.org/cshinkin/">cshinkin</a>,
				<a href="https://profiles.wordpress.org/danbp/">danbp</a>,
				<a href="https://profiles.wordpress.org/dcavins/">David Cavins (dcavins)</a>,
				<a href="https://profiles.wordpress.org/devitate/">devitate</a>,
				<a href="https://profiles.wordpress.org/garrett-eclipse/">Garrett Hyder (garrett-eclipse)</a>,
				<a href="https://profiles.wordpress.org/geminorum/">geminorum</a>,
				<a href="https://profiles.wordpress.org/Mamaduka/">George Mamadashvili (Mamaduka)</a>,
				<a href="https://profiles.wordpress.org/januzi_pl/">januzi_pl</a>,
				<a href="https://profiles.wordpress.org/jcrr/">jcrr</a>,
				<a href="https://profiles.wordpress.org/jdgrimes/">J.D. Grimes (jdgrimes)</a>,
				<a href="https://profiles.wordpress.org/JohnPBloch/">John P. Bloch (JohnPBloch)</a>,
				<a href="https://profiles.wordpress.org/joost-abrahams/">Joost Abrahams (joost-abrahams)</a>,
				<a href="https://profiles.wordpress.org/henry.wright">Henry Wright (henry.wright)</a>,
				<a href="https://profiles.wordpress.org/hnla/">Hugo (hnla)</a>,
				<a href="https://profiles.wordpress.org/idofri/">Ido Friedlander (idofri)</a>,
				<a href="https://profiles.wordpress.org/dunhakdis/">Joseph G. (dunhakdis)</a>,
				<a href="https://profiles.wordpress.org/johnjamesjacoby/">John James Jacoby (johnjamesjacoby)</a>,
				<a href="https://profiles.wordpress.org/Offereins">Laurens Offereins (Offereins)</a>,
				<a href="https://profiles.wordpress.org/mechter/">Markus Echterhoff (mechter)</a>,
				<a href="https://profiles.wordpress.org/imath/">Mathieu Viet (imath)</a>,
				<a href="https://profiles.wordpress.org/meitar/">meitar</a>,
				<a href="https://profiles.wordpress.org/mercime/">mercime</a>,
				<a href="https://profiles.wordpress.org/tw2113/">Michael Beckwith (tw2113)</a>,
				<a href="https://profiles.wordpress.org/mauteri/">Mike Auteri (mauteri)</a>,
				<a href="https://profiles.wordpress.org/modemlooper/">modemlooper</a>,
				<a href="https://profiles.wordpress.org/m_uysl/">Mustafa Uysal (m_uysl)</a>,
				<a href="https://profiles.wordpress.org/pareshradadiya/">paresh.radadiya (pareshradadiya)</a>,
				<a href="https://profiles.wordpress.org/DJPaul/">Paul Gibbs (DJPaul)</a>,
				<a href="https://profiles.wordpress.org/pavloopanasenko/">pavlo.opanasenko (pavloopanasenko)</a>,
				<a href="https://profiles.wordpress.org/pscolv/">pscolv</a>,
				<a href="https://profiles.wordpress.org/r-a-y/">r-a-y</a>,
				<a href="https://profiles.wordpress.org/rachelbaker/">Rachel Baker (rachelbaker)</a>,
				<a href="https://profiles.wordpress.org/rekmla/">rekmla</a>,
				<a href="https://profiles.wordpress.org/espellcaste/">Renato Alves (espellcaste)</a>,
				<a href="https://profiles.wordpress.org/rianrietveld/">Rian Rietveld (rianrietvelde)</a>,
				<a href="https://profiles.wordpress.org/ripstechcom/">ripstechcom</a>,
				<a href="https://profiles.wordpress.org/cyclic/">Ryan Williams (cyclic)</a>,
				<a href="https://profiles.wordpress.org/slaffik/">Slava Abakumov (slaffik)</a>,
				<a href="https://profiles.wordpress.org/netweb/">Stephen Edgar (netweb)</a>,
				<a href="https://profiles.wordpress.org/tobiashonold/">Tobias Honold (tobiashonold)</a>,
				<a href="https://profiles.wordpress.org/uzosky/">uzosky</a>,
				<a href="https://profiles.wordpress.org/vapvarun/">vapvarun</a>,
				<a href="https://profiles.wordpress.org/Venutius/">Venutius</a>,
				<a href="https://profiles.wordpress.org/yahil/">Yahil Madakiya (yahil)</a>
			</p>

			<h3 class="wp-people-group"><?php _e( 'With our thanks to these Open Source projects', 'sportszone' ); ?></h3>
			<p class="wp-credits-list">
				<a href="https://github.com/ichord/At.js">At.js</a>,
				<a href="https://bbpress.org">bbPress</a>,
				<a href="https://github.com/ichord/Caret.js">Caret.js</a>,
				<a href="https://tedgoas.github.io/Cerberus/">Cerberus</a>,
				<a href="https://ionicons.com/">Ionicons</a>,
				<a href="https://github.com/carhartl/jquery-cookie">jquery.cookie</a>,
				<a href="https://mattbradley.github.io/livestampjs/">Livestamp.js</a>,
				<a href="https://www.mediawiki.org/wiki/MediaWiki">MediaWiki</a>,
				<a href="https://momentjs.com/">Moment.js</a>,
				<a href="https://wordpress.org">WordPress</a>.
			</p>

			<h3 class="wp-people-group"><?php _e( 'Contributor Emeriti', 'sportszone' ); ?></h3>
			<ul class="wp-people-group " id="wp-people-group-emeriti">
				<li class="wp-person" id="wp-person-apeatling">
					<a class="web" href="https://profiles.wordpress.org/johnjamesjacoby"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/bb29d699b5cba218c313b61aa82249da?s=120">
					Andy Peatling</a>
					<span class="title"><?php _e( 'Project Founder', 'sportszone' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-burtadsit">
					<a class="web" href="https://profiles.wordpress.org/burtadsit"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/185e1d3e2d653af9d49a4e8e4fc379df?s=120">
					Burt Adsit</a>
				</li>
				<li class="wp-person" id="wp-person-jeffsayre">
					<a class="web" href="https://profiles.wordpress.org/jeffsayre"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/8e009a84ff5d245c22a69c7df6ab45f7?s=120">
					Jeff Sayre</a>
				</li>
				<li class="wp-person" id="wp-person-karmatosed">
					<a class="web" href="https://profiles.wordpress.org/karmatosed"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/ca7d4273a689cdbf524d8332771bb1ca?s=120">
					Tammie Lister</a>
				</li>
				<li class="wp-person" id="wp-person-modemlooper">
					<a class="web" href="https://profiles.wordpress.org/modemlooper"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/1c07be1016e845de514931477c939307?s=120">
					modemlooper</a>
				</li>
			</ul>
		</div>

		<?php
	}

	/** Emails ****************************************************************/

	/**
	 * Registers 'Situations' column on Emails dashboard page.
	 *
	 * @since 2.6.0
	 *
	 * @param array $columns Current column data.
	 * @return array
	 */
	public function emails_register_situation_column( $columns = array() ) {
		$situation = array(
			'situation' => _x( 'Situations', 'Email post type', 'sportszone' )
		);

		// Inject our 'Situations' column just before the last 'Date' column.
		return array_slice( $columns, 0, -1, true ) + $situation + array_slice( $columns, -1, null, true );
	}

	/**
	 * Output column data for our custom 'Situations' column.
	 *
	 * @since 2.6.0
	 *
	 * @param string $column  Current column name.
	 * @param int    $post_id Current post ID.
	 */
	public function emails_display_situation_column_data( $column = '', $post_id = 0 ) {
		if ( 'situation' !== $column ) {
			return;
		}

		// Grab email situations for the current post.
		$situations = wp_list_pluck( get_the_terms( $post_id, sz_get_email_tax_type() ), 'description' );

		// Output each situation as a list item.
		echo '<ul><li>';
		echo implode( '</li><li>', $situations );
		echo '</li></ul>';
	}

	/** Helpers ***************************************************************/

	/**
	 * Return true/false based on whether a query argument is set.
	 *
	 * @see sz_do_activation_redirect()
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public static function is_new_install() {
		return (bool) isset( $_GET['is_new_install'] );
	}

	/**
	 * Return a user-friendly version-number string, for use in translations.
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */
	public static function display_version() {

		// Use static variable to prevent recalculations.
		static $display = '';

		// Only calculate on first run.
		if ( '' === $display ) {

			// Get current version.
			$version = sz_get_version();

			// Check for prerelease hyphen.
			$pre     = strpos( $version, '-' );

			// Strip prerelease suffix.
			$display = ( false !== $pre )
				? substr( $version, 0, $pre )
				: $version;
		}

		// Done!
		return $display;
	}

	/**
	 * Add Emails menu item to custom menus array.
	 *
	 * Several SportsZone components have top-level menu items in the Dashboard,
	 * which all appear together in the middle of the Dashboard menu. This function
	 * adds the Emails screen to the array of these menu items.
	 *
	 * @since 2.4.0
	 *
	 * @param array $custom_menus The list of top-level BP menu items.
	 * @return array $custom_menus List of top-level BP menu items, with Emails added.
	 */
	public function emails_admin_menu_order( $custom_menus = array() ) {
		array_push( $custom_menus, 'edit.php?post_type=' . sz_get_email_post_type() );

		if ( is_network_admin() && sz_is_network_activated() ) {
			array_push(
				$custom_menus,
				get_admin_url( sz_get_root_blog_id(), 'edit.php?post_type=' . sz_get_email_post_type() )
			);
		}

		return $custom_menus;
	}

	/**
	 * Register styles commonly used by SportsZone wp-admin screens.
	 *
	 * @since 2.5.0
	 */
	public function admin_register_styles() {
		$min = sz_core_get_minified_asset_suffix();
		$url = $this->css_url;

		/**
		 * Filters the SportsZone Core Admin CSS file path.
		 *
		 * @since 1.6.0
		 *
		 * @param string $file File path for the admin CSS.
		 */
		$common_css = apply_filters( 'sz_core_admin_common_css', "{$url}common{$min}.css" );

		/**
		 * Filters the SportsZone admin stylesheet files to register.
		 *
		 * @since 2.5.0
		 *
		 * @param array $value Array of admin stylesheet file information to register.
		 */
		$styles = apply_filters( 'sz_core_admin_register_styles', array(
			// Legacy.
			'sz-admin-common-css' => array(
				'file'         => $common_css,
				'dependencies' => array(),
			),

			// 2.5
			'sz-customizer-controls' => array(
				'file'         => "{$url}customizer-controls{$min}.css",
				'dependencies' => array(),
			),

			// 3.0
			'sz-hello-css' => array(
				'file'         => "{$url}hello{$min}.css",
				'dependencies' => array( 'sz-admin-common-css' ),
			),
		) );

		$version = sz_get_version();

		foreach ( $styles as $id => $style ) {
			wp_register_style( $id, $style['file'], $style['dependencies'], $version );
			wp_style_add_data( $id, 'rtl', true );

			if ( $min ) {
				wp_style_add_data( $id, 'suffix', $min );
			}
		}
	}

	/**
	 * Register JS commonly used by SportsZone wp-admin screens.
	 *
	 * @since 2.5.0
	 */
	public function admin_register_scripts() {
		$min = sz_core_get_minified_asset_suffix();
		$url = $this->js_url;

		/**
		 * Filters the SportsZone admin JS files to register.
		 *
		 * @since 2.5.0
		 *
		 * @param array $value Array of admin JS file information to register.
		 */
		$scripts = apply_filters( 'sz_core_admin_register_scripts', array(
			// 2.5
			'sz-customizer-controls' => array(
				'file'         => "{$url}customizer-controls{$min}.js",
				'dependencies' => array( 'jquery' ),
				'footer'       => true,
			),

			// 3.0
			'sz-hello-js' => array(
				'file'         => "{$url}hello{$min}.js",
				'dependencies' => array(),
				'footer'       => true,
			),
		) );

		$version = sz_get_version();

		foreach ( $scripts as $id => $script ) {
			wp_register_script( $id, $script['file'], $script['dependencies'], $version, $script['footer'] );
		}
	}
}
endif; // End class_exists check.
