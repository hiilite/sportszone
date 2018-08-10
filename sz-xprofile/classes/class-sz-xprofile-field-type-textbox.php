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
 * Textbox xprofile field type.
 *
 * @since 2.0.0
 */
class SZ_XProfile_Field_Type_Textbox extends SZ_XProfile_Field_Type {

	/**
	 * Constructor for the textbox field type.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		parent::__construct();

		$this->category = _x( 'Single Fields', 'xprofile field type category', 'sportszone' );
		$this->name     = _x( 'Text Box', 'xprofile field type', 'sportszone' );

		$this->set_format( '/^.*$/', 'replace' );

		/**
		 * Fires inside __construct() method for SZ_XProfile_Field_Type_Textbox class.
		 *
		 * @since 2.0.0
		 *
		 * @param SZ_XProfile_Field_Type_Textbox $this Current instance of
		 *                                             the field type text box.
		 */
		do_action( 'sz_xprofile_field_type_textbox', $this );
	}

	/**
	 * Output the edit field HTML for this field type.
	 * Must be used inside the {@link sz_profile_fields()} template loop.
	 *
	 * @since 2.0.0
	 *
	 * @param array $raw_properties Optional key/value array of
	 *                              {@link http://dev.w3.org/html5/markup/input.text.html permitted attributes}
	 *                              that you want to add.
	 */
	public function edit_field_html( array $raw_properties = array() ) {

		// User_id is a special optional parameter that certain other fields
		// types pass to {@link sz_the_profile_field_options()}.
		if ( isset( $raw_properties['user_id'] ) ) {
			unset( $raw_properties['user_id'] );
		}

		$r = sz_parse_args( $raw_properties, array(
			'type'  => 'text',
			'value' => sz_get_the_profile_field_edit_value(),
		) ); ?>

		<legend id="<?php sz_the_profile_field_input_name(); ?>-1">
			<?php sz_the_profile_field_name(); ?>
			<?php sz_the_profile_field_required_label(); ?>
		</legend>

		<?php

		/** This action is documented in sz-xprofile/sz-xprofile-classes */
		do_action( sz_get_the_profile_field_errors_action() ); ?>

		<input <?php echo $this->get_edit_field_html_elements( $r ); ?> aria-labelledby="<?php sz_the_profile_field_input_name(); ?>-1" aria-describedby="<?php sz_the_profile_field_input_name(); ?>-3">

		<?php if ( sz_get_the_profile_field_description() ) : ?>
			<p class="description" id="<?php sz_the_profile_field_input_name(); ?>-3"><?php sz_the_profile_field_description(); ?></p>
		<?php endif; ?>

		<?php
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

		$r = sz_parse_args( $raw_properties, array(
			'type' => 'text'
		) ); ?>

		<label for="<?php sz_the_profile_field_input_name(); ?>" class="screen-reader-text"><?php
			/* translators: accessibility text */
			esc_html_e( 'Textbox', 'sportszone' );
		?></label>
		<input <?php echo $this->get_edit_field_html_elements( $r ); ?>>

		<?php
	}

	/**
	 * This method usually outputs HTML for this field type's children options on the wp-admin Profile Fields
	 * "Add Field" and "Edit Field" screens, but for this field type, we don't want it, so it's stubbed out.
	 *
	 * @since 2.0.0
	 *
	 * @param SZ_XProfile_Field $current_field The current profile field on the add/edit screen.
	 * @param string            $control_type  Optional. HTML input type used to render the
	 *                                         current field's child options.
	 */
	public function admin_new_field_html( SZ_XProfile_Field $current_field, $control_type = '' ) {}
}
