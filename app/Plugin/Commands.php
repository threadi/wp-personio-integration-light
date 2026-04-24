<?php
/**
 * File to handle the command palette for this plugin.
 *
 * @package personio-intregation-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;

/**
 * The object, which handles the command palette for this plugin.
 */
class Commands {
	/**
	 * Instance of this object.
	 *
	 * @var ?Commands
	 */
	private static ?Commands $instance = null;

	/**
	 * Constructor for Schedules-Handler.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() { }

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Commands {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		// add action to enqueue scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add the command palette script to the admin area.
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
		// bail if the user does not have capabilities to show positions.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		// get the path for the asset script.
		$script_asset_path = Helper::get_plugin_path() . 'blocks/commands/commands.asset.php';

		// bail if the asset script does not exist.
		if ( ! file_exists( $script_asset_path ) ) {
			return;
		}

		// embed script.
		$script_asset = require $script_asset_path;

		wp_enqueue_script(
			'personio-integration-light-commands',
			Helper::get_plugin_url() . 'blocks/commands/commands.js',
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
	}
}
