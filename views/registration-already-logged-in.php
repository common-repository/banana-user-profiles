<?php

/**
 * Already logged in view
 * This is the view that is shown when the user is already logged in and wants to register.
 * You may replace this view by adding a file with the same name to your theme folder
 * inside a folder called "user-profiles".
 * Ex: /wp-content/themes/my-theme/user-profiles/registration-already-logged-in.php
 */

namespace banana\user_profiles;

esc_html_e( 'Please log out from your current account if you want to create a new one.', 'banana-user-profiles' );

?>

<!-- Logout link -->
<a href="<?php echo wp_logout_url(); ?>">
	<?php _e( 'Logout', 'banana-user-profiles' ); ?>
</a>