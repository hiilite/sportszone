<?php
/**
 * Allows storing the 'from', 'to' as array keys in the data.
 *
 * @package    SportsZone Xprofile Custom Field Types
 * @subpackage Handlers
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace SZXProfileCFTR\Handlers;

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Manage and sync field data.
 */
class From_To_Helper {

	/**
	 * Flag to keep a tab on whether we remove the filter or not?
	 *
	 * @var bool
	 */
	protected $removed = false;

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
		add_filter( 'xprofile_data_field_id_before_save', array( $this, 'detach' ), 1, 2 );
		add_filter( 'xprofile_data_after_save', array( $this, 'attach' ) );
	}

	/**
	 * detach the validation filter as it will remove our array keys.
	 *
	 * @param int $field_id field id.
	 * @param int $data_field_id data field id.
	 *
	 * @return mixed
	 */
	public function detach( $field_id, $data_field_id ) {

		if ( empty( $field_id ) ) {
			return $field_id;
		}

		$field = new \SZ_XProfile_Field( $field_id );

		if ( 'fromto' != $field->type ) {
			return $field_id;
		}

		if ( has_filter( 'xprofile_data_value_before_save', 'xprofile_sanitize_data_value_before_save' ) ) {
			$this->removed = true;
			remove_filter( 'xprofile_data_value_before_save', 'xprofile_sanitize_data_value_before_save', 1 );
		}

		return $field_id;
	}

	/**
	 * Re attach the filter.
	 */
	public function attach() {
		if ( $this->removed ) {
			add_filter( 'xprofile_data_value_before_save', 'xprofile_sanitize_data_value_before_save', 1, 4 );
		}
	}
}
