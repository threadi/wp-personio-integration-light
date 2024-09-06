<?php
/**
 * File for handling import of settings.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;

/**
 * Helper-function for import of settings.
 */
class Settings_Import {

	/**
	 * Instance of this object.
	 *
	 * @var ?Settings_Import
	 */
	private static ?Settings_Import $instance = null;

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
	public static function get_instance(): Settings_Import {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Initialize the importer.
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

		// use our own hook.
		add_action( 'wp_ajax_personio_integration_settings_import_file', array( $this, 'import_settings' ) );
	}

	/**
	 * Add settings for the import of settings.
	 *
	 * @param array $settings List of settings.
	 *
	 * @return array
	 */
	public function add_settings( array $settings ): array {
		// bail if advanced section is not available.
		if ( empty( $settings['settings_section_advanced'] ) ) {
			return $settings;
		}

		// add settings.
		$settings['settings_section_advanced']['fields'] = Helper::add_array_in_array_on_position(
			$settings['settings_section_advanced']['fields'],
			5,
			array(
				'personioIntegrationImportSettings' => array(
					'label' => __( 'Import settings', 'personio-integration-light' ),
					'field' => array( $this, 'show_button' ),
				),
			)
		);

		// return resulting list.
		return $settings;
	}

	/**
	 * Show import button.
	 *
	 * @return void
	 */
	public function show_button(): void {
		// define import-dialog.
		$dialog = array(
			'title'   => __( 'Import settings', 'personio-integration-light' ),
			'texts'   => array(
				'<p>' . __( 'Uploading a new configuration overwrites all current settings.<br>Imported positions are not affected by this.', 'personio-integration-light' ) . '</p>',
				'<label for="import_settings_file">' . __( 'Choose file to import:', 'personio-integration-light' ) . '</label>',
				'<input type="file" id="import_settings_file" name="import_settings_file" accept="application/json">',
			),
			'buttons' => array(
				array(
					'action'  => 'personio_integration_import_settings_file();',
					'variant' => 'primary',
					'text'    => __( 'Import file', 'personio-integration-light' ),
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
		<a href="" class="button button-primary wp-easy-dialog" data-dialog="<?php echo esc_attr( wp_json_encode( $dialog ) ); ?>"><?php echo esc_html__( 'Import settings', 'personion-integration-light' ); ?></a>
		<?php
	}

	/**
	 * Import settings file via AJAX.
	 *
	 * @return void
	 */
	public function import_settings(): void {
		// check nonce.
		check_ajax_referer( 'personio-integration-settings-import-file', 'nonce' );

		// bail if no file is given.
		if ( ! isset( $_FILES ) ) {
			wp_send_json(
				array(
					'success' => false,
					'html'    => __( 'No file uploaded.', 'personio-integration-light' ),
				)
			);
		}

		// bail if file has no size.
		if ( isset( $_FILES['file']['size'] ) && 0 === $_FILES['file']['size'] ) {
			wp_send_json(
				array(
					'success' => false,
					'html'    => __( 'The uploaded file is no size.', 'personio-integration-light' ),
				)
			);
		}

		// bail if file type is not JSON.
		if ( isset( $_FILES['file']['type'] ) && 'application/json' !== $_FILES['file']['type'] ) {
			wp_send_json(
				array(
					'success' => false,
					'html'    => __( 'The uploaded file is not a valid JSON-file.', 'personio-integration-light' ),
				)
			);
		}

		// allow JSON-files.
		add_filter( 'upload_mimes', array( $this, 'allow_json' ) );

		// bail if file type is not JSON.
		if ( isset( $_FILES['file']['name'] ) ) {
			$filetype = wp_check_filetype( sanitize_file_name( wp_unslash( $_FILES['file']['name'] ) ) );
			if ( 'json' !== $filetype['ext'] ) {
				wp_send_json(
					array(
						'success' => false,
						'html'    => __( 'The uploaded file does not have the file extension <i>.json</i>.', 'personio-integration-light' ),
					)
				);
			}
		}

		// bail if no tmp_name is available.
		if ( ! isset( $_FILES['file']['tmp_name'] ) ) {
			wp_send_json(
				array(
					'success' => false,
					'html'    => __( 'The uploaded file could not be saved. Contact your hoster about this problem.', 'personio-integration-light' ),
				)
			);
		}

		// bail if uploaded file is not readable.
		if ( isset( $_FILES['file']['tmp_name'] ) && ! file_exists( sanitize_text_field( $_FILES['file']['tmp_name'] ) ) ) {
			wp_send_json(
				array(
					'success' => false,
					'html'    => __( 'The uploaded file could not be saved. Contact your hoster about this problem.', 'personio-integration-light' ),
				)
			);
		}

		// get WP Filesystem-handler for read the file.
		require_once ABSPATH . '/wp-admin/includes/file.php';
		\WP_Filesystem();
		global $wp_filesystem;
		$file_content = $wp_filesystem->get_contents( sanitize_text_field( wp_unslash( $_FILES['file']['tmp_name'] ) ) );

		// convert JSON to array.
		$settings_array = json_decode( $file_content, ARRAY_A );

		// bail if JSON-code does not contain a setting for the Personio URL.
		if ( ! isset( $settings_array['personioIntegrationUrl'] ) ) {
			wp_send_json(
				array(
					'success' => false,
					'html'    => __( 'The uploaded file is not a valid JSON-file with settings for this plugin.', 'personio-integration-light' ),
				)
			);
		}

		/**
		 * Run additional tasks before running the import of settings.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 */
		do_action( 'personio_integration_light_settings_import' );

		// import the settings.
		foreach ( $settings_array as $field_name => $field_value ) {
			update_option( $field_name, $field_value );
		}

		// return that import was successfully.
		wp_send_json(
			array(
				'success' => true,
				'html'    => __( 'Import has been run successfully.', 'personio-integration-light' ),
			)
		);
	}

	/**
	 * Allow SVG as file-type.
	 *
	 * @param array $file_types List of file types.
	 *
	 * @return array
	 */
	public function allow_json( array $file_types ): array {
		$new_filetypes         = array();
		$new_filetypes['json'] = 'application/json';
		return array_merge( $file_types, $new_filetypes );
	}
}
