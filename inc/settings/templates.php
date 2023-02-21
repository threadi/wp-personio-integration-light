<?php

use personioIntegration\helper;

/**
 * Add tab in settings.
 *
 * @param $tab
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_settings_add_template_tab( $tab ): void
{
    // check active tab
    $activeClass = '';
    if( $tab === 'template' ) $activeClass = ' nav-tab-active';

    // output tab
    echo '<a href="?post_type='.WP_PERSONIO_INTEGRATION_CPT.'&page=personioPositions&tab=template" class="nav-tab'.esc_attr($activeClass).'">'.__('Templates', 'wp-personio-integration').'</a>';
}
add_action( 'personio_integration_settings_add_tab', 'personio_integration_settings_add_template_tab', 10, 1 );

/**
 * Page for template-settings.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_menu_content_template()
{
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // show errors
    settings_errors();

    ?>
    <form method="POST" action="options.php">
        <?php
        settings_fields( 'personioIntegrationPositionsTemplates' );
        do_settings_sections( 'personioIntegrationPositionsTemplates' );
        submit_button();
        ?>
    </form>
    <?php
}
add_action('personio_integration_settings_template_page', 'personio_integration_admin_add_menu_content_template' );

/**
 * Get template options
 *
 * @return void
 */
function personio_integration_admin_add_settings_template()
{
    /**
     * List section
     */
    add_settings_section(
        'settings_section_template_list',
        __('List View', 'wp-personio-integration'),
        '__return_true',
        'personioIntegrationPositionsTemplates'
    );

    // enable filter on list-view
    add_settings_field(
        'personioIntegrationEnableFilter',
        __('Enable filter on list-view', 'wp-personio-integration'),
        'personio_integration_admin_checkbox_field',
        'personioIntegrationPositionsTemplates',
        'settings_section_template_list',
        [
            'label_for' => 'personioIntegrationEnableFilter',
            'fieldId' => 'personioIntegrationEnableFilter',
            'readonly' => !helper::is_personioUrl_set()
        ]
    );
    register_setting('personioIntegrationPositionsTemplates', 'personioIntegrationEnableFilter', ['sanitize_callback' => 'personio_integration_admin_validateCheckBox']);

    // set default filter
    add_settings_field(
        'personioIntegrationTemplateFilter',
        __('Available filter', 'wp-personio-integration'),
        'personio_integration_admin_multiselect_field',
        'personioIntegrationPositionsTemplates',
        'settings_section_template_list',
        [
            'label_for' => 'personioIntegrationTemplateFilter',
            'fieldId' => 'personioIntegrationTemplateFilter',
            'values' => personio_integration_admin_categories_labels(),
            'description' => __('Mark multiple default filter for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'wp-personio-integration'),
            'readonly' => !helper::is_personioUrl_set(),
            'sanitizeFunction' => 'personio_integration_admin_sanitize_settings_field_array'
        ]
    );
    register_setting('personioIntegrationPositionsTemplates', 'personioIntegrationTemplateFilter');

    // set filter-type
    add_settings_field(
        'personioIntegrationFilterType',
        __('Choose filter-type', 'wp-personio-integration'),
        'personio_integration_admin_select_field',
        'personioIntegrationPositionsTemplates',
        'settings_section_template_list',
        [
            'label_for' => 'personioIntegrationFilterType',
            'fieldId' => 'personioIntegrationFilterType',
            'values' => helper::get_filter_types(),
            'description' => __('This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'wp-personio-integration'),
            'readonly' => !helper::is_personioUrl_set()
        ]
    );
    register_setting('personioIntegrationPositionsTemplates', 'personioIntegrationFilterType');

    // content templates for list-view
    add_settings_field(
        'personioIntegrationTemplateContentList',
        __('Templates for list-view of positions', 'wp-personio-integration'),
        'personio_integration_admin_multiselect_field',
        'personioIntegrationPositionsTemplates',
        'settings_section_template_list',
        [
            'label_for' => 'personioIntegrationTemplateContentList',
            'fieldId' => 'personioIntegrationTemplateContentList',
            'values' => personio_integration_admin_template_labels(),
            'description' => __('Mark multiple default templates for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'wp-personio-integration'),
            'readonly' => !helper::is_personioUrl_set(),
            'sanitizeFunction' => 'personio_integration_admin_sanitize_settings_field_array'
        ]
    );
    register_setting('personioIntegrationPositionsTemplates', 'personioIntegrationTemplateContentList');

    // Excerpt templates for list-view
    add_settings_field(
        'personioIntegrationTemplateExcerptDefaults',
        __('Excerpt-parts for list-view of positions', 'wp-personio-integration'),
        'personio_integration_admin_multiselect_field',
        'personioIntegrationPositionsTemplates',
        'settings_section_template_list',
        [
            'label_for' => 'personioIntegrationTemplateExcerptDefaults',
            'fieldId' => 'personioIntegrationTemplateExcerptDefaults',
            'values' => personio_integration_admin_categories_labels(),
            'description' => __('Mark multiple default excerpt-parts for each frontend-output. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'wp-personio-integration'),
            'readonly' => !helper::is_personioUrl_set(),
            'sanitizeFunction' => 'personio_integration_admin_sanitize_settings_field_array'
        ]
    );
    register_setting('personioIntegrationPositionsTemplates', 'personioIntegrationTemplateExcerptDefaults');

    /**
     * List section
     */
    add_settings_section(
        'settings_section_template_detail',
        __('Detail View', 'wp-personio-integration'),
        '__return_true',
        'personioIntegrationPositionsTemplates'
    );

    // content templates for detail-view
    add_settings_field(
        'personioIntegrationTemplateContentDefaults',
        __('Templates for detail-view of single positions', 'wp-personio-integration'),
        'personio_integration_admin_multiselect_field',
        'personioIntegrationPositionsTemplates',
        'settings_section_template_detail',
        [
            'label_for' => 'personioIntegrationTemplateContentDefaults',
            'fieldId' => 'personioIntegrationTemplateContentDefaults',
            'values' => personio_integration_admin_template_labels(),
            'description' => __('Mark multiple default templates for each detail-view of single positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'wp-personio-integration'),
            'readonly' => !helper::is_personioUrl_set(),
            'sanitizeFunction' => 'personio_integration_admin_sanitize_settings_field_array'
        ]
    );
    register_setting('personioIntegrationPositionsTemplates', 'personioIntegrationTemplateContentDefaults');

    // Excerpt templates for detail
    add_settings_field(
        'personioIntegrationTemplateExcerptDetail',
        __('Excerpt-parts for detail-view of positions', 'wp-personio-integration'),
        'personio_integration_admin_multiselect_field',
        'personioIntegrationPositionsTemplates',
        'settings_section_template_detail',
        [
            'label_for' => 'personioIntegrationTemplateExcerptDetail',
            'fieldId' => 'personioIntegrationTemplateExcerptDetail',
            'values' => personio_integration_admin_categories_labels(),
            'description' => __('Mark multiple excerpt-parts for detail-view of positions. Only used if template "excerpt" is enabled for detail-view. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'wp-personio-integration'),
            'readonly' => !helper::is_personioUrl_set(),
            'sanitizeFunction' => 'personio_integration_admin_sanitize_settings_field_array'
        ]
    );
    register_setting('personioIntegrationPositionsTemplates', 'personioIntegrationTemplateExcerptDetail');

    // enable link to detail on list-view
    add_settings_field(
        'personioIntegrationEnableLinkInList',
        __('Enable link to detail on list-view', 'wp-personio-integration'),
        'personio_integration_admin_checkbox_field',
        'personioIntegrationPositionsTemplates',
        'settings_section_template_list',
        [
            'label_for' => 'personioIntegrationEnableLinkInList',
            'fieldId' => 'personioIntegrationEnableLinkInList',
            'readonly' => !helper::is_personioUrl_set()
        ]
    );
    register_setting('personioIntegrationPositionsTemplates', 'personioIntegrationEnableLinkInList', ['sanitize_callback' => 'personio_integration_admin_validateCheckBox']);

    // enable back-to-list-button in detail-view
    add_settings_field(
        'personioIntegrationTemplateBackToListButton',
        __( 'Enable back to list-link', 'wp-personio-integration' ),
        'personio_integration_admin_checkbox_field',
        'personioIntegrationPositionsTemplates',
        'settings_section_template_detail',
        [
            'label_for' => 'personioIntegrationTemplateBackToListButton',
            'fieldId' => 'personioIntegrationTemplateBackToListButton',
            'readonly' => !helper::is_personioUrl_set()
        ]
    );
    register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationTemplateBackToListButton' );

    // link-target for back-to-list-button in detail-view
    add_settings_field(
        'personioIntegrationTemplateBackToListUrl',
        __( 'URL for back to list-link', 'wp-personio-integration' ),
        'personio_integration_admin_text_field',
        'personioIntegrationPositionsTemplates',
        'settings_section_template_detail',
        [
            'label_for' => 'personioIntegrationTemplateBackToListUrl',
            'fieldId' => 'personioIntegrationTemplateBackToListUrl',
            'description' => __('If empty the link will be set to list-slug.' , 'wp-personio-integration'),
            'readonly' => !helper::is_personioUrl_set()
        ]
    );
    register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationTemplateBackToListUrl' );

    /**
     * Other section
     */
    add_settings_section(
        'settings_section_template_other',
        __('Other settings', 'wp-personio-integration'),
        '__return_true',
        'personioIntegrationPositionsTemplates'
    );

    // separator for excerpt-listing
    add_settings_field(
        'personioIntegrationTemplateExcerptSeparator',
        __('Separator for excerpt-list', 'wp-personio-integration'),
        'personio_integration_admin_text_field',
        'personioIntegrationPositionsTemplates',
        'settings_section_template_other',
        [
            'label_for' => 'personioIntegrationTemplateExcerptSeparator',
            'fieldId' => 'personioIntegrationTemplateExcerptSeparator',
            'readonly' => !helper::is_personioUrl_set()
        ]
    );
    register_setting('personioIntegrationPositionsTemplates', 'personioIntegrationTemplateExcerptSeparator');

}
add_action( 'personio_integration_settings_add_settings', 'personio_integration_admin_add_settings_template');
