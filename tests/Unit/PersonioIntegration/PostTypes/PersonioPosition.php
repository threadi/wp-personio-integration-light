<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition.
 *
 * @package personio-integration-light
 */

/**
 * Object to test functions in the class PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition.
 */
class PersonioPosition extends WP_UnitTestCase {
	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_name(): void {
		$name = \PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition::get_instance()->get_name();
		$this->assertIsString( $name );
		$this->assertNotEmpty( $name );
		$this->assertEquals( WP_PERSONIO_INTEGRATION_MAIN_CPT, $name );
	}
}
