<?php

/**
 * File to collect all constants this plugin is using.
 */

/**
 * Define text-domain.
 */

/**
 * Name of the custom posttype for positions.
 */
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
 * Update-Flag
 */
const WP_PERSONIO_INTEGRATION_UPDATED = 'personio_integration_updateflag';

/**
 * Language-specific marker for position-title
 */
const WP_PERSONIO_INTEGRATION_LANG_POSITION_TITLE = 'personio_integration_position_title';

/**
 * Language-specific marker for position-text
 */
const WP_PERSONIO_INTEGRATION_LANG_POSITION_CONTENT = 'personio_integration_position_content';

/**
 * language-specific option name for MD5-Hash for import-string
 */
const WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 = 'personioIntegration_xml_hash_';

/**
 * language-specific option name for timestamp for import
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
 * include the taxonomy-settings
 */
include_once 'taxonomies.php';

/**
 * Set transient-based hints for the backend.
 */
const WP_PERSONIO_INTEGRATION_TRANSIENTS = [
    "personio_integration_no_simplexml" => [
        'type' => 'error',
        'options' => [
            'disable_plugin' => true
        ]
    ],
    "personio_integration_no_url_set" => [
        'type' => 'error',
        'options' => [
            'hideOnPages' => [
                'personioPositions'
            ]
        ]
    ],
    "personio_integration_no_position_imported" => [
        'type' => 'error',
        'options' => [
            'hideIfTransients' => [
                'personio_integration_no_url_set',
                'personio_integration_import_now',
                'personio_integration_url_not_usable',
                'personio_integration_import_run',
                'personio_integration_import_cancel',
                'personio_integration_delete_run',
                'personio_integration_update_slugs'
            ],
            'hideOnSettingsTabs' => [
                'importexport'
            ]
        ]
    ],
    'personio_integration_import_run' => [
        'type' => 'success'
    ],
    'personio_integration_delete_run' => [
        'type' => 'success'
    ],
    'personio_integration_could_not_delete' => [
        'type' => 'error'
    ],
    'personio_integration_update_slugs' => [
        'type' => 'success'
    ],
    'personio_integration_import_now' => [
        'type' => 'success'
    ],
    'personio_integration_url_not_usable' => [
        'type' => 'error'
    ],
    "personio_integration_limit_hint" => [
        'type' => 'error',
    ],
];

/**
 * List of languages which are supported by Personio as of July 2022.
 */
$languages = [
    'de' => 1,
    'en' => 1,
];
define("WP_PERSONIO_INTEGRATION_LANGUAGES_COMPLETE", $languages);
define("WP_PERSONIO_INTEGRATION_LANGUAGES", get_option(WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION, []));

/**
 * Define each taxonomy with its specific setting
 */
const WP_PERSONIO_INTEGRATION_TAXONOMIES = [
    WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY => [
        'attr' => [ // taxonomy settings deviating from default
            'rewrite' => ['slug' => 'recruitingCategory'],
        ],
        'slug' => 'recruitingCategory',
        'useInFilter' => 1
    ],
    WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY => [
        'attr' => [ // taxonomy settings deviating from default
            'rewrite' => ['slug' => 'occupationCategory'],
        ],
        'slug' => 'occupation',
        'useInFilter' => 1
    ],
    WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE => [
        'attr' => [ // taxonomy settings deviating from default
            'rewrite' => ['slug' => 'office'],
            'capabilities' => [
                'manage_terms' => 'manage_options',
                'edit_terms' => 'manage_options',
                'delete_terms' => 'god',
                'assign_terms' => 'manage_options',
            ]
        ],
        'slug' => 'office',
        'useInFilter' => 1
    ],
    WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT => [
        'attr' => [ // taxonomy settings deviating from default
            'rewrite' => ['slug' => 'department'],
        ],
        'slug' => 'department',
        'useInFilter' => 1
    ],
    WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE => [
        'attr' => [ // taxonomy settings deviating from default
            'rewrite' => ['slug' => 'employmenttype'],
        ],
        'slug' => 'employmenttype',
        'useInFilter' => 1
    ],
    WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY => [
        'attr' => [ // taxonomy settings deviating from default
            'rewrite' => ['slug' => 'seniority'],
        ],
        'slug' => 'seniority',
        'useInFilter' => 1
    ],
    WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE => [
        'attr' => [ // taxonomy settings deviating from default
            'rewrite' => ['slug' => 'schedule'],
        ],
        'slug' => 'schedule',
        'useInFilter' => 1
    ],
    WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE => [
        'attr' => [ // taxonomy settings deviating from default
            'rewrite' => ['slug' => 'experience'],
        ],
        'slug' => 'experience',
        'useInFilter' => 1
    ],
    WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES => [
        'attr' => [ // taxonomy settings deviating from default
            'show_ui' => false
        ],
        'slug' => 'language',
        'useInFilter' => 0
    ],
];

/**
 * Define names for progressbar during import.
 */
const WP_PERSONIO_OPTION_COUNT = 'piImportCount';
const WP_PERSONIO_OPTION_MAX = 'piImportMax';