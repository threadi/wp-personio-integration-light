<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Imports.
 *
 * @package personio-integration-light
 */

/**
 * Object to test functions in the class PersonioIntegrationLight\PersonioIntegration\Imports.
 */
class Imports extends WP_UnitTestCase {

	/**
	 * Test to run an import of positions.
	 *
	 * @return void
	 */
	public function test_run_import(): void {
		global $personio_positions;

		// first remove all existing positions.
		\PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition::get_instance()->delete_positions();

		// now run the import.
		$imports_obj = \PersonioIntegrationLight\PersonioIntegration\Imports::get_instance()->get_import_extension();
		if ( $imports_obj instanceof \PersonioIntegrationLight\PersonioIntegration\Imports_Base ) {
			$imports_obj->run();

			// check if the import was successful.
			$result = get_option( WP_PERSONIO_INTEGRATION_IMPORT_STATUS );
			$this->assertEquals( 'Import completed.', $result );
		}
		else {
			$this->hasFailed();
		}

		// now get the list of positions and check if it contains at least one entry.
		$personio_positions = \PersonioIntegrationLight\PersonioIntegration\Positions::get_instance()->get_positions();
		$this->assertNotEmpty( $personio_positions );
	}

	/**
	 * Test to run a faulty import of positions.
	 *
	 * @return void
	 */
	public function test_run_error_import(): void {
		// mark that an import is already running.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, time() );

		// first remove all existing positions.
		\PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition::get_instance()->delete_positions();

		// now run the import.
		$imports_obj = \PersonioIntegrationLight\PersonioIntegration\Imports::get_instance()->get_import_extension();
		if ( $imports_obj instanceof \PersonioIntegrationLight\PersonioIntegration\Imports_Base ) {
			$imports_obj->run();

			// check if the import was successful.
			$result = get_option( WP_PERSONIO_INTEGRATION_IMPORT_STATUS );
			$this->assertNotEquals( 'Import completed.', $result );
		}
		else {
			$this->hasFailed();
		}
	}
}
