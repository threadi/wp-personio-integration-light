<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Personio_Accounts.
 *
 * @package personio-integration-light
 */

/**
 * Object to test functions in the class PersonioIntegrationLight\PersonioIntegration\Personio_Accounts.
 */
class Personio_Accounts extends WP_UnitTestCase {

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_name(): void {
		$name = \PersonioIntegrationLight\PersonioIntegration\Personio_Accounts::get_instance()->get_name();
		$this->assertIsString( $name );
		$this->assertNotEmpty( $name );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_label(): void {
		$label = \PersonioIntegrationLight\PersonioIntegration\Personio_Accounts::get_instance()->get_label();
		$this->assertIsString( $label );
		$this->assertNotEmpty( $label );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_is_enabled(): void {
		$enabled = \PersonioIntegrationLight\PersonioIntegration\Personio_Accounts::get_instance()->is_enabled();
		$this->assertIsBool( $enabled );
		$this->assertTrue( $enabled );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_category(): void {
		$category = \PersonioIntegrationLight\PersonioIntegration\Personio_Accounts::get_instance()->get_category();
		$this->assertIsString( $category );
		$this->assertNotEmpty( $category );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_description(): void {
		$description = \PersonioIntegrationLight\PersonioIntegration\Personio_Accounts::get_instance()->get_description();
		$this->assertIsString( $description );
		$this->assertNotEmpty( $description );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_is_active(): void {
		$is_active = \PersonioIntegrationLight\PersonioIntegration\Personio_Accounts::get_instance()->is_active();
		$this->assertIsBool( $is_active );
		$this->assertFalse( $is_active );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_personio_urls(): void {
		$urls = PersonioIntegrationLight\PersonioIntegration\Personio_Accounts::get_instance()->get_personio_urls();
		$this->assertIsArray( $urls);
		$this->assertNotEmpty( $urls );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_login_url(): void {
		$url = PersonioIntegrationLight\PersonioIntegration\Personio_Accounts::get_instance()->get_login_url();
		$this->assertIsString( $url );
		$this->assertNotEmpty( $url );
		$this->assertStringContainsString( 'personio', $url );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_personio_edit_link_not_set(): void {
		global $personio_positions;

		// bail if no positions were found.
		if( empty( $personio_positions ) ) {
			$this->markTestSkipped( 'No positions were found.' );
		}

		$url = PersonioIntegrationLight\PersonioIntegration\Personio_Accounts::get_instance()->get_personio_edit_link( $personio_positions[0] );
		$this->assertIsString( $url );
		$this->assertEmpty( $url );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_personio_edit_link(): void {
		global $personio_positions;

		update_option( 'personioIntegrationLoginUrl', 'https://example.com' );

		// bail if no positions were found.
		if( empty( $personio_positions ) ) {
			$this->markTestSkipped( 'No positions were found.' );
		}

		$url = PersonioIntegrationLight\PersonioIntegration\Personio_Accounts::get_instance()->get_personio_edit_link( $personio_positions[0] );
		$this->assertIsString( $url );
		$this->assertNotEmpty( $url );
	}
}
