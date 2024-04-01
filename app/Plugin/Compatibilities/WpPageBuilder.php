<?php
/**
 * File to handle the compatibility-check for WpPageBuilder.
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
class WpPageBuilder extends Compatibilities_Base {

	/**
	 * Name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_compatibility_wp_page_builder';

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
		return defined( 'WPPB_VERSION' );
	}
}
