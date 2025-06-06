<?php
/**
 * File for handling any requests to the Personio AP v2.
 *
 * @source https://developer.personio.de/reference/introduction
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use WP_Error;

/**
 * Send single request with given post-data.
 * Used for each request to Personio API.
 */
class Api_Request {

	/**
	 * The list of errors.
	 *
	 * @var array<int,WP_Error>
	 */
	private array $errors = array();

	/**
	 * The URL for the request.
	 *
	 * @var string
	 */
	private string $url;

	/**
	 * The method to use for the request.
	 *
	 * @var string
	 */
	private string $method = 'POST';

	/**
	 * Set default http header.
	 *
	 * @var array<string,string>
	 */
	private array $header = array(
		'Accept' => 'application/json',
	);

	/**
	 * The HTTP-Post-data as JSON- or boundary-string.
	 *
	 * @var string|array<string,mixed>
	 */
	private string|array $post_data;

	/**
	 * The response.
	 *
	 * @var string
	 */
	private string $response = '';

	/**
	 * The http-status.
	 *
	 * @var int
	 */
	private int $http_status = -1;

	/**
	 * The md5 hash of the transferred object.
	 *
	 * @var string
	 */
	private string $md5 = '';

	/**
	 * Constructor to build this object.
	 */
	public function __construct() {}

	/**
	 * Set URL.
	 *
	 * @param string $url The url to request.
	 * @return void
	 */
	public function set_url( string $url ): void {
		$this->url = $url;
	}

	/**
	 * Set header for request additional to authentication-header which is set by this object.
	 *
	 * @param array<string,string> $header List of headers.
	 * @return void
	 */
	public function set_header( array $header ): void {
		$this->header = $header;
	}

	/**
	 * Set post data for the request.
	 *
	 * @param string|array<string,mixed> $post_data The post-data as JSON- or boundary-string OR as array.
	 * @return void
	 */
	public function set_post_data( string|array $post_data ): void {
		$this->post_data = $post_data;
	}

	/**
	 * Send the request and collect the result in this object.
	 * Do not interpret anything of the response.
	 *
	 * @return bool
	 */
	public function send(): bool {
		global $wpdb;

		$time_limit = 90;
		/**
		 * Filter the request time limit for Personio API. We use default 90s (60s from Personio API + 30s puffer).
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param int $time_limit The limit in seconds
		 */
		$time_limit = apply_filters( 'personio_integration_light_request_time_limit', $time_limit );

		// check if there has not been 150 request in the last 90 seconds to Personio.
		// -> we use a puffer of 30 seconds more than the Personio API requires.
		$results = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT
                `id`
            FROM ' . $wpdb->prefix . 'personio_api_requests
            WHERE
                insertdate >= DATE_SUB( %s, INTERVAL %d SECOND)',
				current_time( 'mysql', 1 ),
				absint( $time_limit )
			),
			ARRAY_A
		);
		if ( count( $results ) >= 150 ) {
			// add this as error.
			$this->add_error( __( 'More than 150 requests were sent to Personio in the last 90 seconds - we will try it later to get around the limitation of Personio.', 'personio-integration-light' ) );

			// do nothing more.
			return false;
		}

		// get the header-array.
		$headers = $this->header;

		$instance = $this;
		/**
		 * Filter the headers for the request.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 *
		 * @param array<string,string> $headers List of headers.
		 * @param Api_Request $instance The Api_Request-object.
		 */
		$headers = apply_filters( 'personio_integration_light_request_header', $headers, $instance );

		// collect arguments for request.
		$args = array(
			'method'      => $this->get_method(),
			'headers'     => $headers,
			'httpversion' => '1.1',
			'timeout'     => get_option( 'personioIntegrationUrlTimeout' ),
			'redirection' => 10,
			'body'        => $this->get_post_data(),
		);

		// set response initiale to false.
		$response = false;

		// send request and get the result-object.
		switch ( $this->get_method() ) {
			case 'GET':
				$response = wp_remote_get( $this->get_url(), $args );
				break;
			case 'POST':
				$response = wp_remote_post( $this->get_url(), $args );
				break;
		}

		// bail if response is false.
		if ( false === $response ) {
			// add event in log.
			$this->add_error( __( 'Got not response from Personio API.', 'personio-integration-light' ) );

			// return false as request resulted in unspecific http error.
			return false;
		}

		// bail on error.
		if ( is_wp_error( $response ) ) {
			// add event in log.
			$this->add_error( __( 'The request to the Personio API ended in an error: ', 'personio-integration-light' ) . Helper::get_json( $response ) );

			// return false as request resulted in unspecific http error.
			return false;
		}

		// secure response.
		$this->set_response( wp_remote_retrieve_body( $response ) );

		// secure http-status.
		$this->set_http_status( absint( wp_remote_retrieve_response_code( $response ) ) );

		// count request if http_status is > 0 (0 would be timeout).
		if ( $this->get_http_status() > 0 ) {
			$wpdb->insert(
				$wpdb->prefix . 'personio_api_requests',
				array(
					'insertdate' => current_time( 'mysql', 1 ),
					'md5'        => md5( $this->get_url() . Helper::get_json( (array) $this->get_post_data() ) ),
				)
			);

			// log error if any occurred.
			if ( ! empty( $wpdb->last_error ) ) {
				$this->add_error( __( 'Database error:', 'personio-integration-light' ) . ' <code>' . esc_html( $wpdb->last_error ) . '</code>' );
			}
		}

		// log this request.
		$log_text  = __( 'URL:', 'personio-integration-light' ) . ' <code>' . esc_url( $this->get_url() ) . '</code>';
		$log_text .= '<br><br>' . __( 'Request:', 'personio-integration-light' ) . ' <code>' . wp_json_encode( $args ) . '</code>';
		$log_text .= '<br><br>' . __( 'HTTP-Status:', 'personio-integration-light' ) . ' <code>' . wp_json_encode( $this->get_http_status() ) . '</code>';
		$log_text .= '<br><br>' . __( 'Response:', 'personio-integration-light' ) . ' <code>' . wp_json_encode( $this->get_response() ) . '</code>';
		$log       = new Log();
		$log->add_log( $log_text, 'info', 'api', $this->get_md5() );

		// return true as request itself was successful.
		return true;
	}

	/**
	 * Return response of the request.
	 *
	 * @return string
	 */
	public function get_response(): string {
		return $this->response;
	}

	/**
	 * Set response of the request.
	 *
	 * @param string $response The response.
	 *
	 * @return void
	 */
	private function set_response( string $response ): void {
		$this->response = $response;
	}

	/**
	 * Return the http-status of this request.
	 *
	 * @return int
	 */
	public function get_http_status(): int {
		return $this->http_status;
	}

	/**
	 * Set the HTTP status.
	 *
	 * @param int $http_status The HTTP status.
	 *
	 * @return void
	 */
	private function set_http_status( int $http_status ): void {
		$this->http_status = $http_status;
	}

	/**
	 * Return the URL for the request.
	 *
	 * @return string
	 */
	private function get_url(): string {
		return $this->url;
	}

	/**
	 * Return the POST-data.
	 *
	 * @return string|array<string,mixed>
	 */
	private function get_post_data(): string|array {
		return $this->post_data;
	}

	/**
	 * Return the method to use for this request.
	 * *
	 *
	 * @return string
	 */
	public function get_method(): string {
		return $this->method;
	}

	/**
	 * Set the method to use for this request.
	 *
	 * @param string $method The method (must be GET or POST).
	 *
	 * @return void
	 */
	public function set_method( string $method ): void {
		if ( ! in_array( $method, array( 'POST', 'GET' ), true ) ) {
			return;
		}
		$this->method = $method;
	}

	/**
	 * Set md5 hash for the transferred object.
	 *
	 * @param string $md5 The md5 hash.
	 *
	 * @return void
	 */
	public function set_md5( string $md5 ): void {
		$this->md5 = $md5;
	}

	/**
	 * Return the md5 hash.
	 *
	 * @return string
	 */
	private function get_md5(): string {
		return $this->md5;
	}

	/**
	 * Add an error with given text to the list of errors.
	 *
	 * @param string $text The error text.
	 *
	 * @return void
	 */
	private function add_error( string $text ): void {
		$error = new WP_Error();
		$error->add_data( $text );
		$this->errors[] = $error;
	}

	/**
	 * Return the list of errors.
	 *
	 * @return array<int,WP_Error>
	 */
	public function get_errors(): array {
		return $this->errors;
	}
}
