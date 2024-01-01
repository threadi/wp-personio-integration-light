<?php
/**
 * File for handling of imports from single Personio-account.
 *
 * @package personio-integration-light
 */

namespace App\PersonioIntegration;

use App\Helper;
use App\Log;
use App\Plugin\Languages;
use App\Plugin\Transients;
use Exception;
use SimpleXMLElement;
use WP_Post;

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
	 * Constructor which starts the import directly.
	 *
	 * @noinspection PhpUndefinedFunctionInspection
	 */
	public function __construct() {
		// get log-object.
		$this->log = new Log();

		// do not import if it is already running in another process.
		if ( 1 === absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) ) ) {
			return;
		}

		// mark import as running.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, time() );

		// get debug-mode.
		$this->debug = 1 === absint( get_option( 'personioIntegration_debug', 0 ) );

		// get the languages.
		$languages = Languages::get_instance()->get_active_languages();

		// get language-count (will be used multiple times).
		$language_count = count( $languages );

		// set counter for progressbar in backend.
		update_option( WP_PERSONIO_OPTION_MAX, $language_count );
		update_option( WP_PERSONIO_OPTION_COUNT, 0 );
		$do_not_update_max_counter = false;

		// create array for positions.
		$imported_positions = array();

		// check if Personio URL is set.
		if ( ! Helper::is_personio_url_set() ) {
			$this->errors[] = __( 'Personio URL not configured.', 'personio-integration-light' );
		}

		// check if simpleXML exists.
		if ( ! function_exists( 'simplexml_load_string' ) ) {
			$this->errors[] = __( 'The PHP extension simplexml is missing on the system. Please contact your hoster about this.', 'personio-integration-light' );
		}

		// marker if result should do nothing.
		$do_nothing = false;

		// get actual local positions.
		$positions_obj   = Positions::get_instance();
		$positions_count = $positions_obj->get_positions_count();

		// enable xml-error-handling.
		libxml_use_internal_errors( true );

		if ( empty( $this->errors ) ) {
			// define counter.
			$count = 0;

			// CLI-Output.
			$progress = Helper::is_cli() ? \WP_CLI\Utils\make_progress_bar( 'Get positions from Personio by language', $language_count ) : false;
			foreach ( $languages as $language_name => $label ) {
				// get URL.
				$url = Helper::get_personio_xml_url( Helper::get_personio_url() ) . '?language=' . esc_attr( $language_name );

				/**
				 * Change the URL via hook.
				 *
				 * @since 2.5.0 Available since 2.5.0.
				 *
				 * @param string $url The individual text.
				 * @param string $language_name Language-marker.
				 */
				$url = apply_filters( 'personio_integration_import_url', $url, $language_name );

				// define settings for first request to get the last-modified-date.
				$args     = array(
					'timeout'     => get_option( 'personioIntegrationUrlTimeout', 30 ),
					'httpversion' => '1.1',
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
						$this->log->add_log( 'Last modified timestamp from Personio: ' . Helper::get_format_date_time( gmdate( 'Y-m-d H:i:s', $last_modified_timestamp ) ), 'success' );
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
					if ( false !== $last_modified_timestamp && absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $language_name, 0 ) ) === $last_modified_timestamp && ! $this->debug ) {
						// timestamp did not change -> do nothing if we already have positions in the DB.
						if ( $positions_count > 0 ) {
							update_option( WP_PERSONIO_OPTION_COUNT, ++$count );
							$do_nothing = true;
							$this->log->add_log( sprintf( 'No changes in positions for language %s according to the timestamp we get from Personio. No import run.', esc_html($label) ), 'success' );
							$progress ? $progress->tick() : false;
							continue;
						}
					}

					// define settings for second request to get the contents.
					$args     = array(
						'timeout'     => get_option( 'personioIntegrationUrlTimeout', 30 ),
						'httpversion' => '1.1',
						'redirection' => 0,
					);
					$response = wp_remote_get( $url, $args );

					if ( is_wp_error( $response ) ) {
						// log possible error.
						$this->log->add_log( 'Error on request to get Personio positions: ' . $response->get_error_message(), 'error' );
					} elseif ( empty( $response ) ) {
						// log im result is empty.
						$this->log->add_log( 'Get empty response for Personio positions.', 'error' );
					} else {
						// get the body with the contents.
						$body = wp_remote_retrieve_body( $response );

						// get the md5-hash of the response.
						$md5hash = md5( $body );

						// check if md5-hash of body content has not been changed.
						if ( get_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $language_name, '' ) === $md5hash && ! $this->debug ) {
							// md5-hash did not change -> do nothing if we already have positions in the DB.
							if ( $positions_count > 0 ) {
								update_option( WP_PERSONIO_OPTION_COUNT, ++$count );
								$do_nothing = true;
								$this->log->add_log( sprintf( 'No changes in positions for language %1$s according to the content we get from Personio. No import run.', esc_html( $label ) ), 'success' );
								$progress ? $progress->tick() : false;
								continue;
							}
						}

						// load content via SimpleXML.
						try {
							$positions = simplexml_load_string( $body, 'SimpleXMLElement', LIBXML_NOCDATA );
						} catch ( Exception $e ) {
							/* translators: %1$s will be replaced by the language-name, %2$s by the error-message */
							$this->errors[] = sprintf( __( 'XML file from Personio for language %1$s contains incorrect code and therefore cannot be read in. Technical Error: %2$s', 'personio-integration-light' ), esc_html( $language_name ), esc_html( $e->getMessage() ) );
							// show progress.
							update_option( WP_PERSONIO_OPTION_COUNT, ++$count );
							$progress ? $progress->tick() : false;
							continue;
						}

						// get xml-errors.
						$xml_errors = libxml_get_errors();
						if ( ! empty( $xml_errors ) ) {
							/* translators: %1$s will be replaced by the language-name */
							$this->errors[] = sprintf( __( 'XML file from Personio for language %1$s contains incorrect code and therefore cannot be read in.', 'personio-integration-light' ), esc_html( $language_name ) );
							continue;
						}

						// disable taxonomy-counting.
						wp_defer_term_counting( true );

						// loop through the results and import each position.
						if ( ! empty( $positions ) ) {
							// log event.
							$this->log->add_log( sprintf( 'Import of positions for language %1$s starting', esc_html( $label ) ), 'success' );

							// update max-counter only once per import.
							if ( ! $do_not_update_max_counter ) {
								update_option( WP_PERSONIO_OPTION_MAX, $language_count * count( $positions ) );
								$do_not_update_max_counter = true;
							}

							// loop through the positions and import them.
							foreach ( $positions as $position ) {
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
								 * @oaram object $position The XML-object of the Position.
								 * @param string $language_name The language-marker.
								 */
								$run_import = apply_filters( 'personio_integration_import_single_position', $run_import, $position, $language_name );

								// run import of position if it is allowed.
								if ( false !== $run_import ) {
									// import the position.
									$this->import_position( $position, $language_name );
								} elseif ( false !== $this->debug ) {
									$this->log->add_log( sprintf( 'Position %1$s has not been imported.', esc_html( $position->id ) ), 'success' );
								}

								// update counter.
								update_option( WP_PERSONIO_OPTION_COUNT, ++$count );
							}

							// save the md5-hash of this import-file to prevent reimport.
							update_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $language_name, $md5hash );

							// save the last-modified-timestamp.
							update_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $language_name, $last_modified_timestamp );

							// wait 1 second for consistent log-view on fast runs.
							sleep( 1 );

							// log event.
							$this->log->add_log( sprintf( 'Import of positions for language %1$s ended', esc_html( $label ) ), 'success' );
						}

						// re-enable taxonomy-counting.
						wp_defer_term_counting( false );
					}
				} else {
					/* translators: %1$s will be replaced by the name of a language, %2$d will be replaced by HTTP-Status (like 404) */
					$this->errors[] = sprintf( __( 'Personio URL for language %1$s not available. Returned HTTP-Status %2$d. Please check the URL you configured and if it is available.', 'personio-integration-light' ), esc_html( $label ), absint( $http_status ) );
				}

				// log ok.
				$this->log->add_log( sprintf( '%d positions in language %s imported.', count( $imported_positions ), esc_html( $label ) ), 'success' );

				// show progress.
				$progress ? $progress->tick() : false;
			}

			// show finished progress.
			$progress ? $progress->finish() : false;
		}

		// disable xml-error-handling.
		libxml_use_internal_errors( false );

		if ( empty( $this->errors ) ) {
			// get Positions-object.
			$positions_object = Positions::get_instance();

			// delete all not updated positions.
			if ( ! $do_nothing ) {
				foreach ( $positions_object->get_positions() as $position ) {
					$do_delete = true;

					/**
					 * Check if this position should be deleted.
					 *
					 * @since 1.0.0 Available since first release.
					 *
					 * @param bool $do_delete Marker to delete the position.
					 * @param Position $position The position as object.
					 */
					$do_delete = apply_filters( 'personio_integration_delete_single_position', $do_delete, $position );

					if ( false !== $do_delete ) {
						// get personio id.
						$personio_id = $position->get_personio_id();
						if ( 1 === absint( get_post_meta( $position->get_id(), WP_PERSONIO_INTEGRATION_UPDATED, true ) ) ) {
							if ( false === delete_post_meta( $position->get_id(), WP_PERSONIO_INTEGRATION_UPDATED ) ) {
								// log event.
								$this->log->add_log( sprintf( 'Removing updated flag for %1$s failed.', esc_html( $personio_id ) ), 'error' );
							}
						} else {
							// delete this position from database.
							$result = wp_delete_post( $position->get_id(), true );

							if ( $result instanceof WP_Post ) {
								// log this event.
								$this->log->add_log( 'Position ' . $personio_id . ' has been deleted as it was not updated during the last import run.', 'success' );
							} else {
								// log event.
								$this->log->add_log( sprintf( 'Removing of not updated positions %1$s failed.', esc_html( $personio_id ) ), 'error' );
							}
						}
					}
				}
			}

			/**
			 * Run custom actions after import of single Personio-URL has been done.
			 *
			 * @since 2.0.0 Available since release 2.0.0.
			 */
			do_action( 'personio_integration_import_ended' );

			// output success-message.
			Helper::is_cli() ? \WP_CLI::success( $language_count . ' languages grabbed, ' . count( $imported_positions ) . ' positions imported.' ) : false;

			// save position count.
			$count_positions = $positions_object->get_positions_count();
			update_option( 'personioIntegrationPositionCount', $count_positions );

			// remove no-import-hin if positions are available in local db.
			if ( $count_positions > 0 ) {
				// remove transient with no-import-hint.
				Transients::get_instance()->get_transient_by_name( 'personio_integration_no_position_imported' )->delete();
			}
		} else {
			// output error-message.
			$this->show_errors();
		}

		// mark import as not running anymore.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );
	}

	/**
	 * Output all errors as list to cli.
	 * Collect the errors for backend-output.
	 * Inform admin via email about the problems.
	 *
	 * @return void
	 */
	private function show_errors(): void {
		if ( ! empty( $this->errors ) ) {
			$ausgabe = '';
			foreach ( $this->errors as $e ) {
				$ausgabe .= $e . '\n';
			}
			$ausgabe .= "\n";

			// save results in database.
			$this->log->add_log( $ausgabe, 'error' );

			// output results in WP-CLI.
			if ( Helper::is_cli() ) {
				echo esc_html( $ausgabe );
			}

			// send info to admin about the problem if debug is not enabled.
			if ( ! $this->debug ) {
				$send_to = get_bloginfo( 'admin_email' );
				$subject = get_bloginfo( 'name' ) . ': ' . __( 'Error during Import of Personio Positions', 'personio-integration-light' );
				$msg     = __( 'The following error occurred when importing positions provided by Personio:', 'personio-integration-light' ) . '\r\n' . $ausgabe;
				$msg    .= '\r\n\r\n' . __( 'Sent by the plugin Personio Integration', 'personio-integration-light' );
				wp_mail( $send_to, $subject, $msg );
			}
		}
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
		 */
		$position_object = apply_filters( 'personio_integration_import_single_position_xml', $position_object, $position );
		$position_object->save();
	}
}
