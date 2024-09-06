<?php
/**
 * File as base for each pagebuilder support.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Extensions_Base;
use PersonioIntegrationLight\Plugin\Setup;

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
		// add global settings for the page builder.
		add_filter( 'personio_integration_settings', array( $this, 'add_global_settings' ) );

		// actions to run during setup.
		add_action( 'wp_easy_setup_process', array( $this, 'run_setup_process' ), 20 );

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
	 * @return bool Returns true if import has been run successfully.
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

	/**
	 * Add global Elementor-settings, used by setup.
	 *
	 * @param array $settings List of settings.
	 *
	 * @return array
	 */
	public function add_global_settings( array $settings ): array {
		// bail if page builder does not support templates.
		if ( ! $this->has_templates() ) {
			return $settings;
		}

		// add marker for template import via setup.
		$settings['hidden_section']['fields'][ 'pb_templates_import_' . $this->get_name() ] = array(
			'register_attributes' => array(
				'type'         => 'integer',
				'show_in_rest' => true,
				'default'      => 0,
			),
			'page_builder'        => $this->get_name(),
			'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsSavings\PageBuilder', 'save' ),
			'do_not_export'       => true,
		);

		// return resulting settings.
		return $settings;
	}

	/**
	 * Run tasks on setup process depending on settings.
	 *
	 * @return void
	 */
	public function run_setup_process(): void {
		// bail if page builder does not support templates.
		if ( ! $this->has_templates() ) {
			return;
		}

		// bail if import for templates is not enabled.
		if ( 1 !== absint( get_option( 'pb_templates_import_' . $this->get_name() ) ) ) {
			return;
		}

		// get setup object.
		$setup_obj = Setup::get_instance();

		// update max step count.
		$setup_obj->update_max_step( 1 );

		// change label of progressbar in setup.
		$setup_obj->set_process_label( __( 'Import of templates running.', 'personio-integration-light' ) );

		// install templates.
		$this->install_templates();

		// set steps to max steps to end the process.
		update_option( 'wp_easy_setup_step', $setup_obj->get_max_step() );
	}

	/**
	 * Return whether this page builder is active.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return false;
	}
}
