<?php
/**
 * File which holds all deprecated functions from preview versions
 * if a project is using custom plugins or themes based on it to mark them as deprecated
 *
 * @package personio-integration-light
 */

/**
 * @deprecated since 3.0.0
 */
const WP_PERSONIO_INTEGRATION_CPT_PM_PID = 'personioId';

/**
 * Load alias for setup.
 *
 * @deprecated since 4.0.0
 */
if( ! class_exists( '\wpEasySetup\Setup' ) ) {
	class_alias('\easySetupForWordPress\Setup', 'wpEasySetup\Setup');
}

/**
 * Load alias for transients.
 *
 * @deprecated since 5.0.0
 */
if( ! class_exists( 'PersonioIntegrationLight\Plugin\Transients' ) ) {
	class_alias('PersonioIntegrationLight\Dependencies\easyTransientsForWordPress\Transients', 'PersonioIntegrationLight\Plugin\Transients' );
}

/**
 * Load alias for transient.
 *
 * @deprecated since 5.0.0
 */
if( ! class_exists( 'PersonioIntegrationLight\Plugin\Transient' ) ) {
	class_alias('PersonioIntegrationLight\Dependencies\easyTransientsForWordPress\Transient', 'PersonioIntegrationLight\Plugin\Transient' );
}

/**
 * Load alias for old name setting object.
 */
if ( ! class_exists( '\PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings' ) ) {
	class_alias( '\PersonioIntegrationLight\Plugin\DeprecatedSettings', 'PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings' );
}

/**
 * Load alias for old name setting object.
 */
if ( ! class_exists( '\PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Setting' ) ) {
	class_alias( '\PersonioIntegrationLight\Plugin\DeprecatedSetting', 'PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Setting' );
}

/**
 * Load alias for old name setting object.
 */
if ( ! class_exists( '\PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Text' ) ) {
	class_alias( '\PersonioIntegrationLight\Plugin\DeprecatedSettings', 'PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Text' );
}

/**
 * Load alias for old name setting object.
 */
if ( ! class_exists( '\PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\TextInfo' ) ) {
	class_alias( '\PersonioIntegrationLight\Plugin\DeprecatedSettings', 'PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\TextInfo' );
}

/**
 * Load alias for old name setting object.
 */
if ( ! class_exists( '\PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Select' ) ) {
	class_alias( '\PersonioIntegrationLight\Plugin\DeprecatedSettings', 'PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Select' );
}

/**
 * Load alias for old name setting object.
 */
if ( ! class_exists( '\PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\MultiSelect' ) ) {
	class_alias( '\PersonioIntegrationLight\Plugin\DeprecatedSettings', 'PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\MultiSelect' );
}

/**
 * Load alias for old name setting object.
 */
if ( ! class_exists( '\PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Checkbox' ) ) {
	class_alias( '\PersonioIntegrationLight\Plugin\DeprecatedSettings', 'PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Checkbox' );
}

/**
 * Load alias for old name setting object.
 */
if ( ! class_exists( '\PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Button' ) ) {
	class_alias( '\PersonioIntegrationLight\Plugin\DeprecatedSettings', 'PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Button' );
}
