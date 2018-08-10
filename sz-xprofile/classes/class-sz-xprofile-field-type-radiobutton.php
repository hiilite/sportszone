<?php
/**
 * SportsZone XProfile Classes.
 *
 * @package SportsZone
 * @subpackage XProfileClasses
 * @since 2.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Radio button xprofile field type.
 *
 * @since 2.0.0
 */
class SZ_XProfile_Field_Type_Radiobutton extends SZ_XProfile_Field_Type {

	/**
	 * Constructor for the radio button field type.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		parent::__construct();

		$this->category = _x( 'Multi Fields', 'xprofile field type category', 'sportszone' );
		$this->name     = _x( 'Radio Buttons', 'xprofile field type', 'sportszone' );

		$this->supports_options = true;

		$this->set_format( '/^.+$/', 'replace' );

		/**
		 * Fires inside __construct() method for SZ_XProfile_Field_Type_Radiobutton class.
		 *
		 * @since 2.0.0
		 *
		 * @param SZ_XProfile_Field_Type_Radiobutton $this Current instance of
		 *                                                 the field type radio button.
		 */
		do_action( 'sz_xprofile_field_type_radiobutton', $this );
	}

	/**
	 * Output the edit field HTML for this field type.
	 *
	 * Must be used inside the {@link sz_profile_fields()} template loop.
	 *
	 * @since 2.0.0
	 *
	 * @param array $raw_properties Optional key/value array of
	 *                              {@link http://dev.w3.org/html5/markup/input.radio.html permitted attributes}
	 *                              that you want to add.
	 */
	public function edit_field_html( array $raw_properties = array() ) {

		// User_id is a special optional parameter that we pass to
		// {@link sz_the_profile_field_options()}.
		if ( isset( $raw_properties['user_id'] ) ) {
			$user_id = (int) $raw_properties['user_id'];
			unset( $raw_properties['user_id'] );
		} else {
			$user_id = sz_displayed_user_id();
		} ?>

			<legend>
				<?php sz_the_profile_field_name(); ?>
				<?php sz_the_profile_field_required_label(); ?>
			</legend>

			<?php if ( sz_get_the_profile_field_description() ) : ?>
				<p class="description" tabindex="0"><?php sz_the_profile_field_description(); ?></p>
			<?php endif; ?>

			<?php

			/** This action is documented in sz-xprofile/sz-xprofile-classes */
			do_action( sz_get_the_profile_field_errors_action() ); ?>

			<?php sz_the_profile_field_options( array( 'user_id' => $user_id ) );

			if ( ! sz_get_the_profile_field_is_required() ) : ?>

				<a class="clear-value" href="javascript:clear( '<?php echo esc_js( sz_get_the_profile_field_input_name() ); ?>' );">
					<?php esc_html_e( 'Clear', 'sportszone' ); ?>
				</a>

			<?php endif; ?>

		<?php
	}

	/**
	 * Output the edit field options HTML for this field type.
	 *
	 * SportsZone considers a field's "options" to be, for example, the items in a selectbox.
	 * These are stored separately in the database, and their templating is handled separately.
	 *
	 * This templating is separate from {@link SZ_XProfile_Field_Type::edit_field_html()} because
	 * it's also used in the wp-admin screens when creating new fields, and for backwards compatibility.
	 *
	 * Must be used inside the {@link sz_profile_fields()} template loop.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args Optional. The arguments passed to {@link sz_the_profile_field_options()}.
	 */
	public function edit_field_options_html( array $args = array() ) {
		$option_value = SZ_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] );
		$options      = $this->field_obj->get_children();

		$html = '';

		for ( $k = 0, $count = count( $options ); $k < $count; ++$k ) {

			// Check for updated posted values, but errors preventing them from
			// being saved first time.
			if ( isset( $_POST['field_' . $this->field_obj->id] ) && $option_value != $_POST['field_' . $this->field_obj->id] ) {
				if ( ! empty( $_POST['field_' . $this->field_obj->id] ) ) {
					$option_value = sanitize_text_field( $_POST['field_' . $this->field_obj->id] );
				}
			}

			// Run the allowed option name through the before_save filter, so
			// we'll be sure to get a match.
			$allowed_options = xprofile_sanitize_data_value_before_save( $options[$k]->name, false, false );
			$selected        = '';

			if ( $option_value === $allowed_options || ( empty( $option_value ) && ! empty( $options[$k]->is_default_option ) ) ) {
				$selected = ' checked="checked"';
			}

			$new_html = sprintf( '<label for="%3$s" class="option-label"><input %1$s type="radio" name="%2$s" id="%3$s" value="%4$s">%5$s</label>',
				$selected,
				esc_attr( sz_get_the_profile_field_input_name() ),
				esc_attr( "option_{$options[$k]->id}" ),
				esc_attr( stripslashes( $options[$k]->name ) ),
				esc_html( stripslashes( $options[$k]->name ) )
			);

			/**
			 * Filters the HTML output for an individual field options radio button.
			 *
			 * @since 1.1.0
			 *
			 * @param string $new_html Label and radio input field.
			 * @param object $value    Current option being rendered for.
			 * @param int    $id       ID of the field object being rendered.
			 * @param string $selected Current selected value.
			 * @param string $k        Current index in the foreach loop.
			 */
			$html .= apply_filters( 'sz_get_the_profile_field_options_radio', $new_html, $options[$k], $this->field_obj->id, $selected, $k );
		}

		printf( '<div id="%1$s" class="input-options radio-button-options">%2$s</div>',
			esc_attr( 'field_' . $this->field_obj->id ),
			$html
		);
	}

	/**
	 * Output HTML for this field type on the wp-admin Profile Fields screen.
	 *
	 * Must be used inside the {@link sz_profile_fields()} template loop.
	 *
	 * @since 2.0.0
	 *
	 * @param array $raw_properties Optional key/value array of permitted attributes that you want to add.
	 */
	public function admin_field_html( array $raw_properties = array() ) {
		sz_the_profile_field_options();

		if ( sz_get_the_profile_field_is_required() ) {
			return;
		} ?>

		<a class="clear-value" href="javascript:clear( '<?php echo esc_js( sz_get_the_profile_field_input_name() ); ?>' );">
			<?php esc_html_e( 'Clear', 'sportszone' ); ?>
		</a>

		<?php
	}

	/**
	 * Output HTML for this field type's children options on the wp-admin Profile Fields "Add Field" and "Edit Field" screens.
	 *
	 * Must be used inside the {@link sz_profile_fields()} template loop.
	 *
	 * @since 2.0.0
	 *
	 * @param SZ_XProfile_Field $current_field The current profile field on the add/edit screen.
	 * @param string            $control_type  Optional. HTML input type used to render the current
	 *                                         field's child options.
	 */
	public function admin_new_field_html( SZ_XProfile_Field $current_field, $control_type = '' ) {
		parent::admin_new_field_html( $current_field, 'radio' );
	}
}
