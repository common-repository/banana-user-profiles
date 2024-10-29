<?php

/**
 * Login view
 * This is the view that is shown when the user is not logged in.
 * You may replace this view by adding a file with the same name to your theme folder
 * inside a folder called "user-profiles".
 * Ex: /wp-content/themes/my-theme/user-profiles/form-login.php
 */

namespace banana\user_profiles;
$Login = new Login();
?>

<form class="outlined-form outlined-form--centered" method="post" action="<?php echo $Login->login_url(); ?>">
	<?php
	if ( isset( $_GET['ac'] ) ) { // Activated
		echo '<div class="user-profile-login-form__success">' . esc_html( __( 'Your account has been activated.', 'banana-user-profiles' ) ) . '</div>';
	} elseif ( isset( $_GET['pu'] ) ) { // Password updated
		echo '<div class="user-profile-login-form__success">' . esc_html( __( 'Your password has been updated.', 'banana-user-profiles' ) ) . '</div>';
	} elseif ( isset( $_GET['lo'] ) ) { // Logged out
		echo '<div class="user-profile-login-form__success">' . esc_html( __( 'You are now logged out of your account.', 'banana-user-profiles' ) ) . '</div>';
	}
	$Login->maybe_display_error();
	?>
    <div class="outlined-form__field">
        <label for="mail-or-user"><?php _e( 'Email or Username', 'banana-user-profiles' ); ?></label>
        <input type="text" name="mail-or-user"
               id="mail-or-user"
               value="<?php echo isset( $_POST['mail-or-user'] ) ? esc_attr( $_POST['mail-or-user'] ) : ''; ?>">
    </div>
    <div class="outlined-form__field">
        <label for="password"><?php _e( 'Password', 'banana-user-profiles' ); ?></label>
        <input type="password" id="password" name="password">
    </div>
    <div class="outlined-form__field">
        <div>
            <input name="remember" id="remember" type="checkbox" value="forever">
            <label for="remember"><?php _e( 'Remember', 'banana-user-profiles' ); ?></label>
        </div>
    </div>
    <div class="outlined-form__field">
		<?php wp_nonce_field( 'user_profile_login_action', 'login-nonce' ); ?>
        <input type="submit" class="button" value="<?php _e( 'Login', 'banana-user-profiles' ); ?>"/>
    </div>
    <div>
		<?php
		echo '<a class="link link--gray" href="' . $Login->get_action_url( 'lp' ) . '">' . __( 'Lost your password?', 'banana-user-profiles' ) . '</a>';
		?>
    </div>
</form>
