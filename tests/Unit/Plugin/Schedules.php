<?php
/**
 * Tests for class PersonioIntegrationLight\Plugin\Schedules.
 *
 * @package personio-integration-light
 */

/**
 * Object to test functions in class PersonioIntegrationLight\Plugin\Schedules.
 */
class Schedules extends WP_UnitTestCase {

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_add_setting_link(): void {
		$list_of_link = \PersonioIntegrationLight\Plugin\Schedules::get_instance()->get_schedule_object_names();
		$this->assertIsArray( $list_of_link );
		$this->assertNotEmpty( $list_of_link );
	}

	/**
	 * Test if the returning variable is an object.
	 *
	 * @return void
	 */
	public function test_get_schedule_object_by_name(): void {
		$object = \PersonioIntegrationLight\Plugin\Schedules::get_instance()->get_schedule_object_by_name( 'personio_integration_schedule_events' );
		$this->assertIsObject( $object );
		$this->assertInstanceOf( '\PersonioIntegrationLight\Plugin\Schedules_Base', $object );
	}

	/**
	 * Test if the returning variable is an object.
	 *
	 * @return void
	 */
	public function test_get_schedule_object_by_wrong_name(): void {
		$false = \PersonioIntegrationLight\Plugin\Schedules::get_instance()->get_schedule_object_by_name( 'example' );
		$this->assertIsBool( $false );
		$this->assertFalse( $false );
	}
}
