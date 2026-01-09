<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Imports.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\PersonioIntegration;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in the class PersonioIntegrationLight\PersonioIntegration\Imports.
 */
class Imports extends PersonioTestCase {

	/**
	 * Test to run an import of positions.
	 *
	 * @return void
	 */
	public function test_run_xml_import(): void {
		// use the global handler.
		$position_obj = self::get_single_position();

		// test it.
		$this->assertIsObject( $position_obj );
		$this->assertInstanceOf( '\PersonioIntegrationLight\PersonioIntegration\Position', $position_obj );
	}

	/**
	 * Test to run import of positions if other import is stil running.
	 *
	 * @return void
	 */
	public function test_run_xml_import_if_other_import_is_running(): void {
		// mark that an import is already running.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, time() );

		// use the global handler.
		$position_obj = self::get_single_position();

		// test it.
		$this->assertIsNotObject( $position_obj );
	}
}
