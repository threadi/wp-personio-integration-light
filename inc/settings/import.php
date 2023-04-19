<?php

use personioIntegration\cli;
use personioIntegration\helper;
use personioIntegration\Import;

/**
 * Add tab in settings.
 *
 * @param $tab
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_settings_add_importexport_tab( $tab ): void
{
    // check active tab
    $activeClass = '';
    if( $tab === 'importexport' ) $activeClass = ' nav-tab-active';

    // output tab
    echo '<a href="?post_type='.WP_PERSONIO_INTEGRATION_CPT.'&page=personioPositions&tab=importexport" class="nav-tab'.esc_attr($activeClass).'">'.__('Import', 'wp-personio-integration').'</a>';
}
add_action( 'personio_integration_settings_add_tab', 'personio_integration_settings_add_importexport_tab', 20, 1 );

/**
 * Show import-export-page.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_menu_content_importexport(): void
{
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) || !helper::is_personioUrl_set() ) {
        return;
    }

    // show errors
    settings_errors();

    ?>
    <form method="POST" action="options.php">
        <?php
        settings_fields( 'personioIntegrationPositionsImportExport' );
        do_settings_sections( 'personioIntegrationPositionsImportExport' );
        submit_button();
        ?>
    </form>
    <?php
}
add_action('personio_integration_settings_importexport_page', 'personio_integration_admin_add_menu_content_importexport' );

/**
 * Get import/export options.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_settings_importexport(): void
{
    /**
     * Import Section
     */
    add_settings_section(
        'settings_section_import',
        __('Import of positions from Personio', 'wp-personio-integration'),
        '__return_true',
        'personioIntegrationPositionsImportExport'
    );

    // import now button
    add_settings_field(
        'personioIntegrationImportNow',
        __( 'Start import now', 'wp-personio-integration' ),
        'personio_integration_admin_start_import_now',
        'personioIntegrationPositionsImportExport',
        'settings_section_import',
        [
            'label_for' => 'personioIntegrationImportNow',
            'fieldId' => 'personioIntegrationImportNow',
        ]
    );

    // delete all positions button
    add_settings_field(
        'personioIntegrationDeleteNow',
        __( 'Delete positions', 'wp-personio-integration' ),
        'personio_integration_admin_delete_positions_now',
        'personioIntegrationPositionsImportExport',
        'settings_section_import',
        [
            'label_for' => 'personioIntegrationDeleteNow',
            'fieldId' => 'personioIntegrationDeleteNow',
        ]
    );

    // enable automatic import
    add_settings_field(
        'personioIntegrationEnablePositionSchedule',
        __( 'Enable automatic import', 'wp-personio-integration' ),
        'personio_integration_admin_checkbox_field',
        'personioIntegrationPositionsImportExport',
        'settings_section_import',
        [
            'label_for' => 'personioIntegrationEnablePositionSchedule',
            'fieldId' => 'personioIntegrationEnablePositionSchedule',
            'description' => __('If enabled, new positions stored in Personio will be retrieved automatically daily.<br>If disabled, new positions are retrieved manually only.', 'wp-personio-integration'),
            'readonly' => !helper::is_personioUrl_set(),
            /* translators: %1$s is replaced with "string" */
            'pro_hint' => __('Use more import options with the %s. Among other things, you get the possibility to change the time interval for imports and partial imports of very large position lists.', 'wp-personio-integration')
        ]
    );
    register_setting( 'personioIntegrationPositionsImportExport', 'personioIntegrationEnablePositionSchedule', ['sanitize_callback' => 'personio_integration_admin_validateCheckBox'] );

    // add additional settings
    do_action('personio_integration_import_settings');
}
add_action( 'personio_integration_settings_add_settings', 'personio_integration_admin_add_settings_importexport');

/**
 * Start import manually.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_action_manual_import(): void {
    check_ajax_referer( 'wp-personio-integration-import', 'nonce' );

    // run import
    new Import();

    // add hint
    set_transient('personio_integration_import_run', 1);

    // remove other hint
    delete_transient('personio_integration_no_position_imported');

    // redirect user
    wp_redirect($_SERVER['HTTP_REFERER']);
}
add_action( 'admin_action_personioPositionsImport', 'personio_integration_admin_action_manual_import');

/**
 * Set marker to cancel running import.
 *
 * @return void
 */
function personio_integration_admin_action_cancel_import(): void {
    check_ajax_referer( 'wp-personio-integration-cancel-import', 'nonce' );

    // check if import as running
    if( get_option(WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) > 0 ) {
        // remove running marker
        update_option(WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0);

        // add hint
        set_transient('personio_integration_import_canceled', 1);
    }

    // redirect user
    wp_redirect($_SERVER['HTTP_REFERER']);
}
add_action( 'admin_action_personioPositionsCancelImport', 'personio_integration_admin_action_cancel_import');

/**
 * Delete all positions manually.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_action_delete_positions(): void {
    check_ajax_referer( 'wp-personio-integration-delete', 'nonce' );

    // do not delete positions if import is running atm
    if( get_option(WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) == 0 ) {
        // delete positions
        (new cli())->deletePositions();

        // add hint
        set_transient('personio_integration_delete_run', 1);
    }
    else {
        set_transient('personio_integration_could_not_delete', 1);
    }

    // redirect user
    wp_redirect($_SERVER['HTTP_REFERER']);
}
add_action( 'admin_action_personioPositionsDelete', 'personio_integration_admin_action_delete_positions');

/**
 * Add button to start import now on settings-page.
 *
 * @return void
 */
function personio_integration_admin_start_import_now(): void {
    $importRunning = absint(get_option(WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0));
    if( $importRunning == 0 ) {
        ?>
            <p><a href="<?php echo esc_url(helper::get_import_url()); ?>" class="button button-primary personio-integration-import-hint"><?php _e('Run import', 'wp-personio-integration'); ?></a></p>
            <p><i><?php _e('Hint', 'wp-personio-integration'); ?>:</i> <?php _e('Performing the import could take a few minutes. If a timeout occurs, a manual import is not possible this way. Then the automatic import should be used.', 'wp-personio-integration'); ?></p>
        <?php
    }
    else {
        ?>
            <p><?php _e('The import is already running. Please wait some moments.', 'wp-personio-integration'); ?></p>
        <?php
        // show import-break button if import is running min. 1 hour
        if( $importRunning > 1 ) {
        if( $importRunning+60*60 < time() ) {
                $url = add_query_arg(
                    [
                        'action' => 'personioPositionsCancelImport',
                        'nonce' => wp_create_nonce( 'wp-personio-integration-cancel-import' )
                    ],
                    get_admin_url() . 'admin.php'
                );
                ?><p><a href="<?php echo esc_url($url); ?>" class="button button-primary"><?php _e('Cancel running import', 'wp-personio-integration'); ?></a></p><?php
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
    if( helper::is_personioUrl_set() && get_option( 'personioIntegrationPositionCount', 0 ) > 0 ) {
        ?>
        <p><a href="<?php echo esc_url(helper::get_delete_url()); ?>" class="button button-primary"><?php _e('Delete all positions', 'wp-personio-integration'); ?></a></p>
        <p><i><?php _e('Hint', 'wp-personio-integration'); ?>:</i> <?php _e('Removes all actual imported positions.', 'wp-personio-integration'); ?></p>
        <?php
    }
    else {
        ?><p><?php _e('There are currently no imported positions.', 'wp-personio-integration'); ?></p><?php
    }
}