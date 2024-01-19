<?php
/**
 * File for object of single Personio-account in WordPress.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Plugin\Languages;

/**
 * Object for single Personio-account in WordPress.
 */
class Personio {

	/**
	 * The URL of the account.
	 *
	 * @var string
	 */
	private string $url;

	/**
	 * Constructor for this object.
	 *
	 * @param string $url The URL of the public page of the account on Personio.
	 */
	public function __construct( string $url ) {
		$this->url = $url;
	}

	/**
	 * Return the URL.
	 *
	 * @return string
	 */
	public function get_url(): string {
		return $this->url;
	}

	/**
	 * Get generated Personio-application-URL.
	 *
	 * @param Position $position_obj The Position-object.
	 * @param bool     $without_application Whether the link should be generated with form-#hashtag.
	 * @return string
	 */
	public function get_application_url( Position $position_obj, bool $without_application = false ): string {
		// get actual language.
		$language_name = Languages::get_instance()->get_current_lang();

		// create the target-url.
		$url = $this->get_url() . '/job/' . absint( $position_obj->get_personio_id() ) . '?display=' . $language_name;

		// return without application-hash.
		if ( $without_application ) {
			return $url;
		}

		// return with application-hash.
		return $url . '#apply';
	}

	/**
	 * Return the XML-URL.
	 *
	 * @return string
	 */
	public function get_xml_url(): string {
		return $this->get_url() . '/xml';
	}
}
