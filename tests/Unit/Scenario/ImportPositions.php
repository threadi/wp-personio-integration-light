<?php
/**
 * File to some test scenarios for one topic.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\PersonioIntegration;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to run some test scenarios for one topic.
 */
class ImportPositions extends PersonioTestCase {

	/**
	 * Prepare the test environment.
	 *
	 * @return void
	 */
	public function set_up(): void {
		parent::set_up();

		// delete positions.
		\PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition::get_instance()->delete_positions();
	}

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

	/**
	 * Test to run import of positions which error during it.
	 *
	 * @return void
	 */
	public function test_run_xml_error_import(): void {
		// use the global handler.
		$position_obj = self::get_single_position( 'invalid' );

		// test it.
		$this->assertIsNotObject( $position_obj );
	}

	/**
	 * Test to run import of positions which error during it.
	 *
	 * @return void
	 */
	public function test_run_xml_import_with_hidden_positions(): void {
		// prevent import of any position.
		add_filter( 'personio_integration_import_single_position', '__return_false' );

		// use the global handler.
		$position_obj = self::get_single_position();

		// test it.
		$this->assertIsNotObject( $position_obj );
	}

	/**
	 * Test to run import of positions which error during it.
	 *
	 * @return void
	 */
	public function test_run_xml_import_of_empty_xml(): void {
		// use the global handler.
		$position_obj = self::get_single_position( 'empty' );

		// test it.
		$this->assertIsNotObject( $position_obj );
	}
}
