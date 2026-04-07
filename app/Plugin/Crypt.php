<?php
/**
 * File with the handler for any crypt tasks in this plugin
 * We use the composer package "Crypt for WordPress" as help for these tasks.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
use CryptForWordPress\Method_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Object to handle any crypt tasks in this plugin.
 */
class Crypt {
	/**
	 * The used crypt object.
	 *
	 * @var \CryptForWordPress\Crypt|null
	 */
	private ?\CryptForWordPress\Crypt $crypt_obj = null;

	/**
	 * Instance of this object.
	 *
	 * @var ?Crypt
	 */
	private static ?Crypt $instance = null;

	/**
	 * Constructor for this object.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Crypt {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Return the crypt object with its configuration for this plugin.
	 *
	 * @return \CryptForWordPress\Crypt
	 */
	private function get_crypt_obj(): \CryptForWordPress\Crypt {
		if ( null === $this->crypt_obj ) {
			// configure the crypt object.
			$this->crypt_obj = new \CryptForWordPress\Crypt( WP_PERSONIO_INTEGRATION_PLUGIN );
			$this->crypt_obj->set_config(
				array(
					'openssl' => array(
						'hash_type'        => 'hash_pbkdf2',
						'hash_algorithm'   => 'sha256',
						'cipher_algorithm' => 'chacha20-poly1305',
					),
					'sodium'  => array(
						'hash_type' => 'sodium_crypto_secretbox_keygen',
					),
				)
			);
		}

		// return the crypt object.
		return $this->crypt_obj;
	}

	/**
	 * Encrypt a given string.
	 *
	 * @param string $plain_text The plain string.
	 *
	 * @return string
	 */
	public function encrypt( string $plain_text ): string {
		return $this->get_crypt_obj()->encrypt( $plain_text );
	}

	/**
	 * Decrypt a given string.
	 *
	 * @param string $encrypted_string The encrypted string.
	 *
	 * @return string
	 */
	public function decrypt( string $encrypted_string ): string {
		return $this->get_crypt_obj()->decrypt( $encrypted_string );
	}

	/**
	 * Return the used method.
	 *
	 * @return Method_Base|false
	 */
	public function get_method(): Method_Base|false {
		return $this->get_crypt_obj()->get_method();
	}
}
