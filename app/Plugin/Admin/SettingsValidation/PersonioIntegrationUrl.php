<?php
/**
 * File to validate the PersonioIntegrationURL-setting.
 *
 * @package personio-integration-light
 */

namespace App\Plugin\Admin\SettingsValidation;

use App\Helper;

/**
 * Object which validates the given URL.
 */
class PersonioIntegrationUrl {
	/**
	 * Validate the Personio-URL.
	 *
	 * @param string $value The value from the field.
	 *
	 * @return string
	 */
	public static function validate( string $value ): string {
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
		if ( 0 < strlen( $value ) ) {
			// remove slash on the end of the given url.
			$value = rtrim( $value, '/' );

			// check if URL ends with ".jobs.personio.com" or ".jobs.personio.de" with or without "/" on the end.
			if (
				! (
					str_ends_with( $value, '.jobs.personio.com' )
					|| str_ends_with( $value, '.jobs.personio.de' )
				)
			) {
				add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'The Personio URL must end with ".jobs.personio.com" or ".jobs.personio.de"!', 'personio-integration-light' ) );
				$error = true;
				$value = '';
			} elseif ( ! wp_http_validate_url( $value ) ) {
				add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'Please enter a valid URL.', 'personio-integration-light' ) );
				$error = true;
				$value = '';
			} elseif ( get_option( 'personioIntegrationUrl', '' ) !== $value ) {
				// -> should return HTTP-Status 200
				$response = wp_remote_get(
					Helper::get_personio_xml_url( $value ),
					array(
						'timeout'     => 30,
						'redirection' => 0,
					)
				);
				// get the body with the contents.
				$body = wp_remote_retrieve_body( $response );
				if ( ( is_array( $response ) && ! empty( $response['response']['code'] ) && 200 !== $response['response']['code'] ) || str_starts_with( $body, '<!doctype html>' ) ) {
					// error occurred => show hint.
					set_transient( 'personio_integration_url_not_usable', 1 );
					$error = true;
					$value = '';
				} else {
					// URL is available.
					// -> show hint and option to import the positions now.
					set_transient( 'personio_integration_import_now', 1 );
					// reset options for the import.
					foreach ( Helper::get_active_languages_with_default_first() as $key => $lang ) {
						delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $key );
						delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $key );
					}
				}
			}
		}

		// reset transient if url is set.
		if ( ! $error ) {
			delete_transient( 'personio_integration_no_url_set' );
		}

		// return value if all is ok.
		return $value;
	}
}
