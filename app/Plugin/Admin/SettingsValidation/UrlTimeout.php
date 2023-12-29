<?php
/**
 * File to validate the given timeout.
 *
 * @package personio-integration-light
 */

namespace App\Plugin\Admin\SettingsValidation;

/**
 * Object which validates the timeout given.
 */
class UrlTimeout {
	/**
	 * Validate the usage of languages.
	 *
	 * @param int $value Value of setting.
	 * @return int
	 */
	public static function validate( int $value ): int {
		$value = absint( $value );
		if ( 0 === $value ) {
			add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'A timeout must have a value greater than 0.', 'personio-integration-light' ) );
		}
		return $value;
	}
}
