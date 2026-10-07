<?php
/**
 * Tests for class PersonioIntegrationLight\Abilities\Import_Abilities.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Abilities;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Tests\AbilitiesTestCase;
use RuntimeException;
use WP_Error;

/**
 * Object to test functions in the class PersonioIntegrationLight\Abilities\Import_Abilities.
 */
class Import_Abilities extends AbilitiesTestCase {

	/**
	 * Prepare the test environment.
	 *
	 * @return void
	 */
	public function set_up(): void {
		parent::set_up();

		// the import and the deletion of positions are only allowed for administrators.
		$this->set_user( 'administrator' );
	}

	/**
	 * Return the abilities which change something, with the input to really run them.
	 *
	 * @return array<string,array<int,string>>
	 */
	public function get_changing_abilities(): array {
		return array(
			'run-import'       => array( 'run-import' ),
			'cancel-import'    => array( 'cancel-import' ),
			'delete-positions' => array( 'delete-positions' ),
		);
	}

	/**
	 * Test that an editor is not allowed to run the abilities.
	 *
	 * @dataProvider get_changing_abilities
	 *
	 * @param string $name The name of the ability.
	 *
	 * @return void
	 */
	public function test_ability_is_not_available_for_editor( string $name ): void {
		// import the positions and mark an import as running, so each ability would have something to do.
		$count = $this->import_positions();
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, time() - 2 * HOUR_IN_SECONDS );

		// use an editor.
		$this->set_user( 'editor' );

		// run the ability.
		$result = $this->run_ability( $name, array( 'dry_run' => false ) );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'ability_invalid_permissions', $result->get_error_code() );
		$this->assertSame( $count, $this->count_positions() );
		$this->assertGreaterThan( 0, absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING ) ) );
	}

	/**
	 * Test that the abilities do nothing if they are called without input.
	 *
	 * @dataProvider get_changing_abilities
	 *
	 * @param string $name The name of the ability.
	 *
	 * @return void
	 */
	public function test_ability_is_a_dry_run_by_default( string $name ): void {
		// run the ability without any input and with an empty input.
		foreach ( array( null, array() ) as $input ) {
			$result = $this->run_ability( $name, $input );

			// test it.
			$this->assertIsArray( $result );
			$this->assertTrue( $result['dry_run'] );
			$this->assertFalse( $result['done'] );
		}
	}

	/**
	 * Test that the marker for a dry run must be a boolean.
	 *
	 * @dataProvider get_changing_abilities
	 *
	 * @param string $name The name of the ability.
	 *
	 * @return void
	 */
	public function test_ability_with_invalid_dry_run( string $name ): void {
		// run the ability.
		$result = $this->run_ability( $name, array( 'dry_run' => 'no' ) );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'ability_invalid_input', $result->get_error_code() );
	}

	/**
	 * Test to check whether the import can be run.
	 *
	 * @return void
	 */
	public function test_run_import_as_dry_run(): void {
		// run the ability.
		$result = $this->run_ability( 'run-import', array( 'dry_run' => true ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertTrue( $result['dry_run'] );
		$this->assertFalse( $result['done'] );
		$this->assertSame( 'import', $result['action'] );
		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( 'xml_import', $result['import_type'] );
		$this->assertSame( array( self::$personio_url ), $result['personio_urls'] );
		$this->assertNotEmpty( $result['languages'] );
		$this->assertNotEmpty( $result['message'] );

		// nothing must have been imported.
		$this->assertSame( 0, $this->count_positions() );
	}

	/**
	 * Test to run an import of positions.
	 *
	 * @return void
	 */
	public function test_run_import(): void {
		// run the ability.
		$result = $this->run_ability( 'run-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['dry_run'] );
		$this->assertTrue( $result['done'] );
		$this->assertSame( 'import', $result['action'] );
		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( 0, $result['positions_before'] );
		$this->assertGreaterThan( 0, $result['position_count'] );
		$this->assertSame( $this->count_positions(), $result['position_count'] );
		$this->assertSame( 0, $result['deleted_positions'] );
		$this->assertSame( $result['position_count'], $result['new_positions'] );
		$this->assertNotEmpty( $result['message'] );

		// the import must not be marked as running anymore.
		$this->assertSame( 0, absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING ) ) );
	}

	/**
	 * Test to run an import of positions twice: the second one changes nothing.
	 *
	 * @return void
	 */
	public function test_run_import_twice(): void {
		// run the ability twice.
		$first  = $this->run_ability( 'run-import', array( 'dry_run' => false ) );
		$second = $this->run_ability( 'run-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $second );
		$this->assertTrue( $second['done'] );
		$this->assertSame( $first['position_count'], $first['new_positions'] );
		$this->assertSame( $first['position_count'], $second['positions_before'] );
		$this->assertSame( $first['position_count'], $second['position_count'] );
		$this->assertSame( 0, $second['new_positions'] );
		$this->assertSame( 0, $second['deleted_positions'] );
	}

	/**
	 * Test to run an import of positions without a Personio URL.
	 *
	 * @return void
	 */
	public function test_run_import_without_personio_url(): void {
		// remove the Personio URL.
		update_option( 'personioIntegrationUrl', '' );

		// reset the list of errors, to see that a refused import does not touch it.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, array() );

		// run the ability.
		$result = $this->run_ability( 'run-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertSame( 'none', $result['action'] );
		$this->assertCount( 1, $result['errors'] );
		$this->assertSame( array(), $result['personio_urls'] );

		// the refused import must not be documented as failed import.
		$this->assertSame( array(), get_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS ) );
		$this->assertSame( 0, absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING ) ) );
	}

	/**
	 * Test to run an import of positions without an enabled import extension.
	 *
	 * @return void
	 */
	public function test_run_import_without_import_extension(): void {
		// disable the import extension.
		update_option( 'personioIntegrationImportVersion', '' );

		// run the ability.
		$result = $this->run_ability( 'run-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertSame( 'none', $result['action'] );
		$this->assertSame( '', $result['import_type'] );
		$this->assertNotEmpty( $result['errors'] );
		$this->assertSame( 0, $this->count_positions() );
	}

	/**
	 * Test to run import of positions if other import is still running.
	 *
	 * @return void
	 */
	public function test_run_import_if_other_import_is_running(): void {
		// mark that an import is already running.
		$started = time() - 60;
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, $started );

		// run the ability.
		$result = $this->run_ability( 'run-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertSame( 'none', $result['action'] );
		$this->assertCount( 1, $result['errors'] );
		$this->assertStringContainsString( 'get-import-status', $result['errors'][0] );
		$this->assertSame( 0, $this->count_positions() );

		// the marker of the other import must be untouched.
		$this->assertSame( $started, absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING ) ) );
	}

	/**
	 * Test to run import of positions if other import is stuck: the result must name the way out.
	 *
	 * @return void
	 */
	public function test_run_import_if_other_import_is_stuck(): void {
		// mark that an import is running for two hours.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, time() - 2 * HOUR_IN_SECONDS );

		// run the ability.
		$result = $this->run_ability( 'run-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertCount( 1, $result['errors'] );
		$this->assertStringContainsString( 'cancel-import', $result['errors'][0] );
	}

	/**
	 * Test to run import of positions while all positions are deleted.
	 *
	 * @return void
	 */
	public function test_run_import_if_deletion_is_running(): void {
		// mark that a deletion is running.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, time() - 60 );

		// run the ability.
		$result = $this->run_ability( 'run-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertSame( 'none', $result['action'] );
		$this->assertCount( 1, $result['errors'] );
	}

	/**
	 * Test that a deletion which got stuck does not prevent the import.
	 *
	 * @return void
	 */
	public function test_run_import_if_deletion_is_stuck(): void {
		// mark that a deletion is running for two hours.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, time() - 2 * HOUR_IN_SECONDS );

		// run the ability.
		$result = $this->run_ability( 'run-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertTrue( $result['done'] );
		$this->assertGreaterThan( 0, $result['position_count'] );
	}

	/**
	 * Test to prevent the import with the hook for it.
	 *
	 * @return void
	 */
	public function test_run_import_with_custom_blocker(): void {
		// add a reason why the import can not be run.
		add_filter(
			'personio_integration_ability_import_blockers',
			function ( array $blockers ) {
				$blockers[] = '<strong>Maintenance</strong> is running.';
				return $blockers;
			}
		);

		// run the ability.
		$result = $this->run_ability( 'run-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertSame( array( 'Maintenance is running.' ), $result['errors'] );
		$this->assertSame( 0, $this->count_positions() );
	}

	/**
	 * Return the Personio URLs which result in a failed import.
	 *
	 * @return array<string,array<int,string>>
	 */
	public function get_failing_urls(): array {
		return array(
			'faulty XML' => array( self::$personio_faulty_url ),
			'HTTP error' => array( self::$personio_error_url ),
		);
	}

	/**
	 * Test to run import of positions which errors during it: it must be reported and no position may be deleted.
	 *
	 * @dataProvider get_failing_urls
	 *
	 * @param string $url The Personio URL.
	 *
	 * @return void
	 */
	public function test_run_import_with_errors( string $url ): void {
		// import the valid positions.
		$count = $this->import_positions();
		$this->assertGreaterThan( 0, $count );

		// use the failing Personio URL.
		update_option( 'personioIntegrationUrl', $url );

		// run the ability.
		$result = $this->run_ability( 'run-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertNotEmpty( $result['errors'] );
		$this->assertSame( $count, $result['position_count'] );
		$this->assertSame( 0, $result['deleted_positions'] );
		$this->assertSame( $count, $this->count_positions() );

		// the errors must be plain text.
		foreach ( $result['errors'] as $error ) {
			$this->assertSame( wp_strip_all_tags( $error ), $error );
		}

		// the import must not be marked as running anymore.
		$this->assertSame( 0, absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING ) ) );
	}

	/**
	 * Test that a failed import does not influence the following import in the same process.
	 *
	 * @return void
	 */
	public function test_run_import_after_failed_import(): void {
		// run the ability with the faulty Personio URL.
		update_option( 'personioIntegrationUrl', self::$personio_faulty_url );
		$failed = $this->run_ability( 'run-import', array( 'dry_run' => false ) );
		$this->assertFalse( $failed['done'] );

		// run the ability with the valid Personio URL.
		update_option( 'personioIntegrationUrl', self::$personio_url );
		$result = $this->run_ability( 'run-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertTrue( $result['done'] );
		$this->assertSame( array(), $result['errors'] );
		$this->assertGreaterThan( 0, $result['position_count'] );
	}

	/**
	 * Test that an empty feed removes the positions and that they are counted.
	 *
	 * @return void
	 */
	public function test_run_import_of_empty_xml(): void {
		// import the valid positions.
		$count = $this->import_positions();
		$this->assertGreaterThan( 0, $count );

		// use the Personio URL without positions.
		update_option( 'personioIntegrationUrl', self::$personio_empty_url );

		// run the ability.
		$result = $this->run_ability( 'run-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertTrue( $result['done'] );
		$this->assertSame( $count, $result['positions_before'] );
		$this->assertSame( 0, $result['position_count'] );
		$this->assertSame( $count, $result['deleted_positions'] );
		$this->assertSame( 0, $this->count_positions() );
	}

	/**
	 * Test that the import writes no output for WP CLI, if it is run by an ability in a WP CLI process.
	 *
	 * @return void
	 */
	public function test_run_import_is_not_handled_as_wp_cli(): void {
		// let the plugin detect this process as WP CLI.
		add_filter( 'personio_integration_light_is_cli', '__return_true' );
		$this->assertTrue( Helper::is_cli() );

		// collect the detection during the import.
		$is_cli_during_import = null;
		add_action(
			'personio_integration_import_starting',
			function () use ( &$is_cli_during_import ) {
				$is_cli_during_import = Helper::is_cli();
			}
		);

		// run the ability.
		$result = $this->run_ability( 'run-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertTrue( $result['done'] );
		$this->assertFalse( $is_cli_during_import );

		// the detection must be restored after the import.
		$this->assertTrue( Helper::is_cli() );
	}

	/**
	 * Test to cancel an import if none is running.
	 *
	 * @return void
	 */
	public function test_cancel_import_if_no_import_is_running(): void {
		// run the ability.
		$result = $this->run_ability( 'cancel-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertSame( 'none', $result['action'] );
		$this->assertSame( '', $result['started_at'] );
		$this->assertSame( 0, $result['running_seconds'] );
	}

	/**
	 * Test to check what cancelling an import would do.
	 *
	 * @return void
	 */
	public function test_cancel_import_as_dry_run(): void {
		// mark that an import is running for a minute.
		$started = time() - 60;
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, $started );

		// run the ability.
		$result = $this->run_ability( 'cancel-import', array( 'dry_run' => true ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertSame( 'cancel', $result['action'] );
		$this->assertSame( gmdate( 'c', $started ), $result['started_at'] );
		$this->assertGreaterThanOrEqual( 60, $result['running_seconds'] );

		// it must warn, as this import might still be working.
		$this->assertCount( 1, $result['warnings'] );

		// the marker of the import must be untouched.
		$this->assertSame( $started, absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING ) ) );
	}

	/**
	 * Test that there is no warning to cancel an import which is running for more than an hour.
	 *
	 * @return void
	 */
	public function test_cancel_import_which_is_stuck_as_dry_run(): void {
		// mark that an import is running for two hours.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, time() - 2 * HOUR_IN_SECONDS );

		// run the ability.
		$result = $this->run_ability( 'cancel-import', array( 'dry_run' => true ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertSame( 'cancel', $result['action'] );
		$this->assertSame( array(), $result['warnings'] );
	}

	/**
	 * Test to cancel a running import and to start a new one afterwards.
	 *
	 * @return void
	 */
	public function test_cancel_import(): void {
		// mark that an import is running for two hours.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, time() - 2 * HOUR_IN_SECONDS );

		// run the ability.
		$result = $this->run_ability( 'cancel-import', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertTrue( $result['done'] );
		$this->assertSame( 'cancel', $result['action'] );
		$this->assertSame( 0, absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING ) ) );

		// a new import must be possible now.
		$import = $this->run_ability( 'run-import', array( 'dry_run' => false ) );
		$this->assertTrue( $import['done'] );
	}

	/**
	 * Test to check what the deletion of all positions would do.
	 *
	 * @return void
	 */
	public function test_delete_positions_as_dry_run(): void {
		// import the positions.
		$count = $this->import_positions();
		$this->assertGreaterThan( 0, $count );

		// run the ability.
		$result = $this->run_ability( 'delete-positions', array( 'dry_run' => true ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertSame( 'delete', $result['action'] );
		$this->assertSame( $count, $result['positions_before'] );
		$this->assertSame( $count, $result['position_count'] );
		$this->assertSame( 0, $result['deleted_positions'] );
		$this->assertSame( array(), $result['errors'] );

		// nothing must have been deleted.
		$this->assertSame( $count, $this->count_positions() );
	}

	/**
	 * Test to delete all positions.
	 *
	 * @return void
	 */
	public function test_delete_positions(): void {
		// import the positions.
		$count = $this->import_positions();
		$this->assertGreaterThan( 0, $count );

		// run the ability.
		$result = $this->run_ability( 'delete-positions', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertTrue( $result['done'] );
		$this->assertSame( 'delete', $result['action'] );
		$this->assertSame( $count, $result['positions_before'] );
		$this->assertSame( 0, $result['position_count'] );
		$this->assertSame( $count, $result['deleted_positions'] );
		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( 0, $this->count_positions() );

		// the deletion must not be marked as running anymore.
		$this->assertSame( 0, absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING ) ) );

		// the positions must be imported again by the next import.
		$import = $this->run_ability( 'run-import', array( 'dry_run' => false ) );
		$this->assertTrue( $import['done'] );
		$this->assertSame( $count, $import['position_count'] );
	}

	/**
	 * Test to delete all positions if there are none.
	 *
	 * @return void
	 */
	public function test_delete_positions_without_positions(): void {
		// run the ability.
		$result = $this->run_ability( 'delete-positions', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertSame( 'none', $result['action'] );
		$this->assertSame( array(), $result['errors'] );
	}

	/**
	 * Return the markers which prevent the deletion of all positions.
	 *
	 * @return array<string,array<int,string>>
	 */
	public function get_running_markers(): array {
		return array(
			'import is running'   => array( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING ),
			'deletion is running' => array( WP_PERSONIO_INTEGRATION_DELETE_RUNNING ),
		);
	}

	/**
	 * Test to delete all positions while an import or another deletion is running.
	 *
	 * @dataProvider get_running_markers
	 *
	 * @param string $marker The name of the option which marks the running task.
	 *
	 * @return void
	 */
	public function test_delete_positions_if_other_task_is_running( string $marker ): void {
		// import the positions.
		$count = $this->import_positions();
		$this->assertGreaterThan( 0, $count );

		// mark that the task is running for a minute.
		$started = time() - 60;
		update_option( $marker, $started );

		// run the ability.
		$result = $this->run_ability( 'delete-positions', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertSame( 'none', $result['action'] );
		$this->assertCount( 1, $result['errors'] );
		$this->assertSame( $count, $this->count_positions() );

		// the marker of the other task must be untouched.
		$this->assertSame( $started, absint( get_option( $marker ) ) );
	}

	/**
	 * Test to delete all positions if a previous deletion got stuck.
	 *
	 * @return void
	 */
	public function test_delete_positions_if_other_deletion_is_stuck(): void {
		// import the positions.
		$count = $this->import_positions();
		$this->assertGreaterThan( 0, $count );

		// mark that a deletion is running for two hours.
		update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, time() - 2 * HOUR_IN_SECONDS );

		// the check must announce that the stuck deletion will be released, without releasing it.
		$check = $this->run_ability( 'delete-positions', array( 'dry_run' => true ) );
		$this->assertSame( 'delete', $check['action'] );
		$this->assertCount( 1, $check['warnings'] );
		$this->assertGreaterThan( 0, absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING ) ) );

		// run the ability.
		$result = $this->run_ability( 'delete-positions', array( 'dry_run' => false ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertTrue( $result['done'] );
		$this->assertSame( $count, $result['deleted_positions'] );
		$this->assertCount( 1, $result['warnings'] );
		$this->assertSame( 0, $this->count_positions() );
		$this->assertSame( 0, absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING ) ) );
	}

	/**
	 * Test to delete all positions if the deletion breaks up: it must be reported and must not stay marked as running.
	 *
	 * @return void
	 */
	public function test_delete_positions_with_error(): void {
		// import the positions.
		$count = $this->import_positions();
		$this->assertGreaterThan( 2, $count );

		// let the deletion of the third position fail.
		$deleted = 0;
		$breaker = function () use ( &$deleted ) {
			++$deleted;
			if ( 3 === $deleted ) {
				throw new RuntimeException( 'Test error during deletion' );
			}
		};
		add_action( 'before_delete_post', $breaker );

		// run the ability.
		$result = $this->run_ability( 'delete-positions', array( 'dry_run' => false ) );

		// do not let any further deletion fail.
		remove_action( 'before_delete_post', $breaker );

		// test it.
		$this->assertIsArray( $result );
		$this->assertFalse( $result['done'] );
		$this->assertCount( 1, $result['errors'] );
		$this->assertStringContainsString( 'Test error during deletion', $result['errors'][0] );
		$this->assertSame( 2, $result['deleted_positions'] );
		$this->assertSame( $count - 2, $result['position_count'] );

		// the deletion must not be marked as running anymore.
		$this->assertSame( 0, absint( get_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING ) ) );

		// the next deletion must work.
		$second = $this->run_ability( 'delete-positions', array( 'dry_run' => false ) );
		$this->assertTrue( $second['done'] );
		$this->assertSame( 0, $this->count_positions() );
	}
}
