<?php
/* handle field output */
function qum_nickname_handler( $output, $form_location, $field, $user_id, $field_check_errors, $request_data ){
	$item_title = apply_filters( 'qum_'.$form_location.'_nickname_item_title', qum_icl_t( 'plugin quick-user-manager-pro', 'default_field_'.$field['id'].'_title_translation', $field['field-title'] ) );
	$item_description = qum_icl_t( 'plugin quick-user-manager-pro', 'default_field_'.$field['id'].'_description_translation', $field['description'] );
	$input_value = '';
	if( $form_location == 'edit_profile' )
		$input_value = get_the_author_meta( 'nickname', $user_id );
	
	if ( trim( $input_value ) == '' )
		$input_value = $field['default-value'];
		
	$input_value = ( isset( $request_data['nickname'] ) ? trim( $request_data['nickname'] ) : $input_value );
	
	if ( $form_location != 'back_end' ){
		$error_mark = ( ( $field['required'] == 'Yes' ) ? '<span class="qum-required" title="'.qum_required_field_error($field["field-title"]).'">*</span>' : '' );
					
		if ( array_key_exists( $field['id'], $field_check_errors ) )
			$error_mark = '<img src="'.QUM_PLUGIN_URL.'assets/images/pencil_delete.png" title="'.qum_required_field_error($field["field-title"]).'"/>';

        $output = '
			<label for="nickname">'.$item_title.$error_mark.'</label>
			<input class="text-input default_field_nickname" name="nickname" maxlength="'. apply_filters( 'qum_maximum_character_length', 70 ) .'" type="text" id="nickname" value="'. esc_attr( wp_unslash( $input_value ) ) .'" />';
        if( !empty( $item_description ) )
            $output .= '<span class="qum-description-delimiter">'. $item_description .'</span>';

		return apply_filters( 'qum_'.$form_location.'_nickname', $output, $form_location, $field, $user_id, $field_check_errors, $request_data );
	}
}
add_filter( 'qum_output_form_field_default-nickname', 'qum_nickname_handler', 10, 6 );


/* handle field validation */
function qum_check_nickname_value( $message, $field, $request_data, $form_location ){
    if( $field['required'] == 'Yes' ){
        if( ( isset( $request_data['nickname'] ) && ( trim( $request_data['nickname'] ) == '' ) ) || !isset( $request_data['nickname'] ) ){
            return qum_required_field_error($field["field-title"]);
        }
    }

    return $message;
}
add_filter( 'qum_check_form_field_default-nickname', 'qum_check_nickname_value', 10, 4 );

/* handle field save */
function qum_userdata_add_nickname( $userdata, $global_request ){
	if ( isset( $global_request['nickname'] ) )
		$userdata['nickname'] = sanitize_text_field( trim( $global_request['nickname'] ) );
		
	return $userdata;
}
add_filter( 'qum_build_userdata', 'qum_userdata_add_nickname', 10, 2 );