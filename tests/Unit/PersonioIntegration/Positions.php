<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Positions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\PersonioIntegration;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in the class PersonioIntegrationLight\PersonioIntegration\Positions.
 */
class Positions extends PersonioTestCase {

	/**
	 * Test if the returning variable is an object.
	 *
	 * @return void
	 */
	public function test_get_position(): void {
		// get a test position object.
		$test_position_obj = self::get_single_position();

		// test it.
		$position_obj = \PersonioIntegrationLight\PersonioIntegration\Positions::get_instance()->get_position( $test_position_obj->get_id() );
		$this->assertIsObject( $position_obj );
		$this->assertEquals( $test_position_obj->get_id(), $position_obj->get_id() );
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
