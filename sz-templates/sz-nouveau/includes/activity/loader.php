<?php
/**
 * BP Nouveau Activity
 *
 * @since 3.0.0
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Activity Loader class
 *
 * @since 3.0.0
 */
class SZ_Nouveau_Activity {
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
		$this->dir = trailingslashit( dirname( __FILE__ ) );
	}

	/**
	 * Include needed files
	 *
	 * @since 3.0.0
	 */
	protected function includes() {
		require $this->dir . 'functions.php';
		require $this->dir . 'template-tags.php';
		require $this->dir . 'widgets.php';

		// Test suite requires the AJAX functions early.
		if ( function_exists( 'tests_add_filter' ) ) {
			require $this->dir . 'ajax.php';

		// Load AJAX code only on AJAX requests.
		} else {
			add_action( 'admin_init', function() {
				// AJAX condtion.
				if ( defined( 'DOING_AJAX' ) && true === DOING_AJAX &&
					// Check to see if action is activity-specific.
					( false !== strpos( $_REQUEST['action'], 'activity' ) || ( 'post_update' === $_REQUEST['action'] ) )
				) {
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
		add_action( 'sz_nouveau_enqueue_scripts', 'sz_nouveau_activity_enqueue_scripts' );
		add_action( 'sz_widgets_init', array( 'SZ_Latest_Activities', 'register_widget' ) );
		add_action( 'sz_nouveau_notifications_init_filters', 'sz_nouveau_activity_notification_filters' );

		$sz = sportszone();

		if ( sz_is_akismet_active() && isset( $sz->activity->akismet ) ) {
			remove_action( 'sz_activity_entry_meta', array( $sz->activity->akismet, 'add_activity_spam_button' ) );
			remove_action( 'sz_activity_comment_options', array( $sz->activity->akismet, 'add_activity_comment_spam_button' ) );
		}
	}

	/**
	 * Register add_filter() hooks
	 *
	 * @since 3.0.0
	 */
	protected function setup_filters() {
		// Register customizer controls.
		add_filter( 'sz_nouveau_customizer_controls', 'sz_nouveau_activity_customizer_controls', 10, 1 );

		// Register activity scripts
		add_filter( 'sz_nouveau_register_scripts', 'sz_nouveau_activity_register_scripts', 10, 1 );

		// Localize Scripts
		add_filter( 'sz_core_get_js_strings', 'sz_nouveau_activity_localize_scripts', 10, 1 );

		add_filter( 'sz_get_activity_action_pre_meta', 'sz_nouveau_activity_secondary_avatars', 10, 2 );
		add_filter( 'sz_get_activity_css_class', 'sz_nouveau_activity_scope_newest_class', 10, 1 );
	}
}

/**
 * Launch the Activity loader class.
 *
 * @since 3.0.0
 */
function sz_nouveau_activity( $sz_nouveau = null ) {
	if ( is_null( $sz_nouveau ) ) {
		return;
	}

	$sz_nouveau->activity = new SZ_Nouveau_Activity();
}
add_action( 'sz_nouveau_includes', 'sz_nouveau_activity', 10, 1 );
