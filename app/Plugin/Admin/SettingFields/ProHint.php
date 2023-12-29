<?php
/**
 * File to handle a single text field for classic settings.
 *
 * @package personio-integration-light
 */

namespace App\Plugin\Admin\SettingFields;

/**
 * Initialize the field.
 */
class ProHint {

	/**
	 * Get the output.
	 *
	 * @return void
	 */
	public static function get(): void {
		// pro hint.
		/* translators: %1$s is replaced with "string" */
		do_action( 'personio_integration_admin_show_pro_hint', __( 'With %s you get more advanced options, e.g. to change the URL of archives with positions.', 'personio-integration-light' ) );
	}
}
