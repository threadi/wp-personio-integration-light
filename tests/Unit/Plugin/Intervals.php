<?php
/**
 * Tests for class PersonioIntegrationLight\Plugin\Intervals.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Plugin;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in class PersonioIntegrationLight\Plugin\Intervals.
 */
class Intervals extends PersonioTestCase {

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_add_intervals(): void {
		$intervals = \PersonioIntegrationLight\Plugin\Intervals::get_instance()->add_intervals( array() );
		$this->assertIsArray( $intervals );
		$this->assertNotEmpty( $intervals );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_intervals_for_settings(): void {
		$intervals = \PersonioIntegrationLight\Plugin\Intervals::get_instance()->get_intervals_for_settings();
		$this->assertIsArray( $intervals );
		$this->assertNotEmpty( $intervals );
	}
}
