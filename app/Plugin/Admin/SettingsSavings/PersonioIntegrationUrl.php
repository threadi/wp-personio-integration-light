<?php
/**
 * File to save the PersonioIntegrationURL-setting after its validation.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsSavings;

// prevent direct access.
use PersonioIntegrationLight\PersonioIntegration\Positions;

defined( 'ABSPATH' ) || exit;

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
		// trigger re-import hint if URL will be changed.
		if ( get_option( 'personioIntegrationUrl' ) !== $value ) {
			Positions::get_instance()->trigger_reimport_hint();
		}

		// return the cleaned up new value to save it via WP.
		return \PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl::cleanup_url_string( $value );
	}
}
