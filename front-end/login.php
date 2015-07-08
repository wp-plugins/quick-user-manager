<?php
// add hidden input to our form to identify that is a Quick User Manager form
function qum_login_form_bottom( $form_part, $args ){
	// we set this id in the wp_login_form() function
	if( $args['id_submit'] == 'qum-submit' ){
		if( in_the_loop() )
			$form_location = 'page';
		else
			$form_location = 'widget';
		
		$form_part = '<input type="hidden" name="qum_login" value="true"/>';
		$form_part .= '<input type="hidden" name="qum_form_location" value="'. $form_location .'"/>';
		$form_part .= '<input type="hidden" name="qum_request_url" value="'.qum_curpageurl().'"/>';
        $form_part .= '<input type="hidden" name="qum_lostpassword_url" value="'.$args['lostpassword_url'].'"/>';
	}
	
	return $form_part;
}
add_filter( 'login_form_bottom', 'qum_login_form_bottom', 10, 2 );

// when email login is enabled we need to change the post data for the username
function qum_change_login_with_email(){
    if( !empty( $_POST['log'] ) ){
		// only do this for our form
		if( isset( $_POST['qum_login'] ) ){
			global $wpdb, $_POST;

			$qum_generalSettings = get_option( 'qum_general_settings' );

			// if this setting is active, the posted username is, in fact the user's email
			if( isset( $qum_generalSettings['loginWith'] ) && ( $qum_generalSettings['loginWith'] == 'email' ) ){
				$username = $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM $wpdb->users WHERE user_email= %s LIMIT 1", trim( $_POST['log'] ) ) );
				
				if( !empty( $username ) )
					$_POST['log'] = $username;
				
				else {
					// if we don't have a username for the email entered we can't have an empty username because we will receive a field empty error
					$_POST['log'] = 'this_is_an_invalid_email'.time();
				}
			}

			// if this setting is active, the posted username is, in fact the user's email or username
			if( isset( $qum_generalSettings['loginWith'] ) && ( $qum_generalSettings['loginWith'] == 'usernameemail' ) ) {
				if( is_email( $_POST['log'] ) ) {
					$username = $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM $wpdb->users WHERE user_email= %s LIMIT 1", trim( $_POST['log'] ) ) );
				} else {
					$username = $_POST['log'];
				}

				if( !empty( $username ) )
					$_POST['log'] = $username;

				else {
					// if we don't have a username for the email entered we can't have an empty username because we will receive a field empty error
					$_POST['log'] = 'this_is_an_invalid_email'.time();
				}
			}
		}
	}
}
add_action( 'login_init', 'qum_change_login_with_email' );

// login redirect filter. used to redirect from wp-login.php if it errors out
function qum_login_redirect( $redirect_to, $redirect_url, $user ){
	// if login action initialized by our form
	if( isset( $_POST['qum_login'] ) ){
		if( is_wp_error( $user ) ) {
            // if we don't have a successful login we must redirect to the url of the form, so make sure this happens
            $redirect_to = $_POST['qum_request_url'];
            $request_form_location = $_POST['qum_form_location'];
            $error_string = $user->get_error_message();

            $qum_generalSettings = get_option('qum_general_settings');

            if (isset($qum_generalSettings['loginWith'])) {
                $LostPassURL = home_url('/wp-login.php?action=lostpassword');

                // if the Login shortcode has a lostpassword argument set, give the lost password error link that value
                if (!empty($_POST['qum_lostpassword_url'])) {
                    if ( qum_check_missing_http( $_POST['qum_lostpassword_url'] ) ) $LostPassURL = "http://" . $_POST['qum_lostpassword_url'];
                    else $LostPassURL = $_POST['qum_lostpassword_url'];
                }

                //apply filter to allow changing Lost your Password link
                $LostPassURL = apply_filters('qum_pre_login_url_filter', $LostPassURL);

                if ($user->get_error_code() == 'incorrect_password') {
                    $error_string = '<strong>' . __('ERROR', 'quickusermanager') . '</strong>: ' . __('The password you entered is incorrect.', 'quickusermanager') . ' ';
                    $error_string .= '<a href="' . $LostPassURL . '" title="' . __('Password Lost and Found.', 'quickusermanager') . '">' . __('Lost your password', 'quickusermanager') . '</a>?';

                    // change the recover password link
                    $error_string = str_replace(home_url('/wp-login.php?action=lostpassword'), $LostPassURL, $error_string);
                }
                if ($user->get_error_code() == 'invalid_username') {
                    $error_string = '<strong>' . __('ERROR', 'quickusermanager') . '</strong>: ' . __('Invalid username.', 'quickusermanager') . ' ';
                    $error_string .= '<a href="' . $LostPassURL . '" title="' . __('Password Lost and Found.', 'quickusermanager') . '">' . __('Lost your password', 'quickusermanager') . '</a>?';
                }
                // if login with email is enabled change the word username with email
                if ($qum_generalSettings['loginWith'] == 'email')
                    $error_string = str_replace( __('username','quickusermanager'), __('email','quickusermanager'), $error_string);

				// if login with username and email is enabled change the word username with username or email
				if ($qum_generalSettings['loginWith'] == 'usernameemail')
					$error_string = str_replace( __('username','quickusermanager'), __('username or email','quickusermanager'), $error_string);

            }
            // if the error string is empty it means that none of the fields were completed
            if (empty($error_string)) {
                $error_string = '<strong>' . __('ERROR', 'quickusermanager') . '</strong>: ' . __('Both fields are empty.', 'quickusermanager') . ' ';
                $error_string = apply_filters('qum_login_empty_fields_error_message', $error_string);
            }

            $error_string = apply_filters('qum_login_wp_error_message', $error_string, $user);

            // encode the error string and send it as a GET parameter
            $arr_params = array('loginerror' => urlencode(base64_encode($error_string)), 'request_form_location' => $request_form_location);
            $redirect_to = add_query_arg($arr_params, $redirect_to);
            wp_safe_redirect($redirect_to);
        }
		else{
			// we don't have an error make sure to remove the error from the query arg
			$redirect_to = remove_query_arg( 'loginerror', $redirect_to );
		}
	}

	return $redirect_to;
}
add_filter( 'login_redirect', 'qum_login_redirect', 10, 3 );


/* shortcode function */
function qum_front_end_login( $atts ){

    extract( shortcode_atts( array( 'display' => true, 'redirect' => '', 'redirect_url' => '', 'register_url' => '', 'lostpassword_url' => '' ), $atts ) );

	$qum_generalSettings = get_option('qum_general_settings');

	if( !is_user_logged_in() ){
		// set up the form arguments
		$form_args = array( 'echo' => false, 'id_submit' => 'qum-submit' );

        //Add support for "redirect_url" parameter for Login shortcode (will do the same thing as "redirect" - for consistency with Register, Edit Profile shortcodes)
        if ( !empty($redirect_url) ) $redirect = $redirect_url;

		// maybe set up the redirect argument
		if( empty( $redirect ) ){
            if( QUICK_USER_MANAGER == 'Quick User Manager Pro' ) {
                $qum_module_settings = get_option( 'qum_module_settings' );
                if( $qum_module_settings['qum_customRedirect'] == 'show' ){
                    //check to see if the redirect location is not an empty string and is activated
                    $login_redirect_settings = get_option( 'customRedirectSettings' );

                    // set up the redirect argument to our redirect page
                    if( ( trim( $login_redirect_settings['afterLoginTarget'] ) != '' ) && ( $login_redirect_settings['afterLogin'] == 'yes' ) ){
                        $redirect_to = trim( $login_redirect_settings['afterLoginTarget'] );
                        if( qum_check_missing_http( $redirect_to ) )
                            $redirect_to = 'http://'. $redirect_to;

                        $form_args['redirect'] = $redirect_to;
                    }
                }
            }
		}
		else	
			$form_args['redirect'] = trim( $redirect );
			
		// change the label argument for username is login with email is enabled
		if ( isset( $qum_generalSettings['loginWith'] ) && ( $qum_generalSettings['loginWith'] == 'email' ) )
			$form_args['label_username'] = __( 'Email', 'quickusermanager' );

		// change the label argument for username on login with username or email when Username and Email is enabled
		if ( isset( $qum_generalSettings['loginWith'] ) && ( $qum_generalSettings['loginWith'] == 'usernameemail' ) )
			$form_args['label_username'] = __( 'Username or Email', 'quickusermanager' );

		// initialize our form variable
		$login_form = '';
		
		// display our login errors
		if( isset( $_GET['loginerror'] ) || isset( $_POST['loginerror'] ) ){
            $loginerror = isset( $_GET['loginerror'] ) ? $_GET['loginerror'] : $_POST['loginerror'];
            $loginerror = '<p class="qum-error">'.urldecode( base64_decode( $loginerror ) ).'</p><!-- .error -->';
            if( isset( $_GET['request_form_location'] ) ){
                if( $_GET['request_form_location'] == 'widget' && !in_the_loop() ){
                    $login_form .= $loginerror;
                }
                elseif( $_GET['request_form_location'] == 'page' && in_the_loop() ){
                    $login_form .= $loginerror;
                }
            }
		}
		// build our form
		$login_form .= '<div id="qum-login-wrap" class="qum-user-forms">';
        $form_args['lostpassword_url'] = $lostpassword_url;
		$login_form .= wp_login_form( apply_filters( 'qum_login_form_args', $form_args ) );

		if ((!empty($register_url)) || (!empty($lostpassword_url))) {
                $login_form .= '<p class="login-register-lost-password">';
                $i = 0;
                if (!empty($register_url)) {
                    if ( qum_check_missing_http( $register_url ) ) $register_url = "http://" . $register_url;
                    $login_form .= '<a href="' . esc_url($register_url) . '">'. apply_filters('qum_login_register_text', __('Register','quickusermanager')) .'</a>';
                    $i++;
                }
                if (!empty($lostpassword_url)) {
                    if ($i != 0) $login_form .= ' | ';
                    if ( qum_check_missing_http( $lostpassword_url ) ) $lostpassword_url = "http://" . $lostpassword_url;
                    $login_form .= '<a href="'. esc_url($lostpassword_url) .'">'. apply_filters('qum_login_lostpass_text', __('Lost your password?','quickusermanager')) .'</a>';
                }
                $login_form .= '</p>';
        }

        $login_form .= '</div>';
		return $login_form;
	
	}else{
		$user_ID = get_current_user_id();
		$qum_user = get_userdata( $user_ID );
		
		if( isset( $qum_generalSettings['loginWith'] ) && ( $qum_generalSettings['loginWith'] == 'email' ) )
			$display_name = $qum_user->user_email;
		
		elseif($qum_user->display_name !== '')
			$display_name = $qum_user->user_login;
		
		else
			$display_name = $qum_user->display_name;

		if( isset( $qum_generalSettings['loginWith'] ) && ( $qum_generalSettings['loginWith'] == 'usernameemail' ) )
			if( $qum_user->user_login == Wordpress_Creation_Kit_QUM::wck_generate_slug( trim( $qum_user->user_email ) ) )
			$display_name = $qum_user->user_email;

		elseif($qum_user->display_name !== '')
			$display_name = $qum_user->user_login;

		else
			$display_name = $qum_user->display_name;

		$logged_in_message = '<p class="qum-alert">';
        $user_url = '<a href="'.$authorPostsUrl = get_author_posts_url( $qum_user->ID ).'" class="qum-author-url" title="'.$display_name.'">'.$display_name.'</a>';
        $logout_url = '<a href="'.wp_logout_url( $redirectTo = qum_curpageurl() ).'" class="qum-logout-url" title="'.__( 'Log out of this account', 'quickusermanager' ).'">'. __( 'Log out', 'quickusermanager').' &raquo;</a>';
        $logged_in_message .= sprintf(__( 'You are currently logged in as %1$s. %2$s', 'quickusermanager' ), $user_url, $logout_url );
        $logged_in_message .= '</p><!-- .qum-alert-->';
		
		return apply_filters( 'qum_login_message', $logged_in_message, $qum_user->ID, $display_name );
		
	}
}