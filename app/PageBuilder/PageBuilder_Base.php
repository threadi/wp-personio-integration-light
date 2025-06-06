<?php
/**
 * File as base for each pagebuilder support.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PageBuilder;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Extensions_Base;
use PersonioIntegrationLight\Plugin\Setup;

/**
 * Define the base object for schedules.
 */
class PageBuilder_Base extends Extensions_Base {
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
		add_filter( 'init', array( $this, 'add_global_settings' ), 20 );

		// actions to run during setup.
		add_action( 'esfw_process', array( $this, 'run_setup_process' ), 20 );

		// add page builder to list of used page builder in this project.
		Helper::update_page_builder_list( $this->get_name() );
	}

	/**
	 * Return widgets this page builder supports.
	 *
	 * This means any widgets, block, component ... name it. The returning strings should contain their
	 * class names incl. namespace.
	 *
	 * @return array<string>
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
	 * Add global Elementor-settings, used by setup.
	 *
	 * @return void
	 */
	public function add_global_settings(): void {
		// bail if page builder does not support templates.
		if ( ! $this->has_templates() ) {
			return;
		}

		// get settings object.
		$settings_obj = Settings::get_instance();

		// get hidden section.
		$hidden = $settings_obj->get_section( 'hidden_section' );

		// add setting.
		$setting = $settings_obj->add_setting( 'pb_templates_import_' . $this->get_name() );
		$setting->set_section( $hidden );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'integer' );
		$setting->set_default( 0 );
		$setting->set_save_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsSavings\PageBuilder', 'save' ) );
		$setting->prevent_export( true );
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
		update_option( 'esfw_steps', $setup_obj->get_max_step() );
	}
}
