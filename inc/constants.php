<?php
/**
 * File to collect all constants this plugin is using.
 *
 * @package personio-integration-light
 */

/**
 * Name of the custom posttype for positions.
 */

use App\Helper;

const WP_PERSONIO_INTEGRATION_CPT = 'personioposition';

/**
 * Name of the prefix for any language-option.
 */
const WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION = 'personioPositionLanguages';

/**
 * Name of the option which holds the main language.
 */
const WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE = 'personioIntegrationMainLanguage';

/**
 * Name of the postmeta-field with the personioId.
 */
const WP_PERSONIO_INTEGRATION_CPT_PM_PID = 'personioId';

/**
 * Name of the postmeta-field with the createdAt-setting.
 */
const WP_PERSONIO_INTEGRATION_CPT_CREATEDAT = 'personioCreatedAt';

/**
 * Marker for running import.
 */
const WP_PERSONIO_INTEGRATION_IMPORT_RUNNING = 'personioIntegrationImportRunning';

/**
 * Update-Flag.
 */
const WP_PERSONIO_INTEGRATION_UPDATED = 'personio_integration_updateflag';

/**
 * Language-specific marker for position-title.
 */
const WP_PERSONIO_INTEGRATION_LANG_POSITION_TITLE = 'personio_integration_position_title';

/**
 * Language-specific marker for position-text.
 */
const WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT = 'personio_integration_position_content';

/**
 * Language-specific option name for MD5-Hash for import-string.
 */
const WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 = 'personioIntegration_xml_hash_';

/**
 * Language-specific option name for timestamp for import.
 */
const WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP = 'personioIntegration_xml_lm_timestamp_';

/**
 * Language for emergencies if no language-data could be detected.
 */
const WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY = 'en';

/**
 * Name for taxonomy-meta-field for language-specific titles.
 */
const WP_PERSONIO_INTEGRATION_TAXONOMY_LANG_TITLE = 'langs';

/**
 * Define each taxonomy
 */
const WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY = 'personioRecruitingCategory';
const WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY = 'personioOccupationCategory';
const WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION          = 'personioOccupation';
const WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE              = 'personioOffice';
const WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT          = 'personioDepartment';
const WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE     = 'personioEmploymentType';
const WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY           = 'personioSeniority';
const WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE            = 'personioSchedule';
const WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE          = 'personioExperience';
const WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES           = 'personioLanguages';
const WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS            = 'personioKeywords';

/**
 * Set transient-based hints for the backend.
 */
const WP_PERSONIO_INTEGRATION_TRANSIENTS = array(
	'personio_integration_no_simplexml'                => array(
		'type'    => 'error',
		'options' => array(
			'disable_plugin' => true,
		),
	),
	'personio_integration_no_url_set'                  => array(
		'type'    => 'error',
		'options' => array(
			'hideOnPages' => array(
				'personioPositions',
			),
		),
	),
	'personio_integration_no_position_imported'        => array(
		'type'    => 'error',
		'options' => array(
			'hideIfTransients'   => array(
				'personio_integration_no_url_set',
				'personio_integration_import_now',
				'personio_integration_url_not_usable',
				'personio_integration_import_run',
				'personio_integration_import_cancel',
				'personio_integration_delete_run',
				'personio_integration_update_slugs',
			),
			'hideOnSettingsTabs' => array(
				'importexport',
			),
		),
	),
	'personio_integration_import_run'                  => array(
		'type' => 'success',
	),
	'personio_integration_delete_run'                  => array(
		'type' => 'success',
	),
	'personio_integration_could_not_delete'            => array(
		'type' => 'error',
	),
	'personio_integration_update_slugs'                => array(
		'type' => 'success',
	),
	'personio_integration_import_now'                  => array(
		'type' => 'success',
	),
	'personio_integration_url_not_usable'              => array(
		'type' => 'error',
	),
	'personio_integration_limit_hint'                  => array(
		'type' => 'error',
	),
	'personio_integration_import_canceled'             => array(
		'type' => 'success',
	),
	'personio_integration_old_templates'               => array(
		'type' => 'error',
	),
	'personio_integration_divi'                        => array(
		'type' => 'success',
	),
	'personio_integration_elementor'                   => array(
		'type' => 'success',
	),
	'personio_integration_wpbakery'                    => array(
		'type' => 'success',
	),
	'personio_integration_beaver'                      => array(
		'type' => 'success',
	),
	'personio_integration_siteorigin'                  => array(
		'type' => 'success',
	),
	'personio_integration_themify'                     => array(
		'type' => 'success',
	),
	'personio_integration_avada'                       => array(
		'type' => 'success',
	),
	'personio_integration_admin_show_review_hint'      => array(
		'type' => 'success',
	),
	'personio_integration_admin_show_text_domain_hint' => array(
		'type' => 'success',
	),
);

/**
 * List of languages which are supported by Personio as of July 2022.
 */
$languages = array(
	'de' => 1,
	'en' => 1,
);
define( 'WP_PERSONIO_INTEGRATION_LANGUAGES_COMPLETE', $languages );
define( 'WP_PERSONIO_INTEGRATION_LANGUAGES', get_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION, array() ) );

/**
 * Define each taxonomy with its specific setting.
 */
const WP_PERSONIO_INTEGRATION_TAXONOMIES = array(
	WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY => array(
		'attr'        => array( // taxonomy settings deviating from default
			'rewrite' => array( 'slug' => 'recruitingCategory' ),
		),
		'slug'        => 'recruitingCategory',
		'useInFilter' => 1,
	),
	WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY => array(
		'attr'        => array( // taxonomy settings deviating from default
			'rewrite' => array( 'slug' => 'occupationCategory' ),
		),
		'slug'        => 'occupation',
		'useInFilter' => 1,
	),
	WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION          => array(
		'attr'        => array( // taxonomy settings deviating from default
			'rewrite' => array( 'slug' => 'occupation' ),
		),
		'slug'        => 'occupation_detail',
		'useInFilter' => 1,
	),
	WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE              => array(
		'attr'        => array( // taxonomy settings deviating from default
			'rewrite' => array( 'slug' => 'office' ),
		),
		'slug'        => 'office',
		'useInFilter' => 1,
	),
	WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT          => array(
		'attr'        => array( // taxonomy settings deviating from default
			'rewrite' => array( 'slug' => 'department' ),
		),
		'slug'        => 'department',
		'useInFilter' => 1,
	),
	WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE     => array(
		'attr'        => array( // taxonomy settings deviating from default
			'rewrite' => array( 'slug' => 'employmenttype' ),
		),
		'slug'        => 'employmenttype',
		'useInFilter' => 1,
	),
	WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY           => array(
		'attr'        => array( // taxonomy settings deviating from default
			'rewrite' => array( 'slug' => 'seniority' ),
		),
		'slug'        => 'seniority',
		'useInFilter' => 1,
	),
	WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE            => array(
		'attr'        => array( // taxonomy settings deviating from default
			'rewrite' => array( 'slug' => 'schedule' ),
		),
		'slug'        => 'schedule',
		'useInFilter' => 1,
	),
	WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE          => array(
		'attr'        => array( // taxonomy settings deviating from default
			'rewrite' => array( 'slug' => 'experience' ),
		),
		'slug'        => 'experience',
		'useInFilter' => 1,
	),
	WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES           => array(
		'attr'        => array( // taxonomy settings deviating from default
			'show_ui' => false,
		),
		'slug'        => 'language',
		'useInFilter' => 0,
	),
	WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS            => array(
		'attr'        => array( // taxonomy settings deviating from default
			'rewrite' => array( 'slug' => 'keyword' ),
		),
		'slug'        => 'keyword',
		'useInFilter' => 1,
	),
);

/**
 * Define names for progressbar during import.
 */
const WP_PERSONIO_OPTION_COUNT = 'piImportCount';
const WP_PERSONIO_OPTION_MAX   = 'piImportMax';

/**
 * Path to the gutenberg-templates.
 */
define( 'WP_PERSONIO_GUTENBERG_TEMPLATES', Helper::get_plugin_path() . 'templates/gutenberg/' );

/**
 * Define the Gutenberg-template-parent-ID which should reflect the plugin-directory.
 */
const WP_PERSONIO_GUTENBERG_PARENT_ID = 'personio-integration-light/personio-integration-light';

