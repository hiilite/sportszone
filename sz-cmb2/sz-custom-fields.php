<?php 
	
function get_user_teams($value = false, $array = false){

	$teams_args = array(
		'user_id'		=> get_current_user_id( ),
		'group_type'	=> 'team'
	);
	
	if($array):
		if(sz_has_groups($teams_args)):
			$teams = '<option value="0" disabled selected>Select Team</option>';
			while(sz_groups()): sz_the_group();
				$group_id = sz_get_group_id();
				$teams .= '<option value="'.sz_get_group_id().'" '.selected( $value, $group_id, false ) .'>'.sz_get_group_name().'</option>';
			endwhile;
			if(!is_numeric($value)){
				$teams .= '<option value="'.$value.'" '.selected( $value, $value, false ) .'>'.$value.'</option>';
			}
		endif;
	else:
		if(sz_has_groups($teams_args)):
			$teams = array();
			while(sz_groups()): sz_the_group();
				$group_id = sz_get_group_id();
				$teams[$group_id] = sz_get_group_name();
			endwhile;
		endif;
	endif;
	
	return $teams;
}

function cmb2_render_callback_for_select_team( $field, $value, $object_id, $object_type, $field_type ) {
	$event_id = sz_get_event_id();
	$value = wp_parse_args( $value, array(
		'team' => '',
	) );
	
	$options = get_user_teams($value['team'], true);
	$option_cat = $field_type->concat_items();
	//print_r($option_cat);
	echo $field_type->select( array(
			'class'            => 'pw_select2 pw_select',
			'name' 		=> $field_type->_name('[team]'),
			'id' 		=> $field_type->_id('_team'),	
			'options'	=>  $options,
			'data-placeholder' => $field->args( 'attributes', 'placeholder' ) ? $field->args( 'attributes', 'placeholder' ) : $field->args( 'description' ),
	) );

}
add_action( 'cmb2_render_select_team', 'cmb2_render_callback_for_select_team', 10, 5 );


/**
 * Add the country select field
 *
 * @since 3.1.0
 *
 * @params  $field, $value, $object_id, $object_type, $field_type
 * @return array|null $value    See {@see sz_groups_set_group_type()}.
 */
function cmb2_render_callback_for_select_country( $field, $value, $object_id, $object_type, $field_type ) {
	echo $field_type->select( array(
			'class'            => 'crs-country',
			'name' 		=> $field_type->_name('[country]'),
			'id' 		=> $field_type->_id('_country'),	
			'data-placeholder' => $field->args( 'attributes', 'placeholder' ) ? $field->args( 'attributes', 'placeholder' ) : $field->args( 'description' ),
			'data-default-value'	=> $value['country'],
			'data-region-id'	=>	$field->args( 'attributes', 'region_id' ),
	) );

}
add_action( 'cmb2_render_select_country', 'cmb2_render_callback_for_select_country', 10, 5 );

/**
 * Add the province select field
 *
 * @since 3.1.0
 *
 * @params  $field, $value, $object_id, $object_type, $field_type
 * @return array|null $value    See {@see sz_groups_set_group_type()}.
 */
function cmb2_render_callback_for_select_province( $field, $value, $object_id, $object_type, $field_type ) {
	
	$default_value = (isset($value['province'])) ? $value['province'] : '';
	
	echo $field_type->select( array(
			'class'            => 'province',
			'name' 		=> $field_type->_name('[province]'),
			'id' 		=> $field_type->_id(),	
			'data-placeholder' => $field->args( 'attributes', 'placeholder' ) ? $field->args( 'attributes', 'placeholder' ) : $field->args( 'description' ),
			'data-default-value'	=> $default_value,
	) );

}
add_action( 'cmb2_render_select_province', 'cmb2_render_callback_for_select_province', 10, 5 );



/**
 * Add the multi_colorpicker  field
 *
 * @since 3.1.0
 *
 * @params  $field, $value, $object_id, $object_type, $field_type
 * @return array|null $value    See {@see sz_groups_set_group_type()}.
 */
function cmb2_render_callback_for_multi_colorpicker( $field, $value, $object_id, $object_type, $field_type ) {
	$value = wp_parse_args( $value, array(
		'color_one' => '',
		'color_two' => '',
		'color_three'=> '',
	) );


	echo $field_type->colorpicker( array(
			'name' 		=> $field_type->_name('[color_one]'),
			'id' 		=> $field_type->_id('_color_one'),	
			'value'		=> $value['color_one']
	) );
	echo $field_type->colorpicker( array(
			'name' 		=> $field_type->_name('[color_two]'),
			'id' 		=> $field_type->_id('_color_two'),	
			'value'		=> $value['color_two']
	) );
	echo $field_type->colorpicker( array(
			'name' 		=> $field_type->_name('[color_three]'),
			'id' 		=> $field_type->_id('_color_three'),	
			'value'		=> $value['color_three']
	) );

}
add_action( 'cmb2_render_multi_colorpicker', 'cmb2_render_callback_for_multi_colorpicker', 10, 5 );

function cmb2_sanitize_multi_colorpicker_callback( $override_value, $value ) {
	if ( ! is_array( $value ) ) {
		// Empty the value
		$value = '';
	}
	return $value;
}
add_filter( 'cmb2_sanitize_multi_colorpicker', 'cmb2_sanitize_multi_colorpicker_callback', 10, 2 );

