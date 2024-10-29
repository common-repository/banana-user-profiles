=== Banana User Profiles ===

Contributors: @alvarofranz
Tags: users
Requires at least: 5.0
Tested up to: 6.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Clean user profile functionality with no bloat.

== Description ==

There are a few hundred plugins out there which intend to solve the same problem: How to get user profiles, login and registration features outside the Admin area.

Many of them are amazing, but I did not yet find a solution that just provides the basics and allows me to do the rest.

I created this plugin as a solid base to use in the different websites I work on and let the appearance and custom stuff be handled by the theme or even a complementary self-made plugin.

== Installation ==

1. Upload the plugin folder to your /wp-content/plugins/ folder.
2. Go to the **Plugins** page and activate the plugin.
3. Create the necessary pages for the plugin to work.
3.1. For the login page, use the shortcode `[login]`.
3.2. For the registration page, use the shortcode `[registration]`.
3.3. For the profile page, use the shortcode `[show_my_profile]`.
3.4. For the edit profile page, use the shortcode `[edit_my_profile]`.
3.5. For the registration finished page, just create a page and add whatever you want.
4. Go to **Settings > User profiles** and add the page IDs for the pages you created in the previous step.

== Frequently Asked Questions ==

= How to uninstall the plugin? =

Simply deactivate and delete the plugin.

== Changelog ==
= 1.0 =
* Plugin released.
