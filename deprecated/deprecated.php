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
