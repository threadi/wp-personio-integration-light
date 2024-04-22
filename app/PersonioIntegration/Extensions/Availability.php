<?php
/**
 * File to handle availability of a single position.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Extensions;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position_Extensions_Base;

/**
 * Handles the settings multiple files assigned to a single position.
 */
class Availability extends Position_Extensions_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = 'availability';

	/**
	 * Return the availability of the Personio page of this position.
	 *
	 * @return bool
	 */
	public function get_availability(): bool {
		return 1 === absint( get_post_meta( $this->get_id(), 'availability', true ) );
	}

	/**
	 * Set the availability of the Personio page of this position.
	 *
	 * @param bool $availability Must be true if page is available and false if not.
	 *
	 * @return void
	 */
	public function set_availability( bool $availability ): void {
		update_post_meta( $this->get_id(), 'availability', $availability );
	}
}
