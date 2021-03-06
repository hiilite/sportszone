<?php
/**
 * Date Picker Field.
 *
 * @package    SportsZone Xprofile Custom Field Types
 * @subpackage Field_Types
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

namespace SZXProfileCFTR\Field_Types;

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

class Field_Type_Datepicker extends \SZ_XProfile_Field_Type {

    public function __construct() {

	    parent::__construct();

	    $this->name     = _x( 'Datepicker (HTML5 field)', 'xprofile field type', 'sz-xprofile-custom-field-types' );
	    $this->category = _x( 'Custom Fields', 'xprofile field type category', 'sz-xprofile-custom-field-types' );

		$this->set_format( '/^\d{4}-\d{1,2}-\d{1,2}$/', 'replace' );  // "Y-m-d 00:00:00"
		do_action( 'sz_xprofile_field_type_datepicker', $this );
	}

	/**
     * Output html for the Profile->Edit and Register screen.
     *
	 * @param array $raw_properties array of attributes.
	 */
	public function edit_field_html( array $raw_properties = array() ) {

		if ( isset( $raw_properties['user_id'] ) ) {
			unset( $raw_properties['user_id'] );
		}

		$html = $this->get_edit_field_html_elements( array_merge(
			array(
				'type'  => 'date',
				'value' => sz_get_the_profile_field_edit_value(),
			),
			$raw_properties
		) );
		?>

        <legend id="<?php sz_the_profile_field_input_name(); ?>-1">
			<?php sz_the_profile_field_name(); ?>
			<?php sz_the_profile_field_required_label(); ?>
        </legend>

		<?php
		// Errors.
		do_action( sz_get_the_profile_field_errors_action() );
		?>

        <input <?php echo $html; ?> />

		<?php if ( sz_get_the_profile_field_description() ) : ?>
            <p class="description" id="<?php sz_the_profile_field_input_name(); ?>-3"><?php sz_the_profile_field_description(); ?></p>
		<?php endif; ?>

		<?php
	}

	public function admin_field_html( array $raw_properties = array() ) {

	    $html = $this->get_edit_field_html_elements( array_merge(
			array( 'type' => 'date' ),
			$raw_properties
		) );
		?>

        <input <?php echo $html; ?> />
		<?php
	}

	/**
     * Output html for showing options on Add New Field/Edit Field screen.
     *
	 * @param \SZ_XProfile_Field $current_field The current profile field on the add/edit screen.
	 * @param string            $control_type  Optional. HTML input type used to render the current
	 *                          field's child options.
	 */
	public function admin_new_field_html( \SZ_XProfile_Field $current_field, $control_type = '' ) {
	}

	/**
	 * Modify the appearance.
	 *
	 * @param mixed $field_value field value.
	 * @param int   $field_id field id.
	 *
	 * @return mixed
	 */
	public static function display_filter( $field_value, $field_id = 0 ) {

		if ( empty( $field_value ) ) {
			return '';
		}

		// not numeric?
		if ( ! is_numeric( $field_value ) ) {
			$field_value = strtotime( $field_value );
		}

		return date_i18n( get_option( 'date_format' ), $field_value );
	}
}
