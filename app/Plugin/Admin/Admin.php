<?php
/**
 * File for handling tasks in wp-admin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Import;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Cli;
use PersonioIntegrationLight\Plugin\Setup;
use PersonioIntegrationLight\Plugin\Transients;

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
		add_action( 'admin_enqueue_scripts', array( $this, 'add_dialog' ), PHP_INT_MAX );

		// initialize the Dashboard-support.
		Dashboard::get_instance()->init();

		// init site health.
		Site_Health::get_instance()->init();

		// show hint for Pro-version.
		add_action( 'personio_integration_admin_show_pro_hint', array( $this, 'show_pro_hint' ) );

		// add our own checks in wp-admin.
		add_action( 'admin_init', array( $this, 'check_for_pagebuilder' ) );
		add_action( 'admin_init', array( $this, 'check_config' ) );
		add_action( 'admin_init', array( $this, 'show_review_hint' ) );

		// register our own importer in backend.
		add_action( 'admin_init', array( $this, 'add_importer' ) );
		add_action( 'load-importer-personio-integration-importer', array( $this, 'forward_importer_to_settings' ) );

		// add admin_actions.
		add_action( 'admin_action_personioPositionsImport', array( $this, 'import_positions' ) );
		add_action( 'admin_action_personioPositionsCancelImport', array( $this, 'cancel_import' ) );
		add_action( 'admin_action_personioPositionsDelete', array( $this, 'delete_positions' ) );

		// add AJAX-actions.
		add_action( 'wp_ajax_personio_run_import', array( $this, 'run_import' ) );
		add_action( 'wp_ajax_personio_get_import_info', array( $this, 'get_import_info' ) );
	}

	/**
	 * Add own CSS and JS for backend.
	 *
	 * @return void
	 */
	public function add_styles_and_js(): void {
		// Enabled the pointer-scripts.
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );

		// admin-specific styles.
		wp_enqueue_style(
			'personio_integration-admin-css',
			Helper::get_plugin_url() . 'admin/styles.css',
			array(),
			filemtime( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/admin/styles.css' ),
		);

		// admin- and backend-styles for attribute-type-output.
		wp_enqueue_style(
			'personio_integration-styles',
			Helper::get_plugin_url() . 'css/styles.css',
			array(),
			filemtime( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/css/styles.css' )
		);

		// backend-JS.
		wp_enqueue_script(
			'personio_integration-admin-js',
			Helper::get_plugin_url() . 'admin/js.js',
			array( 'jquery', 'wp-easy-dialog', 'wp-i18n' ),
			filemtime( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . '/admin/js.js' ),
			true
		);

		// add php-vars to our js-script.
		wp_localize_script(
			'personio_integration-admin-js',
			'personioIntegrationLightJsVars',
			array(
				'ajax_url'                => admin_url( 'admin-ajax.php' ),
				'pro_url'                 => Helper::get_pro_url(),
				'dismiss_nonce'           => wp_create_nonce( 'personio-integration-dismiss-nonce' ),
				'dismiss_url_nonce'       => wp_create_nonce( 'personio-integration-dismiss-url' ),
				'run_import_nonce'        => wp_create_nonce( 'personio-run-import' ),
				'get_import_nonce'        => wp_create_nonce( 'personio-get-import-info' ),
				'label_import_is_running' => __( 'Import is running', 'personio-integration-light' ),
				'logo_img'                => Helper::get_logo_img(),
				'url_example'             => Helper::get_personio_url_example(),
				'url_positions_backend'   => PersonioPosition::get_instance()->get_link(),
				'url_positions_frontend'  => get_post_type_archive_link( PersonioPosition::get_instance()->get_name() ),
			)
		);
	}

	/**
	 * Add the dialog-scripts and -styles.
	 *
	 * @return void
	 */
	public function add_dialog(): void {
		// embed necessary scripts for dialog.
		$path = Helper::get_plugin_path() . 'lib/threadi/wp-easy-dialog/';
		$url  = Helper::get_plugin_url() . 'lib/threadi/wp-easy-dialog/';

		// bail if path does not exist.
		if ( ! file_exists( $path ) ) {
			return;
		}

		// embed the dialog-components JS-script.
		$script_asset_path = $path . 'build/index.asset.php';
		$script_asset      = require $script_asset_path;
		wp_enqueue_script(
			'wp-easy-dialog',
			$url . 'build/index.js',
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		// embed the dialog-components CSS-script.
		$admin_css      = $url . 'build/style-index.css';
		$admin_css_path = $path . 'build/style-index.css';
		wp_enqueue_style(
			'wp-easy-dialog',
			$admin_css,
			array( 'wp-components' ),
			filemtime( $admin_css_path )
		);
	}

	/**
	 * Show hint for our Pro-version.
	 *
	 * Every $hint should use %1$s where the link to the Pro-info-page is set.
	 *
	 * @param string $hint The individual hint to show before pro-hint.
	 * @return void
	 */
	public function show_pro_hint( string $hint ): void {
		echo '<p class="personio-pro-hint">' . sprintf( wp_kses_post( $hint ), '<a href="' . esc_url( Helper::get_pro_url() ) . '" target="_blank">Personio Integration Pro (opens new window)</a>' ) . '</p>';
	}

	/**
	 * Check for supported PageBuilder and show hint if Pro-version would support it.
	 *
	 * @return void
	 */
	public function check_for_pagebuilder(): void {
		// get transients object.
		$transients_obj = Transients::get_instance();

		// bail if our Pro-plugin is active.
		// TODO move to Pro.
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
			$transient_obj = $transients_obj->add();
			$transient_obj->set_name( 'personio_integration_divi' );
			/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
			$transient_obj->set_message( sprintf( __( 'We realized that you are using Divi - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Divi.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( 'personio_integration_divi' )->delete();
		}

		/**
		 * Check for Elementor.
		 */
		if ( did_action( 'elementor/loaded' ) ) {
			$transient_obj = $transients_obj->add();
			$transient_obj->set_name( 'personio_integration_divi' );
			/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
			$transient_obj->set_message( sprintf( __( 'We realized that you are using Elementor - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Elementor.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( 'personio_integration_elementor' )->delete();
		}

		/**
		 * Check for WPBakery.
		 */
		if ( Helper::is_plugin_active( 'js_composer/js_composer.php' ) ) {
			$transient_obj = $transients_obj->add();
			$transient_obj->set_name( 'personio_integration_wpbakery' );
			/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
			$transient_obj->set_message( sprintf( __( 'We realized that you are using WPBakery - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in WPBakery.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( 'personio_integration_wpbakery' )->delete();
		}

		/**
		 * Check for Beaver Builder.
		 */
		if ( class_exists( 'FLBuilder' ) ) {
			$transient_obj = $transients_obj->add();
			$transient_obj->set_name( 'personio_integration_beaver' );
			/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
			$transient_obj->set_message( sprintf( __( 'We realized that you are using Beaver Builder - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Beaver Builder.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( 'personio_integration_beaver' )->delete();
		}

		/**
		 * Check for SiteOrigin.
		 */
		if ( Helper::is_plugin_active( 'siteorigin-panels/siteorigin-panels.php' ) ) {
			$transient_obj = $transients_obj->add();
			$transient_obj->set_name( 'personio_integration_siteorigin' );
			/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
			$transient_obj->set_message( sprintf( __( 'We realized that you are using Site Origin - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Site Origin.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( 'personio_integration_siteorigin' )->delete();
		}

		/**
		 * Check for Themify.
		 */
		if ( Helper::is_plugin_active( 'themify-builder/themify-builder.php' ) ) {
			$transient_obj = $transients_obj->add();
			$transient_obj->set_name( 'personio_integration_siteorigin' );
			/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
			$transient_obj->set_message( sprintf( __( 'We realized that you are using Themify - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Themify.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( 'personio_integration_themify' )->delete();
		}

		/**
		 * Check for Avada.
		 */
		if ( Helper::is_plugin_active( 'fusion-builder/fusion-builder.php' ) ) {
			$transient_obj = $transients_obj->add();
			$transient_obj->set_name( 'personio_integration_avada' );
			/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
			$transient_obj->set_message( sprintf( __( 'We realized that you are using Avada - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Avada.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'success' );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( 'personio_integration_avada' )->delete();
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
			'__return_true'
		);
	}

	/**
	 * Forward user to settings-page.
	 *
	 * @return void
	 */
	public function forward_importer_to_settings(): void {
		wp_safe_redirect( Helper::get_settings_url( 'import' ) );
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
		$message = sprintf(
			/* translators: %1$s is replaced with "string", %2$s is replaced with "string" */
			__(
				'<strong>The import has been manually run.</strong> Please check the list of positions <a href="%1$s">in backend</a> and <a href="%2$s">frontend</a>.',
				'personio-integration-light'
			),
			esc_url( PersonioPosition::get_instance()->get_link() ),
			get_post_type_archive_link( WP_PERSONIO_INTEGRATION_MAIN_CPT )
		);
		$transient_obj = Transients::get_instance()->add();
		$transient_obj->set_name( 'personio_integration_import_run' );
		$transient_obj->set_message( $message );
		$transient_obj->set_type( 'hint' );
		$transient_obj->save();

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
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_import_canceled' );
			$transient_obj->set_message( __( '<strong>The running import has been canceled.</strong> Click on the following button to start a new import. If it also takes to long please check your hosting logfiles for possible restrictions mentioned there.', 'personio-integration-light' ) . ' <br><br><a href="' . self::get_import_url() . '" class="button button-primary personio-integration-import-hint">' . __( 'Run import', 'personio-integration-light' ) . '</a>' );
			$transient_obj->set_type( 'error' );
			$transient_obj->save();
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

		$transient_obj = Transients::get_instance()->add();

		// do not delete positions if import is running atm.
		if ( 0 === absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) ) ) {
			// delete positions.
			$user = wp_get_current_user();
			( new cli() )->delete_positions( array( 'Delete all positions button', ' by ' . esc_html( $user->display_name ) ) );

			// add hint..
			$transient_obj->set_name( 'personio_integration_delete_run' );
			$transient_obj->set_message( __( '<strong>The positions have been deleted.</strong> You can run the import anytime again to import positions.', 'personio-integration-light' ) );
		} else {
			$transient_obj->set_name( 'personio_integration_could_not_delete' );
			$transient_obj->set_message( __( '<strong>The positions could not been deleted.</strong> An import is actual running.', 'personio-integration-light' ) );
		}
		$transient_obj->set_type( 'success' );
		$transient_obj->save();

		// redirect user.
		wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
	}

	/**
	 * Check plugin configuration and enable hints if necessary.
	 *
	 * @return void
	 */
	public function check_config(): void {
		// bail if setup is not completed.
		if ( ! Setup::get_instance()->is_completed() ) {
			return;
		}

		$transients_obj = Transients::get_instance();
		if ( ! Helper::is_personio_url_set() ) {
			$transient_obj = $transients_obj->add();
			$transient_obj->set_dismissible_days( 60 );
			$transient_obj->set_name( 'personio_integration_no_url_set' );
			/* translators: %1$s will be replaced by the URL to the settings-page. */
			$transient_obj->set_message( sprintf( __( 'The specification of your Personio URL is still pending. <strong>Add it now on the <a href="%1$s">settings page</a></strong>.', 'personio-integration-light' ), esc_url( Helper::get_settings_url() ) ) );
			$transient_obj->set_type( 'hint' );
			$transient_obj->set_hide_on( array( Helper::get_settings_url() ) );
			$transient_obj->save();
		} elseif ( absint( get_option( 'personioIntegrationPositionCount', 0 ) ) > 0 ) {
			$transient_obj = $transients_obj->add();
			$transient_obj->set_dismissible_days( 60 );
			$transient_obj->set_name( 'personio_integration_limit_hint' );
			/* translators: %1$s will be replaced by the URL to the Pro-information-page. */
			$transient_obj->set_message( sprintf( __( 'The list of positions is limited to a maximum of 10 entries in the frontend. With <a href="%1$s">Personio Integration Pro version</a>, any number of positions can be displayed.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'error' );
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( 'personio_integration_no_url_set' )->delete();
			$transients_obj->get_transient_by_name( 'personio_integration_limit_hint' )->delete();
		}
	}

	/**
	 * Show hint to review our plugin every 90 days.
	 *
	 * @return void
	 */
	public function show_review_hint(): void {
		$install_date = absint( get_option( 'personioIntegrationLightInstallDate', 0 ) );
		if ( $install_date > 0 ) {
			if ( time() > strtotime( '+90 days', $install_date ) ) {
				for ( $d = 2;$d < 10;$d++ ) {
					if ( time() > strtotime( '+' . ( $d * 90 ) . ' days', $install_date ) ) {
						Transients::get_instance()->get_transient_by_name( 'personio_integration_admin_show_review_hint' )->delete_dismiss();
					}
				}
				$transient_obj = Transients::get_instance()->add();
				$transient_obj->set_dismissible_days( 90 );
				$transient_obj->set_name( 'personio_integration_admin_show_review_hint' );
				$transient_obj->set_message(
					sprintf(
					/* translators: %1$s is replaced with "string" */
						sprintf( __( 'Your use the WordPress-plugin Personio Integration Light since more than %1$d days. Do you like it? Feel free to <a href="https://wordpress.org/plugins/personio-integration-light/#reviews" target="_blank">leave us a review (opens new window)</a>.', 'personio-integration-light' ), ( absint( get_option( 'personioIntegrationLightInstallDate', 1 ) - time() ) / 60 / 60 / 24 ) ) . ' <span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span>',
						Helper::get_pro_url()
					)
				);
				$transient_obj->set_type( 'error' );
				$transient_obj->save();
			} else {
				Transients::get_instance()->get_transient_by_name( 'personio_integration_admin_show_review_hint' )->delete();
			}
		}
	}

	/**
	 * Start Import via AJAX.
	 *
	 * @return void
	 * @noinspection PhpUnused
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function run_import(): void {
		// check nonce.
		check_ajax_referer( 'personio-run-import', 'nonce' );

		// run import.
		new Import();

		// return nothing.
		wp_die();
	}

	/**
	 * Return state of the actual running import.
	 *
	 * Format: Step;MaxSteps;Running;Errors
	 *
	 * @return void
	 * @noinspection PhpUnused
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function get_import_info(): void {
		// check nonce.
		check_ajax_referer( 'personio-get-import-info', 'nonce' );

		// return actual and max count of import steps.
		echo absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_COUNT, 0 ) ) . ';' . absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_MAX ) ) . ';' . absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) ) . ';' . wp_json_encode( get_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, array() ) );

		// return nothing else.
		wp_die();
	}
}
