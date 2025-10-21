<?php
/**
 * File for handling functions for extensions of the position object.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Object for extensions of the position object.
 */
class Position_Extension extends Position {
	/**
	 * Internal extension name for this object.
	 *
	 * @var string
	 */
	protected string $position_extension_name = '';

	/**
	 * Return the name of this object.
	 *
	 * @return string
	 */
	protected function get_extension_name(): string {
		return $this->position_extension_name;
	}

	/**
	 * Return the extension title.
	 *
	 * @return string
	 */
	public function get_extension_title(): string {
		// get the title.
		$title = get_post_meta( $this->get_id(), 'pi_' . $this->get_extension_name() . '_title', true );

		// bail if title could not be loaded.
		if ( ! is_string( $title ) ) {
			return '';
		}

		// return the title.
		return $title;
	}

	/**
	 * Save the extension title for the position.
	 *
	 * @param string $title The title.
	 * @return void
	 */
	public function set_extension_title( string $title ): void {
		update_post_meta( $this->get_id(), 'pi_' . $this->get_extension_name() . '_title', $title );
	}

	/**
	 * Return description.
	 *
	 * @return string
	 */
	public function get_extension_description(): string {
		return get_post_meta( $this->get_id(), 'pi_' . $this->get_extension_name() . '_description', true );
	}

	/**
	 * Save description.
	 *
	 * @param string $description The description.
	 * @return void
	 */
	public function set_extension_description( string $description ): void {
		update_post_meta( $this->get_id(), 'pi_' . $this->get_extension_name() . '_description', $description );
	}

	/**
	 * Return id of chosen image.
	 *
	 * @return int
	 */
	public function get_extension_image_id(): int {
		return absint( get_post_meta( $this->get_id(), 'pi_' . $this->get_extension_name() . '_image', true ) );
	}

	/**
	 * Set id of chosen image.
	 *
	 * @param int $attachment_id ID of the attachment used as image.
	 *
	 * @return void
	 */
	public function set_extension_image_id( int $attachment_id ): void {
		update_post_meta( $this->get_id(), 'pi_' . $this->get_extension_name() . '_image', $attachment_id );
	}
}
