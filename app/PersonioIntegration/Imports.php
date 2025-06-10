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
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Transients;

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

		// bail of no import extension is enabled.
		if ( ! $this->is_one_extension_enabled() ) {
			// show warning about missing enabled import extension.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_import_extension_not_enabled' );
			$transient_obj->set_dismissible_days( 30 );
			$transient_obj->set_type( 'error' );
			/* translators: %1$s will be replaced by a URL. */
			$transient_obj->set_message( sprintf( __( 'There is no import extension for Personio positions enabled. Please <a href="%1$s">go to the list of import extensions</a> and enable one to import and update your positions in your website.', 'personio-integration-light' ), esc_url( Extensions::get_instance()->get_link( 'imports' ) ) ) );
			$transient_obj->save();
			return;
		}

		// remove transient with warning.
		Transients::get_instance()->delete_transient( Transients::get_instance()->get_transient_by_name( 'personio_import_extension_not_enabled' ) );

		// add settings.
		add_filter( 'init', array( $this, 'add_settings' ) );
	}

	/**
	 * Add the imports as extensions.
	 *
	 * @param array<string> $extensions List of all extensions.
	 *
	 * @return array<string>
	 */
	public function add_import_extensions( array $extensions ): array {
		return array_merge( $extensions, $this->get_import_extensions() );
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
	 * Add and change the settings for the plugin.
	 *
	 * @return void
	 */
	public function add_settings(): void {
		// get settings object.
		$settings_obj = \PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings::get_instance();

		// add the import tab.
		$import_tab = $settings_obj->add_tab( 'import' );
		$import_tab->set_title( __( 'Import', 'personio-integration-light' ) );
		$import_tab->set_position( 1 );

		// add main section.
		$import_section = $import_tab->add_section( 'settings_section_import' );
		$import_section->set_title( __( 'Import of positions from Personio', 'personio-integration-light' ) );
		$import_section->set_setting( $settings_obj );

		// add other section.
		$import_other_section = $import_tab->add_section( 'settings_section_import_other' );
		$import_other_section->set_title( __( 'Other settings', 'personio-integration-light' ) );
		$import_other_section->set_setting( $settings_obj );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationImportNow' );
		$setting->set_section( $import_section );
		$setting->set_autoload( false );
		$setting->prevent_export( true );
		$field = new Button();
		$field->set_title( __( 'Get open positions from Personio', 'personio-integration-light' ) );
		$field->set_button_title( __( 'Run import of positions now', 'personio-integration-light' ) );
		$field->add_class( 'personio-integration-import-hint' );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationDeleteNow' );
		$setting->set_section( $import_section );
		$setting->set_autoload( false );
		$setting->prevent_export( true );
		$field = new Button();
		$field->set_title( __( 'Delete local positions', 'personio-integration-light' ) );
		$field->set_button_title( __( 'Delete all positions', 'personio-integration-light' ) );
		$field->add_class( 'personio-integration-delete-all' );
		$setting->set_field( $field );

		// add setting.
		/* translators: %1$s will be replaced by a link to the Pro plugin page. */
		$pro_hint = __( 'Use more import options with the %1$s. Among other things, you get the possibility to change the time interval for imports and partial imports of very large position lists.', 'personio-integration-light' );
		$true = true;
		$automatic_import_setting = $settings_obj->add_setting( 'personioIntegrationEnablePositionSchedule' );
		$automatic_import_setting->set_section( $import_section );
		$automatic_import_setting->set_type( 'integer' );
		$automatic_import_setting->set_default( 1 );
		$field = new Checkbox();
		$field->set_title( __( 'Enable automatic import', 'personio-integration-light' ) );
		$field->set_description( __( 'The automatic import is run once per day. You don\'t have to worry about updating your jobs on the website yourself.', 'personio-integration-light' ) . apply_filters( 'personio_integration_admin_show_pro_hint', $pro_hint, $true ) );
		$automatic_import_setting->set_field( $field );
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
}
