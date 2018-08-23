<?php
/**
 * Signup validator.
 *
 * @package    SportsZone Xprofile Custom Field Types Reloaded
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace SZXProfileCFTR\Handlers;

// No direct access.
use SZXProfileCFTR\Field_Types\Field_Type_Birthdate;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

class Signup_Validator {

	/**
	 * Setup the bootstrapper.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	public function setup() {
		add_action( 'sz_signup_validate', array( $this, 'validate' ) );
	}

	public function validate() {
		if ( ! sz_is_active( 'xprofile' ) ) {
			return;
		}

		$profile_field_ids = isset( $_POST['signup_profile_field_ids'] ) ? explode( ',', $_POST['signup_profile_field_ids'] ) : array();

		foreach ( $profile_field_ids as $field_id ) {
			$this->validate_field( $field_id );
		}
	}

	/**
	 * Validate a field.
	 *
	 * @param int $field_id field id.
	 */
	private function validate_field( $field_id ) {
		$sz    = sportszone();
		$field = new \SZ_XProfile_Field( $field_id );

		switch ( $field->type ) {
			case 'image':
			case 'file':
				if ( ! isset( $_FILES[ 'field_' . $field_id ] ) ) {
					break;
				}
				// remove error?
				unset( $sz->signup->errors[ 'field_' . $field_id ] );
				$this->validate_file( $field );

				break;

			case 'checkbox_acceptance':
				if ( $field->is_required && ( empty( $_POST[ 'field_' . $field_id ] ) || 1 != $_POST[ 'field_' . $field_id ] ) ) {
					$sz->signup->errors[ 'field_' . $field_id ] = __( 'This is a required field', 'sz-xprofile-custom-field-types' );
				}

				break;

			case 'fromto':
				if ( $field->is_required && ( empty( $_POST[ 'field_' . $field_id ]['from'] ) || empty( $_POST[ 'field_' . $field_id ]['to'] ) ) ) {
					$sz->signup->errors[ 'field_' . $field_id ] = __( 'This is a required field', 'sz-xprofile-custom-field-types' );
				}

				break;

			case 'birthdate':
				$this->validate_birthdate( $field );
				break;
		}
	}

	/**
	 * Validate the file type fields.
	 *
	 * @param \SZ_XProfile_Field $field field object.
	 */
	private function validate_file( $field ) {
		$sz       = sportszone();
		$field_id = $field->id;

		$filesize = round( $_FILES[ 'field_' . $field_id ]['size'] / ( 1024 * 1024 ), 2 );

		if ( $field->is_required && $filesize <= 0 ) {
			$sz->signup->errors[ 'field_' . $field_id ] = __( 'This is a required field.', 'sz-xprofile-custom-field-types' );

			return;
		}

		// Check extensions.
		$ext = strtolower( substr( $_FILES[ 'field_' . $field_id ]['name'], strrpos( $_FILES[ 'field_' . $field_id ]['name'], '.' ) + 1 ) );

		$allowed_extension = szxcftr_get_allowed_file_extensions( $field->type );
		$allowed_size      = szxcftr_get_allowed_file_size( $field->type );

		if ( $allowed_size < $filesize ) {
			$sz->signup->errors[ 'field_' . $field_id ] = sprintf( __( 'File exceed the upload limit. Max upload size %d.', 'sz-xprofile-custom-field-types' ), $allowed_size );
		}

		if ( ! in_array( $ext, $allowed_extension ) ) {
			$sz->signup->errors[ 'field_' . $field_id ] = sprintf( __( 'File type not allowed: (%s).', 'sz-xprofile-custom-field-types' ), implode( ',', $allowed_extension ) );
		}
	}


	/**
	 * Validate the Birthdate.
	 *
	 * @param \SZ_XProfile_Field $field field object.
	 */
	private function validate_birthdate( $field ) {
		$sz       = sportszone();
		$field_id = $field->id;
		$min_age  = Field_Type_Birthdate::get_min_age( $field_id );

		if ( $min_age <= 0 ) {
			return;
		}

		// Check birthdate.
		$now       = new \DateTime();
		$birthdate = new \DateTime( sprintf( "%s-%s-%s",
			$_POST[ 'field_' . $field_id . '_year' ],
			$_POST[ 'field_' . $field_id . '_month' ],
			$_POST[ 'field_' . $field_id . '_day' ] ) );
		$age       = $now->diff( $birthdate );

		if ( $age->y < $min_age ) {
			$sz->signup->errors[ 'field_' . $field_id ] = sprintf( __( 'You have to be at least %s years old.', 'sz-xprofile-custom-field-types' ), $min_age );
		}
	}
}
