<?php
/**
 * Select 2 enabler..
 *
 * @package    SportsZone Xprofile Custom Field Types Reloaded
 * @subpackage Handlers
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

namespace SZXProfileCFTR\Handlers;

// No direct access.

use SZXProfileCFTR\Field_Types\Field_Type_Multi_Select_Taxonomy;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Field settings helper.
 */
class Field_Settings_Handler {

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
		add_action( 'sz_custom_profile_edit_fields_pre_visibility', array( $this, 'enable_select2' ) );
	}

	/**
	 * enable_select2.
	 */
	public function enable_select2() {
		global $field;

		if ( ! $this->is_select2_enabled( $field ) ) {
			return;
		}

		$field_name_id = sz_get_the_profile_field_input_name();
		// for multi valued field.
		if ( szxcftr_is_multi_valued_field( $field ) ) {
			$field_name_id .= '[]';
		}

		$allow_new_tags = false;

		if ( Field_Type_Multi_Select_Taxonomy::allow_new_terms( $field->id ) ) {
			$allow_new_tags = true;
		}


		if ( $allow_new_tags ) {
			?>
            <script>
                jQuery(function ($) {
                    $('select[name="<?php echo $field_name_id; ?>"]').select2({
                        tags: true,
                        tokenSeparators: [',']
                    });
                });
            </script>
			<?php
		} else {
			?>
            <script>
                jQuery(function ($) {
                    $('select[name="<?php echo $field_name_id; ?>"]').select2();
                });
            </script>
			<?php
		}
	}


	/**
	 * Check if select 2 is enabled for the field.
	 *
	 * @param \SZ_XProfile_Field $field field object.
	 *
	 * @return bool
	 */
	private function is_select2_enabled( $field ) {
		if ( ! $field ) {
			return false;
		}

		global $field;
		if ( ! szxcftr_is_selectable_field( $field ) ) {
			return false;
		}


		$do_select2 = sz_xprofile_get_meta( $field->id, 'field', 'do_select2' );

		return 'on' === $do_select2;
	}
}