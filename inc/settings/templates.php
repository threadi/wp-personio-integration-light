<?php
/**
 * File to define template settings.
 *
 * @package personio-integration-light
 */

use App\PersonioIntegration\helper;

/**
 * Add tab in settings.
 *
 * @param string $tab The name of the active tab.
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_settings_add_template_tab( string $tab ): void {
	// check active tab.
	$active_class = '';
	if ( 'template' === $tab ) {
		$active_class = ' nav-tab-active';
	}

	// define URL for import-settings.
	$url = add_query_arg(
		array(
			'post_type' => WP_PERSONIO_INTEGRATION_CPT,
			'page'      => 'personioPositions',
			'tab'       => 'template',
		),
		''
	);

	// output tab.
	echo '<a href="' . esc_url( $url ) . '" class="nav-tab' . esc_attr( $active_class ) . '">' . esc_html__( 'Templates', 'personio-integration-light' ) . '</a>';
}
add_action( 'personio_integration_settings_add_tab', 'personio_integration_settings_add_template_tab' );

/**
 * Page for template-settings.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_menu_content_template(): void {
	// check user capabilities.
	if ( ! current_user_can( 'manage_' . WP_PERSONIO_INTEGRATION_CPT ) ) {
		return;
	}

	// show errors.
	settings_errors();

	?>
	<form method="POST" action="<?php echo esc_url( get_admin_url() ); ?>options.php">
		<?php
		settings_fields( 'personioIntegrationPositionsTemplates' );
		do_settings_sections( 'personioIntegrationPositionsTemplates' );
		submit_button();
		?>
	</form>
	<?php
}
add_action( 'personio_integration_settings_template_page', 'personio_integration_admin_add_menu_content_template' );

/**
 * Get template options
 *
 * @return void
 */
function personio_integration_admin_add_settings_template(): void {
	/**
	 * List section.
	 */
	add_settings_section(
		'settings_section_template_list',
		__( 'List View', 'personio-integration-light' ),
		'__return_true',
		'personioIntegrationPositionsTemplates'
	);

	// enable filter on list-view.
	add_settings_field(
		'personioIntegrationEnableFilter',
		__( 'Enable filter on list-view', 'personio-integration-light' ),
		'personio_integration_admin_checkbox_field',
		'personioIntegrationPositionsTemplates',
		'settings_section_template_list',
		array(
			'label_for' => 'personioIntegrationEnableFilter',
			'fieldId'   => 'personioIntegrationEnableFilter',
			'readonly'  => ! helper::is_personio_url_set(),
		)
	);
	register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationEnableFilter', array( 'type' => 'integer' ) );

	// set default filter.
	add_settings_field(
		'personioIntegrationTemplateFilter',
		__( 'Available filter for details', 'personio-integration-light' ),
		'personio_integration_admin_multiselect_field',
		'personioIntegrationPositionsTemplates',
		'settings_section_template_list',
		array(
			'label_for'        => 'personioIntegrationTemplateFilter',
			'fieldId'          => 'personioIntegrationTemplateFilter',
			'values'           => apply_filters( 'personio_integration_settings_get_list', personio_integration_admin_categories_labels(), get_option( 'personioIntegrationTemplateFilter', array() ) ),
			'description'      => __( 'Mark multiple default filter for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
			'readonly'         => ! helper::is_personio_url_set(),
			'sanitizeFunction' => 'personio_integration_admin_sanitize_settings_field_array',
			/* translators: %1$s is replaced with "string" */
			'pro_hint'         => __( 'Sort this list with %s.', 'personio-integration-light' ),
		)
	);
	register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationTemplateFilter' );

	// set filter-type.
	add_settings_field(
		'personioIntegrationFilterType',
		__( 'Choose filter-type', 'personio-integration-light' ),
		'personio_integration_admin_select_field',
		'personioIntegrationPositionsTemplates',
		'settings_section_template_list',
		array(
			'label_for'   => 'personioIntegrationFilterType',
			'fieldId'     => 'personioIntegrationFilterType',
			'values'      => helper::get_filter_types(),
			'description' => __( 'This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
			'readonly'    => ! helper::is_personio_url_set(),
		)
	);
	register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationFilterType' );

	// choose listing template.
	add_settings_field(
		'personioIntegrationTemplateContentListingTemplate',
		__( 'Choose template for listing', 'personio-integration-light' ),
		'personio_integration_admin_select_field',
		'personioIntegrationPositionsTemplates',
		'settings_section_template_list',
		array(
			'label_for'        => 'personioIntegrationTemplateContentListingTemplate',
			'fieldId'          => 'personioIntegrationTemplateContentListingTemplate',
			'values'           => personio_integration_archive_templates(),
			'description'      => __( 'Mark multiple default templates for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
			'readonly'         => ! helper::is_personio_url_set(),
			'sanitizeFunction' => 'personio_integration_admin_sanitize_settings_field_array',
		)
	);
	register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationTemplateContentListingTemplate' );

	// content templates for list-view.
	add_settings_field(
		'personioIntegrationTemplateContentList',
		__( 'Choose templates for positions in list-view', 'personio-integration-light' ),
		'personio_integration_admin_multiselect_field',
		'personioIntegrationPositionsTemplates',
		'settings_section_template_list',
		array(
			'label_for'        => 'personioIntegrationTemplateContentList',
			'fieldId'          => 'personioIntegrationTemplateContentList',
			'values'           => personio_integration_admin_template_labels(),
			'description'      => __( 'Mark multiple default templates for each list-view of positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
			'readonly'         => ! helper::is_personio_url_set(),
			'sanitizeFunction' => 'personio_integration_admin_sanitize_settings_field_array',
		)
	);
	register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationTemplateContentList' );

	// Excerpt templates for list-view.
	add_settings_field(
		'personioIntegrationTemplateExcerptDefaults',
		__( 'Choose details for positions in list-view', 'personio-integration-light' ),
		'personio_integration_admin_multiselect_field',
		'personioIntegrationPositionsTemplates',
		'settings_section_template_list',
		array(
			'label_for'        => 'personioIntegrationTemplateExcerptDefaults',
			'fieldId'          => 'personioIntegrationTemplateExcerptDefaults',
			'values'           => apply_filters( 'personio_integration_settings_get_list', personio_integration_admin_categories_labels(), get_option( 'personioIntegrationTemplateExcerptDefaults', array() ) ),
			'description'      => __( 'Mark multiple default detail-parts for each frontend-output. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
			'readonly'         => ! helper::is_personio_url_set(),
			'sanitizeFunction' => 'personio_integration_admin_sanitize_settings_field_array',
			/* translators: %1$s is replaced with "string" */
			'pro_hint'         => __( 'Sort this list with %s.', 'personio-integration-light' ),
		)
	);
	register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationTemplateExcerptDefaults' );

	/**
	 * Detail section.
	 */
	add_settings_section(
		'settings_section_template_detail',
		__( 'Single View', 'personio-integration-light' ),
		'__return_true',
		'personioIntegrationPositionsTemplates'
	);

	// content templates for detail-view.
	add_settings_field(
		'personioIntegrationTemplateContentDefaults',
		__( 'Choose templates', 'personio-integration-light' ),
		'personio_integration_admin_multiselect_field',
		'personioIntegrationPositionsTemplates',
		'settings_section_template_detail',
		array(
			'label_for'        => 'personioIntegrationTemplateContentDefaults',
			'fieldId'          => 'personioIntegrationTemplateContentDefaults',
			'values'           => personio_integration_admin_template_labels(),
			'description'      => __( 'Mark multiple default templates for each detail-view of single positions. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
			'readonly'         => ! helper::is_personio_url_set(),
			'sanitizeFunction' => 'personio_integration_admin_sanitize_settings_field_array',
		)
	);
	register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationTemplateContentDefaults' );

	// Excerpt templates in detail view.
	add_settings_field(
		'personioIntegrationTemplateExcerptDetail',
		__( 'Choose details', 'personio-integration-light' ),
		'personio_integration_admin_multiselect_field',
		'personioIntegrationPositionsTemplates',
		'settings_section_template_detail',
		array(
			'label_for'        => 'personioIntegrationTemplateExcerptDetail',
			'fieldId'          => 'personioIntegrationTemplateExcerptDetail',
			'values'           => apply_filters( 'personio_integration_settings_get_list', personio_integration_admin_categories_labels(), get_option( 'personioIntegrationTemplateExcerptDetail', array() ) ),
			'description'      => __( 'Mark multiple details for single-view of positions. Only used if template "detail" is enabled for detail-view. This setting will be overridden by individual settings on the blocks or widgets of your shortcode or PageBuilder.', 'personio-integration-light' ),
			'readonly'         => ! helper::is_personio_url_set(),
			'sanitizeFunction' => 'personio_integration_admin_sanitize_settings_field_array',
			/* translators: %1$s is replaced with "string" */
			'pro_hint'         => __( 'Sort this list with %s.', 'personio-integration-light' ),
		)
	);
	register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationTemplateExcerptDetail' );

	// templates for job description in detail view.
	add_settings_field(
		'personioIntegrationTemplateJobDescription',
		__( 'Choose job description template', 'personio-integration-light' ),
		'personio_integration_admin_select_field',
		'personioIntegrationPositionsTemplates',
		'settings_section_template_detail',
		array(
			'label_for'   => 'personioIntegrationTemplateJobDescription',
			'fieldId'     => 'personioIntegrationTemplateJobDescription',
			'values'      => personio_integration_jobdescription_templates(),
			'description' => __( 'Choose template to output each job description in detail view. You could add your own template as described in GitHub.', 'personio-integration-light' ),
			'readonly'    => ! helper::is_personio_url_set(),
		)
	);
	register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationTemplateJobDescription' );

	// enable link to detail on list-view.
	add_settings_field(
		'personioIntegrationEnableLinkInList',
		__( 'Enable link to single on list-view', 'personio-integration-light' ),
		'personio_integration_admin_checkbox_field',
		'personioIntegrationPositionsTemplates',
		'settings_section_template_list',
		array(
			'label_for' => 'personioIntegrationEnableLinkInList',
			'fieldId'   => 'personioIntegrationEnableLinkInList',
			'readonly'  => ! helper::is_personio_url_set(),
		)
	);
	register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationEnableLinkInList', array( 'type' => 'integer' ) );

	// enable back-to-list-button in detail-view.
	add_settings_field(
		'personioIntegrationTemplateBackToListButton',
		__( 'Enable back to list-link', 'personio-integration-light' ),
		'personio_integration_admin_checkbox_field',
		'personioIntegrationPositionsTemplates',
		'settings_section_template_detail',
		array(
			'label_for' => 'personioIntegrationTemplateBackToListButton',
			'fieldId'   => 'personioIntegrationTemplateBackToListButton',
			'readonly'  => ! helper::is_personio_url_set(),
		)
	);
	register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationTemplateBackToListButton' );

	// link-target for back-to-list-button in detail-view.
	add_settings_field(
		'personioIntegrationTemplateBackToListUrl',
		__( 'URL for back to list-link', 'personio-integration-light' ),
		'personio_integration_admin_text_field',
		'personioIntegrationPositionsTemplates',
		'settings_section_template_detail',
		array(
			'label_for'   => 'personioIntegrationTemplateBackToListUrl',
			'fieldId'     => 'personioIntegrationTemplateBackToListUrl',
			/* translators: %1$s will be replaced by the list-slug */
			'description' => sprintf( __( 'If empty the link will be set to list-slug <a href="%1$s">%1$s</a>.', 'personio-integration-light' ), trailingslashit( get_home_url() ) . helper::get_archive_slug() ),
			'readonly'    => ! helper::is_personio_url_set(),
		)
	);
	register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationTemplateBackToListUrl' );

	/**
	 * Other section
	 */
	add_settings_section(
		'settings_section_template_other',
		__( 'Other settings', 'personio-integration-light' ),
		'__return_true',
		'personioIntegrationPositionsTemplates'
	);

	// separator for excerpt-listing.
	add_settings_field(
		'personioIntegrationTemplateExcerptSeparator',
		__( 'Separator for details-listing', 'personio-integration-light' ),
		'personio_integration_admin_text_field',
		'personioIntegrationPositionsTemplates',
		'settings_section_template_other',
		array(
			'label_for' => 'personioIntegrationTemplateExcerptSeparator',
			'fieldId'   => 'personioIntegrationTemplateExcerptSeparator',
			'readonly'  => ! helper::is_personio_url_set(),
		)
	);
	register_setting( 'personioIntegrationPositionsTemplates', 'personioIntegrationTemplateExcerptSeparator' );
}
add_action( 'personio_integration_settings_add_settings', 'personio_integration_admin_add_settings_template' );
