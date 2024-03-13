<?php
/**
 * File to handle the compatibility-check for Contact Form 7.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Compatibilities;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Compatibilities_Base;
use PersonioIntegrationLight\Plugin\Transients;

/**
 * Object for this check.
 */
class Contact_Form_7 extends Compatibilities_Base {

	/**
	 * Name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_compatibility_cf7';

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
			$transient_obj->set_message( sprintf( __( 'We realized that you are using Contact Form 7 - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design your application forms with Contact Form 7.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->set_dismissible_days( 30 );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( $this->get_name() )->delete();
		}
	}

	/**
	 * Check if Avada and its necessary plugins are active.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return Helper::is_plugin_active( 'contact-form-7/wp-contact-form-7.php' );
	}
}
