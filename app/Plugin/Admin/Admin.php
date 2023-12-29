<?php
/**
 * File for handling tasks in wp-admin.
 *
 * @package personio-integration-light
 */

namespace App\Plugin\Admin;

use App\Helper;
use App\PersonioIntegration\Import;
use App\Plugin\Cli;

/**
 * Helper-function for tasks in wp-admin.
 */
class Admin {
	/**
	 * Instance of this object.
	 *
	 * @var ?Admin
	 */
	private static ?Admin $instance = null;

	/**
	 * Constructor for Init-Handler.
	 */
	private function __construct() {
	}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {
	}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Admin {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Initialize the wp-admin support.
	 *
	 * @return void
	 */
	public function init(): void {
		// enqueue scripts and styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'add_styles_and_js' ), PHP_INT_MAX );

		// initialize the Dashboard-support.
		Dashboard::get_instance()->init();

		// init site health.
		Site_Health::get_instance()->init();

		// show hint for Pro-version.
		add_action( 'personio_integration_admin_show_pro_hint', array( $this, 'show_pro_hint' ) );

		// add marker for free-version.
		add_filter( 'admin_body_class', array( $this, 'add_body_class_free' ) );

		// check for page builder support.
		add_action( 'admin_init', array( $this, 'check_for_pagebuilder' ) );

		// register our own importer in backend.
		add_action( 'admin_init', array( $this, 'add_importer' ) );

		// add admin_actions.
		add_action( 'admin_action_personioPositionsImport', array( $this, 'import_positions' ) );
		add_action( 'admin_action_personioPositionsCancelImport', array( $this, 'cancel_import' ) );
		add_action( 'admin_action_personioPositionsDelete', array( $this, 'delete_positions' ) );
	}

	/**
	 * Add own CSS and JS for backend.
	 *
	 * @return void
	 */
	public function add_styles_and_js(): void {
		// admin-specific styles.
		wp_enqueue_style(
			'personio_integration-admin-css',
			plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/admin/styles.css',
			array(),
			filemtime( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/admin/styles.css' ),
		);

		// admin- and backend-styles for attribute-type-output.
		wp_enqueue_style(
			'personio_integration-styles',
			plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/css/styles.css',
			array(),
			filemtime( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/css/styles.css' )
		);

		// backend-JS.
		wp_enqueue_script(
			'personio_integration-admin-js',
			plugins_url( '/admin/js.js', WP_PERSONIO_INTEGRATION_PLUGIN ),
			array( 'jquery' ),
			filemtime( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/admin/js.js' ),
			true
		);

		// add php-vars to our js-script.
		wp_localize_script(
			'personio_integration-admin-js',
			'customJsVars',
			array(
				'ajax_url'                => admin_url( 'admin-ajax.php' ),
				'pro_url'                 => Helper::get_pro_url(),
				'label_go_pro'            => __( 'Get Personio Integration Pro', 'personio-integration-light' ),
				'dismiss_nonce'           => wp_create_nonce( 'wp-dismiss-notice' ),
				'run_import_nonce'        => wp_create_nonce( 'personio-run-import' ),
				'get_import_nonce'        => wp_create_nonce( 'personio-get-import-info' ),
				'label_reset_sort'        => __( 'Reset sorting', 'personio-integration-light' ),
				'label_run_import'        => __( 'Run import', 'personio-integration-light' ),
				'label_import_is_running' => __( 'Import is running', 'personio-integration-light' ),
				'txt_please_wait'         => __( 'Please wait', 'personio-integration-light' ),
				'txt_import_hint'         => __( 'Performing the import could take a few minutes. If a timeout occurs, a manual import is not possible this way. Then the automatic import should be used.', 'personio-integration-light' ),
				'txt_import_has_been_run' => sprintf(
				/* translators: %1$s is replaced with "string", %2$s is replaced with "string" */
					__( '<strong>The import has been manually run.</strong> Please check the list of positions <a href="%1$s">in backend</a> and <a href="%2$s">frontend</a>.', 'personio-integration-light' ),
					esc_url(
						add_query_arg(
							array(
								'post_type' => WP_PERSONIO_INTEGRATION_CPT,
							),
							get_admin_url() . 'edit.php'
						)
					),
					get_post_type_archive_link( WP_PERSONIO_INTEGRATION_CPT )
				),
				'label_ok'                => __( 'OK', 'personio-integration-light' ),
			)
		);

		// embed necessary scripts for progressbar.
		// TODO replace this
		if ( ( ! empty( $_GET['post_type'] ) && WP_PERSONIO_INTEGRATION_CPT === $_GET['post_type'] ) || ( ! empty( $_GET['import'] ) && 'personio-integration-importer' === $_GET['import'] ) ) {
			$wp_scripts = wp_scripts();
			wp_enqueue_script( 'jquery-ui-progressbar' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_style(
				'personio-jquery-ui-styles',
				'https://code.jquery.com/ui/' . $wp_scripts->registered['jquery-ui-core']->ver . '/themes/smoothness/jquery-ui.min.css',
				false,
				'1.0.0',
				false
			);
		}
	}

	/**
	 * Show hint for our Pro-version.
	 *
	 * @param string $hint The individual hint to show before pro-hint.
	 * @return void
	 */
	public function show_pro_hint( string $hint ): void {
		echo '<p class="personio-pro-hint">' . sprintf( wp_kses_post( $hint ), '<a href="' . esc_url( Helper::get_pro_url() ) . '" target="_blank">Personio Integration Pro (opens new window)</a>' ) . '</p>';
	}

	/**
	 * Add marker for free version on body-element
	 *
	 * TODO nötig?
	 *
	 * @param string $classes List of classes for body-element in wp-admin.
	 * @return string
	 * @noinspection PhpUnused
	 */
	public function add_body_class_free( string $classes ): string {
		$classes .= ' personio-integration-free';
		if ( ! Helper::is_personio_url_set() ) {
			$classes .= ' personio-integration-url-missing';
		}
		return $classes;
	}

	/**
	 * Check for supported PageBuilder and show hint if Pro-version would support it.
	 *
	 * TODO austauschen durch neues Transient-object.
	 *
	 * @return void
	 */
	public function check_for_pagebuilder(): void {
		// bail if our Pro-plugin is active.
		if ( false !== Helper::is_plugin_active( 'personio-integration/personio-integration.php' ) ) {
			delete_transient( 'personio_integration_divi' );
			delete_transient( 'personio_integration_elementor' );
			delete_transient( 'personio_integration_wpbakery' );
			delete_transient( 'personio_integration_beaver' );
			delete_transient( 'personio_integration_siteorigin' );
			delete_transient( 'personio_integration_themify' );
			delete_transient( 'personio_integration_avada' );
			return;
		}

		/**
		 * Check for Divi PageBuilder or Divi Theme.
		 */
		if ( false === Helper::is_plugin_active( 'personio-integration-divi/personio-integration-divi.php' ) && ( Helper::is_plugin_active( 'divi-builder/divi-builder.php' ) || 'Divi' === wp_get_theme()->get( 'Name' ) ) ) {
			set_transient( 'personio_integration_divi', 1 );
		} else {
			delete_transient( 'personio_integration_divi' );
		}

		/**
		 * Check for Elementor.
		 */
		if ( did_action( 'elementor/loaded' ) ) {
			set_transient( 'personio_integration_elementor', 1 );
		} else {
			delete_transient( 'personio_integration_elementor' );
		}

		/**
		 * Check for WPBakery.
		 */
		if ( Helper::is_plugin_active( 'js_composer/js_composer.php' ) ) {
			set_transient( 'personio_integration_wpbakery', 1 );
		} else {
			delete_transient( 'personio_integration_wpbakery' );
		}

		/**
		 * Check for Beaver Builder.
		 */
		if ( class_exists( 'FLBuilder' ) ) {
			set_transient( 'personio_integration_beaver', 1 );
		} else {
			delete_transient( 'personio_integration_beaver' );
		}

		/**
		 * Check for SiteOrigin.
		 */
		if ( Helper::is_plugin_active( 'siteorigin-panels/siteorigin-panels.php' ) ) {
			set_transient( 'personio_integration_siteorigin', 1 );
		} else {
			delete_transient( 'personio_integration_siteorigin' );
		}

		/**
		 * Check for Themify.
		 */
		if ( Helper::is_plugin_active( 'themify-builder/themify-builder.php' ) ) {
			set_transient( 'personio_integration_themify', 1 );
		} else {
			delete_transient( 'personio_integration_themify' );
		}

		/**
		 * Check for Avada.
		 */
		if ( Helper::is_plugin_active( 'fusion-builder/fusion-builder.php' ) ) {
			set_transient( 'personio_integration_avada', 1 );
		} else {
			delete_transient( 'personio_integration_avada' );
		}
	}

	/**
	 * Add custom importer for positions under Tools > Import.
	 *
	 * @return void
	 */
	public function add_importer(): void {
		register_importer(
			'personio-integration-importer',
			__( 'Personio', 'personio-integration-light' ),
			__( 'Import positions from Personio', 'personio-integration-light' ),
			array( $this, 'add_menu_content_importexport' ) // TODO Settingspage anzeigen.
		);
	}

	/**
	 * Start import manually.
	 *
	 * @return void
	 */
	public function import_positions(): void {
		check_ajax_referer( 'wp-personio-integration-import', 'nonce' );

		// run import.
		new Import();

		// add hint.
		set_transient( 'personio_integration_import_run', 1 );

		// remove other hint.
		delete_transient( 'personio_integration_no_position_imported' );

		// redirect user.
		wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
	}

	/**
	 * Set marker to cancel running import.
	 *
	 * @return void
	 */
	public function cancel_import(): void {
		check_ajax_referer( 'wp-personio-integration-cancel-import', 'nonce' );

		// check if import as running.
		if ( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) > 0 ) {
			// remove running marker.
			update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );

			// add hint.
			set_transient( 'personio_integration_import_canceled', 1 );
		}

		// redirect user.
		wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
	}

	/**
	 * Delete all positions manually.
	 *
	 * @return void
	 */
	public function delete_positions(): void {
		check_ajax_referer( 'wp-personio-integration-delete', 'nonce' );

		// do not delete positions if import is running atm.
		if ( 0 === absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) ) ) {
			// delete positions.
			$user = wp_get_current_user();
			( new cli() )->delete_positions( array( 'Delete all positions button', ' by ' . $user->display_name ) );

			// add hint..
			set_transient( 'personio_integration_delete_run', 1 );
		} else {
			set_transient( 'personio_integration_could_not_delete', 1 );
		}

		// redirect user.
		wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
	}
}
