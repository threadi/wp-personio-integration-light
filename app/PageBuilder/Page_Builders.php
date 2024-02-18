<?php
/**
 * File to handle our page builder support.
 *
 * @package wp-personio-integration
 */

namespace PersonioIntegrationLight\PageBuilder;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Object to handle page builder support.
 */
class Page_Builders {

	/**
	 * Instance of this object.
	 *
	 * @var ?Page_Builders
	 */
	private static ?Page_Builders $instance = null;

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Page_Builders {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		foreach( $this->get_page_builder() as $page_builder ) {
			$obj = call_user_func( $page_builder . '::get_instance' );
			if( $obj instanceof PageBuilder_Base ) {
				$obj->init();
			}
		}
	}

	/**
	 * Return list of page builders.
	 *
	 * @return array
	 */
	private function get_page_builder(): array {
		$list = array(
			'\PersonioIntegrationLight\PageBuilder\Gutenberg'
		);

		/**
		 * Filter the possible page builders.
		 *
		 * @param array $list List of the handler.
		 */
		return apply_filters( 'personio_integration_pagebuilder', $list );
	}
}
