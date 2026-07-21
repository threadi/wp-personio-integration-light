<?php
/**
 * Tests for class PersonioIntegrationLight\Helper.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in class PersonioIntegrationLight\Helper.
 */
class Helper extends PersonioTestCase {

	/**
	 * Test that the archive slug is "positions" for English.
	 *
	 * @return void
	 */
	public function test_get_archive_slug_en(): void {
		switch_to_locale( 'en_US' );
		$this->assertSame( 'positions', \PersonioIntegrationLight\Helper::get_archive_slug() );
	}

	/**
	 * Test that the archive slug is "stellen" for German.
	 *
	 * @return void
	 */
	public function test_get_archive_slug_de(): void {
		switch_to_locale( 'de_DE' );
		$this->assertSame( 'stellen', \PersonioIntegrationLight\Helper::get_archive_slug() );
		switch_to_locale( 'en_US' );
	}

	/**
	 * Test that the archive slug can be changed via filter.
	 *
	 * @return void
	 */
	public function test_get_archive_slug_filter(): void {
		switch_to_locale( 'en_US' );
		$callback = function () {
			return 'jobs';
		};
		add_filter( 'personio_integration_archive_slug', $callback );
		$this->assertSame( 'jobs', \PersonioIntegrationLight\Helper::get_archive_slug() );
		remove_filter( 'personio_integration_archive_slug', $callback );
	}

	/**
	 * Test that the single slug is "position" for English.
	 *
	 * @return void
	 */
	public function test_get_single_slug_en(): void {
		switch_to_locale( 'en_US' );
		$this->assertSame( 'position', \PersonioIntegrationLight\Helper::get_single_slug() );
	}

	/**
	 * Test that the single slug is "stelle" for German.
	 *
	 * @return void
	 */
	public function test_get_single_slug_de(): void {
		switch_to_locale( 'de_DE' );
		$this->assertSame( 'stelle', \PersonioIntegrationLight\Helper::get_single_slug() );
		switch_to_locale( 'en_US' );
	}

	/**
	 * Test that the Pro URL differs between English and German.
	 *
	 * @return void
	 */
	public function test_get_pro_url(): void {
		switch_to_locale( 'en_US' );
		$this->assertStringContainsString( '/en/', \PersonioIntegrationLight\Helper::get_pro_url() );

		switch_to_locale( 'de_DE' );
		$this->assertStringNotContainsString( '/en/', \PersonioIntegrationLight\Helper::get_pro_url() );
		switch_to_locale( 'en_US' );
	}

	/**
	 * Test the default list of filter types.
	 *
	 * @return void
	 */
	public function test_get_filter_types(): void {
		$types = \PersonioIntegrationLight\Helper::get_filter_types();
		$this->assertIsArray( $types );
		$this->assertArrayHasKey( 'select', $types );
		$this->assertArrayHasKey( 'linklist', $types );
	}

	/**
	 * Test that the filter type list can be extended via filter.
	 *
	 * @return void
	 */
	public function test_get_filter_types_filter(): void {
		$callback = function ( array $types ) {
			$types['custom'] = 'Custom';
			return $types;
		};
		add_filter( 'personio_integration_filter_types', $callback );
		$types = \PersonioIntegrationLight\Helper::get_filter_types();
		$this->assertArrayHasKey( 'custom', $types );
		remove_filter( 'personio_integration_filter_types', $callback );
	}

	/**
	 * Test check_if_setting_error_entry_exists_in_array() with an existing entry.
	 *
	 * @return void
	 */
	public function test_check_if_setting_error_entry_exists_in_array_true(): void {
		$errors = array(
			array( 'setting' => 'personioIntegrationUrl' ),
			array( 'setting' => 'personioIntegrationEmails' ),
		);
		$this->assertTrue( \PersonioIntegrationLight\Helper::check_if_setting_error_entry_exists_in_array( 'personioIntegrationUrl', $errors ) );
	}

	/**
	 * Test check_if_setting_error_entry_exists_in_array() with a missing entry.
	 *
	 * @return void
	 */
	public function test_check_if_setting_error_entry_exists_in_array_false(): void {
		$errors = array(
			array( 'setting' => 'personioIntegrationUrl' ),
		);
		$this->assertFalse( \PersonioIntegrationLight\Helper::check_if_setting_error_entry_exists_in_array( 'unknown_setting', $errors ) );
	}

	/**
	 * Test check_if_setting_error_entry_exists_in_array() with an empty list.
	 *
	 * @return void
	 */
	public function test_check_if_setting_error_entry_exists_in_array_empty(): void {
		$this->assertFalse( \PersonioIntegrationLight\Helper::check_if_setting_error_entry_exists_in_array( 'anything', array() ) );
	}

	/**
	 * Test that replace_linebreaks() collapses newlines and multiple whitespaces into single spaces.
	 *
	 * @return void
	 */
	public function test_replace_linebreaks(): void {
		$text = "Hello\nWorld\r\n  with   spaces\ttab";
		$this->assertSame( 'Hello World with spaces tab', \PersonioIntegrationLight\Helper::replace_linebreaks( $text ) );
	}

	/**
	 * Test that replace_linebreaks() leaves a plain string untouched.
	 *
	 * @return void
	 */
	public function test_replace_linebreaks_plain_string(): void {
		$this->assertSame( 'Hello World', \PersonioIntegrationLight\Helper::replace_linebreaks( 'Hello World' ) );
	}

	/**
	 * Test that replace_linebreaks() with an empty string returns an empty string.
	 *
	 * @return void
	 */
	public function test_replace_linebreaks_empty_string(): void {
		$this->assertSame( '', \PersonioIntegrationLight\Helper::replace_linebreaks( '' ) );
	}

	/**
	 * Test add_array_in_array_on_position() inserts the new entries at the requested position.
	 *
	 * @return void
	 */
	public function test_add_array_in_array_on_position(): void {
		$fields = array(
			'a' => 1,
			'b' => 2,
			'c' => 3,
		);
		$result = \PersonioIntegrationLight\Helper::add_array_in_array_on_position( $fields, 1, array( 'x' => 99 ) );
		$this->assertSame( array( 'a', 'x', 'b', 'c' ), array_keys( $result ) );
		$this->assertSame( 99, $result['x'] );
	}

	/**
	 * Test add_array_in_array_on_position() with position 0 prepends the entry.
	 *
	 * @return void
	 */
	public function test_add_array_in_array_on_position_at_start(): void {
		$fields = array( 'a' => 1 );
		$result = \PersonioIntegrationLight\Helper::add_array_in_array_on_position( $fields, 0, array( 'x' => 99 ) );
		$this->assertSame( array( 'x', 'a' ), array_keys( $result ) );
	}

	/**
	 * Test add_array_in_array_on_position() returns an empty array if the fields are null.
	 *
	 * @return void
	 */
	public function test_add_array_in_array_on_position_null_fields(): void {
		$result = \PersonioIntegrationLight\Helper::add_array_in_array_on_position( null, 0, array( 'x' => 99 ) );
		$this->assertSame( array(), $result );
	}

	/**
	 * Test get_attribute_value_from_html() extracts a double-quoted attribute value.
	 *
	 * @return void
	 */
	public function test_get_attribute_value_from_html_double_quotes(): void {
		$tag = '<a href="https://example.com" class="button">Link</a>';
		$this->assertSame( 'https://example.com', \PersonioIntegrationLight\Helper::get_attribute_value_from_html( 'href', $tag ) );
	}

	/**
	 * Test get_attribute_value_from_html() extracts a single-quoted attribute value.
	 *
	 * @return void
	 */
	public function test_get_attribute_value_from_html_single_quotes(): void {
		$tag = "<a href='https://example.com'>Link</a>";
		$this->assertSame( 'https://example.com', \PersonioIntegrationLight\Helper::get_attribute_value_from_html( 'href', $tag ) );
	}

	/**
	 * Test get_attribute_value_from_html() returns false if the attribute is not present.
	 *
	 * @return void
	 */
	public function test_get_attribute_value_from_html_missing(): void {
		$tag = '<a class="button">Link</a>';
		$this->assertFalse( \PersonioIntegrationLight\Helper::get_attribute_value_from_html( 'href', $tag ) );
	}

	/**
	 * Test is_plugin_active() returns false for a plugin which is not active.
	 *
	 * @return void
	 */
	public function test_is_plugin_active_false(): void {
		update_option( 'active_plugins', array() );
		$this->assertFalse( \PersonioIntegrationLight\Helper::is_plugin_active( 'some-plugin/some-plugin.php' ) );
	}

	/**
	 * Test is_plugin_active() returns true for a plugin which is set as active.
	 *
	 * @return void
	 */
	public function test_is_plugin_active_true(): void {
		update_option( 'active_plugins', array( 'some-plugin/some-plugin.php' ) );
		$this->assertTrue( \PersonioIntegrationLight\Helper::is_plugin_active( 'some-plugin/some-plugin.php' ) );
		update_option( 'active_plugins', array() );
	}

	/**
	 * Test is_plugin_installed() returns false for a plugin path that does not exist.
	 *
	 * @return void
	 */
	public function test_is_plugin_installed_false(): void {
		$this->assertFalse( \PersonioIntegrationLight\Helper::is_plugin_installed( 'this-plugin-does-not-exist/plugin.php' ) );
	}

	/**
	 * Test get_plugin_url() returns a trailing-slashed URL.
	 *
	 * @return void
	 */
	public function test_get_plugin_url(): void {
		$url = \PersonioIntegrationLight\Helper::get_plugin_url();
		$this->assertIsString( $url );
		$this->assertStringEndsWith( '/', $url );
	}

	/**
	 * Test get_plugin_path() returns a trailing-slashed local path.
	 *
	 * @return void
	 */
	public function test_get_plugin_path(): void {
		$path = \PersonioIntegrationLight\Helper::get_plugin_path();
		$this->assertIsString( $path );
		$this->assertStringEndsWith( '/', $path );
	}

	/**
	 * Test is_personio_url_set() returns true once the option is set.
	 *
	 * @return void
	 */
	public function test_is_personio_url_set_true(): void {
		update_option( 'personioIntegrationUrl', self::$personio_url );
		$this->assertTrue( \PersonioIntegrationLight\Helper::is_personio_url_set() );
	}

	/**
	 * Test is_personio_url_set() returns false once the option is empty.
	 *
	 * @return void
	 */
	public function test_is_personio_url_set_false(): void {
		update_option( 'personioIntegrationUrl', '' );
		$this->assertFalse( \PersonioIntegrationLight\Helper::is_personio_url_set() );
	}

	/**
	 * Test is_cli() returns false in the phpunit context (WP_CLI not defined/true here).
	 *
	 * @return void
	 */
	public function test_is_cli_false(): void {
		$this->assertFalse( \PersonioIntegrationLight\Helper::is_cli() );
	}

	/**
	 * Test get_json() returns a valid JSON string for an array.
	 *
	 * @return void
	 */
	public function test_get_json_array(): void {
		$json = \PersonioIntegrationLight\Helper::get_json( array( 'foo' => 'bar' ) );
		$this->assertJson( $json );
		$this->assertSame( array( 'foo' => 'bar' ), json_decode( $json, true ) );
	}

	/**
	 * Test get_json() with an empty array still returns valid JSON.
	 *
	 * @return void
	 */
	public function test_get_json_empty_array(): void {
		$json = \PersonioIntegrationLight\Helper::get_json( array() );
		$this->assertJson( $json );
	}

	/**
	 * Test get_list_of_our_cpts() contains the plugin's own post-type.
	 *
	 * @return void
	 */
	public function test_get_list_of_our_cpts(): void {
		$list = \PersonioIntegrationLight\Helper::get_list_of_our_cpts();
		$this->assertIsArray( $list );
		$this->assertNotEmpty( $list );
	}

	/**
	 * Test get_settings_url() contains the requested page, tab and sub-tab.
	 *
	 * @return void
	 */
	public function test_get_settings_url_with_tabs(): void {
		$url = \PersonioIntegrationLight\Helper::get_settings_url( 'personioPositions', 'general', 'import' );
		$this->assertStringContainsString( 'page=personioPositions', $url );
		$this->assertStringContainsString( 'tab=general', $url );
		$this->assertStringContainsString( 'subtab=import', $url );
	}

	/**
	 * Test get_settings_url() without tab/sub-tab does not add those params.
	 *
	 * @return void
	 */
	public function test_get_settings_url_without_tabs(): void {
		$url = \PersonioIntegrationLight\Helper::get_settings_url();
		$this->assertStringNotContainsString( 'tab=', $url );
		$this->assertStringNotContainsString( 'subtab=', $url );
	}

	/**
	 * Test get_personio_url_example() differs between English and German.
	 *
	 * @return void
	 */
	public function test_get_personio_url_example(): void {
		switch_to_locale( 'de_DE' );
		$this->assertStringContainsString( 'personio.de', \PersonioIntegrationLight\Helper::get_personio_url_example() );

		switch_to_locale( 'en_US' );
		$this->assertStringContainsString( 'personio.com', \PersonioIntegrationLight\Helper::get_personio_url_example() );
	}

	/**
	 * Test get_a11n_window_hint() returns a non-empty screen-reader hint.
	 *
	 * @return void
	 */
	public function test_get_a11n_window_hint(): void {
		$hint = \PersonioIntegrationLight\Helper::get_a11n_window_hint();
		$this->assertStringContainsString( 'screen-reader-text', $hint );
	}

	/**
	 * Test get_shortcode_attributes() applies int-casting as configured.
	 *
	 * @return void
	 */
	public function test_get_shortcode_attributes_int_casting(): void {
		$defaults = array( 'limit' => 0 );
		$settings = array( 'limit' => 'int' );
		$result   = \PersonioIntegrationLight\Helper::get_shortcode_attributes( $defaults, $settings, array( 'limit' => '5' ) );
		$this->assertSame( 5, $result['limit'] );
	}

	/**
	 * Test get_shortcode_attributes() applies array-casting for comma-separated strings.
	 *
	 * @return void
	 */
	public function test_get_shortcode_attributes_array_casting(): void {
		$defaults = array( 'ids' => '' );
		$settings = array( 'ids' => 'array' );
		$result   = \PersonioIntegrationLight\Helper::get_shortcode_attributes( $defaults, $settings, array( 'ids' => '1, 2, 3' ) );
		$this->assertSame( array( '1', '2', '3' ), $result['ids'] );
	}

	/**
	 * Test get_shortcode_attributes() applies bool-casting.
	 *
	 * @return void
	 */
	public function test_get_shortcode_attributes_bool_casting(): void {
		$defaults = array( 'showfilter' => false );
		$settings = array( 'showfilter' => 'bool' );
		$result   = \PersonioIntegrationLight\Helper::get_shortcode_attributes( $defaults, $settings, array( 'showfilter' => '1' ) );
		$this->assertTrue( (bool) $result['showfilter'] );
	}

	/**
	 * Test get_shortcode_attributes() falls back to defaults for attributes not provided.
	 *
	 * @return void
	 */
	public function test_get_shortcode_attributes_uses_defaults(): void {
		$defaults = array( 'lang' => 'en' );
		$result   = \PersonioIntegrationLight\Helper::get_shortcode_attributes( $defaults, array(), array() );
		$this->assertSame( 'en', $result['lang'] );
	}

	/**
	 * Test get_shortcode_attributes() falls back to the fallback language for an unsupported language.
	 *
	 * @return void
	 */
	public function test_get_shortcode_attributes_invalid_language_fallback(): void {
		$defaults = array( 'lang' => 'en' );
		$result   = \PersonioIntegrationLight\Helper::get_shortcode_attributes( $defaults, array(), array( 'lang' => 'xx' ) );
		$this->assertNotSame( 'xx', $result['lang'] );
	}
}
