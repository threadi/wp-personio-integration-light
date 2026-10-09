<?php
/**
 * Tests for class PersonioIntegrationLight\Plugin\Compatibilities.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Plugin;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in class PersonioIntegrationLight\Plugin\Compatibilities.
 */
class Compatibilities extends PersonioTestCase {

	/**
	 * Reset the simulated admin screen after each test.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		unset( $GLOBALS['current_screen'] );

		parent::tear_down();
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_compatibility_checks_as_object(): void {
		$compatibilities_objects = \PersonioIntegrationLight\Plugin\Compatibilities::get_instance()->get_compatibility_checks_as_object();
		$this->assertIsArray( $compatibilities_objects );
		$this->assertNotEmpty( $compatibilities_objects );
	}

	/**
	 * Test if the returning variable the given value.
	 *
	 * @return void
	 */
	public function test_prevent_checks_outside_of_admin(): void {
		// test 1: with "true" outside of admin.
		$value = \PersonioIntegrationLight\Plugin\Compatibilities::get_instance()->prevent_checks_outside_of_admin( true );
		$this->assertIsBool( $value );
		$this->assertTrue( $value );

		// test 2: with "false" outside of admin.
		$value = \PersonioIntegrationLight\Plugin\Compatibilities::get_instance()->prevent_checks_outside_of_admin( false );
		$this->assertIsBool( $value );
		$this->assertTrue( $value );

		// simulate admin via the current screen (a constant like WP_ADMIN would leak into all following tests).
		if ( ! function_exists( 'set_current_screen' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
			require_once ABSPATH . 'wp-admin/includes/screen.php';
		}
		set_current_screen( 'dashboard' );
		$this->assertTrue( is_admin() );

		// test 3: with "true" in admin.
		$value = \PersonioIntegrationLight\Plugin\Compatibilities::get_instance()->prevent_checks_outside_of_admin( true );
		$this->assertIsBool( $value );
		$this->assertTrue( $value );

		// test 4: with "false" in admin.
		$value = \PersonioIntegrationLight\Plugin\Compatibilities::get_instance()->prevent_checks_outside_of_admin( false );
		$this->assertIsBool( $value );
		$this->assertFalse( $value );
	}
}
