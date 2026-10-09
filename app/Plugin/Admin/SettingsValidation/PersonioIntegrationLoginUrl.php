<?php
/**
 * File to validate the PersonioIntegrationLoginURL-setting.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsValidation;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Admin\Settings_Validation_Base;

/**
 * Object which validates the given URL.
 */
class PersonioIntegrationLoginUrl extends Settings_Validation_Base {
	/**
	 * Validate the Personio-URL.
	 *
	 * @param ?string $value The value from the field.
	 *
	 * @return string
	 */
	public static function validate( ?string $value ): string {
		// set value as string if null is given.
		if ( \is_null( $value ) ) {
			$value = '';
		}

		// in REST requests validate without settings errors and keep the stored value if the new one is not valid.
		if ( Helper::is_rest_request() ) {
			$value = self::cleanup_url_string( $value );

			// an empty value is allowed for this optional setting.
			if ( empty( $value ) ) {
				return '';
			}

			// keep the stored value if the new one is not valid.
			if ( ! self::check_personio_login_url( $value ) || ! self::validate_url( $value ) ) {
				return (string) get_option( 'personioIntegrationLoginUrl', '' );
			}

			// return the normalized URL.
			return self::normalize_url( $value );
		}

		$errors = get_settings_errors();
		/**
		 * If a result-entry already exists, do nothing here.
		 *
		 * @see https://core.trac.wordpress.org/ticket/21989
		 */
		if ( Helper::check_if_setting_error_entry_exists_in_array( 'personioIntegrationLoginUrl', $errors ) ) {
			return $value;
		}

		// clean up the given URL.
		$value = self::cleanup_url_string( $value );

		if ( ! empty( $value ) ) {
			// check if the URL ends with ".personio.com" or ".personio.de" with or without "/" on the end.
			if ( ! self::check_personio_login_url( $value ) ) {
				add_settings_error( 'personioIntegrationLoginUrl', 'personioIntegrationLoginUrl', __( 'The Personio Login URL must end with ".personio.com" or ".personio.de"!', 'personio-integration-light' ) );
				$value = '';
			} elseif ( ! self::validate_url( $value ) ) {
				add_settings_error( 'personioIntegrationLoginUrl', 'personioIntegrationLoginUrl', __( 'Please enter a valid URL for the Personio Login URL.', 'personio-integration-light' ) );
				$value = '';
			} else {
				$value = self::normalize_url( $value );
			}
		}

		// return value if all is ok.
		return $value;
	}

	/**
	 * Check if the given value is a valid Personio login URL.
	 *
	 * Only "https://<account>.personio.com" or "https://<account>.personio.de" is allowed,
	 * without path, query, fragment, port or credentials, and not the public jobs domain.
	 *
	 * @param string $value The value to check.
	 *
	 * @return bool
	 */
	public static function check_personio_login_url( string $value ): bool {
		$parts = wp_parse_url( $value );

		// bail if the URL could not be parsed or has no host.
		if ( ! \is_array( $parts ) || empty( $parts['host'] ) ) {
			return false;
		}

		// only https.
		if ( 'https' !== strtolower( $parts['scheme'] ?? '' ) ) {
			return false;
		}

		// no credentials, port, path, query or fragment: we append our own paths to this URL.
		if ( isset( $parts['user'] ) || isset( $parts['pass'] ) || isset( $parts['port'] ) || isset( $parts['query'] ) || isset( $parts['fragment'] ) ) {
			return false;
		}
		if ( ! empty( $parts['path'] ) && '/' !== $parts['path'] ) {
			return false;
		}

		$host = strtolower( $parts['host'] );

		// the public jobs domain is not the login domain.
		if ( str_contains( $host, '.jobs.personio.' ) ) {
			return false;
		}

		// one or more subdomains (e.g. "company.app.personio.com") below personio.com or personio.de.
		return 1 === preg_match( '/^(?:[a-z0-9-]+\.)+personio\.(?:com|de)$/D', $host );
	}

	/**
	 * Normalize a validated Personio login URL to "https://" and the lowercase host.
	 *
	 * @param string $value The validated URL.
	 *
	 * @return string
	 */
	private static function normalize_url( string $value ): string {
		$host = wp_parse_url( $value, PHP_URL_HOST );

		// bail if no host could be detected.
		if ( ! \is_string( $host ) || '' === $host ) {
			return $value;
		}

		// return the normalized URL.
		return 'https://' . strtolower( $host );
	}

	/**
	 * Validate the URL.
	 *
	 * @param string $value The URL-string.
	 *
	 * @return bool
	 */
	public static function validate_url( string $value ): bool {
		return \is_string( wp_http_validate_url( $value ) );
	}

	/**
	 * Cleanup URL-string.
	 *
	 * @param string $value The URL-string.
	 *
	 * @return string
	 */
	public static function cleanup_url_string( string $value ): string {
		// remove the slash on the end of the given url.
		return rtrim( $value, '/' );
	}
}
