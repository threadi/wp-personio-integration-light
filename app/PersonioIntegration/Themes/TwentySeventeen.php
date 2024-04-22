<?php
/**
 * File to handle support for the theme TwentySeventeen.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Themes;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Themes_Base;

/**
 * Handles the support for the theme TwentySeventeen.
 */
class TwentySeventeen extends Themes_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = 'twentyseventeen';

	/**
	 * Name of stylesheet file to embed.
	 *
	 * @var string
	 */
	protected string $css_file = 'twentyseventeen.css';

	/**
	 * Holds the wrapper-classes of this theme.
	 *
	 * @var string
	 */
	protected string $wrapper_classes = 'wrap';
}
