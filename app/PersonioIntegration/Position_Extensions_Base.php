<?php
/**
 * File to handle basic settings for Position-extensions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Object to handle extension of positions in Pro-plugin.
 */
class Position_Extensions_Base {
	/**
	 * The ID of the WP_Post of the position.
	 *
	 * @var int
	 */
	private int $id;

	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * Constructor.
	 *
	 * @param int $post_id The ID of the WP_Post of the position.
	 */
	public function __construct( int $post_id ) {
		$this->set_id( $post_id );
	}

	/**
	 * Return the title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return get_post_meta( $this->get_id(), 'pi_' . $this->get_name() . '_title', true );
	}

	/**
	 * Save the title.
	 *
	 * @param string $title The title.
	 * @return void
	 */
	public function set_title( string $title ): void {
		update_post_meta( $this->get_id(), 'pi_' . $this->get_name() . '_title', $title );
	}

	/**
	 * Return description.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return get_post_meta( $this->get_id(), 'pi_' . $this->get_name() . '_description', true );
	}

	/**
	 * Save description.
	 *
	 * @param string $description The description.
	 * @return void
	 */
	public function set_description( string $description ): void {
		update_post_meta( $this->get_id(), 'pi_' . $this->get_name() . '_description', $description );
	}

	/**
	 * Return id of chosen image.
	 *
	 * @return int
	 */
	public function get_image_id(): int {
		return absint( get_post_meta( $this->get_id(), 'pi_' . $this->get_name() . '_image', true ) );
	}

	/**
	 * Set id of chosen image.
	 *
	 * @param int $attachment_id ID of the attachment used as image.
	 *
	 * @return void
	 */
	public function set_image_id( int $attachment_id ): void {
		update_post_meta( $this->get_id(), 'pi_' . $this->get_name() . '_image', $attachment_id );
	}

	/**
	 * Set the ID.
	 *
	 * @param int $post_id The ID of the WP_Post of the position.
	 *
	 * @return void
	 */
	private function set_id( int $post_id ): void {
		$this->id = $post_id;
	}

	/**
	 * Return the ID of the WP_Post of the position.
	 *
	 * @return int
	 */
	protected function get_id(): int {
		return $this->id;
	}

	/**
	 * Return the name of this object.
	 *
	 * @return string
	 */
	protected function get_name(): string {
		return $this->name;
	}
}
