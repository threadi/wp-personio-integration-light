<?php
/**
 * File to save the PersonioIntegrationURL-setting after its validation.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsSavings;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Plugin\Languages;

/**
 * Object which saves the validated URL.
 */
class PersonioIntegrationUrl {
	/**
	 * Save the Personio-URL.
	 *
	 * TODO noch relevant?
	 *
	 * @param string $value The new value of this field.
	 *
	 * @return string
	 */
	public static function save( string $value ): string {
		// return the cleaned up new value to save it via WP.
		return \PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl::cleanup_url_string( $value );
	}
}
