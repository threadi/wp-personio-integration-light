<?php
/**
 * Tests for class PersonioIntegrationLight\Plugin\Crypt.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Plugin;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in class PersonioIntegrationLight\Plugin\Crypt.
 */
class Crypt extends PersonioTestCase {

	/**
	 * Test if the returning variable is a crypt method object.
	 *
	 * @return void
	 */
	public function test_get_method(): void {
		$crypt_method = \PersonioIntegrationLight\Plugin\Crypt::get_instance()->get_method();
		$this->assertInstanceOf( '\CryptForWordPress\Method_Base', $crypt_method );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_encrypt(): void {
		$original_string = 'Hallo World';
		$encrypted_string = \PersonioIntegrationLight\Plugin\Crypt::get_instance()->encrypt( $original_string );
		$this->assertIsString( $encrypted_string );
		$this->assertNotEmpty( $encrypted_string );
		$this->assertNotEquals( $original_string, $encrypted_string );
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_decrypt(): void {
		// encrypt a text first.
		$original_string = 'Hallo World';
		$encrypted_string = \PersonioIntegrationLight\Plugin\Crypt::get_instance()->encrypt( $original_string );

		// now decrypt it and test the result.
		$decrypted_string = \PersonioIntegrationLight\Plugin\Crypt::get_instance()->decrypt( $encrypted_string );
		$this->assertIsString( $decrypted_string );
		$this->assertNotEmpty( $decrypted_string );
		$this->assertEquals( $original_string, $decrypted_string );
	}

	/**
	 * Test if a value encrypted with a context could be decrypted with the same context only.
	 *
	 * @return void
	 */
	public function test_decrypt_with_context(): void {
		// encrypt a text with a context.
		$original_string  = 'Hallo World';
		$encrypted_string = \PersonioIntegrationLight\Plugin\Crypt::get_instance()->encrypt( $original_string, 'example_context' );
		$this->assertIsString( $encrypted_string );
		$this->assertNotEmpty( $encrypted_string );

		// decrypt it with the same context.
		$this->assertEquals( $original_string, \PersonioIntegrationLight\Plugin\Crypt::get_instance()->decrypt( $encrypted_string, 'example_context' ) );

		// decrypt it with another context.
		$this->assertEmpty( \PersonioIntegrationLight\Plugin\Crypt::get_instance()->decrypt( $encrypted_string, 'another_context' ) );

		// decrypt it without context.
		$this->assertEmpty( \PersonioIntegrationLight\Plugin\Crypt::get_instance()->decrypt( $encrypted_string ) );
	}

	/**
	 * Test if the callbacks for settings bind the value to the context and read values without context.
	 *
	 * @return void
	 */
	public function test_setting_callbacks_with_context(): void {
		$original_string = 'Hallo World';

		// save a value with the callback of a setting.
		$encrypted_string = \PersonioIntegrationLight\Plugin\Admin\SettingsSavings\SaveAsCryptValue::get_callback( 'example_setting' )( $original_string );
		$this->assertIsString( $encrypted_string );
		$this->assertNotEmpty( $encrypted_string );

		// read it with the callback of the same setting.
		$this->assertEquals( $original_string, \PersonioIntegrationLight\Plugin\Admin\SettingsRead\GetDecryptValue::get_callback( 'example_setting' )( $encrypted_string ) );

		// read it with the callback of another setting.
		$this->assertEmpty( \PersonioIntegrationLight\Plugin\Admin\SettingsRead\GetDecryptValue::get_callback( 'another_setting' )( $encrypted_string ) );

		// a value encrypted without context could still be read (transition).
		$old_encrypted_string = \PersonioIntegrationLight\Plugin\Crypt::get_instance()->encrypt( $original_string );
		$this->assertEquals( $original_string, \PersonioIntegrationLight\Plugin\Admin\SettingsRead\GetDecryptValue::get_callback( 'example_setting' )( $old_encrypted_string ) );
	}
}
