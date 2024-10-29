<?php

namespace banana\user_profiles;

// ACTIONS AND FILTERS

// Create the settings page
add_action( 'admin_menu', 'banana\user_profiles\settings_page' );

// Create sections and fields
add_action( 'admin_init', 'banana\user_profiles\settings_page_sections_and_fields' );

// ACTIONS AND FILTERS CALLBACKS
function settings_page() {
	add_options_page(
		__( 'User Profiles Settings', 'banana-user-profiles' ),
		__( 'User Profiles', 'banana-user-profiles' ),
		'manage_options',
		'user_profile_settings_page',
		'banana\user_profiles\render_settings_page_content',
		3
	);
}

function settings_page_sections_and_fields() {

	// The section for pages
	add_settings_section(
		'user_profile_pages_settings_section',
		__( 'Pages', 'banana-user-profiles' ),
		'banana\user_profiles\pages_settings_section_heading',
		'user_profile_settings_page'
	);

	// The section for options
	add_settings_section(
		'user_profile_options_settings_section',
		__( 'Options', 'banana-user-profiles' ),
		'banana\user_profiles\options_settings_section_heading',
		'user_profile_settings_page'
	);

	// For the "pages" section

	// Add Setting: Login page ID
	register_setting(
		'user_profile_settings',
		'user_profile_page_id_for_login',
	);

	add_settings_field(
		'user_profile_page_id_for_login',
		__( 'Login page ID', 'banana-user-profiles' ),
		'banana\user_profiles\display_login_page_id_field',
		'user_profile_settings_page',
		'user_profile_pages_settings_section',
	);

	// Add Setting: Registration page ID
	register_setting(
		'user_profile_settings',
		'user_profile_page_id_for_registration'
	);

	add_settings_field(
		'user_profile_page_id_for_registration',
		__( 'Registration page ID', 'banana-user-profiles' ),
		'banana\user_profiles\display_registration_page_id_field',
		'user_profile_settings_page',
		'user_profile_pages_settings_section'
	);

	// Add Setting: Registraion finished page ID
	register_setting(
		'user_profile_settings',
		'user_profile_page_id_for_registration_finished'
	);

	add_settings_field(
		'user_profile_page_id_for_registration_finished',
		__( 'Registration finished page ID', 'banana-user-profiles' ),
		'banana\user_profiles\display_registration_finished_page_id_field',
		'user_profile_settings_page',
		'user_profile_pages_settings_section'
	);

	// Add Setting: Show my profile page ID
	register_setting(
		'user_profile_settings',
		'user_profile_page_id_for_show_my_profile'
	);

	add_settings_field(
		'user_profile_page_id_for_show_my_profile',
		__( 'Show my profile page ID', 'banana-user-profiles' ),
		'banana\user_profiles\display_show_my_profile_page_id_field',
		'user_profile_settings_page',
		'user_profile_pages_settings_section'
	);

	// Add Setting: Edit my profile page ID
	register_setting(
		'user_profile_settings',
		'user_profile_page_id_for_edit_my_profile'
	);

	add_settings_field(
		'user_profile_page_id_for_edit_my_profile',
		__( 'Edit my profile page ID', 'banana-user-profiles' ),
		'banana\user_profiles\display_edit_my_profile_page_id_field',
		'user_profile_settings_page',
		'user_profile_pages_settings_section'
	);

	// For the "options" section

	// Add Setting: Disable activation email
	register_setting(
		'user_profile_settings',
		'user_profile_disable_activation_email'
	);

	add_settings_field(
		'user_profile_disable_activation_email',
		__( 'Disable activation email (auto activate account)', 'banana-user-profiles' ),
		'banana\user_profiles\display_disable_activation_email_field',
		'user_profile_settings_page',
		'user_profile_options_settings_section'
	);

}

// This function renders the heading for the Pages section
function pages_settings_section_heading() {
	echo '<p>' . __( 'Please set the pages ids for each view.', 'banana-user-profiles' ) . '</p>';
}

// This function renders the heading for the Options section
function options_settings_section_heading() {
	// Not showing anything for now, because there is only one option
	// echo '<p>' . __( 'Other plugin options.', 'banana-user-profiles' ) . '</p>';
	return;
}

// Render login page ID form input
function display_login_page_id_field() {
	echo '<input
    type="number"
    name="user_profile_page_id_for_login"
    value="' . esc_attr( get_option( 'user_profile_page_id_for_login' ) ) . '" />';
}

// Render registration page ID form input
function display_registration_page_id_field() {
	echo '<input
    type="number"
    name="user_profile_page_id_for_registration"
    value="' . esc_attr( get_option( 'user_profile_page_id_for_registration' ) ) . '" />';
}

// Render registration finished page ID form input
function display_registration_finished_page_id_field() {
	echo '<input
    type="number"
    name="user_profile_page_id_for_registration_finished"
    value="' . esc_attr( get_option( 'user_profile_page_id_for_registration_finished' ) ) . '" />';
}

// Render show my profile page ID form input
function display_show_my_profile_page_id_field() {
	echo '<input
    type="number"
    name="user_profile_page_id_for_show_my_profile"
    value="' . esc_attr( get_option( 'user_profile_page_id_for_show_my_profile' ) ) . '" />';
}

// Render edit my profile page ID form input
function display_edit_my_profile_page_id_field() {
	echo '<input
    type="number"
    name="user_profile_page_id_for_edit_my_profile"
    value="' . esc_attr( get_option( 'user_profile_page_id_for_edit_my_profile' ) ) . '" />';
}

// Render the checkbox to disable the activation email
function display_disable_activation_email_field() {
	$checked = ( get_option( 'user_profile_disable_activation_email' ) ) ? ' checked="checked"' : '';
	echo '<input ' . esc_html( $checked ) . '
    type="checkbox"
    name="user_profile_disable_activation_email"
    value="1" />';
}

// Function that displays the settings page in the admin area
function render_settings_page_content() {
	echo '
    <h1>' . __( 'User Profile Settings' ) . '</h1>
    <p>' . __( 'Settings for the Banana User Profile plugin', 'banana-user-profiles' ) . '</p>
    ';

	// Start the form
	echo '<form action="options.php" method="post">';

	// Generate fields
	settings_fields( 'user_profile_settings' );
	do_settings_sections( 'user_profile_settings_page' );

	// Display save button and close form
	echo '
    <input
      type="submit"
      name="submit"
      class="button button-primary"
      value="Save"
    />
    </form>';
}