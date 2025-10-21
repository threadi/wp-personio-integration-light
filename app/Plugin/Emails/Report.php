<?php
/**
 * File for handling report about the positions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Emails;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Checkbox;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Select;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Section;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Setting;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Tab;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\Statistics;
use PersonioIntegrationLight\Plugin\Email_Base;

/**
 * Object which handle report about the positions.
 */
class Report extends Email_Base {
	/**
	 * Internal name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'report';

	/**
	 * Constructor for this object.
	 */
	public function __construct() {}

	/**
	 * Return the title.
	 *
	 * @return string
	 */
	protected function get_title(): string {
		return __( 'Report about your positions', 'personio-integration-light' );
	}

	/**
	 * Return the description.
	 *
	 * @return string
	 */
	protected function get_description(): string {
		return __( 'If activated, an email is sent to the specified email address with a report about your positions in your website.', 'personio-integration-light' );
	}

	/**
	 * Show description for this email object.
	 *
	 * @return void
	 */
	public function show_description(): void {
		/* translators: %1$s will be replaced a link. */
		echo wp_kses_post( sprintf( __( 'The report contains information about the number of positions on your website, the number of locations where they are advertised and the categories in which they are located. The report can also be viewed <a href="$1%s">here</a> at any time.', 'personio-integration-light' ), '' ) );
	}

	/**
	 * Add settings for this email.
	 *
	 * @param Settings $settings_obj The settings object.
	 * @param Tab      $email_tab The email tab in the settings.
	 *
	 * @return void
	 */
	public function add_settings( Settings $settings_obj, Tab $email_tab ): void {
		// add the main settings from parent object.
		parent::add_settings( $settings_obj, $email_tab );

		// get possible schedules.
		$schedules = array_map(
			static function ( $schedule ) {
				return $schedule['display'];
			},
			wp_get_schedules()
		);

		// get our own section.
		$email_tab_main = $email_tab->get_section( 'settings_section_email_' . $this->get_name() );

		// bail if section does not exist.
		if ( ! $email_tab_main instanceof Section ) {
			return;
		}

		// get the main setting.
		$main_setting = $settings_obj->get_setting( 'personio_integration_email_' . $this->get_name() );

		// bail if settings could not be loaded.
		if ( ! $main_setting instanceof Setting ) {
			return;
		}

		// add callback for main setting.
		$main_setting->set_save_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsSavings\ReportInterval', 'save' ) );

		// add setting.
		$setting = $settings_obj->add_setting( 'personio_integration_email_interval_' . $this->get_name() );
		$setting->set_section( $email_tab_main );
		$setting->set_type( 'string' );
		$setting->set_default( 'weekly' );
		$field = new Select();
		$field->set_title( __( 'Choose interval', 'personio-integration-light' ) );
		$field->set_options( $schedules );
		$field->add_depend( $main_setting, 1 );
		$field->set_sanitize_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\ScheduleInterval', 'validate' ) );
		$setting->set_field( $field );
	}

	/**
	 * Return the subject.
	 *
	 * @return string
	 */
	public function get_subject(): string {
		// set our custom subject.
		$this->subject = get_bloginfo( 'name' ) . ': ' . __( 'Report about your positions', 'personio-integration-light' );

		// return the parent tasks for subject.
		return parent::get_subject();
	}

	/**
	 * Return the body.
	 *
	 * @return string
	 */
	public function get_body(): string {
		// create the body.
		$body = __( 'This report contains statistical data about the open positions on your WordPress website. It will be sent to you automatically on a regular basis.', 'personio-integration-light' );

		$body .= Statistics::get_instance()->get_table();

		// set the body.
		$this->body = $body;

		// return the parent tasks for body.
		return parent::get_body();
	}
}
