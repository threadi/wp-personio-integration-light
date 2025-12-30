<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Api.
 *
 * @package personio-integration-light
 */

use PersonioIntegrationLight\Plugin\Crypt;

/**
 * Object to test functions in class PersonioIntegrationLight\PersonioIntegration\Api.
 */
class Api extends WP_UnitTestCase {

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
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_no_access_token(): void {
		$access_token = \PersonioIntegrationLight\PersonioIntegration\Api::get_instance()->get_access_token();
		$this->assertIsString( $access_token );
		$this->assertEmpty( $access_token );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_access_token(): void {
		// set an example token.
		set_transient( 'personio_integration_api_token', Crypt::get_instance()->encrypt( 'example' ), 180 );

		// test is.
		$access_token = \PersonioIntegrationLight\PersonioIntegration\Api::get_instance()->get_access_token();
		$this->assertIsString( $access_token );
		$this->assertNotEmpty( $access_token );
	}

	/**
	 * Test if the return value is an array.
	 *
	 * @return void
	 */
	public function test_add_log_category(): void {
		$log_categories = \PersonioIntegrationLight\PersonioIntegration\Api::get_instance()->add_log_category( array() );
		$this->assertIsArray( $log_categories );
		$this->assertNotEmpty( $log_categories );
	}

	/**
	 * Test if the return value is an array.
	 *
	 * @return void
	 */
	public function test_add_schedule(): void {
		$log_categories = \PersonioIntegrationLight\PersonioIntegration\Api::get_instance()->add_schedule( array() );
		$this->assertIsArray( $log_categories );
		$this->assertNotEmpty( $log_categories );
	}
}
