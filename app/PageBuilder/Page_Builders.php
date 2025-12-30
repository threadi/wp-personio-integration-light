<?php
/**
 * File to handle our page builder support.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Extensions_Base;

/**
 * Object to handle page builder support.
 */
class Page_Builders extends Extensions_Base {

	/**
	 * The internal name of this extension.
	 *
	 * @var string
	 */
	protected string $name = 'Page Builders';

	/**
	 * Variable for the instance of this Singleton object.
	 *
	 * @var ?Page_Builders
	 */
	private static ?Page_Builders $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Page_Builders {
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
		add_filter( 'personio_integration_extend_position_object', array( $this, 'add_page_builder_as_extension' ) );
		add_filter( 'personio_integration_log_categories', array( $this, 'add_log_categories' ) );

		// register the known pagebuilder.
		foreach ( $this->get_page_builders_as_objects() as $page_builder_obj ) {
			$page_builder_obj->init();
		}
	}

	/**
	 * Return the list of page builders.
	 *
	 * @return array<string>
	 */
	public function get_page_builders(): array {
		$list = array(
			'\PersonioIntegrationLight\PageBuilder\Gutenberg',
		);

		/**
		 * Filter the possible page builders.
		 *
		 * @param array<string> $list List of the handler.
		 */
		return apply_filters( 'personio_integration_pagebuilder', $list );
	}

	/**
	 * Return the list of page builders as their objects.
	 *
	 * @return array<int,PageBuilder_Base>
	 */
	public function get_page_builders_as_objects(): array {
		// create the list.
		$list = array();

		// register the known pagebuilder.
		foreach ( $this->get_page_builders() as $page_builder ) {
			// get the classname.
			$classname = $page_builder . '::get_instance';

			// bail if it is not callable.
			if ( ! is_callable( $classname ) ) {
				continue;
			}

			// get the object.
			$obj = $classname();

			// bail if an object is not PageBuilder_Base.
			if ( ! $obj instanceof PageBuilder_Base ) {
				continue;
			}

			// add an object to the list.
			$list[] = $obj;
		}

		// return the list.
		return $list;
	}

	/**
	 * Add page builder as extensions.
	 *
	 * @param array<string> $extensions List of extensions.
	 *
	 * @return array<string>
	 */
	public function add_page_builder_as_extension( array $extensions ): array {
		return array_merge( $this->get_page_builders(), $extensions );
	}

	/**
	 * Add categories for this extension type.
	 *
	 * @param array<string,string> $categories List of categories.
	 *
	 * @return array<string,string>
	 */
	public function add_log_categories( array $categories ): array {
		// add a category for this extension type.
		$categories['pagebuilder'] = __( 'PageBuilder', 'personio-integration-light' );

		// return the resulting list.
		return $categories;
	}
}
