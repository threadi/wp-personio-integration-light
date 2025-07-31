<?php
/**
 * File to handle the compatibility-check for Yoast.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Compatibilities;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Compatibilities_Base;
use easyTransientsForWordPress\Transients;

/**
 * Object for this check.
 */
class Yoast extends Compatibilities_Base {

	/**
	 * Name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_compatibility_yoast';

	/**
	 * Instance of this object.
	 *
	 * @var ?Yoast
	 */
	private static ?Yoast $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Yoast {
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
			$transient_obj->set_message( sprintf( __( '<strong>We realized that you are using Yoast to optimize your website for SEO - very nice!</strong> <a href="%1$s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to utilize the capabilities of this plugin at your positions.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->set_dismissible_days( 30 );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( $this->get_name() )->delete();
		}
	}

	/**
	 * Return whether this component is active (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return Helper::is_plugin_active( 'wordpress-seo/wp-seo.php' );
	}
}
