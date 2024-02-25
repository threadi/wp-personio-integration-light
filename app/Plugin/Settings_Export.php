<?php
/**
 * File for handling export of settings.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;

/**
 * Helper-function for export of settings.
 */
class Settings_Export {

	/**
	 * Instance of this object.
	 *
	 * @var ?Settings_Export
	 */
	private static ?Settings_Export $instance = null;

	/**
	 * Constructor for Init-Handler.
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
	public static function get_instance(): Settings_Export {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Initialize the exporter.
	 *
	 * @return void
	 */
	public function init(): void {
		// bail if main block editor functions are not available.
		if ( ! has_action( 'enqueue_block_assets' ) ) {
			return;
		}

		// use our own hooks.
		add_filter( 'personio_integration_settings', array( $this, 'add_settings' ) );

		// add action hook.
		add_action( 'admin_action_personio_integration_export_settings', array( $this, 'export_settings' ) );
	}

	/**
	 * Add settings for export of settings.
	 *
	 * @param array $settings List of settings.
	 *
	 * @return array
	 */
	public function add_settings( array $settings ): array {
		$settings['settings_section_advanced']['fields'] = Helper::add_array_in_array_on_position(
			$settings['settings_section_advanced']['fields'],
			5,
			array(
				'personioIntegrationExportSettings' => array(
					'label' => __( 'Export settings', 'personio-integration-light' ),
					'field' => array( $this, 'show_button' ),
				),
			)
		);

		// return resulting list.
		return $settings;
	}

	/**
	 * Show export button.
	 *
	 * @return void
	 */
	public function show_button(): void {
		// define download-URL.
		$download_url = add_query_arg(
			array(
				'action' => 'personio_integration_export_settings',
				'nonce'  => wp_create_nonce( 'personio-integration-export-settings' ),
			),
			get_admin_url() . 'admin.php'
		);

		// define export-dialog.
		$dialog = array(
			'title'   => __( 'Export settings', 'personio-integration-light' ),
			'texts'   => array(
				'<p>' . __( 'Click on the button to download an export of all actual settings in this plugin.<br>No positions will be exported.', 'personio-integration-light' ) . '</p>',
			),
			'buttons' => array(
				array(
					'action'  => 'location.href="' . $download_url . '";closeDialog();',
					'variant' => 'primary',
					'text'    => __( 'Download', 'personio-integration-light' ),
				),
				array(
					'action'  => 'closeDialog();',
					'variant' => 'secondary',
					'text'    => __( 'Cancel', 'personio-integration-light' ),
				),
			),
		);

		// output button.
		?>
		<a href="" class="button button-primary wp-easy-dialog" data-dialog="<?php echo esc_attr( wp_json_encode( $dialog ) ); ?>"><?php echo esc_html__( 'Export settings', 'personion-integration-light' ); ?></a>
		<?php
	}

	/**
	 * Export actual settings as JSON-file.
	 *
	 * @return void
	 */
	public function export_settings(): void {
		// check for nonce.
		if ( isset( $_GET['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'personio-integration-export-settings' ) ) {
			return;
		}

		// get settings.
		$settings_list = array();
		foreach ( Settings::get_instance()->get_settings() as $section_settings ) {
			foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
				if ( ! empty( $field_settings['register_attributes'] ) && empty( $field_settings['do_not_export'] ) ) {
					$settings_list[ $field_name ] = get_option( $field_name );
				}
			}
		}

		// create filename for JSON-download-file.
		$filename = gmdate( 'YmdHi' ) . '_' . get_option( 'blogname' ) . '_Personio_Integration_Light_Settings.json';
		/**
		 * File the filename for JSON-download of all settings.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $filename The generated filename.
		 */
		$filename = apply_filters( 'personio_integration_settings_export_filename', $filename );

		// set header for response as JSON-download.
		header( 'Content-type: application/json' );
		header( 'Content-Disposition: attachment; filename=' . sanitize_file_name( $filename ) );
		echo wp_json_encode( $settings_list );
		exit;
	}
}
