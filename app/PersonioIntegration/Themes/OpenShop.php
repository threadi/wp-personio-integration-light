<?php
/**
 * File to handle support for the theme Open Shop.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Themes;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Themes_Base;

/**
 * Handles the support for the theme Open Shop.
 */
class OpenShop extends Themes_Base {
	/**
	 * Internal name for this object.
	 *
	 * @var string
	 */
	protected string $name = 'open-shop';

	/**
	 * Name of stylesheet file to embed.
	 *
	 * @var string
	 */
	protected string $css_file = 'open-shop.css';

	/**
	 * Holds the wrapper-classes of this theme.
	 *
	 * @var string
	 */
	protected string $wrapper_classes = 'wrap';
}
