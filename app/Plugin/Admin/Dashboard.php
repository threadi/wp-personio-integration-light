<?php
/**
 * File for handling site health options of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;

/**
 * Helper-function for Dashboard options of this plugin.
 */
class Dashboard {
	/**
	 * Instance of this object.
	 *
	 * @var ?Dashboard
	 */
	private static ?Dashboard $instance = null;

	/**
	 * Constructor for Init-Handler.
	 */
	private function __construct() {
	}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {
	}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Dashboard {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Initialize the site health support.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
	}

	/**
	 * Add the dashboard widgets.
	 *
	 * @return void
	 */
	public function add_dashboard_widgets(): void {
		foreach ( $this->get_dashboard_widgets() as $dashboard_widget ) {
			// add dashboard widget to show the newest imported positions.
			wp_add_dashboard_widget(
				$dashboard_widget['id'],
				$dashboard_widget['label'],
				$dashboard_widget['callback'],
				null,
				array(),
				'side',
				'high'
			);
		}
	}

	/**
	 * Get the dashboard-widgets.
	 *
	 * @return array
	 */
	private function get_dashboard_widgets(): array {
		$dashboard_widgets = array();

		/**
		 * Filter the dashboard-widgets used by this plugin.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 * @param array $dashboard_widgets List of widgets.
		 */
		return apply_filters( 'personio_integration_dashboard_widgets', $dashboard_widgets );
	}
}
