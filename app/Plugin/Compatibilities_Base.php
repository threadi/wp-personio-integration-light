<?php
/**
 * File as base for each compatibility-check
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Define the base object for compatibilities.
 */
class Compatibilities_Base {
	/**
	 * Name of this plugin.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * URL of this plugin.
	 *
	 * @var string
	 */
	protected string $plugin_url = '';

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

	/**
	 * Return whether this component is active (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return false;
	}

	/**
	 * Return the plugin URL, if set.
	 *
	 * @return string
	 */
	public function get_plugin_url(): string {
		return $this->plugin_url;
	}
}
