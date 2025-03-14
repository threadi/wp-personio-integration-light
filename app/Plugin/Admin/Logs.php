<?php
/**
 * File to handle output of log table in admin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Log_Table;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;

/**
 * Object to handle the output.
 */
class Logs {

	/**
	 * Output the table.
	 *
	 * @return void
	 */
	public static function show(): void {
		// bail if user has not the capability for this.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// get page from request.
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		// if WP_List_Table is not loaded automatically, we need to load it.
		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		}

		// get the table object.
		$log = new Log_Table();
		$log->prepare_items();

		// output.
		?>
		<div class="wrap">
			<h2><?php echo esc_html__( 'Logs', 'personio-integration-light' ); ?></h2>
			<form action="<?php echo esc_url( get_admin_url() . 'edit.php' ); ?>" method="get">
				<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>">
				<input type="hidden" name="tab" value="logs">
				<input type="hidden" name="post_type" value="<?php echo esc_attr( PersonioPosition::get_instance()->get_name() ); ?>">
				<?php
				$log->views();
				$log->display();
				?>
			</form>
		</div>
		<?php
	}
}
