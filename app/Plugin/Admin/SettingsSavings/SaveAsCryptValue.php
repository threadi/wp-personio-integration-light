<?php
/**
 * File to save a given value as an encrypted value.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsSavings;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Plugin\Crypt;

/**
 * Object, which saves an given plain value as encrypt value.
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
		return self::save_with_context( $value, '' );
	}

	/**
	 * Return the callback to encrypt the value of a setting, bound to a context.
	 *
	 * The encrypted value could only be decrypted with the same context. Use the name of the setting as context.
	 *
	 * @param string $context The context, e.g. the name of the setting.
	 *
	 * @return \Closure
	 */
	public static function get_callback( string $context ): \Closure {
		return static function ( $value ) use ( $context ): null|string {
			return self::save_with_context( null === $value ? null : (string) $value, $context );
		};
	}

	/**
	 * Generate the crypt value, bound to the given context.
	 *
	 * @param string|null $value   The value to save.
	 * @param string      $context The context (optional).
	 *
	 * @return string|null
	 */
	public static function save_with_context( #[\SensitiveParameter] string|null $value, string $context ): null|string {
		// bail if the value is empty.
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
		return Crypt::get_instance()->encrypt( $value, $context );
	}
}
