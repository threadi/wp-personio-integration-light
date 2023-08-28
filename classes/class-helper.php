<?php

namespace personioIntegration;

use WP_Post;
use WP_Post_Type;
use WP_Rewrite;

class helper {

    /**
     * Add terms from an array to a taxonomy.
     *
     * @param $array
     * @param $taxonomy
     * @return void
     */
    public static function addTerms( $array, $taxonomy ): void
    {
        foreach( $array as $key => $termTitle ) {
            if( !term_exists( $key, $taxonomy ) ){
                wp_insert_term(
                    $key,   // the term
                    $taxonomy
                );
            }
        }
    }

    /**
     * Check whether a german language is used in this Wordpress-projekt.
     *
     * @return bool
     */
    public static function isGermanLanguage(): bool
    {
        $germanLanguages = [
            'de_DE',
            'de_DE_formal',
            'de_CH',
            'de_AT'
        ];
        return in_array(get_bloginfo("language"), $germanLanguages);
    }

    /**
     * Return the logo as img
     *
     * @return string
     */
    private static function getLogoImg(): string
    {
        return '<img src="'.helper::getPluginPath().'gfx/personio_icon.png" alt="">';
    }

    /**
     * Return the translated transient-string of given transient.
     *
     * @param $transient
     * @return string
     */
    public static function get_admin_transient_content( $transient ): string
    {
        $transient_value = get_transient($transient);

        $array = [
            'personio_integration_no_simplexml' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p><u>%s</u> %s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                __('Plugin was not activated!', 'wp-personio-integration'),
                __('The PHP extension simplexml is missing on the system. Please contact your hoster about this.', 'wp-personio-integration')
            ),
            'personio_integration_no_url_set' => sprintf(
                '<h3><img src="'.helper::getPluginPath().'gfx/personio_icon.png" alt="">%s</h3><p><u>%s</u> %s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                __('The specification of your Personio URL is still pending.', 'wp-personio-integration'),
                /* translators: %1$s is replaced with "string" */
                sprintf(__('To do this, please go to the <a href="%s">settings page</a>.', 'wp-personio-integration'), esc_url( add_query_arg(
                    [
                        'page' => 'personioPositions',
                        'post_type' => WP_PERSONIO_INTEGRATION_CPT
                    ],
                    get_admin_url() . 'edit.php'
                ) ))
            ),
            'personio_integration_no_position_imported' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p><u>%s</u> %s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                __('You have not imported your open positions from Personio until now.', 'wp-personio-integration'),
                /* translators: %1$s is replaced with "string" */
                __('Click on the following button to import your positions from Personio now:', 'wp-personio-integration').' <br><br><a href="'.helper::get_import_url().'" class="button button-primary">'.__('Run import', 'wp-personio-integration').'</a>'
            ),
            'personio_integration_import_run' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                sprintf(
                    /* translators: %1$s is replaced with "string", %2$s is replaced with "string" */
                    __(
                        '<strong>The import has been manually run.</strong> Please check the list of positions <a href="%1$s">in backend</a> and <a href="%2$s">frontend</a>.',
                        'wp-personio-integration'
                    ),
                    esc_url(add_query_arg(
                        [
                            'post_type' => WP_PERSONIO_INTEGRATION_CPT,
                        ],
                        get_admin_url() . 'edit.php'
                    )),
                    get_post_type_archive_link(WP_PERSONIO_INTEGRATION_CPT)
                )
            ),
            'personio_integration_import_cancel' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                sprintf(
                /* translators: %1$s is replaced with "string", %2$s is replaced with "string" */
                    __(
                        '<strong>The import has been canceled.</strong> Please check the list of positions <a href="%1$s">in backend</a> and <a href="%2$s">frontend</a>.',
                        'wp-personio-integration'
                    ),
                    esc_url(add_query_arg(
                        [
                            'post_type' => WP_PERSONIO_INTEGRATION_CPT,
                        ],
                        get_admin_url() . 'edit.php'
                    )),
                    get_post_type_archive_link(WP_PERSONIO_INTEGRATION_CPT)
                )
            ),
            'personio_integration_delete_run' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                __(
                    '<strong>The positions has been deleted.</strong> You can run the import anytime again to import positions.',
                    'wp-personio-integration'
                )
            ),
            'personio_integration_could_not_delete' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                __(
                    '<strong>The positions could not been deleted.</strong> An import is actual running.',
                    'wp-personio-integration'
                )
            ),
            'personio_integration_import_now' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                __('<strong>The specified Personio URL is reachable.</strong> Click on the following button to import your positions from Personio now:', 'wp-personio-integration').' <br><br><a href="'.helper::get_import_url().'" class="button button-primary personio-integration-import-hint">'.__('Run import', 'wp-personio-integration').'</a>'
            ),
            'personio_integration_url_not_usable' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                sprintf(
                    /* translators: %1$s is replaced with the entered Personio-URL */
                    __('The specified Personio URL %s is not usable for this plugin. Please double-check the URL in your Personio-account under Settings > Recruiting > Career Page > Activations. Please also check if the XML interface is enabled there.', 'wp-personio-integration'),
                    ''
                )
            ),
            'personio_integration_limit_hint' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                sprintf(
                /* translators: %1$s is replaced with "string" */
                    __('The list of positions is limited to a maximum of 10 entries in the frontend. With <a href="%s">Personio Integration Pro version</a>, any number of positions can be displayed.', 'wp-personio-integration'),
                    helper::get_pro_url()
                )
            ),
            'personio_integration_import_canceled' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                __('<strong>The running import has been canceled.</strong> Click on the following button to start a new import. If it also takes to long please check your hosting logfiles for possible restrictions mentioned there.', 'wp-personio-integration').' <br><br><a href="'.helper::get_import_url().'" class="button button-primary personio-integration-import-hint">'.__('Run import', 'wp-personio-integration').'</a>'
            ),
            'personio_integration_old_templates' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>%s<p>%s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                __('<strong>You are using a child theme that contains outdated Personio Integration Light template files.</strong> Please compare the following files in your child-theme with the one this plugin provides:', 'wp-personio-integration'),
                $transient_value,
                __('Hint: the version-number in the header of the files must match.', 'wp-personio-integration')
            ),
	        'personio_integration_divi' => sprintf(
		        '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
		        __('Personio Integration', 'wp-personio-integration'),
				/* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
		        sprintf(__('We realized that you are using Divi - very nice! <i>Personio Integration Pro</i> allows you to design the output of positions in Divi. More information about the Pro version can be found <a href="%1$s" target="_blank">here</a>.', 'wp-personio-integration'), helper::get_pro_url()),
	        ),
            'personio_integration_elementor' => sprintf(
	            '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
	            __('Personio Integration', 'wp-personio-integration'),
	            /* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
	            sprintf(__('We realized that you are using Elementor - very nice! <i>Personio Integration Pro</i> allows you to design the output of positions in Elementor. More information about the Pro version can be found <a href="%1$s" target="_blank">here</a>.', 'wp-personio-integration'), helper::get_pro_url()),
            ),
            'personio_integration_wpbakery' => sprintf(
	            '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
	            __('Personio Integration', 'wp-personio-integration'),
	            /* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
	            sprintf(__('We realized that you are using WPBakery - very nice! <i>Personio Integration Pro</i> allows you to design the output of positions in WPBakery. More information about the Pro version can be found <a href="%1$s" target="_blank">here</a>.', 'wp-personio-integration'), helper::get_pro_url()),
            ),
            'personio_integration_beaver' => sprintf(
	            '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
	            __('Personio Integration', 'wp-personio-integration'),
	            /* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
	            sprintf(__('We realized that you are using Beaver Builder - very nice! <i>Personio Integration Pro</i> allows you to design the output of positions in Beaver Builder. More information about the Pro version can be found <a href="%1$s" target="_blank">here</a>.', 'wp-personio-integration'), helper::get_pro_url()),
            ),
            'personio_integration_siteorigin' => sprintf(
	            '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
	            __('Personio Integration', 'wp-personio-integration'),
	            /* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
	            sprintf(__('We realized that you are using Site Origin - very nice! <i>Personio Integration Pro</i> allows you to design the output of positions in Site Origin. More information about the Pro version can be found <a href="%1$s" target="_blank">here</a>.', 'wp-personio-integration'), helper::get_pro_url()),
            ),
            'personio_integration_themify' => sprintf(
	            '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
	            __('Personio Integration', 'wp-personio-integration'),
	            /* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
	            sprintf(__('We realized that you are using Themify - very nice! <i>Personio Integration Pro</i> allows you to design the output of positions in Themify. More information about the Pro version can be found <a href="%1$s" target="_blank">here</a>.', 'wp-personio-integration'), helper::get_pro_url()),
            ),
            'personio_integration_avada' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                /* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
                sprintf(__('We realized that you are using Avada - very nice! <i>Personio Integration Pro</i> allows you to design the output of positions in Avada. More information about the Pro version can be found <a href="%1$s" target="_blank">here</a>.', 'wp-personio-integration'), helper::get_pro_url()),
            ),
        ];


        if( empty($array[$transient]) ) {
            return '';
        }

        return $array[$transient];
    }

    /**
     * Get the language-depending list-slug.
     *
     * @return string
     */
    public static function getArchiveSlug(): string
    {
        $slug = 'positions';
        if( self::get_wp_lang() == 'de' ) {
            $slug = 'stellen';
        }
        return $slug;
    }

    /**
     * Get the language-depending detail-slug.
     *
     * @return string
     */
    public static function getDetailSlug(): string
    {
        $slug = 'position';
        if( self::get_wp_lang() == 'de' ) {
            $slug = 'stelle';
        }
        return $slug;
    }

    /**
     * Return the URL with information about the Pro-version of this plugin.
     *
     * @return string
     */
    public static function get_pro_url(): string
    {
        return 'https://laolaweb.com/plugins/personio-wordpress-plugin/';
    }

    /**
     * Return the url to start the import manually.
     *
     * @return string
     */
    public static function get_import_url(): string
    {
        return add_query_arg(
            [
                'action' => 'personioPositionsImport',
                'nonce' => wp_create_nonce( 'wp-personio-integration-import' )
            ],
            get_admin_url() . 'admin.php'
        );
    }

    /**
     * Return the url to remove all positions.
     *
     * @return string
     */
    public static function get_delete_url(): string
    {
        return esc_url(add_query_arg(
            [
                'action' => 'personioPositionsDelete',
                'nonce' => wp_create_nonce( 'wp-personio-integration-delete' )
            ],
            get_admin_url() . 'admin.php'
        ));
    }

    /**
     * Get the name of a taxonomy on a position.
     *
     * @param $taxonomy
     * @param $position
     * @return string
     */
    public static function get_taxonomy_name_of_position( $taxonomy, $position): string
    {
        $name = '';
        switch ($taxonomy) {
            case 'recruitingCategory':
                $name = $position->getRecruitingCategoryName();
                break;
            case 'schedule':
                $name = $position->getScheduleName();
                break;
            case 'office':
                $name = $position->getOfficeName();
                break;
            case 'department':
                $name = $position->getDepartmentName();
                break;
            case 'seniority':
                $name = $position->getSeniorityName();
                break;
            case 'experience':
                $name = $position->getExperienceName();
                break;
            case 'occupation':
                $name = $position->getOccupationCategoryName();
                break;
            case 'employmentTypes':
                $name = $position->getEmploymentTypeName();
                break;
	        case 'keyword':
		        $name = $position->getKeywordsTypeName();
		        break;
        }
        return apply_filters( 'personio_integration_get_taxonomy_from_position', $name, $taxonomy, $position );
    }

    /**
     * Get the taxonomy name by its simple name.
     * E.g. from "recruitingCategory" to "personioRecruitingCategory".
     *
     * @param $simpleName
     * @return string
     */
    public static function get_taxonomy_name_by_simple_name( $simpleName ): string
    {
        $taxonomy = '';
        switch( $simpleName ) {
            case 'recruitingCategory':
                $taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY;
                break;
            case 'schedule':
                $taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE;
                break;
            case 'office':
                $taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE;
                break;
            case 'department':
                $taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT;
                break;
            case 'seniority':
                $taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY;
                break;
            case 'experience':
                $taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE;
                break;
            case 'occupation':
                $taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY;
                break;
            case 'employmentTypes':
                $taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE;
                break;
	        case 'keyword':
		        $taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS;
		        break;
        }
        return $taxonomy;
    }

    /**
     * Get list of available filter types.
     *
     * @return array
     */
    public static function get_filter_types(): array
    {
        $types = [
            'select' => __('select-box', 'wp-personio-integration'),
            'linklist' => __('list of links', 'wp-personio-integration')
        ];
        return apply_filters('personio_integration_filter_types', $types);
    }

    /**
     * Load a template if it exists.
     *
     * Also load the requested file if it is located in the /wp-content/themes/xy/personio-integration-light/ directory.
     *
     * @param $template
     * @return string
     */
    public static function getTemplate( $template ): string
    {
        if( is_embed() ) {
            return $template;
        }

		// check if requested template exist in theme.
        $themeTemplate = locate_template(trailingslashit(basename( dirname( WP_PERSONIO_INTEGRATION_PLUGIN ) )).$template);
        if( $themeTemplate ) {
            return $themeTemplate;
        }

		// check if requested template exist in plugin which uses our hook.
		$pluginTemplate = plugin_dir_path(apply_filters('personio_integration_set_template_directory', WP_PERSONIO_INTEGRATION_PLUGIN)).'templates/'.$template;
		if( file_exists( $pluginTemplate ) ) {
			return $pluginTemplate;
		}

		// return template from light-plugin.
	    return plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN).'templates/'.$template;
    }

    /**
     * Return an array with supported languages resorted with the default language as first entry.
     *
     * @return array
     */
    public static function getActiveLanguagesWithDefaultFirst(): array
    {
        $newArray = [];
        $langKey = get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY);
        $newArray[$langKey] = WP_PERSONIO_INTEGRATION_LANGUAGES[$langKey];
        return array_merge($newArray, WP_PERSONIO_INTEGRATION_LANGUAGES);
    }

    /**
     * Checks if the current request is a WP REST API request.
     *
     * Case #1: After WP_REST_Request initialisation
     * Case #2: Support "plain" permalink settings and check if `rest_route` starts with `/`
     * Case #3: It can happen that WP_Rewrite is not yet initialized,
     *          so do this (wp-settings.php)
     * Case #4: URL Path begins with wp-json/ (your REST prefix)
     *          Also supports WP installations in sub-folders
     *
     * @returns boolean
     * @author matzeeable
     */
    public static function is_admin_api_request(): bool {
        if (defined('REST_REQUEST') && REST_REQUEST // (#1)
            || isset($_GET['rest_route']) // (#2)
            && strpos( $_GET['rest_route'], '/', 0 ) === 0)
            return true;

        // (#3)
        global $wp_rewrite;
        if ($wp_rewrite === null) $wp_rewrite = new WP_Rewrite();

        // (#4)
        $rest_url = wp_parse_url( trailingslashit( rest_url( ) ) );
        $current_url = wp_parse_url( add_query_arg( array( ) ) );
        return strpos( $current_url['path'], $rest_url['path'], 0 ) === 0;
    }

    /**
     * Check and secure the allowed shortcode-attributes.
     *
     * @param array $attribute_defaults
     * @param array $attribute_settings
     * @param array $attributes
     * @return array
     */
    public static function get_shortcode_attributes( array $attribute_defaults, array $attribute_settings, array $attributes ): array
    {
        // pre-filter the given attributes
        $filtered = apply_filters('personio_integration_get_shortcode_attributes', ['defaults' => $attribute_defaults, 'settings' => $attribute_settings, 'attributes' => $attributes]);

        // get pre-filtered array
        $attribute_defaults = $filtered['defaults'];
        $attribute_settings = $filtered['settings'];
        $attributes = $filtered['attributes'];

        // concat the lists
        $attributes = shortcode_atts($attribute_defaults, $attributes);

        // check if language-setting is valid
        if( empty(WP_PERSONIO_INTEGRATION_LANGUAGES[$attributes['lang']]) ) {
            $attributes['lang'] = WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY;
        }

        // check each attribute depending on its setting
        foreach( $attributes as $name => $attribute ) {
            if( !empty($attribute_settings[$name]) ) {
                if ($attribute_settings[$name] == "array") {
                    if( !empty($attribute) ) {
                        if( !is_array($attribute) ) {
                            $attributes[$name] = array_map('trim', explode(",", $attribute));
                        }
                        else {
                            $attributes[$name] = $attribute;
                        }
                    }
                    else {
                        $attributes[$name] = [];
                    }
                }
                if ($attribute_settings[$name] == "int") {
                    $attributes[$name] = absint($attribute);
                }
                if ($attribute_settings[$name] == "unsignedint") {
                    $attributes[$name] = (int)$attribute;
                }
                if ($attribute_settings[$name] == "bool") {
                    $attributes[$name] = boolval($attribute);
                }
                if ($attribute_settings[$name] == "filter") {
                    // if filter is set in config
                    $attributes[$name] = absint($attribute);
                    // if filter is set via request
                    if( !empty($_GET['personiofilter'][$name]) ) {
                        $attributes[$name] = absint($_GET['personiofilter'][$name]);
                    }
                }
            }
        }

        // return the resulting array with checked and secured attributes
        return $attributes;
    }

    /**
     * Format a given datetime with WP-settings and functions.
     *
     * @param $date
     * @return string
     */
    public static function get_format_date_time( $date ): string
    {
        $dt = get_date_from_gmt($date);
        return date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($dt));
    }

    /**
     * Checks whether a given plugin is active.
     *
     * Used because WP's own function is_plugin_active() is not accessible everywhere.
     *
     * @param $plugin
     * @return bool
     */
    public static function is_plugin_active( $plugin ): bool
    {
        return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
    }

    /**
     * Return the absolute url to the plugin.
     *
     * @return string
     */
    public static function getPluginPath(): string
    {
        return trailingslashit(plugin_dir_url(WP_PERSONIO_INTEGRATION_PLUGIN));
    }

    /**
     * Return whether the PersonioURL is set or not.
     *
     * @return bool
     */
    public static function is_personioUrl_set(): bool
    {
        $url = get_option('personioIntegrationUrl', '');
        return !empty($url);
    }

    /**
     * Return whether the current theme is a block-theme
     *
     * @return bool
     */
    public static function theme_is_fse_theme(): bool
    {
        if ( function_exists( 'wp_is_block_theme' ) ) {
            return (bool)wp_is_block_theme();
        }
        return false;
    }

    /**
     * Return the active Wordpress-language depending on our own support.
     * If language is unknown for our plugin, use english.
     *
     * @return string
     * @noinspection PhpUnused
     */
    public static function get_wp_lang(): string
    {
        $wpLang = substr(get_bloginfo('language'), 0, 2);

        /**
         * Consider the main language set in Polylang for the web page
         */
        if( self::is_plugin_active('polylang/polylang.php') && function_exists('pll_default_language') ) {
            $wpLang = pll_default_language();
        }

        /**
         * Consider the main language set in WPML for the web page
         */
        if( self::is_plugin_active('sitepress-multilingual-cms/sitepress.php') ) {
            $wpLang = apply_filters('wpml_default_language', NULL );
        }

        // if language not set, use default language
        if( empty($wpLang) ) {
            $wpLang = WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY;
        }

        // if language is not known, use default language
        $languages = helper::get_supported_languages();
        if (empty($languages[$wpLang])) {
            $wpLang = WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY;
        }
        return $wpLang;
    }

    /**
     * Return the current language depending on our own support.
     * If language is unknown for our plugin, use english.
     *
     * @return string
     * @noinspection PhpUnused
     */
    public static function get_current_lang(): string
    {
        $wpLang = substr(get_bloginfo('language'), 0, 2);

        /**
         * Consider the main language set in Polylang for the web page
         */
        if( self::is_plugin_active('polylang/polylang.php') && function_exists('pll_current_language') ) {
            $wpLang = pll_current_language();
        }

        /**
         * Consider the main language set in WPML for the web page
         */
        if( self::is_plugin_active('sitepress-multilingual-cms/sitepress.php') ) {
            $wpLang = apply_filters('wpml_current_language', NULL );
        }

        // if language not set, use default language
        if( empty($wpLang) ) {
            $wpLang = WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY;
        }

        // if language is not known, use default language
        $languages = helper::get_supported_languages();
        if (empty($languages[$wpLang])) {
            $wpLang = WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY;
        }
        return $wpLang;
    }

    /**
     * Check if Settings-Errors-entry already exists in array.
     *
     * @param $entry
     * @param $array
     * @return false
     */
    public static function checkIfSettingErrorEntryExistsInArray( $entry, $array ): bool
    {
        foreach( $array as $item ) {
            if( $item['setting'] == $entry ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get taxonomy-labels.
     *
     * @param $taxonomy
     * @return array
     */
    public static function get_taxonomy_label($taxonomy): array
    {
        $locale = get_locale();
        if( !is_admin() ) {
            switch_to_locale(self::get_plugin_locale());
        }
        $array = [
            WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY => [
                'name' => _x( 'recruiting categories', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'recruiting category', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search recruiting category', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit recruiting category', 'wp-personio-integration' ),
                'update_item' => __( 'Update recruiting category', 'wp-personio-integration' ),
                'menu_name' => __( 'recruiting categories', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY => [
                'name' => _x( 'occupation categories', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'occupation category', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search occupation category', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit occupation category', 'wp-personio-integration' ),
                'update_item' => __( 'Update occupation category', 'wp-personio-integration' ),
                'menu_name' => __( 'occupation categories', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE => [
                'name' => _x( 'offices', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'office', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search office', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit office', 'wp-personio-integration' ),
                'update_item' => __( 'Update office', 'wp-personio-integration' ),
                'menu_name' => __( 'offices', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT => [
                'name' => _x( 'departments', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'department', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search department', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit department', 'wp-personio-integration' ),
                'update_item' => __( 'Update department', 'wp-personio-integration' ),
                'menu_name' => __( 'departments', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE => [
                'name' => _x( 'employment types', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'employment type', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search employment type', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit employment type', 'wp-personio-integration' ),
                'update_item' => __( 'Update employment type', 'wp-personio-integration' ),
                'menu_name' => __( 'employment types', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY => [
                'name' => _x( 'seniority', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'seniority', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search seniority', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit seniority', 'wp-personio-integration' ),
                'update_item' => __( 'Update seniority', 'wp-personio-integration' ),
                'menu_name' => __( 'seniority', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE => [
                'name' => _x( 'schedules', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'schedule', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search schedule', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit schedule', 'wp-personio-integration' ),
                'update_item' => __( 'Update schedule', 'wp-personio-integration' ),
                'menu_name' => __( 'schedules', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE => [
                'name' => _x( 'experiences', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'experience', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search experience', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit experience', 'wp-personio-integration' ),
                'update_item' => __( 'Update experience', 'wp-personio-integration' ),
                'menu_name' => __( 'experiences', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES => [
                'name' => _x( 'languages', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'language', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search language', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit language', 'wp-personio-integration' ),
                'update_item' => __( 'Update language', 'wp-personio-integration' ),
                'menu_name' => __( 'languages', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS => [
	            'name' => _x( 'keywords', 'taxonomy general name', 'wp-personio-integration' ),
	            'singular_name' => _x( 'keyword', 'taxonomy singular name', 'wp-personio-integration' ),
	            'search_items' =>  __( 'Search keywords', 'wp-personio-integration' ),
	            'edit_item' => __( 'Edit keyword', 'wp-personio-integration' ),
	            'update_item' => __( 'Update keyword', 'wp-personio-integration' ),
	            'menu_name' => __( 'keywords', 'wp-personio-integration' ),
            ]
        ];
        // revert the locale-setting
        if( !is_admin() ) {
            switch_to_locale($locale);
        }
        if( empty($array[$taxonomy]) ) {
            $array[$taxonomy] = [];
        }
        return apply_filters('personio_integration_filter_taxonomy_label', $array[$taxonomy], $taxonomy);
    }

    /**
     * Get language-specific defaults for a taxonomy.
     *
     * @param $taxonomy
     * @return array
     */
    public static function get_taxonomy_defaults($taxonomy): array
    {
        // set language in frontend to read the texts depending on main-language
        $locale = get_locale();
        if( !is_admin() ) {
            switch_to_locale(self::get_plugin_locale());
        }
        $array = [
            WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE => [
                'permanent' => __('permanent', 'wp-personio-integration'),
                'intern' => __('intern', 'wp-personio-integration'),
                'trainee' => __('trainee', 'wp-personio-integration'),
                'freelance' => __('freelance', 'wp-personio-integration')
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY => [
                'entry-level' => __('entry-level', 'wp-personio-integration'),
                'experienced' => __('experienced', 'wp-personio-integration'),
                'executive' => __('executive', 'wp-personio-integration'),
                'student' => __('student', 'wp-personio-integration')
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE => [
                'full-time' => __('full-time', 'wp-personio-integration'),
                'part-time' => __('part-time', 'wp-personio-integration'),
                'full-or-part-time' => __('full- or part-time', 'wp-personio-integration')
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE => [
                'lt-1' => __('less than 1 year', 'wp-personio-integration'),
                '1-2' => __('1-2 years', 'wp-personio-integration'),
                '2-5' => __('2-5 years', 'wp-personio-integration'),
                '5-7' => __('5-7 years', 'wp-personio-integration'),
                '7-10' => __('7-10 years', 'wp-personio-integration'),
                '10-15' => __('10-15 years', 'wp-personio-integration'),
                'ht15' => __('more than 15 years', 'wp-personio-integration'),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES => helper::get_supported_languages()
        ];
        // revert the locale-setting
        if( !is_admin() ) {
            switch_to_locale($locale);
        }
        if( empty($array[$taxonomy]) ) {
            return [];
        }
        return $array[$taxonomy];
    }

    /**
     * Get the plugin-local as 4 char string.
     *
     * @return string
     */
    private static function get_plugin_locale(): string
    {
        $newLocale = get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE);
        switch( $newLocale ) {
            case 'en':
                $newLocale = $newLocale.'_US';
                break;
            case 'de':
                $newLocale = $newLocale.'_DE';
                break;
            case 'it':
                $newLocale = $newLocale.'_IT';
                break;
            case 'fr':
                $newLocale = $newLocale.'_FR';
                break;
            case 'pt':
                $newLocale = $newLocale.'_PT';
                break;
            case 'nl':
                $newLocale = $newLocale.'_NL';
                break;
        }
        return $newLocale;
    }

    /**
     * Check if this message has been dismissed.
     *
     * @param $transient
     * @return bool
     * @noinspection PhpUnused
     */
    public static function is_transient_not_dismissed( $transient ): bool
    {
        $db_record   = self::get_admin_transient_cache( $transient );

        if ( 'forever' === $db_record ) {
            return false;
        } elseif ( absint( $db_record ) >= time() ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the transient dismissed time.
     *
     * @param $transient
     * @return false|int|mixed
     */
    private static function get_admin_transient_cache( $transient ) {
        if ( ! $transient ) {
            return false;
        }
        $cache_key = 'pi-dismissed-' . md5( $transient );
        $timeout   = get_option( $cache_key );
        $timeout   = 'forever' === $timeout ? time() + 60 : $timeout;

        if ( empty( $timeout ) || time() > $timeout ) {
            return false;
        }

        return $timeout;
    }

    /**
     * Return an array of the supported languages.
     *
     * @return mixed|void
     * @noinspection PhpUnused
     */
    public static function get_supported_languages() {
        return apply_filters('personio_integration_supported_languages', WP_PERSONIO_INTEGRATION_LANGUAGES_COMPLETE);
    }

    /**
     * Prüfe, ob der Import per CLI aufgerufen wird.
     * Z.B. um einen Fortschrittsbalken anzuzeigen.
     *
     * @return bool
     */
    public static function isCLI(): bool
    {
        return defined( 'WP_CLI' ) && WP_CLI;
    }

    /**
     * Get generated Personio-application-URL.
     *
     * @param $position
     * @param bool $without_application
     * @return string
     */
    public static function get_personio_application_url( $position, $without_application = false ): string
    {
        if( $without_application ) {
            return get_option('personioIntegrationUrl', '').'/job/'.absint($position->getPersonioId()).'?display='.get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY);
        }
        return get_option('personioIntegrationUrl', '').'/job/'.absint($position->getPersonioId()).'?display='.get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY).'#apply';
    }

    /**
     * Get current url for frontend.
     *
     * @return string
     */
    public static function get_current_url(): string {
        // set page url.
        $page_url = '';

        // get actual object.
        $object = get_queried_object();
        if( $object instanceof WP_Post_Type ) {
            $page_url = get_post_type_archive_link($object->name);
        }
        if( $object instanceof WP_Post ) {
            $page_url = get_permalink($object->ID);
        }

        // return result
        return $page_url;
    }

    /**
     * Regex to get html tag attribute value
     */
    public static function get_attribute_value_from_html($attrib, $tag): string
    {
        //get attribute from html tag
        $re = '/' . preg_quote($attrib) . '=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';
        if (preg_match($re, $tag, $match)) {
            return urldecode($match[2]);
        }
        return false;
    }

    /**
     * Get all files of directory recursively.
     *
     * @param $path
     * @param $list
     * @return array
     * @noinspection PhpMissingParamTypeInspection
     */
    public static function get_file_from_directory( $path = '.', $list = [] ): array
    {
        $ignore = array( '.', '..' );
        $dh = @opendir( $path );
        while( false !== ( $file = readdir( $dh ) ) )
        {
            if( !in_array( $file, $ignore ) )
            {
                $filepath = $path.'/'.$file;
                if( is_dir( $filepath ) )
                {
                    $list = self::get_file_from_directory( $filepath, $list );
                }
                else
                {
                    $list[$file] = $filepath;
                }
            }
        }
        closedir( $dh );
        return $list;
    }

    /**
     * Return the Personio-XML-URL without any parameter.
     *
     * @param $domain
     * @return string
     */
    public static function get_personio_xml_url( $domain ): string {
        if( empty($domain) ) {
            return '';
        }
        return $domain.'/xml';
    }

	/**
	 * Get language-specific personio account login url.
	 *
	 * @return string
	 */
	public static function get_personio_login_url(): string {
		if( 'de' === self::get_current_lang() ) {
			return 'https://www.personio.de/login/';
		}
		return 'https://www.personio.com/login/';
	}

	/**
	 * Get language-specific personio account support url.
	 *
	 * @return string
	 */
	public static function get_personio_support_url(): string {
		if( 'de' === self::get_current_lang() ) {
			return 'https://support.personio.de/';
		}
		return 'https://support.personio.de/hc/en-us/';
	}


}