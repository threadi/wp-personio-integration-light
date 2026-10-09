<?php
/**
 * File to handle Show XML of a single position.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Extensions;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;

/**
 * Handles the XML-code from Personio for a single position.
 */
class Show_Xml extends Position {
	/**
	 * The protected meta key for the XML-code.
	 *
	 * @var string
	 */
	private const META_KEY = '_position_xml';

	/**
	 * The former, unprotected meta key for the XML-code (only read as fallback for existing data).
	 *
	 * @var string
	 */
	private const LEGACY_META_KEY = 'position_xml';

	/**
	 * Return the XML-code of this position.
	 *
	 * @return string
	 */
	public function get_xml(): string {
		$xml = get_post_meta( $this->get_id(), self::META_KEY, true );

		// use the former meta key as fallback for existing data.
		if ( empty( $xml ) ) {
			$xml = get_post_meta( $this->get_id(), self::LEGACY_META_KEY, true );
		}

		// return the XML-code.
		return \is_string( $xml ) ? $xml : '';
	}

	/**
	 * Save the XML on object.
	 *
	 * @param string $xml The XML-code.
	 *
	 * @return void
	 */
	public function set_xml( string $xml ): void {
		// bail if the position has not been saved.
		if ( 0 === $this->get_id() ) {
			return;
		}

		// save the XML-code with the protected meta key.
		update_post_meta( $this->get_id(), self::META_KEY, $xml );

		// remove the former unprotected meta key.
		delete_post_meta( $this->get_id(), self::LEGACY_META_KEY );
	}
}
