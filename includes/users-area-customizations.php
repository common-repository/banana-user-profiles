<?php

namespace banana\user_profiles;

// ACTIONS AND FILTERS

// Add custom fields to the edit user screen
add_action('show_user_profile', 'banana\user_profiles\custom_fields_edit_user_screen'); // My user
add_action('edit_user_profile', 'banana\user_profiles\custom_fields_edit_user_screen'); // Other user

// Handle saving the fields 
add_action('personal_options_update', 'banana\user_profiles\handle_user_custom_fields_save'); // My user
add_action('edit_user_profile_update', 'banana\user_profiles\handle_user_custom_fields_save'); // Other user

// Add custom columns to the user table
add_filter('manage_users_columns', 'banana\user_profiles\add_users_table_columns'); // Create columns
add_filter('manage_users_custom_column', 'banana\user_profiles\populate_users_table_columns', 10, 3); // Populate columns

// ACTIONS AND FILTERS CALLBACKS
function custom_fields_edit_user_screen($user)
{
    ?>
    <table class="form-table">
        <tr>
            <th>
                <label for="user_profile_user_activation_status"><?php _e('Status', 'user_profile'); ?></label>
            </th>
            <td>
                <?php
                $user_activation_status = get_user_meta($user->ID, 'user_profile_user_activation_status', true);
                ?>
                <select name="user_profile_user_activation_status">
                    <option value="pending_activation" <?php echo ($user_activation_status === 'pending_activation') ? 'selected' : ''; ?>><?php _e('Pending', 'user_profile'); ?></option>
                    <option value="is_active" <?php echo ($user_activation_status === 'is_active') ? 'selected' : ''; ?>><?php _e('Active', 'user_profile'); ?></option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

function handle_user_custom_fields_save($user_id)
{
    if (current_user_can('edit_user', $user_id)) {
        update_user_meta($user_id, 'user_profile_user_activation_status', sanitize_text_field($_POST['user_profile_user_activation_status']));
    }
}

function add_users_table_columns($columns)
{
    $columns['user_profile_user_activation_status'] = __('Status', 'user_profile');
    return $columns;
}

function populate_users_table_columns($column_content, $column_name, $user_id)
{
	if ( $column_name === 'user_profile_user_activation_status' ) {
		$user_activation_status = get_user_meta( $user_id, 'user_profile_user_activation_status', true );
		if ( 'is_active' === $user_activation_status ) {
			$status = __( 'Active', 'banana-user-profiles' );
		} elseif ( 'pending_activation' == $user_activation_status ) {
			$status = __( 'Pending', 'banana-user-profiles' );
		} else {
			$status = __( 'Other', 'banana-user-profiles' );
		}

		return $status;
	}

    return $column_content;
}