<?php
/**
 * Tests for class PersonioIntegrationLight\Plugin\Init.
 *
 * @package personio-integration-light
 */

/**
 * Object to test functions in the class PersonioIntegrationLight\Plugin\Init.
 */
class Init extends WP_UnitTestCase {

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_add_setting_link(): void {
		$list_of_link = \PersonioIntegrationLight\Plugin\Init::get_instance()->add_setting_link( array() );
		$this->assertIsArray( $list_of_link );
		$this->assertNotEmpty( $list_of_link );
	}
}
