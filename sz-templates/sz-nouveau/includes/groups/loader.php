<?php
/**
 * BP Nouveau Groups
 *
 * @since 3.0.0
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Groups Loader class
 *
 * @since 3.0.0
 */
class SZ_Nouveau_Groups {
	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
		$this->setup_filters();
	}

	/**
	 * Globals
	 *
	 * @since 3.0.0
	 */
	protected function setup_globals() {
		$this->dir                   = trailingslashit( dirname( __FILE__ ) );
		$this->is_group_home_sidebar = false;
	}

	/**
	 * Include needed files
	 *
	 * @since 3.0.0
	 */
	protected function includes() {
		require $this->dir . 'functions.php';
		require $this->dir . 'classes.php';
		require $this->dir . 'template-tags.php';

		// Test suite requires the AJAX functions early.
		if ( function_exists( 'tests_add_filter' ) ) {
			require $this->dir . 'ajax.php';

		// Load AJAX code only on AJAX requests.
		} else {
			add_action( 'admin_init', function() {
				if ( defined( 'DOING_AJAX' ) && true === DOING_AJAX && 0 === strpos( $_REQUEST['action'], 'groups_' ) ) {
					require $this->dir . 'ajax.php';
				}
			} );
		}
	}

	/**
	 * Register do_action() hooks
	 *
	 * @since 3.0.0
	 */
	protected function setup_actions() {
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			add_action( 'groups_setup_nav', 'sz_nouveau_group_setup_nav' );
		}

		add_action( 'sz_nouveau_enqueue_scripts', 'sz_nouveau_groups_enqueue_scripts' );

		// Avoid Notices for SportsZone Legacy Backcompat
		remove_action( 'sz_groups_directory_group_filter', 'sz_group_backcompat_create_nav_item', 1000 );

		// Register the Groups Notifications filters
		add_action( 'sz_nouveau_notifications_init_filters', 'sz_nouveau_groups_notification_filters' );

		// Actions to check whether we are in the Group's default front page sidebar
		add_action( 'dynamic_sidebar_before', array( $this, 'group_home_sidebar_set' ), 10, 1 );
		add_action( 'dynamic_sidebar_after', array( $this, 'group_home_sidebar_unset' ), 10, 1 );

		// Add a new nav item to settings to let users choose their group invites preferences
		if ( sz_is_active( 'friends' ) && ! sz_nouveau_groups_disallow_all_members_invites() ) {
			add_action( 'sz_settings_setup_nav', 'sz_nouveau_groups_invites_restriction_nav' );
		}
	}

	/**
	 * Register add_filter() hooks
	 *
	 * @since 3.0.0
	 */
	protected function setup_filters() {
		add_filter( 'sz_nouveau_register_scripts', 'sz_nouveau_groups_register_scripts', 10, 1 );
		add_filter( 'sz_core_get_js_strings', 'sz_nouveau_groups_localize_scripts', 10, 1 );
		add_filter( 'groups_create_group_steps', 'sz_nouveau_group_invites_create_steps', 10, 1 );

		$buttons = array(
			'groups_leave_group',
			'groups_join_group',
			'groups_accept_invite',
			'groups_reject_invite',
			'groups_membership_requested',
			'groups_request_membership',
			'groups_group_membership',
		);

		foreach ( $buttons as $button ) {
			add_filter( 'sz_button_' . $button, 'sz_nouveau_ajax_button', 10, 5 );
		}

		// Add sections in the BP Template Pack panel of the customizer.
		add_filter( 'sz_nouveau_customizer_sections', 'sz_nouveau_groups_customizer_sections', 10, 1 );

		// Add settings into the Groups sections of the customizer.
		add_filter( 'sz_nouveau_customizer_settings', 'sz_nouveau_groups_customizer_settings', 10, 1 );

		// Add controls into the Groups sections of the customizer.
		add_filter( 'sz_nouveau_customizer_controls', 'sz_nouveau_groups_customizer_controls', 10, 1 );

		// Add the group's default front template to hieararchy if user enabled it (Enabled by default).
		add_filter( 'sz_groups_get_front_template', 'sz_nouveau_group_reset_front_template', 10, 2 );

		// Add a new nav item to settings to let users choose their group invites preferences
		if ( sz_is_active( 'friends' ) && ! sz_nouveau_groups_disallow_all_members_invites() ) {
			add_filter( 'sz_settings_admin_nav', 'sz_nouveau_groups_invites_restriction_admin_nav', 10, 1 );
		}
	}

	/**
	 * Add filters to be sure the (SportsZone) widgets display will be consistent
	 * with the current group's default front page.
	 *
	 * @since 3.0.0
	 *
	 * @param string $sidebar_index The Sidebar identifier.
	 */
	public function group_home_sidebar_set( $sidebar_index = '' ) {
		if ( 'sidebar-sportszone-groups' !== $sidebar_index ) {
			return;
		}

		$this->is_group_home_sidebar = true;

		// Add needed filters.
		sz_nouveau_groups_add_home_widget_filters();
	}

	/**
	 * Remove filters to be sure the (SportsZone) widgets display will no more take
	 * the current group displayed in account.
	 *
	 * @since 3.0.0
	 *
	 * @param string $sidebar_index The Sidebar identifier.
	 */
	public function group_home_sidebar_unset( $sidebar_index = '' ) {
		if ( 'sidebar-sportszone-groups' !== $sidebar_index ) {
			return;
		}

		$this->is_group_home_sidebar = false;

		// Remove no more needed filters.
		sz_nouveau_groups_remove_home_widget_filters();
	}
}

/**
 * Launch the Groups loader class.
 *
 * @since 3.0.0
 */
function sz_nouveau_groups( $sz_nouveau = null ) {
	if ( is_null( $sz_nouveau ) ) {
		return;
	}

	$sz_nouveau->groups = new SZ_Nouveau_Groups();
}
add_action( 'sz_nouveau_includes', 'sz_nouveau_groups', 10, 1 );