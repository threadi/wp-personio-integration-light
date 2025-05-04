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
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize all compatibility-checks for this plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		// add our own hook to prevent checks in wp-admin.
		add_filter( 'personio_integration_run_compatibility_checks', array( $this, 'prevent_checks_outside_of_admin' ) );

		// check each compatibility.
		add_action( 'init', array( $this, 'check' ) );
	}

	/**
	 * Check the compatibility of all supported third party products.
	 *
	 * @return void
	 */
	public function check(): void {
		$false = false;
		/**
		 * Filter whether the compatibility-checks should be run (false) or not (true)
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param bool $false True to prevent compatibility-checks.
		 *
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_run_compatibility_checks', $false ) ) {
			return;
		}

		// loop through our compatibility-checks.
		foreach ( $this->get_compatibility_checks_as_object() as $compatibility_check_obj ) {
			$compatibility_check_obj->check();
		}
	}

	/**
	 * Return list of compatibility-checks as objects.
	 *
	 * @return array<Compatibilities_Base>
	 */
	public function get_compatibility_checks_as_object(): array {
		// define the list for the objects.
		$list = array();

		// loop through our compatibility-checks.
		foreach ( $this->get_compatibility_checks() as $compatibility_check ) {
			// get the class name.
			$class_name = $compatibility_check . '::get_instance';

			// bail if it is not callable.
			if ( ! is_callable( $class_name ) ) {
				continue;
			}

			// get the object.
			$obj = $class_name();

			// bail if object is not a Compatibilities_Base.
			if ( ! $obj instanceof Compatibilities_Base ) {
				continue;
			}

			// add to the list.
			$list[] = $obj;
		}

		// return the resulting list.
		return $list;
	}

	/**
	 * Return array of compatibility-objects.
	 *
	 * @return array<string>
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
			'PersonioIntegrationLight\Plugin\Compatibilities\WPMultilang',
			'PersonioIntegrationLight\Plugin\Compatibilities\Nimble_Builder',
			'PersonioIntegrationLight\Plugin\Compatibilities\Pdf_Generator_For_Wp',
			'PersonioIntegrationLight\Plugin\Compatibilities\Polylang',
			'PersonioIntegrationLight\Plugin\Compatibilities\RankMath',
			'PersonioIntegrationLight\Plugin\Compatibilities\Scf',
			'PersonioIntegrationLight\Plugin\Compatibilities\Seed_Prod',
			'PersonioIntegrationLight\Plugin\Compatibilities\SiteOrigin',
			'PersonioIntegrationLight\Plugin\Compatibilities\Themify',
			'PersonioIntegrationLight\Plugin\Compatibilities\TranslatePress',
			'PersonioIntegrationLight\Plugin\Compatibilities\Visual_Composer',
			'PersonioIntegrationLight\Plugin\Compatibilities\Salient_WpBakery',
			'PersonioIntegrationLight\Plugin\Compatibilities\WpBakery',
			'PersonioIntegrationLight\Plugin\Compatibilities\Wpml',
			'PersonioIntegrationLight\Plugin\Compatibilities\WpPageBuilder',
			'PersonioIntegrationLight\Plugin\Compatibilities\Yoast',
		);

		/**
		 * Filter the list of compatibilities.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array<string> $list List of compatibility-checks.
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
