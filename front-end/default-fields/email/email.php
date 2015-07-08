<?php
/* handle field output */
function qum_email_handler( $output, $form_location, $field, $user_id, $field_check_errors, $request_data ){	
	$item_title = apply_filters( 'qum_'.$form_location.'_email_item_title', qum_icl_t( 'plugin quick-user-manager-pro', 'default_field_'.$field['id'].'_title_translation', $field['field-title'] ) );
	$item_description = qum_icl_t( 'plugin quick-user-manager-pro', 'default_field_'.$field['id'].'_description_translation', $field['description'] );
	$input_value = '';
	if( $form_location == 'edit_profile' )
		$input_value = get_the_author_meta( 'user_email', $user_id );
	
	if ( trim( $input_value ) == '' )
		$input_value = $field['default-value'];
		
	$input_value = ( isset( $request_data['email'] ) ? trim( $request_data['email'] ) : $input_value );
	
	if ( $form_location != 'back_end' ){
		$error_mark = ( ( $field['required'] == 'Yes' ) ? '<span class="qum-required" title="'.qum_required_field_error($field["field-title"]).'">*</span>' : '' );
					
		if ( array_key_exists( $field['id'], $field_check_errors ) )
			$error_mark = '<img src="'.QUM_PLUGIN_URL.'assets/images/pencil_delete.png" title="'.qum_required_field_error($field["field-title"]).'"/>';

        $output = '
			<label for="email">'.$item_title.$error_mark.'</label>
			<input class="text-input default_field_email" name="email" maxlength="'. apply_filters( 'qum_maximum_character_length', 70 ) .'" type="text" id="email" value="'. esc_attr( $input_value ) .'" />';
        if( !empty( $item_description ) )
            $output .= '<span class="qum-description-delimiter">'. $item_description .'</span>';

	}
		
	return apply_filters( 'qum_'.$form_location.'_email', $output, $form_location, $field, $user_id, $field_check_errors, $request_data );
}
add_filter( 'qum_output_form_field_default-e-mail', 'qum_email_handler', 10, 6 );


/* handle field validation */
function qum_check_email_value( $message, $field, $request_data, $form_location ){
	global $wpdb;

	if ( ( isset( $request_data['email'] ) && ( trim( $request_data['email'] ) == '' ) ) && ( $field['required'] == 'Yes' ) )
		return qum_required_field_error($field["field-title"]);

    if ( isset( $request_data['email'] ) && !is_email( trim( $request_data['email'] ) ) ){
        return __( 'The email you entered is not a valid email address.', 'quickusermanager' );
    }

	if ( empty( $request_data['email'] ) ) {
		return __( 'You must enter a valid email address.', 'quickusermanager' );
	}

    $qum_generalSettings = get_option( 'qum_general_settings' );
	if ( is_multisite() || ( !is_multisite() && ( isset( $qum_generalSettings['emailConfirmation'] ) && ( $qum_generalSettings['emailConfirmation'] == 'yes' ) ) ) ){
		$user_signup = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->base_prefix."signups WHERE user_email = %s", $request_data['email'] ) );

        if ( !empty( $user_signup ) ){
            if ( $form_location == 'register' ){
                    return __( 'This email is already reserved to be used soon.', 'quickusermanager' ) .'<br/>'. __( 'Please try a different one!', 'quickusermanager' );
            }
            else if ( $form_location == 'edit_profile' ){
                $current_user = wp_get_current_user();
                if ( $current_user->user_email != $request_data['email'] )
                    return __( 'This email is already reserved to be used soon.', 'quickusermanager' ) .'<br/>'. __( 'Please try a different one!', 'quickusermanager' );
            }
        }
	}
	
	$users = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->users} WHERE user_email = %s", $request_data['email'] ) );
	if ( !empty( $users ) ){
		if ( $form_location == 'register' )
			return __( 'This email is already in use.', 'quickusermanager' ) .'<br/>'. __( 'Please try a different one!', 'quickusermanager' );
		
		if ( $form_location == 'edit_profile' ){
            if( isset( $_GET['edit_user'] ) && ! empty( $_GET['edit_user'] ) )
                $current_user_id = $_GET['edit_user'];
            else{
                $current_user = wp_get_current_user();
                $current_user_id = $current_user->ID;
            }
			foreach ( $users as $user )
				if ( $user->ID != $current_user_id )
					return __( 'This email is already in use.', 'quickusermanager' ) .'<br/>'. __( 'Please try a different one!', 'quickusermanager' );
		}
	}

    return $message;
}
add_filter( 'qum_check_form_field_default-e-mail', 'qum_check_email_value', 10, 4 );

/* handle field save */
function qum_userdata_add_email( $userdata, $global_request ){
	if ( isset( $global_request['email'] ) )
		$userdata['user_email'] = sanitize_text_field( trim( $global_request['email'] ) );
	
	return $userdata;
}
add_filter( 'qum_build_userdata', 'qum_userdata_add_email', 10, 2 );