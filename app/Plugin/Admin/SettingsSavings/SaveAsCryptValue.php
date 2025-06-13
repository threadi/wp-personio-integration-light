<?php
/**
 * File to save a given value as crypt value.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsSavings;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Plugin\Crypt;

/**
 * Object which saves crypt values.
 */
class SaveAsCryptValue {
	/**
	 * Generate the crypt value.
	 *
	 * @param string|null $value The value to save.
	 *
	 * @return string|null
	 */
	public static function save( string|null $value ): null|string {
		// bail if value is empty.
		if ( empty( $value ) ) {
			return '';
		}

		$false = false;
		/**
		 * Do not encrypt a given value if requested.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 *
		 * @param bool $false Return true to prevent decrypting.
		 *
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_light_do_not_encrypt', $false ) ) {
			return $value;
		}

		// return the new value to save it via WP.
		return Crypt::get_instance()->encrypt( $value );
	}
}
