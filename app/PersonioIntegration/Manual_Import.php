<?php
/**
 * File to handle the manual import of positions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Field_Base;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Setting;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Admin\Admin;

/**
 * Object to handle availability-checks for positions.
 */
class Manual_Import extends Extensions_Base {
	/**
	 * List of position objects to import.
	 *
	 * @var array<int,Position>
	 */
	private array $positions = array();

	/**
	 * List of Personio IDs of positions to import.
	 *
	 * @var array<int,string>
	 */
	private array $simple_positions = array();

	/**
	 * The internal name of this extension.
	 *
	 * @var string
	 */
	protected string $name = 'manual_import';

	/**
	 * Name of the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = 'personioIntegrationEnableManualImportCheckStatus';

	/**
	 * Name if the setting tab where the setting field is visible.
	 *
	 * @var string
	 */
	protected string $setting_tab = '';

	/**
	 * Internal name of the used category.
	 *
	 * @var string
	 */
	protected string $extension_category = 'positions';

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Manual_Import
	 */
	private static ?Manual_Import $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Manual_Import {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter( 'personio_integration_light_extension_state_changed_dialog', array( $this, 'add_hint_after_enabling' ), 10, 2 );

		// bail if extension is not enabled.
		if ( ! defined( 'PERSONIO_INTEGRATION_ACTIVATION_RUNNING' ) && ! defined( 'PERSONIO_INTEGRATION_UPDATE_RUNNING' ) && ! defined( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING' ) && ! $this->is_enabled() ) {
			return;
		}

		// use hooks.
		add_action( 'init', array( $this, 'add_settings' ), 30 );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_styles_and_js' ), PHP_INT_MAX );
		add_action( 'wp_ajax_personio_integration_get_manual_import_dialog', array( $this, 'get_dialog' ) );
		add_action( 'wp_ajax_personio_integration_run_manual_import', array( $this, 'download_positions' ) );
		add_action( 'wp_ajax_personio_integration_save_manual_import', array( $this, 'import_selected_positions' ) );

		// use our own hooks.
		add_filter( 'personio_integration_import_dialog', array( $this, 'add_dialog_button' ) );
	}

	/**
	 * Return label of this extension.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Manual Import', 'personio-integration-light' );
	}

	/**
	 * Return whether this extension is enabled (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return 1 === absint( get_option( $this->get_settings_field_name() ) );
	}

	/**
	 * Return the description for this extension.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return __( 'Adds option to manual import positions from Personio. You can choose which positions to import and which not to import.', 'personio-integration-light' );
	}

	/**
	 * Extend the dialog after enabling this extension with hints to usage.
	 *
	 * @param array<string,mixed> $dialog The dialog.
	 * @param Extensions_Base     $extension The changed extension.
	 *
	 * @return array<string,mixed>
	 */
	public function add_hint_after_enabling( array $dialog, Extensions_Base $extension ): array {
		// bail if this is not this extension.
		if ( $this->get_name() !== $extension->get_name() ) {
			return $dialog;
		}

		// bail if status is disabled.
		if ( ! $extension->is_enabled() ) {
			return $dialog;
		}

		// add hint.
		/* translators: %1$s will be replaced by a URL. */
		$dialog['texts'][] = '<p>' . sprintf( __( 'Go to <a href="%1$s">import settings</a> to run the manual import.', 'personio-integration-light' ), Helper::get_settings_url( 'personioPositions', 'import' ) ) . '</p>';

		// return resulting dialog.
		return $dialog;
	}

	/**
	 * Extend the setting for imports.
	 *
	 * @return void
	 */
	public function add_settings(): void {
		// get settings object.
		$settings_obj = Settings::get_instance();

		// get setting for import now.
		$import_now_setting = $settings_obj->get_setting( 'personioIntegrationImportNow' );

		// bail if setting could not be found.
		if ( ! $import_now_setting instanceof Setting ) {
			return;
		}

		// get its field.
		$import_now_setting_field = $import_now_setting->get_field();

		// bail if field could not be loaded.
		if ( ! $import_now_setting_field instanceof Field_Base ) {
			return;
		}

		// add description.
		$import_now_setting_field->set_description( __( 'Click here to start the manual import.', 'personio-integration-light' ) );
		$import_now_setting->set_field( $import_now_setting_field );
	}

	/**
	 * Add the button to start the manual import in dialog.
	 *
	 * @param array<string,mixed> $dialog The dialog.
	 *
	 * @return array<string,mixed>
	 */
	public function add_dialog_button( array $dialog ): array {
		$dialog['detail']['buttons'][] = array(
			'action'  => 'personio_integration_show_manual_import_dialog();',
			'variant' => 'primary',
			'text'    => __( 'Manual Import', 'personio-integration-light' ),
		);
		return $dialog;
	}

	/**
	 * Add own CSS and JS for backend.
	 *
	 * @return void
	 */
	public function add_styles_and_js(): void {
		// backend-JS.
		wp_enqueue_script(
			'personio-integration-admin-manual-import',
			Helper::get_plugin_url() . 'admin/manual_import.js',
			array( 'jquery', 'easy-dialog-for-wordpress' ),
			Helper::get_file_version( Helper::get_plugin_path() . 'admin/manual_import.js' ),
			true
		);

		// add php-vars to our js-script.
		wp_localize_script(
			'personio-integration-admin-manual-import',
			'personioIntegrationLightManualImportJsVars',
			array(
				'ajax_url'                            => admin_url( 'admin-ajax.php' ),
				'manual_import_dialog_nonce'          => wp_create_nonce( 'personio-integration-light-manual-import-dialog' ),
				'run_manual_import_nonce'             => wp_create_nonce( 'personio-integration-light-manual-import' ),
				'save_manual_import_nonce'            => wp_create_nonce( 'personio-integration-light-manual-import-save' ),
				'title_manual_import_progress'        => __( 'Download positions without importing them...', 'personio-integration-light' ),
				'title_manual_import_saving_progress' => __( 'Import is running', 'personio-integration-light' ),
			)
		);
	}

	/**
	 * Return dialog.
	 *
	 * @return void
	 */
	public function get_dialog(): void {
		// check nonce.
		check_ajax_referer( 'personio-integration-light-manual-import-dialog', 'nonce' );

		$show_hint = 1 === absint( get_option( 'personioIntegrationEnablePositionSchedule' ) );
		/**
		 * Prevent manual import if automatic import is enabled.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param bool $show_hint Is true to prevent the manual import, false to run it.
		 */
		if ( apply_filters( 'personio_integration_light_prevent_manual_import', $show_hint ) ) {
			// define dialog.
			$dialog = array(
				'detail' => array(
					'title'   => __( 'Hint', 'personio-integration-light' ),
					'texts'   => array(
						'<p>' . __( '<strong>Automatic import of positions is active.</strong> This would result in the positions you deselected being imported again at any time.', 'personio-integration-light' ) . '</p>',
						/* translators: %1$s will be replaced by a URL. */
						'<p>' . Admin::get_instance()->get_pro_hint( __( 'With %1$s you can use automatic and manual import at the same time. You can choose which items should be imported and which should not.', 'personio-integration-light' ) ) . '</p>',
					),
					'buttons' => array(
						array(
							'action'  => 'closeDialog();',
							'variant' => 'primary',
							'text'    => __( 'OK', 'personio-integration-light' ),
						),
					),
				),
			);

			// send response as JSON.
			wp_send_json( $dialog );
		}

		// define dialog.
		$dialog = array(
			'detail' => array(
				'title'   => __( 'Manual import', 'personio-integration-light' ),
				'texts'   => array(
					'<p><strong>' . __( 'Do you really want to import open positions manually?', 'personio-integration-light' ) . '</strong></p>',
					'<p>' . __( 'First, we will download the current positions, but not import them.', 'personio-integration-light' ) . '</p>',
					'<p>' . __( 'We will then show you each positions for confirmation. This allows you to choose which positions should be imported and which should not.', 'personio-integration-light' ) . '</p>',
					'<p>' . __( 'Positions you have not selected will also be removed from WordPress if they already exist there.', 'personio-integration-light' ) . '</p>',
					/* translators: %1$s will be replaced by a URL. */
					'<p><strong>' . sprintf( __( 'Please note that positions you did not select would still be imported during automatic import. With <a href="%1$s" target="_blank">Personio Integration Pro</a>, you can prevent this from happening.', 'personio-integration-light' ), Helper::get_pro_url() ) . '</strong></p>',
				),
				'buttons' => array(
					array(
						'action'  => 'personio_integration_run_manual_import();',
						'variant' => 'primary',
						'text'    => __( 'Yes', 'personio-integration-light' ),
					),
					array(
						'action'  => 'closeDialog();',
						'variant' => 'secondary',
						'text'    => __( 'No', 'personio-integration-light' ),
					),
				),
			),
		);

		// send response as JSON.
		wp_send_json( $dialog );
	}

	/**
	 * Download the positions without importing them via the active import handler.
	 *
	 * @return void
	 */
	public function download_positions(): void {
		// check nonce.
		check_ajax_referer( 'personio-integration-light-manual-import', 'nonce' );

		// get the actual import object.
		$import_obj = $this->get_import_object_via_ajax();

		// disable the check for changes during import.
		add_filter( 'personio_integration_light_import_of_url_starting', '__return_false' );

		// add filter to save the position data in our list for manual import
		// and prevent the saving of positions on the normal way.
		add_filter( 'personio_integration_import_single_position', array( $this, 'save_position' ), 10, 5 );

		// prevent cleanup after import.
		add_filter( 'personio_integration_light_import_bail_before_cleanup', '__return_true' );

		// call the import.
		$import_obj->run();

		// bail if list of positions is empty.
		if ( empty( $this->positions ) ) {
			// define dialog.
			$dialog = array(
				'detail' => array(
					'title'   => __( 'No positions found', 'personio-integration-light' ),
					'texts'   => array(
						'<p>' . __( 'No positions were found for selection during manual import.', 'personio-integration-light' ) . '</p>',
					),
					'buttons' => array(
						array(
							'action'  => 'closeDialog();',
							'variant' => 'primary',
							'text'    => __( 'OK', 'personio-integration-light' ),
						),
					),
				),
			);

			// send response as JSON.
			wp_send_json( $dialog );
		}

		// filter the next query for positions to only get their Personio IDs.
		add_filter( 'personio_integration_positions_resulting_list', array( $this, 'get_personio_ids_from_db' ) );

		// get list of positions in WordPress as ID-list.
		$positions_in_db = Positions::get_instance()->get_positions();

		// collect all Personio IDs to a string list.
		$personio_id_list = '';

		// generate the list of positions to chose which one should be imported.
		$list = '<label for="check_all"><input type="checkbox" id="check_all" name="check_all" value="1"> ' . __( 'Check all', 'personio-integration-light' ) . '<ul>';
		foreach ( $this->positions as $position_obj ) {
			// set check marker if this position is already in WordPress OR none positions are set.
			$checked = ( empty( $positions_in_db ) || in_array( $position_obj->get_personio_id(), $positions_in_db, true ) ) ? ' checked' : '';

			// add to the list.
			if ( ! empty( $personio_id_list ) ) {
				$personio_id_list .= ',';
			}
			$personio_id_list .= $position_obj->get_personio_id();

			// add the HTML-code.
			$list .= '<li><label for="position' . $position_obj->get_personio_id() . '"><input type="checkbox" data-personio-id="' . $position_obj->get_personio_id() . '" id="position' . $position_obj->get_personio_id() . '" name="position[' . $position_obj->get_personio_id() . ']" value="1"' . $checked . '> ' . $position_obj->get_title() . ' (' . $position_obj->get_personio_id() . ')</label></li>';
		}
		$list .= '</ul>';

		// define dialog.
		$dialog = array(
			'detail' => array(
				'className' => 'personio-integration-manual-import-selection',
				'title'     => __( 'Choose positions to import', 'personio-integration-light' ),
				'callback'  => 'personio_integration_run_manual_import_callback()',
				'texts'     => array(
					'<p><strong>' . __( 'Select the positions you want to import in your WordPress.', 'personio-integration-light' ) . '</strong></p>',
					$list,
					'<p>' . __( '<strong>Hint:</strong> not selected positions will not be imported and also deleted in WordPress if they exist there.', 'personio-integration-light' ) . '</p>',
					'<input type="hidden" id="all_positions" name="all_positions" value="' . $personio_id_list . '">',
				),
				'buttons'   => array(
					array(
						'action'  => 'personio_integration_save_manual_import();',
						'variant' => 'primary',
						'text'    => __( 'Save them', 'personio-integration-light' ),
					),
					array(
						'action'  => 'closeDialog();',
						'variant' => 'secondary',
						'text'    => __( 'Cancel', 'personio-integration-light' ),
					),
				),
			),
		);

		// send response as JSON.
		wp_send_json( $dialog );
	}

	/**
	 * Save data of the given position in DB and prevent normal import of them.
	 *
	 * We only save some main data from the position to show them in dialog.
	 *
	 * @param bool         $run_import The return value, should be false here.
	 * @param object       $source_object The object to import.
	 * @param string       $language_name The language name.
	 * @param Personio     $personio_obj The used Personio object.
	 * @param Imports_Base $imports_obj The used imports object.
	 *
	 * @return bool
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function save_position( bool $run_import, object $source_object, string $language_name, Personio $personio_obj, Imports_Base $imports_obj ): bool {
		// get the position object from source object.
		$this->positions[] = $imports_obj->get_position_from_object( $source_object, $language_name, $personio_obj->get_url() );

		// return false to prevent normal import.
		return false;
	}

	/**
	 * Import the selected positions.
	 *
	 * @return void
	 */
	public function import_selected_positions(): void {
		// check nonce.
		check_ajax_referer( 'personio-integration-light-manual-import-save', 'nonce' );

		// get the selected positions.
		$selected_positions = isset( $_POST['selected_positions'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['selected_positions'] ) ) : array();

		// get all positions.
		$all_positions_string = filter_input( INPUT_POST, 'all_positions', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$all_positions        = array();
		if ( ! empty( $all_positions_string ) ) {
			$all_positions = explode( ',', $all_positions_string );
		}

		/**
		 * Filter the selected positions for manual import.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param array<int,string> $positions List of selected positions.
		 * @param array<int,string> $all_positions List of all positions.
		 */
		$this->simple_positions = apply_filters( 'personio_integration_light_manual_import_selected_positions', $selected_positions, $all_positions );

		// bail if list of positions is empty.
		if ( empty( $this->simple_positions ) ) {
			// define dialog.
			$dialog = array(
				'detail' => array(
					'title'   => __( 'No positions selected', 'personio-integration-light' ),
					'texts'   => array(
						'<p>' . __( 'No positions were selected for import.', 'personio-integration-light' ) . '</p>',
					),
					'buttons' => array(
						array(
							'action'  => 'closeDialog();',
							'variant' => 'primary',
							'text'    => __( 'OK', 'personio-integration-light' ),
						),
					),
				),
			);

			// send response as JSON.
			wp_send_json( $dialog );
		}

		// get the actual import object.
		$import_obj = $this->get_import_object_via_ajax();

		// add filter to only import the selected positions.
		add_filter( 'personio_integration_import_single_position', array( $this, 'prevent_import_of_position' ), 10, 5 );

		// run the import.
		$import_obj->run();

		// define dialog.
		$dialog = array(
			'detail' => array(
				'title'   => __( 'Import has been run', 'personio-integration-light' ),
				'texts'   => array(
					'<p>' . __( 'The selected positions are now imported.', 'personio-integration-light' ) . '</p>',
				),
				'buttons' => array(
					array(
						'action'  => 'closeDialog();',
						'variant' => 'primary',
						'text'    => __( 'OK', 'personio-integration-light' ),
					),
				),
			),
		);

		// send response as JSON.
		wp_send_json( $dialog );
	}

	/**
	 * Return the actual import object. If any error occurred, return a dialog.
	 *
	 * @return Imports_Base
	 */
	private function get_import_object_via_ajax(): Imports_Base {
		// get the active import object.
		$import_obj = Imports::get_instance()->get_import_extension();

		// bail if no import object is active.
		if ( ! $import_obj instanceof Imports_Base ) {
			// define dialog.
			$dialog = array(
				'detail' => array(
					'title'   => __( 'Error', 'personio-integration-light' ),
					'texts'   => array(
						/* translators: %1$s will be replaced by a URL. */
						'<p>' . sprintf( __( 'No import extension is activated. Please <a href="%1$s" target="_blank">go to Extensions</a> and activate the import option you want.', 'personio-integration-light' ), add_query_arg( array( 'category' => 'imports' ), Helper::get_settings_url( 'personioPositionExtensions' ) ) ) . '</p>',
					),
					'buttons' => array(
						array(
							'action'  => 'closeDialog();',
							'variant' => 'primary',
							'text'    => __( 'OK', 'personio-integration-light' ),
						),
					),
				),
			);

			// send response as JSON.
			wp_send_json( $dialog );
		}

		// bail if import can not be run.
		if ( ! $import_obj->can_be_run() ) {
			// define dialog.
			$dialog = array(
				'detail' => array(
					'title'   => __( 'Error', 'personio-integration-light' ),
					'texts'   => array(
						/* translators: %1$s will be replaced by a URL. */
						'<p>' . sprintf( __( 'Import can not be run. Please <a href="%1$s" target="_blank">go to logs</a> for details.', 'personio-integration-light' ), Helper::get_settings_url( 'personioPositions', 'logs' ) ) . '</p>',
					),
					'buttons' => array(
						array(
							'action'  => 'closeDialog();',
							'variant' => 'primary',
							'text'    => __( 'OK', 'personio-integration-light' ),
						),
					),
				),
			);

			// send response as JSON.
			wp_send_json( $dialog );
		}

		// return the actual import object.
		return $import_obj;
	}

	/**
	 * Prevent import of position which is not in our list.
	 *
	 * @param bool         $run_import The return value, should be false here.
	 * @param object       $source_object The object to import.
	 * @param string       $language_name The language name.
	 * @param Personio     $personio_obj The used Personio object.
	 * @param Imports_Base $imports_obj The used imports object.
	 *
	 * @return bool
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function prevent_import_of_position( bool $run_import, object $source_object, string $language_name, Personio $personio_obj, Imports_Base $imports_obj ): bool {
		// get the position object.
		$position_obj = $imports_obj->get_position_from_object( $source_object, $language_name, $personio_obj->get_url() );

		// bail if Personio ID of this position is in list.
		if ( in_array( $position_obj->get_personio_id(), $this->simple_positions, true ) ) {
			return $run_import;
		}

		// return false to prevent the import of this position.
		return false;
	}

	/**
	 * Return a list of all positions only with their Personio Ids.
	 *
	 * @param array<int,Position> $positions List of position objects.
	 *
	 * @return array<int,string>
	 */
	public function get_personio_ids_from_db( array $positions ): array {
		// create the new list.
		$new_list = array();

		// get the Personio Ids.
		foreach ( $positions as $position_obj ) {
			$new_list[] = $position_obj->get_personio_id();
		}

		// return the resulting list.
		return $new_list;
	}

	/**
	 * Return the installation state of the dependent plugin/theme.
	 *
	 * @return bool
	 */
	public function is_installed(): bool {
		return true;
	}
}
