<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Availability.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\PersonioIntegration;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in the class PersonioIntegrationLight\PersonioIntegration\Availability.
 */
class Availability extends PersonioTestCase {

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_name(): void {
		$name = \PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->get_name();
		$this->assertIsString( $name );
		$this->assertNotEmpty( $name );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_label(): void {
		$label = \PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->get_label();
		$this->assertIsString( $label );
		$this->assertNotEmpty( $label );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_is_enabled(): void {
		$enabled = \PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->is_enabled();
		$this->assertIsBool( $enabled );
		$this->assertTrue( $enabled );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_category(): void {
		$category = \PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->get_category();
		$this->assertIsString( $category );
		$this->assertNotEmpty( $category );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_description(): void {
		$description = \PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->get_description();
		$this->assertIsString( $description );
		$this->assertNotEmpty( $description );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_toggle_state(): void {
		// get the actual state.
		$enabled = \PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->is_enabled();

		// toggle the state.
		\PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->toggle_state();

		// get the new state.
		$new_enabled = \PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->is_enabled();
		$this->assertIsBool( $new_enabled );
		$this->assertNotEquals( $enabled, $new_enabled );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_set_disabled(): void {
		// get the actual state.
		$enabled = \PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->is_enabled();

		// set the state.
		\PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->set_disabled();

		// get the new state.
		$new_enabled = \PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->is_enabled();
		$this->assertIsBool( $new_enabled );
		$this->assertNotEquals( $enabled, $new_enabled );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_set_enabled(): void {
		// get the actual state.
		$enabled = \PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->is_enabled();

		// set the state.
		\PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->set_enabled();

		// get the new state.
		$new_enabled = \PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->is_enabled();
		$this->assertIsBool( $new_enabled );
		$this->assertEquals( $enabled, $new_enabled );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_can_be_enabled_by_user(): void {
		$can_be_enabled_by_user = \PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->can_be_enabled_by_user();
		$this->assertIsBool( $can_be_enabled_by_user );
		$this->assertTrue( $can_be_enabled_by_user );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_is_not_active(): void {
		$is_active = \PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->is_active();
		$this->assertIsBool( $is_active );
		$this->assertFalse( $is_active );
	}
}
