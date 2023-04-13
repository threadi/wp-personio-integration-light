<?php

namespace personioIntegration;

use Exception;
use SimpleXMLElement;

/**
 * Import-handling for positions from Personio.
 */
class Import {

    // Debug-Marker
    private bool $_debug;

    // Array to collect all errors on import
    private array $_errors = [];

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
        // get log-object
        $this->_log = new Log();

        // do not import if it is already running in another process
        if( get_option(WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0) == 1 ) {
            return;
        }

        // mark import as running
        update_option(WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, time());

        // get debug-mode
        $this->_debug = get_option('personioIntegration_debug', 0) == 1;

        // get the languages
        $languages = helper::getActiveLanguagesWithDefaultFirst();

        // get the language-count
        $languageCount = count($languages);

        // set counter for progressbar in backend
        update_option(WP_PERSONIO_OPTION_MAX, $languageCount);
        update_option(WP_PERSONIO_OPTION_COUNT, 0);
        $doNotUpdateMaxCounter = false;

        // create array for positions
        $importedPositions = [];

        // check if PersonioUrl is set
        if( !helper::is_personioUrl_set() ) {
            $this->_errors[] = __('Personio URL not configured.', 'wp-personio-integration');
        }

        // marker if result should do nothing
        $doNothing = false;

        // enable xml-error-handling
        libxml_use_internal_errors(true);

        if( empty($this->_errors) ) {
            // get the Personio URL
            $domain = get_option('personioIntegrationUrl');

            // define counter
            $count = 0;

            // CLI-Output
            $progress = helper::isCLI() ? \WP_CLI\Utils\make_progress_bar('Get positions from Personio by language', $languageCount) : false;
            foreach( $languages as $key => $enabled ) {
                // define the url
                $url = $domain . "/xml?language=" . esc_attr($key);

                // define settings for first request to get the last-modified-date
                $args = [
                    'timeout' => get_option('personioIntegrationUrlTimeout', 30),
                    'httpversion' => '1.1',
                    'redirection' => 0
                ];
                $response = wp_remote_head($url, $args);

                // check the response and get its http-status and last-modified-date as timestamp
                $lastModifiedTimestamp = 0;
                $httpStatus = 500;
                if( !is_wp_error($response) && !empty($response) ) {
                    // get the http-status to check if call results in acceptable results
                    $httpStatus = $response["http_response"]->get_status();

                    // get the last modified-timestamp from http-response
                    $lastModifiedTimestamp = strtotime($response["http_response"]->get_headers()->offsetGet('last-modified'));
                }

                // check if response was with http-status 200
                // and also 301 in debug-mode
                if ($httpStatus === 200) {

                    // check if last modified timestamp has been changed
                    if ($lastModifiedTimestamp == get_option(WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $key, 0) && !$this->_debug) {
                        // timestamp did not change
                        // -> do nothing
                        update_option(WP_PERSONIO_OPTION_COUNT, ++$count);
                        $doNothing = true;
                        !$progress ?: $progress->tick();
                        continue;
                    }

                    // define settings for second request to get the contents
                    $args = [
                        'timeout' => get_option('personioIntegrationUrlTimeout', 30),
                        'httpversion' => '1.1',
                        'redirection' => 0
                    ];
                    $response = wp_remote_get($url, $args);

                    // get the body with the contents
                    $body = wp_remote_retrieve_body($response);

                    // get the md5-hash of the response
                    $md5hash = md5($body);
                    // check if md5-hash has been changed
                    if ($md5hash == get_option(WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $key, '') && !$this->_debug) {
                        // md5-hash did not change
                        // -> do nothing
                        update_option(WP_PERSONIO_OPTION_COUNT, ++$count);
                        $doNothing = true;
                        !$progress ?: $progress->tick();
                        continue;
                    }

                    // load content as simplexml
                    try {
                        $positions = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);
                    } catch (Exception $e) {
                        $this->_errors[] = __("XML file from Personio for language " . esc_html($key) . " contains incorrect code and therefore cannot be read in. Technical Error: ") . $e->getMessage();
                        // show progress
                        update_option(WP_PERSONIO_OPTION_COUNT, ++$count);
                        !$progress ?: $progress->tick();
                        continue;
                    }

                    // get xml-errors
                    $xmlErrors = libxml_get_errors();
                    if( !empty($xmlErrors) ) {
                        $this->_errors[] = __("XML file from Personio for language " . esc_html($key) . " contains incorrect code and therefore cannot be read in.");
                        continue;
                    }

                    // disable taxonomy-counting
                    wp_defer_term_counting(true);

                    // loop through the results and import each position
                    if( !empty($positions) ) {
                        // update max-counter
                        // -> only once per import
                        if( !$doNotUpdateMaxCounter ) {
                            update_option(WP_PERSONIO_OPTION_MAX, $languageCount * count($positions));
                            $doNotUpdateMaxCounter = true;
                        }

                        foreach ($positions as $position) {
                            // add to list for counting
                            $importedPositions[(int)$position->id] = $position;

                            if( false !== apply_filters('personio_integration_import_single_position', true, $position, $key) ) {
                                // import the position
                                $this->importPosition($position, $key);
                            }

                            // update counter
                            update_option(WP_PERSONIO_OPTION_COUNT, ++$count);
                        }

                        // save the md5-hash of this import-file to prevent reimport
                        update_option(WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $key, $md5hash);

                        // save the last-modified-timestamp
                        update_option(WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $key, $lastModifiedTimestamp);
                    }

                    // re-enable taxonomy-counting
                    wp_defer_term_counting(false);
                } else {
                    $this->_errors[] = __('Personio URL not available. Please check the URL you configured.', 'wp-personio-integration');
                }
                // show progress
                !$progress ?: $progress->tick();
            }
            // show finished progress
            !$progress ?: $progress->finish();
        }

        // disable xml-error-handling
        libxml_use_internal_errors(false);

        if( empty($this->_errors) ) {
            // get Positions-object
            $positionsObject = Positions::get_instance();

            // delete all not updated positions
            if( !$doNothing ) {
                foreach ($positionsObject->getPositions() as $position) {
                    $positionPostId = $position->ID;
                    $personioId = $position->getPersonioId();
                    if (get_post_meta($positionPostId, WP_PERSONIO_INTEGRATION_UPDATED, true) == 1) {
                        delete_post_meta($positionPostId, WP_PERSONIO_INTEGRATION_UPDATED);
                    } else {
                        // delete this position from database
                        wp_delete_post($positionPostId, true);

                        // log this event
                        $this->_log->addLog('Position ' . $personioId . ' has been deleted as it does not return from Personio.', 'success');
                    }
                }
            }

            // run actions after import has been done
            do_action('personio_integration_import_ended');

            // output success-message
            /** @noinspection PhpUndefinedClassInspection */
            helper::isCLI() ? \WP_CLI::success($languageCount . " languages grabbed, " . count($importedPositions) . " positions imported.") : false;

            // save position count
            $countPositions = $positionsObject->getPositionsCount();
            update_option('personioIntegrationPositionCount', $countPositions);

            // set position count if > 0
            if( $countPositions > 0 ) {
                // remove transient with no-import-hint
                delete_transient('personio_integration_no_position_imported');
            }

            // log ok
            $this->logSuccess($languageCount . " languages grabbed, " . count($importedPositions) . " positions imported.");
        }
        else {
            // output error-message
            /** @noinspection PhpUndefinedClassInspection */
            $this->showErrors();
        }

        // mark import as not running anymore
        update_option(WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0);
    }

    /**
     * Output all errors as list to cli.
     * Collect the errors for backend-output.
     * Inform admin via email about the problems.
     *
     * @return void
     */
    private function showErrors()
    {
        $ausgabe = '';
        foreach( $this->_errors as $e ) {
            $ausgabe .= $e.'\n';
        }
        $ausgabe .= "\n";

        // save results in database
        $this->_log->addLog($ausgabe, !empty($this->_errors) ? 'error' : 'success');

        // output results in WP-CLI
        echo (helper::isCLI() ? esc_html($ausgabe) : "");

        // send info to admin about the problem
        if( !empty($this->_errors) && !$this->_debug ) {
            $sendTo = get_bloginfo('admin_email');
            $subject = get_bloginfo('name') . ": ".__('Error during Personio Positions Import', 'wp-personio-integration');
            $msg = __('The following error occurred when importing positions provided by Personio:', 'wp-personio-integration').'\r\n' . $ausgabe;
            $msg .= '\r\n\r\n'.__('Sent by the plugin Personio Integration', 'wp-personio-integration');
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
    private function importPosition(?SimpleXMLElement $position, $key )
    {
        // create position object to handle all values and save them to database
        $positionObject = new Position(0);
        $positionObject->lang = $key;
        $positionObject->post_title = (string)$position->name;
        $positionObject->post_content = $position->jobDescriptions;
        $positionObject->department = '';
        if( !empty($position->department) ) {
            $positionObject->department = (string)$position->department;
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
    private function logSuccess(string $string)
    {
        // save result in database
        $this->_log->addLog($string, !empty($this->_errors) ? 'error' : 'success');
    }

}