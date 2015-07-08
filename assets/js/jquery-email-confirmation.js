function qum_display_page_select( value ){
	if ( value == 'yes' ){
		jQuery ( '#qum-settings-activation-page' ).show();
		jQuery ( '.dynamic1' ).show();
	
	}else{
		jQuery ( '#qum-settings-activation-page' ).hide();
		jQuery ( '.dynamic1' ).hide();
	}
}


function qum_display_page_select_aa( value ){
	if ( value == 'yes' )
		jQuery ( '.dynamic2' ).show();
	
	else
		jQuery ( '.dynamic2' ).hide();
}


jQuery(function() {
	if ( ( jQuery( '#qum_settings_email_confirmation' ).val() == 'yes' ) || ( jQuery( '#qum_general_settings_hidden' ).val() == 'multisite' ) ){
		jQuery ( '#qum-settings-activation-page' ).show();
		jQuery ( '.dynamic1' ).show();
	
	}else{
		jQuery ( '#qum-settings-activation-page' ).hide();
		jQuery ( '.dynamic1' ).hide();
	}
	
	
	if ( jQuery( '#adminApprovalSelect' ).val() == 'yes' )
		jQuery ( '.dynamic2' ).show();
	
	else
		jQuery ( '.dynamic2' ).hide();
});