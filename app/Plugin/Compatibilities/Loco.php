<?php
/**
 * File to handle the compatibility-check for Loco Translate.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Compatibilities;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Compatibilities_Base;

/**
 * Object for this check.
 */
class Loco extends Compatibilities_Base {

	/**
	 * Name of this object.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_compatibility_loco';

	/**
	 * URL of this plugin.
	 *
	 * @var string
	 */
	protected string $plugin_url = 'https://wordpress.org/plugins/loco-translate/';

	/**
	 * Instance of this object.
	 *
	 * @var ?Loco
	 */
	private static ?Loco $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Loco {
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
		// bail if not active.
		if( ! $this->is_active() ) {
			return;
		}

		// use our own hook.
		add_filter( 'personio_integration_light_term_translate_hint', array( $this, 'add_hint' ) );
	}

	/**
	 * Check if the plugin is active.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return Helper::is_plugin_active( 'loco-translate/loco.php' );
	}

	/**
	 * Add hint to use Loco Translate if it is enabled.
	 *
	 * @param array $dialog
	 *
	 * @return array
	 */
	public function add_hint( array $dialog ): array {
		$url = add_query_arg(
			array(
				'bundle' => trailingslashit( basename( Helper::get_plugin_path() ) ) . 'personio-integration-light.php',
				'page'   => 'loco-plugin',
				'action' => 'view',
			),
			get_admin_url() . 'admin.php'
		);

		/* translators: %1$s will be replaced by the URL for Loco Settings of this plugin. */
		$dialog['texts'][1] = '<p>' . sprintf( __( 'You already have Loco Translate installed. Follow <a href="%1$s">this link</a> to edit the texts there.', 'personio-integration-light' ), esc_url( $url ) ) . '</p>';

		// return the resulting dialog.
		return $dialog;
	}
}
