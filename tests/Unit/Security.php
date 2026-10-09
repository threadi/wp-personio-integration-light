<?php
/**
 * Security regression tests.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit;

use PersonioIntegrationLight\Plugin\Lock;
use PersonioIntegrationLight\Tests\PersonioTestCase;
use WP_Query;

/**
 * Object to test security relevant functions (SQL injection, path traversal, locks).
 */
class Security extends PersonioTestCase {

	/**
	 * The option name used for the lock tests.
	 *
	 * @var string
	 */
	private const LOCK_OPTION = 'personio_integration_test_lock';

	/**
	 * Clean up after each test.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		// reset the simulated admin screen.
		unset( $GLOBALS['current_screen'] );

		// remove the test lock.
		delete_option( self::LOCK_OPTION );

		parent::tear_down();
	}

	/**
	 * Simulate a request in the backend via the current screen.
	 *
	 * @param string $screen The screen ID.
	 *
	 * @return void
	 */
	private function simulate_admin( string $screen ): void {
		if ( ! function_exists( 'set_current_screen' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
			require_once ABSPATH . 'wp-admin/includes/screen.php';
		}
		set_current_screen( $screen );
	}

	/**
	 * Test that the backend search escapes the search term (no SQL injection).
	 *
	 * @return void
	 */
	public function test_backend_search_escapes_search_term(): void {
		// simulate the backend list of positions.
		$this->simulate_admin( 'edit-personioposition' );
		$this->assertTrue( is_admin() );

		// prepare a query with a malicious search term.
		$payload  = "') OR SLEEP(5) OR ('";
		$wp_query = new WP_Query();
		$wp_query->set( 's', $payload );
		$wp_query->set( 'post_type', \PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition::get_instance()->get_name() );

		// get the resulting SQL.
		$sql = \PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition::get_instance()->search_also_in_meta_fields( ' AND (1=1) ', $wp_query );

		// the search must have been extended.
		$this->assertIsString( $sql );
		$this->assertStringContainsString( 'SLEEP(5)', $sql );

		// the quotes of the payload must be escaped.
		$this->assertStringContainsString( "\\'", $sql );

		// there must be no unescaped quote which closes the string before the payload.
		$this->assertSame( 0, preg_match( "/(?<!\\\\)'\\) OR SLEEP\\(5\\)/", $sql ) );
	}

	/**
	 * Test that template names with paths are rejected.
	 *
	 * @return void
	 */
	public function test_get_valid_template_name_rejects_path_traversal(): void {
		$this->assertSame( 'default', \PersonioIntegrationLight\Plugin\Templates::get_instance()->get_valid_template_name( '../../index', 'parts/jobdescription' ) );
	}

	/**
	 * Test that templates with path traversal are not resolved.
	 *
	 * @return void
	 */
	public function test_get_template_rejects_path_traversal(): void {
		$this->assertSame( '', \PersonioIntegrationLight\Plugin\Templates::get_instance()->get_template( '../x.php' ) );
	}

	/**
	 * Test that a lock can only be acquired once.
	 *
	 * @return void
	 */
	public function test_lock_can_only_be_acquired_once(): void {
		delete_option( self::LOCK_OPTION );

		// first process gets the lock.
		$token = Lock::acquire( self::LOCK_OPTION );
		$this->assertGreaterThan( 0, $token );

		// second process does not get it.
		$this->assertSame( 0, Lock::acquire( self::LOCK_OPTION ) );

		// clean up.
		$this->assertTrue( Lock::release( self::LOCK_OPTION, $token ) );
	}

	/**
	 * Test that a lock can only be released with its own token.
	 *
	 * @return void
	 */
	public function test_lock_release_requires_the_right_token(): void {
		delete_option( self::LOCK_OPTION );

		// get the lock.
		$token = Lock::acquire( self::LOCK_OPTION );
		$this->assertGreaterThan( 0, $token );

		// release with wrong token fails.
		$this->assertFalse( Lock::release( self::LOCK_OPTION, $token + 1 ) );
		$this->assertFalse( Lock::release( self::LOCK_OPTION, 0 ) );
		$this->assertSame( $token, Lock::get_start_time( self::LOCK_OPTION ) );

		// release with the right token works.
		$this->assertTrue( Lock::release( self::LOCK_OPTION, $token ) );
		$this->assertSame( 0, Lock::get_start_time( self::LOCK_OPTION ) );
	}

	/**
	 * Test that a stale lock is taken over.
	 *
	 * @return void
	 */
	public function test_stale_lock_is_taken_over(): void {
		// simulate a lock of a process which has been killed 2 hours ago.
		update_option( self::LOCK_OPTION, time() - 2 * HOUR_IN_SECONDS, false );

		// the lock must be taken over.
		$token = Lock::acquire( self::LOCK_OPTION );
		$this->assertGreaterThan( 0, $token );
		$this->assertSame( $token, Lock::get_start_time( self::LOCK_OPTION ) );

		// clean up.
		$this->assertTrue( Lock::release( self::LOCK_OPTION, $token ) );
	}
}
