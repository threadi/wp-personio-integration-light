<?php
/**
 * File as base for each pagebuilder support.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder;

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define the base object for schedules.
 */
class PageBuilder_Base {

	/**
	 * Initialize the Page Builder support.
	 *
	 * @return void
	 */
	public function init(): void {}
}
