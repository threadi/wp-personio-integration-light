<?php
/**
 * File to validate the PersonioIntegrationURL-setting.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsValidation;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Personio;
use PersonioIntegrationLight\Plugin\Admin\Settings_Validation_Base;
use PersonioIntegrationLight\Plugin\Transients;

/**
 * Object which validates the given URL.
 */
class PersonioIntegrationUrl extends Settings_Validation_Base {
	/**
	 * Validate the Personio-URL.
	 *
	 * @param string $value The value from the field.
	 *
	 * @return string
	 */
	public static function validate( string $value ): string {
		if ( ! Helper::is_admin_api_request() ) {
			$transients_obj = Transients::get_instance();

			$errors = get_settings_errors();
			/**
			 * If a result-entry already exists, do nothing here.
			 *
			 * @see https://core.trac.wordpress.org/ticket/21989
			 */
			if ( Helper::check_if_setting_error_entry_exists_in_array( 'personioIntegrationUrl', $errors ) ) {
				return $value;
			}

			$error = false;
			if ( 0 === strlen( $value ) ) {
				add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'The specification of the Personio URL is mandatory.', 'personio-integration-light' ) );
				$error = true;
			}
			if ( self::has_size( $value ) ) {
				$value = self::cleanup_url_string( $value );

				// check if URL ends with ".jobs.personio.com" or ".jobs.personio.de" with or without "/" on the end.
				if ( ! self::check_personio_in_url( $value ) ) {
					add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'The Personio URL must end with ".jobs.personio.com" or ".jobs.personio.de"!', 'personio-integration-light' ) );
					$error = true;
					$value = '';
				} elseif ( ! self::validate_url( $value ) ) {
					add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'Please enter a valid URL, e.g. https://example.jobs.personio.com. See also the hints below.', 'personio-integration-light' ) );
					$error = true;
					$value = '';
				} elseif ( Helper::get_personio_url() !== $value ) {
					if ( ! self::check_url( $value ) ) {
						$transient_obj = $transients_obj->add();
						$transient_obj->set_name( 'personio_integration_url_not_usable' );
						/* translators: %1$s is replaced with the entered Personio-URL */
						$transient_obj->set_message( sprintf( __( 'The specified Personio URL %1$s is not usable for this plugin. Please double-check the URL in your Personio-account under Settings > Recruiting > Career Page > Activations. Please also check if the XML interface is enabled there.', 'personio-integration-light' ), esc_url( $value ) ) );
						$transient_obj->set_type( 'error' );
						$transient_obj->save();
						$error = true;
						$value = '';
					}
				}
			}

			// reset transient if url is set.
			if ( ! $error ) {
				$transient_obj = $transients_obj->get_transient_by_name( 'personio_integration_no_url_set' );
				$transient_obj->delete();
			}
		}

		// return value if all is ok.
		return $value;
	}

	/**
	 * Check availability of given Personio-URL.
	 *
	 * @param string $value The value to check.
	 *
	 * @return bool
	 */
	public static function check_url( string $value ): bool {
		// get Personio-object of the given URL.
		$personio_obj = new Personio( $value );

		// should return HTTP-Status 200.
		$response = wp_remote_get(
			$personio_obj->get_xml_url(),
			array(
				'timeout'     => get_option( 'personioIntegrationUrlTimeout' ),
				'redirection' => 0,
			)
		);
		// get the body with the contents.
		$body = wp_remote_retrieve_body( $response );

		// return false if URL is not available.
		if ( ( is_array( $response ) && ! empty( $response['response']['code'] ) && 200 !== $response['response']['code'] ) || str_starts_with( $body, '<!doctype html>' ) ) {
			return false;
		}

		// return true if URL is available.
		return true;
	}

	/**
	 * Validate given string from REST API.
	 *
	 * Returns an array with list of errors.
	 * Returns empty array if all is ok.
	 *
	 * @param string $value The configured URL.
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	public static function rest_validate( string $value ): array {
		$value = self::cleanup_url_string( $value );

		// check if value has size.
		if ( ! self::has_size( $value ) ) {
			// return empty string as we do not mark this as failure.
			return array();
		} elseif ( ! self::validate_url( $value ) ) {
			// return error as the given string is not a valid URL.
			return array(
				'error' => 'no_url',
				'text'  => __( 'Please enter a valid URL, e.g. https://example.jobs.personio.com. See also the hints below.', 'personio-integration-light' ),
			);
		} elseif ( ! self::check_personio_in_url( $value ) ) {
			// return error as the given string is a URL but not for Personio.
			return array(
				'error' => 'no_personio_url',
				'text'  => __( 'The specified Personio URL is not a Personio-URL. It must end with ".jobs.personio.com" or ".jobs.personio.de".', 'personio-integration-light' ),
			);
		} elseif ( ! self::check_url( $value ) ) {
			// return error as the given URL is not a usable Personio-URL.
			return array(
				'error' => 'url_not_available',
				'text'  => __( 'The specified Personio URL is not usable for this plugin. Please double-check the URL in your Personio-account under Settings > Recruiting > Career Page > Activations. Please also check if the XML interface is enabled there.', 'personio-integration-light' ),
			);
		}

		// return empty value if no error occurred.
		return array();
	}

	/**
	 * Check if one of the allowed personio-URL is in the given string.
	 *
	 * @param string $value The value to check.
	 *
	 * @return bool
	 */
	public static function check_personio_in_url( string $value ): bool {
		return str_ends_with( $value, '.jobs.personio.com' ) || str_ends_with( $value, '.jobs.personio.de' );
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
		// add protocol if this is missing.
		if ( ! empty( $value ) && ! str_contains( $value, 'https://' ) ) {
			$value = 'https://' . $value;
		}

		// remove slash on the end of the given url.
		return rtrim( trim( $value ), '/' );
	}
}
