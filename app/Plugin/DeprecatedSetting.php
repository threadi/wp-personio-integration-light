<?php
/**
 * This file defines the deprecated settings usage of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Object, which handles the deprecated settings usage of this plugin.
 */
class DeprecatedSetting {
	/**
	 * Instance of the actual object.
	 *
	 * @var ?DeprecatedSetting
	 */
	private static ?DeprecatedSetting $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
	 */
	public function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return an instance of this object as a singleton.
	 *
	 * @return DeprecatedSetting
	 */
	public static function get_instance(): DeprecatedSetting {
		if ( is_null( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param string $value The parameter used.
	 *
	 * @return void
	 */
	public function set_type( mixed $value ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param string $value The parameter used.
	 *
	 * @return void
	 */
	public function set_default( mixed $value ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param string $value The parameter used.
	 *
	 * @return void
	 */
	public function set_show_in_rest( mixed $value ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param string $value The parameter used.
	 *
	 * @return void
	 */
	public function set_section( mixed $value ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param string $value The parameter used.
	 *
	 * @return void
	 */
	public function add_custom_var( mixed $value ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param string $value The parameter used.
	 *
	 * @return void
	 */
	public function set_field( mixed $value ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param string $value The parameter used.
	 *
	 * @return void
	 */
	public function prevent_export( mixed $value ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param string $value The parameter used.
	 *
	 * @return void
	 */
	public function set_read_callback( mixed $value ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param string $value The parameter used.
	 *
	 * @return void
	 */
	public function set_save_callback( mixed $value ): void {}
}
