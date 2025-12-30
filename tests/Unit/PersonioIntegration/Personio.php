<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Personio.
 *
 * @package personio-integration-light
 */

/**
 * Object to test functions in the class PersonioIntegrationLight\PersonioIntegration\Personio.
 */
class Personio extends WP_UnitTestCase {

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
		global $personio_positions;

		// bail if no positions were found.
		if( empty( $personio_positions ) ) {
			$this->markTestSkipped( 'No positions were found.' );
		}

		// get the application URL.
		$application_url = $this->object->get_application_url( $personio_positions[0] );
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
		global $personio_positions;

		// bail if no positions were found.
		if( empty( $personio_positions ) ) {
			$this->markTestSkipped( 'No positions were found.' );
		}

		// get the application URL.
		$application_url = $this->object->get_application_url( $personio_positions[0], true );
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
