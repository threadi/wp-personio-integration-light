<?php
/**
 * Tests for class PersonioIntegrationLight\Plugin\Crypt.
 *
 * @package personio-integration-light
 */

/**
 * Object to test functions in class PersonioIntegrationLight\Plugin\Crypt.
 */
class Crypt extends WP_UnitTestCase {

	/**
	 * Test if the returning variable is a crypt method object.
	 *
	 * @return void
	 */
	public function test_get_method(): void {
		$crypt_method = \PersonioIntegrationLight\Plugin\Crypt::get_instance()->get_method();
		$this->assertInstanceOf( '\PersonioIntegrationLight\Plugin\Crypt_Base', $crypt_method );
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
}
