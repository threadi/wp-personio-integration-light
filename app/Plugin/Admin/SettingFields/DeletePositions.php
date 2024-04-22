<?php
/**
 * File to show a button to delete positions.
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
class DeletePositions {

	/**
	 * Get the output.
	 *
	 * @return void
	 */
	public static function get(): void {
		if ( Helper::is_personio_url_set() && get_option( 'personioIntegrationPositionCount', 0 ) > 0 ) {
			?>
			<p><a href="<?php echo esc_url( Helper::get_delete_url() ); ?>" class="button button-primary personio-integration-delete-all"><?php echo esc_html__( 'Delete all positions', 'personio-integration-light' ); ?></a></p>
			<p><i><?php echo esc_html__( 'Hint', 'personio-integration-light' ); ?>:</i> <?php echo esc_html__( 'Removes all actual existing positions from your WordPress-website.', 'personio-integration-light' ); ?></p>
			<?php
		} else {
			?>
			<p><?php echo esc_html__( 'There are currently no imported positions.', 'personio-integration-light' ); ?></p>
			<?php
		}
	}
}
