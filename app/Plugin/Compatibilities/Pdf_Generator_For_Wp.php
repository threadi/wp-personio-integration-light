<?php
/**
 * File to handle the compatibility-check for PDF Generator for WP.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Compatibilities;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Compatibilities_Base;
use PersonioIntegrationLight\Plugin\Transients;

/**
 * Object for this check.
 */
class Pdf_Generator_For_Wp extends Compatibilities_Base {

	/**
	 * Name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_compatibility_pdf_generator_for_wp';

	/**
	 * Run the check.
	 *
	 * @return void
	 */
	public function check(): void {
		$transients_obj = Transients::get_instance();
		if ( $this->is_active() ) {
			// if post-type is set, to nothing more.
			$pdf_generator_advanced_settings = get_option( 'pgfw_advanced_save_settings' );
			if ( ! empty( $pdf_generator_advanced_settings ) && ! empty( $pdf_generator_advanced_settings['pgfw_advanced_show_post_type_icons'] ) && in_array( PersonioPosition::get_instance()->get_name(), $pdf_generator_advanced_settings['pgfw_advanced_show_post_type_icons'], true ) ) {
				$transients_obj->get_transient_by_name( $this->get_name() )->delete();
				return;
			}

			// create URL for advanced settings.
			$url = add_query_arg(
				array(
					'page'     => 'pdf_generator_for_wp_menu',
					'pgfw_tab' => 'pdf-generator-for-wp-advanced',
				),
				get_admin_url()
			);

			// add transient.
			$transient_obj = $transients_obj->add();
			$transient_obj->set_name( $this->get_name() );
			/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
			$transient_obj->set_message( sprintf( __( 'We realized that you are using PDF Generator for WP - very nice! If you want to print your open positions as PDF, go to the <a href="%s" target="_blank">advanced settings of PDF Generator for WP</a> and choose "personioposition" as allowed post type.', 'personio-integration-light' ), esc_url( $url ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->set_dismissible_days( 182 );
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
		return Helper::is_plugin_active( 'pdf-generator-for-wp/pdf-generator-for-wp.php' );
	}
}
