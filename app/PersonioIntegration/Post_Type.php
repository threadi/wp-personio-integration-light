<?php
/**
 * File to handle basic cpt-function.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

	/**
	 * Return the link to manage items of this cpt in backend.
	 *
	 * @param bool $without_admin_url True if the URL should contain get_admin_url().
	 *
	 * @return string
	 */
	public function get_link( bool $without_admin_url = false ): string {
		return add_query_arg(
			array(
				'post_type' => $this->get_name(),
			),
			( $without_admin_url ? '' : get_admin_url() ) . 'edit.php'
		);
	}
}
