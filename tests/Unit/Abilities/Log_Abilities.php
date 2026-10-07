<?php
/**
 * Tests for class PersonioIntegrationLight\Abilities\Log_Abilities.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Abilities;

use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\Tests\AbilitiesTestCase;
use WP_Error;

/**
 * Object to test functions in the class PersonioIntegrationLight\Abilities\Log_Abilities.
 */
class Log_Abilities extends AbilitiesTestCase {

	/**
	 * Prepare the test environment.
	 *
	 * @return void
	 */
	public function set_up(): void {
		parent::set_up();

		// the log is only available for administrators.
		$this->set_user( 'administrator' );
	}

	/**
	 * Return the texts of the entries in the given result of the ability.
	 *
	 * @param mixed $result The result.
	 *
	 * @return array<int,string>
	 */
	private function get_texts( mixed $result ): array {
		$this->assertIsArray( $result );
		return array_column( $result['entries'], 'text' );
	}

	/**
	 * Test that an editor is not allowed to read the log.
	 *
	 * @return void
	 */
	public function test_get_log_is_not_available_for_editor(): void {
		// use an editor.
		$this->set_user( 'editor' );

		// run the ability.
		$result = $this->run_ability( 'get-log', array() );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'ability_invalid_permissions', $result->get_error_code() );
	}

	/**
	 * Test to get the newest entries of the log.
	 *
	 * @return void
	 */
	public function test_get_log(): void {
		// add two entries.
		Log::get_instance()->add( 'First test entry.', 'success', 'import' );
		Log::get_instance()->add( 'Second test entry.', 'error', 'system' );

		// run the ability.
		$result = $this->run_ability( 'get-log', array() );

		// test it.
		$this->assertIsArray( $result );
		$this->assertSame( count( $result['entries'] ), $result['count'] );
		$this->assertLessThanOrEqual( 20, $result['count'] );

		// the newest entry must be the first one.
		$this->assertSame( 'Second test entry.', $result['entries'][0]['text'] );
		$this->assertSame( 'error', $result['entries'][0]['state'] );
		$this->assertSame( 'system', $result['entries'][0]['category'] );
		$this->assertSame( 'First test entry.', $result['entries'][1]['text'] );
		$this->assertSame( 'success', $result['entries'][1]['state'] );
		$this->assertSame( 'import', $result['entries'][1]['category'] );

		// the date must be given in ISO 8601 and must be the actual time.
		$this->assertMatchesRegularExpression( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+00:00$/', $result['entries'][0]['date'] );
		$this->assertEqualsWithDelta( time(), strtotime( $result['entries'][0]['date'] ), 60 );

		// the possible categories must be returned.
		$categories = array_column( $result['categories'], 'name' );
		$this->assertContains( 'import', $categories );
		$this->assertContains( 'system', $categories );
	}

	/**
	 * Test that the texts of the log are returned as plain text.
	 *
	 * @return void
	 */
	public function test_get_log_returns_plain_text(): void {
		// add an entry with HTML, as the plugin logs it.
		Log::get_instance()->add( 'Import failed for <a href="https://example.com">this URL</a>:<br><code>' . esc_html( 'Error "500" & more' ) . '</code>', 'error', 'import' );

		// run the ability.
		$texts = $this->get_texts( $this->run_ability( 'get-log', array( 'limit' => 1 ) ) );

		// test it.
		$this->assertSame( "Import failed for this URL:\nError \"500\" & more", $texts[0] );
	}

	/**
	 * Test to limit the amount of entries.
	 *
	 * @return void
	 */
	public function test_get_log_with_limit(): void {
		// add three entries.
		Log::get_instance()->add( 'Entry 1', 'error', 'system' );
		Log::get_instance()->add( 'Entry 2', 'error', 'system' );
		Log::get_instance()->add( 'Entry 3', 'error', 'system' );

		// run the ability.
		$result = $this->run_ability( 'get-log', array( 'limit' => 2 ) );

		// test it.
		$this->assertIsArray( $result );
		$this->assertSame( 2, $result['count'] );
		$this->assertSame( array( 'Entry 3', 'Entry 2' ), $this->get_texts( $result ) );
	}

	/**
	 * Return limits which are not allowed.
	 *
	 * @return array<string,array<int,mixed>>
	 */
	public function get_invalid_limits(): array {
		return array(
			'too low'   => array( 0 ),
			'too high'  => array( 101 ),
			'no number' => array( 'all' ),
		);
	}

	/**
	 * Test to get the log with a limit which is not allowed.
	 *
	 * @dataProvider get_invalid_limits
	 *
	 * @param mixed $limit The limit.
	 *
	 * @return void
	 */
	public function test_get_log_with_invalid_limit( mixed $limit ): void {
		// run the ability.
		$result = $this->run_ability( 'get-log', array( 'limit' => $limit ) );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'ability_invalid_input', $result->get_error_code() );
	}

	/**
	 * Test to get only the errors.
	 *
	 * @return void
	 */
	public function test_get_log_with_errors_only(): void {
		// add an error and a success entry.
		Log::get_instance()->add( 'Test error.', 'error', 'import' );
		Log::get_instance()->add( 'Test success.', 'success', 'import' );

		// run the ability.
		$result = $this->run_ability(
			'get-log',
			array(
				'errors_only' => true,
				'limit'       => 100,
			)
		);

		// test it.
		$this->assertIsArray( $result );
		$this->assertSame( array( 'error' ), array_values( array_unique( array_column( $result['entries'], 'state' ) ) ) );
		$this->assertSame( 'Test error.', $result['entries'][0]['text'] );
		$this->assertNotContains( 'Test success.', $this->get_texts( $result ) );
	}

	/**
	 * Test to get only the entries of one category.
	 *
	 * @return void
	 */
	public function test_get_log_with_category(): void {
		// add an entry in two categories.
		Log::get_instance()->add( 'Test import error.', 'error', 'import' );
		Log::get_instance()->add( 'Test system error.', 'error', 'system' );

		// run the ability.
		$result = $this->run_ability(
			'get-log',
			array(
				'category' => 'import',
				'limit'    => 100,
			)
		);

		// test it.
		$this->assertIsArray( $result );
		$this->assertSame( array( 'import' ), array_values( array_unique( array_column( $result['entries'], 'category' ) ) ) );
		$this->assertSame( 'Test import error.', $result['entries'][0]['text'] );
		$this->assertNotContains( 'Test system error.', $this->get_texts( $result ) );
	}

	/**
	 * Test to get only the errors of one category.
	 *
	 * @return void
	 */
	public function test_get_log_with_category_and_errors_only(): void {
		// add entries.
		Log::get_instance()->add( 'Test import error.', 'error', 'import' );
		Log::get_instance()->add( 'Test import success.', 'success', 'import' );
		Log::get_instance()->add( 'Test system error.', 'error', 'system' );

		// run the ability.
		$texts = $this->get_texts(
			$this->run_ability(
				'get-log',
				array(
					'category'    => 'import',
					'errors_only' => true,
					'limit'       => 100,
				)
			)
		);

		// test it.
		$this->assertContains( 'Test import error.', $texts );
		$this->assertNotContains( 'Test import success.', $texts );
		$this->assertNotContains( 'Test system error.', $texts );
	}

	/**
	 * Test to get the log for an unknown category.
	 *
	 * @return void
	 */
	public function test_get_log_with_unknown_category(): void {
		// run the ability.
		$result = $this->run_ability( 'get-log', array( 'category' => 'unknown' ) );

		// test it.
		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'personio_integration_unknown_log_category', $result->get_error_code() );
	}

	/**
	 * Test that reading the log does not change the filters for the log table in the backend.
	 *
	 * @return void
	 */
	public function test_get_log_removes_its_filters(): void {
		// run the ability with all filters.
		$this->run_ability(
			'get-log',
			array(
				'category'    => 'import',
				'errors_only' => true,
				'limit'       => 1,
			)
		);

		// test it.
		$this->assertFalse( has_filter( 'personio_integration_light_log_limit' ) );
		$this->assertFalse( has_filter( 'personio_integration_light_log_category' ) );
		$this->assertFalse( has_filter( 'personio_integration_light_log_md5' ) );
		$this->assertFalse( has_filter( 'personio_integration_light_log_errors' ) );
	}

	/**
	 * Test that the log documents what the abilities for the positions do.
	 *
	 * @return void
	 */
	public function test_get_log_contains_import_and_deletion(): void {
		// run a failing import, a successful import and delete the positions.
		update_option( 'personioIntegrationUrl', self::$personio_faulty_url );
		$this->run_ability( 'run-import', array( 'dry_run' => false ) );
		update_option( 'personioIntegrationUrl', self::$personio_url );
		$this->run_ability( 'run-import', array( 'dry_run' => false ) );
		$this->run_ability( 'delete-positions', array( 'dry_run' => false ) );

		// run the ability.
		$log = implode( "\n", $this->get_texts( $this->run_ability( 'get-log', array( 'limit' => 100 ) ) ) );

		// test it.
		$this->assertStringContainsString( 'could not be read', $log );
		$this->assertStringContainsString( 'positions imported from Personio account', $log );
		$this->assertStringContainsString( 'Positions has been deleted by', $log );
	}
}
