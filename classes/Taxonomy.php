<?php
/**
 * File to handle single taxonomy.
 *
 * @package personio-integration-light
 */

namespace personioIntegration;

/**
 * The object which handles a single taxonomy.
 */
class Taxonomy {

	/**
	 * Instance of this object.
	 *
	 * @var ?Taxonomy
	 */
	private static ?Taxonomy $instance = null;

	/**
	 * Constructor for Init-Handler.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() { }

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Taxonomy {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}
}
