<?php

use personioIntegration\helper;

/**
 * Page for general settings.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_menu_content_settings()
{
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // show errors
    settings_errors();

    ?>
    <form method="POST" action="options.php">
        <?php
        settings_fields( 'personioIntegrationPositions' );
        do_settings_sections( 'personioIntegrationPositions' );
        submit_button();
        ?>
    </form>
    <?php
}
add_action('personio_integration_settings_general_page', 'personio_integration_admin_add_menu_content_settings' );

/**
 * Get general options.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_settings_general() {
    /**
     * General Section
     */
    add_settings_section(
        'settings_section_main',
        __( 'General Settings', 'wp-personio-integration' ),
        '__return_true',
        'personioIntegrationPositions'
    );

    // Personio URL
    add_settings_field(
        'personioIntegrationUrl',
        __( 'Personio URL', 'wp-personio-integration' ),
        'personio_integration_admin_text_field',
        'personioIntegrationPositions',
        'settings_section_main',
        [
            'label_for' => 'personioIntegrationUrl',
            'fieldId' => 'personioIntegrationUrl',
            /* translators: %1$s is replaced with the url to personio account, %2$s is replaced with the url to the personio support */
            'description' => sprintf(__('You find this URL in your <a href="%1$s" target="_blank">Personio-account</a> under Settings > Recruiting > Career Page > Activations.<br>If you have any questions about the URL provided by Personio, please contact the <a href="%2$s">Personio support</a>.', 'wp-personio-integration'), helper::get_personio_login_url(), helper::get_personio_support_url() ),
            'placeholder' => helper::isGermanLanguage() ? 'https://yourcompany.jobs.personio.de' : 'https://yourcompany.jobs.personio.com',
            'highlight' => !helper::is_personioUrl_set()
        ]
    );
    register_setting( 'personioIntegrationPositions', 'personioIntegrationUrl', ['sanitize_callback' => 'personio_integration_admin_validatePersonioURL'] );

    // add additional settings
    do_action('personio_integration_add_settings_generell');

    // activate languages
    add_settings_field(
        'personioIntegrationLanguages',
        __( 'Used languages', 'wp-personio-integration' ),
        'personio_integration_admin_languages_field',
        'personioIntegrationPositions',
        'settings_section_main',
        [
            'label_for' => 'personioIntegrationLanguages',
            'fieldId' => 'personioIntegrationLanguages',
            'readonly' => !helper::is_personioUrl_set()
        ]
    );
    get_option('personioIntegrationUrl', '') == '' ? : register_setting( 'personioIntegrationPositions', 'personioIntegrationLanguages', ['sanitize_callback' => 'personio_integration_admin_validateLanguages'] );

    // main language
    add_settings_field(
        WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE,
        __( 'Main language', 'wp-personio-integration' ),
        'personio_integration_admin_languages_radio_field',
        'personioIntegrationPositions',
        'settings_section_main',
        [
            'label_for' => WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE,
            'fieldId' => WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE,
            'readonly' => !helper::is_personioUrl_set()
        ]
    );
    get_option('personioIntegrationUrl', '') == '' ? : register_setting( 'personioIntegrationPositions', WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, ['sanitize_callback' => 'personio_integration_admin_validateMainLanguage'] );
}
add_action( 'personio_integration_settings_add_settings', 'personio_integration_admin_add_settings_general');

/**
 * Show all by this plugin available languages to select which one is active.
 *
 * @return void
 * @noinspection DuplicatedCode
 */
function personio_integration_admin_languages_field( $attr ) {
    if( !empty($attr['fieldId']) ) {
        foreach( helper::get_supported_languages() as $key => $enabled ) {

            $languageName = personio_integration_admin_language_name($key);

            // get checked-marker
            $checked = get_option(WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION.$key, 0) == 1 ? ' checked="checked"' : '';

            // get title
            /* translators: %1$s is replaced with "string" */
            $title = sprintf(__('Mark to enable %1$s', 'wp-personio-integration'), $languageName);

            // readonly
            $readonly = '';
            if( isset($attr['readonly']) && false !== $attr['readonly'] ) {
                $readonly = ' disabled="disabled"';
            }
            if( $enabled == 0 ) {
                $readonly = ' disabled="disabled"';
                $title = '';
            }

            // output
            ?>
            <div>
                <input type="checkbox" id="<?php echo esc_attr($attr['fieldId'].$key); ?>" name="<?php echo esc_attr($attr['fieldId']); ?>[<?php echo esc_attr($key); ?>]" value="1"<?php echo esc_attr($checked).esc_attr($readonly); ?> title="<?php echo esc_attr($title); ?>">
                <label for="<?php echo esc_attr($attr['fieldId'].$key); ?>"><?php echo esc_html($languageName); ?></label>
            </div>
            <?php
        }

        // pro hint
        /* translators: %1$s is replaced with "string" */
        do_action('personio_integration_admin_show_pro_hint', __('Use all languages supported by Personio with %s.', 'wp-personio-integration'));
    }
}

/**
 * Show all by this plugin available languages to select which the main language.
 *
 * @return void
 */
function personio_integration_admin_languages_radio_field( $attr ) {
    if( !empty($attr['fieldId']) ) {
        foreach( helper::get_supported_languages() as $key => $enabled ) {
            // get check state
            $checked = get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, '') == $key ? ' checked="checked"' : '';

            // get the language name
            $languageName = personio_integration_admin_language_name($key);

            // get title
            /* translators: %1$s is replaced with "string" */
            $title = sprintf(__('Mark to set %1$s as default language in the frontend.', 'wp-personio-integration'), $languageName);

            // readonly
            $readonly = '';
            if( isset($attr['readonly']) && false !== $attr['readonly'] ) {
                $readonly = ' disabled="disabled"';
            }
            if( $enabled == 0 ) {
                $readonly = ' disabled="disabled"';
                $title = '';
            }

            // output
            ?>
            <div>
                <input type="radio" id="<?php echo esc_attr($attr['fieldId'].$key); ?>" name="<?php echo esc_attr($attr['fieldId']); ?>" value="<?php echo esc_attr($key); ?>"<?php echo esc_attr($checked).esc_attr($readonly); ?> title="<?php echo esc_attr($title); ?>">
                <label for="<?php echo esc_attr($attr['fieldId'].$key); ?>"><?php echo esc_html($languageName); ?></label>
            </div>
            <?php
        }
    }
}

/**
 * Get the name of the given languages.
 *
 * @param $lang
 * @return string
 */
function personio_integration_admin_language_name( $lang ): string
{
    $array = [
        'de' => __('German', 'wp-personio-integration'),
        'en' => __('English', 'wp-personio-integration'),
    ];
    $languages = apply_filters('personio_integration_languages_names', $array);
    return $languages[$lang];
}

/**
 * Validate the usage of languages.
 *
 * @param $values
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_validateLanguages( $values ) {
    // if given value is not an array, set it to array
    if( !is_array($values) ) {
        $values = [];
    }

    // if empty set english
    if( empty($values) ) {
        add_settings_error('personioIntegrationLanguages', 'personioIntegrationLanguages', __('You must enable one language. English will be set.', 'wp-personio-integration'), 'error');
        $values = [WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY => 1];
    }

    // check if new configuration would change anything
    $actualLanguages = get_option(WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION, []);
    if( $values !== $actualLanguages ) {

        // first remove all language-specific settings
        foreach( helper::get_supported_languages() as $key => $lang ) {
            delete_option(WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $key);
            delete_option(WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $key);
            delete_option(WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $key);
        }

        // then set the activated languages
        update_option(WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION, $values);
        foreach ($values as $key => $active) {
            update_option(WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $key, 1);
        }
    }
    return $values;
}

/**
 * Validate the setting for the main language.
 *
 * @param $value
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_validateMainLanguage( $value ) {
    if( strlen($value) == 0 ) {
        add_settings_error( 'personioIntegrationMainLanguage', 'personioIntegrationMainLanguage', __('No main language was specified. The specification of a main language is mandatory.', 'wp-personio-integration'), 'error' );
        $value = get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE);
    }
    else {
        if( empty(WP_PERSONIO_INTEGRATION_LANGUAGES[$value]) ) {
            add_settings_error( 'personioIntegrationMainLanguage', 'personioIntegrationMainLanguage', __('The selected main language is not activated as a language.', 'wp-personio-integration'), 'error' );
            $value = get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE);
        }
    }
    return $value;
}

/**
 * Valide the Personio-URL.
 *
 * @param $value
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_validatePersonioURL( $value ) {
    $errors = get_settings_errors();
    /**
     * If a result-entry already exists, do nothing here.
     *
     * @see https://core.trac.wordpress.org/ticket/21989
     */
    if( helper::checkIfSettingErrorEntryExistsInArray('personioIntegrationUrl', $errors) ) {
        return $value;
    }

    $error = false;
    if( strlen($value) == 0 ) {
        add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __('The specification of the Personio URL is mandatory.', 'wp-personio-integration'), 'error' );
        $error = true;
    }
    if( strlen($value) > 0 ) {
        // remove slash on the end of the given url
        $value = rtrim($value, "/");

        // check if URL ends with ".jobs.personio.com" or ".jobs.personio.de"
        // with or without "/" on the end
        if(
            !(
                substr($value, -strlen(".jobs.personio.com")) === ".jobs.personio.com"
                || substr($value, -strlen(".jobs.personio.de")) === ".jobs.personio.de"
            )
        ) {
            add_settings_error( 'personioIntegrationUrl', 'personioIntegrationUrl', __('The Personio URL must end with ".jobs.personio.com" or ".jobs.personio.de"!', 'wp-personio-integration'), 'error' );
            $error = true;
            $value = "";
        }
        else {
            // check if input is a valid URL
            if (!wp_http_validate_url($value)) {
                add_settings_error('personioIntegrationUrl', 'personioIntegrationUrl', __('Please enter a valid URL.', 'wp-personio-integration'), 'error');
                $error = true;
                $value = "";
            } else {
                // check the URL
                // -> only if it has been changed
                if (get_option('personioIntegrationUrl', '') != $value) {
                    // -> should return HTTP-Status 200
                    $response = wp_remote_get(helper::get_personio_xml_url($value),
                        array(
                            'timeout' => 30,
                            'redirection' => 0
                        )
                    );
                    // get the body with the contents
                    $body = wp_remote_retrieve_body($response);
                    if( ( is_array($response) && !empty($response["response"]["code"]) && $response["response"]["code"] != 200 ) || 0 === strpos($body, '<!doctype html>') ) {
                        // error occurred => show hint
                        set_transient('personio_integration_url_not_usable', 1);
                        $error = true;
                        $value = "";
                    } else {
                        // URL is available
                        // -> show hint and option to import the positions now
                        set_transient('personio_integration_import_now', 1);
                        // reset options for the import
                        foreach( helper::getActiveLanguagesWithDefaultFirst() as $key => $lang ) {
                            delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP.$key );
                            delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5.$key );
                        }
                    }
                }
            }
        }
    }

    // reset transient if url is set
    if( !$error ) {
        delete_transient('personio_integration_no_url_set');
    }

    // return value if all is ok
    return $value;
}