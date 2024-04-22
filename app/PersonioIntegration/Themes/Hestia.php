<?php
/**
 * File to handle support for the theme Hestia.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Themes;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Themes_Base;

/**
 * Handles the support for the theme Hestia.
 */
class Hestia extends Themes_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = 'hestia';

	/**
	 * Name of stylesheet file to embed.
	 *
	 * @var string
	 */
	protected string $css_file = 'hestia.css';
}
