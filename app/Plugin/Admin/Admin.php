<?php
/**
 * File for handling tasks in wp-admin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Cli;
use PersonioIntegrationLight\Plugin\Setup;
use PersonioIntegrationLight\Plugin\Transients;
use WP_Admin_Bar;

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	private function __clone() {}

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

		// initialize the Site Health support.
		Site_Health::get_instance()->init();

		// show hint for Pro-version.
		add_action( 'personio_integration_admin_show_pro_hint', array( $this, 'show_pro_hint' ) );
		add_filter( 'admin_body_class', array( $this, 'add_body_classes' ) );

		// add our own checks in wp-admin.
		add_action( 'admin_init', array( $this, 'check_config' ) );
		add_action( 'admin_init', array( $this, 'show_review_hint' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_custom_toolbar' ), 100 );

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
		// admin-specific styles.
		wp_enqueue_style(
			'personio_integration-admin-css',
			Helper::get_plugin_url() . 'admin/styles.css',
			array(),
			Helper::get_file_version( Helper::get_plugin_path() . 'admin/styles.css' ),
		);

		// admin- and backend-styles for attribute-type-output.
		wp_enqueue_style(
			'personio_integration-styles',
			Helper::get_plugin_url() . 'css/styles.css',
			array(),
			Helper::get_file_version( Helper::get_plugin_path() . 'css/styles.css' )
		);

		// backend-JS.
		wp_enqueue_script(
			'personio_integration-admin-js',
			Helper::get_plugin_url() . 'admin/js.js',
			array( 'jquery', 'wp-easy-dialog' ),
			Helper::get_file_version( Helper::get_plugin_path() . 'admin/js.js' ),
			true
		);

		// add php-vars to our js-script.
		wp_localize_script(
			'personio_integration-admin-js',
			'personioIntegrationLightJsVars',
			array(
				'ajax_url'                           => admin_url( 'admin-ajax.php' ),
				'pro_url'                            => Helper::get_pro_url(),
				'dismiss_nonce'                      => wp_create_nonce( 'personio-integration-dismiss-nonce' ),
				'dismiss_url_nonce'                  => wp_create_nonce( 'personio-integration-dismiss-url' ),
				'run_import_nonce'                   => wp_create_nonce( 'personio-run-import' ),
				'get_import_nonce'                   => wp_create_nonce( 'personio-get-import-info' ),
				'settings_import_file_nonce'         => wp_create_nonce( 'personio-integration-settings-import-file' ),
				'label_import_is_running'            => __( 'Import is running', 'personio-integration-light' ),
				'logo_img'                           => Helper::get_logo_img(),
				'url_example'                        => Helper::get_personio_url_example(),
				'title_rate_us'                      => __( 'Rate us', 'personio-integration-light' ),
				'title_run_import'                   => __( 'Run import', 'personio-integration-light' ),
				'title_get_pro'                      => __( 'Get Personio Integration Pro', 'personio-integration-light' ),
				'title_import_progress'              => __( 'Import in progress', 'personio-integration-light' ),
				'title_delete_positions'             => __( 'Delete all positions', 'personio-integration-light' ),
				'txt_delete_positions'               => __( '<strong>Are you sure you want to delete all positions in WordPress?</strong><br>Hint: the positions in Personio are not influenced.', 'personio-integration-light' ),
				'lbl_yes'                            => __( 'Yes', 'personio-integration-light' ),
				'lbl_no'                             => __( 'No', 'personio-integration-light' ),
				'title_pro_hint'                     => __( 'Use applications with Personio Integration Pro', 'personio-integration-light' ),
				'txt_pro_hint'                       => __( 'With <strong>Personio Integration Pro</strong> you will be able to capture applications within your website.<br>Several form templates are available for this purpose, which you can also customize individually.<br>Incoming applications are automatically transferred to your Personio account via the Personio API.', 'personio-integration-light' ),
				'lbl_get_more_information'           => __( 'Get more information', 'personio-integration-light' ),
				'lbl_look_later'                     => __( 'I\'ll look later', 'personio-integration-light' ),
				'title_error'                        => __( 'Error during import of positions', 'personio-integration-light' ),
				'txt_error'                          => __( '<strong>Error during import of positions.</strong> The following error occurred:', 'personio-integration-light' ),
				'lbl_ok'                             => __( 'OK', 'personio-integration-light' ),
				'title_import_success'               => __( 'Positions has been imported', 'personio-integration-light' ),
				/* translators: %1$s is replaced with "string", %2$s is replaced with "string" */
				'txt_import_success'                 => sprintf( __( '<strong>The import has been manually run.</strong> Please check the list of positions <a href="%1$s">in backend</a> and <a href="%2$s">frontend</a>.', 'personio-integration-light' ), esc_url( PersonioPosition::get_instance()->get_link() ), esc_url( get_post_type_archive_link( PersonioPosition::get_instance()->get_name() ) ) ),
				'title_settings_import_file_missing' => __( 'Import file missing', 'personio-integration-light' ),
				'title_settings_import_file_result'  => __( 'Import file uploaded', 'personio-integration-light' ),
				'text_settings_import_file_missing'  => __( 'Please choose a file for the import.', 'personio-integration-light' ),
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

		// bail if script does not exist.
		if ( ! file_exists( $script_asset_path ) ) {
			return;
		}

		// embed script.
		$script_asset = require $script_asset_path;
		wp_enqueue_script(
			'wp-easy-dialog',
			$url . 'build/index.js',
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		// embed the dialog-components CSS-file.
		$admin_css      = $url . 'build/style-index.css';
		$admin_css_path = $path . 'build/style-index.css';
		wp_enqueue_style(
			'wp-easy-dialog',
			$admin_css,
			array( 'wp-components' ),
			Helper::get_file_version( $admin_css_path )
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
		$text = '<a href="' . esc_url( Helper::get_pro_url() ) . '" target="_blank">Personio Integration Pro (opens new window)</a>';
		/**
		 * Filter the pro hint text.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $text The text.
		 */
		echo '<p class="personio-pro-hint">' . wp_kses_post( sprintf( $hint, apply_filters( 'personio_integration_pro_hint_text', $text ) ) ) . '</p>';
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
		$imports_obj = Imports::get_instance();
		$imports_obj->run();

		// add hint.
		$message = sprintf(
			/* translators: %1$s is replaced with "string", %2$s is replaced with "string" */
			__(
				'<strong>The import has been manually run.</strong> Please check the list of positions <a href="%1$s">in backend</a> and <a href="%2$s">frontend</a>.',
				'personio-integration-light'
			),
			esc_url( PersonioPosition::get_instance()->get_link() ),
			get_post_type_archive_link( PersonioPosition::get_instance()->get_name() )
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
			$transient_obj->set_message( __( '<strong>The running import has been canceled.</strong> Click on the following button to start a new import. If it also takes to long please check your hosting logfiles for possible restrictions mentioned there.', 'personio-integration-light' ) . ' <br><br><a href="' . esc_url( Helper::get_import_url() ) . '" class="button button-primary personio-integration-import-hint">' . __( 'Run import', 'personio-integration-light' ) . '</a>' );
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

		$false = false;

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
			/**
			 * Hide the additional buttons for reviews or pro-version.
			 *
			 * @since 3.0.0 Available since 3.0.0
			 *
			 * @param array $false Set true to hide the buttons.
			 */
		} elseif ( absint( get_option( 'personioIntegrationPositionCount', 0 ) ) > 0 && ! apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			$transient_obj = $transients_obj->add();
			$transient_obj->set_dismissible_days( 60 );
			$transient_obj->set_name( 'personio_integration_limit_hint' );
			/* translators: %1$s will be replaced by the URL to the Pro-information-page. */
			$transient_obj->set_message( sprintf( __( 'The list of positions is limited to a maximum of 10 entries in the frontend. With <a href="%1$s">Personio Integration Pro</a> any number of positions can be displayed - and you get a large number of additional features.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
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
					/* translators: %1$d is replaced with a day-count, %2$s will be replaced with the review-URL */
					sprintf( __( 'Your use the WordPress-plugin Personio Integration Light since more than %1$d days. Do you like it? Feel free to <a href="%2$s" target="_blank">leave us a review (opens new window)</a>.', 'personio-integration-light' ), ( absint( get_option( 'personioIntegrationLightInstallDate', 1 ) - time() ) / 60 / 60 / 24 ), esc_url( Helper::get_review_url() ) ) . ' <span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span>',
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
		$imports_obj = Imports::get_instance();
		$imports_obj->run();

		// return nothing.
		wp_die();
	}

	/**
	 * Return state of the actual running import.
	 *
	 * Format: Step;MaxSteps;Running;Errors
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function get_import_info(): void {
		// check nonce.
		check_ajax_referer( 'personio-get-import-info', 'nonce' );

		// return actual and max count of import steps.
		wp_send_json(
			array(
				absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_COUNT, 0 ) ),
				absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_MAX ) ),
				absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) ),
				wp_json_encode( get_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, array() ) ),
			)
		);
	}

	/**
	 * Add custom classes to body-tag.
	 *
	 * @param string $classes List of classes.
	 *
	 * @return string
	 */
	public function add_body_classes( string $classes ): string {
		$false = false;

		/**
		 * Hide the additional buttons for reviews or pro-version.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 *
		 * @param array $false Set true to hide the buttons.
		 */
		if ( apply_filters( 'personio_integration_hide_pro_hints', $false ) || ! Helper::is_personio_url_set() ) {
			$classes .= ' personio-integration-hide-buttons';
		}
		return $classes;
	}

	/**
	 * Add link in toolbar to list of positions.
	 * Only if Personio URL is given and list-view is not disabled.
	 *
	 * @param WP_Admin_Bar $admin_bar The object of the Admin-Bar.
	 * @return void
	 */
	public function add_custom_toolbar( WP_Admin_Bar $admin_bar ): void {
		if ( Helper::is_personio_url_set() && 0 === absint( get_option( 'personioIntegrationDisableListSlug' ) ) ) {
			$admin_bar->add_menu(
				array(
					'id'     => PersonioPosition::get_instance()->get_name() . '-archive',
					'parent' => 'site-name',
					'title'  => __( 'Personio Positions', 'personio-integration-light' ),
					'href'   => get_post_type_archive_link( PersonioPosition::get_instance()->get_name() ),
				)
			);

			// add links in admin-bar in backend.
			if ( is_admin() ) {
				// add link to view position in frontend if one is called in backend.
				$post_id = absint( filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT ) );
				if ( $post_id > 0 && PersonioPosition::get_instance()->get_name() === get_post_type( $post_id ) ) {
					$position_obj = Positions::get_instance()->get_position( $post_id );
					$admin_bar->add_menu(
						array(
							'id'     => 'personio-integration-detail',
							'parent' => null,
							'group'  => null,
							'title'  => __( 'View Position in frontend', 'personio-integration-light' ),
							'href'   => $position_obj->get_link(),
						)
					);
				} else {
					$post_type = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
					if ( ! empty( $post_type ) && PersonioPosition::get_instance()->get_name() === $post_type ) {
						$admin_bar->add_menu(
							array(
								'id'     => 'personio-integration-list',
								'parent' => null,
								'group'  => null,
								'title'  => __( 'View Positions in frontend', 'personio-integration-light' ),
								'href'   => get_post_type_archive_link( PersonioPosition::get_instance()->get_name() ),
							)
						);
					}
				}
			}
		}
	}
}
