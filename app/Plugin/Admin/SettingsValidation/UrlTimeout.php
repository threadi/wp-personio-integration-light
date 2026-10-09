<?php
/**
 * File to validate the given timeout.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsValidation;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

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
			add_settings_error( 'personioIntegrationUrlTimeout', 'personioIntegrationUrlTimeout', __( 'A timeout must have a value greater than 0.', 'personio-integration-light' ) );

			// use the actual value or the default value as a timeout of 0 would be unlimited.
			$old_value = absint( get_option( 'personioIntegrationUrlTimeout' ) );
			return $old_value > 0 ? $old_value : 30;
		}
		return $value;
	}
}
