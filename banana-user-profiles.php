<?php
/**
 * Plugin Name: Banana User Profiles
 * Description: Clean user profile functionality with no bloat.
 * Version:     1.0.0
 * Author:      Álvaro Franz
 * Text Domain: banana-user-profiles
 * Domain Path: /translations/
 */

namespace banana\user_profiles;

// Plugin helper functions
require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';

// Load Login class and setup hooks
require_once plugin_dir_path( __FILE__ ) . 'includes/class-login.php';
Login::setup_hooks();

// Load Registration class and setup hooks
require_once plugin_dir_path( __FILE__ ) . 'includes/class-registration.php';
Registration::setup_hooks();

// Load Profile class and setup hooks
require_once plugin_dir_path( __FILE__ ) . 'includes/class-profile.php';
Profile::setup_hooks();

// Filter avatar URL
require_once plugin_dir_path( __FILE__ ) . 'includes/user-avatar-filter.php';

// Load customizations for the admin area
if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/settings-page.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/users-area-customizations.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/custom-page-states-filter.php';
}

// Load text domain
add_action( 'plugins_loaded', 'banana\user_profiles\user_profile_load_text_domain' );
function user_profile_load_text_domain() {
	load_plugin_textdomain( 'user_profile', false /* Deprecated argument */, dirname( plugin_basename( __FILE__ ) ) . '/translations/' );
}

// Disable admin bar
add_filter( 'show_admin_bar', '__return_false' );