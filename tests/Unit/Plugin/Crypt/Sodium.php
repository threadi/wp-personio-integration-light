<?php
/**
 * Tests for class PersonioIntegrationLight\Plugin\Crypt\Sodium.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Plugin\Crypt;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in class PersonioIntegrationLight\Plugin\Crypt\Sodium.
 */
class Sodium extends PersonioTestCase {

	/**
	 * Prepare the test environment.
	 *
	 * @return void
	 * @throws SodiumException
	 */
	public function setUp(): void {
		\PersonioIntegrationLight\Plugin\Crypt\Sodium::get_instance()->init();
	}

	/**
	 * Test if the returning variable is true or false.
	 *
	 * @return void
	 */
	public function test_is_usable(): void {
		$is_usable = \PersonioIntegrationLight\Plugin\Crypt\Sodium::get_instance()->is_usable();
		$this->assertIsBool( $is_usable );
		if( function_exists( 'sodium_crypto_aead_aes256gcm_is_available' ) && sodium_crypto_aead_aes256gcm_is_available() ) {
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
		$encrypted_string = \PersonioIntegrationLight\Plugin\Crypt\Sodium::get_instance()->encrypt( $original_string );
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
		$encrypted_string = \PersonioIntegrationLight\Plugin\Crypt\Sodium::get_instance()->encrypt( $original_string );

		// now decrypt it and test the result.
		$decrypted_string = \PersonioIntegrationLight\Plugin\Crypt\Sodium::get_instance()->decrypt( $encrypted_string );
		$this->assertIsString( $decrypted_string );
	}
}
