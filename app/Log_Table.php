<?php
/**
 * File for handling table of logs in this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use WP_List_Table;

/**
 * Handler for log-output in backend.
 */
class Log_Table extends WP_List_Table {
	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return array
	 */
	public function get_columns(): array {
		return array(
			'state' => __( 'State', 'personio-integration-light' ),
			'date'  => __( 'Date', 'personio-integration-light' ),
			'log'   => __( 'Log', 'personio-integration-light' ),
		);
	}

	/**
	 * Get the table data
	 *
	 * @return array
	 */
	private function table_data(): array {
		global $wpdb;

		// order table.
		$order_by = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( is_null( $order_by ) ) {
			$order_by = 'date';
		}
		$order = filter_input( INPUT_GET, 'order', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! is_null( $order ) ) {
			$order = sanitize_sql_orderby( $order );
		} else {
			$order = 'ASC';
		}

		// get results and return them.
		if ( 'asc' === $order ) {
			return $wpdb->get_results(
				$wpdb->prepare(
					'SELECT `state`, `time` AS `date`, `log`
            			FROM `' . $wpdb->prefix . 'personio_import_logs`
                        WHERE 1 = %d
                        ORDER BY ' . esc_sql( $order_by ) . ' ASC',
					array( 1 )
				),
				ARRAY_A
			);
		}
		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT `state`, `time` AS `date`, `log`
            			FROM `' . $wpdb->prefix . 'personio_import_logs`
                        WHERE 1 = %d
                        ORDER BY ' . esc_sql( $order_by ) . ' DESC',
				array( 1 )
			),
			ARRAY_A
		);
	}

	/**
	 * Get the log-table for the table-view.
	 *
	 * @return void
	 */
	public function prepare_items(): void {
		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $this->table_data();
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return array
	 */
	public function get_hidden_columns(): array {
		return array();
	}

	/**
	 * Define the sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns(): array {
		return array( 'date' => array( 'date', false ) );
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  array  $item        Data.
	 * @param  String $column_name - Current column name.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ): string {
		return match ( $column_name ) {
			'date' => Helper::get_format_date_time( $item[ $column_name ] ),
			'state' => $item[ $column_name ],
			'log' => nl2br( $item[ $column_name ] ),
			default => '',
		};
	}

	/**
	 * Add export-buttons on top of table.
	 *
	 * @param string $which The position.
	 * @return void
	 */
	public function extra_tablenav( $which ): void {
		if ( 'top' === $which ) {
			// define export-URL.
			$download_url = add_query_arg(
				array(
					'action' => 'personio_integration_log_export',
					'nonce'  => wp_create_nonce( 'personio-integration-log-export' ),
				),
				get_admin_url() . 'admin.php'
			);

			// create download-dialog.
			$download_dialog = array(
				'title'   => __( 'Export log entries', 'personio-integration-light' ),
				'texts'   => array(
					'<p>' . __( 'Click on the button below to download the log entries as CSV.', 'personio-integration-light' ) . '</p>',
					'<p>' . __( 'The file will contain ALL entries. Be aware of this before you send this file to someone.', 'personio-integration-light' ) . '</p>',
				),
				'buttons' => array(
					array(
						'action'  => 'location.href="' . esc_url( $download_url ) . '";closeDialog();',
						'variant' => 'primary',
						'text'    => __( 'Export log entries', 'personio-integration-light' ),
					),
					array(
						'action'  => 'closeDialog();',
						'variant' => 'secondary',
						'text'    => __( 'Cancel', 'personio-integration-light' ),
					),
				),
			);

			// define empty-URL.
			$empty_url = add_query_arg(
				array(
					'action' => 'personio_integration_log_empty',
					'nonce'  => wp_create_nonce( 'personio-integration-log-empty' ),
				),
				get_admin_url() . 'admin.php'
			);

			// create download-dialog.
			$empty_dialog = array(
				'title'   => __( 'Empty log entries', 'personio-integration-light' ),
				'texts'   => array(
					'<p><strong>' . __( 'Are you sure you want to empty the log?', 'personio-integration-light' ) . '</strong></p>',
					'<p>' . __( 'You will lost any log until now.', 'personio-integration-light' ) . '</p>',
				),
				'buttons' => array(
					array(
						'action'  => 'location.href="' . esc_url( $empty_url ) . '";',
						'variant' => 'primary',
						'text'    => __( 'Yes, empty the log', 'personio-integration-light' ),
					),
					array(
						'action'  => 'closeDialog();',
						'variant' => 'secondary',
						'text'    => __( 'Cancel', 'personio-integration-light' ),
					),
				),
			);

			?>
			<a href="<?php echo esc_url( $download_url ); ?>" class="button button-secondary wp-easy-dialog<?php echo ( 0 === count( $this->items ) ? ' disabled' : '' ); ?>" data-dialog="<?php echo esc_attr( wp_json_encode( $download_dialog ) ); ?>"><?php echo esc_html__( 'Export as CSV', 'personio-integration-light' ); ?></a>
			<a href="<?php echo esc_url( $empty_url ); ?>" class="button button-secondary wp-easy-dialog<?php echo ( 0 === count( $this->items ) ? ' disabled' : '' ); ?>" data-dialog="<?php echo esc_attr( wp_json_encode( $empty_dialog ) ); ?>"><?php echo esc_html__( 'Empty the log', 'personio-integration-light' ); ?></a>
			<?php
		}
	}

	/**
	 * Message to be displayed when there are no items.
	 *
	 * @since 3.1.0
	 */
	public function no_items(): void {
		echo esc_html__( 'No log entries found.', 'personio-integration-light' );
	}
}
