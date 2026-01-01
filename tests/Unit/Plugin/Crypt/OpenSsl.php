<?php
/**
 * Tests for class PersonioIntegrationLight\Plugin\Crypt\OpenSSL.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Plugin\Crypt;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in class PersonioIntegrationLight\Plugin\Crypt\OpenSSL.
 */
class OpenSsl extends PersonioTestCase {

	/**
	 * Test if the returning variable is true or false.
	 *
	 * @return void
	 */
	public function test_is_usable(): void {
		$is_usable = \PersonioIntegrationLight\Plugin\Crypt\OpenSSL::get_instance()->is_usable();
		$this->assertIsBool( $is_usable );
		if( function_exists( 'openssl_encrypt' ) ) {
			$this->assertTrue( $is_usable );
		}
		else {
			$this->assertFalse( $is_usable );
		}
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_encrypt(): void {
		$original_string = 'Hallo World';
		$encrypted_string = \PersonioIntegrationLight\Plugin\Crypt\OpenSSL::get_instance()->encrypt( $original_string );
		$this->assertIsString( $encrypted_string );
		if( function_exists( 'openssl_encrypt' ) ) {
			$this->assertNotEmpty( $encrypted_string );
			$this->assertNotEquals( $original_string, $encrypted_string );
		}
		else {
			$this->assertEmpty( $encrypted_string );
		}
	}

	/**
	 * Test if the returning variable is a string.
	 *
	 * @return void
	 */
	public function test_decrypt(): void {
		// encrypt a text first.
		$original_string = 'Hallo World';
		$encrypted_string = \PersonioIntegrationLight\Plugin\Crypt\OpenSSL::get_instance()->encrypt( $original_string );

		// now decrypt it and test the result.
		$decrypted_string = \PersonioIntegrationLight\Plugin\Crypt\OpenSSL::get_instance()->decrypt( $encrypted_string );
		$this->assertIsString( $decrypted_string );
		if( function_exists( 'openssl_encrypt' ) ) {
			$this->assertNotEmpty( $decrypted_string );
			$this->assertEquals( $original_string, $decrypted_string );
		}
		else {
			$this->assertEmpty( $decrypted_string );
		}
	}
}
