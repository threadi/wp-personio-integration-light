<?php
/**
 * File to handle Show XML of a single position.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Extensions;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;

/**
 * Handles the XML-code from Personio for a single position.
 */
class Show_Xml extends Position {
	/**
	 * Return the XML-code of this position.
	 *
	 * @return string
	 */
	public function get_xml(): string {
		return get_post_meta( $this->get_id(), 'position_xml', true );
	}

	/**
	 * Save the XML on object.
	 *
	 * @param string $xml The XML-code.
	 *
	 * @return void
	 */
	public function set_xml( string $xml ): void {
		update_post_meta( $this->get_id(), 'position_xml', $xml );
	}
}
