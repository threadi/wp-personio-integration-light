<?php
/**
 * File for advanced settings.
 *
 * @package personio-integration-light
 */

use App\helper;

/**
 * Get advanced options.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_settings_advanced(): void {
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
		array(
			'label_for' => 'personioIntegration_advanced_pro_hint',
			'fieldId'   => 'personioIntegration_advanced_pro_hint',
		)
	);

	// add more advanced settings.
	do_action( 'personio_integration_advanced_settings' );

	// extend search.
	add_settings_field(
		'personioIntegrationExtendSearch',
		__( 'Note the position-keywords in search in frontend', 'personio-integration-light' ),
		'personio_integration_admin_checkbox_field',
		'personioIntegrationPositionsAdvanced',
		'settings_section_advanced',
		array(
			'label_for' => 'personioIntegrationExtendSearch',
			'fieldId'   => 'personioIntegrationExtendSearch',
			'readonly'  => ! helper::is_personio_url_set(),
		)
	);
	register_setting( 'personioIntegrationPositionsAdvanced', 'personioIntegrationExtendSearch', array( 'type' => 'integer' ) );

	// max age for log-entries.
	add_settings_field(
		'personioIntegrationMaxAgeLogEntries',
		__( 'max. Age for log entries in days', 'personio-integration-light' ),
		'personio_integration_admin_number_field',
		'personioIntegrationPositionsAdvanced',
		'settings_section_advanced',
		array(
			'label_for' => 'personioIntegrationMaxAgeLogEntries',
			'fieldId'   => 'personioIntegrationMaxAgeLogEntries',
			'readonly'  => ! helper::is_personio_url_set(),
		)
	);
	register_setting( 'personioIntegrationPositionsAdvanced', 'personioIntegrationMaxAgeLogEntries' );

	// Personio URL Timeout.
	add_settings_field(
		'personioIntegrationUrlTimeout',
		__( 'Timeout for URL-request in Seconds', 'personio-integration-light' ),
		'personio_integration_admin_number_field',
		'personioIntegrationPositionsAdvanced',
		'settings_section_advanced',
		array(
			'label_for' => 'personioIntegrationUrlTimeout',
			'fieldId'   => 'personioIntegrationUrlTimeout',
			'readonly'  => ! helper::is_personio_url_set(),
		)
	);
	register_setting(
		'personioIntegrationPositionsAdvanced',
		'personioIntegrationUrlTimeout',
		array(
			'sanitize_callback' => 'personio_integration_admin_validate_personio_url_timeout',
			'type'              => 'integer',
		)
	);

	// delete all data on uninstall.
	add_settings_field(
		'personioIntegrationDeleteOnUninstall',
		__( 'Delete all imported data on uninstall', 'personio-integration-light' ),
		'personio_integration_admin_checkbox_field',
		'personioIntegrationPositionsAdvanced',
		'settings_section_advanced',
		array(
			'label_for' => 'personioIntegrationDeleteOnUninstall',
			'fieldId'   => 'personioIntegrationDeleteOnUninstall',
			'readonly'  => ! helper::is_personio_url_set(),
		)
	);
	register_setting( 'personioIntegrationPositionsAdvanced', 'personioIntegrationDeleteOnUninstall', array( 'type' => 'integer' ) );

	// enable debug-Mode.
	add_settings_field(
		'personioIntegration_debug',
		__( 'Debug-Mode', 'personio-integration-light' ),
		'personio_integration_admin_checkbox_field',
		'personioIntegrationPositionsAdvanced',
		'settings_section_advanced',
		array(
			'label_for'   => 'personioIntegration_debug',
			'fieldId'     => 'personioIntegration_debug',
			'description' => __( 'If activated, the import will be executed every time even if there are no changes.', 'personio-integration-light' ),
			'readonly'    => ! helper::is_personio_url_set(),
		)
	);
	register_setting( 'personioIntegrationPositionsAdvanced', 'personioIntegration_debug', array( 'type' => 'integer' ) );
}
add_action( 'personio_integration_settings_add_settings', 'personio_integration_admin_add_settings_advanced' );

/**
 * Add pro hint via settings-field for better position in list.
 *
 * @return void
 */
function personio_integration_admin_advanced_pro_hint(): void {
	// pro hint.
	/* translators: %1$s is replaced with "string" */
	do_action( 'personio_integration_admin_show_pro_hint', __( 'With %s you get more advanced options, e.g. to change the URL of archives with positions.', 'personio-integration-light' ) );
}

/**
 * Valide the timeout
 *
 * @param int $value The timeout value.
 * @return int
 * @noinspection PhpUnused
 */
function personio_integration_admin_validate_personio_url_timeout( int $value ): int {
	$value = absint( $value );
	if ( 0 === $value ) {
		add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'A timeout must have a value greater than 0.', 'personio-integration-light' ) );
	}
	return $value;
}
