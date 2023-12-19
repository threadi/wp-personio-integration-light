<?php
/**
 * File for general settings.
 *
 * @package personio-integration-light
 */

use personioIntegration\helper;

/**
 * Page for general settings.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_menu_content_settings(): void {
	// check user capabilities.
	if ( ! current_user_can( 'manage_' . WP_PERSONIO_INTEGRATION_CPT ) ) {
		return;
	}

	// show errors.
	settings_errors();

	?>
	<form method="POST" action="<?php echo esc_url( get_admin_url() ); ?>options.php">
		<?php
		settings_fields( 'personioIntegrationPositions' );
		do_settings_sections( 'personioIntegrationPositions' );
		submit_button();
		?>
	</form>
	<?php
}
add_action( 'personio_integration_settings_general_page', 'personio_integration_admin_add_menu_content_settings' );

/**
 * Get general options.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_settings_general(): void {
	/**
	 * General Section
	 */
	add_settings_section(
		'settings_section_main',
		__( 'General Settings', 'personio-integration-light' ),
		'__return_true',
		'personioIntegrationPositions'
	);

	// Personio URL.
	add_settings_field(
		'personioIntegrationUrl',
		__( 'Personio URL', 'personio-integration-light' ),
		'personio_integration_admin_text_field',
		'personioIntegrationPositions',
		'settings_section_main',
		array(
			'label_for'   => 'personioIntegrationUrl',
			'fieldId'     => 'personioIntegrationUrl',
			/* translators: %1$s is replaced with the url to personio account, %2$s is replaced with the url to the personio support */
			'description' => sprintf( __( 'You find this URL in your <a href="%1$s" target="_blank">Personio-account (opens new window)</a> under Settings > Recruiting > Career Page > Activations.<br>If you have any questions about the URL provided by Personio, please contact the <a href="%2$s">Personio support</a>.', 'personio-integration-light' ), helper::get_personio_login_url(), helper::get_personio_support_url() ),
			'placeholder' => helper::isGermanLanguage() ? 'https://yourcompany.jobs.personio.de' : 'https://yourcompany.jobs.personio.com',
			'highlight'   => ! helper::is_personioUrl_set(),
		)
	);
	register_setting(
		'personioIntegrationPositions',
		'personioIntegrationUrl',
		array(
			'sanitize_callback' => 'personio_integration_admin_validate_personio_url',
			'type'              => 'string',
		)
	);

	// add additional settings.
	do_action( 'personio_integration_add_settings_generell' );

	// activate languages.
	add_settings_field(
		'personioIntegrationLanguages',
		__( 'Used languages', 'personio-integration-light' ),
		'personio_integration_admin_languages_field',
		'personioIntegrationPositions',
		'settings_section_main',
		array(
			'label_for' => 'personioIntegrationLanguages',
			'fieldId'   => 'personioIntegrationLanguages',
			'readonly'  => ! helper::is_personioUrl_set(),
		)
	);
	if ( ! empty( get_option( 'personioIntegrationUrl', '' ) ) ) {
		register_setting(
			'personioIntegrationPositions',
			'personioIntegrationLanguages',
			array(
				'sanitize_callback' => 'personio_integration_admin_validate_languages',
				'type'              => 'array',
			)
		);
	}

	// main language.
	add_settings_field(
		WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE,
		__( 'Main language', 'personio-integration-light' ),
		'personio_integration_admin_languages_radio_field',
		'personioIntegrationPositions',
		'settings_section_main',
		array(
			'label_for' => WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE,
			'fieldId'   => WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE,
			'readonly'  => ! helper::is_personioUrl_set(),
		)
	);
	if ( ! empty( get_option( 'personioIntegrationUrl', '' ) ) ) {
		register_setting(
			'personioIntegrationPositions',
			WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE,
			array(
				'sanitize_callback' => 'personio_integration_admin_validate_main_language',
				'type'              => 'string',
			)
		);
	}
}
add_action( 'personio_integration_settings_add_settings', 'personio_integration_admin_add_settings_general' );

/**
 * Show all by this plugin available languages to select which one is active.
 *
 * @param array $attr List of settings for this field.
 * @noinspection DuplicatedCode
 * @return void
 */
function personio_integration_admin_languages_field( array $attr ): void {
	if ( ! empty( $attr['fieldId'] ) ) {
		foreach ( helper::get_supported_languages() as $key => $enabled ) {

			// get language name.
			$language_name = personio_integration_admin_language_name( $key );

			// get checked-marker.
			$checked = 1 === absint( get_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $key, 0 ) ) ? ' checked="checked"' : '';

			// get title.
			/* translators: %1$s is replaced with "string" */
			$title = sprintf( __( 'Mark to enable %1$s', 'personio-integration-light' ), $language_name );

			// readonly.
			$readonly = '';
			if ( isset( $attr['readonly'] ) && false !== $attr['readonly'] ) {
				$readonly = ' disabled="disabled"';
				?>
				<input type="hidden" id="<?php echo esc_attr( $attr['fieldId'] . $key ); ?>" name="<?php echo esc_attr( $attr['fieldId'] ); ?>[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_html( ! empty( $checked ) ? '1' : '0' ); ?>">
				<?php
			}
			if ( 0 === absint( $enabled ) ) {
				$readonly = ' disabled="disabled"';
				$title    = '';
			}

			// output.
			?>
			<div>
				<input type="checkbox" id="<?php echo esc_attr( $attr['fieldId'] . $key ); ?>" name="<?php echo esc_attr( $attr['fieldId'] ); ?>[<?php echo esc_attr( $key ); ?>]" value="1"<?php echo esc_attr( $checked ) . esc_attr( $readonly ); ?> title="<?php echo esc_attr( $title ); ?>">
				<label for="<?php echo esc_attr( $attr['fieldId'] . $key ); ?>"><?php echo esc_html( $language_name ); ?></label>
			</div>
			<?php
		}

		// pro hint.
		/* translators: %1$s is replaced with "string" */
		do_action( 'personio_integration_admin_show_pro_hint', __( 'Use all languages supported by Personio with %s.', 'personio-integration-light' ) );
	}
}

/**
 * Show all by this plugin available languages to select which the main language.
 *
 * @param array $attr List of settings for the field.
 *
 * @return void
 */
function personio_integration_admin_languages_radio_field( array $attr ): void {
	if ( ! empty( $attr['fieldId'] ) ) {
		foreach ( helper::get_supported_languages() as $key => $enabled ) {
			// get check state.
			$checked = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, '' ) === $key ? ' checked="checked"' : '';

			// get the language name.
			$language_name = personio_integration_admin_language_name( $key );

			// get title.
			/* translators: %1$s is replaced with "string" */
			$title = sprintf( __( 'Mark to set %1$s as default language in the frontend.', 'personio-integration-light' ), $language_name );

			// readonly.
			$readonly = '';
			if ( isset( $attr['readonly'] ) && false !== $attr['readonly'] ) {
				$readonly = ' disabled="disabled"';
				?>
				<input type="hidden" id="<?php echo esc_attr( $attr['fieldId'] . $key ); ?>" name="<?php echo esc_attr( $attr['fieldId'] ); ?>" value="<?php echo esc_html( ! empty( $checked ) ? '1' : '0' ); ?>">
				<?php
			}
			if ( 0 === absint( $enabled ) ) {
				$readonly = ' disabled="disabled"';
				$title    = '';
			}

			// output.
			?>
			<div>
				<input type="radio" id="<?php echo esc_attr( $attr['fieldId'] . $key ); ?>" name="<?php echo esc_attr( $attr['fieldId'] ); ?>" value="<?php echo esc_attr( $key ); ?>"<?php echo esc_attr( $checked ) . esc_attr( $readonly ); ?> title="<?php echo esc_attr( $title ); ?>">
				<label for="<?php echo esc_attr( $attr['fieldId'] . $key ); ?>"><?php echo esc_html( $language_name ); ?></label>
			</div>
			<?php
		}
	}
}

/**
 * Get the name of the given languages.
 *
 * @param string $lang The requested language.
 * @return string
 */
function personio_integration_admin_language_name( string $lang ): string {
	$languages = apply_filters(
		'personio_integration_languages_names',
		array(
			'de' => __( 'German', 'personio-integration-light' ),
			'en' => __( 'English', 'personio-integration-light' ),
		)
	);
	return $languages[ $lang ];
}

/**
 * Validate the usage of languages.
 *
 * @param array $values List of configured languages.
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_admin_validate_languages( array $values ): array {
	// if empty set english.
	if ( empty( $values ) ) {
		add_settings_error( 'personioIntegrationLanguages', 'personioIntegrationLanguages', __( 'You must enable one language. English will be set.', 'personio-integration-light' ) );
		$values = array( WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY => 1 );
	}

	// check if new configuration would change anything.
	$actual_languages = get_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION, array() );
	if ( $values !== $actual_languages ) {

		// first remove all language-specific settings.
		foreach ( helper::get_supported_languages() as $key => $lang ) {
			delete_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $key );
			delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $key );
			delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $key );
		}

		// then set the activated languages.
		update_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION, $values );
		foreach ( $values as $key => $active ) {
			update_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $key, 1 );
		}
	}
	return $values;
}

/**
 * Validate the setting for the main language.
 *
 * @param string $value The string of the main language.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_admin_validate_main_language( string $value ): string {
	if ( 0 === strlen( $value ) ) {
		add_settings_error( 'personioIntegrationMainLanguage', 'personioIntegrationMainLanguage', __( 'No main language was specified. The specification of a main language is mandatory.', 'personio-integration-light' ) );
		$value = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE );
	} elseif ( empty( WP_PERSONIO_INTEGRATION_LANGUAGES[ $value ] ) ) {
		add_settings_error( 'personioIntegrationMainLanguage', 'personioIntegrationMainLanguage', __( 'The selected main language is not activated as a language.', 'personio-integration-light' ) );
		$value = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE );
	}
	return $value;
}

/**
 * Valide the Personio-URL.
 *
 * @param string $value The string of the Personio URL.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_admin_validate_personio_url( string $value ): string {
	$errors = get_settings_errors();
	/**
	 * If a result-entry already exists, do nothing here.
	 *
	 * @see https://core.trac.wordpress.org/ticket/21989
	 */
	if ( helper::checkIfSettingErrorEntryExistsInArray( 'personioIntegrationUrl', $errors ) ) {
		return $value;
	}

	$error = false;
	if ( 0 === strlen( $value ) ) {
		add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'The specification of the Personio URL is mandatory.', 'personio-integration-light' ) );
		$error = true;
	}
	if ( 0 < strlen( $value ) ) {
		// remove slash on the end of the given url.
		$value = rtrim( $value, '/' );

		// check if URL ends with ".jobs.personio.com" or ".jobs.personio.de" with or without "/" on the end.
		if (
			! (
				str_ends_with( $value, '.jobs.personio.com' )
				|| str_ends_with( $value, '.jobs.personio.de' )
			)
		) {
			add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'The Personio URL must end with ".jobs.personio.com" or ".jobs.personio.de"!', 'personio-integration-light' ) );
			$error = true;
			$value = '';
		} elseif ( ! wp_http_validate_url( $value ) ) {
			add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __( 'Please enter a valid URL.', 'personio-integration-light' ) );
			$error = true;
			$value = '';
		} elseif ( get_option( 'personioIntegrationUrl', '' ) !== $value ) {
			// -> should return HTTP-Status 200
			$response = wp_remote_get(
				helper::get_personio_xml_url( $value ),
				array(
					'timeout'     => 30,
					'redirection' => 0,
				)
			);
			// get the body with the contents.
			$body = wp_remote_retrieve_body( $response );
			if ( ( is_array( $response ) && ! empty( $response['response']['code'] ) && 200 !== $response['response']['code'] ) || str_starts_with( $body, '<!doctype html>' ) ) {
				// error occurred => show hint.
				set_transient( 'personio_integration_url_not_usable', 1 );
				$error = true;
				$value = '';
			} else {
				// URL is available.
				// -> show hint and option to import the positions now.
				set_transient( 'personio_integration_import_now', 1 );
				// reset options for the import.
				foreach ( helper::getActiveLanguagesWithDefaultFirst() as $key => $lang ) {
					delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $key );
					delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $key );
				}
			}
		}
	}

	// reset transient if url is set.
	if ( ! $error ) {
		delete_transient( 'personio_integration_no_url_set' );
	}

	// return value if all is ok.
	return $value;
}
