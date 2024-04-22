<?php
/**
 * File to handle the compatibility-check for Divi.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Compatibilities;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Compatibilities_Base;
use PersonioIntegrationLight\Plugin\Transients;

/**
 * Object for this check.
 */
class Divi extends Compatibilities_Base {

	/**
	 * Name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_compatibility_divi';

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
			$transient_obj->set_message( sprintf( __( '<strong>We realized that you are using Divi - very nice!</strong> <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Divi.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->set_dismissible_days( 30 );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( $this->get_name() )->delete();
		}
	}

	/**
	 * Check if Divi is active.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		$is_divi = Helper::is_plugin_active( 'divi-builder/divi-builder.php' );
		$theme   = wp_get_theme();
		if ( 'Divi' === $theme->get( 'Name' ) ) {
			$is_divi = true;
		}
		if ( $theme->parent() && 'Divi' === $theme->parent()->get( 'Name' ) ) {
			$is_divi = true;
		}
		return $is_divi;
	}
}
