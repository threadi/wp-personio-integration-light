<?php
/**
 * File to validate the PersonioIntegrationURL-setting.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsValidation;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\Personio;
use PersonioIntegrationLight\Plugin\Admin\Settings_Validation_Base;
use PersonioIntegrationLight\Dependencies\easyTransientsForWordPress\Transients;

/**
 * Object which validates the given URL.
 */
class PersonioIntegrationUrl extends Settings_Validation_Base {
	/**
	 * Validate the Personio-URL.
	 *
	 * @param ?string $value The value from the field.
	 *
	 * @return string
	 */
	public static function validate( ?string $value ): string {
		// set value as string if null is given.
		$value = (string) $value;

		// in REST requests validate without settings errors and keep the stored value if the new one is not valid.
		if ( Helper::is_rest_request() ) {
			$value = self::cleanup_url_string( $value );
			if ( ! self::has_size( $value ) || ! empty( self::rest_validate( $value ) ) ) {
				return (string) get_option( 'personioIntegrationUrl', '' );
			}
			return $value;
		}

		// get the transient object.
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
		if ( '' === $value ) {
			add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'The specification of the Personio URL is mandatory.', 'personio-integration-light' ) );
			$error = true;
		}
		if ( self::has_size( $value ) ) {
			$value = self::cleanup_url_string( $value );

			// check if the URL ends with ".jobs.personio.com" or ".jobs.personio.de" with or without "/" on the end.
			if ( ! self::check_personio_in_url( $value ) ) {
				add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'The Personio URL must end with ".jobs.personio.com" or ".jobs.personio.de"!', 'personio-integration-light' ) );
				$error = true;
				$value = '';
			} elseif ( ! self::validate_url( $value ) ) {
				add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'Please enter a valid URL, e.g., https://example.jobs.personio.com. See also the hints below.', 'personio-integration-light' ) );
				$error = true;
				$value = '';
			} elseif ( Helper::get_personio_url() !== $value ) {
				if ( ! self::check_url( $value ) ) {
					$transient_obj = $transients_obj->add();
					$transient_obj->set_name( 'personio_integration_url_not_usable' );
					/* translators: %1$s is replaced with the entered Personio-URL */
					$transient_obj->set_message( \sprintf( __( 'The specified Personio URL %1$s is not usable for this plugin. Please double-check the URL in your Personio-account under Settings > Recruiting > Career Page > Activations. Please also check if the XML interface is enabled there.', 'personio-integration-light' ), esc_url( $value ) ) );
					$transient_obj->set_type( 'error' );
					$transient_obj->save();
					$error = true;
					$value = '';
				}
			}
		}

		// reset transient if the URL is set.
		if ( ! $error ) {
			$transient_obj = $transients_obj->get_transient_by_name( 'personio_integration_no_url_set' );
			$transient_obj->delete();
		}

		// return value if all is ok.
		return $value;
	}

	/**
	 * Check the availability of the given Personio-URL.
	 *
	 * @param string $value The value to check.
	 *
	 * @return bool
	 */
	public static function check_url( string $value ): bool {
		// get Personio-object of the given URL.
		$personio_obj = new Personio( $value );

		// should return HTTP-Status 200.
		$response = wp_safe_remote_get(
			$personio_obj->get_xml_url(),
			array(
				'timeout'             => max( 1, absint( get_option( 'personioIntegrationUrlTimeout', 30 ) ) ),
				'redirection'         => 0,
				'limit_response_size' => 8 * KB_IN_BYTES, // we only check the start of the body.
			)
		);

		// bail on technical errors (DNS, timeout, SSL, connection refused …).
		if ( is_wp_error( $response ) ) {
			Log::get_instance()->add(
			/* translators: %1$s will be replaced by the URL, %2$s by the error message. */
				sprintf( __( 'Personio URL %1$s could not be checked: %2$s', 'personio-integration-light' ), esc_url( $value ), esc_html( $response->get_error_message() ) ),
				'error',
				'import'
			);
			return false;
		}

		// bail if the HTTP status is not 200.
		if ( 200 !== absint( wp_remote_retrieve_response_code( $response ) ) ) {
			return false;
		}

		// bail on empty body or HTML instead of XML (e.g. deactivated XML interface).
		$body = ltrim( wp_remote_retrieve_body( $response ) );
		if ( '' === $body || 0 === stripos( $body, '<!doctype html' ) || 0 === stripos( $body, '<html' ) ) {
			return false;
		}

		// return true as the URL is usable.
		return true;
	}

	/**
	 * Validate the given string from REST API.
	 *
	 * Returns an array with a list of errors.
	 * Returns an empty array if all is ok.
	 *
	 * @param string $value The configured URL.
	 *
	 * @return array<string,string>
	 * @noinspection PhpUnused
	 */
	public static function rest_validate( string $value ): array {
		$value = self::cleanup_url_string( $value );

		// check if the value has size.
		if ( ! self::has_size( $value ) ) {
			// return empty string as we do not mark this as a failure.
			return array();
		}

		// return error as the given string is not a valid URL.
		if ( ! self::validate_url( $value ) ) {
			return array(
				'error' => 'no_url',
				'text'  => __( 'Please enter a valid URL, e.g. https://example.jobs.personio.com. See also the hints below.', 'personio-integration-light' ),
			);
		}

		// return error as the given string is a URL but not for Personio.
		if ( ! self::check_personio_in_url( $value ) ) {
			return array(
				'error' => 'no_personio_url',
				'text'  => __( 'The specified Personio URL is not a Personio-URL. It must end with ".jobs.personio.com" or ".jobs.personio.de".', 'personio-integration-light' ),
			);
		}

		// return error as the given URL is not a usable Personio-URL.
		if ( ! self::check_url( $value ) ) {
			return array(
				'error' => 'url_not_available',
				'text'  => __( 'The specified Personio URL is not usable for this plugin. Please double-check the URL in your Personio-account under Settings > Recruiting > Career Page > Activations. Please also check if the XML interface is enabled there.', 'personio-integration-light' ),
			);
		}

		// return an empty value if no error occurred.
		return array();
	}

	/**
	 * Check if one of the allowed Personio URL is in the given string.
	 *
	 * @param string $value The value to check.
	 *
	 * @return bool
	 */
	public static function check_personio_in_url( string $value ): bool {
		if ( ! \function_exists( 'str_ends_with' ) ) {
			return false;
		}

		$host = wp_parse_url( $value, PHP_URL_HOST );

		// bail if the URL has no parsable host at all.
		if ( ! \is_string( $host ) || '' === $host ) {
			return false;
		}

		return str_ends_with( $host, '.jobs.personio.com' ) || str_ends_with( $host, '.jobs.personio.de' );
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
		// add protocol if this is missing.
		if ( ! empty( $value ) && \function_exists( 'str_contains' ) && ! str_contains( $value, 'https://' ) ) {
			$value = 'https://' . $value;
		}

		// remove the slash on the end of the given url.
		return rtrim( trim( $value ), '/' );
	}
}
