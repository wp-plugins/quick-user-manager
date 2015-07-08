<?php
function qum_default_about_yourself_handler( $output, $form_location, $field, $user_id, $field_check_errors, $request_data ){
	if ( $field['field'] == 'Default - About Yourself (Heading)' ){
		$item_title = apply_filters( 'qum_'.$form_location.'_default_heading_about_yourself_'.$field['id'].'_item_title', qum_icl_t( 'plugin quick-user-manager-pro', 'custom_field_'.$field['id'].'_title_translation', $field['field-title'] ) );
		$item_description = qum_icl_t( 'plugin quick-user-manager-pro', 'default_field_'.$field['id'].'_description_translation', $field['description'] );

        $output = '<h4>'.$item_title.'</h4><span class="qum-description-delimiter">'.$item_description.'</span>';
		
		return apply_filters( 'qum_'.$form_location.'_default_heading_about_yourself_'.$field['id'], $output, $form_location, $field, $user_id, $field_check_errors, $request_data );
	}
}
add_filter( 'qum_output_form_field_default-about-yourself-heading', 'qum_default_about_yourself_handler', 10, 6 );