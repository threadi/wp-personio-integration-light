<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Api_Request.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\PersonioIntegration;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in class PersonioIntegrationLight\PersonioIntegration\Api_Request.
 */
class Api_Request extends PersonioTestCase {

	/**
	 * Prepare the test environment.
	 *
	 * @return void
	 */
	public function setUp(): void {
		// install the db table for the API.
		\PersonioIntegrationLight\PersonioIntegration\Api::get_instance()->create_table();

		// set pseudo credentials for the API.
		update_option( 'personioIntegrationClientId', 'client id example' );
		update_option( 'personioIntegrationApiSecret', 'api secret example' );
	}

	/**
	 * Test a valid API request.
	 *
	 * @return void
	 */
	public function test_valid_request(): void {
		// create the request.
		$request_object = new \PersonioIntegrationLight\PersonioIntegration\Api_Request();
		$request_object->set_url( 'https://api.personio.de/v2/auth/token' );
		$request_object->set_post_data( array( 'client_id' => 'valid' ) );

		// send it.
		$request_object->send();

		// get the response.
		$response = $request_object->get_response();
		$this->assertNotEmpty( $response );
		$this->assertIsString( $response );
		$this->assertJson( $response );

		// check if the response contains the key "access_token".
		$this->assertStringContainsString( '"access_token"', $response );
	}

	/**
	 * Test an invalid API request.
	 *
	 * @return void
	 */
	public function test_invalid_request(): void {
		// create the request.
		$request_object = new \PersonioIntegrationLight\PersonioIntegration\Api_Request();
		$request_object->set_url( 'https://api.personio.de/v2/auth/token' );
		$request_object->set_post_data( array( 'client_id' => 'invalid' ) );

		// send it.
		$request_object->send();

		// get the response.
		$response = $request_object->get_response();
		$this->assertNotEmpty( $response );
		$this->assertIsString( $response );
		$this->assertJson( $response );

		// check if the response contains the key "invalid_request".
		$this->assertStringContainsString( '"invalid_request"', $response );
	}

	/**
	 * Test an invalid API request.
	 *
	 * @return void
	 */
	public function get_failed_http_status(): void {
		// create the request.
		$request_object = new \PersonioIntegrationLight\PersonioIntegration\Api_Request();
		$request_object->set_url( 'https://api.personio.de/v2/auth/token' );
		$request_object->set_post_data( array( 'key' => 'value' ) );

		// send it.
		$request_object->send();

		// get the HTTP status.
		$http_status = $request_object->get_http_status();
		$this->assertIsInt( $http_status );
		$this->assertGreaterThanOrEqual( 400, $http_status );
	}
}
