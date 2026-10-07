<?php
/**
 * File to some test scenarios for one topic.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Scenarios;

use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to run some test scenarios for one topic.
 */
class DeletePositions extends PersonioTestCase {

	/**
	 * Prepare the test environment.
	 *
	 * @return void
	 */
	public function set_up(): void {
		parent::set_up();

		// mark that neither an import nor a deletion is running.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, 0 );

		// delete all positions to have a clean start.
		PersonioPosition::get_instance()->delete_positions();
	}

	/**
	 * Import the positions and return their amount.
	 *
	 * @return int
	 */
	private function import_positions(): int {
		update_option( 'personioIntegrationUrl', self::$personio_url );
		( new \PersonioIntegrationLight\PersonioIntegration\Imports\Xml() )->run();
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );
		return $this->count_positions();
	}

	/**
	 * Count all currently imported positions.
	 *
	 * @return int
	 */
	private function count_positions(): int {
		return count( \PersonioIntegrationLight\PersonioIntegration\Positions::get_instance()->get_positions( -1 ) );
	}

	/**
	 * Return whether the log contains an entry with the given text.
	 *
	 * @param string $text The text to search.
	 *
	 * @return bool
	 */
	private function is_logged( string $text ): bool {
		foreach ( \PersonioIntegrationLight\Log::get_instance()->get_entries() as $entry ) {
			if ( str_contains( $entry['log'], $text ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Return a fatal PHP error as it is returned by error_get_last().
	 *
	 * @param int    $type    The type of the error.
	 * @param string $message The message of the error.
	 *
	 * @return array{type:int,message:string,file:string,line:int}
	 */
	private function get_error( int $type, string $message ): array {
		return array(
			'type'    => $type,
			'message' => $message,
			'file'    => __FILE__,
			'line'    => __LINE__,
		);
	}

	/**
	 * Test to delete all positions.
	 *
	 * @return void
	 */
	public function test_delete_positions(): void {
		// import the positions.
		$this->assertGreaterThan( 0, $this->import_positions() );

		// delete them.
		PersonioPosition::get_instance()->delete_positions();

		// test it.
		$this->assertSame( 0, $this->count_positions() );
		$this->assertSame( 0, PersonioPosition::get_instance()->get_deletion_start_time() );
		$this->assertSame( array(), get_option( WP_PERSONIO_INTEGRATION_DELETE_ERRORS ) );
	}

	/**
	 * Test that a deletion resets the errors of a previous deletion.
	 *
	 * @return void
	 */
	public function test_delete_positions_resets_errors(): void {
		// set an error of a previous deletion.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_ERRORS, array( 'Error of a previous deletion.' ) );

		// delete the positions.
		PersonioPosition::get_instance()->delete_positions();

		// test it.
		$this->assertSame( array(), get_option( WP_PERSONIO_INTEGRATION_DELETE_ERRORS ) );
	}

	/**
	 * Test to delete all positions if an import is running.
	 *
	 * @return void
	 */
	public function test_delete_positions_if_import_is_running(): void {
		// import the positions.
		$count = $this->import_positions();
		$this->assertGreaterThan( 0, $count );

		// mark that an import is running.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, time() );

		// try to delete them.
		PersonioPosition::get_instance()->delete_positions();

		// test it.
		$this->assertSame( $count, $this->count_positions() );
	}

	/**
	 * Test to delete all positions if another deletion is still running.
	 *
	 * @return void
	 */
	public function test_delete_positions_if_other_deletion_is_running(): void {
		// import the positions.
		$count = $this->import_positions();
		$this->assertGreaterThan( 0, $count );

		// mark that a deletion is running for a minute.
		$started = time() - MINUTE_IN_SECONDS;
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, $started );

		// try to delete them.
		PersonioPosition::get_instance()->delete_positions();

		// test it: nothing is deleted and the other deletion is still marked as running.
		$this->assertSame( $count, $this->count_positions() );
		$this->assertSame( $started, PersonioPosition::get_instance()->get_deletion_start_time() );
	}

	/**
	 * Intended behavior: a deletion which is marked as running for more than an hour got stuck.
	 * It must not block further deletions forever, so the next deletion releases it.
	 *
	 * @return void
	 */
	public function test_delete_positions_releases_stuck_deletion(): void {
		// import the positions.
		$this->assertGreaterThan( 0, $this->import_positions() );

		// mark that a deletion is running for more than an hour.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, time() - HOUR_IN_SECONDS - MINUTE_IN_SECONDS );

		// delete them.
		PersonioPosition::get_instance()->delete_positions();

		// test it.
		$this->assertSame( 0, $this->count_positions() );
		$this->assertSame( 0, PersonioPosition::get_instance()->get_deletion_start_time() );

		// the release must show up in the log.
		$this->assertTrue( $this->is_logged( 'did not end properly' ) );
	}

	/**
	 * Test the detection of a deletion which got stuck.
	 *
	 * @return void
	 */
	public function test_is_deletion_stuck(): void {
		// no deletion is running.
		$this->assertFalse( PersonioPosition::get_instance()->is_deletion_stuck() );

		// a deletion is running for a minute.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, time() - MINUTE_IN_SECONDS );
		$this->assertFalse( PersonioPosition::get_instance()->is_deletion_stuck() );

		// a deletion is running for more than an hour.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, time() - HOUR_IN_SECONDS - MINUTE_IN_SECONDS );
		$this->assertTrue( PersonioPosition::get_instance()->is_deletion_stuck() );
	}

	/**
	 * Test that only a deletion which got stuck is released.
	 *
	 * @return void
	 */
	public function test_release_stuck_deletion(): void {
		// no deletion is running.
		$this->assertFalse( PersonioPosition::get_instance()->release_stuck_deletion() );

		// a deletion is running for a minute: it must stay marked as running.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, time() - MINUTE_IN_SECONDS );
		$this->assertFalse( PersonioPosition::get_instance()->release_stuck_deletion() );
		$this->assertGreaterThan( 0, PersonioPosition::get_instance()->get_deletion_start_time() );

		// a deletion is running for more than an hour: it must be released.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, time() - HOUR_IN_SECONDS - MINUTE_IN_SECONDS );
		$this->assertTrue( PersonioPosition::get_instance()->release_stuck_deletion() );
		$this->assertSame( 0, PersonioPosition::get_instance()->get_deletion_start_time() );

		// there is nothing to release anymore.
		$this->assertFalse( PersonioPosition::get_instance()->release_stuck_deletion() );
	}

	/**
	 * The shutdown handler must log a fatal error, save it for the progress in the backend and reset the running-flag.
	 *
	 * @return void
	 */
	public function test_shutdown_handler_logs_and_resets_running_flag(): void {
		// simulate a running deletion.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, time() );
		update_option( WP_PERSONIO_INTEGRATION_DELETE_ERRORS, array() );

		PersonioPosition::get_instance()->process_deletion_shutdown_error( $this->get_error( E_ERROR, 'Allowed memory size of 134217728 bytes exhausted' ) );

		// the running-flag must be reset.
		$this->assertSame( 0, absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING ) ) );

		// the error must be saved for the progress in the backend.
		$errors = get_option( WP_PERSONIO_INTEGRATION_DELETE_ERRORS );
		$this->assertIsArray( $errors );
		$this->assertCount( 1, $errors );
		$this->assertStringContainsString( 'Allowed memory size', $errors[0] );

		// the error must show up in the log.
		$this->assertTrue( $this->is_logged( 'Allowed memory size' ) );
	}

	/**
	 * The error for the progress in the backend must be escaped and must not contain the stack trace.
	 *
	 * @return void
	 */
	public function test_shutdown_handler_saves_only_the_first_line_of_the_error(): void {
		// simulate a running deletion.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, time() );

		PersonioPosition::get_instance()->process_deletion_shutdown_error( $this->get_error( E_ERROR, "Uncaught Error: <b>Something</b> failed\nStack trace:\n#0 /secret/path/file.php(1): test()" ) );

		// test it.
		$errors = get_option( WP_PERSONIO_INTEGRATION_DELETE_ERRORS );
		$this->assertCount( 1, $errors );
		$this->assertStringContainsString( 'Something', $errors[0] );
		$this->assertStringNotContainsString( '<b>', $errors[0] );
		$this->assertStringNotContainsString( 'Stack trace', $errors[0] );

		// the complete error must show up in the log.
		$this->assertTrue( $this->is_logged( 'Stack trace' ) );
	}

	/**
	 * Non-fatal errors (e.g. a warning) must NOT touch the running-flag.
	 *
	 * @return void
	 */
	public function test_shutdown_handler_ignores_non_fatal_errors(): void {
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, time() );
		update_option( WP_PERSONIO_INTEGRATION_DELETE_ERRORS, array() );

		PersonioPosition::get_instance()->process_deletion_shutdown_error( $this->get_error( E_WARNING, 'just a warning' ) );

		$this->assertGreaterThan( 0, absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING ) ) );
		$this->assertSame( array(), get_option( WP_PERSONIO_INTEGRATION_DELETE_ERRORS ) );
	}

	/**
	 * A null error (no fatal error occurred) must NOT touch the running-flag.
	 *
	 * @return void
	 */
	public function test_shutdown_handler_ignores_no_error(): void {
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, time() );

		PersonioPosition::get_instance()->process_deletion_shutdown_error( null );

		$this->assertGreaterThan( 0, absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING ) ) );
	}

	/**
	 * A fatal error after the deletion finished cleanly must NOT be saved as error of the deletion.
	 *
	 * @return void
	 */
	public function test_shutdown_handler_ignores_errors_after_deletion(): void {
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, 0 );
		update_option( WP_PERSONIO_INTEGRATION_DELETE_ERRORS, array() );

		PersonioPosition::get_instance()->process_deletion_shutdown_error( $this->get_error( E_ERROR, 'error in another part of the request' ) );

		$this->assertSame( array(), get_option( WP_PERSONIO_INTEGRATION_DELETE_ERRORS ) );
		$this->assertFalse( $this->is_logged( 'error in another part of the request' ) );
	}
}
