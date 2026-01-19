<?php
/**
 * File for handling info about new imported positions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Emails;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\Plugin\Email_Base;

/**
 * Object that handles info about new imported positions.
 */
class NewPositions extends Email_Base {
	/**
	 * Internal name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'new_positions';

	/**
	 * List of new positions.
	 *
	 * @var array<int,Position>
	 */
	private array $new_positions = array();

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
		return __( 'Info about new positions', 'personio-integration-light' );
	}

	/**
	 * Return the description.
	 *
	 * @return string
	 */
	protected function get_description(): string {
		return __( 'If activated, an email is sent to the specified email address each time a new position is imported from Personio in WordPress.', 'personio-integration-light' );
	}

	/**
	 * Show description for this email object.
	 *
	 * @return void
	 */
	public function show_description(): void {
		echo esc_html__( 'This email is sent when a new position from Personio is saved in WordPress. The fact that a new position has been added is documented in the log even without this email.', 'personio-integration-light' );
	}

	/**
	 * Return the subject of this email.
	 *
	 * @return string
	 */
	public function get_subject(): string {
		// set our custom subject.
		$this->subject = get_bloginfo( 'name' ) . ': ' . _n( 'New position imported from Personio', 'New positions imported from Personio', count( $this->get_new_positions() ), 'personio-integration-light' );

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
		$body = __( 'New positions from Personio have been imported into your WordPress. These are the following:', 'personio-integration-light' );
		foreach ( $this->get_new_positions() as $position_obj ) {
			$body .= '<br>' . $position_obj->get_title() . ' (Personio ID: ' . $position_obj->get_personio_id() . ')';
		}

		// set the body.
		$this->body = $body;

		// return the parent tasks for body.
		return parent::get_body();
	}

	/**
	 * Return list of new positions.
	 *
	 * @return array<int,Position>
	 */
	private function get_new_positions(): array {
		return $this->new_positions;
	}

	/**
	 * Set the new positions.
	 *
	 * @param array<int,Position> $new_positions List of new positions.
	 *
	 * @return void
	 */
	public function set_new_positions( array $new_positions ): void {
		$this->new_positions = $new_positions;
	}

	/**
	 * Prepare the object for a test email.
	 *
	 * @return void
	 */
	protected function prepare_for_test(): void {
		// get one position for the list.
		$positions = Positions::get_instance()->get_positions( 1 );

		// bail if no position could be found.
		if ( empty( $positions ) ) {
			return;
		}

		// set this position as a new position for the test.
		$this->set_new_positions( $positions );
	}
}
