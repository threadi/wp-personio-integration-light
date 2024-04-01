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
	 * Variable for instance of this Singleton object.
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
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
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
	 * Register the post-types from list.
	 *
	 * @return void
	 */
	public function register_post_type(): void {
		foreach ( $this->get_post_types() as $post_type ) {
			$obj = call_user_func( $post_type . '::get_instance' );
			if ( $obj instanceof Post_Type ) {
				$obj->init();
			}
		}
	}

	/**
	 * Return list of post types.
	 *
	 * @return array
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
		 * @param array $post_types List of post types.
		 */
		return apply_filters( 'personio_position_register_post_type', $post_types );
	}
}
