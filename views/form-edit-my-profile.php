<?php

namespace banana\user_profiles;
$Profile = new Profile();

// Load all editable sections
$sections = apply_filters(
	'user_profile_editable_sections',
	[]
);

// Action hook before displaying sections
do_action( 'user_profile_hook_before_displaying_sections', $sections, get_current_user_id() );

$Profile->maybe_display_notice();
?>
<div class="user-profile-edit-form">
    <div class="user-profile-edit-form__header">
        <?php echo '<a href="' . esc_url( $Profile->my_profile_url() ) . '">' . __( 'Back to my profile', 'banana-user-profiles' ) . '</a>'; ?>
    </div>
    <div class="user-profile-edit-form__body">
        <ul class="user-profile-edit-form__sidebar">
            <?php
            foreach ( $sections as $section ) {
                echo '<li><a href="#' . esc_attr( $section['id'] ) . '">' . esc_html( $section['title'] ) . '</a></li>';
            }
            ?>
        </ul>
        <div class="user-profile-edit-form__sections">
        <?php
        // Loop through each item
        foreach ( $sections as $section ) {

            // Build the content class
            $content_class = '';

            // If we have a class provided
            if ( '' !== $section['content_class'] ) {
                $content_class .= ' ' . $section['content_class'];
            }
            ?>

            <div class="outlined-form user-profile-edit-form__section-<?php echo esc_attr( $content_class ); ?>"
                 id="<?php echo esc_attr( $section['id'] ); ?>">

                <form method="post" action="<?php echo esc_url( $Profile->edit_my_profile_url() ); ?>"
                      enctype='multipart/form-data'>
                    <?php
                    // Check if this section has a custom callback function
                    if ( isset( $section['callback'] ) && function_exists( $section['callback'] ) ) {
                        // Use custom callback function
                        $section['callback']( $section );
                    } else {
                        // Use default callback function
                        $Profile->display_editable_section( $section );
                    }

                    wp_nonce_field(
                        'user_profile_edit_profile_action',
                        'edit-profile-nonce'
                    );
                    ?>
                    <input type="submit" value="<?php _e( 'Save', 'banana-user-profiles' ); ?>">
                </form>
            </div>
            <?php
        }
        ?>
        </div>
    </div>
</div>
