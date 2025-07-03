<?php
/**
 * File to handle widgets in Pro-plugin.
 *
 * @package wp-personio-integration
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Object to handle widgets we add with this plugin.
 */
class Widgets {
	/**
	 * Instance of this object.
	 *
	 * @var ?Widgets
	 */
	private static ?Widgets $instance = null;

	/**
	 * Constructor for Init-Handler.
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
	public static function get_instance(): Widgets {
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
		// use our own hooks.
		add_filter( 'personio_integration_extend_position_object', array( $this, 'add_widgets' ) );
		add_filter( 'personio_integration_extension_categories', array( $this, 'add_extension_categories' ) );
	}

	/**
	 * Return list of supported widgets.
	 *
	 * @param array<int,string> $extensions List of extensions.
	 *
	 * @return array<int,string>
	 */
	public function add_widgets( array $extensions ): array {
		return array_merge( $this->get_widgets(), $extensions );
	}

	/**
	 * Return list of light plugin widgets.
	 *
	 * @return array<int,string>
	 */
	public function get_widgets(): array {
		return array(
			'\PersonioIntegrationLight\PersonioIntegration\Widgets\Application_Button',
			'\PersonioIntegrationLight\PersonioIntegration\Widgets\Archive',
			'\PersonioIntegrationLight\PersonioIntegration\Widgets\Description',
			'\PersonioIntegrationLight\PersonioIntegration\Widgets\Details',
			'\PersonioIntegrationLight\PersonioIntegration\Widgets\Filter_List',
			'\PersonioIntegrationLight\PersonioIntegration\Widgets\Filter_Select',
			'\PersonioIntegrationLight\PersonioIntegration\Widgets\Single'
		);
	}

	/**
	 * Return list of widgets as object.
	 *
	 * @return array<int,Widget_Base>
	 */
	public function get_widgets_as_objects(): array {
		// create the list.
		$list = array();

		// add the widgets.
		foreach ( $this->add_widgets( array() ) as $widget_class_name ) {
			// create classname.
			$classname = $widget_class_name . '::get_instance';

			// bail if classname is not callable.
			if ( ! is_callable( $classname ) ) {
				continue;
			}

			// get the object.
			$obj = $classname();

			// bail if object is not the handler base.
			if ( ! $obj instanceof Widget_Base ) {
				continue;
			}

			// add object to the list.
			$list[] = $obj;
		}

		// return the list.
		return $list;
	}

	/**
	 * Add pro-categories for extension table.
	 *
	 * @param array<string,string> $categories List of categories.
	 *
	 * @return array<string,string>
	 */
	public function add_extension_categories( array $categories ): array {
		$categories['widgets'] = __( 'Widgets', 'wp-personio-integration' );
		return $categories;
	}
}
