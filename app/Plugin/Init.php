<?php
/**
 * File with main initializer for this plugin.
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

use App\PersonioIntegration\PostTypes\PersonioPosition;
use App\PersonioIntegration\Taxonomies;
use App\Third_Party_Plugins;
use App\Widgets\Widgets;

/**
 * Initialize this plugin.
 */
class Init {
	/**
	 * Instance of this object.
	 *
	 * @var ?Init
	 */
	private static ?Init $instance = null;

	/**
	 * Constructor for Init-Handler.
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
	public static function get_instance(): Init {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize this plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		// register our post-types and taxonomies.
		PersonioPosition::get_instance()->init();

		// init classic widget support.
		Widgets::get_instance()->init();

		// init third-party-support.
		Third_Party_Plugins::get_instance()->init();

		// on activation.
		register_activation_hook( WP_PERSONIO_INTEGRATION_PLUGIN, array( $this, 'activation' ) );

		// on deactivation.
		register_deactivation_hook( WP_PERSONIO_INTEGRATION_PLUGIN, array( $this, 'deactivation' ) );

		// register cli.
		add_action( 'cli_init', array( $this, 'cli' ) );
	}

	/**
	 * Tasks to run on activation.
	 *
	 * @return void
	 */
	public function activation(): void {
		Installer::activation();
	}

	/**
	 * Tasks to run on deactivation.
	 *
	 * @return void
	 */
	public function deactivation(): void {
		wp_clear_scheduled_hook( 'personio_integration_schudule_events' );
	}

	/**
	 * Register WP-CLI.
	 *
	 * @return void
	 */
	public function cli(): void {
		\WP_CLI::add_command( 'personio', 'personioIntegration\Cli' );
	}
}
