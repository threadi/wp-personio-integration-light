<?php
/**
 * File as base for each pagebuilder support.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder;

// prevent direct access.
defined( 'ABSPATH' ) or exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Extensions_Base;

/**
 * Define the base object for schedules.
 */
class PageBuilder_Base extends Extensions_Base {
	/**
	 * Internal name of the page builder.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * Internal name of the used category.
	 *
	 * @var string
	 */
	protected string $extension_category = 'pagebuilder';

	/**
	 * True if Page Builder has templates.
	 *
	 * @var bool
	 */
	protected bool $has_templates = false;

	/**
	 * This extension can be enabled by user.
	 *
	 * @var bool
	 */
	protected bool $can_be_enabled_by_user = false;

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
