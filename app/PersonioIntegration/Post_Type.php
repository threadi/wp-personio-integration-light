<?php
/**
 * File to handle basic cpt-function.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;

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

	/**
	 * Mark active menu if one of our own cpt is called.
	 *
	 * @param bool $use_editor Whether to use editor (true) or not (false).
	 *
	 * @return bool
	 */
	public function mark_menu( bool $use_editor ): bool {
		global $parent_file, $submenu_file;

		$post_id = absint( filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT ) );
		if ( $post_id > 0 && get_post_type( $post_id ) === $this->get_name() ) {
			$parent_file  = 'edit.php?post_type=' . PersonioPosition::get_instance()->get_name();
			$submenu_file = get_post_type( $post_id );
		}

		// return the initial value.
		return $use_editor;
	}

	/**
	 * Return whether this cpt is assigned to given plugin.
	 *
	 * @param string $cpt Plugin-path.
	 *
	 * @return bool
	 */
	public function is_from_plugin( string $cpt ): bool {
		return WP_PERSONIO_INTEGRATION_PLUGIN === $cpt;
	}
}
