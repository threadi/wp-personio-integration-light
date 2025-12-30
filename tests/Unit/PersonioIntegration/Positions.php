<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Positions.
 *
 * @package personio-integration-light
 */

/**
 * Object to test functions in the class PersonioIntegrationLight\PersonioIntegration\Positions.
 */
class Positions extends WP_UnitTestCase {

	/**
	 * Test if the returning variable is an object.
	 *
	 * @return void
	 */
	public function test_get_position(): void {
		global $personio_positions;

		// bail if no positions were found.
		if( empty( $personio_positions ) ) {
			$this->markTestSkipped( 'No positions were found.' );
		}

		// get the object.
		$position_obj = \PersonioIntegrationLight\PersonioIntegration\Positions::get_instance()->get_position( $personio_positions[0]->get_id() );
		$this->assertIsObject( $position_obj );
		$this->assertInstanceOf( '\PersonioIntegrationLight\PersonioIntegration\Position', $position_obj );
		$this->assertEquals( $personio_positions[0]->get_id(), $position_obj->get_id() );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_positions(): void {
		$positions = \PersonioIntegrationLight\PersonioIntegration\Positions::get_instance()->get_positions();
		$this->assertIsArray( $positions );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_positions_count(): void {
		$positions_count = \PersonioIntegrationLight\PersonioIntegration\Positions::get_instance()->get_positions_count();
		$this->assertIsInt( $positions_count );
	}
}
