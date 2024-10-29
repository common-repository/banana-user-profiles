<?php

namespace banana\user_profiles;

// Include a template file (try to find it in the theme directory first or load default)
function load_view( $file ) {
	$child_theme_dir  = get_stylesheet_directory() . '/user-profiles/';
	$parent_theme_dir = get_template_directory() . '/user-profiles/';

	if ( file_exists( $child_theme_dir . $file ) ) {
		include $child_theme_dir . $file;
	} elseif ( file_exists( $parent_theme_dir . $file ) ) {
		include $parent_theme_dir . $file;
	} else {
		include plugin_dir_path( __DIR__ ) . 'views/' . $file;
	}
}