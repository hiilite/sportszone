<?php
/**
 * SportsZone Xprofile Custom Field Types
 *
 * @package    SportsZone Xprofile Custom Field Types
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

use SZXProfileCFTR\Contracts\Field_Type_Multi_Valued;
use SZXProfileCFTR\Contracts\Field_Type_Selectable;

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Get a mapping of field type to their implementation class.
 *
 * @return array Key/value pairs (field type => class name).
 */
function szxcftr_get_field_types() {
	$fields = array(
		// I haven't changed the field type name to make it drop in replacement for SportsZone Xprofile Custom Fields Type plugin.
		'birthdate'                    => 'SZXProfileCFTR\Field_Types\Field_Type_Birthdate',
		'email'                        => 'SZXProfileCFTR\Field_Types\Field_Type_Email',
		'web'                          => 'SZXProfileCFTR\Field_Types\Field_Type_Web',
		'datepicker'                   => 'SZXProfileCFTR\Field_Types\Field_Type_Datepicker',
		'select_custom_post_type'      => 'SZXProfileCFTR\Field_Types\Field_Type_Select_Post_Type',
		'multiselect_custom_post_type' => 'SZXProfileCFTR\Field_Types\Field_Type_Multi_Select_Post_Type',
		'select_custom_taxonomy'       => 'SZXProfileCFTR\Field_Types\Field_Type_Select_Taxonomy',
		'multiselect_custom_taxonomy'  => 'SZXProfileCFTR\Field_Types\Field_Type_Multi_Select_Taxonomy',
		'checkbox_acceptance'          => 'SZXProfileCFTR\Field_Types\Field_Type_Checkbox_Acceptance',
		'image'                        => 'SZXProfileCFTR\Field_Types\Field_Type_Image',
		'file'                         => 'SZXProfileCFTR\Field_Types\Field_Type_File',
		'color'                        => 'SZXProfileCFTR\Field_Types\Field_Type_Color',
		'decimal_number'               => 'SZXProfileCFTR\Field_Types\Field_Type_Decimal_Number',
		'number_minmax'                => 'SZXProfileCFTR\Field_Types\Field_Type_Number_Min_Max',
		'slider'                       => 'SZXProfileCFTR\Field_Types\Field_Type_Slider',
		'fromto'                       => 'SZXProfileCFTR\Field_Types\Field_Type_From_To',
		// end of the SportsZone Xprofile Custom Fields Type plugin's field type.

	);

	return $fields;
}

/**
 * Get field types which support the selec2  js.
 *
 * @return array
 */
function szxcftr_get_selectable_field_types() {
	$types = array(
		'selectbox',
		'multiselectbox',
		'select_custom_post_type',
		'multiselect_custom_post_type',
		'select_custom_taxonomy',
		'multiselect_custom_taxonomy',
	);

	return apply_filters( 'szxcftr_selectable_types', $types );
}
/**
 * Get an array of allowed file extensions.
 *
 * @param string $type possible values 'image', 'file'.
 *
 * @return array
 */
function szxcftr_get_allowed_file_extensions( $type ) {

	$extensions = array(
		'file'  => array(
			'doc',
			'docx',
			'pdf',
		),
		'image' => array(
			'jpg',
			'jpeg',
			'gif',
			'png',
		),
	);

	$extensions = apply_filters( 'szxcftr_allowed_extensions', $extensions );

	return isset( $extensions[ $type ] ) ? $extensions[ $type ] : array();
}

/**
 * Get maximum allowed file size.
 *
 * @param string $type field type.
 *
 * @return int|mixed
 */
function szxcftr_get_allowed_file_size( $type ) {

	$sizes = array(
		'file'  => 8,
		'image' => 8,
	);

	$sizes = apply_filters( 'szxcftr_allowed_sizes', $sizes );
	return isset( $sizes[ $type ] ) ? $sizes[ $type ] : 0;
}

/**
 * Is field type selectable?
 *
 * Used for deciding when to apply select2
 *
 * @param SZ_XProfile_Field $field field object.
 *
 * @return bool
 */
function szxcftr_is_selectable_field( $field ) {
	$selectable_types = szxcftr_get_selectable_field_types();
	return in_array( $field->type, $selectable_types ) || $field->type_obj instanceof Field_Type_Selectable ;
}

/**
 * Is field type multi valued?
 *
 * Used for deciding when to apply select2
 *
 * @param SZ_XProfile_Field $field field object.
 *
 * @return bool
 */
function szxcftr_is_multi_valued_field( $field ) {
	$selectable_types = apply_filters( 'szxcftr_multi_valued_types', array(
		'multiselectbox',
	));

	return in_array( $field->type, $selectable_types ) || $field->type_obj instanceof Field_Type_Multi_Valued ;
}

/**
 * It is a work around to get the field at the time is_valid() is called on field types.
 * SportsZone does not pass the id at the moment.
 *
 * @return SZ_XProfile_Field|null
 */
function szxcftr_get_current_field() {
	return sz_xprofile_cftr()->data->current_field;
}

/**
 * Save the current field value.
 *
 * @param SZ_XProfile_Field|null $field field object or null.
 */
function szxcftr_set_current_field( $field ) {
	sz_xprofile_cftr()->data->current_field = $field;
}
