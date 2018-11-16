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

function get_user_clubs($value = false, $array = false){
	
	$clubs_args = array(
		'user_id'		=> get_current_user_id( ),
		'group_type'	=> 'club'
	);
	
	if($array):
		if(sz_has_groups($clubs_args)):
			$clubs = '<option value="0" disabled selected>Select Club</option>';
			while(sz_groups()): sz_the_group();
				$group_id = sz_get_group_id();
				$clubs .= '<option value="'.sz_get_group_id().'" '.selected( $value, $group_id, false ) .'>'.sz_get_group_name().'</option>';
			endwhile;
			if(!is_numeric($value)){
				$clubs .= '<option value="'.$value.'" '.selected( $value, $value, false ) .'>'.$value.'</option>';
			}
		endif;
	else:
		if(sz_has_groups($clubs_args)):
			$clubs = array();
			while(sz_groups()): sz_the_group();
				$group_id = sz_get_group_id();
				$clubs[$group_id] = sz_get_group_name();
			endwhile;
		endif;
	endif;
	
	return $clubs;
}

function cmb2_render_callback_for_select_club( $field, $value, $object_id, $object_type, $field_type ) {
	pw_select_2_setup_admin_scripts();
	$event_id = sz_get_event_id();
	$value = wp_parse_args( $value, array(
		'club' => '',
	) );
	
	$options = get_user_clubs($value['club'], true);
	$option_cat = $field_type->concat_items();
	echo $field_type->select( array(
			'class'            => 'pw_select2 pw_select',
			'name' 		=> $field_type->_name('[club]'),
			'id' 		=> $field_type->_id('_club'),	
			'options'	=>  $options,
			'data-placeholder' => $field->args( 'attributes', 'placeholder' ) ? $field->args( 'attributes', 'placeholder' ) : $field->args( 'description' ),
	) );

}
add_action( 'cmb2_render_select_club', 'cmb2_render_callback_for_select_club', 10, 5 );

function cmb2_render_callback_for_select_team( $field, $value, $object_id, $object_type, $field_type ) {
	pw_select_2_setup_admin_scripts();
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
	if(isset($value['country'])){
		$default_value = $value['country'];
	} else {
		$default_value = '';
	}
	echo $field_type->select( array(
			'class'            => 'crs-country pw_select2 pw_select',
			'name' 		=> $field_type->_name('[country]'),
			'id' 		=> $field_type->_id('_country'),	
			'data-placeholder' => $field->args( 'attributes', 'placeholder' ) ? $field->args( 'attributes', 'placeholder' ) : $field->args( 'description' ),
			'data-default-value'	=> $default_value,
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
			'class'            => 'province pw_select2 pw_select',
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


 function pw_select_2_setup_admin_scripts() {
	$asset_path = apply_filters( 'pw_cmb2_field_select2_asset_path', plugins_url( '', __FILE__  ) );

	wp_register_script( 'pw-select2', $asset_path . '/js/select2.min.js', array( 'jquery-ui-sortable' ), '4.0.3' );
	wp_enqueue_script( 'pw-select2-init', $asset_path . '/js/script.js', array( 'cmb2-scripts', 'pw-select2' ), '1' );
	wp_register_style( 'pw-select2', $asset_path . '/css/select2.min.css', array(), '4.0.3' );
	wp_enqueue_style( 'pw-select2-tweaks', $asset_path . '/css/style.css', array( 'pw-select2' ), '1');
}

