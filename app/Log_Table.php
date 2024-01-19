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

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
			'state' => __( 'state', 'personio-integration-light' ),
			'date'  => __( 'date', 'personio-integration-light' ),
			'log'   => __( 'log', 'personio-integration-light' ),
		);
	}

	/**
	 * Get the table data
	 *
	 * @return array
	 */
	private function table_data(): array {
		// check nonce.
		if ( isset( $_REQUEST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'personio-integration-table-log' ) ) {
			// redirect user back.
			wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
			exit;
		}

		global $wpdb;

		// order table.
		$order_by = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ), true ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'date';
		$order    = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], array( 'asc', 'desc' ), true ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'desc';

		// define vars for prepared statement.
		$vars = array(
			1,
			$order_by,
			$order,
		);

		$sql = '
            SELECT `state`, `time` AS `date`, `log`
            FROM `' . $wpdb->prefix . 'personio_import_logs`
            WHERE 1 = %1$d
            ORDER BY %2$s %3$s';

		// get results and return them.
		return $wpdb->get_results( $wpdb->prepare( $sql, $vars ), ARRAY_A );
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
