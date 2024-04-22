<?php
/**
 * File to handle the compatibility-check for Brizy.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Compatibilities;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Plugin\Compatibilities_Base;

/**
 * Object for this check.
 */
class Brizy extends Compatibilities_Base {

	/**
	 * Name of this event.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_compatibility_brizy';

	/**
	 * Do nothing on check as we can not support this builder with functions.
	 *
	 * @return void
	 */
	public function check(): void {}

	/**
	 * Return whether this component is active (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return defined( 'BRIZY_VERSION' );
	}
}
