<?php
/**
 * Bootstrapper. Initializes the plugin.
 *
 * @package    SportsZone Xprofile Custom Field Types
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace SZXProfileCFTR\Bootstrap;

use SZXProfileCFTR\Admin\Admin;
use SZXProfileCFTR\Filters\SZ_Profile_Search_Helper;
use SZXProfileCFTR\Handlers\Birthdate_Field_Validator;
use SZXProfileCFTR\Handlers\Field_Upload_Helper;
use SZXProfileCFTR\Handlers\From_To_Helper;
use SZXProfileCFTR\Handlers\Taxonomy_Terms_Creator;
use SZXProfileCFTR\Handlers\Field_Settings_Handler;
use SZXProfileCFTR\Admin\Field_Settings_Helper as Admin_Field_Settings_Helper;
use SZXProfileCFTR\Handlers\Signup_Validator;

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Bootstrapper.
 */
class Bootstrapper {

	/**
	 * Setup the bootstrapper.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Bind hooks
	 */
	private function setup() {

		add_action( 'sz_loaded', array( $this, 'load' ) );
		add_action( 'sz_init', array( $this, 'load_translations' ) );

		// register.
		add_filter( 'sz_xprofile_get_field_types', array( $this, 'register_field_types' ), 10, 1 );
	}

	/**
	 * Load core functions/template tags.
	 * These are non auto loadable constructs.
	 */
	public function load() {
		$this->load_common();
		$this->load_admin();
	}

	/**
	 * Load translations.
	 */
	public function load_translations() {
		load_plugin_textdomain( 'sz-xprofile-custom-field-types', false, basename( dirname( sz_xprofile_cftr()->path ) ) . '/languages' );
	}

	/**
	 * Register the field types.
	 *
	 * @param array $fields field types.
	 *
	 * @return array
	 */
	public function register_field_types( $fields ) {
		$fields = array_merge( $fields, szxcftr_get_field_types() );
		return $fields;
	}

	/**
	 * Load files common to each request type.
	 */
	private function load_common() {
		$path = sz_xprofile_cftr()->path;

		$files = array(
			'src/core/sz-xprofile-custom-field-types-functions.php',
		);

		if ( is_admin() ) {
		}

		foreach ( $files as $file ) {
			require_once $path . $file;
		}

		// Boot the app.
		Assets_Loader::boot();
		Field_Upload_Helper::boot();
		Taxonomy_Terms_Creator::boot();
		Birthdate_Field_Validator::boot();
		Field_Settings_Handler::boot();
		Signup_Validator::boot();
		From_To_Helper::boot();
		// SZ profile Search
		SZ_Profile_Search_Helper::boot();
	}

	/**
	 * Load admin.
	 */
	private function load_admin() {

		if ( ! is_admin() ) {
			return;
		}

		if ( ! defined( 'DOING_AJAX' ) ) {
			Admin::boot();
		}

		Admin_Field_Settings_Helper::boot();
	}
}
