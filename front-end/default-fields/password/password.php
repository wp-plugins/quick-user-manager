<?php
/* handle field output */
function qum_password_handler( $output, $form_location, $field, $user_id, $field_check_errors, $request_data ){
	$item_title = apply_filters( 'qum_'.$form_location.'_password_item_title', qum_icl_t( 'plugin quick-user-manager-pro', 'default_field_'.$field['id'].'_title_translation', $field['field-title'] ) );
	$item_description = qum_icl_t( 'plugin quick-user-manager-pro', 'default_field_'.$field['id'].'_description_translation', $field['description'] );

	if ( $form_location != 'back_end' ){
		$error_mark = ( ( $field['required'] == 'Yes' ) ? '<span class="qum-required" title="'.qum_required_field_error($field["field-title"]).'">*</span>' : '' );
					
		if ( array_key_exists( $field['id'], $field_check_errors ) )
			$error_mark = '<img src="'.QUM_PLUGIN_URL.'assets/images/pencil_delete.png" title="'.qum_required_field_error($field["field-title"]).'"/>';

        $output = '
			<label for="passw1">' . $item_title.$error_mark . '</label>
			<input class="text-input" name="passw1" maxlength="'. apply_filters( 'qum_maximum_character_length', 70 ) .'" type="password" id="passw1" value="" autocomplete="off" />';
        if( !empty( $item_description ) )
            $output .= '<span class="qum-description-delimiter">'.$item_description.' '.qum_password_length_text().'</span>';
        else
            $output .= '<span class="qum-description-delimiter">'.qum_password_length_text().'</span>';

        /* if we have active the password strength checker */
        $output .= qum_password_strength_checker_html();

	}
		
	return apply_filters( 'qum_'.$form_location.'_password', $output, $form_location, $field, $user_id, $field_check_errors, $request_data );
}
add_filter( 'qum_output_form_field_default-password', 'qum_password_handler', 10, 6 );

/* handle field validation */
function qum_check_password_value( $message, $field, $request_data, $form_location ){
	if ( $form_location == 'register' ){
		if ( ( isset( $request_data['passw1'] ) && ( trim( $request_data['passw1'] ) == '' ) ) && ( $field['required'] == 'Yes' ) )
			return qum_required_field_error($field["field-title"]);
		
		elseif ( !isset( $request_data['passw1'] ) && ( $field['required'] == 'Yes' ) )
			return qum_required_field_error($field["field-title"]);
	}

    if ( trim( $request_data['passw1'] ) != '' ){
        $qum_generalSettings = get_option( 'qum_general_settings' );

        if( qum_check_password_length( $request_data['passw1'] ) )
            return '<br/>'. sprintf( __( "The password must have the minimum length of %s characters", "quickusermanager" ), $qum_generalSettings['minimum_password_length'] );


        if( qum_check_password_strength() ){
            return '<br/>' . sprintf( __( "The password must have a minimum strength of %s", "quickusermanager" ), qum_check_password_strength() );
        }
    }

    return $message;
}
add_filter( 'qum_check_form_field_default-password', 'qum_check_password_value', 10, 4 );

/* handle field save */
function qum_userdata_add_password( $userdata, $global_request ){
	if ( isset( $global_request['passw1'] ) && ( trim( $global_request['passw1'] ) != '' ) )
		$userdata['user_pass'] = trim( $global_request['passw1'] );
	
	return $userdata;
}
add_filter( 'qum_build_userdata', 'qum_userdata_add_password', 10, 2 );