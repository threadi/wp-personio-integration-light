<?php
/**
 * File to handle the compatibility-check for Loco Translate.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Compatibilities;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Compatibilities_Base;

/**
 * Object for this check.
 */
class SayWhat extends Compatibilities_Base {

	/**
	 * Name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_compatibility_saywhat';

	/**
	 * URL of this plugin.
	 *
	 * @var string
	 */
	protected string $plugin_url = 'https://wordpress.org/plugins/say-what/';

	/**
	 * Instance of this object.
	 *
	 * @var ?SayWhat
	 */
	private static ?SayWhat $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): SayWhat {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Check if Avada and its necessary plugins are active.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return Helper::is_plugin_active( 'say-what/say-what.php' );
	}
}
