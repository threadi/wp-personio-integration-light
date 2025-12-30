<?php
/**
 * File to handle basic cpt-function.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Base Object for each post-type.
 */
class Post_Type {

	/**
	 * Define the post-type name.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * The parent slug.
	 *
	 * @var string
	 */
	protected string $parent_slug = 'edit.php?post_type=personioposition';

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {}

	/**
	 * Return the post-type name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Return the link to manage items of this cpt in the backend.
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
	 * Mark the active menu if one of our own cpt is called.
	 *
	 * @param bool $use_editor Whether to use editor (true) or not (false).
	 *
	 * @return bool
	 */
	public function mark_menu( bool $use_editor ): bool {
		global $parent_file, $submenu_file;

		$post_id = absint( filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT ) );
		if ( $post_id > 0 && get_post_type( $post_id ) === $this->get_name() ) {
			$parent_file  = $this->get_parent_slug();
			$submenu_file = get_post_type( $post_id );
		}

		// return the initial value.
		return $use_editor;
	}

	/**
	 * Return whether this cpt is assigned to a given plugin.
	 *
	 * @param string $cpt Plugin-path.
	 *
	 * @return bool
	 */
	public function is_from_plugin( string $cpt ): bool {
		return WP_PERSONIO_INTEGRATION_PLUGIN === $cpt;
	}

	/**
	 * Return the archive URL of this post-type.
	 *
	 * @return string
	 */
	public function get_archive_url(): string {
		// get the archive URL.
		$url = get_post_type_archive_link( $this->get_name() );
		if ( ! $url ) {
			$url = '';
		}
		return $url;
	}

	/**
	 * Return the parent slug.
	 *
	 * @return string
	 */
	private function get_parent_slug(): string {
		return $this->parent_slug;
	}
}
