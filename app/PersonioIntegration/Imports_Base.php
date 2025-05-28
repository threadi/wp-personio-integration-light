<?php
/**
 * File to handle import extensions for this plugin.
 *
 * @package wp-personio-integration
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Handles import extensions for this plugin.
 */
class Imports_Base extends Extensions_Base {
	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Imports_Base
	 */
	private static ?Imports_Base $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Imports_Base {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Run the import.
	 *
	 * @return void
	 */
	public function run(): void {}
}
