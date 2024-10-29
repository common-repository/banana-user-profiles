<?php

/**
 * Lost password view
 * This is the view that displays the form send a password recovery link.
 * You may replace this view by adding a file with the same name to your theme folder
 * inside a folder called "user-profiles".
 * Ex: /wp-content/themes/my-theme/user-profiles/form-lost-password.php
 */

namespace banana\user_profiles;
$Login = new Login();
?>

<form class="outlined-form outlined-form--centered" method="post">
	<?php $Login->maybe_display_error(); ?>
    <div class="outlined-form__field">
        <label for="mail-or-user">E-mail or username:</label>
        <input type="text" name="mail-or-user" id="mail-or-user">
    </div>
    <input type="hidden" name="user_profile-password-recovery-step" value="send-recovery-link">
	<?php wp_nonce_field( 'user_profile_password_recovery_action', 'nonce-password-recovery' ); ?>
    <input class="button" type="submit" value="Reset my password">
</form>