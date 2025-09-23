<?php
/**
 * File to handle crypt methods as base-object.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easyTransientsForWordPress\Transients;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;

/**
 * Object to handle crypt methods as base-object.
 */
class Crypt_Base {
	/**
	 * Name of the method.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * The hash for encryption.
	 *
	 * @var string
	 */
	protected string $hash = '';

	/**
	 * The constant used in wp-config.php for the hash.
	 *
	 * @var string
	 */
	protected string $constant = '';

	/**
	 * Constructor for this object.
	 */
	protected function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	protected function __clone() {}

	/**
	 * Initialize this crypt method.
	 *
	 * @return void
	 */
	public function init(): void {}

	/**
	 * Return whether this method is usable in this hosting.
	 *
	 * @return bool
	 */
	public function is_usable(): bool {
		return false;
	}

	/**
	 * Return name of the method.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Encrypt a given string.
	 *
	 * @param string $plain_text The plain string.
	 *
	 * @return string
	 */
	public function encrypt( string $plain_text ): string {
		if ( empty( $plain_text ) ) {
			return $plain_text;
		}
		return '';
	}

	/**
	 * Decrypt a given string.
	 *
	 * @param string $encrypted_text The encrypted string.
	 *
	 * @return string
	 */
	public function decrypt( string $encrypted_text ): string {
		if ( empty( $encrypted_text ) ) {
			return $encrypted_text;
		}
		return '';
	}

	/**
	 * Return hash for encryption.
	 *
	 * @return string
	 */
	public function get_hash(): string {
		return $this->hash;
	}

	/**
	 * Set hash for encryption.
	 *
	 * @param string $hash The hash.
	 *
	 * @return void
	 */
	protected function set_hash( string $hash ): void {
		$this->hash = $hash;
	}

	/**
	 * Return whether the hash is saved in wp-config.php.
	 *
	 * @return bool
	 */
	public function is_hash_saved(): bool {
		return defined( $this->get_constant() );
	}

	/**
	 * Run the constant.
	 *
	 * @return void
	 */
	protected function run_constant(): void {
		if ( $this->is_hash_saved() ) {
			return;
		}
		define( $this->get_constant(), $this->get_hash() );
	}

	/**
	 * Return the used constant in wp-config.php.
	 *
	 * @return string
	 */
	protected function get_constant(): string {
		return $this->constant;
	}

	/**
	 * Return the header for the MU-plugin.
	 *
	 * @return string
	 */
	private function get_php_header(): string {
		return '
/**
 * Plugin Name:       Personio Integration Light Hash
 * Description:       Holds the Personio Integration Light hash value, which is necessary to use encryption.
 * Requires at least: 4.9.24
 * Requires PHP:      8.1
 * Requires Plugins:  personio-integration-light
 * Version:           1.0.0
 * Author:            laOlaWeb
 * Author URI:        https://laolaweb.com
 * Text Domain:       personio-integration-light-hash
 *
 * @package personio-integration-light-hash
 */';
	}

	/**
	 * Return the mu plugin filename.
	 *
	 * @return string
	 */
	private function get_mu_plugin_filename(): string {
		return 'personio-integration-light-hash.php';
	}

	/**
	 * Create the MU-plugin which is used as fallback if wp-config.php could not be changed.
	 *
	 * @return void
	 */
	protected function create_mu_plugin(): void {
		// bail if WPMU_PLUGIN_DIR is not set.
		if( ! defined( 'WPMU_PLUGIN_DIR' ) ) {
			return;
		}

		// get WP Filesystem-handler.
		$wp_filesystem = Helper::get_wp_filesystem();

		// create a custom must-use-plugin instead.
		$file_content = '<?php ' . $this->get_php_header() . "\ndefine( '" . $this->get_constant() . "', '" . $this->get_hash() . "' ); // Added by Personio Integration Light.\r\n";

		// create mu-plugin directory if it is missing.
		if ( ! $wp_filesystem->exists( WPMU_PLUGIN_DIR ) ) {
			$wp_filesystem->mkdir( WPMU_PLUGIN_DIR );
		}

		// define path.
		$file_path = WPMU_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->get_mu_plugin_filename();

		// save the file.
		if( ! $wp_filesystem->put_contents( $file_path, $file_content ) ) {
			// trigger warning if the fallback file could also not be saved.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_crypt_error' );
			$transient_obj->set_message( __( 'The security token for encryption could not be saved. Neither <em>wp-config.php</em> nor the mu-plugin directory are writable. Please contact your hosters support team for assistance.', 'personio-integration-light' ) );
			$transient_obj->set_type( 'error' );
			$transient_obj->save();

			// log this event.
			Log::get_instance()->add( __( 'The security token for encryption could not be saved. Neither wp-config.php nor the mu-plugin directory are writable. Please contact your hosters support team for assistance.', 'personio-integration-light' ), 'error', 'system' );
			return;
		}

		// run the constant for this process.
		$this->run_constant();
	}

	/**
	 * Delete our own mu-plugin.
	 *
	 * @return void
	 */
	protected function delete_mu_plugin(): void {
		// bail if WPMU_PLUGIN_DIR is not set.
		if( ! defined( 'WPMU_PLUGIN_DIR' ) ) {
			return;
		}

		// get WP Filesystem-handler.
		$wp_filesystem = Helper::get_wp_filesystem();

		// bail if mu directory does not exist.
		if ( ! $wp_filesystem->exists( WPMU_PLUGIN_DIR ) ) {
			return;
		}

		// define path.
		$file_path = WPMU_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->get_mu_plugin_filename();

		// delete the file.
		$wp_filesystem->delete( $file_path );
	}

	/**
	 * Uninstall this method.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		// get the wp-config.php path.
		$wp_config_php_path = Helper::get_wp_config_path();

		// bail if wp-config.php is not writable.
		if ( ! Helper::is_writable( $wp_config_php_path ) ) {
			// remove mu-plugin.
			$this->delete_mu_plugin();
			return;
		}

		// get WP Filesystem-handler.
		$wp_filesystem = Helper::get_wp_filesystem();

		// get the contents of the wp-config.php.
		$wp_config_php_content = $wp_filesystem->get_contents( $wp_config_php_path );

		// bail if file has no contents.
		if ( ! $wp_config_php_content ) {
			return;
		}

		// remove the value.
		$wp_config_php_content = preg_replace( '@^[\t ]*define\s*\(\s*["\']' . $this->get_constant() . '["\'].*$@miU', '', $wp_config_php_content );

		if ( ! is_string( $wp_config_php_content ) ) {
			return;
		}

		// save the changed wp-config.php.
		$wp_filesystem->put_contents( $wp_config_php_path, $wp_config_php_content );
	}
}
