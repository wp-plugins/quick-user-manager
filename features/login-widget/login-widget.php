<?php
function qum_register_login_widget() {
	register_widget( 'qum_login_widget' );
}
add_action( 'widgets_init', 'qum_register_login_widget' );

class qum_login_widget extends WP_Widget {

	function qum_login_widget() {
		$widget_ops = array( 'classname' => 'login', 'description' => __( 'This login widget lets you add a login form in the sidebar.', 'quickusermanager' ) );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'qum-login-widget' );
		
		do_action( 'qum_login_widget_settings', $widget_ops, $control_ops);
		
		$this->WP_Widget( 'qum-login-widget', __('Quick User Manager Login Widget', 'quickusermanager'), $widget_ops, $control_ops );
		
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('qum_login_widget_title', $instance['title'] );
		$redirect = trim($instance['redirect']);
		$register = trim($instance['register']);
		$lostpass = trim($instance['lostpass']);

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo do_shortcode('[qum-login display="false" register_url="'.$register.'" lostpassword_url="'.$lostpass.'" redirect="'.$redirect.'"]');
		
		do_action( 'qum_login_widget_display', $args, $instance);
			
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['redirect'] = strip_tags( $new_instance['redirect'] );
		$instance['register'] = strip_tags( $new_instance['register'] );
		$instance['lostpass'] = strip_tags( $new_instance['lostpass'] );

		do_action( 'qum_login_widget_update_action', $new_instance, $old_instance);
		
		return $instance;
	
	}


	function form( $instance ) {

		$defaults = array( 'title' => __('Login', 'quickusermanager'), 'redirect' => '', 'register' => '', 'lostpass' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'quickusermanager' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'redirect' ); ?>"><?php _e( 'After login redirect URL (optional):', 'quickusermanager' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'redirect' ); ?>" class="widefat" type="url" name="<?php echo $this->get_field_name( 'redirect' ); ?>" value="<?php echo $instance['redirect']; ?>" style="width:100%;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'register' ); ?>"><?php _e( 'Register page URL (optional):', 'quickusermanager' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'register' ); ?>" class="widefat" type="url" name="<?php echo $this->get_field_name( 'register' ); ?>" value="<?php echo $instance['register']; ?>" style="width:100%;" />
		</p>		
		
		<p>
			<label for="<?php echo $this->get_field_id( 'lostpass' ); ?>"><?php _e( 'Password Recovery page URL (optional):', 'quickusermanager' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'lostpass' ); ?>" class="widefat" type="url" name="<?php echo $this->get_field_name( 'lostpass' ); ?>" value="<?php echo $instance['lostpass']; ?>" style="width:100%;" />
		</p>

	<?php
	
		do_action( 'qum_login_widget_after_display', $instance);
	}
}

// we can apply this easily, if we need it
function qum_scroll_down_to_widget($content){
	return "<script> jQuery('html, body').animate({scrollTop: jQuery('#qum_login').offset().top }) </script>" . $content;
}
//add_filter('qum_login_wp_error_message', 'qum_scroll_down_to_widget');

function qum_require_jquery(){
	wp_enqueue_script( 'jquery' );
}
//add_action( 'wp_enqueue_scripts', 'qum_require_jquery' ); 