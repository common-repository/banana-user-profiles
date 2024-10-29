<?php

/**
 * Registration view
 * This is the view that displays the registration form.
 * You may replace this view by adding a file with the same name to your theme folder
 * inside a folder called "user-profiles".
 * Ex: /wp-content/themes/my-theme/user-profiles/form-registration.php
 */

namespace banana\user_profiles;
$Registration = new Registration();
?>

<form class="outlined-form outlined-form--centered" method="post">
    <?php $Registration->maybe_display_error(); ?>
    <div class="outlined-form__field">
        <label for="user_username"><?php _e( 'Username', 'banana-user-profiles' ); ?></label>
        <input type="text" required name="user_username"
               id="user_username"
               pattern="[a-zA-Z0-9\-_]+"
               value="<?php echo isset( $_POST['user_username'] ) ? esc_attr( $_POST['user_username'] ) : ''; ?>">
    </div>
    <div class="outlined-form__field">
        <label for="user_first_name"><?php _e( 'First name', 'banana-user-profiles' ); ?></label>
        <input type="text" name="user_first_name" id="user_first_name"
               value="<?php echo isset( $_POST['user_first_name'] ) ? esc_attr( $_POST['user_first_name'] ) : ''; ?>">
    </div>
    <div class="outlined-form__field">
        <label for="user_last_name"><?php _e( 'Last name', 'banana-user-profiles' ); ?></label>
        <input type="text" name="user_last_name" id="user_last_name"
               value="<?php echo isset( $_POST['user_last_name'] ) ? esc_attr( $_POST['user_last_name'] ) : ''; ?>">
    </div>
    <div class="outlined-form__field">
        <label for="user_email"><?php _e( 'E-mail', 'banana-user-profiles' ); ?></label>
        <input type="email" required name="user_email" id="user_email" 
               value="<?php echo isset( $_POST['user_email'] ) ? esc_attr( $_POST['user_email'] ) : ''; ?>">
    </div>
    <div class="outlined-form__field">
        <label for="password1"><?php _e( 'Password', 'banana-user-profiles' ); ?></label>
        <input type="password" required id="password1" name="password1"
               placeholder="<?php _e( 'Choose wisely', 'banana-user-profiles' ); ?>">
        <input type="password" required name="password2"
               placeholder="<?php _e( 'Confirm password', 'banana-user-profiles' ); ?>">
    </div>
    <div class="outlined-form__field">
		<?php wp_nonce_field( 'user_profile_registration_action', 'registration-nonce' ); ?>
        <input class="button" type="submit" value="<?php _e( 'Create account', 'banana-user-profiles' ); ?>">
    </div>
</form>