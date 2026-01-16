<?php
/**
 * File for handling info about deleted positions.
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
 * Object that handles info about deleted positions.
 */
class DeletedPositions extends Email_Base {
	/**
	 * Internal name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'deleted_positions';

	/**
	 * List of deleted positions.
	 *
	 * @var array<int,Position>
	 */
	private array $deleted_positions = array();

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
		return __( 'Info about deleted positions', 'personio-integration-light' );
	}

	/**
	 * Return the description.
	 *
	 * @return string
	 */
	protected function get_description(): string {
		return __( 'If activated, an email is sent to the specified email address each time a position is been deleted in WordPress.', 'personio-integration-light' );
	}

	/**
	 * Show the description for this email object.
	 *
	 * @return void
	 */
	public function show_description(): void {
		echo esc_html__( 'This email is sent when a position has been deleted in WordPress. This only happens if the position in question is no longer reported by Personio or if a setting in the plugin excludes the position in future.', 'personio-integration-light' );
	}

	/**
	 * Return the subject of the email.
	 *
	 * @return string
	 */
	public function get_subject(): string {
		// set our custom subject.
		$this->subject = get_bloginfo( 'name' ) . ': ' . _n( 'Position deleted after import from Personio', 'Positions deleted after imported from Personio', count( $this->get_deleted_positions() ), 'personio-integration-light' );

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
		$body = __( 'The following positions have been deleted in WordPress after import from Personio:', 'personio-integration-light' );
		foreach ( $this->get_deleted_positions() as $position_obj ) {
			$body .= '<br>' . $position_obj->get_title() . ' (Personio ID: ' . $position_obj->get_personio_id() . ')';
		}
		$body .= '<br><br>' . __( 'They were deleted because they were no longer made available as positions by Personio.', 'personio-integration-light' );

		// set the body.
		$this->body = $body;

		// return the parent tasks for body.
		return parent::get_body();
	}

	/**
	 * Return the list of deleted positions.
	 *
	 * @return array<int,Position>
	 */
	private function get_deleted_positions(): array {
		return $this->deleted_positions;
	}

	/**
	 * Set the new positions.
	 *
	 * @param array<int,Position> $deleted_positions List of deleted positions.
	 *
	 * @return void
	 */
	public function set_deleted_positions( array $deleted_positions ): void {
		$this->deleted_positions = $deleted_positions;
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

		// set this position as deleted for the test.
		$this->set_deleted_positions( $positions );
	}
}
