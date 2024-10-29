<?php

namespace banana\user_profiles;

// This class handles the login, user activation and password recovery features
class Login {

	// Activate class: Setup shortcodes and add some actions and filters
	public static function setup_hooks() {

		$this_class = new self();

		// Set up the shortcode that displays the login form
		add_shortcode( 'login', [ $this_class, 'shortcode_login' ] );

		// Add actions to the init hook
		add_action( 'init', [ $this_class, 'process_login' ] );
		add_action( 'init', [ $this_class, 'process_logout' ] );
		add_action( 'init', [ $this_class, 'process_password_recovery' ] );
		add_action( 'init', [ $this_class, 'process_user_activation' ] );

		// Add filters
		add_filter( 'login_url', [ $this_class, 'filter_login_url' ], 10, 2 );
		add_filter( 'logout_url', [ $this_class, 'filter_logout_url' ], 10, 2 );
		add_filter( 'lostpassword_url', [ $this_class, 'filter_password_recovery_url' ], 10, 2 );

	}


	// Get specific action URL
	public function get_action_url( $action = 'login', $redirect_after_login = '' ) {

		$login_url = $this->login_url();

		switch ( $action ) {
			case 'lp': // Lost Password
				return add_query_arg( [ 'action' => 'lp' ], $login_url );
				break;
			case 'rp': // Reset Password
				return add_query_arg( [ 'action' => 'rp' ], $login_url );
				break;
			case 'lo': // Log-Out
				return wp_nonce_url( add_query_arg( [ 'action' => 'lo' ], $login_url ), 'log-out' );
				break;
			default:
				return $login_url;
		}
	}

	// Get login URL
	public function login_url() {

		$login_page_id = get_option( 'user_profile_page_id_for_login' );

		if ( ! $login_page_id ) {
			return false;
		}

		return get_permalink( $login_page_id );

	}

	// Replace WP native login url with new custom URL
	public function filter_login_url( $url, $redirect ) {
		return $this->login_url();
	}

	// Replace WP native logout url with new custom URL
	public function filter_logout_url( $url, $redirect ) {
		return $this->get_action_url( 'lo', $redirect );
	}

	// Replace WP native password recovery url with new custom URL
	public function filter_password_recovery_url( $url, $redirect ) {
		return $this->get_action_url( 'lp', $redirect );
	}

	// [user_profile_login] shortcode callback
	public function shortcode_login() {

		ob_start();

		if ( is_user_logged_in() ) {
			load_view( 'login-already-logged-in.php' );
		} else {

			$action = isset( $_GET['action'] ) ? sanitize_title( $_GET['action'] ) : 'login';

			// Display content according to action parameter
			switch ( $action ) {

				// Lost Password. Ask user so we may send recovery mail
				case 'lp':
					load_view( 'form-lost-password.php' );
					break;

				// Reset password. User already clicked the recovery link
				case 'rp':
					load_view( 'form-reset-password.php' );
					break;

				// By default, display the login form
				default:
					if ( isset( $_GET['rms'] ) ) { // RMS stands for Recovery Mail Sent
						wp_cache_set( 'user_profile_login_error', __( 'A password recovery link has been sent to your e-mail.', 'banana-user-profiles' ) );
					}
					load_view( 'form-login.php' );
			}

		}

		return ob_get_clean();
	}

	// Process login form
	public function process_login() {

		// Return if the nonce is not set
		if ( ! isset( $_POST['login-nonce'] ) ) {
			return;
		}

		// Nonce exists, let's verify it
		if ( ! wp_verify_nonce( $_POST['login-nonce'], 'user_profile_login_action' ) ) {
			wp_cache_set( 'user_profile_login_error', __( 'Please try again', 'banana-user-profiles' ) );

			return;
		}

		// Args to be passed to wp_signon()
		$signon_args = [];

		// Make sure an email or username has been given
		if ( empty( $_POST['mail-or-user'] ) ) {
			wp_cache_set( 'user_profile_login_error', __( 'Please provide your username or email', 'banana-user-profiles' ) );

			return;
		} else {

			// Get user by mail or username
			$user = ( is_email( $_POST['mail-or-user'] ) ) ? get_user_by( 'email', sanitize_email( $_POST['mail-or-user'] ) ) : get_user_by( 'login', sanitize_user( $_POST['mail-or-user'] ) );

			// Check if we found a user
			if ( $user ) {
				$signon_args['user_login'] = $user->user_login;
			} else {
				wp_cache_set( 'user_profile_login_error', __( 'Wrong credentials', 'banana-user-profiles' ) );

				return;
			}

		}

		// Make sure a password was provided
		if ( empty( $_POST['password'] ) ) {
			wp_cache_set( 'user_profile_login_error', __( 'Please provide your password', 'banana-user-profiles' ) );

			return;
		} else {
			$signon_args['user_password'] = $_POST['password'];
		}

		// Check user activation status
		$user_activation_status = get_user_meta( $user->ID, 'user_profile_user_activation_status', true );

		if ( $user_activation_status == 'pending_activation' ) {
			wp_cache_set( 'user_profile_login_error', __( 'Please, verify your account', 'banana-user-profiles' ) );

			return;
		}

		// Remember login?
		$signon_args['remember'] = isset( $_POST['remember'] );

		// Try to execute signon
		$user = wp_signon( $signon_args, is_ssl() );

		if ( is_wp_error( $user ) ) {
			wp_cache_set( 'user_profile_login_error', __( 'Wrong credentials', 'banana-user-profiles' ) );

			return;
		} else {

			wp_set_current_user( $user->ID );

			// Should we redirect after login?
			if ( isset( $_POST['redirect_after_login'] ) ) {
				$redirect_after_login = sanitize_url( wp_unslash( $_POST['redirect_after_login'] ) );
			} else {
				// If not provided, redirect to profile page by default
				$redirect_after_login = get_permalink( get_option( 'user_profile_page_id_for_show_my_profile' ) );
			}

			wp_redirect( $redirect_after_login );
			exit;
		}

	}

	// Process logout
	public function process_logout() {
		if ( isset( $_GET['action'] ) && 'lo' == $_GET['action'] ) {
			wp_logout();
			wp_redirect( add_query_arg( [ 'lo' => '1' ], $this->login_url() ) );
			exit;
		}
	}

	// Process password recovery
	public function process_password_recovery() {

		if ( ! isset( $_POST['user_profile-password-recovery-step'] ) ) {
			return;
		}

		// Verify nonce
		if ( ! isset( $_POST['nonce-password-recovery'] )
		     || ! wp_verify_nonce( $_POST['nonce-password-recovery'], 'user_profile_password_recovery_action' ) ) {
			wp_cache_set( 'user_profile_login_error', __( 'Please try again.', 'banana-user-profiles' ) );

			return;
		}

		// First step: send recovery email
		if ( 'send-recovery-link' === $_POST['user_profile-password-recovery-step'] ) {

			if ( $this->send_password_recovery_email() ) {
				// RMS stands for Recovery Mail Sent
				wp_redirect( add_query_arg( [ 'rms' => '1' ], $this->login_url() ) );
				exit;
			}

		// Second step: handle password reset
		} elseif ( 'execute-password-reset' === $_POST['user_profile-password-recovery-step'] ) {

			// If required data is set
			if ( isset( $_POST['new-password'] )
			     && isset( $_POST['key'] )
			     && isset( $_POST['login'] ) ) {

				// Check the password reset key
				$user = check_password_reset_key( $_POST['key'], $_POST['login'] );

				if ( is_wp_error( $user ) ) {

					wp_redirect( add_query_arg( [ 'action' => 'lp' ], $this->login_url() ) );
					exit;

				} else {

					if ( empty( $_POST['new-password'] ) ) {
						wp_cache_set( 'user_profile_login_error', __( 'You have to enter your new password', 'banana-user-profiles' ) );

						return;
					}

					// Execute password reset
					$this->update_password_and_send_confirmation_email( $user, $_POST['new-password'] );
					wp_redirect( add_query_arg( [ 'pu' => '1' ], $this->login_url() ) );
					exit;

				}
			}

		}

	}

	// Send password recovery email
	public function send_password_recovery_email() {

		if ( is_email( $_POST['mail-or-user'] ) ) {
			$user = get_user_by( 'email', sanitize_email( wp_unslash( $_POST['mail-or-user'] ) ) );
		} else {
			$user = get_user_by( 'login', sanitize_user( wp_unslash( $_POST['mail-or-user'] ) ) );
		}

		if ( ! $user ) {
			wp_cache_set( 'user_profile_login_error', __( 'No user was found with the provided email or username.', 'banana-user-profiles' ) );

			return false;
		}

		$key = get_password_reset_key( $user );

		$password_recovery_url = add_query_arg(
			[
				'action' => 'rp',
				'key'    => $key,
				'login'  => urlencode( $user->user_login ),
			],
			$this->login_url()
		);

		$password_recovery_message = '
		<p>' . __( 'A password recovery email has been requested', 'banana-user-profiles' ) . '</p>
		<p>' . __( 'To reset your password, click the following link:', 'banana-user-profiles' ) . '</p>
		<p><a href="' . $password_recovery_url . '">' . $password_recovery_url . '</a></p>';
		$password_recovery_subject = __( 'Password recovery', 'banana-user-profiles' );

		wp_mail( $user->user_email, $password_recovery_subject, $password_recovery_message );

		return true;
	}

	// Process user activation
	public function process_user_activation() {

		// Return if the user or activation code are not set
		if ( ( ! isset( $_GET['u'] ) ) or ( ! isset( $_GET['c'] ) ) ) {
			return;
		}

		$user_id = intval( $_GET['u'] );

		// Grab the user activation code from the database
		$the_correct_user_activation_code = get_user_meta( $user_id, 'user_profile_user_activation_code', true );

		if ( ! $the_correct_user_activation_code ) {
			wp_cache_set( 'user_profile_login_error', __( 'This password recovery link is not longer valid.', 'banana-user-profiles' ) );

			return;
		}

		$the_provided_user_activation_code = sanitize_text_field( $_GET['c'] );

		// Check if the provided activation code is right
		if ( $the_correct_user_activation_code !== $the_provided_user_activation_code ) {
			wp_cache_set( 'user_profile_login_error', __( 'This password recovery link is not longer valid', 'banana-user-profiles' ) );

			return;
		}

		// Set user as active
		update_user_meta( $user_id, 'user_profile_user_activation_status', 'is_active' );

		// Delete activation code
		delete_user_meta( $user_id, 'user_profile_user_activation_code' );

		// Redirect to the login URL where the activated message will be shown
		wp_redirect( add_query_arg( [ 'ac' => '1' ], $this->login_url() ) );
		exit;

	}


	// Update password and send confirmation
	public function update_password_and_send_confirmation_email( $user, $new_password ) {

		wp_set_password( $new_password, $user->ID );

		// Send confirmation email
		$password_recovery_message = __( 'Your password has been updated', 'banana-user-profiles' );
		$password_recovery_subject = __( 'Password updated. Now you can login using your new password. Have a nice day!', 'banana-user-profiles' );
		wp_mail( $user->user_email, $password_recovery_subject, $password_recovery_message );

	}

	// Display login notice
	public function maybe_display_error() {

		$notice = wp_cache_get( 'user_profile_login_error' );

		if ( $notice ) {
			echo '<div class="user-profile-login-form__error">' . esc_html( $notice ) . '</div>';
		}

	}

}