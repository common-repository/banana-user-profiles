<?php

namespace banana\user_profiles;

add_filter( 'display_post_states', 'banana\user_profiles\add_display_post_states', 10, 2 );

/**
 * Add a post display state for special User Profile pages in the page list table.
 *
 * @param array   $post_states An array of post display states.
 * @param WP_Post $post The current post object.
 */
function add_display_post_states( $post_states, $post ) {

	$login_page_id                 = get_option( 'user_profile_page_id_for_login' );
	$registration_page_id          = get_option( 'user_profile_page_id_for_registration' );
	$registration_finished_page_id = get_option( 'user_profile_page_id_for_registration_finished' );
	$show_my_profile_page_id       = get_option( 'user_profile_page_id_for_show_my_profile' );
	$edit_my_profile_page_id       = get_option( 'user_profile_page_id_for_edit_my_profile' );

	if ( (int) $login_page_id === $post->ID ) {
		$post_states['login_page'] = __( 'Login', 'banana-user-profiles' );
	}

	if ( (int) $registration_page_id === $post->ID ) {
		$post_states['registration_page'] = __( 'Registration', 'banana-user-profiles' );
	}

	if ( (int) $registration_finished_page_id === $post->ID ) {
		$post_states['registration_finished_page'] = __( 'Registration Finished', 'banana-user-profiles' );
	}

	if ( (int) $show_my_profile_page_id === $post->ID ) {
		$post_states['show_my_profile_page'] = __( 'Show my Profile', 'banana-user-profiles' );
	}

	if ( (int) $edit_my_profile_page_id === $post->ID ) {
		$post_states['edit_my_profile_page'] = __( 'Edit my profile', 'banana-user-profiles' );
	}

	return $post_states;
}
