<?php
/**
 * File to handle support for the theme GeneratePress.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Themes;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Themes_Base;

/**
 * Handles the support for the theme GeneratePress.
 */
class GeneratePress extends Themes_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = 'generatepress';

	/**
	 * Name of stylesheet file to embed.
	 *
	 * @var string
	 */
	protected string $css_file = 'generatepress.css';

	/**
	 * Holds the wrapper-classes of this theme.
	 *
	 * @var string
	 */
	protected string $wrapper_classes = '';
}
