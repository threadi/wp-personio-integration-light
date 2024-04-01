<?php
/**
 * File for object of single Personio-account in WordPress.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

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
	 * Return the language-specific XML-URL.
	 *
	 * @param string $language_name The internal name of the language (e.g. "de"), optional.
	 *
	 * @return string
	 */
	public function get_xml_url( string $language_name = '' ): string {
		if ( empty( $language_name ) ) {
			return $this->get_url() . '/xml';
		}
		return $this->get_url() . '/xml?language=' . esc_attr( $language_name );
	}

	/**
	 * Get languages-specific last modified timestamp from previous import.
	 *
	 * @param string $language_name The internal language-name (e.g. "de").
	 *
	 * @return int
	 */
	public function get_timestamp( string $language_name ): int {
		return absint( get_option( 'personioIntegration_xml_lm_timestamp_' . md5( $this->get_url() ) . $language_name ) );
	}

	/**
	 * Set or update languages-specific last modified timestamp.
	 *
	 * @param int    $last_modified_timestamp The timestamp as unit-timestamp.
	 * @param string $language_name The internal language-name (e.g. "de").
	 *
	 * @return void
	 */
	public function set_timestamp( int $last_modified_timestamp, string $language_name ): void {
		update_option( 'personioIntegration_xml_lm_timestamp_' . md5( $this->get_url() ) . $language_name, $last_modified_timestamp );
	}

	/**
	 * Remove languages-specific last modified timestamp.
	 *
	 * @param string $language_name The internal language-name (e.g. "de").
	 *
	 * @return void
	 */
	public function remove_timestamp( string $language_name ): void {
		delete_option( 'personioIntegration_xml_lm_timestamp_' . md5( $this->get_url() ) . $language_name );
	}

	/**
	 * Return md5-hash of language-specific import content from this Personio-account.
	 *
	 * @param string $language_name The internal language-name (e.g. "de").
	 *
	 * @return string
	 */
	public function get_md5( string $language_name ): string {
		return get_option( 'personioIntegration_xml_hash_' . md5( $this->get_url() ) . $language_name, '' );
	}

	/**
	 * Set md5-hash of language-specific import content from this Personio-account.
	 *
	 * @param string $language_name The internal language-name (e.g. "de").
	 * @param string $md5hash The md5-hash to use.
	 *
	 * @return void
	 */
	public function set_md5( string $language_name, string $md5hash ): void {
		update_option( 'personioIntegration_xml_hash_' . md5( $this->get_url() ) . $language_name, $md5hash );
	}

	/**
	 * Remove md5-hash for language-specific import content from this Personio-account.
	 *
	 * @param string $language_name The internal language-name (e.g. "de").
	 *
	 * @return void
	 */
	public function remove_md5( string $language_name ): void {
		delete_option( 'personioIntegration_xml_hash_' . md5( $this->get_url() ) . $language_name );
	}
}
