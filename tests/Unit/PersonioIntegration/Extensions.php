<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Extensions.
 *
 * @package personio-integration-light
 */

/**
 * Object to test functions in the class PersonioIntegrationLight\PersonioIntegration\Extensions.
 */
class Extensions extends WP_UnitTestCase {

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_extensions_as_objects(): void {
		$extensions = \PersonioIntegrationLight\PersonioIntegration\Extensions::get_instance()->get_extensions_as_objects();
		$this->assertIsArray( $extensions );
		$this->assertNotEmpty( $extensions );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_add_extensions(): void {
		$extensions = \PersonioIntegrationLight\PersonioIntegration\Extensions::get_instance()->add_extensions( array() );
		$this->assertIsArray( $extensions );
		$this->assertNotEmpty( $extensions );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_extensions(): void {
		$extensions = \PersonioIntegrationLight\PersonioIntegration\Extensions::get_instance()->get_extensions();
		$this->assertIsArray( $extensions );
		$this->assertNotEmpty( $extensions );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_link(): void {
		$extensions = \PersonioIntegrationLight\PersonioIntegration\Extensions::get_instance()->get_link();
		$this->assertIsString( $extensions );
		$this->assertNotEmpty( $extensions );
	}
}
