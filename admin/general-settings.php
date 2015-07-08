<?php
/**
 * Function that creates the "General Settings" submenu page
 *
 * @since v.1.0
 *
 * @return void
 */
function qum_register_general_settings_submenu_page() {
	add_submenu_page( 'quick-user-manager', __( 'General Settings', 'quickusermanager' ), __( 'General Settings', 'quickusermanager' ), 'manage_options', 'quick-user-manager-general-settings', 'qum_general_settings_content' ); 
}
add_action( 'admin_menu', 'qum_register_general_settings_submenu_page', 3 );


function qum_generate_default_settings_defaults(){
	$qum_general_settings = get_option( 'qum_general_settings', 'not_found' );
	
	if ( $qum_general_settings == 'not_found' )
		update_option( 'qum_general_settings', array( 'extraFieldsLayout' => 'default', 'emailConfirmation' => 'no', 'activationLandingPage' => '', 'adminApproval' => 'no', 'loginWith' => 'usernameemail' ) );
}


/**
 * Function that adds content to the "General Settings" submenu page
 *
 * @since v.1.0
 *
 * @return string
 */
function qum_general_settings_content() {
	qum_generate_default_settings_defaults();
?>	
	<div class="wrap qum-wrap">
	<form method="post" action="options.php#general-settings">
	<?php $qum_generalSettings = get_option( 'qum_general_settings' ); ?>
	<?php settings_fields( 'qum_general_settings' ); ?>

	<h2><?php _e( 'General Settings', 'quickusermanager' ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row">
				<?php _e( "Load Quick User Manager's own CSS file in the front-end:", "quickusermanager" ); ?>
			</th>
			<td>
				<label><input type="checkbox" name="qum_general_settings[extraFieldsLayout]"<?php echo ( ( isset( $qum_generalSettings['extraFieldsLayout'] ) && ( $qum_generalSettings['extraFieldsLayout'] == 'default' ) ) ? ' checked' : '' ); ?> value="default" class="qum-select"><?php _e( 'Yes', 'quickusermanager' ); ?></label>
				<ul>
					<li class="description"><?php printf( __( 'You can find the default file here: %1$s', 'quickusermanager' ), '<a href="'.dirname( plugin_dir_url( __FILE__ ) ).'/assets/css/style-front-end.css" target="_blank">'.dirname( dirname( plugin_basename( __FILE__ ) ) ).'\assets\css\style-front-end.css</a>' ); ?></li>
				</ul>
			</td>
		</tr>
		<?php
		if ( !is_multisite() ){
		?>
		<tr>
			<th scope="row">
				<?php _e( '"Email Confirmation" Activated:', 'quickusermanager' );?>
			</th>
			<td>
				<select name="qum_general_settings[emailConfirmation]" class="qum-select" id="qum_settings_email_confirmation" onchange="qum_display_page_select(this.value)">
					<option value="yes" <?php if ( $qum_generalSettings['emailConfirmation'] == 'yes' ) echo 'selected'; ?>><?php _e( 'Yes', 'quickusermanager' ); ?></option>
					<option value="no" <?php if ( $qum_generalSettings['emailConfirmation'] == 'no' ) echo 'selected'; ?>><?php _e( 'No', 'quickusermanager' ); ?></option>
				</select>
				<ul>
				<li class="description"><?php _e( 'On single-site installations this works with front-end forms only. Recommended to redirect WP default registration to a Quick User Manager one using "Custom Redirects" addon.', 'quickusermanager' ); ?></li>
				<li class="description"><?php _e( 'The "Email Confirmation" feature is active (by default) on WPMU installations.', 'quickusermanager' ); ?></li>
				<?php if ( is_multisite() || ( $qum_generalSettings['emailConfirmation'] == 'yes' ) ) ?>
					<li class="description dynamic1"><?php printf( __( 'You can find a list of unconfirmed email addresses %1$sUsers > All Users > Email Confirmation%2$s.', 'quickusermanager' ), '<a href="'.get_bloginfo( 'url' ).'/wp-admin/users.php?page=unconfirmed_emails">', '</a>' )?></li>
				</ul>
			</td>
		</tr>
		<?php
		}else{
			echo '<input type="hidden" id="qum_general_settings_hidden" value="multisite"/>';
		}
		?>

		<tr id="qum-settings-activation-page">
			<th scope="row">
				<?php _e( '"Email Confirmation" Landing Page:', 'quickusermanager' ); ?>
			</th>
			<td>
				<select name="qum_general_settings[activationLandingPage]" class="qum-select">
					<option value="" <?php if ( empty( $qum_generalSettings['emailConfirmation'] ) ) echo 'selected'; ?>></option>
					<optgroup label="<?php _e( 'Existing Pages', 'quickusermanager' ); ?>">
					<?php
						$pages = get_pages( apply_filters( 'qum_page_args_filter', array( 'sort_order' => 'ASC', 'sort_column' => 'post_title', 'post_type' => 'page', 'post_status' => array( 'publish' ) ) ) );
						
						foreach ( $pages as $key => $value ){
							echo '<option value="'.$value->ID.'"';
							if ( $qum_generalSettings['activationLandingPage'] == $value->ID )
								echo ' selected';

							echo '>' . $value->post_title . '</option>';
						}
					?>
					</optgroup>
				</select>
				<p class="description">
					<?php _e( 'Specify the page where the users will be directed when confirming the email account. This page can differ from the register page(s) and can be changed at any time. If none selected, a simple confirmation page will be displayed for the user.', 'quickusermanager' ); ?>
				</p>
			</td>
		</tr>


	<?php
	if ( file_exists( QUM_PLUGIN_DIR.'/features/admin-approval/admin-approval.php' ) ){
	?>
		<tr>
			<th scope="row">
				<?php _e( '"Admin Approval" Activated:', 'quickusermanager' ); ?>
			</th>
			<td>
				<select id="adminApprovalSelect" name="qum_general_settings[adminApproval]" class="qum-select" onchange="qum_display_page_select_aa(this.value)">
					<option value="yes" <?php if( !empty( $qum_generalSettings['adminApproval'] ) && $qum_generalSettings['adminApproval'] == 'yes' ) echo 'selected'; ?>><?php _e( 'Yes', 'quickusermanager' ); ?></option>
					<option value="no" <?php if( !empty( $qum_generalSettings['adminApproval'] ) && $qum_generalSettings['adminApproval'] == 'no' ) echo 'selected'; ?>><?php _e( 'No', 'quickusermanager' ); ?></option>
				</select>
				<ul>
					<li class="description dynamic2"><?php printf( __( 'You can find a list of users at %1$sUsers > All Users > Admin Approval%2$s.', 'quickusermanager' ), '<a href="'.get_bloginfo( 'url' ).'/wp-admin/users.php?page=admin_approval&orderby=registered&order=desc">', '</a>' )?></li>
				<ul>
			</td>
		</tr>
	
	<?php } ?>

	<?php
	if ( QUICK_USER_MANAGER == 'Quick User Manager Free' ) {
	?>
		<tr>
			<th scope="row">
				<?php _e( '"Admin Approval" Feature:', 'quickusermanager' ); ?>
			</th>
			<td>
				<p><em>	<?php printf( __( 'You decide who is a user on your website. Get notified via email or approve multiple users at once from the WordPress UI. Enable Admin Approval by upgrading to %1$sPRO versions%2$s.', 'quickusermanager' ),'<a href="http://plugin.crackcodex.com/quick-user-manager/?utm_source=wpbackend&utm_medium=clientsite&utm_content=general-settings-link&utm_campaign=QUMFree">', '</a>' )?></em></p>
			</td>
		</tr>
	<?php } ?>

		<tr>
			<th scope="row">
				<?php _e( 'Allow Users to Log in With:', 'quickusermanager' ); ?>
			</th>
			<td>
				<select name="qum_general_settings[loginWith]" class="qum-select">
					<option value="usernameemail" <?php if ( $qum_generalSettings['loginWith'] == 'usernameemail' ) echo 'selected'; ?>><?php _e( 'Username and Email', 'quickusermanager' ); ?></option>
					<option value="username" <?php if ( $qum_generalSettings['loginWith'] == 'username' ) echo 'selected'; ?>><?php _e( 'Username', 'quickusermanager' ); ?></option>
					<option value="email" <?php if ( $qum_generalSettings['loginWith'] == 'email' ) echo 'selected'; ?>><?php _e( 'Email', 'quickusermanager' ); ?></option>
				</select>
				<ul>
					<li class="description"><?php _e( '"Username and Email" - users can Log In with both Username and Email.', 'quickusermanager' ); ?></li>
					<li class="description"><?php _e( '"Username" - users can Log In only with Username.', 'quickusermanager' ); ?></li>
					<li class="description"><?php _e( '"Email" - users can Log In only with Email.', 'quickusermanager' ); ?></li>
				</ul>
			</td>
		</tr>

        <tr>
            <th scope="row">
                <?php _e( 'Minimum Password Length:', 'quickusermanager' ); ?>
            </th>
            <td>
                <input type="text" name="qum_general_settings[minimum_password_length]" class="qum-text" value="<?php if( !empty( $qum_generalSettings['minimum_password_length'] ) ) echo $qum_generalSettings['minimum_password_length']; ?>"/>
                <ul>
                    <li class="description"><?php _e( 'Enter the minimum characters the password should have. Leave empty for no minimum limit', 'quickusermanager' ); ?> </li>
                </ul>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <?php _e( 'Minimum Password Strength:', 'quickusermanager' ); ?>
            </th>
            <td>
                <select name="qum_general_settings[minimum_password_strength]" class="qum-select">
                    <option value=""><?php _e( 'Disabled', 'quickusermanager' ); ?></option>
                    <option value="short" <?php if ( !empty($qum_generalSettings['minimum_password_strength']) && $qum_generalSettings['minimum_password_strength'] == 'short' ) echo 'selected'; ?>><?php _e( 'Very weak', 'quickusermanager' ); ?></option>
                    <option value="bad" <?php if ( !empty($qum_generalSettings['minimum_password_strength']) && $qum_generalSettings['minimum_password_strength'] == 'bad' ) echo 'selected'; ?>><?php _e( 'Weak', 'quickusermanager' ); ?></option>
                    <option value="good" <?php if ( !empty($qum_generalSettings['minimum_password_strength']) && $qum_generalSettings['minimum_password_strength'] == 'good' ) echo 'selected'; ?>><?php _e( 'Medium', 'quickusermanager' ); ?></option>
                    <option value="strong" <?php if ( !empty($qum_generalSettings['minimum_password_strength']) && $qum_generalSettings['minimum_password_strength'] == 'strong' ) echo 'selected'; ?>><?php _e( 'Strong', 'quickusermanager' ); ?></option>
                </select>
            </td>
        </tr>

        <?php do_action( 'qum_extra_general_settings', $qum_generalSettings ); ?>
	</table>
		
	
	
	<input type="hidden" name="action" value="update" />
	<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ); ?>" /></p>
</form>
</div>
	
<?php
}


/*
 * Function that sanitizes the general settings
 *
 * @param array $qum_generalSettings
 *
 * @since v.1.0.7
 */
function qum_general_settings_sanitize( $qum_generalSettings ) {

    $qum_generalSettings = apply_filters( 'qum_general_settings_sanitize_extra', $qum_generalSettings );

    return $qum_generalSettings;
}


/*
 * Function that pushes settings errors to the user
 *
 * @since v.1.0.7
 */
function qum_general_settings_admin_notices() {
    settings_errors( 'qum_general_settings' );
}
add_action( 'admin_notices', 'qum_general_settings_admin_notices' );