<?php
/**
 * File to handle assigning of Personio accounts for positions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Text;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Page;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Section;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Tab;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Languages;

/**
 * Object to handle files for positions.
 */
class Personio_Accounts extends Extensions_Base {
	/**
	 * The internal name of this extension.
	 *
	 * @var string
	 */
	protected string $name = 'personio_accounts';

	/**
	 * Name if the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = 'personioIntegrationPersonioAccountsStatus';

	/**
	 * This extension can be enabled by user.
	 *
	 * Defaults to true as most extensions will be.
	 *
	 * @var bool
	 */
	protected bool $can_be_enabled_by_user = false;

	/**
	 * Internal name of the used category.
	 *
	 * @var string
	 */
	protected string $extension_category = 'positions';

	/**
	 * Name if the setting tab where the setting field is visible.
	 *
	 * @var string
	 */
	protected string $setting_tab = 'basic';

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Personio_Accounts
	 */
	private static ?Personio_Accounts $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Personio_Accounts {
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
		// use hooks.
		add_action( 'init', array( $this, 'add_settings' ), 20 );
		add_filter( 'site_status_tests', array( $this, 'add_checks' ) );

		// use our own hooks.
		add_filter( 'personio_integration_site_health_endpoints', array( $this, 'add_site_health' ) );
	}

	/**
	 * Define settings for this object.
	 *
	 * @return void
	 */
	public function add_settings(): void {
		// get the settings object.
		$settings_obj = \PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings::get_instance();

		// get the main page.
		$main_page = $settings_obj->get_page( $this->get_settings_page() );

		// bail if page could not be loaded.
		if ( ! $main_page instanceof Page ) {
			return;
		}

		// get the main settings tab.
		$main_tab = $main_page->get_tab( $this->get_setting_tab() );

		// bail if main tab could not be loaded.
		if ( ! $main_tab instanceof Tab ) {
			return;
		}

		// get main section.
		$main_section = $main_tab->get_section( 'settings_section_main' );

		// bail if section could not be loaded.
		if ( ! $main_section instanceof Section ) {
			return;
		}

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationLoginUrl' );
		$setting->set_section( $main_section );
		$setting->set_type( 'string' );
		$setting->set_default( '' );
		$setting->set_save_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationLoginUrl', 'validate' ) );
		$field = new Text();
		$field->set_title( __( 'Personio Login URL', 'personio-integration-light' ) );
		/* translators: %1$s is replaced with the URL to the Personio support */
		$field->set_description( sprintf( __( 'This URL is used by Personio to give you a unique login URL to your Personio account. It will be communicated to you when you register with Personio.<br>Entering this URL activates links in the WordPress backend that allow you to quickly switch from WordPress to Personio to edit positions.<br>This is NOT the URL where your open positions are visible.<br>If you have any questions about this URL, please contact the <a href="%1$s" target="_blank">Personio support (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_personio_support_url() ) ) );
		$field->set_placeholder( $this->get_login_url_example() );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$field->set_sanitize_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationLoginUrl', 'validate' ) );
		$setting->set_field( $field );
	}

	/**
	 * Return list of Personio URLs which should be used to import positions.
	 *
	 * The array contains the URLs as strings.
	 *
	 * @return array<string>
	 */
	public function get_personio_urls(): array {
		// define list of Personio URLs.
		$personio_urls = array();

		// add the configured Personio URL, if set.
		if ( Helper::is_personio_url_set() ) {
			$personio_urls[] = Helper::get_personio_url();
		}

		/**
		 * Filter the list of Personio URLs used to import positions.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array<string> $personio_urls List of Personio URLs.
		 */
		return apply_filters( 'personio_integration_personio_urls', $personio_urls );
	}

	/**
	 * Reset the Personio settings complete.
	 *
	 * @return void
	 *
	 * @noinspection PhpUnused
	 */
	public function reset_personio_settings(): void {
		foreach ( $this->get_personio_urls() as $personio_url ) {
			$personio_obj = new Personio( $personio_url );
			foreach ( Languages::get_instance()->get_languages() as $language_name => $label ) {
				$personio_obj->remove_timestamp( $language_name );
				delete_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $language_name );
				$personio_obj->remove_md5( $language_name );
			}
		}
	}

	/**
	 * Add checks for Personio URLs for site health.
	 *
	 * @param array<int,array<string,mixed>> $check_list List of checks.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public function add_site_health( array $check_list ): array {
		// bail if Availability is not enabled.
		if ( ! Availability::get_instance()->is_enabled() ) {
			return $check_list;
		}

		// add one check for each Personio URL.
		foreach ( self::get_instance()->get_personio_urls() as $personio_url ) {
			$check_list[] = array(
				'namespace' => 'personio/v1',
				'route'     => '/url_availability_checks_' . md5( $personio_url ) . '/',
				'callback'  => array( Availability::get_instance(), 'url_availability_checks' ),
				'args'      => array( array( 'personio_url' => $personio_url ) ),
			);
		}

		// return the resulting list.
		return $check_list;
	}

	/**
	 * Add custom status-check for running cronjobs of our own plugin.
	 * Only if Personio-URL is set.
	 *
	 * @param array<string,mixed> $statuses List of tests to run.
	 *
	 * @return array<string,mixed>
	 */
	public function add_checks( array $statuses ): array {
		// one check for each Personio URL.
		foreach ( self::get_instance()->get_personio_urls() as $personio_url ) {
			// get the md5.
			$md5 = md5( $personio_url );

			// add to list.
			$statuses['async'][ 'personio_integration_url_availability_check_' . $md5 ] = array(
				'label'    => __( 'Personio Integration URL availability check', 'personio-integration-light' ),
				'test'     => rest_url( 'personio/v1/url_availability_checks_' . $md5 ),
				'has_rest' => true,
			);
		}
		return $statuses;
	}

	/**
	 * Return label of this extension.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Personio Account', 'personio-integration-light' );
	}

	/**
	 * Return whether this extension is enabled (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return 1 === absint( get_option( $this->get_settings_field_name() ) );
	}

	/**
	 * Return the description for this extension.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return __( 'Adds the handling of your Personio account in this plugin.', 'personio-integration-light' );
	}

	/**
	 * Whether this extension is enabled by default (true) or not (false).
	 *
	 * @return bool
	 */
	protected function is_default_enabled(): bool {
		return true;
	}

	/**
	 * Return the installation state of the dependent plugin/theme.
	 *
	 * @return bool
	 */
	public function is_installed(): bool {
		return true;
	}

	/**
	 * Return the configured login URL.
	 *
	 * If none is set, return the language-specific general Personio account login url.
	 *
	 * @return string
	 */
	public function get_login_url(): string {
		// get the configured Personio Login URL.
		$personio_login_url = get_option( 'personioIntegrationLoginUrl' );

		// return default URLs, if no Login URL is configured.
		if ( empty( $personio_login_url ) ) {
			if ( Languages::get_instance()->is_german_language() ) {
				return 'https://www.personio.de/login/';
			}
			return 'https://www.personio.com/login/';
		}

		// return the custom Personio Login URL.
		return $personio_login_url;
	}

	/**
	 * Return language-specific Personio Login URL example.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	private function get_login_url_example(): string {
		return Languages::get_instance()->is_german_language() ? 'https://dein-unternehmen.app.personio.com' : 'https://your-company.app.personio.com';
	}

	/**
	 * Return the URL to edit this position on Personio.
	 *
	 * Needs the login URL to work.
	 *
	 * @param Position $position_obj The position object.
	 *
	 * @return string
	 */
	public function get_edit_link_on_personio( Position $position_obj ): string {
		// get the configured Personio Login URL.
		$personio_login_url = get_option( 'personioIntegrationLoginUrl' );

		// bail if no login URL is given.
		if ( empty( $personio_login_url ) ) {
			return '';
		}

		// get the URL.
		return $personio_login_url . '/recruiting/position/' . $position_obj->get_personio_id() . '?tab=job-details';
	}

	/**
	 * Return HTML-link with icon to edit specific entity in Personio account.
	 *
	 * @param Position $position_obj The object of the position.
	 *
	 * @return string
	 */
	public function get_personio_edit_link( Position $position_obj ): string {
		// get the configured Personio Login URL.
		$personio_login_url = get_option( 'personioIntegrationLoginUrl' );

		// bail if no login URL is given.
		if ( empty( $personio_login_url ) ) {
			return '';
		}

		return ' <a href="' . esc_url( $this->get_edit_link_on_personio( $position_obj ) ) . '" target="_blank" class="personio-integration-icon-link"><span class="dashicons dashicons-edit"></span></a>';
	}

	/**
	 * Return the URL where the user could manage its API integrations in Personio.
	 *
	 * @return string
	 */
	public function get_personio_api_management_url(): string {
		return get_option( 'personioIntegrationLoginUrl' ) . '/configuration/marketplace/connected';
	}
}
