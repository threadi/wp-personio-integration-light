<?php
/**
 * File with general helper tasks for the plugin.
 *
 * @package wp-personio-integration
 */

namespace personioIntegration;

use WP_Post;
use WP_Post_Type;
use WP_Rewrite;

/**
 * The helper class itself.
 */
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
		        sprintf(__('We realized that you are using Divi - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Divi.', 'wp-personio-integration'), helper::get_pro_url()),
	        ),
            'personio_integration_elementor' => sprintf(
	            '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
	            __('Personio Integration', 'wp-personio-integration'),
	            /* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
	            sprintf(__('We realized that you are using Elementor - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Elementor.', 'wp-personio-integration'), helper::get_pro_url()),
            ),
            'personio_integration_wpbakery' => sprintf(
	            '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
	            __('Personio Integration', 'wp-personio-integration'),
	            /* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
	            sprintf(__('We realized that you are using WPBakery - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in WPBakery.', 'wp-personio-integration'), helper::get_pro_url()),
            ),
            'personio_integration_beaver' => sprintf(
	            '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
	            __('Personio Integration', 'wp-personio-integration'),
	            /* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
	            sprintf(__('We realized that you are using Beaver Builder - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Beaver Builder.', 'wp-personio-integration'), helper::get_pro_url()),
            ),
            'personio_integration_siteorigin' => sprintf(
	            '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
	            __('Personio Integration', 'wp-personio-integration'),
	            /* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
	            sprintf(__('We realized that you are using Site Origin - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Site Origin.', 'wp-personio-integration'), helper::get_pro_url()),
            ),
            'personio_integration_themify' => sprintf(
	            '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
	            __('Personio Integration', 'wp-personio-integration'),
	            /* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
	            sprintf(__('We realized that you are using Themify - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Themify.', 'wp-personio-integration'), helper::get_pro_url()),
            ),
            'personio_integration_avada' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                /* translators: %1$s will be replaced by the URL to the Pro-version-info-page. */
                sprintf(__('We realized that you are using Avada - very nice! <a href="%s" target="_blank"><i>Personio Integration Pro</i> (opens new window)</a> allows you to design the output of positions in Avada.', 'wp-personio-integration'), helper::get_pro_url()),
            ),
            'personio_integration_admin_show_review_hint' => sprintf(
                '<h3>'.helper::getLogoImg().'%s</h3><p>%s</p>',
                __('Personio Integration', 'wp-personio-integration'),
                sprintf(
                /* translators: %1$s is replaced with "string" */
                    sprintf(__('Your use the WordPress-plugin Personio Integration since more than %d days. Do you like it? Feel free to <a href="https://wordpress.org/plugins/personio-integration-light/#reviews" target="_blank">leave us a review (opens new window)</a>.', 'wp-personio-integration'), ( absint(get_option( 'personioIntegrationLightInstallDate', 1 ) - time () ) / 60 / 60 / 24 )).' <span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span>',
                    helper::get_pro_url()
                )
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
            case 'occupation_detail':
                $name = $position->getOccupationName();
                break;
            case 'employmenttype':
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
            case 'occupation_detail':
                $taxonomy = WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION;
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
        $filtered = apply_filters('personio_integration_get_shortcode_attributes', array( 'defaults' => $attribute_defaults, 'settings' => $attribute_settings, 'attributes' => $attributes ) );

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
     * Return whether the current theme is a block-theme.
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
         * Consider the main language set in Polylang for the web page.
         */
        if( self::is_plugin_active('polylang/polylang.php') && function_exists('pll_default_language') ) {
            $wpLang = pll_default_language();
        }

        /**
         * Consider the main language set in WPML for the web page.
         */
        if( self::is_plugin_active('sitepress-multilingual-cms/sitepress.php') ) {
            $wpLang = apply_filters('wpml_default_language', NULL );
        }

        // if language not set, use default language.
        if( empty($wpLang) ) {
            $wpLang = WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY;
        }

        // if language is not known, use default language.
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

        // if language not set, use default language.
        if( empty($wpLang) ) {
            $wpLang = WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY;
        }

        // if language is not known, use default language.
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
        $array = array(
            WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY => array(
                'name' => _x( 'Categories', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'Category', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search category', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit category', 'wp-personio-integration' ),
                'update_item' => __( 'Update category', 'wp-personio-integration' ),
                'menu_name' => __( 'Categories', 'wp-personio-integration' ),
            ),
            WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY => [
                'name' => _x( 'Job types', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'Job type', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search Job type', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit Job type', 'wp-personio-integration' ),
                'update_item' => __( 'Update Job type', 'wp-personio-integration' ),
                'menu_name' => __( 'Job types', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION => [
                'name' => _x( 'Job type details', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'Job type detail', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search Job type detail', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit Job type detail', 'wp-personio-integration' ),
                'update_item' => __( 'Update Job type detail', 'wp-personio-integration' ),
                'menu_name' => __( 'Job type details', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE => [
                'name' => _x( 'Locations', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'Location', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search location', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit location', 'wp-personio-integration' ),
                'update_item' => __( 'Update location', 'wp-personio-integration' ),
                'menu_name' => __( 'Locations', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT => [
                'name' => _x( 'Departments', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'Department', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search department', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit department', 'wp-personio-integration' ),
                'update_item' => __( 'Update department', 'wp-personio-integration' ),
                'menu_name' => __( 'Departments', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE => [
                'name' => _x( 'Employment types', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'Employment type', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search employment type', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit employment type', 'wp-personio-integration' ),
                'update_item' => __( 'Update employment type', 'wp-personio-integration' ),
                'menu_name' => __( 'Employment types', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY => [
                'name' => _x( 'Experiences', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'Experience', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search Experience', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit Experience', 'wp-personio-integration' ),
                'update_item' => __( 'Update Experience', 'wp-personio-integration' ),
                'menu_name' => __( 'Experiences', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE => [
                'name' => _x( 'Contract types', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'Contract type', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search Contract type', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit Contract type', 'wp-personio-integration' ),
                'update_item' => __( 'Update Contract type', 'wp-personio-integration' ),
                'menu_name' => __( 'Contract types', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE => [
                'name' => _x( 'Years of experiences', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'Years of experience', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search years of experience', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit years of experience', 'wp-personio-integration' ),
                'update_item' => __( 'Update years of experience', 'wp-personio-integration' ),
                'menu_name' => __( 'Years of experiences', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES => [
                'name' => _x( 'Languages', 'taxonomy general name', 'wp-personio-integration' ),
                'singular_name' => _x( 'Language', 'taxonomy singular name', 'wp-personio-integration' ),
                'search_items' =>  __( 'Search language', 'wp-personio-integration' ),
                'edit_item' => __( 'Edit language', 'wp-personio-integration' ),
                'update_item' => __( 'Update language', 'wp-personio-integration' ),
                'menu_name' => __( 'Languages', 'wp-personio-integration' ),
            ],
            WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS => array(
	            'name' => _x( 'Keywords', 'taxonomy general name', 'wp-personio-integration' ),
	            'singular_name' => _x( 'Keyword', 'taxonomy singular name', 'wp-personio-integration' ),
	            'search_items' =>  __( 'Search Keywords', 'wp-personio-integration' ),
	            'edit_item' => __( 'Edit Keyword', 'wp-personio-integration' ),
	            'update_item' => __( 'Update keyword', 'wp-personio-integration' ),
	            'menu_name' => __( 'Keywords', 'wp-personio-integration' ),
            )
        );
        // revert the locale-setting.
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
        $array = array(
            WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY => array(
                'accounting_and_finance' => __('Accounting/Finance', 'wp-personio-integration'),
                'administrative_and_clerical' => __('Administrative/Clerical', 'wp-personio-integration'),
                'banking_and_real_estate' => __('Banking/Real Estate/Mortgage Professionals', 'wp-personio-integration'),
                'building_construction_and_skilled_trades' => __('Building Construction/Skilled Trades', 'wp-personio-integration'),
                'business_and_strategic_development' => __('Business/Strategic Management', 'wp-personio-integration'),
                'creative_and_design' => __('Creative/Design', 'wp-personio-integration'),
                'customer_support_and_client_care' => __('Customer Support/Client Care', 'wp-personio-integration'),
                'editorial_and_writing' => __('Editorial/Writing', 'wp-personio-integration'),
                'engineering' => __('Engineering', 'wp-personio-integration'),
                'food_services_and_hospitality' => __('Food Services/Hospitality', 'wp-personio-integration'),
                'human_resources' => __('Human Resources', 'wp-personio-integration'),
                'installation_and_maintenance_repair' => __('Installation/Maintenance/Repair', 'wp-personio-integration'),
                'it_software' => __('IT/Software Development', 'wp-personio-integration'),
                'legal' => __('Legal', 'wp-personio-integration'),
                'logistics_and_transportation' => __('Logistics/Transportation', 'wp-personio-integration'),
                'marketing_and_product' => __('Marketing/Product', 'wp-personio-integration'),
                'medical_health' => __('Medical/Health', 'wp-personio-integration'),
                'other' => __('Other', 'wp-personio-integration'),
                'production_and_operations' => __('Production/Operations', 'wp-personio-integration'),
                'project_and_program_management' => __('Project/Program Management', 'wp-personio-integration'),
                'quality_assurance_and_saftey' => __('Quality Assurance/Safety', 'wp-personio-integration'),
                'rd_and_science' => __('R&D/Science', 'wp-personio-integration'),
                'sales_and_business_development' => __('Sales/Business Development', 'wp-personio-integration'),
                'security_and_protective_services' => __('Security/Protective Services', 'wp-personio-integration'),
                'training_instruction' => __('Training/Instruction', 'wp-personio-integration'),
            ),
            WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION => array(
                'acturial_analysis' => __('Actuarial Analysis', 'wp-personio-integration'),
                'bookkeeping' => __('Bookkeeping/General Ledger', 'wp-personio-integration'),
                'financial_control' => __('Financial Control', 'wp-personio-integration'),
                'corporate_finance' => __('Corporate Finance', 'wp-personio-integration'),
                'accounts_payable_and_receivable' => __('Accounts Payable/Receivable', 'wp-personio-integration'),
                'financial_reporting' => __('Financial Planning/Advising', 'wp-personio-integration'),
                'financial_analysis' => __('Financial Analysis/Research/Reporting', 'wp-personio-integration'),
                'corporate_accounting' => __('Corporate Accounting', 'wp-personio-integration'),
                'fund_accounting' => __('Fund Accounting', 'wp-personio-integration'),
                'claims_review' => __('Claims Review and Adjusting', 'wp-personio-integration'),
                'securities_analysis' => __('Securities Analysis/Research', 'wp-personio-integration'),
                'real_estate_appraisal' => __('Real Estate Appraisal', 'wp-personio-integration'),
                'real_estate_leasing' => __('Real Estate Leasing/Acquisition', 'wp-personio-integration'),
                'collections' => __('Collections', 'wp-personio-integration'),
                'investment_management' => __('Investment Management', 'wp-personio-integration'),
                'credit_review' => __('Credit Review/Analysis', 'wp-personio-integration'),
                'risk_management' => __('Risk Management/Compliance', 'wp-personio-integration'),
                'tax_assessment' => __('Tax Assessment and Collections', 'wp-personio-integration'),
                'tax_accounting' => __('Tax Accounting', 'wp-personio-integration'),
                'policy_underwriting' => __('Policy Underwriting', 'wp-personio-integration'),
                'financial_products_sales' => __('Financial Products Sales/Brokerage', 'wp-personio-integration'),
                'audit' => __('Audit', 'wp-personio-integration'),
                'general_other_accounting_finance' => __('General/Other: Accounting/Finance', 'wp-personio-integration'),
                'records_management' => __('Filing/Records Management', 'wp-personio-integration'),
                'executive_support' => __('Executive Support', 'wp-personio-integration'),
                'data_entry' => __('Data Entry/Order Processing', 'wp-personio-integration'),
                'reception' => __('Front Desk/Reception', 'wp-personio-integration'),
                'property_management' => __('Property Management', 'wp-personio-integration'),
                'office_management' => __('Office Management', 'wp-personio-integration'),
                'administrative' => __('Administrative Support', 'wp-personio-integration'),
                'claims_processing' => __('Claims Processing', 'wp-personio-integration'),
                'transaction' => __('Transcription', 'wp-personio-integration'),
                'secretary' => __('Paralegal & Legal Secretary', 'wp-personio-integration'),
                'general_other_administrative_clerical' => __('General/Other: Administrative/Clerical', 'wp-personio-integration'),
                'loan_officer_and_originator' => __('Loan Officer/Originator', 'wp-personio-integration'),
                'escrow_officer_and_manager' => __('Escrow Officer/Manager', 'wp-personio-integration'),
                'store_and_branch_management' => __('Store/Branch Management', 'wp-personio-integration'),
                'mortgage_broker' => __('Mortgage Broker', 'wp-personio-integration'),
                'real_estate_agent_and_broker' => __('Real Estate Agent/Broker', 'wp-personio-integration'),
                'real_estate_law' => __('Real Estate Law', 'wp-personio-integration'),
                'credit_manager' => __('Credit Manager', 'wp-personio-integration'),
                'bank_teller' => __('Bank Teller', 'wp-personio-integration'),
                'underwriter' => __('Underwriter', 'wp-personio-integration'),
                'title_officer_and_closer' => __('Title Officer/Closer', 'wp-personio-integration'),
                'site_superintendent' => __('Site Superintendent', 'wp-personio-integration'),
                'concrete_and_masonry' => __('Concrete and Masonry', 'wp-personio-integration'),
                'heavy_equipment_operation' => __('Heavy Equipment Operation', 'wp-personio-integration'),
                'roofing' => __('Roofing', 'wp-personio-integration'),
                'electrician' => __('Electrician', 'wp-personio-integration'),
                'flooring_and_tiling_and_painting_and_wallpapering' => __('Flooring/Tiling/Painting/Wallpapering', 'wp-personio-integration'),
                'hvac' => __('HVAC', 'wp-personio-integration'),
                'plumbing_and_pipefitting' => __('Plumbing/Pipefitting', 'wp-personio-integration'),
                'ironwork_and_metal_fabrication' => __('Ironwork/Metal Fabrication', 'wp-personio-integration'),
                'cad_and_drafting' => __('CAD/Drafting', 'wp-personio-integration'),
                'surveying' => __('Surveying', 'wp-personio-integration'),
                'sheetrock_and_plastering' => __('Sheetrock/Plastering', 'wp-personio-integration'),
                'carpentry_and_framing' => __('Carpentry/Framing', 'wp-personio-integration'),
                'general_and_other_building_construction_and_skilled_trades' => __('General/Other: Construction/Skilled Trades', 'wp-personio-integration'),
                'business_analysis_and_research' => __('Business Analysis/Research', 'wp-personio-integration'),
                'managerial_consulting' => __('Managerial Consulting', 'wp-personio-integration'),
                'franchise_business_ownership' => __('Franchise-Business Ownership', 'wp-personio-integration'),
                'business_unit_management' => __('Business Unit Management', 'wp-personio-integration'),
                'president_and_top_executive' => __('President/Top Executive', 'wp-personio-integration'),
                'public_health_administration' => __('Public Health Administration', 'wp-personio-integration'),
                'hotel_and_lodging_management' => __('Hotel/Lodging Management', 'wp-personio-integration'),
                'hospital_and_clinic_administration' => __('Hospital/Clinic Administration', 'wp-personio-integration'),
                'mergers_and_acquisitions' => __('Mergers and Acquisitions', 'wp-personio-integration'),
                'restaurant_management' => __('Restaurant Management', 'wp-personio-integration'),
                'school_and_college_administration' => __('School/College Administration', 'wp-personio-integration'),
                'town_and_city_planning' => __('Town/City Planning', 'wp-personio-integration'),
                'strategic_planning_and_intelligence' => __('Strategic Planning/Intelligence', 'wp-personio-integration'),
                'general_and_other_business_and_strategic_management' => __('General/Other: Business/Strategic Management', 'wp-personio-integration'),
                'architecture_and_interior_design' => __('Architecture/Interior Design', 'wp-personio-integration'),
                'computer_animation_multimedia' => __('Computer Animation & Multimedia', 'wp-personio-integration'),
                'creative_direction_and_lead' => __('Creative Direction/Lead', 'wp-personio-integration'),
                'graphic_arts_and_illustration' => __('Graphic Arts/Illustration', 'wp-personio-integration'),
                'industrial_design' => __('Industrial Design', 'wp-personio-integration'),
                'fashion_accessories_design' => __('Fashion & Accessories Design', 'wp-personio-integration'),
                'photography_and_videography' => __('Photography and Videography', 'wp-personio-integration'),
                'web_and_ui_and_ux_design' => __('Web/UI/UX Design', 'wp-personio-integration'),
                'advertising_writing_creative' => __('Advertising Writing (Creative)', 'wp-personio-integration'),
                'general_and_other_creative_and_design' => __('General/Other: Creative/Design', 'wp-personio-integration'),
                'call_center' => __('Call Center', 'wp-personio-integration'),
                'flight_attendant' => __('Flight Attendant', 'wp-personio-integration'),
                'hair_cutting_and_styling' => __('Hair Cutting/Styling', 'wp-personio-integration'),
                'retail_customer_service' => __('Retail Customer Service', 'wp-personio-integration'),
                'account_management_non_commissioned' => __('Account Management (Non-Commissioned)', 'wp-personio-integration'),
                'customer_training' => __('Customer Training', 'wp-personio-integration'),
                'reservations_and_ticketing' => __('Reservations/Ticketing', 'wp-personio-integration'),
                'general_and_other_customer_support_and_client_care' => __('General/Other: Customer Support/Client Care', 'wp-personio-integration'),
                'technical_customer_service' => __('Technical Customer Service', 'wp-personio-integration'),
                'documentation_and_technical_writing' => __('Documentation/Technical Writing', 'wp-personio-integration'),
                'journalism' => __('Journalism', 'wp-personio-integration'),
                'digital_content_development' => __('Digital Content Development', 'wp-personio-integration'),
                'editing_proofreading' => __('Editing & Proofreading', 'wp-personio-integration'),
                'general_and_other_editorial_and_writing' => __('General/Other: Editorial/Writing', 'wp-personio-integration'),
                'translation_and_interpretation' => __('Translation/Interpretation', 'wp-personio-integration'),
                'civil__structural_engineering' => __('Civil & Structural Engineering', 'wp-personio-integration'),
                'bio_engineering' => __('Bio-Engineering', 'wp-personio-integration'),
                'chemical_engineering' => __('Chemical Engineering', 'wp-personio-integration'),
                'electrical_and_electronics_engineering' => __('Electrical/Electronics Engineering', 'wp-personio-integration'),
                'energy_and_nuclear_engineering' => __('Energy/Nuclear Engineering', 'wp-personio-integration'),
                'rf_and_wireless_engineering' => __('RF/Wireless Engineering', 'wp-personio-integration'),
                'aeronautic_and_avionic_engineering' => __('Aeronautic/Avionic Engineering', 'wp-personio-integration'),
                'mechanical_engineering' => __('Mechanical Engineering', 'wp-personio-integration'),
                'systems_and_process_engineering' => __('Systems/Process Engineering', 'wp-personio-integration'),
                'industrial_and_manufacturing_engineering' => __('Industrial/Manufacturing Engineering', 'wp-personio-integration'),
                'naval_architecture_and_marine_engineering' => __('Naval Architecture/Marine Engineering', 'wp-personio-integration'),
                'environmental_and_geological_engineering' => __('Environmental and Geological Engineering', 'wp-personio-integration'),
                'general_and_other_engineering' => __('General/Other: Engineering', 'wp-personio-integration'),
                'food_beverage_serving' => __('Food & Beverage Serving', 'wp-personio-integration'),
                'host_and_hostess' => __('Host/Hostess', 'wp-personio-integration'),
                'guest_services_and_concierge' => __('Guest Services/Concierge', 'wp-personio-integration'),
                'food_preparation_and_cooking' => __('Food Preparation/Cooking', 'wp-personio-integration'),
                'guide_tour' => __('Guide (Tour)', 'wp-personio-integration'),
                'front_desk_and_reception' => __('Front Desk/Reception', 'wp-personio-integration'),
                'wine_steward_sommelier' => __('Wine Steward (Sommelier)', 'wp-personio-integration'),
                'general_and_other_food_services_and_hospitality' => __('General/Other: Food Services', 'wp-personio-integration'),
                'corporate_development_and_training' => __('Corporate Development and Training', 'wp-personio-integration'),
                'compensation_and_benefits_policy' => __('Compensation/Benefits Policy', 'wp-personio-integration'),
                'diversity_management_and_eeo_and_compliance' => __('Diversity Management/EEO/Compliance', 'wp-personio-integration'),
                'academic_admissions_and_advising' => __('Academic Admissions and Advising', 'wp-personio-integration'),
                'payroll_and_benefits_administration' => __('Payroll and Benefits Administration', 'wp-personio-integration'),
                'recruiting_and_sourcing' => __('Recruiting/Sourcing', 'wp-personio-integration'),
                'hr_systems_administration' => __('HR Systems Administration', 'wp-personio-integration'),
                'general_and_other_human_resources' => __('General/Other: Human Resources', 'wp-personio-integration'),
                'computer_and_electronics_and_telecomm_install_and_maintain_and_repair' => __('Computer/Electronics/Telecomm Install/Maintain/Repair', 'wp-personio-integration'),
                'oil_rig_pipeline_install_and_maintain_and_repair' => __('Oil Rig & Pipeline Install/Maintain/Repair', 'wp-personio-integration'),
                'facilities_maintenance' => __('Facilities Maintenance', 'wp-personio-integration'),
                'janitorial_cleaning' => __('Janitorial & Cleaning', 'wp-personio-integration'),
                'vehicle_repair_and_maintenance' => __('Vehicle Repair and Maintenance', 'wp-personio-integration'),
                'wire_and_cable_install_and_maintain_and_repair' => __('Wire and Cable Install/Maintain/Repair', 'wp-personio-integration'),
                'landscaping' => __('Landscaping', 'wp-personio-integration'),
                'equipment_install_and_maintain_and_repair' => __('Equipment Install/Maintain/Repair', 'wp-personio-integration'),
                'locksmith' => __('Locksmith', 'wp-personio-integration'),
                'general_and_other_installation_and_maintenance_and_repair' => __('General/Other: Installation/Maintenance/Repair', 'wp-personio-integration'),
                'usability_and_information_architecture' => __('Usability/Information Architecture', 'wp-personio-integration'),
                'desktop_service_and_support' => __('Desktop Service and Support', 'wp-personio-integration'),
                'computer_and_network_security' => __('Computer/Network Security', 'wp-personio-integration'),
                'database_development_and_administration' => __('Database Development/Administration', 'wp-personio-integration'),
                'enterprise_software_implementation_consulting' => __('Enterprise Software Implementation & Consulting', 'wp-personio-integration'),
                'it_project_management' => __('IT Project Management', 'wp-personio-integration'),
                'software_and_system_architecture' => __('Software/System Architecture', 'wp-personio-integration'),
                'software_and_web_development' => __('Software/Web Development', 'wp-personio-integration'),
                'network_and_server_administration' => __('Network and Server Administration', 'wp-personio-integration'),
                'systems_analysis__it' => __('Systems Analysis - IT', 'wp-personio-integration'),
                'telecommunications_administration_and_management' => __('Telecommunications Administration/Management', 'wp-personio-integration'),
                'general_and_other_it_software' => __('General/Other: IT/Software Development', 'wp-personio-integration'),
                'labor__employment_law' => __('Labor & Employment Law', 'wp-personio-integration'),
                'patent_and_ip_law' => __('Patent/IP Law', 'wp-personio-integration'),
                'regulatory_and_compliance_law' => __('Regulatory/Compliance Law', 'wp-personio-integration'),
                'tax_law' => __('Tax Law', 'wp-personio-integration'),
                'attorney' => __('Attorney', 'wp-personio-integration'),
                'contracts_administration' => __('Contracts Administration', 'wp-personio-integration'),
                'paralegal__legal_secretary' => __('Paralegal & Legal Secretary', 'wp-personio-integration'),
                'general_and_other_legal' => __('General/Other: Legal', 'wp-personio-integration'),
                'car_van_and_bus_driving' => __('Car, Van and Bus Driving', 'wp-personio-integration'),
                'train_or_rail_operator' => __('Train or Rail Operator', 'wp-personio-integration'),
                'purchasing_goods_and_services' => __('Purchasing Goods and Services', 'wp-personio-integration'),
                'piloting_air_and_marine' => __('Piloting: Air and Marine', 'wp-personio-integration'),
                'cargo_and_baggage_handling' => __('Cargo and Baggage Handling', 'wp-personio-integration'),
                'hazardous_materials_handling' => __('Hazardous Materials Handling', 'wp-personio-integration'),
                'merchandise_planning_and_buying' => __('Merchandise Planning and Buying', 'wp-personio-integration'),
                'import_and_export_administration' => __('Import/Export Administration', 'wp-personio-integration'),
                'cost_estimating' => __('Cost Estimating', 'wp-personio-integration'),
                'messenger_and_courier' => __('Messenger/Courier', 'wp-personio-integration'),
                'truck_driving' => __('Truck Driving', 'wp-personio-integration'),
                'supplier_management_and_vendor_management' => __('Supplier Management/Vendor Management', 'wp-personio-integration'),
                'equipment_and_forklift_and_crane_operation' => __('Equipment/Forklift/Crane Operation', 'wp-personio-integration'),
                'inventory_planning_and_management' => __('Inventory Planning and Management', 'wp-personio-integration'),
                'vehicle_dispatch_routing_and_scheduling' => __('Vehicle Dispatch, Routing and Scheduling', 'wp-personio-integration'),
                'shipping_and_receiving_and_warehousing' => __('Shipping and Receiving/Warehousing', 'wp-personio-integration'),
                'general_and_other_logistics_and_transportation' => __('General/Other: Logistics/Transportation', 'wp-personio-integration'),
                'visual_and_display_merchandising' => __('Visual/Display Merchandising', 'wp-personio-integration'),
                'brand_and_product_marketing' => __('Brand/Product Marketing', 'wp-personio-integration'),
                'direct_marketing_crm' => __('Direct Marketing (CRM)', 'wp-personio-integration'),
                'events_and_promotional_marketing' => __('Events/Promotional Marketing', 'wp-personio-integration'),
                'investor_and_public_and_media_relations' => __('Investor and Public/Media Relations', 'wp-personio-integration'),
                'marketing_communications' => __('Marketing Communications', 'wp-personio-integration'),
                'market_research' => __('Market Research', 'wp-personio-integration'),
                'media_planning_and_buying' => __('Media Planning and Buying', 'wp-personio-integration'),
                'marketing_production_and_traffic' => __('Marketing Production/Traffic', 'wp-personio-integration'),
                'product_management' => __('Product Management', 'wp-personio-integration'),
                'telemarketing' => __('Telemarketing', 'wp-personio-integration'),
                'copy_writing_and_editing' => __('Copy Writing/Editing', 'wp-personio-integration'),
                'general_and_other_marketing_and_product' => __('General/Other: Marketing/Product', 'wp-personio-integration'),
                'healthcare_aid' => __('Healthcare Aid', 'wp-personio-integration'),
                'pharmacy' => __('Pharmacy', 'wp-personio-integration'),
                'nutrition_and_diet' => __('Nutrition and Diet', 'wp-personio-integration'),
                'nursing' => __('Nursing', 'wp-personio-integration'),
                'laboratory_and_pathology' => __('Laboratory/Pathology', 'wp-personio-integration'),
                'physician_assistant_and_nurse_practitioner' => __('Physician Assistant/Nurse Practitioner', 'wp-personio-integration'),
                'optical' => __('Optical', 'wp-personio-integration'),
                'medical_therapy_and_rehab_services' => __('Medical Therapy/Rehab Services', 'wp-personio-integration'),
                'medical_practitioner' => __('Medical Practitioner', 'wp-personio-integration'),
                'mental_health' => __('Mental Health', 'wp-personio-integration'),
                'medical_imaging' => __('Medical Imaging', 'wp-personio-integration'),
                'emt_and_paramedic' => __('EMT/Paramedic', 'wp-personio-integration'),
                'social_service' => __('Social Service', 'wp-personio-integration'),
                'sports_medicine' => __('Sports Medicine', 'wp-personio-integration'),
                'veterinary_and_animal_care' => __('Veterinary/Animal Care', 'wp-personio-integration'),
                'dental_assistant_and_hygienist' => __('Dental Assistant/Hygienist', 'wp-personio-integration'),
                'dental_practitioner' => __('Dental Practitioner', 'wp-personio-integration'),
                'general_and_other_medical_and_health' => __('General/Other: Medical/Health', 'wp-personio-integration'),
                'work_at_home' => __('Work at Home', 'wp-personio-integration'),
                'career_fair' => __('Career Fair', 'wp-personio-integration'),
                'other' => __('Other', 'wp-personio-integration'),
                'waste_pick_up_and_removal' => __('Waste Pick-up and Removal', 'wp-personio-integration'),
                'operations_and_plant_management' => __('Operations/Plant Management', 'wp-personio-integration'),
                'equipment_operations' => __('Equipment Operations', 'wp-personio-integration'),
                'scientific_and_technical_production' => __('Scientific/Technical Production', 'wp-personio-integration'),
                'layout_prepress_printing_binding_operations' => __('Layout, Prepress, Printing, & Binding Operations', 'wp-personio-integration'),
                'assembly_and_assembly_line' => __('Assembly/Assembly Line', 'wp-personio-integration'),
                'moldmaking_and_casting' => __('Moldmaking/Casting', 'wp-personio-integration'),
                'metal_fabrication_and_welding' => __('Metal Fabrication and Welding', 'wp-personio-integration'),
                'audio_and_video_broadcast_postproduction' => __('Audio/Video Broadcast & Postproduction', 'wp-personio-integration'),
                'sewing_and_tailoring' => __('Sewing and Tailoring', 'wp-personio-integration'),
                'laundry_and_dry_cleaning_operations' => __('Laundry and Dry-Cleaning Operations', 'wp-personio-integration'),
                'machining_and_cnc' => __('Machining/CNC', 'wp-personio-integration'),
                'general_and_other_production_and_operations' => __('General/Other: Production/Operations', 'wp-personio-integration'),
                'event_planning_and_coordination' => __('Event Planning/Coordination', 'wp-personio-integration'),
                'program_management' => __('General/Other: Project/Program Management', 'wp-personio-integration'),
                'project_management' => __('IT Project Management', 'wp-personio-integration'),
                'general_and_other_project_and_program_management' => __('General/Other: Project/Program Management', 'wp-personio-integration'),
                'occupational_health_and_safety' => __('Occupational Health and Safety', 'wp-personio-integration'),
                'building_and_construction_inspection' => __('Building/Construction Inspection', 'wp-personio-integration'),
                'fraud_investigation' => __('Fraud Investigation', 'wp-personio-integration'),
                'iso_certification' => __('ISO Certification', 'wp-personio-integration'),
                'food_safety_and_inspection' => __('Food Safety and Inspection', 'wp-personio-integration'),
                'production_quality_assurance' => __('Production Quality Assurance', 'wp-personio-integration'),
                'six_sigma_and_black_belt_and_tqm' => __('Six Sigma/Black Belt/TQM', 'wp-personio-integration'),
                'software_quality_assurance' => __('Software Quality Assurance', 'wp-personio-integration'),
                'vehicle_inspection' => __('Vehicle Inspection', 'wp-personio-integration'),
                'environmental_protection_and_conservation' => __('Environmental Protection/Conservation', 'wp-personio-integration'),
                'general_and_other_quality_assurance_and_safety' => __('General/Other: Quality Assurance/Safety', 'wp-personio-integration'),
                'biological_and_chemical_research' => __('Biological/Chemical Research', 'wp-personio-integration'),
                'materials_and_physical_research' => __('Materials/Physical Research', 'wp-personio-integration'),
                'mathematical_and_statistical_research' => __('Mathematical/Statistical Research', 'wp-personio-integration'),
                'clinical_research' => __('Clinical Research', 'wp-personio-integration'),
                'new_product_rd' => __('New Product R&D', 'wp-personio-integration'),
                'pharmaceutical_research' => __('Pharmaceutical Research', 'wp-personio-integration'),
                'environmental_and_geological_testing_analysis' => __('Environmental/Geological Testing & Analysis', 'wp-personio-integration'),
                'general_and_other_r_and_d_and_science' => __('General/Other: R&D/Science', 'wp-personio-integration'),
                'account_management_commissioned' => __('Account Management (Commissioned)', 'wp-personio-integration'),
                'field_sales' => __('Field Sales', 'wp-personio-integration'),
                'business_development_and_new_accounts' => __('Business Development/New Accounts', 'wp-personio-integration'),
                'retail_and_counter_sales_and_cashier' => __('Retail/Counter Sales and Cashier', 'wp-personio-integration'),
                'wholesale_and_reselling_sales' => __('Wholesale/Reselling Sales', 'wp-personio-integration'),
                'international_sales' => __('International Sales', 'wp-personio-integration'),
                'fundraising' => __('Fundraising', 'wp-personio-integration'),
                'technical_presales_support__technical_sales' => __('Technical Presales Support & Technical Sales', 'wp-personio-integration'),
                'telesales' => __('Telesales', 'wp-personio-integration'),
                'travel_agent_and_ticket_sales' => __('Travel Agent/Ticket Sales', 'wp-personio-integration'),
                'media_and_advertising_sales' => __('Media and Advertising Sales', 'wp-personio-integration'),
                'insurance_agent_and_broker' => __('Insurance Agent/Broker', 'wp-personio-integration'),
                'sales_support_and_assistance' => __('Sales Support/Assistance', 'wp-personio-integration'),
                'financial_products_sales_and_brokerage' => __('Financial Products Sales/Brokerage', 'wp-personio-integration'),
                'general_and_other_sales_and_business_development' => __('General/Other: Sales/Business Development', 'wp-personio-integration'),
                'customs_and_immigration' => __('Customs/Immigration', 'wp-personio-integration'),
                'firefighting_and_rescue' => __('Firefighting and Rescue', 'wp-personio-integration'),
                'airport_security_and_screening' => __('Airport Security and Screening', 'wp-personio-integration'),
                'store_security_and_loss_prevention' => __('Store Security/Loss Prevention', 'wp-personio-integration'),
                'security_intelligence_analysis' => __('Security Intelligence & Analysis', 'wp-personio-integration'),
                'police_law_enforcement' => __('Police-Law Enforcement', 'wp-personio-integration'),
                'security_guard' => __('Security Guard', 'wp-personio-integration'),
                'correctional_officer' => __('Correctional Officer', 'wp-personio-integration'),
                'military_combat' => __('Military Combat', 'wp-personio-integration'),
                'general_and_other_security_and_protective_services' => __('General/Other: Security/Protective Services', 'wp-personio-integration'),
                'corporate_development' => __('Corporate Development and Training', 'wp-personio-integration'),
                'continuing_and_adult' => __('Continuing/Adult', 'wp-personio-integration'),
                'elementary_school' => __('Elementary School', 'wp-personio-integration'),
                'software_and_web_training' => __('Software/Web Training', 'wp-personio-integration'),
                'early_childhood_care' => __('Early Childhood Care & Development', 'wp-personio-integration'),
                'university' => __('University', 'wp-personio-integration'),
                'junior_and_high_school' => __('Junior/High School', 'wp-personio-integration'),
                'classroom_teaching' => __('Classroom Teaching', 'wp-personio-integration'),
                'special_education' => __('Special Education', 'wp-personio-integration'),
                'fitness_and_sports' => __('Fitness & Sports Training/Instruction', 'wp-personio-integration'),
                'general_other_training_and_instruction' => __('General/Other: Training/Instruction', 'wp-personio-integration'),
            ),
            WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE => array(
                'permanent' => __('Permanent employee', 'wp-personio-integration'),
                'intern' => __('Intern', 'wp-personio-integration'),
                'trainee' => __('Trainee', 'wp-personio-integration'),
                'freelance' => __('Freelance', 'wp-personio-integration'),
                'temporary' => __('Temporary', 'wp-personio-integration'),
                'working_student' => __('Working Student', 'wp-personio-integration')
            ),
            WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE => array(
                'full-time' => __('full-time', 'wp-personio-integration'),
                'part-time' => __('part-time', 'wp-personio-integration'),
                'full-or-part-time' => __('full- or part-time', 'wp-personio-integration')
            ),
            WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY => array(
                'entry-level' => __('entry-level', 'wp-personio-integration'),
                'experienced' => __('experienced', 'wp-personio-integration'),
                'executive' => __('executive', 'wp-personio-integration'),
                'student' => __('student', 'wp-personio-integration')
            ),
            WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE => array(
                'lt-1' => __('less than 1 year', 'wp-personio-integration'),
                '1-2' => __('1-2 years', 'wp-personio-integration'),
                '2-5' => __('2-5 years', 'wp-personio-integration'),
                '5-7' => __('5-7 years', 'wp-personio-integration'),
                '7-10' => __('7-10 years', 'wp-personio-integration'),
                '10-15' => __('10-15 years', 'wp-personio-integration'),
                'ht15' => __('more than 15 years', 'wp-personio-integration'),
            ),
            WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES => helper::get_supported_languages()
        );
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

    /**
     * Set the import schedule.
     *
     * @return void
     */
    public static function set_import_schedule(): void {
        if ( !wp_next_scheduled('personio_integration_schudule_events') ) {
            wp_schedule_event( time(), get_option('personioIntegrationPositionScheduleInterval'), 'personio_integration_schudule_events' );
        }
    }
}
