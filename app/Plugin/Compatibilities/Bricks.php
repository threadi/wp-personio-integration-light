<?php
/**
 * File to handle the compatibility-check for Bricks.
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
class Bricks extends Compatibilities_Base {

	/**
	 * Name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_compatibility_bricks';

	/**
	 * Instance of this object.
	 *
	 * @var ?Bricks
	 */
	private static ?Bricks $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Bricks {
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
			$transient_obj->set_message( sprintf( __( '<strong>We realized that you are using Bricks - very nice!</strong> <a href="%1$s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Bricks.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->set_dismissible_days( 30 );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( $this->get_name() )->delete();
		}
	}

	/**
	 * Check if Bricks and its necessary plugins are active.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		// check for the theme.
		$is_bricks = false;
		$theme    = wp_get_theme();
		if ( 'Bricks' === $theme->get( 'Name' ) ) {
			$is_bricks = true;
		}
		if ( $theme->parent() && 'Bricks' === $theme->parent()->get( 'Name' ) ) {
			$is_bricks = true;
		}

		// return resulting value.
		return $is_bricks;
	}
}
