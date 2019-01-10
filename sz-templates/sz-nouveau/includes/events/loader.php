<?php
/**
 * BP Nouveau Events
 *
 * @since 3.0.0
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Events Loader class
 *
 * @since 3.0.0
 */
class SZ_Nouveau_Events {
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
		$this->is_event_home_sidebar = false;
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
				if ( defined( 'DOING_AJAX' ) && true === DOING_AJAX && 0 === strpos( $_REQUEST['action'], 'events_' ) ) {
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
			add_action( 'events_setup_nav', 'sz_nouveau_event_setup_nav' );
		}

		add_action( 'sz_nouveau_enqueue_scripts', 'sz_nouveau_events_enqueue_scripts' );

		// Avoid Notices for SportsZone Legacy Backcompat
		remove_action( 'sz_events_directory_event_filter', 'sz_event_backcompat_create_nav_item', 1000 );

		// Register the Events Notifications filters
		add_action( 'sz_nouveau_notifications_init_filters', 'sz_nouveau_events_notification_filters' );

		// Actions to check whether we are in the Event's default front page sidebar
		add_action( 'dynamic_sidebar_before', array( $this, 'event_home_sidebar_set' ), 10, 1 );
		add_action( 'dynamic_sidebar_after', array( $this, 'event_home_sidebar_unset' ), 10, 1 );

		// Add a new nav item to settings to let users choose their event invites preferences
		if ( sz_is_active( 'friends' ) && ! sz_nouveau_events_disallow_all_members_invites() ) {
			add_action( 'sz_settings_setup_nav', 'sz_nouveau_events_invites_restriction_nav' );
		}
	}

	/**
	 * Register add_filter() hooks
	 *
	 * @since 3.0.0
	 */
	protected function setup_filters() {
		add_filter( 'sz_nouveau_register_scripts', 'sz_nouveau_events_register_scripts', 10, 1 );
		add_filter( 'sz_core_get_js_strings', 'sz_nouveau_events_localize_scripts', 10, 1 );
		add_filter( 'events_create_event_steps', 'sz_nouveau_event_invites_create_steps', 10, 1 );

		$buttons = array(
			'events_leave_event',
			'events_join_event',
			'events_pay_event',
			'events_accept_invite',
			'events_reject_invite',
			'events_membership_requested',
			'events_request_membership',
			'events_event_membership',
		);

		foreach ( $buttons as $button ) {
			add_filter( 'sz_button_' . $button, 'sz_nouveau_ajax_button', 10, 5 );
		}

		// Add sections in the BP Template Pack panel of the customizer.
		add_filter( 'sz_nouveau_customizer_sections', 'sz_nouveau_events_customizer_sections', 10, 1 );

		// Add settings into the Events sections of the customizer.
		add_filter( 'sz_nouveau_customizer_settings', 'sz_nouveau_events_customizer_settings', 10, 1 );

		// Add controls into the Events sections of the customizer.
		add_filter( 'sz_nouveau_customizer_controls', 'sz_nouveau_events_customizer_controls', 10, 1 );

		// Add the event's default front template to hieararchy if user enabled it (Enabled by default).
		add_filter( 'sz_events_get_front_template', 'sz_nouveau_event_reset_front_template', 10, 2 );

		// Add a new nav item to settings to let users choose their event invites preferences
		if ( sz_is_active( 'friends' ) && ! sz_nouveau_events_disallow_all_members_invites() ) {
			add_filter( 'sz_settings_admin_nav', 'sz_nouveau_events_invites_restriction_admin_nav', 10, 1 );
		}
	}

	/**
	 * Add filters to be sure the (SportsZone) widgets display will be consistent
	 * with the current event's default front page.
	 *
	 * @since 3.0.0
	 *
	 * @param string $sidebar_index The Sidebar identifier.
	 */
	public function event_home_sidebar_set( $sidebar_index = '' ) {
		if ( 'sidebar-sportszone-events' !== $sidebar_index ) {
			return;
		}

		$this->is_event_home_sidebar = true;

		// Add needed filters.
		sz_nouveau_events_add_home_widget_filters();
	}

	/**
	 * Remove filters to be sure the (SportsZone) widgets display will no more take
	 * the current event displayed in account.
	 *
	 * @since 3.0.0
	 *
	 * @param string $sidebar_index The Sidebar identifier.
	 */
	public function event_home_sidebar_unset( $sidebar_index = '' ) {
		if ( 'sidebar-sportszone-events' !== $sidebar_index ) {
			return;
		}

		$this->is_event_home_sidebar = false;

		// Remove no more needed filters.
		sz_nouveau_events_remove_home_widget_filters();
	}
}

/**
 * Launch the Events loader class.
 *
 * @since 3.0.0
 */
function sz_nouveau_events( $sz_nouveau = null ) {
	if ( is_null( $sz_nouveau ) ) {
		return;
	}

	$sz_nouveau->events = new SZ_Nouveau_Events();
}
add_action( 'sz_nouveau_includes', 'sz_nouveau_events', 10, 1 );
