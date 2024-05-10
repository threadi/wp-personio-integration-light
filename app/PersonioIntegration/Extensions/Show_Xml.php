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
use PersonioIntegrationLight\PersonioIntegration\Position_Extensions_Base;

/**
 * Handles the settings multiple files assigned to a single position.
 */
class Show_Xml extends Position_Extensions_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = 'show_xml';

	/**
	 * Return the availability of the Personio page of this position.
	 *
	 * @return string
	 */
	public function get_xml(): string {
		return get_post_meta( $this->get_id(), 'position_xml', true );
	}

	/**
	 * Save the XML on object.
	 *
	 * @param Position $position_obj The object where the XML should be saved.
	 *
	 * @return void
	 */
	public function save( Position $position_obj ): void {
		update_post_meta( $position_obj->get_id(), 'position_xml', $position_obj->get_setting( 'position_xml' ) );
	}

	/**
	 * Set the XML for the position on object.
	 *
	 * @param Position $position The position.
	 * @param string   $xml The XML of the position.
	 *
	 * @return Position
	 */
	public function set_xml( Position $position, string $xml ): Position {
		// add xml as setting on object.
		$position->add_setting( 'position_xml', $xml );

		// return resulting position object.
		return $position;
	}
}
