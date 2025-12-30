<?php
/**
 * Tests for class PersonioIntegrationLight\Plugin\Languages.
 *
 * @package personio-integration-light
 */

/**
 * Object to test functions in class PersonioIntegrationLight\Plugin\Languages.
 */
class Languages extends WP_UnitTestCase {

	/**
	 * Test if the active api is set to capito.
	 *
	 * @return void
	 */
	public function test_is_german_language(): void {
		// test 1: we check for the actual language.
		$is_german_language = \PersonioIntegrationLight\Plugin\Languages::get_instance()->is_german_language();
		$this->assertFalse( $is_german_language );

		// test 2: we check for german.
		switch_to_locale( 'de_DE' );
		$is_german_language = \PersonioIntegrationLight\Plugin\Languages::get_instance()->is_german_language();
		$this->assertTrue( $is_german_language );
	}

	/**
	 * Test if we get the active languages as an array and 'en' is in it.
	 *
	 * @return void
	 */
	public function test_get_active_languages(): void {
		$active_languages = \PersonioIntegrationLight\Plugin\Languages::get_instance()->get_active_languages();
		$this->assertIsArray( $active_languages );
		$this->assertArrayHasKey( 'en', $active_languages );
	}
}
