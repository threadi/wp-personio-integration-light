<?php
/**
 * File for handling of imports from single Personio-account.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

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
		return '<a href="' . esc_url( $this->get_url() ) . '" target="_blank">' . esc_html( $this->get_url() ) . '</a>';
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
	 * Get the language key we use for this import.
	 *
	 * @return string
	 */
	public function get_language(): string {
		return $this->lang;
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
		// get imports-object to update stats during import.
		$imports_obj = $this->get_imports_object();
		if ( ! ( $imports_obj instanceof Imports ) ) {
			$this->log->add_log( 'Imports-object could not be loaded.', 'error', 'import' );
		}

		// get actual local positions.
		$positions_obj   = Positions::get_instance();
		$positions_count = $positions_obj->get_positions_count();

		// enable xml-error-handling.
		libxml_use_internal_errors( true );

		// get language name (e.g. "en").
		$language_name  = $this->get_language();
		$language_title = Languages::get_instance()->get_language_title( $language_name );

		// get Personio-URL-object.
		$personio_obj = new Personio( $this->get_url() );

		// get language-specific URL for XML-API from Personio object.
		$url = $personio_obj->get_xml_url( $language_name );

		/**
		 * Run action on start of import of single URL.
		 *
		 * @since 3.0.5 Available since 3.0.5
		 * @param Import $this The import-object.
		 */
		do_action( 'personio_integration_import_of_url_starting', $this );

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
			$this->log->add_log( 'Error on request to get Personio timestamp: ' . $response->get_error_message(), 'error', 'import' );
		} elseif ( empty( $response ) ) {
			// log im result is empty.
			$this->log->add_log( 'Get empty response for Personio timestamp.', 'error', 'import' );
		} else {
			// get the http-status to check if call results in acceptable results.
			$http_status = $response['http_response']->get_status();

			// get the last modified-timestamp from http-response.
			$last_modified_timestamp = $response['http_response']->get_headers()->offsetGet( 'last-modified' );

			// log timestamp if debug is enabled.
			if ( ! is_null( $last_modified_timestamp ) && false !== $this->debug ) {
				/* translators: %1$s will be replaced by the Personio URL. */
				$this->log->add_log( sprintf( __( 'Last modified timestamp for %1$s from Personio: ', 'personio-integration-light' ), wp_kses_post( $this->get_link() ) ) . Helper::get_format_date_time( gmdate( 'Y-m-d H:i:s', strtotime( $last_modified_timestamp ) ) ), 'success', 'import' );
			}

			// if timestamp and xml api are not available set 404 as state.
			if ( is_null( $last_modified_timestamp ) ) {
				$http_status = 404;
			} else {
				$last_modified_timestamp = strtotime( $last_modified_timestamp );
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
			if ( false !== $last_modified_timestamp && $personio_obj->get_timestamp( $this->get_language() ) === $last_modified_timestamp && ! $this->debug ) {
				// timestamp did not change -> do nothing if we already have positions in the DB.
				if ( $positions_count > 0 ) {
					// set import count to actual max to show that it has been run.
					$imports_obj->set_import_count( $imports_obj->get_import_max_count() );
					// log event.
					/* translators: %1$s will be replaced by the language title. */
					$this->log->add_log( sprintf( __( 'No changes in positions for language %1$s according to the timestamp we got from Personio account %2$s. Timestamp: %3$s. No import run.', 'personio-integration-light' ), esc_html( $language_title ), wp_kses_post( $this->get_link() ), esc_html( Helper::get_format_date_time( gmdate( 'Y-m-d H:i:s', $last_modified_timestamp ) ) ) ), 'success', 'import' );

					/**
					 * Run actions for this case.
					 *
					 * @since 3.0.4 Available since 3.0.4.
					 *
					 * @param Import $this The import-object.
					 * @param int $last_modified_timestamp The timestamp.
					 */
					do_action( 'personio_integration_import_timestamp_no_changed', $this, $last_modified_timestamp );
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
				$this->log->add_log( sprintf( 'Error on request to get Personio positions from %1$s: ', wp_kses_post( $this->get_link() ) ) . $response->get_error_message(), 'error', 'import' );
			} elseif ( empty( $response ) ) {
				// log im result is empty.
				$this->log->add_log( sprintf( 'Got empty response for Personio positions from %1$s.', wp_kses_post( $this->get_link() ) ), 'error', 'import' );
			} else {
				// get the body with the contents.
				$body = wp_remote_retrieve_body( $response );

				// get the md5-hash of the response.
				$md5hash = md5( $body );

				// check if md5-hash of body content has not been changed.
				if ( $personio_obj->get_md5( $language_name ) === $md5hash && ! $this->debug ) {
					// md5-hash did not change -> do nothing if we already have positions in the DB.
					if ( $positions_count > 0 ) {
						// log event.
						$this->log->add_log( sprintf( 'No changes in positions from %1$s for language %2$s according to the content we got from Personio. No import run.', wp_kses_post( $this->get_link() ), esc_html( $language_title ) ), 'success', 'import' );

						/**
						 * Run actions for this case.
						 *
						 * @since 3.0.4 Available since 3.0.4.
						 *
						 * @param Import $this The import-object.
						 * @param string $md5hash The md5-hash from body.
						 */
						do_action( 'personio_integration_import_content_not_changed', $this, $md5hash );
						return;
					}
				}

				// load content via SimpleXML.
				try {
					$this->set_xml_positions( simplexml_load_string( $body, 'SimpleXMLElement', LIBXML_NOCDATA ) );
				} catch ( Exception $e ) {
					/* translators: %1$s will be replaced with the Personio account URL, %2$s will be replaced by the language-name, %3$s by the error-message */
					$this->errors[] = sprintf( __( 'XML file from Personio account %1$s for language %2$s contains incorrect code and therefore cannot be read in. Technical Error: %3$s', 'personio-integration-light' ), wp_kses_post( $this->get_link() ), esc_html( $language_title ), esc_html( $e->getMessage() ) );
					return;
				}

				// get xml-errors.
				$xml_errors = libxml_get_errors();
				if ( ! empty( $xml_errors ) ) {
					/* translators: %1$s will be replaced with the Personio account URL, %2$s will be replaced by the language-name */
					$this->errors[] = sprintf( __( 'XML file from Personio account %1$s for language %2$s contains incorrect code and therefore cannot be read in.', 'personio-integration-light' ), wp_kses_post( $this->get_link() ), esc_html( $language_title ) );
					return;
				}

				// disable taxonomy-counting.
				wp_defer_term_counting( true );

				// loop through the results and import each position.
				if ( $this->has_xml_positions() ) {
					// log event.
					/* translators: %1$s will be replaced by the PersonioId, %2$s by the language title. */
					$this->log->add_log( sprintf( __( 'Import of positions from %1$s for language %2$s starting.', 'personio-integration-light' ), wp_kses_post( $this->get_link() ), esc_html( $language_title ) ), 'success', 'import' );

					// loop through the positions and import them.
					foreach ( $this->get_xml_positions() as $position ) {
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
						 * @param Personio $personio_obj The used Personio-account-object.
						 */
						if ( false !== apply_filters( 'personio_integration_import_single_position', $run_import, $position, $language_name, $personio_obj ) ) {
							// import the position and add it to the list.
							$this->imported_postions[] = $this->get_imports_object()->import_single_position( $position, $language_name, $personio_obj->get_url() );
						} elseif ( false !== $this->debug ) {
							$this->log->add_log( sprintf( 'Position %1$s has not been imported from %2$s.', esc_html( $position->id ), wp_kses_post( $this->get_link() ) ), 'success', 'import' );
						}

						// update progress.
						$imports_obj->set_import_count( 1 );
					}

					// save the md5-hash of this import-file to prevent reimport.
					$personio_obj->set_md5( $language_name, $md5hash );

					// save the last-modified-timestamp.
					$personio_obj->set_timestamp( $last_modified_timestamp, $this->get_language() );

					// wait 1 second for consistent log-view on fast runs with just a view positions.
					if ( count( $this->get_xml_positions() ) < apply_filters( 'personio_integration_import_sleep_positions_limit', 20 ) ) {
						sleep( 1 );
					}

					// log event.
					/* translators: %1$s will be replaced by the Personio account URL, %2$s by the language title. */
					$this->log->add_log( sprintf( __( 'Import of positions from Personio account %1$s for language %2$s ended.', 'personio-integration-light' ), wp_kses_post( $this->get_link() ), esc_html( $language_title ) ), 'success', 'import' );
				}

				// log ok.
				/* translators: %1$d will be replaced by a number, %2$s by the Personio account URL and %3$s by the language title. */
				$this->log->add_log( sprintf( __( '%1$d positions from Personio account %2$s in language %3$s imported.', 'personio-integration-light' ), count( $this->imported_postions ), wp_kses_post( $this->get_link() ), esc_html( $language_title ) ), 'success', 'import' );

				// re-enable taxonomy-counting.
				wp_defer_term_counting( false );
			}
		} else {
			// get the url for the log.
			$log_url = add_query_arg( array( 'category' => 'import' ), Helper::get_settings_url( 'personioPositions', 'logs' ) );

			/* translators: %1$s will be replaced by the name of a language, %2$d will be replaced by the name of the language used for import. */
			$this->errors[] = sprintf( __( 'Personio URL from Personio account %1$s for language %2$s not available.', 'personio-integration-light' ), wp_kses_post( $this->get_link() ), esc_html( $language_title ) );
			/* translators: %1$d will be replaced by HTTP-Status (like 404). */
			$this->errors[] = sprintf( __( 'Returned HTTP-Status %1$d.', 'personio-integration-light' ), absint( $http_status ) );
			$this->errors[] = __( 'Please check the configured URL and if it is available.', 'personio-integration-light' );
			/* translators: %1$s will be replaced the url for the personio account login */
			$this->errors[] = sprintf( __( 'Please also check if the XML-API is enabled in <a href="%1$s" target="_blank">your Personio account (opens new window)</a> under Settings > Recruiting > Career Page > Activations.', 'personio-integration-light' ), esc_url( Helper::get_personio_login_url() ) );
			/* translators: %1$s will be replaced the url for the personio account login */
			$this->errors[] = sprintf( __( 'And please check <a href="%1$s" target="_blank">the log (opens new window)</a> in your WordPress-backend under Positions > Settings > Logs.', 'personio-integration-light' ), esc_url( $log_url ) );
		}

		/**
		 * Run action on end of import of single URL.
		 *
		 * @since 3.0.5 Available since 3.0.5
		 * @param Import $this The import-object.
		 */
		do_action( 'personio_integration_import_of_url_ended', $this );

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
		if ( $imports_obj instanceof Imports ) {
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
