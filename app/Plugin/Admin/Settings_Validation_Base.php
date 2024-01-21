<?php
/**
 * File as base for each validation-object.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin;

/**
 * Base-Object for each validation-object.
 */
class Settings_Validation_Base {

	/**
	 * Check size of given string.
	 *
	 * @param string $value The value.
	 *
	 * @return bool
	 */
	protected static function has_size( string $value ): bool {
		return strlen( $value );
	}
}
