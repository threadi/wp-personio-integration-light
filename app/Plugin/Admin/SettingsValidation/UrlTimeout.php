<?php
/**
 * File to validate the given timeout.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsValidation;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Object which validates the timeout given.
 */
class UrlTimeout {
	/**
	 * Validate the usage of languages.
	 *
	 * @param string|null $value Value of setting.
	 *
	 * @return int
	 */
	public static function validate( string|null $value ): int {
		$value = absint( $value );
		if ( 0 === $value ) {
			add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'A timeout must have a value greater than 0.', 'personio-integration-light' ) );
		}
		return $value;
	}
}
