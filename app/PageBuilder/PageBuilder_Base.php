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

use PersonioIntegrationLight\Helper;

/**
 * Define the base object for schedules.
 */
class PageBuilder_Base {
	/**
	 * Internal name of the page builder.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * True if Page Builder has templates.
	 *
	 * @var bool
	 */
	protected bool $has_templates = false;

	/**
	 * Initialize the Page Builder support.
	 *
	 * @return void
	 */
	public function init(): void {
		// add page builder to list of used page builder in this project.
		Helper::update_page_builder_list( $this->get_name() );
	}

	/**
	 * Return widgets this page builder supports.
	 *
	 * This means any widgets, block, component ... name it.
	 *
	 * @return array
	 */
	public function get_widgets(): array {
		return array();
	}

	/**
	 * Return whether this page builder supports templates.
	 *
	 * @return bool
	 */
	public function has_templates(): bool {
		return $this->has_templates;
	}

	/**
	 * Installer for templates this page builder is using.
	 *
	 * @return bool
	 */
	public function install_templates(): bool {
		return false;
	}

	/**
	 * Return the internal name of the page builder.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}
}
