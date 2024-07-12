<?php
/**
 * File to save the PersonioIntegrationURL-setting after its validation.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsSavings;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Positions;

/**
 * Object which saves the validated Personio URL.
 */
class PersonioIntegrationUrl {
	/**
	 * Save the Personio-URL.
	 *
	 * @param string $value The new value of this field.
	 *
	 * @return string
	 */
	public static function save( string $value ): string {
		// get cleaned new value.
		$value = \PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl::cleanup_url_string( $value );

		// trigger re-import hint if URL will be changed.
		if ( ! empty( get_option( 'personioIntegrationUrl' ) ) && get_option( 'personioIntegrationUrl' ) !== $value && ! defined( 'PERSONIO_INTEGRATION_UPDATE_RUNNING' ) && ! defined( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING' ) ) {
			Positions::get_instance()->trigger_reimport_hint();
		}

		// return the cleaned up new value to save it via WP.
		return $value;
	}
}
