<?php
/**
 * File for handling import error mails.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Emails;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Plugin\Email_Base;

/**
 * Object that handles import error mails.
 */
class ImportError extends Email_Base {
	/**
	 * Internal name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'import_error';

	/**
	 * Enabled by default on plugin installation.
	 *
	 * @var bool
	 */
	protected bool $default_enabled = true;

	/**
	 * The list of errors
	 *
	 * @var array<int,string>
	 */
	private array $errors = array();

	/**
	 * Constructor for this object.
	 */
	public function __construct() {}

	/**
	 * Return the title.
	 *
	 * @return string
	 */
	protected function get_title(): string {
		return __( 'Import Errors', 'personio-integration-light' );
	}

	/**
	 * Return the description.
	 *
	 * @return string
	 */
	protected function get_description(): string {
		return __( 'If activated, an email is sent to the specified email address each time an error occurs during import.', 'personio-integration-light' );
	}

	/**
	 * Return the default recipient.
	 *
	 * @return string
	 */
	protected function get_default_recipient(): string {
		return get_bloginfo( 'admin_email' );
	}

	/**
	 * Show the description for this email object.
	 *
	 * @return void
	 */
	public function show_description(): void {
		echo esc_html__( 'This email is sent when an error has occurred during the import of Personio positions in WordPress . The email describes the specific error and, if possible, how to solve it. The error messages are also documented in the log without this email.', 'personio-integration-light' );
	}

	/**
	 * Return the subject for this email.
	 *
	 * @return string
	 */
	public function get_subject(): string {
		// set our custom subject.
		$this->subject = get_bloginfo( 'name' ) . ': ' . __( 'Error during Import of positions from Personio', 'personio-integration-light' );

		// return the parent tasks for the subject.
		return parent::get_subject();
	}

	/**
	 * Return the body.
	 *
	 * @return string
	 */
	public function get_body(): string {
		// create the body.
		$body = __( 'The following error occurred when importing positions provided by Personio:', 'personio-integration-light' ) . '<br><br><em>' . implode( "\n", $this->get_errors() ) . '</em>';

		// set the body.
		$this->body = $body;

		// return the parent tasks for body.
		return parent::get_body();
	}

	/**
	 * Return the list of errors.
	 *
	 * @return array<int,string>
	 */
	private function get_errors(): array {
		return $this->errors;
	}

	/**
	 * Set the list of errors.
	 *
	 * @param array<int,string> $errors The list of errors.
	 *
	 * @return void
	 */
	public function set_errors( array $errors ): void {
		$this->errors = $errors;
	}

	/**
	 * Prepare the object for a test email.
	 *
	 * @return void
	 */
	protected function prepare_for_test(): void {
		// create a list of pseudo-errors.
		$errors = array(
			__( 'This is a test-error.', 'personio-integration-light' ),
		);

		// set error to trigger the test email.
		$this->set_errors( $errors );
	}
}
