<?php
/**
 * File for handling of imports from single Personio-account.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use Exception;
use PersonioIntegrationLight\Plugin\Languages;
use SimpleXMLElement;

/**
 * Import-handling for positions from Personio.
 */
class Import {

	/**
	 * Debug-marker.
	 *
	 * @var bool
	 */
	private bool $debug;

	/**
	 * Array to collect all errors on import.
	 *
	 * @var array
	 */
	private array $errors = array();

	/**
	 * Log-Object
	 *
	 * @var Log
	 */
	private Log $log;

	/**
	 * The URL where we import the positions from.
	 *
	 * @var string
	 */
	private string $url;

	/**
	 * Internal name of the language.
	 *
	 * @var string
	 */
	private string $lang;

	/**
	 * List of imported positions.
	 *
	 * @var array
	 */
	private array $imported_postions = array();

	/**
	 * List of positions from XML.
	 *
	 * @var SimpleXMLElement|array
	 */
	private SimpleXMLElement|array $xml_positions = array();

	/**
	 * The Imports object.
	 *
	 * @var ?Imports
	 */
	private ?Imports $imports_obj = null;

	/**
	 * Constructor which runs the import of position for single Personio-account.
	 */
	public function __construct() {
		// get log-object.
		$this->log = new Log();

		// get debug-mode.
		$this->debug = 1 === absint( get_option( 'personioIntegration_debug' ) );
	}

	/**
	 * Import single position.
	 *
	 * @param SimpleXMLElement|null $position The XML-object of a single position.
	 * @param string                $language_name The language-name.
	 * @return void
	 */
	private function import_position( ?SimpleXMLElement $position, string $language_name ): void {
		// create position object to handle all values and save them to database.
		$position_object = new Position( 0 );
		$position_object->set_lang( $language_name );
		$position_object->set_title( (string) $position->name );
		$position_object->set_content( $position->jobDescriptions );
		if ( ! empty( $position->department ) ) {
			$position_object->set_department( (string) $position->department );
		}
		if ( ! empty( $position->keywords ) ) {
			$position_object->set_keywords( (string) $position->keywords );
		}
		$position_object->set_office( (string) $position->office );
		$position_object->set_personio_id( (int) $position->id );
		$position_object->set_recruiting_category( (string) $position->recruitingCategory );
		$position_object->set_employment_type( (string) $position->employmentType );
		$position_object->set_seniority( (string) $position->seniority );
		$position_object->set_schedule( (string) $position->schedule );
		$position_object->set_years_of_experience( (string) $position->yearsOfExperience );
		$position_object->set_occupation( (string) $position->occupation );
		$position_object->set_occupation_category( (string) $position->occupationCategory );
		$position_object->set_created_at( (string) $position->createdAt );
		/**
		 * Change the XML-object before saving the position.
		 *
		 * @since 1.0.0 Available since first release.
		 *
		 * @param Position $position_object The object of this position.
		 * @param object $position The XML-object with the data from Personio.
		 * @param Import $import The Import-object.
		 */
		$position_object = apply_filters( 'personio_integration_import_single_position_xml', $position_object, $position, $this );
		$position_object->save();

		// add position to list.
		$this->imported_postions[] = $position_object;
	}

	/**
	 * Get the URL the import should use for positions.
	 *
	 * @return string
	 */
	public function get_url(): string {
		return $this->url;
	}

	/**
	 * Get HTML-formatted link to the used Personio URL.
	 *
	 * @return string
	 */
	private function get_link(): string {
		return '<a href="'.esc_html( $this->get_url() ) . '" target="_blank">'.esc_html( $this->get_url() ) . '</a>';
	}

	/**
	 * Set the URL the import should use for positions.
	 *
	 * @param string $import_url The URL which will be used to import positions.
	 *
	 * @return void
	 */
	public function set_url( string $import_url ): void {
		$this->url = $import_url;
	}

	/**
	 * Get the language name we use for this import.
	 *
	 * @return string
	 */
	public function get_language_name(): string {
		return $this->lang;
	}

	/**
	 * Get the language title we use for this import.
	 *
	 * @return string
	 */
	public function get_language_title(): string {
		$languages = Languages::get_instance()->get_languages();

		// return 'Fallback English' if language could not be detected.
		if( empty( $languages[$this->get_language_name()]) ) {
			return 'Fallback English';
		}

		// return the title of the language.
		return $languages[$this->get_language_name()];
	}

	/**
	 * Set the language we use for this import.
	 *
	 * @param string $language_name Internal name of the language (e.g. "de").
	 *
	 * @return void
	 */
	public function set_language( string $language_name ): void {
		$this->lang = $language_name;
	}

	/**
	 * Run the import of positions.
	 *
	 * @return void
	 */
	public function run(): void {
		// create array for positions.
		$imported_positions = array();

		// get imports-object to update stats during import.
		$imports_obj = $this->get_imports_object();
		if( !( $imports_obj instanceof Imports ) ) {
			$this->log->add_log( 'Imports-object could not be loaded.', 'error' );
		}

		// get actual local positions.
		$positions_obj   = Positions::get_instance();
		$positions_count = $positions_obj->get_positions_count();

		// enable xml-error-handling.
		libxml_use_internal_errors( true );

		// get language name (e.g. "en").
		$language_name = $this->get_language_name();

		// get Personio-URL-object.
		$personio_obj = new Personio( $this->get_url() );

		// get language-specific URL for XML-API from Personio object.
		$url = $personio_obj->get_xml_url( $language_name );

		/**
		 * Change the URL via hook.
		 *
		 * @since 2.5.0 Available since 2.5.0.
		 *
		 * @param string $url The URL.
		 * @param string $language_name Name of the language.
		 */
		$url = apply_filters( 'personio_integration_import_url', $url, $language_name );

		// define settings for first request to get the last-modified-date.
		$args     = array(
			'timeout'     => get_option( 'personioIntegrationUrlTimeout' ),
			'redirection' => 0,
		);
		$response = wp_remote_head( $url, $args );

		// check the response and get its http-status and last-modified-date as timestamp.
		$last_modified_timestamp = 0;
		$http_status             = 404;

		if ( is_wp_error( $response ) ) {
			// log possible error.
			$this->log->add_log( 'Error on request to get Personio timestamp: ' . $response->get_error_message(), 'error' );
		} elseif ( empty( $response ) ) {
			// log im result is empty.
			$this->log->add_log( 'Get empty response for Personio timestamp.', 'error' );
		} else {
			// get the http-status to check if call results in acceptable results.
			$http_status = $response['http_response']->get_status();

			// get the last modified-timestamp from http-response.
			$last_modified_timestamp = strtotime( $response['http_response']->get_headers()->offsetGet( 'last-modified' ) );

			// log timestamp if debug is enabled.
			if ( false !== $this->debug ) {
				$this->log->add_log( sprintf( 'Last modified timestamp for %1$s from Personio: ', wp_kses_post( $this->get_link() ) ) . Helper::get_format_date_time( gmdate( 'Y-m-d H:i:s', $last_modified_timestamp ) ), 'success' );
			}
		}

		/**
		 * Check if response has been used http-status 200, all others are errors.
		 *
		 * @since 2.5.0 Available since 2.5.0.
		 *
		 * @param int $http_status The returned http-status.
		 */
		$http_status = apply_filters( 'personio_integration_import_header_status', $http_status );
		if ( 200 === $http_status ) {
			// check if last modified timestamp has been changed.
			if ( false !== $last_modified_timestamp && $personio_obj->get_timestamp( $this->get_language_name() ) === $last_modified_timestamp && ! $this->debug ) {
				// timestamp did not change -> do nothing if we already have positions in the DB.
				if ( $positions_count > 0 ) {
					// set import count to actual max to show that it has been run.
					$imports_obj->set_import_count( $imports_obj->get_import_max_count() );
					// log event
					$this->log->add_log( sprintf( 'No changes in positions for language %1$s according to the timestamp we got from Personio account %2$s. No import run.', esc_html( $this->get_language_title() ), wp_kses_post( $this->get_link() ) ), 'success' );
					return;
				}
			}

			// define settings for second request to get the contents.
			$args     = array(
				'timeout'     => get_option( 'personioIntegrationUrlTimeout' ),
				'redirection' => 0,
			);
			$response = wp_remote_get( $url, $args );

			if ( is_wp_error( $response ) ) {
				// log possible error.
				$this->log->add_log( sprintf( 'Error on request to get Personio positions from %1$s: ', wp_kses_post( $this->get_link() ) ) . $response->get_error_message(), 'error' );
			} elseif ( empty( $response ) ) {
				// log im result is empty.
				$this->log->add_log( sprintf( 'Got empty response for Personio positions from %1$s.', wp_kses_post( $this->get_link() ) ), 'error' );
			} else {
				// get the body with the contents.
				$body = wp_remote_retrieve_body( $response );

				// get the md5-hash of the response.
				$md5hash = md5( $body );

				// check if md5-hash of body content has not been changed.
				if ( $personio_obj->get_md5( $language_name ) === $md5hash && ! $this->debug ) {
					// md5-hash did not change -> do nothing if we already have positions in the DB.
					if ( $positions_count > 0 ) {
						$this->log->add_log( sprintf( 'No changes in positions from %1$s for language %2$s according to the content we got from Personio. No import run.', wp_kses_post( $this->get_link() ), esc_html( $this->get_language_title() ) ), 'success' );
						return;
					}
				}

				// load content via SimpleXML.
				try {
					$this->set_xml_positions( simplexml_load_string( $body, 'SimpleXMLElement', LIBXML_NOCDATA ) );
				} catch ( Exception $e ) {
					/* translators: %1$s will be replaced with the Personio account URL, %2$s will be replaced by the language-name, %3$s by the error-message */
					$this->errors[] = sprintf( __( 'XML file from Personio account %1$s for language %2$s contains incorrect code and therefore cannot be read in. Technical Error: %3$s', 'personio-integration-light' ), wp_kses_post( $this->get_link() ), esc_html( $this->get_language_title() ), esc_html( $e->getMessage() ) );
					return;
				}

				// get xml-errors.
				$xml_errors = libxml_get_errors();
				if ( ! empty( $xml_errors ) ) {
					/* translators: %1$s will be replaced with the Personio account URL, %2$s will be replaced by the language-name */
					$this->errors[] = sprintf( __( 'XML file from Personio account %1$s for language %2$s contains incorrect code and therefore cannot be read in.', 'personio-integration-light' ), wp_kses_post( $this->get_link() ), esc_html( $this->get_language_title() ) );
					return;
				}

				// disable taxonomy-counting.
				wp_defer_term_counting( true );

				// loop through the results and import each position.
				if ( $this->has_xml_positions() ) {
					// log event.
					$this->log->add_log( sprintf( 'Import of positions from %1$s for language %2$s starting', wp_kses_post( $this->get_link() ), esc_html( $this->get_language_title() ) ), 'success' );

					// loop through the positions and import them.
					foreach ( $this->get_xml_positions() as $position ) {
						// add to list for counting.
						$imported_positions[ (int) $position->id ] = $position;

						// marker to run import.
						$run_import = true;

						/**
						 * Check the position before import.
						 *
						 * @noinspection PhpConditionAlreadyCheckedInspection
						 *
						 * @since 1.0.0 Available since first release.
						 *
						 * @param bool $run_import The individual text.
						 * @param object $position The XML-object of the Position.
						 * @param string $language_name The language-marker.
						 */
						$run_import = apply_filters( 'personio_integration_import_single_position', $run_import, $position, $language_name );

						// run import of position if it is allowed.
						if ( false !== $run_import ) {
							// import the position.
							$this->import_position( $position, $language_name );
						} elseif ( false !== $this->debug ) {
							$this->log->add_log( sprintf( 'Position %1$s could not been imported from %2$s.', esc_html( $position->id ), wp_kses_post( $this->get_link() ) ), 'success' );
						}

						// update progress.
						$imports_obj->set_import_count( 1 );
					}

					// save the md5-hash of this import-file to prevent reimport.
					$personio_obj->set_md5( $language_name, $md5hash );

					// save the last-modified-timestamp.
					$personio_obj->set_timestamp( $last_modified_timestamp, $this->get_language_name() );

					// wait 1 second for consistent log-view on fast runs with just a view positions.
					if( count($this->get_xml_positions()) < apply_filters( 'personio_integration_import_sleep_positions_limit', 20 ) ) { sleep( 1 ); }

					// log event.
					$this->log->add_log( sprintf( 'Import of positions from Personio account %1$s for language %2$s ended.', wp_kses_post( $this->get_link() ), esc_html( $this->get_language_title() ) ), 'success' );
				}

				// log ok.
				$this->log->add_log( sprintf( '%1$d positions from Personio account %2$s in language %3$s imported.', count( $imported_positions ), wp_kses_post( $this->get_link() ), esc_html( $this->get_language_title() ) ), 'success' );

				// re-enable taxonomy-counting.
				wp_defer_term_counting( false );
			}
		} else {
			/* translators: %1$s will be replaced by the name of a language, %2$d will be replaced by HTTP-Status (like 404) */
			$this->errors[] = sprintf( __( 'Personio URL from Personio account %1$s for language %2$s not available. Returned HTTP-Status %3$d. Please check the URL you configured and if it is available.', 'personio-integration-light' ), wp_kses_post( $this->get_link() ), esc_html( $this->get_language_title() ), absint( $http_status ) );
		}

		// disable xml-error-handling.
		libxml_use_internal_errors( false );
	}

	/**
	 * Return list of errors during this singe import.
	 *
	 * @return array
	 */
	public function get_errors(): array {
		return $this->errors;
	}

	/**
	 * Return list of imported positions.
	 *
	 * @return array
	 */
	public function get_imported_positions(): array {
		return $this->imported_postions;
	}

	/**
	 * Return list of positions from XML.
	 *
	 * @return SimpleXMLElement|array
	 */
	public function get_xml_positions(): SimpleXMLElement|array {
		return $this->xml_positions;
	}

	/**
	 * Set list of positions from XML.
	 *
	 * @param SimpleXMLElement $xml_positions List of positions from XML.
	 *
	 * @return void
	 */
	public function set_xml_positions( SimpleXMLElement $xml_positions ): void {
		$this->xml_positions = $xml_positions;

		// update max counter.
		$imports_obj = $this->get_imports_object();
		if( $imports_obj instanceof Imports ) {
			$imports_obj->set_import_max_count( $imports_obj->get_import_max_count() + count( $this->get_xml_positions() ) );
		}
	}

	/**
	 * Return whether we have XML-positions (true) or not (false).
	 *
	 * @return bool
	 */
	private function has_xml_positions(): bool {
		return ! empty( $this->get_xml_positions() );
	}

	/**
	 * Return the Imports-object for this import of positions.
	 *
	 * @return ?Imports
	 */
	private function get_imports_object(): ?Imports {
		return $this->imports_obj;
	}

	/**
	 * Set Imports-object to update counter on it.
	 *
	 * @param Imports $imports_obj The object for the imports.
	 *
	 * @return void
	 */
	public function set_imports_object( Imports $imports_obj ): void {
		$this->imports_obj = $imports_obj;
	}
}