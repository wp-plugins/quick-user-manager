<?php
/**
 * Function that creates the "Basic Information" submenu page
 *
 * @since v.1.0
 *
 * @return void
 */
function qum_register_basic_info_submenu_page() {
	add_submenu_page( 'quick-user-manager', __( 'Basic Information', 'quickusermanager' ), __( 'Basic Information', 'quickusermanager' ), 'manage_options', 'quick-user-manager-basic-info', 'qum_basic_info_content' ); 
}
add_action( 'admin_menu', 'qum_register_basic_info_submenu_page', 2 );

/**
 * Function that adds content to the "Basic Information" submenu page
 *
 * @since v.1.0
 *
 * @return string
 */
function qum_basic_info_content() {
	
	$version = 'Free';
	$version = ( ( QUICK_USER_MANAGER == 'Quick User Manager Pro' ) ? 'Pro' : $version );

?>
	<div class="wrap qum-wrap qum-info-wrap">
		<div class="qum-badge <?php echo $version; ?>"><?php printf( __( 'Version %s' ), QUICK_USER_MANAGER_VERSION ); ?></div>
		<h1><?php printf( __( '<strong>Quick User Manager </strong>' . $version . ' <small>v.</small>%s', 'quickusermanager' ), QUICK_USER_MANAGER_VERSION ); ?></h1>
		<p class="qum-info-text"><?php printf( __( 'The best way to add front-end registration, edit profile and login forms, send an email to the registered blog user(s) and users group.', 'quickusermanager' ) ); ?></p>
		<hr />
		<h2 class="qum-callout"><?php _e( 'For Modern User Interaction', 'quickusermanager' ); ?></h2>
		<div class="qum-row qum-3-col">
			<div>
				<h3><?php _e( 'Login', 'quickusermanager' ); ?></h3>
				<p><?php _e( 'Friction-less login using <strong class="nowrap">[qum-login]</strong> shortcode or a widget.', 'quickusermanager' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Registration', 'quickusermanager'  ); ?></h3>
				<p><?php _e( 'Beautiful registration forms fully customizable using the <strong class="nowrap">[qum-register]</strong> shortcode.', 'quickusermanager' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Edit Profile', 'quickusermanager' ); ?></h3>
				<p><?php _e( 'Straight forward edit profile forms using <strong class="nowrap">[qum-edit-profile]</strong> shortcode.', 'quickusermanager' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Recover Password', 'quickusermanager' ); ?></h3>
				<p><?php _e( 'Allow users to recover their password in the front-end using the [qum-recover-password].', 'quickusermanager' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Admin Approval', 'quickusermanager' ); ?></h3>
				<p><?php _e( 'You decide who is a user on your website. Get notified via email or approve multiple users at once from the WordPress UI.', 'quickusermanager' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Email Confirmation', 'quickusermanager' ); ?></h3>
				<p><?php _e( 'Make sure users sign up with genuine emails. On registration users will receive a notification to confirm their email address.', 'quickusermanager' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Minimum Password Length and Strength Meter', 'quickusermanager' ); ?></h3>
				<p><?php _e( 'Eliminate weak passwords altogether by setting a minimum password length and enforcing a certain password strength.', 'quickusermanager' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Login with Email or Username', 'quickusermanager' ); ?></h3>
				<p><?php _e( 'Allow users to log in with their email or username when accessing your site.', 'quickusermanager' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Customize Your Forms The Way You Want', 'quickusermanager' ); ?></h3>
				<p><?php _e( 'With Extra Profile Fields you can create the exact registration form your project needs.', 'quickusermanager' ); ?></p>
			</div>
		</div>
		<?php ob_start(); ?>
		<hr/>

		<div> 
			<h3><?php _e( 'Pro Modules (*)', 'quickusermanager' );?></h3>
			<p><?php _e( 'Everything you will need to manage your users is probably already available using the Pro Modules.', 'quickusermanager' ); ?></p>
            <?php if( file_exists ( QUM_PLUGIN_DIR.'/modules/modules.php' ) ): ?>
			    <p><a href="admin.php?page=quick-user-manager-modules" class="button"><?php _e( 'Enable your modules', 'quickusermanager' ); ?></a></p>
            <?php endif; ?>
			<?php if ($version == 'Free'){ ?>
				<p><a href="http://plugin.crackcodex.com/quick-user-manager/?utm_source=wpbackend&utm_medium=clientsite&utm_content=basicinfo-modules&utm_campaign=QUMFree" class="qum-button-free"><?php _e( 'Find out more about PRO Modules', 'quickusermanager' ); ?></a></p>
			<?php }?>
		</div>
		<div class="qum-row qum-3-col">
			<div>
				<h3><?php _e( 'User Listing', 'quickusermanager' ); ?></h3>
				<?php if ($version == 'Free'): ?>
				<p><?php _e( 'Easy to edit templates for listing your website users as well as creating single user pages. Shortcode based, offering many options to customize your listings.', 'quickusermanager' ); ?></p>
				<?php else : ?>
				<p><?php _e( 'To create a page containing the users registered to this current site/blog, insert the following shortcode in a page of your chosing: <strong class="nowrap">[qum-list-users]</strong>.', 'quickusermanager' ); ?></p>
				<?php endif;?>
			</div>
			<div>
				<h3><?php _e( 'Email Customizer', 'quickusermanager' ); ?></h3>
				<p><?php _e( 'Personalize all emails sent to your users or admins. On registration, email confirmation, admin approval / un-approval.', 'quickusermanager' ); ?></p>
			</div>
			<div>
				<h3><?php _e( 'Custom Redirects', 'quickusermanager' ); ?></h3>
				<p><?php _e( 'Keep your users out of the WordPress dashboard, redirect them to the front-page after login or registration, everything is just a few clicks away.', 'quickusermanager' ); ?></p>
			</div>
		</div>

		<?php
		//Output here Extra Features html for Pro versions
		if ( $version != 'Free' ) echo $extra_features_html; ?>

		<hr/>
		<div>
			<h3>Extra Notes</h3>
			<ul>
				<li><?php printf( __( '* only available in the %1$sPro version%2$s.', 'quickusermanager' ), '<a href="http://plugin.crackcodex.com/quick-user-manager/?utm_source=wpbackend&utm_medium=clientsite&utm_content=basicinfo-extranotes&utm_campaign=QUM'.$version.'" target="_blank">', '</a>' );?></li>
			</ul>
		</div>
	</div>
<?php
}