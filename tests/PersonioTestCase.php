<?php
/**
 * File to handle the main object for each test class.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests;

use PersonioIntegrationLight\PersonioIntegration\Imports\Xml;
use WP_HTTP_Requests_Response;
use WP_UnitTestCase;

/**
 * Object to handle the preparations for each test class.
 */
abstract class PersonioTestCase extends WP_UnitTestCase {

	/**
	 * The test email.
	 *
	 * @var string
	 */
	protected static string $email = 'info@example.com';

	/**
	 * The pseudo Personio URL we use as test.
	 *
	 * @var string
	 */
	protected static string $personio_url = 'https://personio-integration-test.jobs.personio.com';

	/**
	 * Prepare the test environment for each test class.
	 *
	 * @return void
	 */
	public static function set_up_before_class(): void {
		parent::set_up_before_class();

		// prepare loading just one time.
		if ( ! did_action('personio_integration_light_test_preparation_loaded') ) {
			// Plugin initialisieren
			\PersonioIntegrationLight\Plugin\Installer::get_instance()->activation();

			// run initialization.
			do_action( 'init' );

			// prevent external requests from Personio APIs.
			add_filter( 'pre_http_request', function( $false, $parsed_args, $url ) {
				// create a local response for the HEAD request.
				if( 'HEAD' === $parsed_args['method'] && str_starts_with( $url, self::$personio_url ) ) {
					// create the response object.
					$requests_response = new \WpOrg\Requests\Response();
					$requests_response->status_code = 200;
					$requests_response->headers['last-modified'] = time();

					// create the header response.
					return array(
						'http_response' => new WP_HTTP_Requests_Response( $requests_response, $parsed_args['filename'] )
					);
				}

				// create a local response for the GET request.
				if( 'GET' === $parsed_args['method'] && str_starts_with( $url, self::$personio_url ) ) {
					// get our XML file and return its content.
					$xml = \PersonioIntegrationLight\Helper::get_wp_filesystem()->get_contents( UNIT_TESTS_DATA_PLUGIN_DIR . 'positions.xml' );

					// create the response object.
					$requests_response = new \WpOrg\Requests\Response();
					$requests_response->status_code = 200;
					$requests_response->headers['last-modified'] = time();

					// create the header response.
					return array(
						'http_response' => new WP_HTTP_Requests_Response( $requests_response, $parsed_args['filename'] ),
						'body' => $xml
					);
				}

				// return the given value.
				return $false;
			}, 10, 3 );

			// mark that an import is not running.
			update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );

			// mark as loaded.
			do_action('personio_integration_light_test_preparation_loaded');
		}
	}

	/**
	 * Return a single position as object.
	 *
	 * @return \PersonioIntegrationLight\PersonioIntegration\Position|false
	 */
	protected static function get_single_position(): \PersonioIntegrationLight\PersonioIntegration\Position|false {
		// get positions.
		$positions = \PersonioIntegrationLight\PersonioIntegration\Positions::get_instance()->get_positions( 1 );

		if( empty( $positions ) ) {
			// set the Personio URL.
			update_option( 'personioIntegrationUrl', self::$personio_url );

			// import them via XML-import.
			$imports_obj = new Xml();
			$imports_obj->run();

			// get the list of positions.
			$positions = \PersonioIntegrationLight\PersonioIntegration\Positions::get_instance()->get_positions( 1 );
		}

		// bail if no positions could be loaded.
		if( empty( $positions ) ) {
			return false;
		}

		// get the first entry.
		return $positions[0];
	}
}
