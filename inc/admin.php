<?php
/**
 * File for functions to run in wp-admin only.
 *
 * @package personio-integration-light
 */

use App\helper;
use personioIntegration\Import;
use personioIntegration\Position;
use personioIntegration\Positions;

/**
 * Generate transient-based messages in backend.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_notices(): void {
	// show transients.
	foreach ( apply_filters( 'personio_integration_admin_transients', WP_PERSONIO_INTEGRATION_TRANSIENTS ) as $transient => $settings ) {
		if ( false !== get_transient( $transient ) ) {
			// marker to show the transient.
			$show = true;

			// check if this transient is dismissed to some time.
			if ( ! helper::is_transient_not_dismissed( $transient ) ) {
				continue;
			}

			// hide on specific pages.
			$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			if ( isset( $settings['options']['hideOnPages'] ) && in_array( $page, $settings['options']['hideOnPages'], true ) ) {
				$show = false;
			}

			// hide if other transient is also visible.
			if ( isset( $settings['options']['hideIfTransients'] ) ) {
				foreach ( $settings['options']['hideIfTransients'] as $transient_to_check ) {
					if ( false !== get_transient( $transient_to_check ) ) {
						$show = false;
					}
				}
			}

			// hide on settings-tab.
			$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
			if ( isset( $settings['options']['hideOnSettingsTabs'] ) && in_array( $tab, $settings['options']['hideOnSettingsTabs'], true ) ) {
				$show = false;
			}

			// get the translated content.
			$settings['content'] = helper::get_admin_transient_content( $transient );

			// do not show anything on empty content.
			if ( empty( $settings['content'] ) ) {
				$show = false;
			}

			// show it.
			if ( $show ) {
				?>
				<div class="wp-personio-integration-transient updated <?php echo esc_attr( $settings['type'] ); ?>" data-dismissible="<?php echo esc_attr( $transient ); ?>-14">
					<?php echo wp_kses_post( $settings['content'] ); ?>
					<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo esc_html__( 'Dismiss this notice.', 'personio-integration-light' ); ?></span></button>
				</div>
				<?php

				// remove the transient.
				delete_transient( $transient );

				// disable plugin if option is set.
				if ( ! empty( $settings['options']['disable_plugin'] ) ) {
					deactivate_plugins( plugin_basename( WP_PERSONIO_INTEGRATION_PLUGIN ) );
				}
			}
		}
	}
}
add_action( 'admin_notices', 'personio_integration_admin_notices' );

/**
 * Activate transient-based hint if configuration does not contain the necessary URL.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_check_config(): void {
	if ( ! helper::is_personio_url_set() ) {
		set_transient( 'personio_integration_no_url_set', 1, 60 );
	} elseif ( get_option( 'personioIntegrationPositionCount', 0 ) > 0 ) {
		set_transient( 'personio_integration_limit_hint', 0 );
	}
}
add_action( 'admin_init', 'personio_integration_admin_check_config' );

/**
 * Show hint to review our plugin every 90 days.
 *
 * @return void
 */
function personio_integration_admin_show_review_hint(): void {
	$install_date = absint( get_option( 'personioIntegrationLightInstallDate', 0 ) );
	if ( $install_date > 0 ) {
		if ( time() > strtotime( '+90 days', $install_date ) ) {
			for ( $d = 2;$d < 10;$d++ ) {
				if ( time() > strtotime( '+' . ( $d * 90 ) . ' days', $install_date ) ) {
					delete_option( 'pi-dismissed-' . md5( 'personio_integration_admin_show_review_hint' ) );
				}
			}
			set_transient( 'personio_integration_admin_show_review_hint', 1 );
		} else {
			delete_transient( 'personio_integration_admin_show_review_hint' );
		}
	}
}
add_action( 'admin_init', 'personio_integration_admin_show_review_hint' );

/**
 * Activate transient-based hint if configuration is set but no positions are imported until now.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_check_position_count(): void {
	if ( helper::is_personio_url_set() && 0 === absint( get_option( 'personioIntegrationPositionCount', 0 ) ) ) {
		set_transient( 'personio_integration_no_position_imported', 1, 60 );
	}
}
add_action( 'admin_init', 'personio_integration_admin_check_position_count' );

/**
 * Handles Ajax request to persist notices dismissal.
 * Uses check_ajax_referer to verify nonce.
 *
 * TODO use transients-object instead of this.
 *
 * @return void
 * @noinspection PhpUnused
 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
 */
function personio_integration_admin_dismiss(): void {
	// get values.
	$option_name        = isset( $_POST['option_name'] ) ? sanitize_text_field( wp_unslash( $_POST['option_name'] ) ) : false;
	$dismissible_length = isset( $_POST['dismissible_length'] ) ? sanitize_text_field( wp_unslash( $_POST['dismissible_length'] ) ) : 14;

	if ( 'forever' !== $dismissible_length ) {
		// If $dismissible_length is not an integer default to 14.
		$dismissible_length = ( 0 === absint( $dismissible_length ) ) ? 14 : $dismissible_length;
		$dismissible_length = strtotime( absint( $dismissible_length ) . ' days' );
	}

	// check nonce.
	check_ajax_referer( 'wp-dismiss-notice', 'nonce' );

	// save value.
	update_site_option( 'pi-dismissed-' . md5( $option_name ), $dismissible_length );

	// return nothing.
	wp_die();
}

/**
 * Start Import via AJAX.
 *
 * @return void
 * @noinspection PhpUnused
 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
 */
function personio_integration_admin_run_import(): void {
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
 * @return void
 * @noinspection PhpUnused
 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
 */
function personio_integration_admin_get_import_info(): void {
	// check nonce.
	check_ajax_referer( 'personio-get-import-info', 'nonce' );

	// return actual and max count of import steps.
	echo absint( get_option( WP_PERSONIO_OPTION_COUNT, 0 ) ) . ';' . absint( get_option( WP_PERSONIO_OPTION_MAX ) ) . ';' . absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) );

	// return nothing else.
	wp_die();
}

/**
 * Add AJAX-endpoints.
 */
add_action(
	'admin_init',
	function () {
		add_action( 'wp_ajax_nopriv_dismiss_admin_notice', 'personio_integration_admin_dismiss' );
		add_action( 'wp_ajax_dismiss_admin_notice', 'personio_integration_admin_dismiss' );

		add_action( 'wp_ajax_nopriv_personio_run_import', 'personio_integration_admin_run_import' );
		add_action( 'wp_ajax_personio_run_import', 'personio_integration_admin_run_import' );

		add_action( 'wp_ajax_nopriv_personio_get_import_info', 'personio_integration_admin_get_import_info' );
		add_action( 'wp_ajax_personio_get_import_info', 'personio_integration_admin_get_import_info' );
	}
);

/**
 * Update slugs on request.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_update_slugs(): void {
	if ( false !== get_transient( 'personio_integration_update_slugs' ) ) {
		flush_rewrite_rules();
		delete_transient( 'personio_integration_update_slugs' );
	}
}
add_action( 'wp', 'personio_integration_update_slugs' );

/**
 * Allow our own capability to save settings.
 */
function personio_integration_admin_allow_save_settings(): void {
	$settings_pages = array(
		'personioIntegrationPositions',
		'personioIntegrationPositionsTemplates',
		'personioIntegrationPositionsImportExport',
		'personioIntegrationPositionsAdvanced',
	);
	foreach ( apply_filters( 'personio_integration_admin_settings_pages', $settings_pages ) as $settings_page ) {
		add_filter(
			'option_page_capability_' . $settings_page,
			function () {
				return 'manage_' . WP_PERSONIO_INTEGRATION_CPT;
			},
			10,
			0
		);
	}
}
add_action( 'admin_init', 'personio_integration_admin_allow_save_settings' );

/**
 * Create our own schedules via click.
 *
 * @return void
 */
function personio_integration_create_schedules(): void {
	check_ajax_referer( 'wp-personio-integration-create-schedules', 'nonce' );

	// check if import-schedule does already exist.
	helper::set_import_schedule();

	// redirect user.
	wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
}
add_action( 'admin_action_personioPositionsCreateSchedules', 'personio_integration_create_schedules' );
