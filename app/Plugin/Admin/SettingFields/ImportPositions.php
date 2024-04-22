<?php
/**
 * File to show a button to import positions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingFields;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;

/**
 * Initialize the field.
 */
class ImportPositions {

	/**
	 * Get the output.

	 * @return void
	 */
	public static function get(): void {
		$import_is_running = absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) );
		if ( 0 === $import_is_running ) {
			?>
			<p><a href="<?php echo esc_url( Helper::get_import_url() ); ?>" class="button button-primary personio-integration-import-hint"><?php echo esc_html__( 'Run import of positions now', 'personio-integration-light' ); ?></a></p>
			<p><i><?php echo esc_html__( 'Hint', 'personio-integration-light' ); ?>:</i> <?php echo esc_html__( 'This will import positions from your Personio account in your Wordpress-website.', 'personio-integration-light' ); ?></p>
			<?php
		} else {
			?>
			<p><?php echo esc_html__( 'The import is already running. Please wait some moments.', 'personio-integration-light' ); ?></p>
			<?php
			// show import-break button if import is running min. 1 hour.
			if ( 1 < $import_is_running ) {
				if ( $import_is_running + 60 * 60 < time() ) {
					$url = add_query_arg(
						array(
							'action' => 'personioPositionsCancelImport',
							'nonce'  => wp_create_nonce( 'wp-personio-integration-cancel-import' ),
						),
						get_admin_url() . 'admin.php'
					);
					?>
					<p><a href="<?php echo esc_url( $url ); ?>" class="button button-primary"><?php echo esc_html__( 'Cancel running import', 'personio-integration-light' ); ?></a></p>
					<?php
				}
			}
		}
	}
}
