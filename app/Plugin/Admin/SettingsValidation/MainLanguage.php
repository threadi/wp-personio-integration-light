<?php
/**
 * File to validate the main language-setting.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsValidation;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\Plugin\Admin\Settings_Validation_Base;

/**
 * Object which validates the main language.
 */
class MainLanguage extends Settings_Validation_Base {
	/**
	 * Validate the setting for the main language.
	 *
	 * @param string $value The string of the main language.
	 * @return string
	 */
	public static function validate( string $value ): string {
		if ( ! Helper::is_admin_api_request() ) {
			if ( ! self::has_size( $value ) ) {
				add_settings_error( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, __( 'No main language was specified. The specification of a main language is mandatory.', 'personio-integration-light' ) );
				$value = \PersonioIntegrationLight\Plugin\Languages::get_instance()->get_main_language();
			} elseif ( ! self::check_language( $value ) ) {
				add_settings_error( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, __( 'The selected main language is not activated as a language.', 'personio-integration-light' ) );
				$value = \PersonioIntegrationLight\Plugin\Languages::get_instance()->get_main_language();
			}

			// trigger re-import hint if setting will be changed.
			if ( get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE ) !== $value && ! defined( 'PERSONIO_INTEGRATION_UPDATE_RUNNING' ) && ! defined( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING' ) ) {
				Positions::get_instance()->trigger_reimport_hint();
			}
		}
		return $value;
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
		// check the given string size.
		if ( ! self::has_size( $value ) ) {
			return array(
				'error' => 'no_size',
			);
		} elseif ( ! self::check_language( $value ) ) {
			// return error if language is not available.
			return array(
				'error' => 'language_not_available',
			);
		}

		// return empty array if all is ok.
		return array();
	}

	/**
	 * Check if given language is available.
	 *
	 * @param string $language_string The requested language-name (e.g. "en").
	 *
	 * @return bool
	 */
	private static function check_language( string $language_string ): bool {
		$languages = \PersonioIntegrationLight\Plugin\Languages::get_instance()->get_languages();
		if ( empty( $languages ) ) {
			return false;
		}
		if ( empty( $languages[ $language_string ] ) ) {
			return false;
		}
		return true;
	}
}
