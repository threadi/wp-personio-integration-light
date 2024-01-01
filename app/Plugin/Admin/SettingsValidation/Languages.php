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
	 * @param array|null $values List of configured languages.
	 *
	 * @return array
	 */
	public static function validate( array|null $values ): array {
		// if empty set english.
		if ( empty( $values ) ) {
			add_settings_error( 'personioIntegrationLanguages', 'personioIntegrationLanguages', __( 'You must enable one language. English will be set.', 'personio-integration-light' ) );
			$values = array( \App\Plugin\Languages::get_instance()->get_fallback_language_name() => 1 );
		}

		// check if new configuration would change anything.
		$actual_languages = \App\Plugin\Languages::get_instance()->get_languages();
		if ( $values !== $actual_languages ) {

			// first remove all language-specific settings.
			foreach ( \App\Plugin\Languages::get_instance()->get_languages() as $language_name => $label ) {
				delete_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $language_name );
				delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $language_name );
				delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $language_name );
			}

			// then set the activated languages.
			foreach ( $values as $language_name => $active ) {
				if ( 1 === absint( $active ) ) {
					update_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $language_name, 1 );
				}
			}
		}
		return $values;
	}
}
