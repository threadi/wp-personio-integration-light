<?php
/**
 * File for manage any Personio API v2 tasks. This is not an extension but is used by some of them.
 *
 * Hint: API v2 from Personio is Beta and should not and can not be used from productive systems.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Text;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Page;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Section;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Tab;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Crypt;
use PersonioIntegrationLight\Plugin\Schedules\ApiAccessToken;

/**
 * Object to manage any Personio API tasks.
 */
class Api {
	/**
	 * Instance of this object.
	 *
	 * @var ?Api
	 */
	private static ?Api $instance = null;

	/**
	 * Constructor for this object.
	 */
	public function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Api {
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
		// bail if WordPress has not enabled the development mode.
		if ( ! Helper::is_development_mode_active() ) {
			return;
		}

		// add settings.
		add_action( 'init', array( $this, 'add_the_settings' ), 20 );

		// use option hooks.
		add_action( 'update_option_personioIntegrationApiSecret', array( $this, 'check_for_api_support' ), 10, 2 );

		// use our own hooks.
		add_filter( 'personio_integration_objects_with_db_tables', array( $this, 'add_table' ) );
		add_filter( 'personio_integration_log_categories', array( $this, 'add_category' ) );
		add_filter( 'personio_integration_schedules', array( $this, 'add_schedule' ) );
	}

	/**
	 * Add settings for API.
	 *
	 * @return void
	 */
	public function add_the_settings(): void {
		// get settings object.
		$settings_obj = Settings::get_instance();

		// get the main settings page.
		$main_settings_page = $settings_obj->get_page( 'personioPositions' );

		// bail if page could not be loaded.
		if ( ! $main_settings_page instanceof Page ) {
			return;
		}

		// get the general tab.
		$general_tab = $main_settings_page->get_tab( 'basic' );

		// bail if basic tab does not exist.
		if ( ! $general_tab instanceof Tab ) {
			return;
		}

		// create the section.
		$api_section = $general_tab->add_section( 'settings_section_api', 70 );
		$api_section->set_title( __( 'Settings for API', 'personio-integration-light' ) );
		$api_section->set_setting( $settings_obj );
		$api_section->set_callback( array( $this, 'show_api_settings_hint' ) );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationClientId' );
		$setting->set_section( $api_section );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'string' );
		$setting->set_default( '' );
		$setting->set_read_callback( array( '\PersonioIntegrationLight\Plugin\Admin\SettingsRead\GetDecryptValue', 'get' ) );
		$setting->set_save_callback( array( '\PersonioIntegrationLight\Plugin\Admin\SettingsSavings\SaveAsCryptValue', 'save' ) );
		$field = new Text();
		$field->set_title( __( 'Your Client-ID', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationApiSecret' );
		$setting->set_section( $api_section );
		$setting->set_show_in_rest( true );
		$setting->set_type( 'string' );
		$setting->set_default( '' );
		$setting->set_read_callback( array( '\PersonioIntegrationLight\Plugin\Admin\SettingsRead\GetDecryptValue', 'get' ) );
		$setting->set_save_callback( array( '\PersonioIntegrationLight\Plugin\Admin\SettingsSavings\SaveAsCryptValue', 'save' ) );
		$field = new Text();
		$field->set_title( __( 'Access token', 'personio-integration-light' ) );
		$field->set_readonly( ! Helper::is_personio_url_set() );
		$setting->set_field( $field );

		// get the hidden section.
		$hidden = \PersonioIntegrationLight\Plugin\Settings::get_instance()->get_hidden_section();

		// bail if hidden section does not exist.
		if ( ! $hidden instanceof Section ) {
			return;
		}

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationEnableApiAccessToken' );
		$setting->set_section( $hidden );
		$setting->set_type( 'integer' );
		$setting->set_default( 0 );
	}

	/**
	 * Show hint why API credentials are necessary.
	 *
	 * @return void
	 */
	public function show_api_settings_hint(): void {
		if ( empty( get_option( 'personioIntegrationLoginUrl' ) ) ) {
			/* translators: %1$s will be replaced by a URL. */
			echo '<p>' . wp_kses_post( sprintf( __( 'You can find this information in your Personio account (opens in new window) under Marketplace > Integrations. For more information take a look in the <a href="%1$s">Personio documentation about API credentials</a>.', 'personio-integration-light' ), esc_url( Helper::get_personio_api_documentation_url() ) ) ) . '</p>';
			return;
		}

		/* translators: %1$s will be replaced by a URL. */
		echo '<p>' . wp_kses_post( sprintf( __( 'You can find this information <a href="%1$s" target="_blank">in your Personio account (opens in new window)</a> under Marketplace > Integrations. For more information take a look in the <a href="%2$s">Personio documentation about API credentials</a>.', 'personio-integration-light' ), esc_url( Personio_Accounts::get_instance()->get_personio_api_management_url() ), esc_url( Helper::get_personio_api_documentation_url() ) ) ) . '</p>';
	}

	/**
	 * Return whether the credentials are prepared.
	 *
	 * @return bool
	 */
	private function is_credential_prepared(): bool {
		return ! empty( get_option( 'personioIntegrationClientId' ) ) && ! empty( get_option( 'personioIntegrationApiSecret' ) );
	}

	/**
	 * Get the access token for the Personio API.
	 *
	 * If token is not set, get it atm via API request.
	 *
	 * @return string
	 */
	public function get_access_token(): string {
		// bail if credentials are not set.
		if ( ! $this->is_credential_prepared() ) {
			return '';
		}

		// get the saved token from DB.
		$access_token = Crypt::get_instance()->decrypt( get_transient( 'personio_integration_api_token' ) );

		// return token if it is known.
		if ( ! empty( $access_token ) ) {
			return $access_token;
		}

		/**
		 * As access token is unknown we request a new one from Personio API.
		 */

		// define our custom header for this API request.
		add_filter( 'personio_integration_light_request_header', array( $this, 'set_api_request_header_for_bearer_update' ) );

		// collect the post data for this request.
		$post_data = array(
			'client_id'     => get_option( 'personioIntegrationClientId' ),
			'client_secret' => get_option( 'personioIntegrationApiSecret' ),
			'grant_type'    => 'client_credentials',
		);

		// create the request.
		$request_object = new Api_Request();
		$request_object->set_url( 'https://api.personio.de/v2/auth/token' );
		$request_object->set_post_data( $post_data );

		// send it.
		$request_object->send();

		// bail if HTTP-status is not 200.
		if ( 200 !== $request_object->get_http_status() ) {
			return '';
		}

		// get the response.
		$response = json_decode( $request_object->get_response(), true );

		// bail if response does not contain "access_token", "expires_in or "token_type".
		if ( ! isset( $response['access_token'], $response['token_type'], $response['expires_in'] ) ) {
			return '';
		}

		// get the access token.
		$access_token = $response['access_token'];

		// bail if token is empty.
		if ( empty( $access_token ) ) {
			return '';
		}

		// save this token as transients.
		set_transient( 'personio_integration_api_token', Crypt::get_instance()->encrypt( $access_token ), absint( $response['expires_in'] ) );

		// return the token.
		return $access_token;
	}

	/**
	 * Revoke the access token.
	 *
	 * @return void
	 */
	public function delete_access_token(): void {
		// get the access token from DB.
		$access_token = $this->get_access_token_from_db();

		// bail if token is not set.
		if ( empty( $access_token ) ) {
			return;
		}

		// define our custom header for this API request.
		add_filter( 'personio_integration_light_request_header', array( $this, 'set_api_request_header' ) );

		// collect the post data for this request.
		$post_data = array(
			'token' => $access_token,
		);

		// create the request.
		$request_object = new Api_Request();
		$request_object->set_url( 'https://api.personio.de/v2/auth/revoke' );
		$request_object->set_post_data( $post_data );

		// send it.
		$request_object->send();

		// bail if HTTP-status is not 200.
		if ( 200 !== $request_object->get_http_status() ) {
			return;
		}

		// delete the token in DB.
		delete_transient( 'personio_integration_api_token' );
	}

	/**
	 * Add this object to the list of objects which add tables in the DB.
	 *
	 * @param array<int,string> $table_objects List of objects which add tables in the DB.
	 *
	 * @return array<int,string>
	 */
	public function add_table( array $table_objects ): array {
		$table_objects[] = '\PersonioIntegrationLight\PersonioIntegration\Api';
		return $table_objects;
	}

	/**
	 * Create request table on plugin activation.
	 *
	 * @return void
	 */
	public function create_table(): void {
		global $wpdb;

		// initialize the database-tables.
		$charset_collate = $wpdb->get_charset_collate();

		// table for applications meta-data.
		$table_name = $wpdb->prefix . 'personio_api_requests';
		$sql        = "CREATE TABLE $table_name (
                `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                `insertdate` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                `md5` varchar(32) NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

		// get db-functions.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Add log category for Personio API.
	 *
	 * @param array<string> $categories List of categories.
	 *
	 * @return array<string>
	 */
	public function add_category( array $categories ): array {
		// add category for this extension type.
		$categories['api'] = __( 'Personio API', 'personio-integration-light' );

		// return resulting list.
		return $categories;
	}

	/**
	 * Set a specific HTTP-header for update of bearer token.
	 *
	 * @return array<string,mixed>
	 */
	public function set_api_request_header_for_bearer_update(): array {
		return array(
			'Content-Type' => 'application/x-www-form-urlencoded',
		);
	}

	/**
	 * Get the decrypted access token from DB.
	 *
	 * @return string
	 */
	private function get_access_token_from_db(): string {
		return Crypt::get_instance()->decrypt( get_transient( 'personio_integration_api_token' ) );
	}

	/**
	 * Set a specific HTTP-header for update of bearer token.
	 *
	 * @return array<string,mixed>
	 */
	public function set_api_request_header(): array {
		// get the access token from DB.
		$access_token = $this->get_access_token_from_db();

		// return the header.
		return array(
			'Content-Type'  => 'application/x-www-form-urlencoded',
			'Authorization' => 'Bearer ' . $access_token,
			'Beta'          => 'true',
		);
	}

	/**
	 * Update the access token (e.g. via schedule).
	 *
	 * @return void
	 */
	public function update_access_token(): void {
		$this->get_access_token();
	}

	/**
	 * If token changes get the access token in one first request and enable the schedule if token has a value.
	 *
	 * If token has no value remove the access token and disable the schedule.
	 *
	 * @param string $old_value The old value.
	 * @param string $new_value The new value.
	 *
	 * @return void
	 */
	public function check_for_api_support( string $old_value, string $new_value ): void {
		// bail if value has not been changed.
		if ( Crypt::get_instance()->decrypt( $new_value ) === $old_value ) {
			return;
		}

		// get the schedule object.
		$schedule_obj = new ApiAccessToken();

		// if new value is empty, remove the access token and disable the schedule.
		if ( empty( $new_value ) ) {
			$schedule_obj->delete();
			$this->delete_access_token();
			update_option( 'personioIntegrationEnableApiAccessToken', 0 );
			return;
		}

		// get the access token.
		$this->get_access_token();

		// enable the schedule settings.
		update_option( 'personioIntegrationEnableApiAccessToken', 1 );

		// install the schedule to update the token daily.
		$schedule_obj->install();
	}

	/**
	 * Add our own schedule to the list.
	 *
	 * @param array<string> $list_of_schedules List of schedules.
	 *
	 * @return array<string>
	 */
	public function add_schedule( array $list_of_schedules ): array {
		// add the schedule-objekt.
		$list_of_schedules[] = '\PersonioIntegrationLight\Plugin\Schedules\ApiAccessToken';

		// return resulting list.
		return $list_of_schedules;
	}
}
