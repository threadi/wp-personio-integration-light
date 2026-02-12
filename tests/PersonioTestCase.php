<?php
/**
 * File to handle the main object for each test class.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests;

use WP_Error;
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
	 * The real API token URL used by our API object.
	 *
	 * @var string
	 */
	protected static string $api_url = 'https://api.personio.de/v2/auth/token';

	/**
	 * The pseudo Personio URL we use as a test for valid XMLs.
	 *
	 * @var string
	 */
	protected static string $personio_url = 'https://personio-integration-test.jobs.personio.com';

	/**
	 * The pseudo Personio URL we use as a test for invalid XMLs.
	 *
	 * @var string
	 */
	protected static string $personio_empty_url = 'https://personio-integration-empty-test.jobs.personio.com';

	/**
	 * The pseudo Personio URL we use as a test for invalid XMLs.
	 *
	 * @var string
	 */
	protected static string $personio_faulty_url = 'https://personio-integration-fauly-test.jobs.personio.com';

	/**
	 * Prepare the test environment for each test class.
	 *
	 * @return void
	 */
	public static function set_up_before_class(): void {
		parent::set_up_before_class();

		// prepare to load just one time.
		if ( ! did_action('personio_integration_light_test_preparation_loaded') ) {
			// Plugin initialisieren
			\PersonioIntegrationLight\Plugin\Installer::get_instance()->activation();

			// run initialization.
			do_action( 'init' );

			// prevent external requests from Personio APIs.
			add_filter( 'pre_http_request', array( self::class, 'add_url_filter' ), 10, 3 );

			// mark that an import is not running.
			update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );

			// mark as loaded.
			do_action('personio_integration_light_test_preparation_loaded');
		}
	}

	/**
	 * Define the URL filter for external requests to prevent any external requests for selected URLs.
	 *
	 * @param false|array|WP_Error $false The return value of the filter.
	 * @param array $parsed_args The used parameters for the request.
	 * @param string $url The requested URL.
	 *
	 * @return false|array|WP_Error
	 */
	public static function add_url_filter( false|array|WP_Error $false, array $parsed_args, string $url ): false|array|WP_Error {
		// create a local response for the HEAD request.
		if( 'HEAD' === $parsed_args['method'] && ( str_starts_with( $url, self::$personio_url ) || str_starts_with( $url, self::$personio_faulty_url ) || str_starts_with( $url, self::$personio_empty_url ) ) ) {
			// create the response object.
			$requests_response = new \WpOrg\Requests\Response();
			$requests_response->status_code = 200;
			$requests_response->headers['last-modified'] = time();

			// create the header response.
			return array(
				'http_response' => new WP_HTTP_Requests_Response( $requests_response, $parsed_args['filename'] )
			);
		}

		// create a local response for the GET request of valid Personio XML without the "last-modified" header.
		if( 'GET' === $parsed_args['method'] && isset( $parsed_args['no-last-modified'] ) && str_starts_with( $url, self::$personio_url ) ) {
			// get our XML file and return its content.
			$xml = \PersonioIntegrationLight\Helper::get_wp_filesystem()->get_contents( UNIT_TESTS_DATA_PLUGIN_DIR . 'positions.xml' );

			// create the response object.
			$requests_response = new \WpOrg\Requests\Response();
			$requests_response->status_code = 200;

			// create the header response.
			return array(
				'http_response' => new WP_HTTP_Requests_Response( $requests_response, $parsed_args['filename'] ),
				'body' => $xml
			);
		}

		// create a local response for the GET request of valid Personio XML.
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

		// create a local response for the GET request of invalid Personio XML.
		if( 'GET' === $parsed_args['method'] && str_starts_with( $url, self::$personio_faulty_url ) ) {
			// get our XML file and return its content.
			$xml = \PersonioIntegrationLight\Helper::get_wp_filesystem()->get_contents( UNIT_TESTS_DATA_PLUGIN_DIR . 'positions_faulty.xml' );

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

		// create a local response for the GET request of invalid Personio XML.
		if( 'GET' === $parsed_args['method'] && str_starts_with( $url, self::$personio_empty_url ) ) {
			// get our XML file and return its content.
			$xml = \PersonioIntegrationLight\Helper::get_wp_filesystem()->get_contents( UNIT_TESTS_DATA_PLUGIN_DIR . 'positions_empty.xml' );

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

		// create a local response for the POST request to Personio API.
		if( ! empty( $parsed_args['body']['client_id'] ) && 'POST' === $parsed_args['method'] && str_starts_with( $url, self::$api_url ) ) {
			// get the response.
			$json = \PersonioIntegrationLight\Helper::get_wp_filesystem()->get_contents( UNIT_TESTS_DATA_PLUGIN_DIR . 'api_' . $parsed_args['body']['client_id'] . '.json' );

			// create the response object.
			$requests_response = new \WpOrg\Requests\Response();
			$requests_response->status_code = 200;

			// create the header response.
			return array(
				'http_response' => new WP_HTTP_Requests_Response( $requests_response, $parsed_args['filename'] ),
				'body' => $json
			);
		}

		// return the given value.
		return $false;
	}

	/**
	 * Return a single position as an object.
	 *
	 * @param string $xml_type
	 *
	 * @return \PersonioIntegrationLight\PersonioIntegration\Position|false
	 */
	protected static function get_single_position( string $xml_type = '' ): \PersonioIntegrationLight\PersonioIntegration\Position|false {
		// get positions.
		$positions = \PersonioIntegrationLight\PersonioIntegration\Positions::get_instance()->get_positions( 1 );

		if( empty( $positions ) ) {
			// get the URL depending on XML-type.
			switch( $xml_type ) {
				case 'empty':
					$url = self::$personio_empty_url;
					break;
				case 'invalid':
					$url = self::$personio_faulty_url;
					break;
				case "without_lm":
					$url = self::$personio_url;
					add_filter( 'http_request_args', function( array $parsed_args ) {
						$parsed_args['no-last-modified'] = true;
						return $parsed_args;
					});
					break;
				default:
					$url = self::$personio_url;
			}

			// set the Personio URL.
			update_option( 'personioIntegrationUrl', $url );

			// import them via XML-import.
			$imports_obj = new \PersonioIntegrationLight\PersonioIntegration\Imports\Xml();
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
