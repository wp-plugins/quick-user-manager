<?php
/*
Plugin Name: Quick User Manager
Plugin URI: http://plugin.crackcodex.com/quick-user-manager/
Description: Login, registration and edit profile shortcodes for the front-end. Also you can chose what fields should be displayed or add new (custom) ones both in the front-end and in the dashboard.
Version: 1.0
Author: CrackCodex, Delower Hossain Rhine
Author URI: http://www.crackcodex.com/
License: GPL2

== Copyright ==
Copyright 2015 CrackCodex (www.crackcodex.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

/* Check if another version of Quick User Manager is activated, to prevent fatal errors*/
function qum_free_plugin_init() {
    if (function_exists('qum_return_bytes')) {
        function qum_admin_notice()
        {
            ?>
            <div class="error">
                <p><?php _e( QUICK_USER_MANAGER . ' is also activated. You need to deactivate it before activating this version of the plugin.', 'quickusermanager'); ?></p>
            </div>
        <?php
        }
        function QUM_PLUGIN_deactivate() {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            unset($_GET['activate']);
        }

        add_action('admin_notices', 'qum_admin_notice');
        add_action( 'admin_init', 'QUM_PLUGIN_deactivate' );
    } else {

        /**
         * Convert memory value from ini file to a readable form
         *
         * @since v.1.0
         *
         * @return integer
         */
        function qum_return_bytes($val)
        {
            $val = trim($val);

            switch (strtolower($val[strlen($val) - 1])) {
                // The 'G' modifier is available since PHP 5.1.0
                case 'g':
                    $val *= 1024;
                case 'm':
                    $val *= 1024;
                case 'k':
                    $val *= 1024;
            }

            return $val;
        }

        /**
         * Definitions
         *
         *
         */
        define('QUICK_USER_MANAGER_VERSION', '1.0' );
        define('QUM_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('QUM_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('QUM_SERVER_MAX_UPLOAD_SIZE_BYTE', apply_filters('QUM_SERVER_max_upload_size_byte_constant', qum_return_bytes(ini_get('upload_max_filesize'))));
        define('QUM_SERVER_MAX_UPLOAD_SIZE_MEGA', apply_filters('QUM_SERVER_max_upload_size_mega_constant', ini_get('upload_max_filesize')));
        define('QUM_SERVER_MAX_POST_SIZE_BYTE', apply_filters('QUM_SERVER_max_post_size_byte_constant', qum_return_bytes(ini_get('post_max_size'))));
        define('QUM_SERVER_MAX_POST_SIZE_MEGA', apply_filters('QUM_SERVER_max_post_size_mega_constant', ini_get('post_max_size')));
        define('qum_TRANSLATE_DIR', QUM_PLUGIN_DIR . '/translation');
        define('qum_TRANSLATE_DOMAIN', 'quickusermanager');

        /* include notices class */
        if (file_exists(QUM_PLUGIN_DIR . '/assets/lib/class_notices.php'))
            include_once(QUM_PLUGIN_DIR . '/assets/lib/class_notices.php');

        if (file_exists(QUM_PLUGIN_DIR . '/modules/modules.php'))
            define('QUICK_USER_MANAGER', 'Quick User Manager Pro');
        else
            define('QUICK_USER_MANAGER', 'Quick User Manager Free');


        /**
         * Required files
         *
         *
         */
        include_once(QUM_PLUGIN_DIR . '/assets/lib/wck-api/wordpress-creation-kit.php');
        include_once(QUM_PLUGIN_DIR . '/features/upgrades/upgrades.php');
        include_once(QUM_PLUGIN_DIR . '/features/functions.php');
        include_once(QUM_PLUGIN_DIR . '/admin/admin-functions.php');
        include_once(QUM_PLUGIN_DIR . '/admin/basic-info.php');
        include_once(QUM_PLUGIN_DIR . '/admin/general-settings.php');
        include_once(QUM_PLUGIN_DIR . '/admin/admin-bar.php');
        include_once(QUM_PLUGIN_DIR . '/admin/manage-fields.php');
        include_once(QUM_PLUGIN_DIR . '/features/email-confirmation/email-confirmation.php');
        include_once(QUM_PLUGIN_DIR . '/features/email-confirmation/class-email-confirmation.php');
        if (file_exists(QUM_PLUGIN_DIR . '/features/admin-approval/admin-approval.php')) {
            include_once(QUM_PLUGIN_DIR . '/features/admin-approval/admin-approval.php');
            include_once(QUM_PLUGIN_DIR . '/features/admin-approval/class-admin-approval.php');
        }
        include_once(QUM_PLUGIN_DIR . '/features/login-widget/login-widget.php');

        if (file_exists(QUM_PLUGIN_DIR . '/update/update-checker.php')) {
            include_once(QUM_PLUGIN_DIR . '/update/update-checker.php');
            include_once(QUM_PLUGIN_DIR . '/admin/register-version.php');
        }

        if (file_exists(QUM_PLUGIN_DIR . '/modules/modules.php')) {
            include_once(QUM_PLUGIN_DIR . '/modules/modules.php');
            include_once(QUM_PLUGIN_DIR . '/modules/custom-redirects/custom-redirects.php');
            include_once(QUM_PLUGIN_DIR . '/modules/email-customizer/email-customizer.php');
            include_once(QUM_PLUGIN_DIR . '/modules/multiple-forms/multiple-forms.php');

            $qum_module_settings = get_option('qum_module_settings');
            if (isset($qum_module_settings['qum_userListing']) && ($qum_module_settings['qum_userListing'] == 'show')) {
                include_once(QUM_PLUGIN_DIR . '/modules/user-listing/userlisting.php');
                add_shortcode('qum-list-users', 'qum_user_listing_shortcode');
            } else
                add_shortcode('qum-list-users', 'qum_list_all_users_display_error');

            if (isset($qum_module_settings['qum_emailCustomizerAdmin']) && ($qum_module_settings['qum_emailCustomizerAdmin'] == 'show'))
                include_once(QUM_PLUGIN_DIR . '/modules/email-customizer/admin-email-customizer.php');

            if (isset($qum_module_settings['qum_emailCustomizer']) && ($qum_module_settings['qum_emailCustomizer'] == 'show'))
                include_once(QUM_PLUGIN_DIR . '/modules/email-customizer/user-email-customizer.php');
        }

        /**
         * Check for add-ons
         *
         *
         */
        if (file_exists(QUM_PLUGIN_DIR . '/admin/add-ons.php')) {
        include_once(QUM_PLUGIN_DIR . '/admin/add-ons.php');
        }
        include_once(QUM_PLUGIN_DIR . '/assets/misc/plugin-compatibilities.php');
        if ( QUICK_USER_MANAGER != 'Quick User Manager Free' )
            include_once(QUM_PLUGIN_DIR . '/front-end/extra-fields/recaptcha/recaptcha.php'); //need to load this here for displaying reCAPTCHA on Login and Recover Password forms


        /**
         * Check for updates
         *
         *
         */
        if (file_exists(QUM_PLUGIN_DIR . '/update/update-checker.php')) {
            if (file_exists(QUM_PLUGIN_DIR . '/modules/modules.php')) {
                $localSerial = get_option('qum_QUICK_USER_MANAGER_pro_serial');
                $qum_update = new QUM_PLUGINUpdateChecker('http://updatemetadata.crackcodex.com/?localSerialNumber=' . $localSerial . '&uniqueproduct=CLqumP', __FILE__, 'quick-user-manager-pro-update');

            }
        }


// these settings are important, so besides running them on page load, we also need to do a check on plugin activation
        register_activation_hook(__FILE__, 'qum_generate_default_settings_defaults');    //prepoulate general settings
        register_activation_hook(__FILE__, 'qum_prepopulate_fields');                    //prepopulate manage fields list

    }
} //end qum_free_plugin_init
add_action( 'plugins_loaded', 'qum_free_plugin_init' );

if (file_exists( plugin_dir_path(__FILE__) . '/front-end/extra-fields/upload/upload_helper_functions.php'))
    include_once( plugin_dir_path(__FILE__) . '/front-end/extra-fields/upload/upload_helper_functions.php');