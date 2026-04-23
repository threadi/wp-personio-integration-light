<?php
/**
 * File to handle the compatibility-check for Generate Press Premium.
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
class GeneratePressPremium extends Compatibilities_Base {

	/**
	 * Name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_compatibility_gppremium';

	/**
	 * Instance of this object.
	 *
	 * @var ?GeneratePressPremium
	 */
	private static ?GeneratePressPremium $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): GeneratePressPremium {
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
			$transient_obj->set_message( sprintf( __( '<strong>We realized that you are using Generate Press Premium - very nice!</strong> <a href="%1$s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Generate Press Elements.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->set_dismissible_days( 30 );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( $this->get_name() )->delete();
		}
	}

	/**
	 * Check if this plugin is active.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return Helper::is_plugin_active( 'gp-premium/gp-premium.php' );
	}
}
