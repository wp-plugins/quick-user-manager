<?php
/* handle field output */
function qum_password_repeat_handler( $output, $form_location, $field, $user_id, $field_check_errors, $request_data ){
	$item_title = apply_filters( 'qum_'.$form_location.'_password_item_title', qum_icl_t( 'plugin quick-user-manager-pro', 'default_field_'.$field['id'].'_title_translation', $field['field-title'] ) );
	$item_description = qum_icl_t( 'plugin quick-user-manager-pro', 'default_field_'.$field['id'].'_description_translation', $field['description'] );
	
	if ( $form_location != 'back_end' ){
		$error_mark = ( ( $field['required'] == 'Yes' ) ? '<span class="qum-required" title="'.qum_required_field_error($field["field-title"]).'">*</span>' : '' );
					
		if ( array_key_exists( $field['id'], $field_check_errors ) )
			$error_mark = '<img src="'.QUM_PLUGIN_URL.'assets/images/pencil_delete.png" title="'.qum_required_field_error($field["field-title"]).'"/>';

        $output = '
			<label for="passw2">' . $item_title.$error_mark . '</label>
			<input class="text-input" name="passw2" maxlength="'. apply_filters( 'qum_maximum_character_length', 70 ) .'" type="password" id="passw2" value="" autocomplete="off" />';
        if( !empty( $item_description ) )
            $output .= '<span class="qum-description-delimiter">'.$item_description.'</span>';
	}
		
	return apply_filters( 'qum_'.$form_location.'_repeat_password', $output, $form_location, $field, $user_id, $field_check_errors, $request_data );
}
add_filter( 'qum_output_form_field_default-repeat-password', 'qum_password_repeat_handler', 10, 6 );


/* handle field validation */
function qum_check_repeat_password_value( $message, $field, $request_data, $form_location ){
	if ( $form_location == 'register' ){
		if ( ( isset( $request_data['passw2'] ) && ( trim( $request_data['passw2'] ) == '' ) ) && ( $field['required'] == 'Yes' ) )
			return qum_required_field_error($field["field-title"]);
		
		elseif ( !isset( $request_data['passw2'] ) && ( $field['required'] == 'Yes' ) )
			return qum_required_field_error($field["field-title"]);
			
		elseif ( isset( $request_data['passw1'] ) && isset( $request_data['passw2'] ) && ( trim( $request_data['passw1'] ) != trim( $request_data['passw2'] ) ) && ( $field['required'] == 'Yes' ) )
			return __( "The passwords do not match", "quickusermanager" );
	
	}elseif ( $form_location == 'edit_profile' ){
		if ( isset( $request_data['passw1'] ) && isset( $request_data['passw2'] ) && ( trim( $request_data['passw1'] ) != trim( $request_data['passw2'] ) ) )
			return __( "The passwords do not match", "quickusermanager" );
	}

    return $message;
}
add_filter( 'qum_check_form_field_default-repeat-password', 'qum_check_repeat_password_value', 10, 4 );