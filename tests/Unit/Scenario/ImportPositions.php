<?php
/**
 * File to some test scenarios for one topic.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Scenario;

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

		// delete all positions to have a clean start.
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
	 * Test to run import of positions without the "last-modified" header from Personio.
	 *
	 * @return void
	 */
	public function test_run_xml_import_without_last_modified(): void {
		// use the global handler.
		$position_obj = self::get_single_position( 'without_lm' );

		// test it.
		$this->assertIsObject( $position_obj );
		$this->assertInstanceOf( '\PersonioIntegrationLight\PersonioIntegration\Position', $position_obj );
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

	/**
	 * Run one XML-import against a given Personio URL (unconditionally).
	 */
	private function run_import_for_url( string $url ): void {
		update_option( 'personioIntegrationUrl', $url );
		( new \PersonioIntegrationLight\PersonioIntegration\Imports\Xml() )->run();
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );
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
	 * Intended behavior: an empty feed after having positions removes them all.
	 * This test locks the intent in, so it cannot be "fixed" away by accident.
	 *
	 * @return void
	 */
	public function test_empty_feed_deletes_all_positions(): void {
		$this->run_import_for_url( self::$personio_url );
		$this->assertGreaterThan( 0, $this->count_positions() );

		$this->run_import_for_url( self::$personio_empty_url );
		$this->assertSame( 0, $this->count_positions() );
	}

	/**
	 * Safety guard: a broken/faulty feed must NOT delete existing positions.
	 * Currently only "faulty feed creates nothing" is covered, not "faulty feed deletes nothing".
	 *
	 * @return void
	 */
	public function test_error_feed_does_not_delete_positions(): void {
		$this->run_import_for_url( self::$personio_url );
		$count_before = $this->count_positions();
		$this->assertGreaterThan( 0, $count_before );

		$this->run_import_for_url( self::$personio_faulty_url );
		$this->assertSame( $count_before, $this->count_positions() );
	}

	/**
	 * Safety guard: a Personio outage (HTTP 503) must NOT delete existing positions.
	 *
	 * @return void
	 */
	public function test_http_error_does_not_delete_positions(): void {
		$this->run_import_for_url( self::$personio_url );
		$count_before = $this->count_positions();
		$this->assertGreaterThan( 0, $count_before );

		$this->run_import_for_url( self::$personio_error_url );
		$this->assertSame( $count_before, $this->count_positions() );
	}

	/**
	 * A position imported in one language must survive an empty feed in another language.
	 *
	 * The cleanup runs once after all languages; the "de" (empty) feed must not delete
	 * the positions that "en" just imported and flagged as updated.
	 *
	 * @return void
	 */
	public function test_empty_feed_in_one_language_keeps_positions_from_another(): void {
		// baseline: the main language "en" only, from the multilingual feed.
		update_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, 'en' );
		update_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION, array( 'en' => 1 ) );
		$this->run_import_for_url( self::$personio_multilang_url );

		$baseline = $this->count_positions();
		$this->assertGreaterThan( 0, $baseline );

		// now also activate "de", whose feed is empty, and re-import.
		update_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION, array( 'en' => 1, 'de' => 1 ) );
		$this->run_import_for_url( self::$personio_multilang_url );

		// the empty "de" feed must not remove any "en" position.
		$this->assertSame( $baseline, $this->count_positions() );
	}
}
