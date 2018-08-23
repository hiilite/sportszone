<?php
/**
 * Color field.
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

/**
 * Color type.
 */
class Field_Type_Color extends \SZ_XProfile_Field_Type {

	public function __construct() {

		parent::__construct();

		$this->name     = _x( 'Color (HTML5 field)', 'xprofile field type', 'sz-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'sz-xprofile-custom-field-types' );

		$this->set_format( '/^.+$/', 'replace' );
		do_action( 'sz_xprofile_field_type_color', $this );
	}

	/**
     * Edit profile field/register page.
     *
	 * @param array $raw_properties props.
	 */
	public function edit_field_html( array $raw_properties = array() ) {

        // reset user_id.
	    if ( isset( $raw_properties['user_id'] ) ) {
			unset( $raw_properties['user_id'] );
		}

		$html = $this->get_edit_field_html_elements( array_merge(
			array(
				'type'  => 'color',
				'value' => sz_get_the_profile_field_edit_value(),
                'class' => 'szxcftr-color'
			),
			$raw_properties
		) );
		?>

        <legend id="<?php sz_the_profile_field_input_name(); ?>-1">
			<?php sz_the_profile_field_name(); ?>
			<?php sz_the_profile_field_required_label(); ?>
        </legend>

		<?php do_action( sz_get_the_profile_field_errors_action() ); ?>

        <input <?php echo $html; ?>>

		<?php if ( sz_get_the_profile_field_description() ) : ?>
            <p class="description"
               id="<?php sz_the_profile_field_input_name(); ?>-3"><?php sz_the_profile_field_description(); ?></p>
		<?php endif; ?>

		<?php
	}

	/**
     * Admin field list html.
     *
	 * @param array $raw_properties properties.
	 */
	public function admin_field_html( array $raw_properties = array() ) {

	    $html = $this->get_edit_field_html_elements( array_merge(
			array( 'type' => 'color' ),
			$raw_properties
		) );
		?>

        <input <?php echo $html; ?>>
		<?php
	}

	public function admin_new_field_html( \SZ_XProfile_Field $current_field, $control_type = '' ) {
	}
}
