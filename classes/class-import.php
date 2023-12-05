<?php
/**
 * File for handling of imports from Personio.
 *
 * @package personio-integration-light
 */

namespace personioIntegration;

use Exception;
use SimpleXMLElement;

/**
 * Import-handling for positions from Personio.
 */
class Import {

    // Debug-Marker.
    private bool $_debug;

    // Array to collect all errors on import.
    private array $_errors = array();

    /**
     * Log-Object
     *
     * @var Log
     */
    private LOG $_log;

    /**
     * Constructor which starts the import directly.
     *
     * @noinspection PhpUndefinedFunctionInspection
     */
    public function __construct() {
        // get log-object.
        $this->_log = new Log();

        // do not import if it is already running in another process.
        if( 1 === absint(get_option(WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0)) ) {
            return;
        }

        // mark import as running.
        update_option(WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, time());

        // get debug-mode.
        $this->_debug = 1 === absint(get_option('personioIntegration_debug', 0));

        // get the languages.
        $languages = helper::getActiveLanguagesWithDefaultFirst();

        // get the language-count.
        $languageCount = count($languages);

        // set counter for progressbar in backend.
        update_option(WP_PERSONIO_OPTION_MAX, $languageCount);
        update_option(WP_PERSONIO_OPTION_COUNT, 0);
        $doNotUpdateMaxCounter = false;

        // create array for positions.
        $importedPositions = array();

        // check if PersonioUrl is set
        if( !helper::is_personioUrl_set() ) {
            $this->_errors[] = __('Personio URL not configured.', 'personio-integration-light');
        }

        // marker if result should do nothing.
        $doNothing = false;

        // get actual live positions.
        $positions_obj = Positions::get_instance();
        $positions_count = $positions_obj->getPositionsCount();

        // enable xml-error-handling.
        libxml_use_internal_errors(true);

        if( empty($this->_errors) ) {
            // define counter.
            $count = 0;

            // CLI-Output.
            $progress = helper::isCLI() ? \WP_CLI\Utils\make_progress_bar('Get positions from Personio by language', $languageCount) : false;
            foreach( $languages as $key => $enabled ) {
                // define the url.
                $url = apply_filters( 'personio_integration_import_url', helper::get_personio_xml_url(get_option('personioIntegrationUrl', '')).'?language=' . esc_attr($key) );

                // define settings for first request to get the last-modified-date.
                $args = array(
                    'timeout' => get_option('personioIntegrationUrlTimeout', 30),
                    'httpversion' => '1.1',
                    'redirection' => 0
                );
                $response = wp_remote_head($url, $args);

                // check the response and get its http-status and last-modified-date as timestamp.
                $lastModifiedTimestamp = 0;
                $httpStatus = 404;

				if( is_wp_error($response) ) {
					// log possible error.
					$this->_log->addLog( 'Error on request to get Personio timestamp: '.$response->get_error_message(), 'error' );
				}
				elseif( empty($response) ) {
					// log im result is empty.
					$this->_log->addLog( 'Get empty response for Personio timestamp.', 'error' );
				}
                else {
                    // get the http-status to check if call results in acceptable results.
                    $httpStatus = $response["http_response"]->get_status();

                    // get the last modified-timestamp from http-response.
                    $lastModifiedTimestamp = strtotime($response["http_response"]->get_headers()->offsetGet('last-modified'));

					// log timestamp if debug is enabled.
					if( false !== $this->_debug ) {
						$this->_log->addLog( 'Last modified timestamp from Personio: ' . Helper::get_format_date_time( gmdate( 'Y-m-d H:i:s', $lastModifiedTimestamp ) ), 'success' );
					}
                }

                // check if response was with http-status 200, all others are errors.
                if( 200 === apply_filters( 'personio_integration_import_header_status', $httpStatus ) ) {

                    // check if last modified timestamp has been changed.
                    if( false !== $lastModifiedTimestamp && $lastModifiedTimestamp === absint(get_option(WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $key, 0)) && !$this->_debug ) {
                        // timestamp did not change -> do nothing if we already have positions in the DB.
                        if( $positions_count > 0 ) {
                            update_option(WP_PERSONIO_OPTION_COUNT, ++$count);
                            $doNothing = true;
	                        $this->_log->addLog( sprintf( 'No changes in positions for language %s according to the timestamp we get from Personio. No import run.', $key ), 'success' );
                            !$progress ?: $progress->tick();
                            continue;
                        }
                    }

                    // define settings for second request to get the contents.
                    $args = array(
                        'timeout' => get_option('personioIntegrationUrlTimeout', 30),
                        'httpversion' => '1.1',
                        'redirection' => 0
                    );
                    $response = wp_remote_get($url, $args);

					if( is_wp_error($response) ) {
						// log possible error.
						$this->_log->addLog( 'Error on request to get Personio positions: '.$response->get_error_message(), 'error' );
					}
					elseif( empty($response) ) {
						// log im result is empty.
						$this->_log->addLog( 'Get empty response for Personio positions.', 'error' );
					}
					else {
						// get the body with the contents.
						$body = wp_remote_retrieve_body( $response );

						// get the md5-hash of the response.
						$md5hash = md5( $body );

						// check if md5-hash of body content has not been changed.
						if ( $md5hash === get_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $key, '' ) && ! $this->_debug ) {
							// md5-hash did not change -> do nothing if we already have positions in the DB.
							if ( $positions_count > 0 ) {
								update_option( WP_PERSONIO_OPTION_COUNT, ++$count );
								$doNothing = true;
								$this->logSuccess( sprintf( 'No changes in positions for language %s according to the content we get from Personio. No import run.', $key ) );
								! $progress ?: $progress->tick();
								continue;
							}
						}

						// load content via SimpleXML.
						try {
							$positions = simplexml_load_string( $body, 'SimpleXMLElement', LIBXML_NOCDATA );
						} catch ( Exception $e ) {
							/* translators: %1$s will be replaced by the language-name, %2$s by the error-message */
							$this->_errors[] = sprintf( __( "XML file from Personio for language %1$s contains incorrect code and therefore cannot be read in. Technical Error: %2$s", 'personio-integration-light' ), esc_html( $key ), esc_html( $e->getMessage() ) );
							// show progress.
							update_option( WP_PERSONIO_OPTION_COUNT, ++ $count );
							! $progress ?: $progress->tick();
							continue;
						}

						// get xml-errors.
						$xmlErrors = libxml_get_errors();
						if ( ! empty( $xmlErrors ) ) {
							/* translators: %1$s will be replaced by the language-name */
							$this->_errors[] = sprintf( __( "XML file from Personio for language %1$s contains incorrect code and therefore cannot be read in.", 'personio-integration-light' ), esc_html( $key ) );
							continue;
						}

						// disable taxonomy-counting.
						wp_defer_term_counting( true );

						// loop through the results and import each position.
						if ( ! empty( $positions ) ) {
							// log event.
							$this->_log->addLog( sprintf( 'Import of positions for language %1$s starting', esc_html($key) ), 'success' );

							// update max-counter only once per import.
							if ( ! $doNotUpdateMaxCounter ) {
								update_option( WP_PERSONIO_OPTION_MAX, $languageCount * count( $positions ) );
								$doNotUpdateMaxCounter = true;
							}

							// loop through the positions and import them.
							foreach ( $positions as $position ) {
								// add to list for counting.
								$importedPositions[ (int) $position->id ] = $position;

								// run import of position if it is allowed.
								if ( false !== apply_filters( 'personio_integration_import_single_position', true, $position, $key ) ) {
									// import the position.
									$this->importPosition( $position, $key );
								}
								elseif( false !== $this->_debug ) {
									$this->_log->addLog( sprintf( 'Position %1$s has not been imported.', esc_html($position->id) ), 'success' );
								}

								// update counter.
								update_option( WP_PERSONIO_OPTION_COUNT, ++ $count );
							}

							// save the md5-hash of this import-file to prevent reimport.
							update_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $key, $md5hash );

							// save the last-modified-timestamp.
							update_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $key, $lastModifiedTimestamp );

							// wait 1 second for consistent log-view on fast runs.
							sleep(1);

							// log event.
							$this->_log->addLog( sprintf( 'Import of positions for language %1$s ended', esc_html( $key ) ), 'success' );
						}

						// re-enable taxonomy-counting.
						wp_defer_term_counting( false );
					}
                } else {
                    /* translators: %1$s will be replaced by the name of a language, %2$d will be replaced by HTTP-Status (like 404) */
                    $this->_errors[] = sprintf(__('Personio URL for language %1$s not available. Returned HTTP-Status %2$d. Please check the URL you configured and if it is available.', 'personio-integration-light'), esc_html($key), absint($httpStatus));
                }

                // log ok.
                $this->logSuccess(sprintf( "%d positions in language %s imported.", count($importedPositions), $key ) );

                // show progress.
                !$progress ?: $progress->tick();
            }

            // show finished progress.
            !$progress ?: $progress->finish();
        }

        // disable xml-error-handling.
        libxml_use_internal_errors(false);

        if( empty($this->_errors) ) {
            // get Positions-object.
            $positionsObject = Positions::get_instance();

            // delete all not updated positions.
            if( !$doNothing ) {
                foreach( $positionsObject->getPositions() as $position ) {
                    if( false !== apply_filters('personio_integration_delete_single_position', true, $position ) ) {
                        // get post id.
                        $positionPostId = $position->ID;

                        // get personio id.
                        $personioId = $position->getPersonioId();
                        if( 1 === absint(get_post_meta($positionPostId, WP_PERSONIO_INTEGRATION_UPDATED, true)) ) {
                            if( false === delete_post_meta($positionPostId, WP_PERSONIO_INTEGRATION_UPDATED) ) {
	                            // log event.
	                            $this->_log->addLog( sprintf( 'Removing updated flag for %1$s failed.', esc_html( $personioId ) ), 'error' );
                            }
                        } else {
                            // delete this position from database.
                            $result = wp_delete_post($positionPostId, true);

							if( $result instanceof \WP_Post ) {
								// log this event.
								$this->_log->addLog( 'Position ' . $personioId . ' has been deleted as it was not updated during the last import run.', 'success' );
							}
							else {
								// log event.
								$this->_log->addLog( sprintf( 'Removing of not updated positions %1$s failed.', esc_html( $personioId ) ), 'error' );
							}
                        }
                    }
                }
            }

            // run custom actions after import has been done.
            do_action('personio_integration_import_ended');

            // output success-message.
            /** @noinspection PhpUndefinedClassInspection */
            helper::isCLI() ? \WP_CLI::success($languageCount . " languages grabbed, " . count($importedPositions) . " positions imported.") : false;

            // save position count.
            $countPositions = $positionsObject->getPositionsCount();
            update_option('personioIntegrationPositionCount', $countPositions);

            // remove no-import-hin if positions are available in local db.
            if( $countPositions > 0 ) {
                // remove transient with no-import-hint
                delete_transient('personio_integration_no_position_imported');
            }
        }
        else {
            // output error-message.
            /** @noinspection PhpUndefinedClassInspection */
            $this->showErrors();
        }

        // mark import as not running anymore.
        update_option(WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0);
    }

    /**
     * Output all errors as list to cli.
     * Collect the errors for backend-output.
     * Inform admin via email about the problems.
     *
     * @return void
     */
    private function showErrors(): void
    {
        $ausgabe = '';
        foreach( $this->_errors as $e ) {
            $ausgabe .= $e.'\n';
        }
        $ausgabe .= "\n";

        // save results in database.
        $this->_log->addLog($ausgabe, !empty($this->_errors) ? 'error' : 'success');

        // output results in WP-CLI.
        echo (helper::isCLI() ? esc_html($ausgabe) : "");

        // send info to admin about the problem.
        if( !empty($this->_errors) && !$this->_debug ) {
            $sendTo = get_bloginfo('admin_email');
            $subject = get_bloginfo('name') . ": ".__('Error during Personio Positions Import', 'personio-integration-light');
            $msg = __('The following error occurred when importing positions provided by Personio:', 'personio-integration-light').'\r\n' . $ausgabe;
            $msg .= '\r\n\r\n'.__('Sent by the plugin Personio Integration', 'personio-integration-light');
            wp_mail($sendTo, $subject, $msg);
        }
    }

    /**
     * Import single position.
     *
     * @param SimpleXMLElement|null $position
     * @param $key
     * @return void
     * @noinspection PhpUndefinedFieldInspection
     */
    private function importPosition(?SimpleXMLElement $position, $key ): void
    {
        // create position object to handle all values and save them to database.
        $positionObject = new Position(0);
        $positionObject->lang = $key;
        $positionObject->post_title = (string)$position->name;
        $positionObject->post_content = $position->jobDescriptions;
        $positionObject->department = '';
        if( !empty($position->department) ) {
            $positionObject->department = (string)$position->department;
        }
	    $positionObject->keywords = '';
	    if( !empty($position->keywords) ) {
		    $positionObject->keywords = (string)$position->keywords;
	    }
        $positionObject->office = (string)$position->office;
        $positionObject->personioId = (int)$position->id;
        $positionObject->recruitingCategory = (string)$position->recruitingCategory;
        $positionObject->employmentType = (string)$position->employmentType;
        $positionObject->seniority = (string)$position->seniority;
        $positionObject->schedule = (string)$position->schedule;
        $positionObject->yearsOfExperience = (string)$position->yearsOfExperience;
        $positionObject->occupation = (string)$position->occupation;
        $positionObject->occupationCategory = (string)$position->occupationCategory;
        $positionObject->createdAt = (string)$position->createdAt;
        $positionObject = apply_filters('personio_integration_import_single_position_xml', $positionObject, $position);
        $positionObject->save();
    }

    /**
     * Log the successful import.
     *
     * @param string $string
     * @return void
     */
    private function logSuccess(string $string): void
    {
        // save result in database.
        $this->_log->addLog($string, !empty($this->_errors) ? 'error' : 'success');
    }
}
