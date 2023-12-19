<?php
/**
 * File for import settings.
 *
 * @package personio-integration-light
 */

use personioIntegration\cli;
use personioIntegration\helper;
use personioIntegration\Import;

/**
 * Add tab in settings.
 *
 * @param string $tab The name of the active tab.
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_settings_add_import_tab( string $tab ): void {
	// check active tab.
	$active_class = '';
	if ( 'import' === $tab ) {
		$active_class = ' nav-tab-active';
	}

	// define URL for import-settings.
	$url = add_query_arg(
		array(
			'post_type' => WP_PERSONIO_INTEGRATION_CPT,
			'page'      => 'personioPositions',
			'tab'       => 'import',
		),
		''
	);

	// output tab.
	echo '<a href="' . esc_url( $url ) . '" class="nav-tab' . esc_attr( $active_class ) . '">' . esc_html__( 'Import', 'personio-integration-light' ) . '</a>';
}
add_action( 'personio_integration_settings_add_tab', 'personio_integration_settings_add_import_tab', 20 );

/**
 * Show import-export-page.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_menu_content_import(): void {
	// check user capabilities.
	if ( ! current_user_can( 'manage_' . WP_PERSONIO_INTEGRATION_CPT ) || ! helper::is_personioUrl_set() ) {
		return;
	}

	// show errors.
	settings_errors();

	?>
	<form method="POST" action="<?php echo esc_url( get_admin_url() ); ?>options.php">
		<?php
		settings_fields( 'personioIntegrationPositionsImportExport' );
		do_settings_sections( 'personioIntegrationPositionsImportExport' );
		submit_button();
		?>
	</form>
	<?php
}
add_action( 'personio_integration_settings_import_page', 'personio_integration_admin_add_menu_content_import' );

/**
 * Get import/export options.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_settings_import(): void {
	/**
	 * Import Section.
	 */
	add_settings_section(
		'settings_section_import',
		__( 'Import of positions from Personio', 'personio-integration-light' ),
		'__return_true',
		'personioIntegrationPositionsImportExport'
	);

	// import now button.
	add_settings_field(
		'personioIntegrationImportNow',
		__( 'Start import now', 'personio-integration-light' ),
		'personio_integration_admin_start_import_now',
		'personioIntegrationPositionsImportExport',
		'settings_section_import',
		array(
			'label_for' => 'personioIntegrationImportNow',
			'fieldId'   => 'personioIntegrationImportNow',
		)
	);

	// delete all positions button.
	add_settings_field(
		'personioIntegrationDeleteNow',
		__( 'Delete positions', 'personio-integration-light' ),
		'personio_integration_admin_delete_positions_now',
		'personioIntegrationPositionsImportExport',
		'settings_section_import',
		array(
			'label_for' => 'personioIntegrationDeleteNow',
			'fieldId'   => 'personioIntegrationDeleteNow',
		)
	);

	// enable automatic import.
	add_settings_field(
		'personioIntegrationEnablePositionSchedule',
		__( 'Enable automatic import', 'personio-integration-light' ),
		'personio_integration_admin_checkbox_field',
		'personioIntegrationPositionsImportExport',
		'settings_section_import',
		array(
			'label_for'   => 'personioIntegrationEnablePositionSchedule',
			'fieldId'     => 'personioIntegrationEnablePositionSchedule',
			'description' => __( 'If enabled, new positions stored in Personio will be retrieved automatically daily.<br>If disabled, new positions are retrieved manually only.', 'personio-integration-light' ),
			'readonly'    => ! helper::is_personioUrl_set(),
			/* translators: %1$s is replaced with "string" */
			'pro_hint'    => __( 'Use more import options with the %s. Among other things, you get the possibility to change the time interval for imports and partial imports of very large position lists.', 'personio-integration-light' ),
		)
	);
	register_setting( 'personioIntegrationPositionsImportExport', 'personioIntegrationEnablePositionSchedule', array( 'type' => 'integer' ) );

	// add additional settings.
	do_action( 'personio_integration_import_settings' );
}
add_action( 'personio_integration_settings_add_settings', 'personio_integration_admin_add_settings_import' );

/**
 * Start import manually.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_action_manual_import(): void {
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
add_action( 'admin_action_personioPositionsImport', 'personio_integration_admin_action_manual_import' );

/**
 * Set marker to cancel running import.
 *
 * @return void
 */
function personio_integration_admin_action_cancel_import(): void {
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
add_action( 'admin_action_personioPositionsCancelImport', 'personio_integration_admin_action_cancel_import' );

/**
 * Delete all positions manually.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_action_delete_positions(): void {
	check_ajax_referer( 'wp-personio-integration-delete', 'nonce' );

	// do not delete positions if import is running atm.
	if ( 0 === absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) ) ) {
		// delete positions.
		$user = wp_get_current_user();
		( new cli() )->deletePositions( array( 'Delete all positions button', ' by ' . $user->display_name ) );

		// add hint..
		set_transient( 'personio_integration_delete_run', 1 );
	} else {
		set_transient( 'personio_integration_could_not_delete', 1 );
	}

	// redirect user.
	wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
}
add_action( 'admin_action_personioPositionsDelete', 'personio_integration_admin_action_delete_positions' );

/**
 * Add button to start import now on settings-page.
 *
 * @return void
 */
function personio_integration_admin_start_import_now(): void {
	$import_is_running = absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) );
	if ( 0 === $import_is_running ) {
		?>
			<p><a href="<?php echo esc_url( helper::get_import_url() ); ?>" class="button button-primary personio-integration-import-hint"><?php echo esc_html__( 'Run import', 'personio-integration-light' ); ?></a></p>
			<p><i><?php echo esc_html__( 'Hint', 'personio-integration-light' ); ?>:</i> <?php echo esc_html__( 'Performing the import could take a few minutes. If a timeout occurs, a manual import is not possible this way. Then the automatic import should be used.', 'personio-integration-light' ); ?></p>
		<?php
	} else {
		?>
			<p><?php echo esc_html__( 'The import is already running. Please wait some moments.', 'personio-integration-light' ); ?></p>
		<?php
		// show import-break button if import is running min. 1 hour.
		if ( 1 < $import_is_running ) {
			if ( $import_is_running + 60 * 60 < time() ) {
				$url = add_query_arg(
					array(
						'action' => 'personioPositionsCancelImport',
						'nonce'  => wp_create_nonce( 'wp-personio-integration-cancel-import' ),
					),
					get_admin_url() . 'admin.php'
				);
				?>
				<p><a href="<?php echo esc_url( $url ); ?>" class="button button-primary"><?php echo esc_html__( 'Cancel running import', 'personio-integration-light' ); ?></a></p>
										<?php
			}
		}
	}
}

/**
 * Add button to delete all positions.
 *
 * @return void
 */
function personio_integration_admin_delete_positions_now(): void {
	if ( helper::is_personioUrl_set() && get_option( 'personioIntegrationPositionCount', 0 ) > 0 ) {
		?>
		<p><a href="<?php echo esc_url( helper::get_delete_url() ); ?>" class="button button-primary"><?php echo esc_html__( 'Delete all positions', 'personio-integration-light' ); ?></a></p>
		<p><i><?php echo esc_html__( 'Hint', 'personio-integration-light' ); ?>:</i> <?php echo esc_html__( 'Removes all actual imported positions.', 'personio-integration-light' ); ?></p>
		<?php
	} else {
		?>
		<p><?php echo esc_html__( 'There are currently no imported positions.', 'personio-integration-light' ); ?></p>
					<?php
	}
}
