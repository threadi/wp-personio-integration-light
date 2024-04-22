<?php
/**
 * File to collect all constants this plugin is using.
 *
 * @package personio-integration-light
 */

/**
 * Name of the custom posttype for positions.
 */
const WP_PERSONIO_INTEGRATION_MAIN_CPT = 'personioposition';

/**
 * Name of the option which holds the main language.
 */
const WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE = 'personioIntegrationMainLanguage';

/**
 * Name of the prefix for any language-option.
 */
const WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION = 'personioPositionLanguages';

/**
 * Name of the postmeta-field with the personioId.
 */
const WP_PERSONIO_INTEGRATION_MAIN_CPT_PM_PID = 'personioId';

/**
 * Name of the postmeta-field with the createdAt-setting.
 */
const WP_PERSONIO_INTEGRATION_MAIN_CPT_CREATEDAT = 'personioCreatedAt';

/**
 * Marker for running import.
 */
const WP_PERSONIO_INTEGRATION_IMPORT_RUNNING = 'personioIntegrationImportRunning';

/**
 * List of possible errors during import.
 */
const WP_PERSONIO_INTEGRATION_IMPORT_ERRORS = 'personioIntegrationImportErrors';

/**
 * Marker for import status.
 */
const WP_PERSONIO_INTEGRATION_IMPORT_STATUS = 'personioIntegrationImportStatus';

/**
 * Marker for running deletion.
 */
const WP_PERSONIO_INTEGRATION_DELETE_RUNNING = 'personioIntegrationDeleteRunning';

/**
 * List of possible errors during deletion.
 */
const WP_PERSONIO_INTEGRATION_DELETE_ERRORS = 'personioIntegrationDeleteErrors';

/**
 * Marker for deletion status.
 */
const WP_PERSONIO_INTEGRATION_DELETE_STATUS = 'personioIntegrationDeleteStatus';

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
 * Options-list of transients.
 */
const WP_PERSONIO_INTEGRATION_TRANSIENTS_LIST = 'personio_integration_transients';

/**
 * Define names for progressbar during import.
 */
const WP_PERSONIO_INTEGRATION_OPTION_COUNT = 'piImportCount';
const WP_PERSONIO_INTEGRATION_OPTION_MAX   = 'piImportMax';

/**
 * Define names for progressbar during deletion.
 */
const WP_PERSONIO_INTEGRATION_DELETE_COUNT = 'piDeleteCount';
const WP_PERSONIO_INTEGRATION_DELETE_MAX   = 'piDeleteMax';

/**
 * Define the Gutenberg-template-parent-ID which should reflect the plugin-directory.
 */
const WP_PERSONIO_GUTENBERG_PARENT_ID = 'personio-integration-light/personio-integration-light';
