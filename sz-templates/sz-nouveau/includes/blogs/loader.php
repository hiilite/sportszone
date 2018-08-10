<?php
/**
 * BP Nouveau Blogs
 *
 * @since 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Blogs Loader class
 *
 * @since 3.0.0
 */
class SZ_Nouveau_Blogs {
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

		// Test suite requires the AJAX functions early.
		if ( function_exists( 'tests_add_filter' ) ) {
			require $this->dir . 'ajax.php';

		// Load AJAX code only on AJAX requests.
		} else {
			add_action( 'admin_init', function() {
				if ( defined( 'DOING_AJAX' ) && true === DOING_AJAX && 0 === strpos( $_REQUEST['action'], 'blogs_' ) ) {
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
			// Avoid Notices for SportsZone Legacy Backcompat
			remove_action( 'sz_blogs_directory_blog_types', 'sz_blog_backcompat_create_nav_item', 1000 );
		}

		add_action( 'sz_nouveau_enqueue_scripts', function() {
			if ( sz_get_blog_signup_allowed() && sz_is_register_page() ) {
				wp_add_inline_script( 'sz-nouveau', sz_nouveau_get_blog_signup_inline_script() );
			}
		} );
	}

	/**
	 * Register add_filter() hooks
	 *
	 * @since 3.0.0
	 */
	protected function setup_filters() {
		if ( is_multisite() ) {
			// Add settings into the Blogs sections of the customizer.
			add_filter( 'sz_nouveau_customizer_settings', 'sz_nouveau_blogs_customizer_settings', 11, 1 );

			// Add controls into the Blogs sections of the customizer.
			add_filter( 'sz_nouveau_customizer_controls', 'sz_nouveau_blogs_customizer_controls', 11, 1 );
		}
	}
}

/**
 * Launch the Blogs loader class.
 *
 * @since 3.0.0
 */
function sz_nouveau_blogs( $sz_nouveau = null ) {
	if ( is_null( $sz_nouveau ) ) {
		return;
	}

	$sz_nouveau->blogs = new SZ_Nouveau_Blogs();
}
add_action( 'sz_nouveau_includes', 'sz_nouveau_blogs', 10, 1 );
