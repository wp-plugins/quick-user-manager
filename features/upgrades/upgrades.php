<?php
include_once ( QUM_PLUGIN_DIR.'/features/upgrades/upgrades-functions.php' );

/**
 * Function that assures backwards compatibility for all future versions, where this is needed
 *
 * @since v.1.0.0
 *
 * @return void
 */
function qum_update_patch(){
	if ( !get_option( 'qum_version' ) ) {
		add_option( 'qum_version', '1.0.0' );
		
		do_action( 'qum_set_initial_version_number', QUICK_USER_MANAGER_VERSION );
	}

	$qum_version = get_option( 'qum_version' );
	
	do_action( 'qum_before_default_changes', QUICK_USER_MANAGER_VERSION, $qum_version );
	
	if ( version_compare( QUICK_USER_MANAGER_VERSION, $qum_version, '>' ) ) {
		if ( ( QUICK_USER_MANAGER == 'Quick User Manager Pro' ) || ( QUICK_USER_MANAGER == 'Quick User Manager Hobbyist' ) ){
			$upload_dir = wp_upload_dir(); 
			
			wp_mkdir_p( $upload_dir['basedir'].'/QUICK_USER_MANAGER' );
			wp_mkdir_p( $upload_dir['basedir'].'/QUICK_USER_MANAGER/attachments/' );
			wp_mkdir_p( $upload_dir['basedir'].'/QUICK_USER_MANAGER/avatars/' );
			
			// Flush the rewrite rules and add them, if need be, the proper way.
			if ( function_exists( 'qum_flush_rewrite_rules' ) )
				qum_flush_rewrite_rules();
			
			qum_pro_hobbyist_v1_0_0();
		}
		
		if ( QUICK_USER_MANAGER == 'Quick User Manager Pro' ){
			qum_pro_v1_0_0();
		}
		
		update_option( 'qum_version', QUICK_USER_MANAGER_VERSION );
	}

	//this should run only once, mainly if the old version is < 2.0 (can be anything)
	if ( version_compare( $qum_version, 2.0, '<' ) ) {
		if ( ( QUICK_USER_MANAGER == 'Quick User Manager Pro' ) || ( QUICK_USER_MANAGER == 'Quick User Manager Hobbyist' ) || ( QUICK_USER_MANAGER == 'Quick User Manager Free' ) ){
			qum_pro_hobbyist_free_v2_0();
		}
		
		if ( QUICK_USER_MANAGER == 'Quick User Manager Pro' ){
			qum_pro_userlisting_compatibility_upgrade();
			qum_pro_email_customizer_compatibility_upgrade();
		}
	}
	
	do_action ( 'qum_after_default_changes', QUICK_USER_MANAGER_VERSION, $qum_version );	
}
add_action ( 'init', 'qum_update_patch' );