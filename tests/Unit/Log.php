<?php
/**
 * Tests for class PersonioIntegrationLight\Log.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in the class PersonioIntegrationLight\Log.
 */
class Log extends PersonioTestCase {

	/**
	 * Test to add a new successful import log entry in the database.
	 *
	 * @return void
	 */
	public function test_add_success_import(): void {
		// create a text example.
		$test_text = 'This is a test text.';

		// add the entry.
		\PersonioIntegrationLight\Log::get_instance()->add( $test_text, 'success', 'import' );

		// get the entry.
		$entries = \PersonioIntegrationLight\Log::get_instance()->get_entries();
		$found = false;
		foreach( $entries as $entry ) {
			if( $test_text === $entry['log'] ) {
				$found = true;
			}
		}

		$this->assertTrue( $found );
	}

	/**
	 * Test to add a new error log entry in the database.
	 *
	 * @return void
	 */
	public function test_add_error_entry(): void {
		// create a text example.
		$test_text = 'This is a test text.';

		// add the entry.
		\PersonioIntegrationLight\Log::get_instance()->add( $test_text, 'error', 'system' );

		// get the entry.
		$entries = \PersonioIntegrationLight\Log::get_instance()->get_entries();
		$found = false;
		foreach( $entries as $entry ) {
			if( $test_text === $entry['log'] ) {
				$found = true;
			}
		}

		$this->assertTrue( $found );
	}
}
