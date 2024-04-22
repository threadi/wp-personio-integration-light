<?php
/**
 * File to handle the compatibility-check for Themify.
 *
 * @package personio-intregation-light
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
class Themify extends Compatibilities_Base {

	/**
	 * Name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_compatibility_themify';

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
			/* translators: %1$s will be replaced by the URL to the shortcode-info-page. */
			$transient_obj->set_message( sprintf( __( 'We realized that you are using Themify - very nice! Actually we do not support this page builder. You can use Shortcodes <a href="%1$s">as described here</a>.', 'personio-integration-light' ), esc_url( Helper::get_shortcode_documentation_url() ) ) );
			$transient_obj->set_type( 'success' );
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
		return Helper::is_plugin_active( 'themify-builder/themify-builder.php' );
	}
}
