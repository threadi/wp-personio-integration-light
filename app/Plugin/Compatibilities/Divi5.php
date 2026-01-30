<?php
/**
 * File to handle the compatibility-check for Divi 5.0 or newer.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Compatibilities;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Compatibilities_Base;
use PersonioIntegrationLight\Dependencies\easyTransientsForWordPress\Transients;

/**
 * Object for this check.
 */
class Divi5 extends Compatibilities_Base {

	/**
	 * Name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_compatibility_divi5';

	/**
	 * Instance of this object.
	 *
	 * @var ?Divi5
	 */
	private static ?Divi5 $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Divi5 {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Run the check.
	 *
	 * @return void
	 */
	public function check(): void {
		$transients_obj = Transients::get_instance();
		if ( $this->is_active() ) {
			$transient_obj = $transients_obj->add();
			$transient_obj->set_name( $this->get_name() );
			/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
			$transient_obj->set_message( sprintf( __( '<strong>We realized that you are using Divi 5 - very nice!</strong> <a href="%1$s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Divi.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->set_dismissible_days( 30 );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( $this->get_name() )->delete();
		}
	}

	/**
	 * Check if Divi 5.0 or newer is active.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		// first check for the plugin.
		if( Helper::is_plugin_active( 'divi-builder/divi-builder.php' ) ) {
			// get the plugin version.
			require_once ABSPATH . 'wp-admin/includes/admin.php';
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/divi-builder/divi-builder.php' );
			if ( version_compare( $plugin_data['Version'], '5.0.0', '>=' ) ) {
				return true;
			}
		}

		// check for the theme.
		$theme   = wp_get_theme();
		if ( 'Divi' === $theme->get( 'Name' ) ) {
			$version = substr( $theme->get( 'Version' ), 0, 5 );
			return version_compare( $version, '5.0.0', '>=');
		}

		// check for the parent theme.
		if ( $theme->parent() && 'Divi' === $theme->parent()->get( 'Name' ) ) {
			$version = substr( $theme->parent()->get( 'Version' ), 0, 5 );
			return version_compare( $version, '5.0.0', '>=');
		}

		// otherwise return false.
		return false;
	}
}
