<?php

namespace banana\user_profiles;

add_filter( 'get_avatar_url', 'banana\user_profiles\filter_get_avatar_url', 10, 2 );

function filter_get_avatar_url( $url, $id_or_email ) {

	// If $id_or_email is a WP_User object, just get the user ID from it
	if ( is_object( $id_or_email ) && isset( $id_or_email->ID ) ) {
		$id_or_email = $id_or_email->ID;
	}

	// If $id_or_email is a WP_Comment object, just get the user ID from it
	if ( is_object( $id_or_email ) && isset( $id_or_email->user_id ) ) {
		$id_or_email = $id_or_email->user_id;
	}

	// Normalize the current user ID
	if ( is_numeric( $id_or_email ) ) {
		$user_id = $id_or_email;
	} else if ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
		$user = get_user_by( 'email', $id_or_email );
		if ( empty( $user ) ) {
			return $url;
		}
		$user_id = $user->ID;
	} else {
		return $url;
	}

	// Get user meta avatar
	$custom_avatar_url = get_user_meta( $user_id, 'avatar', true );

	if ( empty( $custom_avatar_url ) ) {
		return $url;
	}

	return $custom_avatar_url;

}
