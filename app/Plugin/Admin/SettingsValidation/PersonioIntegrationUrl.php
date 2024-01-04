<?php
/**
 * File to validate the PersonioIntegrationURL-setting.
 *
 * @package personio-integration-light
 */

namespace App\Plugin\Admin\SettingsValidation;

use App\Helper;
use App\PersonioIntegration\Personio;
use App\Plugin\Transients;

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

			// delete import hint.
			$transients_obj->get_transient_by_name( 'personio_integration_no_position_imported' )->delete();

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
			} elseif ( Helper::get_personio_url() !== $value ) {
				$personio_obj = new Personio( $value );

				// -> should return HTTP-Status 200
				$response = wp_remote_get(
					$personio_obj->get_xml_url(),
					array(
						'timeout'     => 30,
						'redirection' => 0,
					)
				);
				// get the body with the contents.
				$body = wp_remote_retrieve_body( $response );

				if ( ( is_array( $response ) && ! empty( $response['response']['code'] ) && 200 !== $response['response']['code'] ) || str_starts_with( $body, '<!doctype html>' ) ) {
					// error occurred => show hint.
					$transient_obj = $transients_obj->add();
					$transient_obj->set_name( 'personio_integration_url_not_usable' );
					/* translators: %1$s is replaced with the entered Personio-URL */
					$transient_obj->set_message( sprintf( __( 'The specified Personio URL %1$s is not usable for this plugin. Please double-check the URL in your Personio-account under Settings > Recruiting > Career Page > Activations. Please also check if the XML interface is enabled there.', 'personio-integration-light' ), esc_url( $value ) ) );
					$transient_obj->set_type( 'error' );
					$transient_obj->save();
					$error = true;
					$value = '';
				} else {
					// delete other message.
					Transients::get_instance()->get_transient_by_name( 'personio_integration_no_position_imported' )->delete();

					// reset options for the import.
					foreach ( \App\Plugin\Languages::get_instance()->get_active_languages() as $language_name => $label ) {
						delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $language_name );
						delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $language_name );
					}
				}
			}
		}

		// reset transient if url is set.
		if ( ! $error ) {
			$transient_obj = $transients_obj->get_transient_by_name( 'personio_integration_no_url_set' );
			$transient_obj->delete();
		}

		// return value if all is ok.
		return $value;
	}
}
