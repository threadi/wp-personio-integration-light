<?php
/**
 * File to handle email object as base-object.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Button;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Checkbox;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\MultiField;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Text;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Tab;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;

/**
 * Object to handle email objects as base-object.
 */
class Email_Base {
	/**
	 * Name of the method.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * Subject for the email.
	 *
	 * @var string
	 */
	protected string $subject = '';

	/**
	 * Body for the email.
	 *
	 * @var string
	 */
	protected string $body = '';

	/**
	 * Disabled by default on plugin installation.
	 *
	 * @var bool
	 */
	protected bool $default_enabled = false;

	/**
	 * Test-mode if true.
	 *
	 * @var bool
	 */
	private bool $test = false;

	/**
	 * Return name of the method.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Add settings for this email.
	 *
	 * @param Settings $settings_obj The settings object.
	 * @param Tab      $email_tab    The email tab in the settings.
	 *
	 * @return void
	 */
	public function add_settings( Settings $settings_obj, Tab $email_tab ): void {
		// get the URL for the main settings of WordPress.
		$wp_general_settings_url = add_query_arg(
			array(),
			get_admin_url() . 'options-general.php'
		);

		// add a section.
		$email_tab_main = $email_tab->add_section( 'settings_section_email_' . $this->get_name(), 10 );
		/* translators: %1$s will be replaced by the email title. */
		$email_tab_main->set_title( sprintf( __( 'Settings for %1$s', 'personio-integration-light' ), $this->get_title() ) );
		$email_tab_main->set_callback( array( $this, 'show_description' ) );
		$email_tab_main->set_setting( $settings_obj );

		// add setting.
		$enable_setting = $settings_obj->add_setting( 'personio_integration_email_' . $this->get_name() );
		$enable_setting->set_section( $email_tab_main );
		$enable_setting->set_type( 'integer' );
		$enable_setting->set_default( $this->is_default_enabled() ? 1 : 0 );
		$field = new Checkbox();
		$field->set_title( __( 'Enable', 'personio-integration-light' ) );
		$field->set_description( $this->get_description() );
		$enable_setting->set_field( $field );

		// create the field description.
		$description = __( 'Add one or more recipients for this email. One email per field. Get more fields after saving the settings.', 'personio-integration-light' ) . ' ';
		if ( ! empty( $this->get_default_recipient() ) ) {
			/* translators: %1$s will be replaced by the email address. */
			$description .= sprintf( __( '<strong>If no email is set we use the admin-email %1$s as recipient.</strong> You can edit the admin-email of your WordPress <a href="%2$s">here</a>.', 'personio-integration-light' ), '<code>' . $this->get_default_recipient() . '</code>', $wp_general_settings_url );
		} else {
			$description .= '<strong>' . __( 'Without recipient this email will not be sent.', 'personio-integration-light' ) . '</strong>';
		}

		// add setting.
		$setting = $settings_obj->add_setting( 'personio_integration_email_recipients_' . $this->get_name() );
		$setting->set_section( $email_tab_main );
		$setting->set_type( 'array' );
		$setting->set_default( array() );
		$field = new MultiField();
		$field->set_title( __( 'Add recipients', 'personio-integration-light' ) );
		$field->set_description( $description );
		$field->set_sanitize_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\Emails', 'validate' ) );
		$text_field = new Text();
		$text_field->set_placeholder( 'info@example.com' );
		$text_field->add_depend( $enable_setting, 1 );
		$field->set_field( $text_field );
		$field->set_quantity( count( $setting->get_value() ? $setting->get_value() : array() ) + 1 );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personio_integration_email_from_' . $this->get_name() );
		$setting->set_section( $email_tab_main );
		$setting->set_type( 'string' );
		$setting->set_default( get_option( 'admin_email' ) );
		$setting->set_read_callback( array( $this, 'set_from' ) );
		$field = new Text();
		$field->set_title( __( 'Sender Email', 'personio-integration-light' ) );
		/* translators: %1$s will be replaced by the email address. */
		$field->set_description( sprintf( __( 'If no email is set we use the admin-email %1$s as sender. You can edit the admin-email of your WordPress <a href="%2$s">here</a>.', 'personio-integration-light' ), '<code>' . get_option( 'admin_email' ) . '</code>', $wp_general_settings_url ) );
		$field->set_placeholder( 'info@example.com' );
		$field->add_depend( $enable_setting, 1 );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personio_integration_email_test_' . $this->get_name() );
		$setting->set_section( $email_tab_main );
		$setting->set_autoload( false );
		$setting->prevent_export( true );
		$field = new Button();
		$field->set_title( __( 'Test-Email', 'personio-integration-light' ) );
		$field->set_button_title( __( 'Send now', 'personio-integration-light' ) );
		$field->set_button_url(
			add_query_arg(
				array(
					'action' => 'personioPositionsEmailTest',
					'object' => $this->get_name(),
					'nonce'  => wp_create_nonce( 'personio-integration-email-test' ),
				),
				get_admin_url() . 'admin.php'
			)
		);
		$field->add_depend( $enable_setting, 1 );
		$setting->set_field( $field );
	}

	/**
	 * Return the title.
	 *
	 * @return string
	 */
	protected function get_title(): string {
		return '';
	}

	/**
	 * Return the description.
	 *
	 * @return string
	 */
	protected function get_description(): string {
		return '';
	}

	/**
	 * Return list of configured recipients.
	 *
	 * @return array<int,string>
	 */
	public function get_recipients(): array {
		// get the setting.
		$recipients = get_option( 'personio_integration_email_recipients_' . $this->get_name() );

		// if setting is empty and if we are using the test mode, use the admin email.
		if ( empty( $recipients ) && $this->is_test() ) {
			return array( get_option( 'admin_email' ) );
		}

		// if setting is empty use the default email.
		if ( empty( $recipients ) ) {
			return array( $this->get_default_recipient() );
		}

		// return the list of configured recipients.
		return $recipients;
	}

	/**
	 * Return the subject for the email.
	 *
	 * @return string
	 */
	public function get_subject(): string {
		return $this->subject;
	}

	/**
	 * Set the subject for the email.
	 *
	 * @param string $subject The subject to use.
	 *
	 * @return void
	 */
	public function set_subject( string $subject ): void {
		$this->subject = $subject;
	}

	/**
	 * Return the body for the email.
	 *
	 * @return string
	 */
	public function get_body(): string {
		// get the body.
		$body = $this->body;

		// bail if body is empty.
		if ( empty( $body ) ) {
			return '';
		}

		// get the URL where the email configuration could be found.
		$email_config_url = Helper::get_settings_url( 'personioPositions', 'emails' );

		// get the website domain.
		$domain = get_option( 'siteurl' );

		$support_part = '<div id="signature">---------------------------------------------------------';
		/* translators: %1$s will be replaced by a URL. */
		$support_part .= '<br><br>' . sprintf( __( 'This email was sent to you by the WordPress-plugin Personio Integration Light which is installed in your website under <a href="%1$s">%2$s</a>. You can disable this email <a href="%3$s">here</a>.', 'personio-integration-light' ), esc_url( $domain ), esc_html( $domain ), esc_url( $email_config_url ) );
		/* translators: %1$s will be replaced by a URL. */
		$support_part .= '<br><br>' . sprintf( __( 'If you have any questions about the message, please feel free to contact us in <a href="%1$s">our support forum</a>.', 'personio-integration-light' ), esc_url( Helper::get_plugin_support_url() ) );
		$support_part .= '</div>';

		/**
		 * Filter the support part of an email.
		 *
		 * @since 4.1.0 Available since 4.1.0.
		 * @param string $support_part The text to use.
		 */
		$support_part = apply_filters( 'personio_integration_light_import_error_support_hint', $support_part );

		// return the body with the footer.
		return $body . $support_part;
	}

	/**
	 * Set the body for the email.
	 *
	 * @param string $body The body to use.
	 *
	 * @return void
	 */
	public function set_body( string $body ): void {
		$this->body = $body;
	}

	/**
	 * Send the email.
	 *
	 * @return void
	 */
	public function send(): void {
		// bail if this email is not enabled and this is not a test.
		if ( ! $this->is_test() && 1 !== absint( get_option( 'personio_integration_email_' . $this->get_name() ) ) ) {
			return;
		}

		// bail if no recipients are set.
		if ( empty( $this->get_recipients() ) ) {
			/* translators: %1$s will be replaced by the title of the email object. */
			Log::get_instance()->add( sprintf( __( 'Recipients missing to sent email for %1$s!', 'personio-integration-light' ), $this->get_title() ), 'error', 'emails' );
			return;
		}

		// bail if no subject is set.
		if ( empty( $this->get_subject() ) ) {
			/* translators: %1$s will be replaced by the title of the email object. */
			Log::get_instance()->add( sprintf( __( 'Subject missing to sent email for %1$s!', 'personio-integration-light' ), $this->get_title() ), 'error', 'emails' );
			return;
		}

		// bail if body is not set.
		if ( empty( $this->get_body() ) ) {
			/* translators: %1$s will be replaced by the title of the email object. */
			Log::get_instance()->add( sprintf( __( 'Body missing to sent email for %1$s!', 'personio-integration-light' ), $this->get_title() ), 'error', 'emails' );
			return;
		}

		// get JSON of this email-configuration.
		$configuration_json = wp_json_encode( $this->get_debug() );
		if ( ! $configuration_json ) {
			return;
		}

		// strip all HTML from JSON.
		$configuration_json = wp_strip_all_tags( $configuration_json );

		// send email.
		if ( wp_mail( $this->get_recipients(), $this->get_subject(), $this->get_body(), $this->get_headers() ) ) {
			// log this event, if debug mode is enabled.
			if ( 1 === absint( get_option( 'personioIntegration_debug', 0 ) ) ) {
				Log::get_instance()->add( __( 'Sent email:', 'personio-integration-light' ) . ' <code>' . $configuration_json . '</code>', 'success', 'emails' );
			}
			return;
		}

		// log that mail could not be sent.
		Log::get_instance()->add( __( 'Email could not be sent:', 'personio-integration-light' ) . ' <code>' . $configuration_json . '</code>', 'error', 'emails' );
	}

	/**
	 * Return the default recipient.
	 *
	 * @return string
	 */
	protected function get_default_recipient(): string {
		return '';
	}

	/**
	 * Return the mail headers.
	 *
	 * @return array<int,string>
	 */
	private function get_headers(): array {
		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $this->get_from(),
			'X-Mailer: ' . Helper::get_plugin_name(),
		);

		/**
		 * Filter the email header.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param array<int,string> $headers List of headers.
		 */
		return apply_filters( 'personio_integration_light_email_headers', $headers );
	}

	/**
	 * Return a debug array with all value of this email.
	 *
	 * @return array<string,mixed>
	 */
	private function get_debug(): array {
		return array(
			'used_email_object' => $this->get_name(),
			'header'            => $this->get_headers(),
			'recipients'        => $this->get_recipients(),
			'subject'           => $this->get_subject(),
			'body'              => $this->get_body(),
		);
	}

	/**
	 * Show description for this email object.
	 *
	 * @return void
	 */
	public function show_description(): void {}

	/**
	 * Return whether this object is enabled by default.
	 *
	 * @return bool
	 */
	private function is_default_enabled(): bool {
		return $this->default_enabled;
	}

	/**
	 * Prepare the object for a test email.
	 *
	 * @return void
	 */
	protected function prepare_for_test(): void {}

	/**
	 * Cleanup the object after test email.
	 *
	 * @return void
	 */
	protected function cleanup_after_test(): void {}

	/**
	 * Trigger a test email with default content.
	 *
	 * @return void
	 */
	public function send_test(): void {
		// mark as test.
		$this->set_test();

		// prepare individual setting in email object.
		$this->prepare_for_test();

		// send the email.
		$this->send();

		// cleanup after test.
		$this->cleanup_after_test();
	}

	/**
	 * Return whether the object is in test-mode.
	 *
	 * @return bool
	 */
	private function is_test(): bool {
		return $this->test;
	}

	/**
	 * Set test mode.
	 *
	 * @return void
	 */
	private function set_test(): void {
		$this->test = true;
	}

	/**
	 * Return the configured from address we should use.
	 *
	 * @return string
	 */
	private function get_from(): string {
		return get_option( 'personio_integration_email_from_' . $this->get_name() );
	}

	/**
	 * Set the "from" email during reading the setting.
	 *
	 * If no "from" email is set, use the admin email.
	 *
	 * @param string|null $value The value from setting.
	 *
	 * @return string
	 */
	public function set_from( string|null $value ): string {
		// if value is null, create a string.
		if ( is_null( $value ) ) {
			$value = '';
		}

		// if value is not empty, return its value.
		if ( ! empty( $value ) ) {
			return $value;
		}

		// otherwise return the admin email.
		return get_option( 'admin_email' );
	}
}
