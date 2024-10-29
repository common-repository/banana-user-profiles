<?php

/**
 * My profile view
 * This is the view that is shown when the user is logged in.
 * You may replace this view by adding a file with the same name to your theme folder
 * inside a folder called "user-profiles".
 * Ex: /wp-content/themes/my-theme/user-profiles/my-profile.php
 */

namespace banana\user_profiles;
$Profile      = new Profile();
$current_user = wp_get_current_user();
?>

<div class="user-profile">

    <div class="user-profile__header">
        <figure class="user-profile__avatar">
            <?php echo get_avatar( $current_user->ID, 120 ); ?>
        </figure>
        <div class="user-profile__info">
            <p class="user-profile__name"><?php echo esc_html( $current_user->display_name ); ?></p>
            <p class="user-profile__email"><?php echo esc_html( $current_user->user_email ); ?></p>
            <p class="user-profile__links">
                <a class="button" href="<?php echo esc_url( $Profile->edit_my_profile_url() ); ?>">
		            <?php _e( 'Edit my profile', 'banana-user-profiles' ); ?>
                </a>
                <a class="button" href="<?php echo wp_logout_url(); ?>">
		            <?php _e( 'Logout', 'banana-user-profiles' ); ?>
                </a>
            </p>
        </div>
    </div>


</div>
