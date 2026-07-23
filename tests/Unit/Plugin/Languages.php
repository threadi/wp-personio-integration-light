<?php
/**
 * Tests for class PersonioIntegrationLight\Plugin\Languages.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Plugin;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in class PersonioIntegrationLight\Plugin\Languages.
 */
class Languages extends PersonioTestCase {

	/**
	 * Test if the language is set to german.
	 *
	 * @return void
	 */
	public function test_is_german_language(): void {
		switch_to_locale( 'de_DE' );
		$is_german_language = \PersonioIntegrationLight\Plugin\Languages::get_instance()->is_german_language();
		$this->assertTrue( $is_german_language );
	}

	/**
	 * Test if the language is not set to german.
	 *
	 * @return void
	 */
	public function test_is_not_german_language(): void {
		switch_to_locale( 'en_US' );
		$is_german_language = \PersonioIntegrationLight\Plugin\Languages::get_instance()->is_german_language();
		$this->assertFalse( $is_german_language );
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

	/**
	 * Test if the main language survives a missing mapping.
	 *
	 * @return void
	 */
	public function test_main_language_survives_missing_mapping(): void {
		// a third-party filter adds a supported language without a mapping.
		add_filter( 'personio_integration_supported_languages', static function ( array $languages ): array {
			$languages['xx'] = 'Test language';
			return $languages;
		} );

		update_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, 'xx' );

		// must not fatal, must return a string.
		$this->assertIsString( \PersonioIntegrationLight\Plugin\Languages::get_instance()->get_main_language( true ) );
	}
}
