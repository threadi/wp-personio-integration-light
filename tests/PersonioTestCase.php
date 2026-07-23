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
	 * Pseudo Personio URL that simulates a temporary outage (HTTP 503).
	 *
	 * @var string
	 */
	protected static string $personio_error_url = 'https://personio-integration-error-test.jobs.personio.com';

	/**
	 * Pseudo Personio URL for the multilingual test: "de" is empty, every other language has positions.
	 *
	 * @var string
	 */
	protected static string $personio_multilang_url = 'https://personio-integration-multilang-test.jobs.personio.com';

	/**
	 * The shortcircuit Personio URL we use as a test for valid XMLs.
	 *
	 * @var string
	 */
	protected static string $personio_shortcircuit_url = 'https://personio-integration-shortcircuit-test.jobs.personio.com';

	/**
	 * The shortcircuit Personio URL we use as a test for valid XMLs.
	 *
	 * @var string
	 */
	protected static string $personio_shortcircuit_nolm_url = 'https://personio-integration-shortcircuit-nolm-test.jobs.personio.com';

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
			return self::mock_http_response( 200, $parsed_args['filename'] );
		}

		// simulate a Personio outage: HEAD returns a non-200 status -> import errors out.
		if ( 'HEAD' === $parsed_args['method'] && str_starts_with( $url, self::$personio_error_url ) ) {
			return self::mock_http_response( 503, $parsed_args['filename'] );
		}

		// create a local response for the GET request of valid Personio XML without the "last-modified" header.
		if( 'GET' === $parsed_args['method'] && isset( $parsed_args['no-last-modified'] ) && str_starts_with( $url, self::$personio_url ) ) {
			// get our XML file and return its content.
			$xml = \PersonioIntegrationLight\Helper::get_wp_filesystem()->get_contents( UNIT_TESTS_DATA_PLUGIN_DIR . 'positions.xml' );

			return self::mock_http_response( 200, $parsed_args['filename'], $xml );
		}

		// create a local response for the GET request of valid Personio XML.
		if( 'GET' === $parsed_args['method'] && str_starts_with( $url, self::$personio_url ) ) {
			// get our XML file and return its content.
			$xml = \PersonioIntegrationLight\Helper::get_wp_filesystem()->get_contents( UNIT_TESTS_DATA_PLUGIN_DIR . 'positions.xml' );

			return self::mock_http_response( 200, $parsed_args['filename'], $xml );
		}

		// create a local response for the GET request of invalid Personio XML.
		if( 'GET' === $parsed_args['method'] && str_starts_with( $url, self::$personio_faulty_url ) ) {
			// get our XML file and return its content.
			$xml = \PersonioIntegrationLight\Helper::get_wp_filesystem()->get_contents( UNIT_TESTS_DATA_PLUGIN_DIR . 'positions_faulty.xml' );

			return self::mock_http_response( 200, $parsed_args['filename'], $xml );
		}

		// create a local response for the GET request of invalid Personio XML.
		if( 'GET' === $parsed_args['method'] && str_starts_with( $url, self::$personio_empty_url ) ) {
			// get our XML file and return its content.
			$xml = \PersonioIntegrationLight\Helper::get_wp_filesystem()->get_contents( UNIT_TESTS_DATA_PLUGIN_DIR . 'positions_empty.xml' );

			return self::mock_http_response( 200, $parsed_args['filename'], $xml );
		}

		// create a local response for the POST request to Personio API.
		if( ! empty( $parsed_args['body']['client_id'] ) && 'POST' === $parsed_args['method'] && str_starts_with( $url, self::$api_url ) ) {
			// get the response.
			$json = \PersonioIntegrationLight\Helper::get_wp_filesystem()->get_contents( UNIT_TESTS_DATA_PLUGIN_DIR . 'api_' . $parsed_args['body']['client_id'] . '.json' );

			return self::mock_http_response( 200, $parsed_args['filename'], $json );
		}

		// multilingual test: HEAD is fine for every language.
		if ( 'HEAD' === $parsed_args['method'] && str_starts_with( $url, self::$personio_multilang_url ) ) {
			return self::mock_http_response( 200, $parsed_args['filename'] );
		}

		// multilingual test: "de" delivers an empty feed, every other language delivers positions.
		if ( 'GET' === $parsed_args['method'] && str_starts_with( $url, self::$personio_multilang_url ) ) {
			$file = str_contains( $url, 'language=de' ) ? 'personio_empty.xml' : 'positions.xml';
			$xml  = \PersonioIntegrationLight\Helper::get_wp_filesystem()->get_contents( UNIT_TESTS_DATA_PLUGIN_DIR . $file );
			return self::mock_http_response( 200, $parsed_args['filename'], $xml );
		}

		// simulate a third-party plugin short-circuiting pre_http_request.
		if ( str_starts_with( $url, self::$personio_shortcircuit_url ) ) {
			// the HEAD request only needs the status and the last-modified header.
			if ( 'HEAD' === $parsed_args['method'] ) {
				return array(
					'headers'  => array( 'last-modified' => gmdate( 'D, d M Y H:i:s' ) . ' GMT' ),
					'body'     => '',
					'response' => array(
						'code'    => 200,
						'message' => 'OK',
					),
					'cookies'  => array(),
				);
			}

			// the GET request delivers the positions.
			return array(
				'headers'  => array(),
				'body'     => \PersonioIntegrationLight\Helper::get_wp_filesystem()->get_contents( UNIT_TESTS_DATA_PLUGIN_DIR . 'positions.xml' ),
				'response' => array(
					'code'    => 200,
					'message' => 'OK',
				),
				'cookies'  => array(),
			);
		}

		// same as the short-circuit case above, but WITHOUT the last-modified header.
		if ( str_starts_with( $url, self::$personio_shortcircuit_nolm_url ) ) {
			if ( 'HEAD' === $parsed_args['method'] ) {
				return array(
					'headers'  => array(),
					'body'     => '',
					'response' => array(
						'code'    => 200,
						'message' => 'OK',
					),
					'cookies'  => array(),
				);
			}

			return array(
				'headers'  => array(),
				'body'     => \PersonioIntegrationLight\Helper::get_wp_filesystem()->get_contents( UNIT_TESTS_DATA_PLUGIN_DIR . 'positions.xml' ),
				'response' => array(
					'code'    => 200,
					'message' => 'OK',
				),
				'cookies'  => array(),
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

	/**
	 * Prepare a mock response for the HTTP request.
	 *
	 * @param int         $status_code The status code of the response.
	 * @param string|null $filename The filename of the response.
	 * @param string      $body The body of the response.
	 *
	 * @return array
	 */
	private static function mock_http_response( int $status_code, ?string $filename = null, string $body = '' ): array {
		$requests_response              = new \WpOrg\Requests\Response();
		$requests_response->status_code = $status_code;

		$response = array(
			'http_response' => new WP_HTTP_Requests_Response( $requests_response, (string) $filename ),
			'response'      => array( 'code' => $status_code, 'message' => '' ),
		);
		if ( '' !== $body ) {
			$response['body'] = $body;
		}
		return $response;
	}
}
