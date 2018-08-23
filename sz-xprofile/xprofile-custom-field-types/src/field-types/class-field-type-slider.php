<?php
/**
 * Post Type Field.
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
 * Slider Type
 */
class Field_Type_Slider extends \SZ_XProfile_Field_Type {
	public function __construct() {
		parent::__construct();

		$this->name     = __( 'Range input (HTML5 field)', 'sz-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'sz-xprofile-custom-field-types' );

		$this->accepts_null_value = true;
		$this->supports_options   = false;

		do_action( 'sz_xprofile_field_type_slider', $this );
	}

	/**
	 * Edit field html.
	 *
	 * @param array $raw_properties properties.
	 */
	public function edit_field_html( array $raw_properties = array() ) {
        global $field;

		if ( isset( $raw_properties['user_id'] ) ) {
			unset( $raw_properties['user_id'] );
		}

		$args = array(
			'type'  => 'range',
			'class' => 'szxcftr-slider',
			'value' => sz_get_the_profile_field_edit_value(),
			'min'   => self::get_min_val( $field->id ),
			'max'   => self::get_max_val( $field->id ),
		);
		?>

        <legend id="<?php sz_the_profile_field_input_name(); ?>-1">
			<?php sz_the_profile_field_name(); ?>
			<?php sz_the_profile_field_required_label(); ?>
        </legend>

        <?php
		// Errors.
		do_action( sz_get_the_profile_field_errors_action() );
		// Input.
		?>
        <input <?php echo $this->get_edit_field_html_elements( array_merge( $args, $raw_properties ) ); ?> />
        <span id="output-field_<?php echo $field->id; ?>"></span>

        <?php  if ( sz_get_the_profile_field_description() ) : ?>
            <p class="description" id="<?php sz_the_profile_field_input_name(); ?>-3"><?php sz_the_profile_field_description(); ?></p>
		<?php endif;?>

		<?php
	}

	public function admin_field_html( array $raw_properties = array() ) {
		global $field;

		$args = array(
			'type'  => 'range',
			'class' => 'szxcftr-slider',
			'min'   => self::get_min_val( $field->id ),
			'max'   => self::get_max_val( $field->id ),
		);
		?>

        <input <?php echo $this->get_edit_field_html_elements( array_merge( $args, $raw_properties ) ); ?> />

        <?php
	}


	public function admin_new_field_html( \SZ_XProfile_Field $current_field, $control_type = '' ) {

	    $type = array_search( get_class( $this ), sz_xprofile_get_field_types() );

		if ( false === $type ) {
			return;
		}

		$class            = $current_field->type != $type ? 'display: none;' : '';

		$min     = self::get_min_val( $current_field->id );
		$max     = self::get_max_val( $current_field->id );

		?>
        <div id="<?php echo esc_attr( $type ); ?>" class="postbox sz-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">

        <h3><?php esc_html_e( 'Write min and max values.', 'sz-xprofile-custom-field-types' ); ?></h3>
            <div class="inside">
                <p>
                    <label for="szxcftr_slider_min">
						<?php esc_html_e( 'Minimum:', 'sz-xprofile-custom-field-types' ); ?>
                    </label>
                    <input type="text" name="szxcftr_slider_min" id="szxcftr_slider_min" value="<?php echo esc_attr( $min ); ?>"/>
                    <label for="szxcftr_slider_max">
						<?php esc_html_e( 'Maximum:', 'sz-xprofile-custom-field-types' ); ?>
                    </label>
                    <input type="text" name="szxcftr_slider_max" id="szxcftr_slider_max" value="<?php echo esc_attr( $max ); ?>"/>
                </p>
            </div>
        </div>

		<?php
	}

	/**
	 * Check the validity of the value
	 *
	 * @param string $values value.
	 *
	 * @return bool
	 */
	public function is_valid( $values ) {

		if ( empty( $values ) ) {
			return true;
		}

		$field = szxcftr_get_current_field();
		// we don't know the field, have no idea how to validate.
		if ( empty( $field ) ) {
			return is_numeric( $values );
		}

		$min = self::get_min_val( $field->id );
		$max = self::get_max_val( $field->id );

		// unset the current field from our saved field id.
		szxcftr_set_current_field(null );

		return ( $values >= $min ) && ( $values <= $max );
	}


	/**
	 * Modify the appearance of value. Apply autolink if enabled.
	 *
	 * @param  string $field_value Original value of field.
	 * @param  int $field_id Id of field.
	 *
	 * @return string   Value formatted
	 */
	public static function display_filter( $field_value, $field_id = 0 ) {

		return $field_value;

	}

	/**
	 * Get the minimum allowed value for the field id.
	 *
	 * @param int $field_id field id.
	 *
	 * @return float
	 */
	public static function get_min_val( $field_id ) {
		return sz_xprofile_get_meta( $field_id, 'field', 'min_val', true );
	}

	/**
	 * Get the max allowed value for the field id.
	 *
	 * @param int $field_id field id.
	 *
	 * @return float
	 */
	public static function get_max_val( $field_id ) {
		return sz_xprofile_get_meta( $field_id, 'field', 'max_val', true );
	}
}
