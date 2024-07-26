<?php
/**
 * File to handle our page builder support.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Extensions;

/**
 * Object to handle page builder support.
 */
class Page_Builders extends Extensions {
	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter( 'personio_integration_extend_position_object', array( $this, 'add_page_builder_as_extension' ) );
		add_filter( 'personio_integration_log_categories', array( $this, 'add_log_categories' ) );

		// register the known pagebuilder.
		foreach ( $this->get_page_builders() as $page_builder ) {
			$obj = call_user_func( $page_builder . '::get_instance' );
			if ( $obj instanceof PageBuilder_Base ) {
				$obj->init();
			}
		}
	}

	/**
	 * Return list of page builders.
	 *
	 * @return array
	 */
	public function get_page_builders(): array {
		$list = array(
			'\PersonioIntegrationLight\PageBuilder\Gutenberg',
		);

		/**
		 * Filter the possible page builders.
		 *
		 * @param array $list List of the handler.
		 */
		return apply_filters( 'personio_integration_pagebuilder', $list );
	}

	/**
	 * Add page builder as extensions.
	 *
	 * @param array $extensions List of extensions.
	 *
	 * @return array
	 */
	public function add_page_builder_as_extension( array $extensions ): array {
		return array_merge( $this->get_page_builders(), $extensions );
	}

	/**
	 * Add categories for this extension type.
	 *
	 * @param array $categories List of categories.
	 *
	 * @return array
	 */
	public function add_log_categories( array $categories ): array {
		// add category for this extension type.
		$categories['pagebuilder'] = __( 'PageBuilder', 'personio-integration-light' );

		// return resulting list.
		return $categories;
	}

	/**
	 * Run uninstaller for all registered pagebuilder.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		// register the known pagebuilder.
		foreach ( $this->get_page_builders() as $page_builder ) {
			$obj = call_user_func( $page_builder . '::get_instance' );
			if ( $obj instanceof PageBuilder_Base ) {
				$obj->uninstall();
			}
		}
	}
}
