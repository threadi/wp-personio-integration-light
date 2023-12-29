<?php
/**
 * File to validate the languages-setting.
 *
 * @package personio-integration-light
 */

namespace App\Plugin\Admin\SettingsValidation;

/**
 * Object which validates the given URL.
 */
class Languages {
	/**
	 * Validate the usage of languages.
	 *
	 * @param array $values List of configured languages.
	 * @return array
	 */
	public static function validate( array $values ): array {
		// if empty set english.
		if ( empty( $values ) ) {
			add_settings_error( 'personioIntegrationLanguages', 'personioIntegrationLanguages', __( 'You must enable one language. English will be set.', 'personio-integration-light' ) );
			$values = array( WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY => 1 );
		}

		// check if new configuration would change anything.
		$actual_languages = get_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION, array() );
		if ( $values !== $actual_languages ) {

			// first remove all language-specific settings.
			foreach ( \App\Plugin\Languages::get_instance()->get_languages() as $key => $lang ) {
				delete_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $key );
				delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $key );
				delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $key );
			}

			// then set the activated languages.
			foreach ( $values as $key => $active ) {
				if ( 1 === absint( $active ) ) {
					update_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $key, 1 );
				}
			}
		}
		return $values;
	}
}
