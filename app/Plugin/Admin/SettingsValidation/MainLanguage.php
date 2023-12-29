<?php
/**
 * File to validate the main language-setting.
 *
 * @package personio-integration-light
 */

namespace App\Plugin\Admin\SettingsValidation;

/**
 * Object which validates the main language.
 */
class MainLanguage {
	/**
	 * Validate the setting for the main language.
	 *
	 * @param string $value The string of the main language.
	 * @return string
	 */
	public static function validate( string $value ): string {
		if ( 0 === strlen( $value ) ) {
			add_settings_error( 'personioIntegrationMainLanguage', 'personioIntegrationMainLanguage', __( 'No main language was specified. The specification of a main language is mandatory.', 'personio-integration-light' ) );
			$value = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE );
		} elseif ( empty( WP_PERSONIO_INTEGRATION_LANGUAGES[ $value ] ) ) {
			add_settings_error( 'personioIntegrationMainLanguage', 'personioIntegrationMainLanguage', __( 'The selected main language is not activated as a language.', 'personio-integration-light' ) );
			$value = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE );
		}
		return $value;
	}
}
