<?php
/**
 * File for handling any post-type we add.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Object to handle positions.
 */
class Post_Types {
	/**
	 * Variable for the instance of this Singleton object.
	 *
	 * @var ?Post_Types
	 */
	protected static ?Post_Types $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
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
	public static function get_instance(): Post_Types {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		// register our post-types.
		$this->register_post_type();
	}

	/**
	 * Register the post-types from a list.
	 *
	 * @return void
	 */
	public function register_post_type(): void {
		foreach ( $this->get_post_types() as $post_type ) {
			// get the class name.
			$class_name = $post_type . '::get_instance';

			// check if it is callable.
			if ( ! is_callable( $class_name ) ) {
				continue;
			}

			// get the object.
			$obj = $class_name();

			// bail if the instance is not our Post_Type.
			if ( ! $obj instanceof Post_Type ) {
				continue;
			}

			// initialize the object.
			$obj->init();
		}
	}

	/**
	 * Return the list of post-types.
	 *
	 * @return array<string>
	 */
	public function get_post_types(): array {
		$post_types = array(
			'\PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition',
		);
		/**
		 * Filter the post-types.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array<string> $post_types List of post-types.
		 */
		return apply_filters( 'personio_position_register_post_type', $post_types );
	}
}
