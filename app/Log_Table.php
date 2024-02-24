<?php
/**
 * File for handling table of logs in this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight;

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
}
