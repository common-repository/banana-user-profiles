<?php

namespace banana\user_profiles;

// Make sure we are uninstalling a plugin
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete options from database
function user_profile_delete_all_options() {
	delete_option( 'user_profile_page_id_for_login' );
	delete_option( 'user_profile_page_id_for_registration' );
	delete_option( 'user_profile_page_id_for_registration_finished' );
	delete_option( 'user_profile_page_id_for_show_my_profile' );
	delete_option( 'user_profile_page_id_for_edit_my_profile' );
}

user_profile_delete_all_options();
