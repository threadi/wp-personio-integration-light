<?php
/**
 * File to show a button to import positions.
 *
 * @package personio-integration-light
 */

namespace App\Plugin\Admin\SettingFields;

use App\Helper;

/**
 * Initialize the field.
 */
class ImportPositions {

	/**
	 * Get the output.
	 *
	 * @return void
	 */
	public static function get(): void {
		$import_is_running = absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) );
		if ( 0 === $import_is_running ) {
			?>
			<p><a href="<?php echo esc_url( Helper::get_import_url() ); ?>" class="button button-primary personio-integration-import-hint"><?php echo esc_html__( 'Run import', 'personio-integration-light' ); ?></a></p>
			<p><i><?php echo esc_html__( 'Hint', 'personio-integration-light' ); ?>:</i> <?php echo esc_html__( 'Performing the import could take a few minutes. If a timeout occurs, a manual import is not possible this way. Then the automatic import should be used.', 'personio-integration-light' ); ?></p>
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