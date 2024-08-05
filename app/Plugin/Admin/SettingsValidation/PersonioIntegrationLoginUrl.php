<?php
/**
 * File to validate the PersonioIntegrationLoginURL-setting.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsValidation;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Admin\Settings_Validation_Base;

/**
 * Object which validates the given URL.
 */
class PersonioIntegrationLoginUrl extends Settings_Validation_Base {
	/**
	 * Validate the Personio-URL.
	 *
	 * @param string $value The value from the field.
	 *
	 * @return string
	 */
	public static function validate( string $value ): string {
		if ( ! Helper::is_admin_api_request() ) {
			$errors = get_settings_errors();
			/**
			 * If a result-entry already exists, do nothing here.
			 *
			 * @see https://core.trac.wordpress.org/ticket/21989
			 */
			if ( Helper::check_if_setting_error_entry_exists_in_array( 'personioIntegrationLoginUrl', $errors ) ) {
				return $value;
			}

			// cleanup the given URL.
			$value = self::cleanup_url_string( $value );

			if ( ! empty( $value ) ) {
				// check if URL ends with ".jobs.personio.com" or ".jobs.personio.de" with or without "/" on the end.
				if ( ! self::check_personio_login_url( $value ) ) {
					add_settings_error( 'personioIntegrationLoginUrl', 'personioIntegrationLoginUrl', __( 'The Personio Login URL must end with ".personio.com" or ".personio.de"!', 'personio-integration-light' ) );
					$value = '';
				} elseif ( ! self::validate_url( $value ) ) {
					add_settings_error( 'personioIntegrationLoginUrl', 'personioIntegrationLoginUrl', __( 'Please enter a valid URL for the Personio Login URL.', 'personio-integration-light' ) );
					$value = '';
				}
			}
		}

		// return value if all is ok.
		return $value;
	}

	/**
	 * Check if one of the allowed personio-URL is in the given string.
	 *
	 * @param string $value The value to check.
	 *
	 * @return bool
	 */
	public static function check_personio_login_url( string $value ): bool {
		return ( str_ends_with( $value, '.personio.com' ) || str_ends_with( $value, '.personio.de' ) ) && ! str_contains( $value, '.jobs.personio.' );
	}

	/**
	 * Validate the URL.
	 *
	 * @param string $value The URL-string.
	 *
	 * @return bool
	 */
	public static function validate_url( string $value ): bool {
		return wp_http_validate_url( $value );
	}

	/**
	 * Cleanup URL-string.
	 *
	 * @param string $value The URL-string.
	 *
	 * @return string
	 */
	public static function cleanup_url_string( string $value ): string {
		// remove slash on the end of the given url.
		return rtrim( $value, '/' );
	}
}
