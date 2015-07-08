=== Quick User Manager - front-end user registration, login and edit profile, email to the user(s) === 

Contributors: crackcodex, rhinokul


Donate link: http://plugin.crackcodex.com/quick-user-manager/

Tags: quick user manager, user manager, registration, user profile, user registration, custom field registration, customize profile, user fields, extra user fields, builder, custom user profile, user profile page, edit profile, custom registration, custom registration form, custom registration page, registration page, user custom fields, user listing, front-end user listing, user login, user registration form, front-end login, login redirect, login widget, front-end register, front-end registration, front-end edit profile, front-end user registration, custom redirects, user email, avatar upload, email confirmation, user approval, customize registration email, minimum password length, minimum password strength, password strength meter, multiple registration forms, register, register form, email to the registered user(s), email to the users group, member directory

Requires at least: 3.1
Tested up to: 4.2.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple to use profile plugin allowing front-end login, user registration and edit profile by using shortcodes.
 
== Description ==

**Quick User Manager is WordPress user registration done right.**

It lets you customize your website by adding a front-end menu for all your users, 
giving them a more flexible way to modify their user profile or register new users (front-end user registration). 
Users with administrator rights can customize basic user fields or add custom user fields to the front-end forms, email to the user(s) or users group.

To achieve this, simply create a new page and give it an intuitive name(i.e. Edit Profile).
Now all you need to do is add the following shortcode: [qum-edit-profile].
Publish the page and you are done!

= Front-end User Registration, Login, Edit Profile and Password Recovery Shortcodes =
You can use the following shortcode list:

* **[qum-edit-profile]** - to grant users front-end access to their profile (requires user to be logged in).
* **[qum-login]** - to add a front-end login form.
* **[qum-logout]** - to add logout functionality.
* **[qum-register]** - to add a front-end register form.
* **[qum-recover-password]** - to add a password recovery form.

Users with administrator rights have access to the following features:

* drag & drop to reorder user profile fields
* enable **Email Confirmation** (on registration users will receive a notification to confirm their email address).
* allow users to **Log-in with their Username or Email**
* enforce a **minimum password length** and **minimum password strength** (using the default WordPress password strength meter)
* assign users a specific role at registration (using **[qum-register role="desired_role"]** shortcode argument for the register form)
* redirect users after login, register and edit-profile using redirect_url shortcode argument ( e.g **[qum-login redirect_url="www.example.com"]** )
* add register and lost password links below the login form (using **[qum-login register_url="www.example.com" lostpassword_url="www.example.com"]** shortcode arguments)
* customizable login widget
* add a custom stylesheet/inherit values from the current theme or use the default one built into this plugin.
* chose which user roles view the admin bar in the front-end of the website (Admin Bar Settings page).
* select which profile fields users can see/modify.

**Quick User Manager PRO**

The [Pro version](http://plugin.crackcodex.com/quick-user-manager/?utm_source=wp.org&utm_medium=plugin-description-page&utm_campaign=QUMFree) has the following extra features:

* User Listing (fully customizable, sorting included)
* Create Multiple User Listings
* Custom Redirects
* Admin Approval
* Email Customizer (Personalize all emails sent to your users, users group or admins; customize default WordPress registration email)

[Find out more about Quick User Manager PRO](http://plugin.crackcodex.com/quick-user-manager/?utm_source=wp.org&utm_medium=plugin-description-page&utm_campaign=QUMFree)


= Quick User Manager in your Language =
We're focusing on translating Quick User Manager in as many languages as we can. So far, the translations is under process(http://translate.crackcodex.com/projects/quickusermanager). Please help us to translate Quick User Manager in your language.

NOTE:
This plugin adds/removes user fields in the front-end. Both default and extra profile fields will be visible in the back-end as well.
	

== Installation ==

1. Upload the quick-user-manager folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a new page and use one of the shortcodes available. Publish the page and you're done!

== Frequently Asked Questions ==

= I navigated away from Quick User Manager and now I can't find it anymore; where is it? =

	Quick User Manager can be found in the default menu of your WordPress installation below the 'Users' menu item.

= Why do the default WordPress user fields still show up in the back-end? =

	Quick User Manager can only remove the default user fields in the front-end of your site/blog, it doesn't remove them from the back-end.

= I can’t find a question similar to my issue; Where can I find support? =

	For more information please visit http://www.plugin.crackcodex.com/quick-user-manager and check out the documentation section from Quick User Manager - front-end user registration, login and edit profile, email to the registered user(s) and users group.


== Screenshots ==
1. Basic Information - Quick User Manager, front-end user registration plugin
2. General Settings - Quick User Manager, front-end user registration plugin
3. Show/Hide Admin Bar
4. Quick User Manager - Manage Default User Fields (Add, Edit or Delete)
5. Quick User Manager - Drag & Drop to Reorder User Profile Fields
6. Register Form - Front-end User Registration Page
7. User Login Page
8. Edit User Profile Page
9. Recover Password Page
10. Quick User Manager Login Widget

== Changelog ==
= 1.0 =
Initial Release

