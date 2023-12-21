<?php
/**
 * File to handle multiple taxonomies.
 *
 * @package personio-integration-light
 */

namespace personioIntegration;

/**
 * The object which handles multiple taxonomies.
 */
class Taxonomies {

	/**
	 * Instance of this object.
	 *
	 * @var ?Taxonomies
	 */
	private static ?Taxonomies $instance = null;

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
	public static function get_instance(): Taxonomies {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 *
	 *
	 * @return void
	 */
	public function register_taxonomies(): void {
		foreach( $this->get_taxonomies() as $taxonomy ) {
			var_dump($taxonomy);
		}
	}

	/**
	 * Return the taxonomies this plugin is using.
	 *
	 * @return array
	 */
	private function get_taxonomies(): array {
		return apply_filters(
			'personio_integration_taxonomies',
			array(
				'personioRecruitingCategory',
				'personioOccupationCategory'
			)
		);
	}
}
