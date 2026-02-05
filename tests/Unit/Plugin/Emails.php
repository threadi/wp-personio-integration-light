<?php
/**
 * Tests for class PersonioIntegrationLight\Plugin\Emails.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Plugin;

use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * Object to test functions in class PersonioIntegrationLight\Plugin\Emails.
 */
class Emails extends PersonioTestCase {

	/**
	 * Test if an email is sent when new positions are available.
	 *
	 * @return void
	 */
	public function test_trigger_new_positions(): void {
		global $phpmailer;

		// set the pseudo email recipient.
		$email_recipient = 'info@example.com';

		// set necessary options for "new positions" email.
		update_option( 'personio_integration_email_new_positions', 1 );
		update_option( 'personio_integration_email_recipients_new_positions', array( $email_recipient ) );

		// trigger the email.
		\PersonioIntegrationLight\Plugin\Emails::get_instance()->trigger_new_positions();

		// get the email.
		$email = $phpmailer->getSentMIMEMessage();
		$this->assertIsString( $email );
		$this->assertNotEmpty( $email );
		$this->assertStringContainsString( get_option( 'blogname' ), $email );
		$this->assertStringContainsString( $email_recipient, $email );
	}

	/**
	 * Test if the return value is an array.
	 *
	 * @return void
	 */
	public function test_add_log_category(): void {
		$log_categories = \PersonioIntegrationLight\Plugin\Emails::get_instance()->add_log_category( array() );
		$this->assertIsArray( $log_categories );
		$this->assertNotEmpty( $log_categories );
	}

	/**
	 * Test if the return value is an array.
	 *
	 * @return void
	 */
	public function test_add_schedule(): void {
		$log_categories = \PersonioIntegrationLight\Plugin\Emails::get_instance()->add_schedule( array() );
		$this->assertIsArray( $log_categories );
		$this->assertNotEmpty( $log_categories );
	}

	/**
	 * Test if an email is sent when positions are deleted.
	 *
	 * @return void
	 */
	public function test_trigger_deleted_positions(): void {
		global $phpmailer;

		// set the pseudo email recipient.
		$email_recipient = 'info@example.com';

		// set necessary options for "delete positions" email.
		update_option( 'personio_integration_email_deleted_positions', 1 );
		update_option( 'personio_integration_email_recipients_deleted_positions', array( $email_recipient ) );

		// trigger the email.
		\PersonioIntegrationLight\Plugin\Emails::get_instance()->trigger_deleted_positions();

		// get the email.
		$email = $phpmailer->getSentMIMEMessage();
		$this->assertIsString( $email );
		$this->assertNotEmpty( $email );
		$this->assertStringContainsString( get_option( 'blogname' ), $email );
		$this->assertStringContainsString( $email_recipient, $email );
	}
}
