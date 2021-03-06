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

use SZXProfileCFTR\Contracts\Field_Type_Selectable;

/**
 * Select Custom Post Type Type
 */
class Field_Type_Select_Post_Type extends \SZ_XProfile_Field_Type implements Field_Type_Selectable {

    public function __construct() {
		parent::__construct();

		$this->name     = _x( 'Custom Post Type Selector', 'xprofile field type', 'sz-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'sz-xprofile-custom-field-types' );

		$this->supports_options = false;

		do_action( 'sz_xprofile_field_type_select_custom_post_type', $this );
	}


	public function edit_field_html( array $raw_properties = array() ) {
		$user_id = sz_displayed_user_id();

		if ( isset( $raw_properties['user_id'] ) ) {
			$user_id = (int) $raw_properties['user_id'];
			unset( $raw_properties['user_id'] );
		}

		$html = $this->get_edit_field_html_elements( $raw_properties );
		?>

        <legend id="<?php sz_the_profile_field_input_name(); ?>-1">
			<?php sz_the_profile_field_name(); ?>
			<?php sz_the_profile_field_required_label(); ?>
        </legend>

		<?php do_action( sz_get_the_profile_field_errors_action() ); ?>

        <select <?php echo $html; ?>>
            <option value=""><?php _e( 'Select...', 'sz-xprofile-custom-field-types' ); ?></option>
			<?php sz_the_profile_field_options( "user_id={$user_id}" ); ?>
        </select>

		<?php if ( sz_get_the_profile_field_description() ) : ?>
            <p class="description" id="<?php sz_the_profile_field_input_name(); ?>-3"><?php sz_the_profile_field_description(); ?></p>
		<?php endif; ?>

		<?php
	}

	public function edit_field_options_html( array $args = array() ) {
        global $field;
		$post_type_selected = self::get_selected_post_type( $field->id );
		$post_selected      = \SZ_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] );

		$html = '';


		if ( ! empty( $_POST[ 'field_' . $this->field_obj->id ] ) ) {
			$new_post_selected = (int) $_POST[ 'field_' . $this->field_obj->id ];
			$post_selected     = ( $post_selected != $new_post_selected ) ? $new_post_selected : $post_selected;
		}
		// Get posts of custom post type selected.
		$posts = new \WP_Query( array(
			'posts_per_page' => - 1,
			'post_type'      => $post_type_selected,
			'orderby'        => 'title',
			'order'          => 'ASC'
		) );
		if ( $posts ) {
			foreach ( $posts->posts as $post ) {
				$html .= sprintf( '<option value="%s"%s>%s</option>',
					$post->ID,
					( $post_selected == $post->ID ) ? ' selected="selected"' : '',
					$post->post_title );
			}
		}


		echo apply_filters( 'sz_get_the_profile_field_select_custom_post_type', $html, $args['type'], $post_type_selected, $this->field_obj->id );
	}

	public function admin_field_html( array $raw_properties = array() ) {
		$html = $this->get_edit_field_html_elements( $raw_properties );
		?>
        <select <?php echo $html; ?>>
			<?php sz_the_profile_field_options(); ?>
        </select>
		<?php
	}

	public function admin_new_field_html( \SZ_XProfile_Field $current_field, $control_type = '' ) {

        $type = array_search( get_class( $this ), sz_xprofile_get_field_types() );

		if ( false === $type ) {
			return;
		}

		$class = $current_field->type != $type ? 'display: none;' : '';

		$post_types = get_post_types( array(
			'public'   => true,
		) );

        $selected_post_type = self::get_selected_post_type( $current_field->id );
        ?>
        <div id="<?php echo esc_attr( $type ); ?>" class="postbox sz-options-box"
             style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
			<?php if ( ! $post_types ): ?>
                <h3><?php _e( 'There is no custom post type. You need to create at least one to use this field.', 'sz-xprofile-custom-field-types' ); ?></h3>
			<?php else : ?>
                <h3><?php esc_html_e( 'Select a post type:', 'sz-xprofile-custom-field-types' ); ?></h3>
                <div class="inside">
                    <p>
						<?php _e( 'Select a post type:', 'sz-xprofile-custom-field-types' ); ?>
                        <select name="szxcftr_selected_post_type" id="szxcftr_selected_post_type">
                            <option value=""><?php _e( 'Select...', 'sz-xprofile-custom-field-types' ); ?></option>
							<?php foreach ( $post_types as $k => $v ): ?>
                                <option value="<?php echo $k; ?>" <?php selected( $selected_post_type, $k, true ); ?>><?php echo $v; ?></option>
							<?php endforeach; ?>
                        </select>
                    </p>
                </div>
			<?php endif; ?>
        </div>
		<?php
	}

	/**
	 * Check if valid.
	 *
	 * @param int $values post id.
	 *
	 * @return bool
	 */
	public function is_valid( $values ) {
		return empty( $values ) || get_post( $values );
	}

	/**
	 * @param mixed $field_value
	 * @param string $field_id
	 *
	 * @return string
	 */
	public static function display_filter( $field_value, $field_id = '' ) {

		$post_id = absint( $field_value );

		if ( empty( $field_value ) || ! get_post( $post_id ) ) {
			return '';
		}

		return sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_permalink( $post_id ) ), get_the_title( $post_id ) );
	}

	/**
	 * Get the terms content.
	 *
	 * @param int $field_id field id.
	 *
	 * @return string
	 */
	private static function get_selected_post_type( $field_id ) {

		if ( ! $field_id ) {
			return '';
		}

		return sz_xprofile_get_meta( $field_id, 'field', 'selected_post_type', true );
	}
}
