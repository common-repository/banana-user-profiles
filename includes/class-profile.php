<?php

namespace banana\user_profiles;

// This class handles the User Show Profile and Edit profile functionality
class Profile {

	// Message array
	private $messages = [];

	// Activate class: Setup shortcodes and add some actions and filters
	public static function setup_hooks() {

		$this_class = new self();

		add_shortcode( 'show_my_profile', [ $this_class, 'shortcode_show_my_profile' ] );
		add_shortcode( 'edit_my_profile', [ $this_class, 'shortcode_edit_my_profile' ] );

		add_action( 'template_redirect', [ $this_class, 'maybe_update_user_password' ], 10, 2 );
		add_action( 'user_profile_hook_before_displaying_sections', [ $this_class, 'save_editable_fields' ], 5, 2 );

		add_filter( 'user_profile_editable_sections', [ $this_class, 'add_profile_section' ], 10 );
		add_filter( 'user_profile_editable_sections', [ $this_class, 'add_avatar_section' ], 10 );
		add_filter( 'user_profile_editable_sections', [ $this_class, 'add_password_section' ], 20 );

		add_filter( 'user_profile_editable_fields_profile', [ $this_class, 'default_editable_fields_profile' ], 10 );
		add_filter( 'user_profile_editable_fields_avatar', [ $this_class, 'default_editable_fields_avatar' ], 10 );
		add_filter( 'user_profile_editable_fields_password', [ $this_class, 'default_editable_fields_password' ], 10 );

	}

	// Add the profile section
	public function add_profile_section( $sections ) {

		$sections[] = [
			'id'            => 'profile',
			'title'         => __( 'Profile', 'banana-user-profiles' ),
            'description'   => __( 'Edit your profile', 'banana-user-profiles' ),
			'section_class' => 'profile-section',
			'content_class' => 'profile-content'
		];

		return $sections;
	}

	// Add the avatar section
	public function add_avatar_section( $sections ) {

		$sections[] = [
			'id'            => 'avatar',
			'title'         => __( 'Avatar', 'banana-user-profiles' ),
            'description'   => __( 'Change your profile picture', 'banana-user-profiles' ),
			'section_class' => 'avatar-section',
			'content_class' => 'avatar-content'
		];

		return $sections;
	}


	// Add the password section
	public function add_password_section( $sections ) {

		$sections[] = [
			'id'            => 'password',
			'title'         => __( 'Password', 'banana-user-profiles' ),
            'description'   => __( 'Change your password (leave blank to do nothing)', 'banana-user-profiles' ),
			'section_class' => 'password-section',
			'content_class' => 'password-content'
		];

		return $sections;
	}

	// Default profile section fields
	function default_editable_fields_profile( $fields ) {
		$fields[] = [
			'id'      => 'user_email',
			'label'   => __( 'Email Address', 'banana-user-profiles' ),
			'description'    => __( 'Edit your email address', 'banana-user-profiles' ),
			'type'    => 'email',
			'classes' => 'user_email',
		];
		$fields[] = [
			'id'      => 'first_name',
			'label'   => __( 'First Name', 'banana-user-profiles' ),
			'description'    => __( 'Edit your first name', 'banana-user-profiles' ),
			'type'    => 'text',
			'classes' => 'first_name',
		];
		$fields[] = [
			'id'      => 'last_name',
			'label'   => __( 'Last Name', 'banana-user-profiles' ),
			'description'    => __( 'Edit your last name', 'banana-user-profiles' ),
			'type'    => 'text',
			'classes' => 'last_name',
		];
		$fields[] = [
			'id'      => 'user_url',
			'label'   => __( 'URL', 'banana-user-profiles' ),
			'description'    => __( 'Edit your profile associated URL', 'banana-user-profiles' ),
			'type'    => 'text',
			'classes' => 'user_url',
		];

		return $fields;
	}

	// Default avatar section fields
	function default_editable_fields_avatar( $fields ) {
		$fields[] = [
			'id'      => 'avatar',
			'label'   => __( 'Avatar', 'banana-user-profiles' ),
			'description'    => __( 'User profile image', 'banana-user-profiles' ),
			'type'    => 'image',
			'classes' => 'avatar',
		];

		return $fields;
	}

	// Default password section fields
	function default_editable_fields_password( $fields ) {
		$fields[] = [
			'id'      => 'user_pass',
			'label'   => __( 'Password', 'banana-user-profiles' ),
			'description'    => __( 'New password', 'banana-user-profiles' ),
			'type'    => 'password',
			'classes' => 'user_pass',
		];

		return $fields;
	}


	// [user_profile_show_my_profile] shortcode callback
	public function shortcode_show_my_profile() {

		// If the user is not logged in, show login form
		if ( is_user_logged_in() ) {
			$view = 'my-profile.php';
		} else {
			$view = 'form-login.php';
		}

		ob_start();

		load_view( $view );

		return ob_get_clean();
	}

	// [user_profile_edit_my_profile] shortcode callback
	public function shortcode_edit_my_profile() {

		// If the user is not logged in, show login form
		if ( is_user_logged_in() ) {
			$view = 'form-edit-my-profile.php';
		} else {
			$view = 'form-login.php';
		}

		ob_start();

		load_view( $view );

		return ob_get_clean();
	}


	// Get my profile URL
	public function my_profile_url() {
		$my_profile_page_id = get_option( 'user_profile_page_id_for_show_my_profile' );
		if ( ! $my_profile_page_id ) {
			return false;
		}

		return get_permalink( $my_profile_page_id );
	}

	// Get the edit my profile URL
	public function edit_my_profile_url() {
		$edit_my_profile_page_id = get_option( 'user_profile_page_id_for_edit_my_profile' );
		if ( ! $edit_my_profile_page_id ) {
			return false;
		}

		return get_permalink( $edit_my_profile_page_id );
	}

	// Display editable section
	public function display_editable_section( $section ) {

		// Build an array of fields to output
		$fields = apply_filters(
			'user_profile_editable_fields_' . $section['id'],
			[],
			get_current_user_ID()
		);

		// Make sure we have some fields
		if ( empty( $fields ) ) {
			return;
		}

		if ( isset( $section['title'] ) ) {
			echo '<div class="user-profile-edit-form__section-title">' . esc_html( $section['title'] ) . '</div>';
		}

		if ( isset( $section['description'] ) ) {
			echo '<div class="user-profile-edit-form__section-description">' . esc_html( $section['description'] ) . '</div>';
		}

		// Loop through the fields array
		foreach ( $fields as $field ) {
			$this->display_editable_field( $field, $section['id'], get_current_user_id() );
		}

	}

	// Display editable field
	public function display_editable_field( $field, $section_id, $user_id ) {
		$extra_field_classes = ( empty( $field['classes'] ) ) ? '' : ' ' . $field['classes'];

        // Native user object attribute ids
        $fields_handled_with_update_user = apply_filters(
            'user_profile_fields_handled_with_update_user',
            [
                'user_email',
                'user_url',
            ]
        );

        // If the current field id is in the special list
        if ( in_array( $field['id'], $fields_handled_with_update_user ) ) {
            $userdata            = get_userdata( $user_id );
            $current_field_value = $userdata->{$field['id']};

            // Not a reserved id, handle via user meta
        } else {
            $current_field_value = get_user_meta( get_current_user_id(), $field['id'], true );
        }

        // All fields will have the same id and name structure
        $field_id = $section_id . '-' . $field['id'];
        $field_name = $section_id . '[' . $field['id'] . ']';

        // Generate description HTML
        $description = ( empty( $field['description'] ) ) ? '' : '<div class="user-profile-edit-form__field-description">' . esc_html( $field['description'] ) . '</div>';

        switch ( $field['type'] ) {
            case 'image':
                ?>
                <div class="user-profile-edit-form__field user-profile-edit-form__field--image<?php echo $extra_field_classes; ?>">
                    <label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                    <input name="<?php echo esc_attr( $field_name ); ?>"
                           id="<?php echo esc_attr( $field_id ); ?>"
                           type="file" accept="image/*">
                    <img src="<?php echo esc_attr( $current_field_value ); ?>">
                    <?php echo $description; ?>
                </div>
                <?php
                break;
            case 'textarea':
                ?>
                <div class="user-profile-edit-form__field user-profile-edit-form__field--textarea<?php echo $extra_field_classes; ?>">
                    <label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                    <textarea name="<?php echo esc_attr( $field_name ); ?>"
                              id="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_textarea( $current_field_value ); ?></textarea>
                    <?php echo $description; ?>
                </div>
                <?php
                break;
            case 'checkbox':
                ?>
                <div class="user-profile-edit-form__field user-profile-edit-form__field--checkbox<?php echo $extra_field_classes; ?>">
                    <label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                    <input type="hidden"
                           name="<?php echo esc_attr( $field_name ); ?>"
                           value="0" />
                    <input type="checkbox"
                           name="<?php echo esc_attr( $field_name ); ?>"
                           id="<?php echo esc_attr( $field_id ); ?>"
                           value="1" <?php checked( $current_field_value, '1' ); ?> />
                    <?php echo $description; ?>
                </div>
                <?php
                break;
            case 'email':
                ?>
                <div class="user-profile-edit-form__field user-profile-edit-form__field--email<?php echo $extra_field_classes; ?>">
                    <label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                    <input type="email"
                           name="<?php echo esc_attr( $field_name ); ?>"
                           id="<?php echo esc_attr( $field_id ); ?>"
                           value="<?php echo esc_attr( $current_field_value ); ?>">
                    <?php echo $description; ?>
                </div>
                <?php
                break;
            case 'password':
                ?>
                <div class="user-profile-edit-form__field user-profile-edit-form__field--password<?php echo $extra_field_classes; ?>">
                    <label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                    <input type="password"
                           name="<?php echo esc_attr( $field_name ); ?>"
                           id="<?php echo esc_attr( $field_id ); ?>"
                           placeholder="<?php _e( 'New password', 'banana-user-profiles' ); ?>">
                    <input type="password"
                           name="<?php echo esc_attr( $section_id ); ?>[<?php echo esc_attr( $field['id'] ); ?>_check]"
                           id="<?php echo esc_attr( $field['id'] ); ?>_check"
                           placeholder="<?php _e( 'Confirm password', 'banana-user-profiles' ); ?>">
                    <?php echo $description; ?>
                </div>
                <?php
                break;
            default:
                ?>
                <div class="user-profile-edit-form__field user-profile-edit-form__field--text<?php echo $extra_field_classes; ?>">
                    <label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                    <input type="text"
                           name="<?php echo esc_attr( $field_name ); ?>"
                           id="<?php echo esc_attr( $field_id ); ?>"
                           value="<?php echo esc_attr( $current_field_value ); ?>">
                    <?php echo $description; ?>
                </div>
            <?php
        }
	}

	// Save editable fields (attached to the user_profile_hook_before_displaying_sections hook)
	public function save_editable_fields( $all_editable_sections, $user_id ) {

		// Verify nonce
		if ( ! isset( $_POST['edit-profile-nonce'] ) || ! wp_verify_nonce( $_POST['edit-profile-nonce'], 'user_profile_edit_profile_action' ) ) {
			return;
		}

		// Array to store messages
		$messages = [];

		// The $_POST data
		$posted_data = $_POST;

		// Attach the $_FILES data to the posted data keeping the field_id -> value structure
		if ( isset( $_FILES ) ) {
			foreach ( $_FILES as $file_row_key => $file_row_content ) {
				foreach ( $file_row_content['name'] as $field_id => $field_file_name ) {
					$posted_data[ $file_row_key ][ $field_id ] = $field_file_name;
				}
			}
		}

		// Check we have some data to save
		if ( empty( $posted_data ) ) {
			return;
		}

		// Reserved ids are handled in a different way
		$fields_handled_with_update_user = apply_filters(
			'user_profile_fields_handled_with_update_user',
			[
				'user_email',
				'user_url',
			]
		);

		// Array of all registered editable fields
		$all_editable_fields = [];
		foreach ( $all_editable_sections as $section ) {
			$section_fields      = apply_filters(
				'user_profile_editable_fields_' . $section['id'],
				[],
				$user_id
			);
			$all_editable_fields = array_merge( $all_editable_fields, $section_fields );
		}

		// Set an array of registered keys
		$all_editable_fields_ids = wp_list_pluck( $all_editable_fields, 'id' );

		// Loop through the data array - each element of this will be a section's data
		foreach ( $posted_data as $posted_data_key => $posted_data_value ) {

			// Check if this posted data row is an array ( = section data)
			if ( ! is_array( $posted_data_value ) ) {
				continue;
			}

			// Yes, it is an array of all the section fields (key => value)

			// Loop through this sections array
			foreach ( $posted_data_value as $field_id => $field_value ) {

				// If the key is not in our list of registered keys - move to next in array
				if ( ! in_array( $field_id, $all_editable_fields_ids ) ) {
					continue;
				}

				// Check whether the key is reserved - handled with wp_update_user
				if ( in_array( $field_id, $fields_handled_with_update_user ) ) {

					$user_id = wp_update_user(
						[
							'ID'      => $user_id,
							$field_id => $field_value,
						]
					);

					// Check for errors
					if ( is_wp_error( $user_id ) ) {
						$messages['update_failed'] = __( 'There was a problem with updating your perfil', 'banana-user-profiles' );
					}

					// Standard user meta, handle with update_user_meta
				} else {

					// Lookup field options by key
					$registered_field_array_key = array_search( $field_id, array_column( $all_editable_fields, 'id' ) );

					// Sanitize user input based on field type
					switch ( $all_editable_fields[ $registered_field_array_key ]['type'] ) {
						case 'textarea':
							$field_value = wp_filter_nohtml_kses( $field_value );
							break;
						case 'image':
							if ( wp_check_filetype( $_FILES[ $posted_data_key ]['tmp_name'][ $field_id ] ) ) {
								$upload_dir            = wp_upload_dir();
								$final_file_name       = uniqid() . '-' . $field_value;
								$file_destination_path = $upload_dir['path'] . '/' . $final_file_name;
								if ( move_uploaded_file( $_FILES[ $posted_data_key ]['tmp_name'][ $field_id ], $file_destination_path ) ) {
									$field_value = $upload_dir['url'] . '/' . $final_file_name;
								} else {
									$messages['update_failed'] = __( 'Image upload has failed', 'banana-user-profiles' );
								}
							} else {
								$messages['update_failed'] = __( 'Image type not allowed', 'banana-user-profiles' );
							}
							break;
						case 'checkbox':
							$field_value = isset( $field_value ) && '1' === $field_value;
							break;
						case 'email':
							$field_value = sanitize_email( $field_value );
							break;
						case 'text':
							$field_value = sanitize_text_field( $field_value );
							break;
						default:
							continue 2; // Jump out of switch and continue
					}

					update_user_meta( $user_id, $field_id, $field_value );
				}
			} // End section loop
		} // End data loop


		// Check if we have any messages to output
		if ( ! empty( $messages ) ) {
			?>
            <div class="user_profile-notice error">
				<?php
				// Let's loop through the stored messages
				foreach ( $messages as $message ) {
					// Output the message
					echo '<p class="error">' . $message . '</p>';
				}
				?>
            </div>
			<?php
		} else {
			?>
            <div class="user_profile-notice"><p
                        class="updated"><?php esc_html_e( 'Your profile was updated successfully!', 'banana-user-profiles' ); ?></p>
            </div>
			<?php
		}
		?>
		<?php
	}

	// Save password
	function maybe_update_user_password() {

		// Return if the password hasn't been edited
		if ( ! isset( $_POST['password']['user_pass'] ) || strlen( $_POST['password']['user_pass'] ) === 0 ) {
			return;
		}

		// Check the nonce
		if ( ! isset( $_POST['edit-profile-nonce'] ) || ! wp_verify_nonce( $_POST['edit-profile-nonce'], 'user_profile_edit_profile_action' ) ) {
			return;
		}

		$new_password = $_POST['password']['user_pass'];

		// Check that the password matches in both fields
		if ( $_POST['password']['user_pass'] !== $_POST['password']['user_pass_check'] ) {
			wp_cache_set( 'user_profile_profile_notice', __( 'Please make sure the passwords match', 'banana-user-profiles' ) );

			return;
		}

		// The password can now be updated
		wp_set_password( $new_password, get_current_user_id() );

		// Redirect to login page
		wp_redirect(
			add_query_arg(
				[ 'pu' => '1' ],
				get_permalink( get_option( 'user_profile_page_id_for_login' ) )
			)
		);
		exit;

	}

	// Maybe display notice
	public function maybe_display_notice() {

		$notice = wp_cache_get( 'user_profile_profile_notice' );

		if ( $notice ) {
			echo '<div class="user-profile-message">' . esc_html( $notice ) . '</div>';
		}

	}

}