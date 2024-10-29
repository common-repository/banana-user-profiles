<?php

/**
 * Reset password view
 * This is the view that displays the form to reset the password.
 * You may replace this view by adding a file with the same name to your theme folder
 * inside a folder called "user-profiles".
 * Ex: /wp-content/themes/my-theme/user-profiles/form-reset-password.php
 */

namespace banana\user_profiles;
$Login = new Login();
?>

<form class="outlined-form outlined-form--centered" method="post">
	<?php $Login->maybe_display_error(); ?>
    <input type="hidden" name="key" value="<?php echo esc_attr( $_GET['key'] ); ?>">
    <input type="hidden" name="login" value="<?php echo esc_attr( $_GET['login'] ); ?>">
    <input type="hidden" name="user_profile-password-recovery-step" value="execute-password-reset">
    <div class="outlined-form__field">
        <label for="new-password"><?php esc_attr_e( 'New password', 'banana-user-profiles' ); ?></label>
        <input autocomplete="off" name="new-password" id="new-password" type="password">
    </div>
	<?php wp_nonce_field( 'user_profile_password_recovery_action', 'nonce-password-recovery' ); ?>
    <input class="button" type="submit" value="<?php esc_attr_e( 'Update password', 'banana-user-profiles' ); ?>"/>
</form>