<?php
/**
 * File to handle assigning of Personio accounts for positions in pro-plugin.
 *
 * @package wp-personio-integration
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\Plugin\Schedules_Base;

/**
 * Object to handle files for positions in pro-plugin.
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
	protected string $setting_field = 'personioIntegrationUrlsStatus';

	/**
	 * Internal name of the used category.
	 *
	 * @var string
	 */
	protected string $extension_category = 'positions';

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
	 * Initialize this extension.
	 *
	 * @return void
	 */
	public function init(): void {
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
}
