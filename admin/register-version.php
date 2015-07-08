<?php
/**
 * Function that creates the "Register your version" submenu page
 *
 * @since v.1.0
 *
 * @return void
 */

if( !is_multisite() ){
    function qum_register_your_version_submenu_page()
    {
        if (QUICK_USER_MANAGER != 'Quick User Manager Free')
            add_submenu_page('quick-user-manager', __('Register Your Version', 'quickusermanager'), __('Register Version', 'quickusermanager'), 'manage_options', 'quick-user-manager-register', 'qum_register_your_version_content');
    }
    add_action('admin_menu', 'qum_register_your_version_submenu_page', 20);
}
else{
    function qum_multisite_register_your_version_page()
    {
        if (QUICK_USER_MANAGER != 'Quick User Manager Free')
            add_menu_page(__('Quick User Manager Register', 'quickusermanager'), __('Quick User Manager Register', 'quickusermanager'), 'manage_options', 'quick-user-manager-register', 'qum_register_your_version_content', QUM_PLUGIN_URL . 'assets/images/qum_menu_icon.png');
    }
    add_action('network_admin_menu', 'qum_multisite_register_your_version_page', 20);
}


/**
 * Function that adds content to the "Register your Version" submenu page
 *
 * @since v.1.0
 *
 * @return string
 */
function qum_register_your_version_content() {

    ?>
    <div class="wrap qum-wrap">
        <?php
        if ( QUICK_USER_MANAGER == 'Quick User Manager Pro' ){
            qum_serial_form('pro', 'Quick User Manager Pro');
        }
        ?>

    </div>
<?php
}

/**
 * Function that creates the "Register your version" form depending on Pro version
 *
 * @since v.1.0
 *
 * @return void
 */
function qum_serial_form($version, $fullname){
    ?>

    <h2><?php _e( "Register your version of $fullname", 'quickusermanager' ); ?></h2>

    <form method="post" action="<?php echo get_admin_url( 1, 'options.php' ) ?>">

        <?php $qum_QUICK_USER_MANAGER_serial = get_option( 'qum_QUICK_USER_MANAGER_'.$version.'_serial' ); ?>
        <?php $qum_QUICK_USER_MANAGER_serial_status = get_option( 'qum_QUICK_USER_MANAGER_'.$version.'_serial_status' ); ?>
        <?php settings_fields( 'qum_QUICK_USER_MANAGER_'.$version.'_serial' ); ?>

        <p><?php printf( __( "Now that you acquired a copy of %s, you should take the time and register it with the serial number you received", 'quickusermanager'), $fullname);?></p>
        <p><?php _e( "If you register this version of Quick User Manager, you'll receive information regarding upgrades, patches, and technical support.", 'quickusermanager' );?></p>
        <p class="qum-serial-wrap">
            <label for="qum_QUICK_USER_MANAGER_<?php echo $version; ?>_serial"><?php _e(' Serial Number:', 'quickusermanager' );?></label>
                <input type="text" size="50" name="qum_QUICK_USER_MANAGER_<?php echo $version; ?>_serial" id="qum_QUICK_USER_MANAGER_<?php echo $version; ?>_serial" class="regular-text" <?php if ( $qum_QUICK_USER_MANAGER_serial != ''){ echo ' value="'.$qum_QUICK_USER_MANAGER_serial.'"';} ?>/>

                <?php
                if( $qum_QUICK_USER_MANAGER_serial_status == 'found' )
                    echo '<span class="validateStatus"><img src="'.QUM_PLUGIN_URL.'/assets/images/accept.png" title="'.__( 'The serial number was successfully validated!', 'quickusermanager' ).'"/></span>';
                elseif ( $qum_QUICK_USER_MANAGER_serial_status == 'notFound' )
                    echo '<span class="validateStatus"><img src="'.QUM_PLUGIN_URL.'/assets/images/icon_error.png" title="'.__( 'The serial number entered couldn\'t be validated!','quickusermanager' ).'"/></span>';
                elseif ( strpos( $qum_QUICK_USER_MANAGER_serial_status, 'aboutToExpire')  !== false )
                    echo '<span class="validateStatus"><img src="' . QUM_PLUGIN_URL . '/assets/images/icon_error.png" title="' . __('The serial number is about to expire soon!', 'quickusermanager') . '"/>'. sprintf( __(' Your serial number is about to expire, please %1$s Renew Your License%2$s.','quickusermanager'), "<a href='http://downloads.crackcodex.com/quick-user-manager-". $version ."-v2-yearly-renewal/?utm_source=qum&utm_medium=dashboard&utm_campaign=QUM-Renewal' >", "</a>").'</span>';
                elseif ( $qum_QUICK_USER_MANAGER_serial_status == 'expired' )
                    echo '<span class="validateStatus"><img src="'.QUM_PLUGIN_URL.'/assets/images/icon_error.png" title="'.__( 'The serial number couldn\'t be validated because it expired!','quickusermanager' ).'"/>'. sprintf( __(' Your serial number is expired, please %1$s Renew Your License%2$s.','quickusermanager'), "<a href='http://downloads.crackcodex.com/quick-user-manager-". $version ."-v2-yearly-renewal/?utm_source=qum&utm_medium=dashboard&utm_campaign=QUM-Renewal' >", "</a>").'</span>';
                elseif ( $qum_QUICK_USER_MANAGER_serial_status == 'serverDown' )
                    echo '<span class="validateStatus"><img src="'.QUM_PLUGIN_URL.'/assets/images/icon_error.png" title="'.__( 'The serial number couldn\'t be validated because process timed out. This is possible due to the server being down. Please try again later!','quickusermanager' ).'"/></span>';
                ?>
        <span class="qum-serialnumber-descr"><?php _e( '(e.g. RMqum-15-SN-253a55baa4fbe7bf595b2aabb8d72985)', 'quickusermanager' );?></span>
        </p>


        <div id="qum_submit_button_div">
            <input type="hidden" name="action" value="update" />
            <p class="submit">
                <?php wp_nonce_field( 'qum_register_version_nonce', 'qum_register_version_nonce' ); ?>
                <input type="submit" name="qum_serial_number_activate" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
        </div>

    </form>
<?php
}


//the function to check the validity of the serial number and save a variable in the DB; purely visual
function qum_check_serial_number($oldVal, $newVal){

	$serial_number_set = $newVal;


	$response = wp_remote_get( 'http://updatemetadata.crackcodex.com/checkserial/?serialNumberSent='.$serial_number_set );
	if ( QUICK_USER_MANAGER == 'Quick User Manager Pro' ){
        qum_update_serial_status($response, 'pro');
        wp_clear_scheduled_hook( "check_plugin_updates-quick-user-manager-pro-update" );
    }
    $user_ID = get_current_user_id();
	delete_user_meta( $user_ID, 'qum_dismiss_notification' );
	
}

add_action( 'update_option_qum_QUICK_USER_MANAGER_pro_serial', 'qum_check_serial_number', 10, 2 );

add_action( 'add_option_qum_QUICK_USER_MANAGER_pro_serial', 'qum_check_serial_number', 10, 2 );

/**
 * @param $response
 */
function qum_update_serial_status($response, $version)
{
    if (is_wp_error($response)) {
        update_option('qum_QUICK_USER_MANAGER_'.$version.'_serial_status', 'serverDown'); //server down
    } elseif ((trim($response['body']) != 'notFound') && (trim($response['body']) != 'found') && (trim($response['body']) != 'expired') && (strpos( $response['body'], 'aboutToExpire' ) === false)) {
        update_option('qum_QUICK_USER_MANAGER_'.$version.'_serial_status', 'serverDown'); //unknown response parameter
        update_option('qum_QUICK_USER_MANAGER_'.$version.'_serial', ''); //reset the entered password, since the user will need to try again later

    } else {
        update_option('qum_QUICK_USER_MANAGER_'.$version.'_serial_status', trim($response['body'])); //either found, notFound or expired
    }
}

//the update didn't work when the old value = new value, so we need to apply a filter on get_option (that is run before update_option), that resets the old value
function qum_check_serial_number_fix($newvalue, $oldvalue){

	if ( $newvalue == $oldvalue )
		qum_check_serial_number( $oldvalue, $newvalue );
		
	return $newvalue;
}
add_filter( 'pre_update_option_qum_QUICK_USER_MANAGER_pro_serial', 'qum_check_serial_number_fix', 10, 2 );


/**
 * Class that adds a notice when either the serial number wasn't found, or it has expired
 *
 * @since v.1.0
 *
 * @return void
 */
class qum_add_notices{
	public $pluginPrefix = '';
	public $pluginName = '';
	public $notificaitonMessage = '';
	public $pluginSerialStatus = '';
	
	function __construct( $pluginPrefix, $pluginName, $notificaitonMessage, $pluginSerialStatus ){
		$this->pluginPrefix = $pluginPrefix;
		$this->pluginName = $pluginName;
		$this->notificaitonMessage = $notificaitonMessage;
		$this->pluginSerialStatus = $pluginSerialStatus;
		
		add_action( 'admin_notices', array( $this, 'add_admin_notice' ) );
		add_action( 'admin_init', array( $this, 'dismiss_notification' ) );
	}
	

	// Display a notice that can be dismissed in case the serial number is inactive
	function add_admin_notice() {
		global $current_user ;
		global $pagenow;
		
		$user_id = $current_user->ID;
		
		do_action( $this->pluginPrefix.'_before_notification_displayed', $current_user, $pagenow );
		
		if ( current_user_can( 'manage_options' ) ){

				$plugin_serial_status = get_option( $this->pluginSerialStatus );
				if ( $plugin_serial_status != 'found' ){
					// Check that the user hasn't already clicked to ignore the message
					if ( ! get_user_meta($user_id, $this->pluginPrefix.'_dismiss_notification' ) ) {
						echo $finalMessage = apply_filters($this->pluginPrefix.'_notification_message','<div class="error qum-serial-notification" >'.$this->notificaitonMessage.'</div>', $this->notificaitonMessage);
					}
				}
				
				do_action( $this->pluginPrefix.'_notification_displayed', $current_user, $pagenow, $plugin_serial_status );

		}
		
		do_action( $this->pluginPrefix.'_after_notification_displayed', $current_user, $pagenow );
		
	}

	function dismiss_notification() {
		global $current_user;
		
		$user_id = $current_user->ID;
		
		do_action( $this->pluginPrefix.'_before_notification_dismissed', $current_user );
		
		// If user clicks to ignore the notice, add that to their user meta 
		if ( isset( $_GET[$this->pluginPrefix.'_dismiss_notification']) && '0' == $_GET[$this->pluginPrefix.'_dismiss_notification'] )
			add_user_meta( $user_id, $this->pluginPrefix.'_dismiss_notification', 'true', true ); 
		
		do_action( $this->pluginPrefix.'_after_notification_dismissed', $current_user );
	}
}

if( is_multisite() && function_exists( 'switch_to_blog' ) )
    switch_to_blog(1);

if ( QUICK_USER_MANAGER == 'Quick User Manager Pro' ){
    $qum_QUICK_USER_MANAGER_pro_hobbyist_serial_status = get_option( 'qum_QUICK_USER_MANAGER_pro_serial_status', 'empty' );
    $version = 'pro';

} elseif( QUICK_USER_MANAGER == 'Quick User Manager Hobbyist' ) {
    $qum_QUICK_USER_MANAGER_pro_hobbyist_serial_status = get_option( 'qum_QUICK_USER_MANAGER_hobbyist_serial_status', 'empty' );
    $version = 'hobbyist';
}
if( is_multisite() && function_exists( 'restore_current_blog' ) )
    restore_current_blog();

if ( $qum_QUICK_USER_MANAGER_pro_hobbyist_serial_status == 'notFound' || $qum_QUICK_USER_MANAGER_pro_hobbyist_serial_status == 'empty' ){
    if( !is_multisite() )
        $register_url = 'admin.php?page=quick-user-manager-register';
    else
        $register_url = network_admin_url( 'admin.php?page=quick-user-manager-register' );

	new qum_add_notices( 'wpqum', 'QUICK_USER_MANAGER_pro', sprintf( __( '<p>Your <strong>Quick User Manager</strong> serial number is invalid or missing. <br/>Please %1$sregister your copy%2$s to receive access to automatic updates and support. Need a license key? %3$sPurchase one now%4$s</p>', 'quickusermanager'), "<a href='". $register_url ."'>", "</a>", "<a href='http://plugin.crackcodex.com/quick-user-manager/?utm_source=qum&utm_medium=dashboard&utm_campaign=QUM-SN-Purchase' target='_blank' class='button-primary'>", "</a>" ), 'qum_QUICK_USER_MANAGER_pro_serial_status' );
}
elseif ( $qum_QUICK_USER_MANAGER_pro_hobbyist_serial_status == 'expired' ){
    new qum_add_notices( 'qum_expired', 'QUICK_USER_MANAGER_pro', sprintf( __( '<p>Your <strong>Quick User Manager</strong> license has expired. <br/>Please %1$sRenew Your Licence%2$s to continue receiving access to product downloads, automatic updates and support. %3$sRenew now and get 50&#37; off %4$s %5$sDismiss%6$s</p>', 'quickusermanager'), "<a href='http://downloads.crackcodex.com/quick-user-manager-". $version ."-v2-yearly-renewal/?utm_source=qum&utm_medium=dashboard&utm_campaign=QUM-Renewal' target='_blank'>", "</a>", "<a href='http://downloads.crackcodex.com/quick-user-manager-".$version."-v2-yearly-renewal/?utm_source=qum&utm_medium=dashboard&utm_campaign=QUM-Renewal' target='_blank' class='button-primary'>", "</a>", "<a href='". esc_url( add_query_arg( 'qum_expired_dismiss_notification', '0' ) ) ."' class='qum-dismiss-notification'>", "</a>" ), 'qum_QUICK_USER_MANAGER_pro_serial_status' );
}
elseif( strpos( $qum_QUICK_USER_MANAGER_pro_hobbyist_serial_status, 'aboutToExpire' ) === 0 ){
    $serial_status_parts = explode( '#', $qum_QUICK_USER_MANAGER_pro_hobbyist_serial_status );
    $date = $serial_status_parts[1];
    new qum_add_notices( 'qum_about_to_expire', 'QUICK_USER_MANAGER_pro', sprintf( __( '<p>Your <strong>Quick User Manager</strong> license is about to expire on %5$s. <br/>Please %1$sRenew Your Licence%2$s to continue receiving access to product downloads, automatic updates and support. %3$sRenew now and get 50&#37; off %4$s %6$sDismiss%7$s</p>', 'quickusermanager'), "<a href='http://downloads.crackcodex.com/quick-user-manager-". $version ."-v2-yearly-renewal/?utm_source=qum&utm_medium=dashboard&utm_campaign=QUM-Renewal' target='_blank'>", "</a>", "<a href='http://downloads.crackcodex.com/quick-user-manager-".$version."-v2-yearly-renewal/?utm_source=qum&utm_medium=dashboard&utm_campaign=QUM-Renewal' target='_blank' class='button-primary'>", "</a>", $date, "<a href='". esc_url( add_query_arg( 'qum_about_to_expire_dismiss_notification', '0' ) )."' class='qum-dismiss-notification'>", "</a>" ), 'qum_QUICK_USER_MANAGER_pro_serial_status' );
}