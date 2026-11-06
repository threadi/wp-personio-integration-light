<?php
/**
 * File to get a given value as a decrypted value.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsRead;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Plugin\Crypt;

/**
 * Object, which returns a given encrypted value as a decrypted value.
 */
class GetDecryptValue {
	/**
	 * Generate the crypt value.
	 *
	 * @param string|null $value The value to save.
	 *
	 * @return string|null
	 */
	public static function get( string|null $value ): null|string {
		return self::get_with_context( $value, '' );
	}

	/**
	 * Return the callback to decrypt the value of a setting, bound to a context.
	 *
	 * @param string $context The context, e.g. the name of the setting.
	 *
	 * @return \Closure
	 */
	public static function get_callback( string $context ): \Closure {
		return static function ( $value ) use ( $context ): null|string {
			return self::get_with_context( null === $value || false === $value ? null : (string) $value, $context );
		};
	}

	/**
	 * Return the decrypted value of a value, which has been encrypted with the given context.
	 *
	 * @param string|null $value   The encrypted value.
	 * @param string      $context The context (optional).
	 *
	 * @return string|null
	 */
	public static function get_with_context( string|null $value, string $context ): null|string {
		// bail if the value is empty.
		if ( empty( $value ) ) {
			return '';
		}

		$false = false;
		/**
		 * Do not decrypt a given value if requested.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 *
		 * @param bool $false Return true to prevent decrypting.
		 * @param string $value The requested value.
		 *
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_light_do_not_decrypt', $false, $value ) ) {
			return $value;
		}

		// decrypt the value with its context.
		$plain = Crypt::get_instance()->decrypt( $value, $context );

		// bail if this was successful or no context has been used.
		if ( '' !== $plain || '' === $context ) {
			return $plain;
		}

		// transition: the value has been encrypted before the context existed and has not been converted yet.
		return Crypt::get_instance()->decrypt( $value );
	}
}
