<?php
/**
 * File to handle the compatibility-check for Brizy.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Compatibilities;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easyTransientsForWordPress\Transients;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Compatibilities_Base;

/**
 * Object for this check.
 */
class Brizy extends Compatibilities_Base {

	/**
	 * Name of this event.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_compatibility_brizy';

	/**
	 * Instance of this object.
	 *
	 * @var ?Brizy
	 */
	private static ?Brizy $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Brizy {
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
			$transient_obj->set_message( sprintf( __( '<strong>We realized that you are using Brizy - very nice!</strong> <a href="%1$s" target="_blank"><i>Personio Integration Pro</i> (opens a new window)</a> allows you to design the output of positions in Brizy.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
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
		return defined( 'BRIZY_VERSION' );
	}
}
