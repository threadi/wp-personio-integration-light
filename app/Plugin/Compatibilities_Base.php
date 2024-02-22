<?php
/**
 * File as base for each compatibility-check
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define the base object for compatibilities.
 */
class Compatibilities_Base {
	/**
	 * Name of this event.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * Instance of this object.
	 *
	 * @var ?Compatibilities_Base
	 */
	private static ?Compatibilities_Base $instance = null;

	/**
	 * Constructor for Init-Handler.
	 */
	private function __construct() {
	}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Compatibilities_Base {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Run the check.
	 *
	 * @return void
	 */
	public function check(): void {}

	/**
	 * Return the name of the object.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}
}
