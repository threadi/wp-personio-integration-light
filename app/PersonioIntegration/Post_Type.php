<?php
/**
 * File to handle basic cpt-function.
 *
 * @package personio-integration-light
 */

namespace App\PersonioIntegration;

/**
 * Base Object for each post-type.
 */
class Post_Type {

	/**
	 * Define the post type name.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * Return the post type name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}
}
