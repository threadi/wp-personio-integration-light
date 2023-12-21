<?php
/**
 * File with main initializer for this plugin.
 */

namespace personioIntegration;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks\Tax;
use personioIntegration\PostTypes\PersonioPosition;

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
		// register our post-types.
		PersonioPosition::get_instance()->init();

		// register our taxonomies.
		Taxonomies::get_instance()->register_taxonomies();

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
