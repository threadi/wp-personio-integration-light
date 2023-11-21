<?php

use personioIntegration\helper;

/**
 * Add tab in settings.
 *
 * @param $tab
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_settings_add_advanced_tab( $tab ): void
{
    // check active tab
    $activeClass = '';
    if( $tab === 'advanced' ) $activeClass = ' nav-tab-active';

    // output tab
    echo '<a href="?post_type='.WP_PERSONIO_INTEGRATION_CPT.'&page=personioPositions&tab=advanced" class="nav-tab'.esc_attr($activeClass).'">'.__('Advanced', 'personio-integration-light').'</a>';
}
add_action( 'personio_integration_settings_add_tab', 'personio_integration_settings_add_advanced_tab', 60, 1 );

/**
 * Show advanced-page.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_menu_content_advanced()
{
    // check user capabilities
    if ( ! current_user_can( 'manage_'.WP_PERSONIO_INTEGRATION_CPT ) ) {
        return;
    }

    // show errors
    settings_errors();

    ?>
    <form method="POST" action="<?php echo get_admin_url(); ?>options.php">
        <?php
        settings_fields( 'personioIntegrationPositionsAdvanced' );
        do_settings_sections( 'personioIntegrationPositionsAdvanced' );
        submit_button();
        ?>
    </form>
    <?php
}
add_action('personio_integration_settings_advanced_page', 'personio_integration_admin_add_menu_content_advanced' );

/**
 * Get advanced options.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_settings_advanced()
{
    /**
     * Advanced section
     */
    add_settings_section(
        'settings_section_advanced',
        __( 'Advanced Settings', 'personio-integration-light' ),
        '__return_true',
        'personioIntegrationPositionsAdvanced'
    );

    add_settings_field(
        'personioIntegration_advanced_pro_hint',
        '',
        'personio_integration_admin_advanced_pro_hint',
        'personioIntegrationPositionsAdvanced',
        'settings_section_advanced',
        [
            'label_for' => 'personioIntegration_advanced_pro_hint',
            'fieldId' => 'personioIntegration_advanced_pro_hint',
        ]
    );

    // add more advanced settings
    do_action('personio_integration_advanced_settings');

    // extend search.
    add_settings_field(
        'personioIntegrationExtendSearch',
        __( 'Note the position-keywords in search in frontend', 'personio-integration-light' ),
        'personio_integration_admin_checkbox_field',
        'personioIntegrationPositionsAdvanced',
        'settings_section_advanced',
        [
            'label_for' => 'personioIntegrationExtendSearch',
            'fieldId' => 'personioIntegrationExtendSearch',
            'readonly' => !helper::is_personioUrl_set()
        ]
    );
    register_setting( 'personioIntegrationPositionsAdvanced', 'personioIntegrationExtendSearch', ['sanitize_callback' => 'personio_integration_admin_validateCheckBox'] );

    // max age for log-entries
    add_settings_field(
        'personioIntegrationMaxAgeLogEntries',
        __( 'max. Age for log entries in days', 'personio-integration-light' ),
        'personio_integration_admin_number_field',
        'personioIntegrationPositionsAdvanced',
        'settings_section_advanced',
        [
            'label_for' => 'personioIntegrationMaxAgeLogEntries',
            'fieldId' => 'personioIntegrationMaxAgeLogEntries',
            'readonly' => !helper::is_personioUrl_set()
        ]
    );
    register_setting( 'personioIntegrationPositionsAdvanced', 'personioIntegrationMaxAgeLogEntries' );

    // Personio URL Timeout
    add_settings_field(
        'personioIntegrationUrlTimeout',
        __( 'Timeout for URL-request in Seconds', 'personio-integration-light' ),
        'personio_integration_admin_number_field',
        'personioIntegrationPositionsAdvanced',
        'settings_section_advanced',
        [
            'label_for' => 'personioIntegrationUrlTimeout',
            'fieldId' => 'personioIntegrationUrlTimeout',
            'readonly' => !helper::is_personioUrl_set()
        ]
    );
    register_setting( 'personioIntegrationPositionsAdvanced', 'personioIntegrationUrlTimeout', ['sanitize_callback' => 'personio_integration_admin_validatePersonioURLTimeout'] );

    // delete all data on uninstall
    add_settings_field(
        'personioIntegrationDeleteOnUninstall',
        __( 'Delete all imported data on uninstall', 'personio-integration-light' ),
        'personio_integration_admin_checkbox_field',
        'personioIntegrationPositionsAdvanced',
        'settings_section_advanced',
        [
            'label_for' => 'personioIntegrationDeleteOnUninstall',
            'fieldId' => 'personioIntegrationDeleteOnUninstall',
            'readonly' => !helper::is_personioUrl_set()
        ]
    );
    register_setting( 'personioIntegrationPositionsAdvanced', 'personioIntegrationDeleteOnUninstall', ['sanitize_callback' => 'personio_integration_admin_validateCheckBox'] );

    // enable debug-Mode
    add_settings_field(
        'personioIntegration_debug',
        __( 'Debug-Mode', 'personio-integration-light' ),
        'personio_integration_admin_checkbox_field',
        'personioIntegrationPositionsAdvanced',
        'settings_section_advanced',
        [
            'label_for' => 'personioIntegration_debug',
            'fieldId' => 'personioIntegration_debug',
            'description' => __('If activated, the import will be executed every time even if there are no changes.', 'personio-integration-light'),
            'readonly' => !helper::is_personioUrl_set()
        ]
    );
    register_setting( 'personioIntegrationPositionsAdvanced', 'personioIntegration_debug', ['sanitize_callback' => 'personio_integration_admin_validateCheckBox'] );
}
add_action( 'personio_integration_settings_add_settings', 'personio_integration_admin_add_settings_advanced');

/**
 * Add pro hint via settings-field for better position in list.
 *
 * @return void
 */
function personio_integration_admin_advanced_pro_hint() {
    // pro hint
    /* translators: %1$s is replaced with "string" */
    do_action('personio_integration_admin_show_pro_hint', __('With %s you get more advanced options, e.g. to change the URL of archives with positions.', 'personio-integration-light'));
}

/**
 * Valide the timeout
 *
 * @param $value
 * @return int
 * @noinspection PhpUnused
 */
function personio_integration_admin_validatePersonioURLTimeout( $value ): int
{
    $value = absint($value);
    if( $value == 0 ) {
        add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __('A timeout must have a value greater than 0.', 'personio-integration-light'), 'error' );
    }
    return $value;
}
