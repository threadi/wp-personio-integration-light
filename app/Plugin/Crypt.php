<?php
/**
 * File to handle crypt-tasks.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;

/**
 * Object to handle crypt tasks.
 */
class Crypt {
	/**
	 * Define the method for crypt-tasks.
	 *
	 * @var false|Crypt_Base
	 */
	private false|Crypt_Base $method = false;

	/**
	 * Instance of this object.
	 *
	 * @var ?Crypt
	 */
	private static ?Crypt $instance = null;

	/**
	 * Constructor which sets the active method.
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
	 * Return the method object to use for encryption.
	 *
	 * @return false|Crypt_Base
	 */
	public function get_method(): false|Crypt_Base {
		if ( $this->method instanceof Crypt_Base ) {
			return $this->method;
		}

		// loop through the objects to check which one we could use.
		foreach ( $this->get_methods_as_objects() as $obj ) {
			// bail if the method is not usable.
			if ( ! $obj->is_usable() ) {
				continue;
			}

			// initiate the method.
			$obj->init();

			// set method as our method to use.
			$this->method = $obj;

			return $this->method;
		}

		// return false if no usable method has been found.
		return false;
	}

	/**
	 * Return an encrypted string.
	 *
	 * @param string $encrypted_text Text to decrypt.
	 *
	 * @return string
	 */
	public function encrypt( string $encrypted_text ): string {
		// get the active method.
		$method_obj = $this->get_method();

		// bail if the method could not be found.
		if ( false === $method_obj ) {
			return '';
		}

		// encrypt the string with the detected method.
		return $method_obj->encrypt( $encrypted_text );
	}

	/**
	 * Return the decrypted string.
	 *
	 * @param string $encrypted_text Text to decrypt.
	 *
	 * @return string
	 */
	public function decrypt( string $encrypted_text ): string {
		// get the active method.
		$method_obj = $this->get_method();

		// bail if the method could not be found.
		if ( false === $method_obj ) {
			// log this event.
			/* translators: %1$s will be replaced by our support-URL. */
			Log::get_instance()->add( sprintf( __( 'No supported encryption method found! Please contact <a href="%1$s">our support</a> about this problem.', 'personio-integration-light' ), esc_url( Helper::get_plugin_support_url() ) ), 'error', 'system' );
			return '';
		}

		// decrypt the string with the detected method.
		return $method_obj->decrypt( $encrypted_text );
	}

	/**
	 * Return the list of supported methods.
	 *
	 * @return array<int,string>
	 */
	private function get_available_methods(): array {
		$methods = array(
			'PersonioIntegrationLight\Plugin\Crypt\OpenSSL',
			'PersonioIntegrationLight\Plugin\Crypt\Sodium',
		);

		/**
		 * Filter the available crypt-methods.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param array<int,string> $methods List of methods.
		 */
		return apply_filters( 'personio_integration_light_crypt_methods', $methods );
	}

	/**
	 * Return the list of available methods as objects.
	 *
	 * @return array<int,Crypt_Base>
	 */
	private function get_methods_as_objects(): array {
		// define the list for objects.
		$list = array();

		// get all available methods.
		foreach ( $this->get_available_methods() as $method_class_name ) {
			// create the classname.
			$class_name = $method_class_name . '::get_instance';

			// bail if it is not callable.
			if ( ! is_callable( $class_name ) ) {
				continue;
			}

			// get the object.
			$obj = $class_name();

			// bail if the object could not be loaded.
			if ( ! $obj instanceof Crypt_Base ) {
				continue;
			}

			// add the object to the list.
			$list[] = $obj;
		}

		// return the resulting list of objects.
		return $list;
	}

	/**
	 * Run uninstall tasks for crypt.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		foreach ( $this->get_methods_as_objects() as $obj ) {
			$obj->uninstall();
		}
	}
}
