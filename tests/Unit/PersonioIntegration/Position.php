<?php
/**
 * Tests for class PersonioIntegrationLight\PersonioIntegration\Position.
 *
 * @package personio-integration-light
 */

/**
 * Object to test functions in the class PersonioIntegrationLight\PersonioIntegration\Position.
 */
class Position extends WP_UnitTestCase {

	/**
	 * The object.
	 *
	 * @var \PersonioIntegrationLight\PersonioIntegration\Position
	 */
	private \PersonioIntegrationLight\PersonioIntegration\Position $object;

	/**
	 * Prepare the test environment.
	 *
	 * @return void
	 */
	public function setUp(): void {
		global $personio_positions;

		// set the first position as the object.
		$this->object = $personio_positions[0];
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_title(): void {
		$title = $this->object->get_title();
		$this->assertIsString( $title );
		$this->assertNotEmpty( $title );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_personio_id(): void {
		$personio_id = $this->object->get_personio_id();
		$this->assertIsString( $personio_id );
		$this->assertNotEmpty( $personio_id );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_excerpt(): void {
		$excerpt = $this->object->get_excerpt();
		$this->assertIsString( $excerpt );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_is_valid(): void {
		$is_valid = $this->object->is_valid();
		$this->assertIsBool( $is_valid );
		$this->assertTrue( $is_valid );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_id(): void {
		$id = $this->object->get_id();
		$this->assertIsInt( $id );
		$this->assertGreaterThan( 0, $id );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_link(): void {
		$url = $this->object->get_link();
		$this->assertIsString( $url );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_created_at(): void {
		$date = $this->object->get_created_at();
		$this->assertIsString( $date );
		$this->assertNotEmpty( $date );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_lang(): void {
		$lang = $this->object->get_lang();
		$this->assertIsString( $lang );
		$this->assertNotEmpty( $lang );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_content(): void {
		$content = $this->object->get_content();
		$this->assertIsArray( $content );
		$this->assertNotEmpty( $content );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_content_as_array(): void {
		$content = $this->object->get_content();
		$this->assertIsArray( $content );
		$this->assertNotEmpty( $content );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_application_url_with_hash(): void {
		$content = $this->object->get_application_url();
		$this->assertIsString( $content );
		$this->assertNotEmpty( $content );
		$this->assertStringContainsString( '#apply', $content );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_application_url_without_hash(): void {
		$content = $this->object->get_application_url( true );
		$this->assertIsString( $content );
		$this->assertNotEmpty( $content );
		$this->assertStringNotContainsString( '#apply', $content );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_settings(): void {
		$content = $this->object->get_settings();
		$this->assertIsArray( $content );
		$this->assertNotEmpty( $content );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_setting(): void {
		$content = $this->object->get_setting( 'ID' );
		$this->assertIsString( $content );
		$this->assertNotEmpty( $content );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_not_existing_setting(): void {
		$content = $this->object->get_setting( 'example' );
		$this->assertIsString( $content );
		$this->assertEmpty( $content );
	}

	/**
	 * Test if the returning variable is a boolean.
	 *
	 * Hint: false is the right result during running the unit test.
	 *
	 * @return void
	 */
	public function test_is_visible(): void {
		$is_visible = $this->object->is_visible();
		$this->assertIsBool( $is_visible );
		$this->assertFalse( $is_visible );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_get_term_by_field(): void {
		$term = $this->object->get_term_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES, 'name' );
		$this->assertIsString( $term );
	}

	/**
	 * Test if the returning variable is an array.
	 *
	 * @return void
	 */
	public function test_get_terms_by_field(): void {
		$terms = $this->object->get_terms_by_field( WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES );
		$this->assertIsArray( $terms );
	}
}
