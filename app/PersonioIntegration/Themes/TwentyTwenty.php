<?php
/**
 * File to handle support for the theme TwentyTwenty.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Themes;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Themes_Base;

/**
 * Handles the support for the theme TwentyTwenty.
 */
class TwentyTwenty extends Themes_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = 'twentytwenty';

	/**
	 * Name of stylesheet file to embed.
	 *
	 * @var string
	 */
	protected string $css_file = 'twentytwenty.css';
}
