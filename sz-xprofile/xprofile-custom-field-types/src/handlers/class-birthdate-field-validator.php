<?php
/**
 * Birthdate Field Validator
 *
 * @package    SportsZone Xprofile Custom Field Types
 * @subpackage Handlers
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace SZXProfileCFTR\Handlers;


use SZXProfileCFTR\Field_Types\Field_Type_Birthdate;

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Manage and sync field data.
 */
class Birthdate_Field_Validator {

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
		add_action( 'xprofile_data_before_save', array( $this, 'on_field_data_save' ) );
	}

	/**
	 * Validate on field save.
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function on_field_data_save( $data ) {

		if ( ! is_user_logged_in() ) {
			return $data;
		}

		$field_id = $data->field_id;
		$field    = new \SZ_XProfile_Field( $field_id );
		$min_age  = Field_Type_Birthdate::get_min_age( $field_id );

		if ( 'birthdate' != $field->type || $min_age <= 0 ) {
			return $data;
		}

		$redirect_url = trailingslashit( sz_displayed_user_domain() . sz_get_profile_slug() . '/edit/group/' . sz_action_variable( 1 ) );

		// Check birthdate.
		$now       = new \DateTime();

		$birthdate = new \DateTime( sprintf( "%s-%s-%s",
			$_POST[ 'field_' . $field_id . '_year' ],
			$_POST[ 'field_' . $field_id . '_month' ],
			$_POST[ 'field_' . $field_id . '_day' ] ) );

		if ( $now <= $birthdate ) {
			sz_core_add_message( sprintf( __( 'Incorrect birthdate selection.', 'sz-xprofile-custom-field-types' ), $min_age ), 'error' );
			sz_core_redirect( $redirect_url );
		}

		$age = $now->diff( $birthdate );

		if ( $age->y < $min_age ) {
			sz_core_add_message( sprintf( __( 'You have to be at least %s years old.', 'sz-xprofile-custom-field-types' ), $min_age ), 'error' );
			sz_core_redirect( $redirect_url );
		}
	}
}
