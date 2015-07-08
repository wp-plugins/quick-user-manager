<?php
/* handle field output */
function qum_username_handler( $output, $form_location, $field, $user_id, $field_check_errors, $request_data ){	
	$item_title = apply_filters( 'qum_'.$form_location.'_username_item_title', qum_icl_t( 'plugin quick-user-manager-pro', 'default_field_'.$field['id'].'_title_translation', $field['field-title'] ) );
	$item_description = qum_icl_t( 'plugin quick-user-manager-pro', 'default_field_'.$field['id'].'_description_translation', $field['description'] );
	
	$input_value = ( ( $form_location == 'edit_profile' ) ? get_the_author_meta( 'user_login', $user_id ) : '' );
	
	$input_value = ( ( trim( $input_value ) == '' ) ? $field['default-value'] : $input_value );
		
	$input_value = ( isset( $request_data['username'] ) ? trim( $request_data['username'] ) : $input_value );

	if ( $form_location != 'back_end' ){
		$error_mark = ( ( $field['required'] == 'Yes' ) ? '<span class="qum-required" title="'.qum_required_field_error($field["field-title"]).'">*</span>' : '' );
					
		if ( array_key_exists( $field['id'], $field_check_errors ) )
			$error_mark = '<img src="'.QUM_PLUGIN_URL.'assets/images/pencil_delete.png" title="'.qum_required_field_error($field["field-title"]).'"/>';
		
		$readonly = ( ( $form_location == 'edit_profile' ) ? ' disabled="disabled"' : '' );

        $output = '
			<label for="username">'.$item_title.$error_mark.'</label>
			<input class="text-input default_field_username" name="username" maxlength="'. apply_filters( 'qum_maximum_character_length', 70 ) .'" type="text" id="username" value="'. esc_attr( $input_value ) .'"'.$readonly.'/>';
        if( !empty( $item_description ) )
            $output .= '<span class="qum-description-delimiter">'.$item_description.'</span>';
	}
		
	return apply_filters( 'qum_'.$form_location.'_username', $output, $form_location, $field, $user_id, $field_check_errors, $request_data );
}
add_filter( 'qum_output_form_field_default-username', 'qum_username_handler', 10, 6 );


/* handle field validation */
function qum_check_username_value( $message, $field, $request_data, $form_location ){
	global $wpdb;

    if( $field['required'] == 'Yes' ){
        if( ( isset( $request_data['username'] ) && ( trim( $request_data['username'] ) == '' ) ) || ( $form_location == 'register' && !isset( $request_data['username'] ) ) ){
            return qum_required_field_error($field["field-title"]);
        }

    }

    if( !empty( $request_data['username'] ) ){
        if( $form_location == 'register' )
            $search_by_user_login = get_users( 'search='.$request_data['username'] );
        if( !empty( $search_by_user_login ) ){
            return __( 'This username already exists.', 'quickusermanager' ) .'<br/>'. __( 'Please try a different one!', 'quickusermanager' );
        }
		if( ! validate_username( $request_data['username'] ) ) {
			return __( 'This username is invalid because it uses illegal characters.', 'quickusermanager' ) .'<br/>'. __( 'Please enter a valid username.', 'quickusermanager' );
		}

        $qum_generalSettings = get_option('qum_general_settings');
        if ( is_multisite() || ( !is_multisite() && $qum_generalSettings['emailConfirmation'] == 'yes'  ) ){
            $userSignup = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."signups WHERE user_login = %s", $request_data['username'] ) );
            if ( !empty( $userSignup ) ){
                return __( 'This username is already reserved to be used soon.', 'quickusermanager') .'<br/>'. __( 'Please try a different one!', 'quickusermanager' );
            }
        }
    }

    return $message;
}
add_filter( 'qum_check_form_field_default-username', 'qum_check_username_value', 10, 4 );


/* handle field save */
function qum_userdata_add_username( $userdata, $global_request ){
	if ( isset( $global_request['username'] ) )
		$userdata['user_login'] = sanitize_user( trim( $global_request['username'] ) );

	return $userdata;
}
add_filter( 'qum_build_userdata', 'qum_userdata_add_username', 10, 2 );