<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Personio.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\PersonioIntegration;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in the class PersonioIntegrationLight\PersonioIntegration\Personio.
 */
class Personio extends PersonioTestCase {

	/**
	 * The object.
	 *
	 * @var \PersonioIntegrationLight\PersonioIntegration\Personio
	 */
	private \PersonioIntegrationLight\PersonioIntegration\Personio $object;

	/**
	 * The test URL.
	 *
	 * @var string
	 */
	private string $url = 'https://example.com';

	/**
	 * Prepare the test environment.
	 *
	 * @return void
	 */
	public function setUp(): void {
		// create the object with our test URL.
		$this->object = new \PersonioIntegrationLight\PersonioIntegration\Personio( $this->url );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_url(): void {
		$url = $this->object->get_url();
		$this->assertIsString( $url );
		$this->assertNotEmpty( $url );
		$this->assertEquals( $this->url, $url );
	}

	/**
	 * Test if the returning variable is a string with "#apply" in it.
	 *
	 * @return void
	 */
	public function test_get_application_url_with_apply_hash(): void {
		// get test position.
		$position_obj = self::get_single_position();

		// get the application URL.
		$application_url = $this->object->get_application_url( $position_obj );
		$this->assertIsString( $application_url );
		$this->assertNotEmpty( $application_url );
		$this->assertStringContainsString( $this->url, $application_url );
		$this->assertStringContainsString( '#apply', $application_url );
	}

	/**
	 * Test if the returning variable is a string without "#apply" in it.
	 *
	 * @return void
	 */
	public function test_get_application_url_without_apply_hash(): void {
		// get test position.
		$position_obj = self::get_single_position();

		// get the application URL.
		$application_url = $this->object->get_application_url( $position_obj, true );
		$this->assertIsString( $application_url );
		$this->assertNotEmpty( $application_url );
		$this->assertStringContainsString( $this->url, $application_url );
		$this->assertStringNotContainsString( '#apply', $application_url );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_xml_url_without_language(): void {
		$url = $this->object->get_xml_url();
		$this->assertIsString( $url );
		$this->assertNotEmpty( $url );
		$this->assertStringContainsString( '/xml', $url );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_xml_url_with_language(): void {
		$language = 'en';
		$url = $this->object->get_xml_url( $language );
		$this->assertIsString( $url );
		$this->assertNotEmpty( $url );
		$this->assertStringContainsString( '/xml', $url );
		$this->assertStringContainsString( 'language=' . $language, $url );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_timestamp(): void {
		$timestamp = $this->object->get_timestamp( 'en' );
		$this->assertIsInt( $timestamp );
	}
}
