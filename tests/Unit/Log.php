<?php
/**
 * Tests for class PersonioIntegrationLight\Log.
 *
 * @package personio-integration-light
 */

/**
 * Object to test functions in the class PersonioIntegrationLight\Log.
 */
class Log extends WP_UnitTestCase {

	/**
	 * Test to add a new log entry in the database.
	 *
	 * @return void
	 */
	public function test_add(): void {
		// create a text example.
		$test_text = 'This is a test text.';

		// add the entry.
		\PersonioIntegrationLight\Log::get_instance()->add( $test_text, 'success', 'system' );

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
