<?php
/**
 * File to handle table with extensions for our cpt.
 *
 * @package wp-personio-integration
 */

namespace PersonioIntegrationLight\PersonioIntegration\Tables;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Extensions_Base;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use WP_List_Table;

// if WP_List_Table is not loaded automatically, we need to load it.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Handler for applications-table in Backend.
 */
class Extensions extends WP_List_Table {
	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return array
	 */
	public function get_columns(): array {
		$columns = array(
			'state'        => __( 'Status', 'personio-integration-light' ),
			'name'       => __( 'Extension', 'personio-integration-light' ),
			'description' => __( 'Description', 'personio-integration-light' ),
		);

		/**
		 * Filter the possible columns for the extension table.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $columns List of columns.
		 */
		return apply_filters( 'personio_integration_extensions_table_columns', $columns );
	}

	/**
	 * Get the table data
	 *
	 * @return array
	 */
	private function table_data(): array {
		$extensions = array();
		foreach( \PersonioIntegrationLight\PersonioIntegration\Extensions::get_instance()->get_extensions() as $extension_name ) {
			if ( method_exists( $extension_name, 'get_instance' ) && is_callable( $extension_name . '::get_instance' ) ) {
				$extensions[] = call_user_func( $extension_name . '::get_instance' );
			}
		}
		return $extensions;
	}

	/**
	 * Get the log-table for the table-view.
	 *
	 * @return void
	 */
	public function prepare_items(): void {
		// use some hooks.
		add_filter( 'wp_kses_allowed_html', array( $this, 'add_kses_html' ) );

		// prepare table.
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
		return array();
	}

	/**
	 * Define what data to show on each column of the table.
	 *
	 * @param  object  $item       Object.
	 * @param  string $column_name Current column name.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ): string {
		// bail if $item is not our object.
		if( ! ( $item instanceof Extensions_Base ) ) {
			return '';
		}

		if( 'name' === $column_name ) {
			return $item->get_label();
		}

		if( 'description' === $column_name ) {
			return $item->get_description();
		}

		return '';
	}

	/**
	 * Show actions on name-row.
	 *
	 * @param Extensions_Base $item List of properties.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function _column_state( Extensions_Base $item ): string {
		// create URL to change the extension state.
		$url = add_query_arg(
			array(
				'action' => 'personio_integration_extension_state',
				'extension' => $item->get_name(),
				'nonce' => wp_create_nonce( 'personio-integration-extension-state' )
			),
			get_admin_url().'admin.php'
		);

		// create dialog.


		// create output depending on state.
		$html = '<a href="#" class="button button-state button-state-disabled" data-extension="' . esc_attr( $item->get_name() ) . '">' . esc_html__( 'Disabled', 'personio-integration-light' ) . '</a>';
		if( $item->is_enabled() ) {
			$html = '<a href="#" class="button button-state button-state-enabled" data-extension="' . esc_attr( $item->get_name() ) . '">' . __( 'Enabled', 'personio-integration-light' ) . '</a>';
		}

		// output.
		return '<td>'. wp_kses_post( $html ) .'</td>';
	}

	/**
	 * Allow input-field in kses for on/off-toggle.
	 *
	 * @param array  $allowed_tags The allowed tags.
	 *
	 * @return array
	 */
	public function add_kses_html( array $allowed_tags ): array {
		$allowed_tags['input'] = array(
			'type' => true,
			'class'  => true,
			'id'     => true,
		);
		return $allowed_tags;
	}
}
