<?php
/**
 * BP Nouveau xProfile
 *
 * @since 3.0.0
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * xProfile Loader class
 *
 * @since 3.0.0
 */
class SZ_Nouveau_xProfile {
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
		$this->dir = dirname( __FILE__ );
	}

	/**
	 * Include needed files
	 *
	 * @since 3.0.0
	 */
	protected function includes() {
		require( trailingslashit( $this->dir ) . 'functions.php' );
		require( trailingslashit( $this->dir ) . 'template-tags.php' );
	}

	/**
	 * Register do_action() hooks
	 *
	 * @since 3.0.0
	 */
	protected function setup_actions() {
		add_action( 'sz_nouveau_enqueue_scripts', 'sz_nouveau_xprofile_enqueue_scripts' );
	}

	/**
	 * Register add_filter() hooks
	 *
	 * @since 3.0.0
	 */
	protected function setup_filters() {
		add_filter( 'sz_nouveau_register_scripts', 'sz_nouveau_xprofile_register_scripts', 10, 1 );
	}
}

/**
 * Launch the xProfile loader class.
 *
 * @since 3.0.0
 */
function sz_nouveau_xprofile( $sz_nouveau = null ) {
	if ( is_null( $sz_nouveau ) ) {
		return;
	}

	$sz_nouveau->xprofile = new SZ_Nouveau_xProfile();
}
add_action( 'sz_nouveau_includes', 'sz_nouveau_xprofile', 10, 1 );
