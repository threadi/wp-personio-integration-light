<?php
/**
 * File for handling different themes to output templates of our plugin.
 *
 * Depending on the used theme, we add custom css to optimize the output initially.
 *
 * Not supported themes do not get any additions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Object to handle different themes to output templates of our plugin.
 */
class Themes {

	/**
	 * Object which holds the active theme-support.
	 *
	 * @var Themes_Base|null
	 */
	private ?Themes_Base $theme = null;

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Themes
	 */
	protected static ?Themes $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Themes {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Initialize the theme-handler.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'get_theme_support' ) );
	}

	/**
	 * Check if we support this theme and if yes, activate the additions.
	 *
	 * @return void
	 */
	public function get_theme_support(): void {
		// bail if theme-support is already known.
		if ( ! is_null( $this->theme ) ) {
			return;
		}

		// get actual theme.
		$theme = wp_get_theme();

		// search for the active theme in the list of supported themes.
		foreach ( $this->get_themes() as $theme_class_name ) {
			$obj = new $theme_class_name();
			if ( $obj instanceof Themes_Base && $obj->get_name() === $theme->get_template() ) {
				// set theme as active-theme support.
				$this->set_theme_support( $obj );

				// initialize this support.
				$obj->init();

				// break the loop.
				return;
			}
		}
	}

	/**
	 * Return list of supported themes.
	 *
	 * @return array
	 */
	private function get_themes(): array {
		$theme_list = array(
			'\PersonioIntegrationLight\PersonioIntegration\Themes\Astra',
			'\PersonioIntegrationLight\PersonioIntegration\Themes\Blocksy',
			'\PersonioIntegrationLight\PersonioIntegration\Themes\GeneratePress',
			'\PersonioIntegrationLight\PersonioIntegration\Themes\Hestia',
			'\PersonioIntegrationLight\PersonioIntegration\Themes\Hitchcock',
			'\PersonioIntegrationLight\PersonioIntegration\Themes\OceanWp',
			'\PersonioIntegrationLight\PersonioIntegration\Themes\OpenShop',
			'\PersonioIntegrationLight\PersonioIntegration\Themes\TwentySeventeen',
		);

		/**
		 * Filter the list of supported themes.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 * @param array $theme_list The list of supported themes.
		 */
		return apply_filters( 'personio_integration_supported_themes', $theme_list );
	}

	/**
	 * Set the theme-support.
	 *
	 * @param Themes_Base $obj The theme-object based on Themes_Base.
	 *
	 * @return void
	 */
	private function set_theme_support( Themes_Base $obj ): void {
		$this->theme = $obj;
	}

	/**
	 * Return wrapper class of the supported theme.
	 *
	 * @return string
	 */
	public function get_theme_wrapper_classes(): string {
		// bail if no theme is set.
		if ( is_null( $this->theme ) ) {
			return '';
		}

		// return the classes.
		return $this->theme->get_wrapper_classes();
	}
}
