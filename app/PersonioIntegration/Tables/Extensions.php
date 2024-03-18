<?php
/**
 * File to handle table with extensions for our cpt.
 *
 * @package wp-personio-integration
 */

namespace PersonioIntegrationLight\PersonioIntegration\Tables;

defined( 'ABSPATH' ) or exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Extensions_Base;
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
			'category' => __( 'Category', 'personio-integration-light' ),
			'name'       => __( 'Extension', 'personio-integration-light' ),
			'state'        => __( 'Status', 'personio-integration-light' ),
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
		// get filter.
		$category = isset( $_GET['category'] ) ? sanitize_text_field( wp_unslash( $_GET['category'] ) ) : '';

		$extensions = array();
		foreach( \PersonioIntegrationLight\PersonioIntegration\Extensions::get_instance()->get_extensions() as $extension_name ) {
			if ( is_string( $extension_name ) && method_exists( $extension_name, 'get_instance' ) && is_callable( $extension_name . '::get_instance' ) ) {
				$obj = call_user_func( $extension_name . '::get_instance' );
				if( $obj instanceof Extensions_Base && $this->filter_object( $obj, $category ) ) {
					$extensions[] = $obj;
				}
			}
			elseif( $extension_name instanceof Extensions_Base && $this->filter_object( $extension_name, $category ) ) {
				$extensions[] = $extension_name;
			}
		}

		// sort the data.
		usort( $extensions, array( $this, 'sort_extension_objects' ) );

		/**
		 * Filter the list of extensions in this table.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $extensions List of sorted extensions.
		 */
		return apply_filters( 'personio_integration_extensions_table_extensions',  $extensions );
	}

	/**
	 * Sort the list of extension by its categories.
	 *
	 * @param Extensions_Base $a Object to compare.
	 * @param Extensions_Base $b Object to compare with.
	 *
	 * @return int
	 */
	public function sort_extension_objects( Extensions_Base $a, Extensions_Base $b ): int {
		return strcmp( $a->get_category(), $b->get_category() );
	}

	/**
	 * Filter for given category-string.
	 *
	 * @param Extensions_Base $obj
	 * @param string          $category
	 *
	 * @return bool
	 */
	private function filter_object( Extensions_Base $obj, string $category ): bool {
		// bail if category is not given.
		if( empty( $category ) ) {
			return true;
		}

		// filter for the given string,
		return $obj->get_category() === $category;
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

		// show category.
		if( 'category' === $column_name ) {
			return $this->get_extension_categories()[$item->get_category()];
		}

		// show description.
		if( 'description' === $column_name ) {
			return $item->get_description();
		}

		return '';
	}

	/**
	 * Get the available categories.
	 *
	 * @return array
	 */
	private function get_extension_categories(): array {
		$categories = array(
			'general' => __( 'General', 'personio-integration-light' ),
			'forms' => __( 'Application forms', 'personio-integration-light' ),
			'positions' => __( 'Positions', 'personio-integration-light' ),
			'multilingual' => __( 'Multilingual', 'wp-personio-integration' ),
			'seo' => __( 'SEO', 'personio-integration-light' ),
			'tracking' => __( 'Tracking', 'personio-integration-light' ),
		);

		/**
		 * Filter the extension categories.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 * @param array $categories List of categories.
		 */
		return apply_filters( 'personio_integration_extension_categories', $categories );
	}

	/**
	 * Show actions on name-row.
	 *
	 * @param Extensions_Base $item List of properties.
	 *
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function _column_name( Extensions_Base $item ): string {
		// define actions.
		$actions = array();
		if( ! empty( $item->get_setting_tab() ) ) {
			$actions['settings'] = '<a href="' . esc_url( Helper::get_settings_url( $item->get_settings_page(), $item->get_setting_tab() ) ) . '">' . esc_html__( 'Settings', 'personio-integration-light' ) . '</a>';
		}

		// change actions to pro-link only.
		if( $item->is_pro() ) {
			$actions = array(
				'pro' => '<a href="'.esc_url( Helper::get_pro_url() ).'" target="_blank">'.esc_html__( 'Get Pro', 'personio-integration-light' ).'</a>'
			);
		}

		// output.
		return '<td><strong>'. wp_kses_post( $item->get_label() ) . '</strong><div class="row-actions-wrapper" style="display: ' . ( $item->is_enabled() ? 'block' : 'none' ) . '">' . wp_kses_post( $this->row_actions( $actions ) ) .'</div></td>';
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
		// show simple pro-hint if this is a pro-extension.
		if( $item->is_pro() ) {
			$html = '<a class="pro-marker" href="'.esc_url( Helper::get_pro_url() ).'" target="_blank">'.__( 'Only in Pro', 'personio-integration-light' ).' <span class="dashicons dashicons-external"></span></a>';

			return '<td>'. wp_kses_post( $html ) .'</td>';
		}

		// create output depending on state.
		$html = '<a href="#" class="button button-state button-state-disabled" data-extension="' . esc_attr( $item->get_name() ) . '">' . esc_html__( 'Disabled', 'personio-integration-light' ) . '</a>';
		if( $item->is_enabled() ) {
			$html = '<a href="#" class="button button-state button-state-enabled" data-extension="' . esc_attr( $item->get_name() ) . '">' . __( 'Enabled', 'personio-integration-light' ) . '</a>';
		}
		if( $item->has_custom_state() ) {
			$html = $item->get_custom_state(); //'<span class="button button-state button-disabled">' . __( 'Not available', 'personio-integration-light' ) . '</a>';
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

	/**
	 * Define filter for categories.
	 *
	 * @return array
	 */
	protected function get_views(): array {
		// get main url.
		$url = remove_query_arg( 'category' );

		// define initial list.
		$list = array(
			'all' => '<a href="' . esc_url( $url ) . '">' . __( 'All', 'wp-personio-integration' ) . '</a>',
		);

		// add all categories to the list.
		foreach ( $this->get_extension_categories() as $name => $label ) {
			$url               = add_query_arg( array( 'category' => $name ) );
			$list[ $name ] = '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
		}

		// return resulting list.
		return $list;
	}

	/**
	 * Define options for view-output.
	 *
	 * @param string $which Where to display.
	 *
	 * @return void
	 */
	protected function extra_tablenav( $which ): void {
		if( 'top' === $which ) {
			// URL to disable all extensions.
			$disable_url = add_query_arg(
				array(
					'action' => 'personio_integration_extension_disable_all',
					'nonce' => wp_create_nonce( 'personio-integration-extension-disable-all' )
				),
				get_admin_url() . 'admin.php'
			);

			// define dialog for export.
			$dialog_disable = array(
				'title'   => __( 'Disable all extensions', 'wp-personio-integration' ),
				'texts'   => array(
					'<p><strong>' . __( 'Do you really want to disable all extensions?', 'wp-personio-integration' ) . '</strong></p>',
					'<p>' . __( 'You might not be able to you all possibilities for your positions.', 'wp-personio-integration' ) . '</p>',
					'<p>' . __( 'Settings of extension will not change through its deactivation.', 'wp-personio-integration' ) . '</p>',
				),
				'buttons' => array(
					array(
						'action'  => 'location.href="' . esc_url($disable_url) . '";',
						'variant' => 'primary',
						'text'    => __( 'Yes, disable all', 'wp-personio-integration' ),
					),
					array(
						'action'  => 'closeDialog();',
						'variant' => 'secondary',
						'text'    => __( 'Cancel', 'wp-personio-integration' ),
					),
				),
			);

			// URL to disable all extensions.
			$enable_url = add_query_arg(
				array(
					'action' => 'personio_integration_extension_enable_all',
					'nonce' => wp_create_nonce( 'personio-integration-extension-enable-all' )
				),
				get_admin_url() . 'admin.php'
			);

			// define dialog for export.
			$dialog_enable = array(
				'title'   => __( 'Enable all extensions', 'wp-personio-integration' ),
				'texts'   => array(
					'<p><strong>' . __( 'Do you really want to enable all extensions?', 'wp-personio-integration' ) . '</strong></p>',
					'<p>' . __( 'Extensions that depend on third-party plugins are not activated automatically.', 'wp-personio-integration' ) . '</p>',
				),
				'buttons' => array(
					array(
						'action'  => 'location.href="' . esc_url($enable_url) . '";',
						'variant' => 'primary',
						'text'    => __( 'Yes, enable them', 'wp-personio-integration' ),
					),
					array(
						'action'  => 'closeDialog();',
						'variant' => 'secondary',
						'text'    => __( 'Cancel', 'wp-personio-integration' ),
					),
				),
			);

			// output buttons.
			echo '<a data-dialog="' . esc_attr( wp_json_encode( $dialog_disable ) ) . '" class="page-title-action wp-easy-dialog" href="' . esc_url( $disable_url ) . '">' . esc_html__( 'Disable all', 'wp-personio-integration' ) . '</a>';
			echo '<a data-dialog="' . esc_attr( wp_json_encode( $dialog_enable ) ) . '" class="page-title-action wp-easy-dialog" href="' . esc_url( $enable_url ) . '">' . esc_html__( 'Enable all', 'wp-personio-integration' ) . '</a>';
		}
	}
}
