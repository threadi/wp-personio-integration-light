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
	 * @param string $value The new value of this field.
	 *
	 * @return string
	 */
	public static function save( string $value ): string {
		// reset options for the import.
		foreach ( Languages::get_instance()->get_active_languages() as $language_name => $label ) {
			delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $language_name );
			delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $language_name );
		}

		// return the cleaned up new value to save it via WP.
		return \PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl::cleanup_url_string( $value );
	}
}
