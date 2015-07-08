<?php
/**
 * Function that creates the "Show/Hide the Admin Bar on the Front-End" submenu page
 *
 * @since v.1.0
 *
 * @return void
 */
function qum_show_hide_admin_bar_submenu_page() {
	add_submenu_page( 'quick-user-manager', __( 'Show/Hide the Admin Bar on the Front-End', 'quickusermanager' ), __( 'Admin Bar Settings', 'quickusermanager' ), 'manage_options', 'quick-user-manager-admin-bar-settings', 'qum_show_hide_admin_bar_content' ); 
}
add_action( 'admin_menu', 'qum_show_hide_admin_bar_submenu_page', 4 );


function qum_generate_admin_bar_default_values( $roles ){
	$qum_display_admin_settings = get_option( 'qum_display_admin_settings', 'not_found' );
	
	if ( $qum_display_admin_settings == 'not_found' ){
        if( !empty( $roles ) ){
            $admin_settings = array();
            foreach ( $roles as $role ){
                if( !empty( $role['name'] ) )
                    $admin_settings[$role['name']] = 'default';
            }

            update_option( 'qum_display_admin_settings', $admin_settings );
        }
	}
}


/**
 * Function that adds content to the "Show/Hide the Admin Bar on the Front-End" submenu page
 *
 * @since v.1.0
 *
 * @return string
 */
function qum_show_hide_admin_bar_content() {
	global $wp_roles;
	
	qum_generate_admin_bar_default_values( $wp_roles );
	?>
	
	<div class="wrap qum-wrap qum-admin-bar">
	
		<h2><?php _e( 'Admin Bar Settings', 'quickusermanager' );?></h2>
		<p><?php _e( 'Choose which user roles view the admin bar in the front-end of the website.', 'quickusermanager' ); ?>
		<form method="post" action="options.php#show-hide-admin-bar">
		<?php	
			$admin_bar_settings = get_option( 'qum_display_admin_settings' );
			settings_fields( 'qum_display_admin_settings' );
		?>
		<table class="widefat">
			<thead>
				<tr>
					<th class="row-title" scope="col"><?php _e('User-Role', 'quickusermanager');?></th>
					<th scope="col"><?php _e('Visibility', 'quickusermanager');?></th>
				</tr>
			</thead>
				<tbody>
					<?php
					$alt_i = 0;
					foreach ( $wp_roles->roles as $role ) {
						$alt_i++;
						$key = $role['name'];
						$setting_exists = !empty( $admin_bar_settings[$key] );
						$alt_class = ( ( $alt_i%2 == 0 ) ? ' class="alternate"' : '' );
						
						echo'<tr'.$alt_class.'>
								<td>'.translate_user_role($key).'</td>
								<td>
									<span><input id="rd'.$key.'" type="radio" name="qum_display_admin_settings['.$key.']" value="default"'.( ( !$setting_exists || $admin_bar_settings[$key] == 'default' ) ? ' checked' : '' ).'/><label for="rd'.$key.'">'.__( 'Default', 'quickusermanager' ).'</label></span>
									<span><input id="rs'.$key.'" type="radio" name="qum_display_admin_settings['.$key.']" value="show"'.( ( $setting_exists && $admin_bar_settings[$key] == 'show') ? ' checked' : '' ).'/><label for="rs'.$key.'">'.__( 'Show', 'quickusermanager' ).'</label></span>
									<span><input id="rh'.$key.'" type="radio" name="qum_display_admin_settings['.$key.']" value="hide"'.( ( $setting_exists && $admin_bar_settings[$key] == 'hide') ? ' checked' : '' ).'/><label for="rh'.$key.'">'.__( 'Hide', 'quickusermanager' ).'</label></span>
								</td>
							</tr>';
					}
					?>
				
		</table>

		<div id="qum_submit_button_div">
			<input type="hidden" name="action" value="update" />
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" /> 
			</p>
		</div>
		
		</form>
		
	</div>
	<?php
}

/**
 * Function that changes the username on the top right menu (admin bar)
 *
 * @since v.1.0
 *
 * @return string
 */
function qum_replace_username_on_admin_bar( $wp_admin_bar ) {
	$qum_general_settings = get_option( 'qum_general_settings' );
	
	if ( isset( $qum_general_settings['loginWith'] ) && ( $qum_general_settings['loginWith'] == 'email' ) ){
		$current_user = wp_get_current_user();
	
		$my_account_main = $wp_admin_bar->get_node( 'my-account' );
		$new_title1 = str_replace( $current_user->display_name, $current_user->user_email, $my_account_main->title );
		$wp_admin_bar->add_node( array( 'id' => 'my-account', 'title' => $new_title1 ) );
		
		$my_account_sub = $wp_admin_bar->get_node( 'user-info' );
		$wp_admin_bar->add_node( array( 'parent' => 'user-actions', 'id' => 'user-info', 'title'  => get_avatar( $current_user->ID, 64 )."<span class='display-name'>{$current_user->user_email}</span>", 'href' => get_edit_profile_url( $current_user->ID ), 'meta'   => array( 'tabindex' => -1 ) ) );
	}
	
	return $wp_admin_bar;
}
add_filter( 'admin_bar_menu', 'qum_replace_username_on_admin_bar', 25 );