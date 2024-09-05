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
			'state'    => __( 'State', 'personio-integration-light' ),
			'date'     => __( 'Date', 'personio-integration-light' ),
			'log'      => __( 'Log', 'personio-integration-light' ),
			'category' => __( 'Category', 'personio-integration-light' ),
		);
	}

	/**
	 * Get the table data
	 *
	 * @return array
	 */
	private function table_data(): array {
		$log = new Log();
		return $log->get_entries();
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

		$data = $this->table_data();

		$per_page     = 100;
		$current_page = $this->get_pagenum();
		$total_items  = count( $data );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $data;
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
			'state' => $this->get_status_icon( $item[ $column_name ] ),
			'log' => nl2br( $item[ $column_name ] ),
			'category' => empty( $item[ $column_name ] ) ? '<i>' . esc_html__( 'not defined', 'personio-integration-light' ) . '</i>' : $this->get_category( $item[ $column_name ] ),
			default => '',
		};
	}

	/**
	 * Get a single category.
	 *
	 * @param string $category The searched category.
	 *
	 * @return string
	 */
	private function get_category( string $category ): string {
		// get list of categories.
		$log_obj    = new Log();
		$categories = $log_obj->get_categories();

		// bail if search category is not found.
		if ( empty( $categories[ $category ] ) ) {
			return '<i>' . esc_html__( 'Unknown', 'personio-integration-light' ) . '</i>';
		}

		// return the category-label.
		return $categories[ $category ];
	}

	/**
	 * Add export- and delete-buttons on top of table.
	 *
	 * @param string $which The position.
	 * @return void
	 */
	public function extra_tablenav( $which ): void {
		if ( 'top' === $which ) {
			// define hint text.
			$contains = '<p>' . __( 'The file will contain ALL entries. Be aware of this before you send this file to someone.', 'personio-integration-light' ) . '</p>';

			// define export-URL.
			$download_url = add_query_arg(
				array(
					'action' => 'personio_integration_log_export',
					'nonce'  => wp_create_nonce( 'personio-integration-log-export' ),
				),
				get_admin_url() . 'admin.php'
			);

			// get filter.
			$category = $this->get_category_filter();
			if ( ! empty( $category ) ) {
				$download_url = add_query_arg( array( 'category' => $category ), $download_url );
				$contains     = '<p>' . __( 'The file will contain ALL entries of the chosen filter. Be aware of this before you send this file to someone.', 'personio-integration-light' ) . '</p>';
			}

			// get md5.
			$md5 = $this->get_md5_filter();
			if ( ! empty( $md5 ) ) {
				$download_url = add_query_arg( array( 'md5' => $md5 ), $download_url );
				$contains     = '<p>' . __( 'The file will contain ALL entries of the chosen filter. Be aware of this before you send this file to someone.', 'personio-integration-light' ) . '</p>';
			}

			// create download-dialog.
			$download_dialog = array(
				'title'   => __( 'Export log entries', 'personio-integration-light' ),
				'texts'   => array(
					'<p>' . __( 'Click on the button below to download the log entries as CSV.', 'personio-integration-light' ) . '</p>',
					$contains,
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
		// get actual filter.
		$category = $this->get_category_filter();

		// if filter is set show other text.
		if ( ! empty( $category ) ) {
			// get all categories to get the title.
			$log_obj    = new Log();
			$categories = $log_obj->get_categories();

			// show text.
			/* translators: %1$s will be replaced by the category name. */
			printf( esc_html__( 'No log entries for %1$s found.', 'personio-integration-light' ), esc_html( $categories[ $category ] ) );
			return;
		}

		// show default text.
		echo esc_html__( 'No log entries found.', 'personio-integration-light' );
	}

	/**
	 * Define filter for categories.
	 *
	 * @return array
	 */
	protected function get_views(): array {
		// get main url without filter.
		$url = remove_query_arg( array( 'category', 'md5' ) );

		// get actual filter.
		$category = $this->get_category_filter();

		// define initial list.
		$list = array(
			'all' => '<a href="' . esc_url( $url ) . '"' . ( empty( $category ) ? ' class="current"' : '' ) . '>' . esc_html__( 'All', 'personio-integration-light' ) . '</a>',
		);

		// get all log categories.
		$log_obj = new Log();
		foreach ( $log_obj->get_categories() as $key => $label ) {
			$url          = add_query_arg( array( 'category' => $key ) );
			$list[ $key ] = '<a href="' . esc_url( $url ) . '"' . ( $category === $key ? ' class="current"' : '' ) . '>' . esc_html( $label ) . '</a>';
		}

		/**
		 * Filter the list before output.
		 *
		 * @since 3.1.0 Available since 3.1.0.
		 * @param array $list List of filter.
		 */
		return apply_filters( 'personio_integration_log_table_filter', $list );
	}

	/**
	 * Get actual category-filter-value.
	 *
	 * @return string
	 */
	private function get_category_filter(): string {
		$category = filter_input( INPUT_GET, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( is_null( $category ) ) {
			return '';
		}
		return $category;
	}

	/**
	 * Get actual category-filter-value.
	 *
	 * @return string
	 */
	private function get_md5_filter(): string {
		$md5 = filter_input( INPUT_GET, 'md5', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( is_null( $md5 ) ) {
			return '';
		}
		return $md5;
	}

	/**
	 * Return HTML-code for icon of the given status.
	 *
	 * @param string $status The requested status.
	 *
	 * @return string
	 */
	private function get_status_icon( string $status ): string {
		$list = array(
			'success' => '<span class="dashicons dashicons-yes"></span>',
			'error'   => '<span class="dashicons dashicons-no"></span>',
		);

		// bail if status is unknown.
		if ( empty( $list[ $status ] ) ) {
			return '';
		}

		// return the HTML-code for the icon of this status.
		return $list[ $status ];
	}
}
