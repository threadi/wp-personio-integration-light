<?php
/**
 * File for handling all extensions for import of positions from Personio.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Button;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Checkbox;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\TextInfo;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Page;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Setup;
use easyTransientsForWordPress\Transients;

/**
 * Object to handle all import extensions.
 */
class Imports {
	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Imports
	 */
	protected static ?Imports $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Imports {
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
		// use our own hooks.
		add_filter( 'personio_integration_extend_position_object', array( $this, 'add_import_extensions' ) );
		add_filter( 'personio_integration_extension_categories', array( $this, 'add_category' ) );

		// add settings.
		add_action( 'init', array( $this, 'add_settings' ), 20 );

		// bail of no import extension is enabled.
		if ( ! $this->is_one_extension_enabled() ) {
			add_action( 'init', array( $this, 'trigger_no_extension_hint' ) );
			return;
		}

		// remove transient with warning.
		Transients::get_instance()->delete_transient( Transients::get_instance()->get_transient_by_name( 'personio_import_extension_not_enabled' ) );

		// use our own hooks.
		add_action( 'personio_integration_import_starting', array( $this, 'reset_new_position_list' ) );
		add_filter( 'personio_integration_import_single_position_filter_before_saving', array( $this, 'check_if_position_is_new' ), 10, 2 );
		add_action( 'personio_integration_light_import_deleted_position', array( $this, 'add_to_list_of_deleted_positions' ) );
	}

	/**
	 * Tasks to run during plugin activation for this extension.
	 *
	 * @return void
	 */
	public function add_settings(): void {
		// get settings object.
		$settings_obj = Settings::get_instance();

		// get settings page.
		$settings_page = $settings_obj->get_page( 'personioPositions' );

		// bail if page could not be loaded.
		if ( ! $settings_page instanceof Page ) {
			return;
		}

		// add the import tab.
		$import_tab = $settings_page->add_tab( 'import', 20 );
		$import_tab->set_title( __( 'Import', 'personio-integration-light' ) );

		// add main section.
		$import_section = $import_tab->add_section( 'settings_section_import', 10 );
		$import_section->set_title( __( 'Import of positions from Personio', 'personio-integration-light' ) );
		$import_section->set_setting( $settings_obj );

		// add setting.
		$running_import = absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) );
		$import_now_setting = $settings_obj->add_setting( 'personioIntegrationImportNow' );
		$import_now_setting->set_section( $import_section );
		$import_now_setting->set_autoload( false );
		$import_now_setting->prevent_export( true );
		if( $running_import > 0 && ( $running_import + HOUR_IN_SECONDS ) < time() ) {
			$url = add_query_arg(
				array(
					'action' => 'personioPositionsCancelImport',
					'nonce'  => wp_create_nonce( 'personio-integration-cancel-import' ),
				),
				get_admin_url() . 'admin.php'
			);
			$field = new Button();
			$field->set_title( __( 'Get open positions from Personio', 'personio-integration-light' ) );
			$field->set_button_title( __( 'Cancel running import', 'personio-integration-light' ) );
			$field->set_button_url( $url );
		}
		elseif( $running_import > 0 ) {
			$field = new TextInfo();
			$field->set_title( __( 'Get open positions from Personio', 'personio-integration-light' ) );
			$field->set_description( __( 'The import is already running. Please wait some moments.', 'personio-integration-light' ) );
		}
		else {
			$field = new Button();
			$field->set_title( __( 'Get open positions from Personio', 'personio-integration-light' ) );
			$field->set_button_title( __( 'Run import of positions now', 'personio-integration-light' ) );
			$field->add_class( 'personio-integration-import-hint' );
		}
		$import_now_setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationDeleteNow' );
		$setting->set_section( $import_section );
		$setting->set_autoload( false );
		$setting->prevent_export( true );
		if ( Positions::get_instance()->get_positions_count() > 0 ) {
			$field = new Button();
			$field->set_title( __( 'Clear positions', 'personio-integration-light' ) );
			$field->set_button_title( __( 'Delete all positions', 'personio-integration-light' ) );
			$field->add_class( 'personio-integration-delete-all' );
		} else {
			$field = new TextInfo();
			$field->set_title( __( 'Clear positions', 'personio-integration-light' ) );
			$field->set_description( __( 'There are currently no positions imported.', 'personio-integration-light' ) );
		}
		$setting->set_field( $field );

		// add setting.
		/* translators: %1$s will be replaced by a link to the Pro plugin page. */
		$pro_hint                 = __( 'Use more import options with the %1$s. Among other things, you get the possibility to change the time interval for imports and partial imports of very large position lists.', 'personio-integration-light' );
		$true                     = true;
		$automatic_import_setting = $settings_obj->add_setting( 'personioIntegrationEnablePositionSchedule' );
		$automatic_import_setting->set_section( $import_section );
		$automatic_import_setting->set_type( 'integer' );
		$automatic_import_setting->set_default( 1 );
		$automatic_import_setting->set_save_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsSavings\Import', 'save' ) );
		$field = new Checkbox();
		$field->set_title( __( 'Enable automatic import', 'personio-integration-light' ) );
		$field->set_description( __( 'The automatic import is run once per day. You don\'t have to worry about updating your jobs on the website yourself.', 'personio-integration-light' ) . apply_filters( 'personio_integration_admin_show_pro_hint', $pro_hint, $true ) );
		$automatic_import_setting->set_field( $field );
	}

	/**
	 * Add the imports as extensions.
	 *
	 * @param array<string> $extensions List of all extensions.
	 *
	 * @return array<string>
	 */
	public function add_import_extensions( array $extensions ): array {
		return array_merge( $this->get_import_extensions(), $extensions );
	}

	/**
	 * Return list of import extensions.
	 *
	 * @return array<int,string>
	 */
	private function get_import_extensions(): array {
		$import_extensions = array(
			'\PersonioIntegrationLight\PersonioIntegration\Imports\Api',
			'\PersonioIntegrationLight\PersonioIntegration\Imports\Xml',
		);

		/**
		 * Filter the import extensions.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param array<int,string> $import_extensions List of import extensions.
		 */
		return apply_filters( 'personio_integration_light_import_extensions', $import_extensions );
	}

	/**
	 * Add categories for this extension type.
	 *
	 * @param array<string> $categories List of categories.
	 *
	 * @return array<string>
	 */
	public function add_category( array $categories ): array {
		// add category for this extension type.
		$categories['imports'] = __( 'Imports', 'personio-integration-light' );

		// return resulting list.
		return $categories;
	}

	/**
	 * Return list of import extensions as object.
	 *
	 * No check for their states.
	 *
	 * @return array<int,Imports_Base>
	 */
	private function get_import_extensions_as_object(): array {
		$list = array();
		foreach ( $this->get_import_extensions() as $import_extension_name ) {
			// get class name.
			$class_name = $import_extension_name . '::get_instance';

			// bail if class is not callable.
			if ( ! is_callable( $class_name ) ) {
				continue;
			}

			// get the handler as object.
			$obj = $class_name();

			// bail if object is not an Imports_Base.
			if ( ! $obj instanceof Imports_Base ) {
				continue;
			}

			$list[] = $obj;
		}

		// return list of import extensions.
		return $list;
	}

	/**
	 * Check if min. 1 import extension is enabled.
	 *
	 * @return bool
	 */
	private function is_one_extension_enabled(): bool {
		return false !== $this->get_import_extension();
	}

	/**
	 * Return the active import extension as object.
	 *
	 * @return Imports_Base|false
	 */
	public function get_import_extension(): Imports_Base|false {
		foreach ( $this->get_import_extensions_as_object() as $import_extension_obj ) {
			if ( ! $import_extension_obj->is_enabled() ) {
				continue;
			}

			// return the object.
			return $import_extension_obj;
		}

		// return false if no import extension is enabled.
		return false;
	}

	/**
	 * Trigger the "no extension enabled" hint.
	 *
	 * @return void
	 */
	public function trigger_no_extension_hint(): void {
		// bail if setup has not been run.
		if ( ! Setup::get_instance()->is_completed() ) {
			return;
		}

		// show warning about missing enabled import extension.
		$transient_obj = Transients::get_instance()->add();
		$transient_obj->set_name( 'personio_import_extension_not_enabled' );
		$transient_obj->set_dismissible_days( 30 );
		$transient_obj->set_type( 'error' );
		/* translators: %1$s will be replaced by a URL. */
		$transient_obj->set_message( sprintf( __( 'There is no import extension for Personio positions enabled. Please <a href="%1$s">go to the list of import extensions</a> and enable one to import and update your positions in your website.', 'personio-integration-light' ), esc_url( Extensions::get_instance()->get_link( 'imports' ) ) ) );
		$transient_obj->save();
	}

	/**
	 * Reset the list of newly imported positions.
	 *
	 * @return void
	 */
	public function reset_new_position_list(): void {
		delete_option( WP_PERSONIO_INTEGRATION_IMPORT_NEW_POSITIONS );
	}

	/**
	 * Check if we have a new position. This is detected if "ID" is 0.
	 *
	 * @param array<string,mixed> $post_array List of data.
	 * @param Position            $position_obj The position object.
	 *
	 * @return array<string,mixed>
	 */
	public function check_if_position_is_new( array $post_array, Position $position_obj ): array {
		// bail if ID is given.
		if ( absint( $post_array['ID'] ) > 0 ) {
			return $post_array;
		}

		// get the actual list of new position from this import.
		$new_positions = get_option( WP_PERSONIO_INTEGRATION_IMPORT_NEW_POSITIONS, array() );

		// bail if list is empty.
		if ( empty( $new_positions ) ) {
			$new_positions = array();
		}

		// add this position.
		$new_positions[] = $position_obj;

		// save the list of new positions.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_NEW_POSITIONS, $new_positions );

		// return the post array.
		return $post_array;
	}

	/**
	 * Add entry to the list of deleted positions.
	 *
	 * @param string $personio_id The Personio ID of the position which has been deleted.
	 *
	 * @return void
	 */
	public function add_to_list_of_deleted_positions( string $personio_id ): void {
		// get the actual list of deleted position from this import.
		$deleted_positions = get_option( WP_PERSONIO_INTEGRATION_IMPORT_DELETED_POSITIONS, array() );

		// bail if list is empty.
		if ( empty( $deleted_positions ) ) {
			$deleted_positions = array();
		}

		// add this position.
		$deleted_positions[] = $personio_id;

		// save the list of new positions.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_DELETED_POSITIONS, $deleted_positions );
	}
}
