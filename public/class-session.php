<?php
/**
 * Session management functionality.
 *
 * Manages user sessions using cookies for tracking conversations,
 * OpenAI thread IDs, and user information submission status.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/public
 */

/**
 * Session management class.
 *
 * Handles session creation, retrieval, and management using cookies
 * to maintain state across page loads for chat conversations.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/public
 */
class Sitecompass_Ai_Session {

	/**
	 * Cookie name for session ID.
	 *
	 * @var string
	 */
	const SESSION_COOKIE = 'sitecompass_session_id';

	/**
	 * Cookie name for thread ID.
	 *
	 * @var string
	 */
	const THREAD_COOKIE = 'sitecompass_thread_id';

	/**
	 * Cookie name for user session ID (user info submitted).
	 *
	 * @var string
	 */
	const USER_SESSION_COOKIE = 'sitecompass_user_session_id';

	/**
	 * Cookie expiration time in seconds (30 days).
	 *
	 * @var int
	 */
	const COOKIE_EXPIRATION = 2592000;

	/**
	 * Generate a unique session ID.
	 *
	 * Creates a unique identifier using uniqid with more entropy
	 * and the current timestamp for additional uniqueness.
	 *
	 * @return string Unique session identifier.
	 */
	public function generate_session_id() {
		return uniqid( 'sitecompass_', true ) . '_' . time();
	}

	/**
	 * Get the current session ID.
	 *
	 * Retrieves the session ID from cookies. If no session exists,
	 * creates a new session ID and sets the cookie.
	 *
	 * @return string Session identifier.
	 */
	public function get_session_id() {
		// Check if session cookie exists.
		if ( isset( $_COOKIE[ self::SESSION_COOKIE ] ) ) {
			return sanitize_text_field( wp_unslash( $_COOKIE[ self::SESSION_COOKIE ] ) );
		}

		// Generate new session ID.
		$session_id = $this->generate_session_id();

		// Set cookie.
		$this->set_cookie( self::SESSION_COOKIE, $session_id );

		return $session_id;
	}

	/**
	 * Get the OpenAI thread ID for the current session.
	 *
	 * Retrieves the thread ID associated with the current session
	 * from cookies.
	 *
	 * @return string|null Thread ID or null if not set.
	 */
	public function get_thread_id() {
		if ( isset( $_COOKIE[ self::THREAD_COOKIE ] ) ) {
			return sanitize_text_field( wp_unslash( $_COOKIE[ self::THREAD_COOKIE ] ) );
		}

		return null;
	}

	/**
	 * Set the OpenAI thread ID for the current session.
	 *
	 * Stores the thread ID in a cookie to maintain the conversation
	 * thread across page loads.
	 *
	 * @param string $thread_id OpenAI thread identifier.
	 * @return bool True on success, false on failure.
	 */
	public function set_thread_id( $thread_id ) {
		if ( empty( $thread_id ) ) {
			return false;
		}

		return $this->set_cookie( self::THREAD_COOKIE, sanitize_text_field( $thread_id ) );
	}

	/**
	 * Check if user information has been submitted.
	 *
	 * Determines whether the user has already submitted their
	 * information (name, email, phone) by checking for the
	 * user session cookie.
	 *
	 * @return bool True if user info submitted, false otherwise.
	 */
	public function is_user_info_submitted() {
		return isset( $_COOKIE[ self::USER_SESSION_COOKIE ] ) && ! empty( $_COOKIE[ self::USER_SESSION_COOKIE ] );
	}

	/**
	 * Mark user information as submitted.
	 *
	 * Sets a cookie to indicate that the user has submitted
	 * their information, preventing the form from showing again.
	 *
	 * @param int $user_id Database user ID.
	 * @return bool True on success, false on failure.
	 */
	public function set_user_info_submitted( $user_id ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		return $this->set_cookie( self::USER_SESSION_COOKIE, absint( $user_id ) );
	}

	/**
	 * Get the user session ID (database user ID).
	 *
	 * Retrieves the database user ID from the user session cookie.
	 *
	 * @return int|null User ID or null if not set.
	 */
	public function get_user_session_id() {
		if ( isset( $_COOKIE[ self::USER_SESSION_COOKIE ] ) ) {
			return absint( $_COOKIE[ self::USER_SESSION_COOKIE ] );
		}

		return null;
	}

	/**
	 * Set a cookie with proper WordPress security.
	 *
	 * Helper method to set cookies with consistent expiration
	 * and security settings.
	 *
	 * @param string $name  Cookie name.
	 * @param string $value Cookie value.
	 * @return bool True on success, false on failure.
	 */
	private function set_cookie( $name, $value ) {
		// Don't set cookies if headers already sent.
		if ( headers_sent() ) {
			return false;
		}

		$expiration = time() + self::COOKIE_EXPIRATION;
		$secure     = is_ssl();
		$httponly   = true;
		$samesite   = 'Lax';

		// Set cookie with security options.
		return setcookie(
			$name,
			$value,
			array(
				'expires'  => $expiration,
				'path'     => COOKIEPATH,
				'domain'   => COOKIE_DOMAIN,
				'secure'   => $secure,
				'httponly' => $httponly,
				'samesite' => $samesite,
			)
		);
	}

	/**
	 * Clear all session cookies.
	 *
	 * Removes all session-related cookies, effectively ending
	 * the current session.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function clear_session() {
		if ( headers_sent() ) {
			return false;
		}

		$cookies = array(
			self::SESSION_COOKIE,
			self::THREAD_COOKIE,
			self::USER_SESSION_COOKIE,
		);

		$success = true;
		foreach ( $cookies as $cookie ) {
			if ( isset( $_COOKIE[ $cookie ] ) ) {
				$result = setcookie(
					$cookie,
					'',
					array(
						'expires' => time() - 3600,
						'path'    => COOKIEPATH,
						'domain'  => COOKIE_DOMAIN,
					)
				);
				if ( ! $result ) {
					$success = false;
				}
			}
		}

		return $success;
	}
}
