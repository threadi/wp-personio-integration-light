<?php
/**
 * File for handling tasks in wp-admin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Intro;
use PersonioIntegrationLight\Plugin\Setup;
use PersonioIntegrationLight\Plugin\Transients;
use WP_Admin_Bar;

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
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_custom_toolbar' ), 100 );

		// register our own importer in backend.
		add_action( 'admin_init', array( $this, 'add_importer' ) );
		add_action( 'load-importer-personio-integration-importer', array( $this, 'forward_importer_to_settings' ) );

		// add admin_actions.
		add_action( 'admin_action_personioPositionsImport', array( $this, 'import_positions' ) );
		add_action( 'admin_action_personioPositionsReImport', array( $this, 'reimport_positions' ) );
		add_action( 'admin_action_personioPositionsCancelImport', array( $this, 'cancel_import' ) );
		add_action( 'admin_action_personioPositionsDelete', array( $this, 'delete_positions' ) );
		add_action( 'admin_action_personio_integration_log_export', array( $this, 'export_log' ) );
		add_action( 'admin_action_personio_integration_log_empty', array( $this, 'empty_log' ) );
	}

	/**
	 * Add own CSS and JS for backend.
	 *
	 * @return void
	 */
	public function add_styles_and_js(): void {
		// admin-specific styles.
		wp_enqueue_style(
			'personio-integration-admin',
			Helper::get_plugin_url() . 'admin/styles.css',
			array(),
			Helper::get_file_version( Helper::get_plugin_path() . 'admin/styles.css' ),
		);

		// backend-JS.
		wp_enqueue_script(
			'personio-integration-admin',
			Helper::get_plugin_url() . 'admin/js.js',
			array( 'jquery', 'wp-easy-dialog' ),
			Helper::get_file_version( Helper::get_plugin_path() . 'admin/js.js' ),
			true
		);

		// add php-vars to our js-script.
		wp_localize_script(
			'personio-integration-admin',
			'personioIntegrationLightJsVars',
			array(
				'ajax_url'                           => admin_url( 'admin-ajax.php' ),
				'rest_personioposition_delete'       => rest_url( 'wp/v2/personioposition' ),
				'pro_url'                            => Helper::get_pro_url(),
				'review_url'                         => Helper::get_review_url(),
				'dismiss_nonce'                      => wp_create_nonce( 'personio-integration-dismiss-nonce' ),
				'dismiss_url_nonce'                  => wp_create_nonce( 'personio-integration-dismiss-url' ),
				'run_import_nonce'                   => wp_create_nonce( 'personio-run-import' ),
				'get_import_nonce'                   => wp_create_nonce( 'personio-get-import-info' ),
				'get_import_dialog_nonce'            => wp_create_nonce( 'personio-import-dialog' ),
				'get_deletion_nonce'                 => wp_create_nonce( 'personio-get-deletion-info' ),
				'settings_import_file_nonce'         => wp_create_nonce( 'personio-integration-settings-import-file' ),
				'extension_state_nonce'              => wp_create_nonce( 'personio-integration-extension-state' ),
				'rest_nonce'                         => wp_create_nonce( 'wp_rest' ),
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
				'import_title_error'                 => __( 'Error during import of positions', 'personio-integration-light' ),
				'import_txt_error'                   => __( '<strong>Error during import of positions.</strong> The following error occurred:', 'personio-integration-light' ),
				'lbl_ok'                             => __( 'OK', 'personio-integration-light' ),
				'title_import_success'               => __( 'Positions has been imported', 'personio-integration-light' ),
				/* translators: %1$s is replaced with "string", %2$s is replaced with "string" */
				'txt_import_success'                 => sprintf( __( '<strong>The import has been manually run.</strong> Please check the list of positions <a href="%1$s">in backend</a> and <a href="%2$s">frontend</a>.', 'personio-integration-light' ), esc_url( PersonioPosition::get_instance()->get_link() ), esc_url( get_post_type_archive_link( PersonioPosition::get_instance()->get_name() ) ) ),
				'title_settings_import_file_missing' => __( 'Import file missing', 'personio-integration-light' ),
				'title_settings_import_file_result'  => __( 'Import file uploaded', 'personio-integration-light' ),
				'text_settings_import_file_missing'  => __( 'Please choose a file for the import.', 'personio-integration-light' ),
				'title_delete_progress'              => __( 'Deletion in progress', 'personio-integration-light' ),
				'title_deletion_success'             => __( 'Deletion endet', 'personio-integration-light' ),
				'txt_deletion_success'               => __( '<strong>All positions have been deleted from WordPress.</strong><br>They are still available in Personio.<br>You can re-import the positions at any time.', 'personio-integration-light' ),
				'title_error'                        => __( 'Error', 'personio-integration-light' ),
				'txt_error'                          => __( '<strong>An unexpected error occurred.</strong> The error was:', 'personio-integration-light' ),
			)
		);

		// add php-vars to our js-script for possible import-errors.
		wp_localize_script(
			'personio-integration-admin',
			'personioIntegrationLightJsImportErrors',
			array(
				/* translators: %1$s will be replaced by the URL for the Pro-plugin */
				'Request Timeout'  => sprintf( __( '<u>Request Timeout</u> - The import apparently took too long to be completed.<br>Use <a href="%1$s">Personio Integration Pro</a> to use partial imports without timeouts.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ),
				/* translators: %1$s will be replaced by the URL for the Pro-plugin */
				'Gateway Time-out' => sprintf( __( '<u>Gateway Timeout</u> - The import apparently took too long to be completed.<br>Use <a href="%1$s">Personio Integration Pro</a> to use partial imports without timeouts.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ),
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
		$path = Helper::get_plugin_path() . 'vendor/threadi/wp-easy-dialog/';
		$url  = Helper::get_plugin_url() . 'vendor/threadi/wp-easy-dialog/';

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
		wp_safe_redirect( Helper::get_settings_url( 'personioPositions', 'import' ) );
		exit;
	}

	/**
	 * Start import manually via request.
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
		exit;
	}

	/**
	 * Start import manually via request.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function reimport_positions(): void {
		check_ajax_referer( 'personio-integration-re-import', 'nonce' );

		// delete positions.
		PersonioPosition::get_instance()->delete_positions();

		// run import.
		$imports_obj = Imports::get_instance();
		$imports_obj->run();

		// redirect user.
		wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
		exit;
	}

	/**
	 * Set marker to cancel running import.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
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
		exit;
	}

	/**
	 * Delete all positions manually.
	 *
	 * @return void
	 */
	public function delete_positions(): void {
		check_ajax_referer( 'wp-personio-integration-delete', 'nonce' );

		// delete positions.
		PersonioPosition::get_instance()->delete_positions();

		// redirect user.
		wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
		exit;
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
			$transient_obj->set_message( sprintf( __( 'The specification of your Personio URL is still pending. <strong>Add it now on the <a href="%1$s">settings page</a>.</strong>', 'personio-integration-light' ), esc_url( Helper::get_settings_url() ) ) );
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
		} elseif ( Positions::get_instance()->get_positions_count() > 10 && absint( get_option( 'personioIntegrationPositionCount', 0 ) ) > 0 && ! apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
			$transient_obj = $transients_obj->add();
			$transient_obj->set_dismissible_days( 60 );
			$transient_obj->set_name( 'personio_integration_limit_hint' );
			/* translators: %1$s will be replaced by the URL to the Pro-information-page. */
			$transient_obj->set_message( sprintf( __( 'The list of positions is limited to a maximum of 10 entries in the frontend. With <a href="%1$s">Personio Integration Pro (opens new window)</a> any number of positions can be displayed - and you get a large number of additional features.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) );
			$transient_obj->set_type( 'error' );
			$transient_obj->set_hide_on(
				array(
					get_admin_url() . 'edit.php?post_type=' . PersonioPosition::get_instance()->get_name() . '&page=personioPositionsLicense',
				)
			);
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
		// bail if transient is already dismissed.
		if ( Transients::get_instance()->get_transient_by_name( 'personio_integration_admin_show_review_hint' )->is_dismissed() ) {
			return;
		}

		$install_date = absint( get_option( 'personioIntegrationLightInstallDate' ) );
		if ( $install_date > 0 ) {
			if ( time() > strtotime( '+90 days', $install_date ) ) {
				$transient_obj = Transients::get_instance()->add();
				$transient_obj->set_dismissible_days( 90 );
				$transient_obj->set_name( 'personio_integration_admin_show_review_hint' );
				$transient_obj->set_message(
					/* translators: %1$d is replaced with a day-count, %2$s will be replaced with the review-URL */
					sprintf( __( 'Your use the WordPress-plugin Personio Integration Light since more than %1$d days. Do you like it? Feel free to <a href="%2$s" target="_blank">leave us a review (opens new window)</a>.', 'personio-integration-light' ), ( absint( get_option( 'personioIntegrationLightInstallDate', 1 ) - time() ) / 60 / 60 / 24 ), esc_url( Helper::get_review_url() ) ) . ' <span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span>',
				);
				$transient_obj->set_type( 'info' );
				$transient_obj->save();
			}
			return;
		}
		Transients::get_instance()->get_transient_by_name( 'personio_integration_admin_show_review_hint' )->delete();
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

		// enable intros if set as parameter.
		$import_intro = filter_input( INPUT_GET, 'import_intro', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! empty( $import_intro ) ) {
			Intro::get_instance()->add_js();
			$classes .= ' personio-integration-import-intro';
		}
		// enable intros if set as parameter.
		$template_intro = filter_input( INPUT_GET, 'template_intro', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! empty( $template_intro ) ) {
			Intro::get_instance()->add_js();
			$classes .= ' personio-integration-template-intro';
		}

		// return resulting classes.
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
					if ( $position_obj->is_visible() ) {
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
						$admin_bar->add_menu(
							array(
								'id'     => 'personio-integration-detail',
								'parent' => null,
								'group'  => null,
								'title'  => __( 'Not visible in frontend', 'personio-integration-light' ),
							)
						);
					}
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

	/**
	 * Show help page for Pro-plugin.
	 *
	 * It is also visible before license is validated.
	 *
	 * @return void
	 */
	public function show_help_page(): void {
		// add the boxes.
		$this->add_meta_boxes_for_help();

		// output.
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
		</div>
		<div id="poststuff">
			<?php
			do_meta_boxes( get_current_screen(), 'normal', null );
			?>
		</div>
		<?php
	}

	/**
	 * Add settings-page for the plugin if setup has been completed.
	 *
	 * @return void
	 */
	public function add_menu(): void {
		if ( Setup::get_instance()->is_completed() ) {
			// add menu entry for applications (with hint to pro).
			$false = false;
			/**
			 * Hide the additional the sort column which is only filled in Pro.
			 *
			 * @since 3.0.0 Available since 3.0.0
			 *
			 * @param array $false Set true to hide the buttons.
			 */
			if ( ! apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
				add_submenu_page(
					PersonioPosition::get_instance()->get_link( true ),
					__( 'Personio Integration Light', 'personio-integration-light' ) . ' ' . __( 'Settings', 'personio-integration-light' ),
					__( 'Applications', 'personio-integration-light' ),
					'manage_' . PersonioPosition::get_instance()->get_name(),
					'#',
					false,
					2
				);
			}

			// add help link.
			add_submenu_page(
				PersonioPosition::get_instance()->get_link( true ),
				__( 'Need help with Personio Integration?', 'personio-integration-light' ),
				'<span class="disable">' . __( 'Help', 'personio-integration-light' ) . '</span>',
				'read_' . PersonioPosition::get_instance()->get_name(),
				'personioPositionsHelp',
				array( $this, 'show_help_page' ),
				9
			);
		}
	}

	/**
	 * Define not post-type- or taxonomy-assigned boxes in wp-admin for our plugin.
	 *
	 * @return void
	 */
	public function add_meta_boxes_for_help(): void {
		// box for tours in help.
		add_meta_box(
			Helper::get_plugin_name() . '-tours',
			__( 'Tours', 'personio-integration-light' ),
			array( $this, 'help_page_tours_box' ),
			get_current_screen(),
			'normal'
		);

		// box for links in help.
		add_meta_box(
			Helper::get_plugin_name() . '-links',
			__( 'Get help', 'personio-integration-light' ),
			array( $this, 'help_page_link_box' ),
			get_current_screen(),
			'normal'
		);

		/**
		 * Add additional boxes for help page.
		 */
		do_action( 'personio_integration_help_page' );
	}

	/**
	 * Show tasks the user could use.
	 *
	 * @return void
	 */
	public function help_page_tours_box(): void {
		// button to show import options as intro.
		$dialog_import = array(
			'title'   => __( 'How to change the import of positions?', 'personio-integration-light' ),
			'texts'   => array(
				'<p>' . __( 'Click on the button bellow to start a journey through the settings to import positions.', 'personio-integration-light' ) . '</p>',
			),
			'buttons' => array(
				array(
					'action'  => 'location.href="' . esc_url( add_query_arg( array( 'import_intro' => 1 ), Helper::get_settings_url( 'personioPositions', 'import' ) ) ) . '";',
					'variant' => 'primary',
					'text'    => __( 'Start', 'personio-integration-light' ),
				),
				array(
					'action'  => 'closeDialog();',
					'variant' => 'secondary',
					'text'    => __( 'Cancel', 'personio-integration-light' ),
				),
			),
		);
		?>
		<p><a href="#" class="button button-primary wp-easy-dialog" data-dialog="<?php echo esc_attr( wp_json_encode( $dialog_import ) ); ?>"><?php echo esc_html__( 'How to change the import of positions?', 'personio-integration-light' ); ?></a></p>
																							<?php

																							// button to show template options as intro.
																							$dialog_templates = array(
																								'title'   => __( 'How to configure templates?', 'personio-integration-light' ),
																								'texts'   => array(
																									'<p>' . __( 'Click on the button bellow to start a journey through the settings for templates.', 'personio-integration-light' ) . '</p>',
																								),
																								'buttons' => array(
																									array(
																										'action'  => 'location.href="' . esc_url( add_query_arg( array( 'template_intro' => 1 ), Helper::get_settings_url( 'personioPositions', 'templates' ) ) ) . '";',
																										'variant' => 'primary',
																										'text'    => __( 'Start', 'personio-integration-light' ),
																									),
																									array(
																										'action'  => 'closeDialog();',
																										'variant' => 'secondary',
																										'text'    => __( 'Cancel', 'personio-integration-light' ),
																									),
																								),
																							);
																							?>
		<p><a href="#" class="button button-primary wp-easy-dialog" data-dialog="<?php echo esc_attr( wp_json_encode( $dialog_templates ) ); ?>"><?php echo esc_html__( 'How to configure templates?', 'personio-integration-light' ); ?></a></p>
																							<?php

																							// button to show how to get the pro-version.
																							$false = false;
																							/**
																							 * Hide the additional the sort column which is only filled in Pro.
																							 *
																							 * @since 3.0.0 Available since 3.0.0
																							 *
																							 * @param array $false Set true to hide the buttons.
																							 *
																							 * @noinspection PhpConditionAlreadyCheckedInspection
																							 */
																							if ( ! apply_filters( 'personio_integration_hide_pro_hints', $false ) ) {
																								$dialog_templates = array(
																									'title'   => __( 'How to get the Pro-version?', 'personio-integration-light' ),
																									'texts'   => array(
																										/* translators: %1$s will be replaced by the Pro-plugin-URL */
																										'<p>' . sprintf( __( 'If you want to use the Pro-version of our plugin, check out <a href="%1$s" target="_blank">our website (opens new window)</a> and fill out the order form there.', 'personio-integration-light' ), esc_url( Helper::get_pro_url() ) ) . '</p>',
																									),
																									'buttons' => array(
																										array(
																											'action'  => 'closeDialog();',
																											'variant' => 'primary',
																											'text'    => __( 'OK', 'personio-integration-light' ),
																										),
																									),
																								);
																								?>
			<p><a href="#" class="button button-primary wp-easy-dialog" data-dialog="<?php echo esc_attr( wp_json_encode( $dialog_templates ) ); ?>"><?php echo esc_html__( 'How to get the Pro-version?', 'personio-integration-light' ); ?></a></p>
																								<?php
																							}

																							/**
																							 * Add additional helper tasks via hook.
																							 *
																							 * @since 3.0.0 Available since 3.0.0.
																							 */
																							do_action( 'personio_integration_help_tours' );
	}

	/**
	 * Show links for help and more information.
	 *
	 * @return void
	 */
	public function help_page_link_box(): void {
		?>
		<p>
			<?php
			/* translators: %1$s will be replaced by the support-forum-URL. */
			echo wp_kses_post( sprintf( __( 'If you have any questions do not hesitate to ask them in our <a href="%1$s" target="_blank">forum (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_plugin_support_url() ) ) );
			?>
		</p>
		<p>
			<?php
			/* translators: %1$s and %2$s will be replaced by external URLs. */
			echo wp_kses_post( sprintf( __( 'Check out our repository on <a href="%1$s" target="_blank">github</a>. There you will also find <a href="%2$s" target="_blank">some documentations (opens new window)</a>.', 'personio-integration-light' ), esc_url( 'https://github.com/threadi/wp-personio-integration-light' ), esc_url( 'https://github.com/threadi/wp-personio-integration-light/tree/master/doc' ) ) );
			?>
			</p>
		<?php
	}

	/**
	 * Export log as CSV.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function export_log(): void {
		// check the nonce.
		check_admin_referer( 'personio-integration-log-export', 'nonce' );

		// get entries.
		$log     = new Log();
		$entries = $log->get_entries();

		// create filename for JSON-download-file.
		$filename = gmdate( 'YmdHi' ) . '_' . get_option( 'blogname' ) . '_Personio_Integration_Light_Logs.csv';
		/**
		 * File the filename for CSV-download.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param string $filename The generated filename.
		 */
		$filename = apply_filters( 'personio_integration_log_export_filename', $filename );

		// set header for response as CSV-download.
		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment; filename=' . sanitize_file_name( $filename ) );

		// generate CSV-output.
		$fp       = fopen( 'php://output', 'w' );
		$head_row = $entries[0];
		fputcsv( $fp, array_keys( $head_row ) );
		foreach ( $entries as $data ) {
			fputcsv( $fp, $data );
		}

		// do nothing more.
		exit;
	}

	/**
	 * Empty the log per request.
	 *
	 * @return void
	 */
	public function empty_log(): void {
		global $wpdb;

		// check the nonce.
		check_admin_referer( 'personio-integration-log-empty', 'nonce' );

		// empty the table.
		$wpdb->query( 'TRUNCATE TABLE `' . $wpdb->prefix . 'personio_import_logs`' );

		// redirect user.
		wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
		exit;
	}
}
