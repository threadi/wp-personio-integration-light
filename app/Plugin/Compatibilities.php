<?php
/**
 * File to handle every compatibility-check for this plugin.
 *
 * @package personio-intregation-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * The object which handles schedules.
 */
class Compatibilities {
	/**
	 * Instance of this object.
	 *
	 * @var ?Compatibilities
	 */
	private static ?Compatibilities $instance = null;

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
	public static function get_instance(): Compatibilities {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize all compatibility-checks for this plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		// add our own hook to prevent checks in wp-admin.
		add_filter( 'personio_integration_run_compatibility_checks', array( $this, 'prevent_checks_outside_of_admin' ) );

		$false = false;
		/**
		 * Filter whether the compatibility-checks should be run (false) or not (true)
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param bool $false True to prevent compatibility-checks.
		 */
		if ( apply_filters( 'personio_integration_run_compatibility_checks', $false ) ) {
			return;
		}

		// loop through our compatibility-checks.
		foreach ( $this->get_compatibility_checks() as $compatibility_check ) {
			$obj = call_user_func( $compatibility_check . '::get_instance' );
			if ( $obj instanceof Compatibilities_Base ) {
				$obj->check();
			}
		}
	}

	/**
	 * Return array of compatibility-objects.
	 *
	 * @return array
	 */
	public function get_compatibility_checks(): array {
		$list = array(
			'PersonioIntegrationLight\Plugin\Compatibilities\Acf',
			'PersonioIntegrationLight\Plugin\Compatibilities\Avada',
			'PersonioIntegrationLight\Plugin\Compatibilities\Beaver',
			'PersonioIntegrationLight\Plugin\Compatibilities\Bold_Page_Builder',
			'PersonioIntegrationLight\Plugin\Compatibilities\Brizy',
			'PersonioIntegrationLight\Plugin\Compatibilities\Divi',
			'PersonioIntegrationLight\Plugin\Compatibilities\Elementor',
			'PersonioIntegrationLight\Plugin\Compatibilities\Nimble_Builder',
			'PersonioIntegrationLight\Plugin\Compatibilities\Pdf_Generator_For_Wp',
			'PersonioIntegrationLight\Plugin\Compatibilities\Polylang',
			'PersonioIntegrationLight\Plugin\Compatibilities\Seed_Prod',
			'PersonioIntegrationLight\Plugin\Compatibilities\SiteOrigin',
			'PersonioIntegrationLight\Plugin\Compatibilities\Themify',
			'PersonioIntegrationLight\Plugin\Compatibilities\TranslatePress',
			'PersonioIntegrationLight\Plugin\Compatibilities\Visual_Composer',
			'PersonioIntegrationLight\Plugin\Compatibilities\Salient_WpBakery',
			'PersonioIntegrationLight\Plugin\Compatibilities\WpBakery',
			'PersonioIntegrationLight\Plugin\Compatibilities\Wpml',
			'PersonioIntegrationLight\Plugin\Compatibilities\WpPageBuilder',
		);

		/**
		 * Filter the list of compatibilities.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $list List of compatibility-checks.
		 */
		return apply_filters( 'personio_integration_compatibility_checks', $list );
	}

	/**
	 * Prevent checks outside of admin.
	 *
	 * @param bool $prevent_checks Must be true to prevent the checks.
	 *
	 * @return bool
	 */
	public function prevent_checks_outside_of_admin( bool $prevent_checks ): bool {
		if ( ! is_admin() ) {
			return true;
		}

		// return initial value.
		return $prevent_checks;
	}
}
