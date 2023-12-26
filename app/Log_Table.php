<?php
/**
 * File for handling table of logs in this plugin.
 *
 * TODO mit easy-language abgleichen wg. Sortierung.
 *
 * @package personio-integration-light
 */

namespace App;

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
		global $wpdb;
		$sql = '
            SELECT `state`, `time` AS `date`, `log`
            FROM `' . $wpdb->prefix . 'personio_import_logs`
            ORDER BY `time` DESC';
		return $wpdb->get_results( $sql, ARRAY_A );
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
