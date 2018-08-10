<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    SZ_Add_Group_Types
 * @subpackage SZ_Add_Group_Types/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    SZ_Add_Group_Types
 * @subpackage SZ_Add_Group_Types/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class SZ_Add_Group_Types_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->szgt_save_general_settings();
		$this->szgt_save_group_types();
		$this->szgt_save_type_display_settings();
		$this->szgt_save_group_type_search_settings();

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SZ_Add_Group_Types_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SZ_Add_Group_Types_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( strpos( filter_input( INPUT_SERVER, 'REQUEST_URI' ), 'sz-add-group-types' ) !== false ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sz-add-group-types-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SZ_Add_Group_Types_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SZ_Add_Group_Types_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( strpos( filter_input( INPUT_SERVER, 'REQUEST_URI' ), 'sz-add-group-types' ) !== false ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sz-add-group-types-admin.js', array( 'jquery' ), $this->version, false );
		}
	}

	/**
	 * Register a submenu to handle group types
	 *
	 * @since    1.0.0
	 */
	public function szgt_add_submenu_page() {
		add_submenu_page( 'sz-groups', __( 'Group Types Settings', 'sportszone' ), __( 'Types Settings', 'sportszone' ), 'manage_options', $this->plugin_name, array( $this, 'szgt_admin_settings_page' ));
	}
	/**
	 * Actions performed to create a submenu page content
	 */
	public function szgt_admin_settings_page() {
		$tab = ( filter_input( INPUT_GET, 'tab' ) !== null ) ? filter_input( INPUT_GET, 'tab' ) : $this->plugin_name;
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Group Types - SportsZone', 'sz-add-group-types' ); ?></h2>
			<?php $this->szgt_plugin_settings_tabs(); ?>
			<?php do_settings_sections( $tab ); ?>
		</div>
		<?php
	}

	/**
	 * Actions performed to create tabs on the sub menu page
	 */
	public function szgt_plugin_settings_tabs() {
		$current_tab = ( filter_input( INPUT_GET, 'tab' ) !== null ) ? filter_input( INPUT_GET, 'tab' ) : $this->plugin_name;
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab === $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . esc_attr( $active ) . '" id="' . esc_attr( $tab_key ) . '-tab" href="?page=' . esc_attr( $this->plugin_name ) . '&tab=' . esc_attr( $tab_key ) . '">' . esc_attr( $tab_caption ) . '</a>';
		}
		echo '</h2>';
	}

	/**
	 * General Tab
	 */
	public function szgt_register_general_settings() {
		$this->plugin_settings_tabs['szgt-general'] = __( 'General', 'sz-add-group-types' );
		register_setting( 'szgt-general', 'szgt-general' );
		add_settings_section( 'szgt-general-section', ' ', array( &$this, 'szgt_general_settings_content' ), 'szgt-general' );
	}

	/**
	 * General Tab Content
	 */
	public function szgt_general_settings_content() {
		if ( file_exists( dirname( __FILE__ ) . '/settings/szgt-general-settings.php' ) ) {
			require_once dirname( __FILE__ ) . '/settings/szgt-general-settings.php';
		}
	}

	/**
	 * Group Types Listing Tab
	 */
	public function szgt_register_group_types_listing_settings() {
		$this->plugin_settings_tabs['sz-add-group-types'] = __( 'Group Types', 'sz-add-group-types' );
		register_setting( 'sz-add-group-types', 'sz-add-group-types' );
		add_settings_section( 'sz-add-group-types-section', ' ', array( &$this, 'szgt_group_types_listing_settings_content' ), 'sz-add-group-types' );
	}

	/**
	 * Group Types Listing Tab Content
	 */
	public function szgt_group_types_listing_settings_content() {
		if ( file_exists( dirname( __FILE__ ) . '/settings/szgt-group-types-listing-settings.php' ) ) {
			require_once dirname( __FILE__ ) . '/settings/szgt-group-types-listing-settings.php';
		}
	}

	/**
	 * Group Types Search Tab
	 */
	public function szgt_register_group_type_search_settings() {
		$this->plugin_settings_tabs['szgt-search'] = __( 'Group Type Search', 'sz-add-group-types' );
		register_setting( 'szgt-search', 'szgt-search' );
		add_settings_section( 'szgt-search-enabled-section', ' ', array( &$this, 'szgt_group_type_search_settings_content' ), 'szgt-search' );
	}

	/**
	 * Group Types Search Tab Content
	 */
	public function szgt_group_type_search_settings_content() {
		if ( file_exists( dirname( __FILE__ ) . '/settings/szgt-group-type-search-settings.php' ) ) {
			require_once dirname( __FILE__ ) . '/settings/szgt-group-type-search-settings.php';
		}
	}

	/**
	 * Support Tab
	 */
	public function szgt_register_support_settings() {
		$this->plugin_settings_tabs['szgt-support'] = __( 'Support', 'sz-add-group-types' );
		register_setting( 'szgt-support', 'szgt-support' );
		add_settings_section( 'szgt-support-section', ' ', array( &$this, 'szgt_support_settings_content' ), 'szgt-support' );
	}

	/**
	 * Support Tab Content
	 */
	public function szgt_support_settings_content() {
		if ( file_exists( dirname( __FILE__ ) . '/settings/szgt-support-settings.php' ) ) {
			require_once dirname( __FILE__ ) . '/settings/szgt-support-settings.php';
		}
	}

	/**
	 * Group Type Display Setting Tab
	 */
	public function szgt_register_type_display_settings() {
		$this->plugin_settings_tabs['szgt-type-display'] = __( 'Display Group Types', 'sz-add-group-types' );
		register_setting( 'szgt-type-display', 'szgt-type-display' );
		add_settings_section( 'szgt-type-display-section', ' ', array( &$this, 'szgt_type_display_settings_content' ), 'szgt-type-display' );
	}

	/**
	 * Group Type Display Setting Tab Content
	 */
	public function szgt_type_display_settings_content() {
		if ( file_exists( dirname( __FILE__ ) . '/settings/szgt-group-type-display-settings.php' ) ) {
			require_once dirname( __FILE__ ) . '/settings/szgt-group-type-display-settings.php';
		}
	}

	/**
	 * Save Plugin General Settings
	 */
	public function szgt_save_general_settings() {
		global $allowedposttags;
		if ( ( filter_input( INPUT_POST, 'szgt_submit_general_settings' ) !== null ) && wp_verify_nonce( filter_input( INPUT_POST, 'szgt-general-settings-nonce' ), 'szgt' ) ) {

			$group_types_pre_selected = 'no';
			if ( null !== filter_input( INPUT_POST, 'szgt-group-types-pre-selected' ) ) {
				$group_types_pre_selected = 'yes';
			}

			$group_type_search_enabled = 'no';
			if ( null !== filter_input( INPUT_POST, 'szgt-group-types-search-enabled' ) ) {
				$group_type_search_enabled = 'yes';
			}

			$admin_settings = array(
				'group_types_pre_selected'  => $group_types_pre_selected,
				'group_type_search_enabled' => $group_type_search_enabled,
			);

			update_site_option( 'szgt_general_settings', $admin_settings );
			$success_msg  = "<div class='notice updated is-dismissible' id='message'>";
			$success_msg .= '<p>' . __( 'Settings Saved.', 'sz-add-group-types' ) . '</p>';
			$success_msg .= '</div>';
			wp_kses( $success_msg, $allowedposttags );
		}
	}

	/**
	 * Save Plugin General Settings
	 */
	public function szgt_save_type_display_settings() {
		global $allowedposttags;
		$type_arr = array();
		if ( null !== filter_input( INPUT_POST, 'szgt_submit_group_type_display_settings' ) ) {
			if ( isset( $_POST['szgt_group_type_display'] ) ) {
				$type_arr = array_map( 'sanitize_text_field', wp_unslash( $_POST['szgt_group_type_display'] ) );
			}
			update_site_option( 'szgt_type_display_settings', $type_arr );
			$success_msg  = "<div class='notice updated is-dismissible' id='message'>";
			$success_msg .= '<p>' . __( 'Settings Saved.', 'sz-add-group-types' ) . '</p>';
			$success_msg .= '</div>';
			wp_kses( $success_msg, $allowedposttags );
		}
	}

	/**
	 * Save Group Types that are added
	 */
	public function szgt_save_group_types() {
		global $allowedposttags;
		if ( ( filter_input( INPUT_POST, 'szgt-add-group-type' ) !== null ) && wp_verify_nonce( filter_input( INPUT_POST, 'szgt-add-group-types-nonce' ), 'szgt-group-types' ) ) {
			$group_type_name = sanitize_text_field( filter_input( INPUT_POST, 'group-type-name' ) );

			if ( filter_input( INPUT_POST, 'group-type-slug' ) !== null ) {
				$group_type_slug = sanitize_text_field( filter_input( INPUT_POST, 'group-type-slug' ) );
			} else {
				$group_type_slug = str_replace( ' ', '', strtolower( $group_type_name ) );
			}

			$group_type_desc = '';
			if ( filter_input( INPUT_POST, 'group-type-desc' ) !== null ) {
				$group_type_desc = sanitize_text_field( filter_input( INPUT_POST, 'group-type-desc' ) );
			}

			$group_types = get_site_option( 'szgt_group_types' );
			if ( ! is_array( $group_types ) ) {
				$group_types = array();
			}

			$flag = 0;
			if ( ! empty( $group_types ) ) {
				foreach ( $group_types as $key => $group_type ) {
					if ( $group_type_slug === $group_type['slug'] ) {
						$flag = 1;
					}
				}
			}

			if ( 0 === $flag ) {
				$group_types[] = array(
					'name' => $group_type_name,
					'slug' => $group_type_slug,
					'desc' => $group_type_desc,
				);
				update_site_option( 'szgt_group_types', $group_types );
				$success_msg  = "<div class='notice updated is-dismissible' id='message'>";
				$success_msg .= '<p>' . __( 'Group Type Added!', 'sz-add-group-types' ) . '</p>';
				$success_msg .= '</div>';
				wp_kses( $success_msg, $allowedposttags );
			} else {
				$error_msg  = "<div class='notice notice-error is-dismissible' id='message'>";
				$error_msg .= '<p>' . __( 'Group Type With This Name/Slug Already Exists!', 'sz-add-group-types' ) . '</p>';
				$error_msg .= '</div>';
				wp_kses( $error_msg, $allowedposttags );
			}
		}
	}

	/**
	 * Ajax served to delete the group type
	 */
	public function szgt_delete_group_type() {
		if ( ( filter_input( INPUT_POST, 'action' ) !== null ) && 'szgt_delete_group_type' === filter_input( INPUT_POST, 'action' ) ) {
			$slug = sanitize_text_field( filter_input( INPUT_POST, 'slug' ) );

			$group_types = get_site_option( 'szgt_group_types' );
			foreach ( $group_types as $key => $group_type ) {
				if ( $slug === $group_type['slug'] ) {
					$key_to_unset = $key;
					break;
				}
			}
			unset( $group_types[ $key_to_unset ] );
			if ( empty( $group_types ) ) {
				delete_option( 'szgt_group_types' );
			} else {
				update_site_option( 'szgt_group_types', $group_types );
			}

			$response = array(
				'message' => __( 'Group Type Deleted.', 'sz-add-group-types' ),
			);
			wp_send_json_success( $response );
			die;
		}
	}

	/**
	 * Ajax served to update the group type
	 */
	public function szgt_update_group_type() {
		if ( ( filter_input( INPUT_POST, 'action' ) !== null ) && 'szgt_update_group_type' === filter_input( INPUT_POST, 'action' ) ) {
			$new_name = sanitize_text_field( filter_input( INPUT_POST, 'new_name' ) );
			$old_slug = sanitize_text_field( filter_input( INPUT_POST, 'old_slug' ) );

			$group_types = get_site_option( 'szgt_group_types' );
			foreach ( $group_types as $key => $group_type ) {
				if ( $old_slug === $group_type['slug'] ) {
					$key_to_update = $key;
					break;
				}
			}

			$new_group_type = array(
				'name' => $new_name,
				'slug' => sanitize_text_field( filter_input( INPUT_POST, 'new_slug' ) ),
				'desc' => sanitize_text_field( filter_input( INPUT_POST, 'new_desc' ) ),
			);

			$group_types[ $key_to_update ] = $new_group_type;
			update_site_option( 'szgt_group_types', $group_types );

			$response = array(
				'message' => __( 'Group Type Updated.', 'sz-add-group-types' ),
			);
			wp_send_json_success( $response );
			die;
		}
	}

	/**
	 * Register all saved group types
	 */
	public function szgt_register_group_types() {
		global $sz_grp_types;
		$create_screen_checked = false;
		if ( isset( $sz_grp_types->group_types_pre_selected ) ) {
			if ( 'yes' === $sz_grp_types->group_types_pre_selected ) {
				$create_screen_checked = true;
			}
		}
		$saved_group_types = $sz_grp_types->group_types;
		$group_types       = sz_groups_get_group_types();
		if ( ! empty( $saved_group_types ) ) {
			foreach ( $saved_group_types as $key => $saved_group_type ) {
				$slug = $saved_group_type['slug'];
				$name = $saved_group_type['name'];
				$desc = $saved_group_type['desc'];
				if ( ! in_array( $slug, $group_types, true ) ) {
					$temp = array(
						'labels'                => array(
							'name'          => $name,
							'singular_name' => $name,
						),
						'has_directory'         => strtolower( $name ),
						'show_in_create_screen' => true,
						'show_in_list'          => true,
						'description'           => $desc,
						'create_screen_checked' => $create_screen_checked,
					);
					sz_groups_register_group_type( $name, $temp );
				}
			}
		}
	}

	/**
	 * Save Plugin Group Type Search Settings
	 */
	public function szgt_save_group_type_search_settings() {
		global $allowedposttags;
		if ( ( filter_input( INPUT_POST, 'szgt_submit_group_type_search_settings' ) !== null ) && wp_verify_nonce( filter_input( INPUT_POST, 'szgt-group-type-search-settings-nonce' ), 'szgt-search-settings' ) ) {
			$group_type_search_template = 'both';
			if ( null !== filter_input( INPUT_POST, 'szgt-group-type-search-template' ) ) {
				$group_type_search_template = sanitize_text_field( filter_input( INPUT_POST, 'szgt-group-type-search-template' ) );
			}

			$admin_settings = array(
				'group_type_search_template' => $group_type_search_template,
			);

			update_site_option( 'szgt_group_type_search_settings', $admin_settings );
			$success_msg  = "<div class='notice updated is-dismissible' id='message'>";
			$success_msg .= '<p>' . __( 'Settings Saved.', 'sz-add-group-types' ) . '</p>';
			$success_msg .= '</div>';
			echo wp_kses( $success_msg, $allowedposttags );
		}
	}
}
