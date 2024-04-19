<?php
/**
 * File to validate the languages-setting.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsValidation;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\PersonioIntegration\Positions;

/**
 * Object which validates the given URL.
 */
class Languages {
	/**
	 * Validate the usage of languages.
	 *
	 * @param array|null $values List of configured languages.
	 *
	 * @return array
	 */
	public static function validate( array|null $values ): array {
		// if empty set fallback language.
		if ( empty( $values ) ) {
			add_settings_error( 'personioIntegrationLanguages', 'personioIntegrationLanguages', __( 'You must enable one language. English will be set.', 'personio-integration-light' ) );
			$values = array( \PersonioIntegrationLight\Plugin\Languages::get_instance()->get_fallback_language_name() => 1 );
		}

		// check if new configuration would change anything.
		$actual_languages = array();
		foreach ( \PersonioIntegrationLight\Plugin\Languages::get_instance()->get_active_languages( false ) as $language_name => $label ) {
			$actual_languages[ $language_name ] = '1';
		}
		if ( $values !== $actual_languages ) {
			// reset Personio- and language-specific settings.
			Imports::get_instance()->reset_personio_settings();

			// then set the activated languages.
			foreach ( $values as $language_name => $active ) {
				if ( 1 === absint( $active ) ) {
					update_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $language_name, 1 );
				}
			}

			// trigger re-import hint.
			if ( ! defined( 'PERSONIO_INTEGRATION_UPDATE_RUNNING' ) && ! defined( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING' ) ) {
				Positions::get_instance()->trigger_reimport_hint();
			}
		}
		return $values;
	}
}
