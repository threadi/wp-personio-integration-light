<?php

use personioIntegration\helper;

/**
 * Gutenberg-Callback to get the content for single position.
 *
 * @param $attributes
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_single( $attributes ): string
{
    // collect the configured templates
    $templates = '';
    if( $attributes["showTitle"] ) {
        $templates .= (strlen($templates) > 0 ? ',': '').'title';
    }
    if( $attributes["showExcerpt"] ) {
        $templates .= (strlen($templates) > 0 ? ',': '').'excerpt';
    }
    if( $attributes["showContent"] ) {
        $templates .= (strlen($templates) > 0 ? ',': '').'content';
    }
    if( $attributes["showApplicationForm"] ) {
        $templates .= (strlen($templates) > 0 ? ',': '').'formular';
    }

    // get the excerpt-templates
    $excerptTemplates = '';
    if( !empty($attributes["excerptTemplates"]) ) {
        $excerptTemplates = implode(",", $attributes["excerptTemplates"]);
    }

    // link title?
    $doNotLink = true;
    if( $attributes["linkTitle"] ) {
        $doNotLink = false;
    }

    // set ID as class
    $class = '';
    if( !empty($attributes['blockId']) ) {
        $class = 'personio-integration-block-' . $attributes['blockId'];
    }

    $stylesArray = [];
    if( !empty($class) ) {
        // generate styles
        if (!empty($attributes['textColor'])) {
            $stylesArray[] = '.wp-block-post-content .' . $class . ' { color: ' . $attributes['textColor'] . ' }';
        }
        if (!empty($attributes['backgroundColor'])) {
            $stylesArray[] = '.wp-block-post-content .' . $class . ' { background-color: ' . $attributes['backgroundColor'] . ' }';
        }
        if (!empty($attributes['linkColor'])) {
            $stylesArray[] = '.wp-block-post-content .' . $class . ' a { color: ' . $attributes['linkColor'] . ' }';
        }
    }

    $attribute_defaults = [
        'templates' => $templates,
        'excerpt' => $excerptTemplates,
        'donotlink' => $doNotLink,
        'personioid' => $attributes['id'],
        'styles' => implode(PHP_EOL, $stylesArray),
        'classes' => $class
    ];

    // get the output
    return personio_integration_position_shortcode( apply_filters( 'personio_integration_get_gutenberg_single_attributes', $attribute_defaults ) );
}

/**
 * Gutenberg-Callback to get the list of positions.
 *
 * @param $attributes
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_list( $attributes ): string
{
    // collect the configured templates
    $templates = '';
    if( $attributes["showTitle"] ) {
        $templates .= (strlen($templates) > 0 ? ',': '').'title';
    }
    if( $attributes["showExcerpt"] ) {
        $templates .= (strlen($templates) > 0 ? ',': '').'excerpt';
    }
    if( $attributes["showContent"] ) {
        $templates .= (strlen($templates) > 0 ? ',': '').'content';
    }
    if( $attributes["showApplicationForm"] ) {
        $templates .= (strlen($templates) > 0 ? ',': '').'formular';
    }

    // get the excerpt-templates
    $excerptTemplates = '';
    if( !empty($attributes["excerptTemplates"]) ) {
        $excerptTemplates = implode(",", $attributes["excerptTemplates"]);
    }

    // link title?
    $doNotLink = true;
    if( $attributes["linkTitle"] ) {
        $doNotLink = false;
    }

    // set ID as class
    $class = '';
    if( !empty($attributes['blockId']) ) {
        $class = 'personio-integration-block-' . $attributes['blockId'];
    }

    $stylesArray = [];
    if( !empty($class) ) {
        if (!empty($attributes['textColor'])) {
            $stylesArray[] = '.wp-block-post-content .' . $class . ' { color: ' . $attributes['textColor'] . ' }';
        }
        if (!empty($attributes['backgroundColor'])) {
            $stylesArray[] = '.wp-block-post-content .' . $class . ' { background-color: ' . $attributes['backgroundColor'] . ' }';
        }
        if (!empty($attributes['linkColor'])) {
            $stylesArray[] = '.wp-block-post-content .' . $class . ' a { color: ' . $attributes['linkColor'] . ' }';
        }
        if (!empty($attributes['style']) && !empty($attributes['style']['spacing']) && !empty($attributes['style']['spacing']['blockGap'])) {
            $value = $attributes['style']['spacing']['blockGap'];
            // convert var-setting to var-style-entity
            if (strpos($attributes['style']['spacing']['blockGap'], 'var:')) {
                $value = str_replace('|', '--', $value);
                $value = str_replace('var:', '', $value);
                $value = 'var(--wp--' . $value . ')';
            }
            $stylesArray[] = '.wp-block-post-content .' . $class . ' { margin-bottom: ' . $value . '; }';
        }
    }

    // collect all settings for this block
    $attributes = [
        'templates' => $templates,
        'excerpt' => $excerptTemplates,
        'donotlink' => $doNotLink,
        'sort' => $attributes["sort"],
        'sortby' => $attributes["sortby"],
        'groupby' => $attributes["groupby"],
        'limit' => $attributes["limit"],
        'filter' => implode(",", $attributes['filter']),
        'filtertype' => $attributes['filtertype'],
        'showfilter' => $attributes['showFilter'],
        'show_back_to_list' => '',
        'styles' => implode(PHP_EOL, $stylesArray),
        'classes' => $class
    ];

    // get the output
    return personio_integration_positions_shortcode( apply_filters( 'personio_integration_get_gutenberg_list_attributes', $attributes) );
}

/**
 * Gutenberg-Callback to get the filter as linklist.
 *
 * @param $attributes
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_filter_list( $attributes ): string
{
    // set ID as class
    $class = '';
    if( !empty($attributes['blockId']) ) {
        $class = 'personio-integration-block-' . $attributes['blockId'];
    }

    $stylesArray = [];
    if( !empty($class) ) {
        if (!empty($attributes['textColor'])) {
            $stylesArray[] = '.wp-block-post-content .' . $class . ' { color: ' . $attributes['textColor'] . ' }';
        }
        if (!empty($attributes['backgroundColor'])) {
            $stylesArray[] = '.wp-block-post-content .' . $class . ' { background-color: ' . $attributes['backgroundColor'] . ' }';
        }
        if (!empty($attributes['linkColor'])) {
            $stylesArray[] = '.wp-block-post-content .' . $class . ' a { color: ' . $attributes['linkColor'] . ' }';
        }
    }

    // collect all settings for this block
    $attributes = [
        'templates' => '',
        'filter' => implode(",", $attributes['filter']),
        'filtertype' => 'linklist',
        'showfilter' => true,
        'styles' => implode(PHP_EOL, $stylesArray),
        'classes' => $class
    ];

    // get the output
    return personio_integration_positions_shortcode( apply_filters( 'personio_integration_get_gutenberg_filter_list_attributes', $attributes) );
}

/**
 * Register the Gutenberg-Blocks with all necessary settings.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_add_blocks(): void
{
    // include Blocks only if Gutenberg exists and the PersonioURL is set
    if( function_exists('register_block_type') && helper::is_personioUrl_set() ) {
        // collect attributes for single block
        $single_attributes = [
            'id' => [
                'type' => 'integer',
                'default' => 0
            ],
            'showTitle' => [
                'type' => 'boolean',
                'default' => true
            ],
            'linkTitle' => [
                'type' => 'boolean',
                'default' => false
            ],
            'showExcerpt' => [
                'type' => 'boolean',
                'default' => false
            ],
            'excerptTemplates' => [
                'type' => 'array',
                'default' => ['recruitingCategory','schedule','office']
            ],
            'showContent' => [
                'type' => 'boolean',
                'default' => true
            ],
            'showApplicationForm' => [
                'type' => 'boolean',
                'default' => true
            ],
            'blockId' => [
                'type' => 'string'
            ],
            'textColor' => [
                'type' => 'string'
            ],
            'linkColor' => [
                'type' => 'string'
            ],
            'backgroundColor' => [
                'type' => 'string'
            ]
        ];
        $single_attributes = apply_filters('personio_integration_gutenberg_block_single_attributes', $single_attributes);

        // register single block
        register_block_type(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN).'blocks/show/', [
            'render_callback' => 'personio_integration_get_single',
            'attributes' => $single_attributes
        ]);

        // collect attributes for list block
        $list_attributes = [
            'showFilter' => [
                'type' => 'boolean',
                'default' => true
            ],
            'filter' => [
                'type' => 'array',
                'default' => ['recruitingCategory','schedule','office']
            ],
            'filtertype' => [
                'type' => 'string',
                'default' => 'linklist'
            ],
            'limit' => [
                'type' => 'integer',
                'default' => 0
            ],
            'sort' => [
                'type' => 'string',
                'default' => 'asc'
            ],
            'sortby' => [
                'type' => 'string',
                'default' => 'title'
            ],
            'groupby' => [
                'type' => 'string',
                'default' => ''
            ],
            'showTitle' => [
                'type' => 'boolean',
                'default' => true
            ],
            'linkTitle' => [
                'type' => 'boolean',
                'default' => true
            ],
            'showExcerpt' => [
                'type' => 'boolean',
                'default' => true
            ],
            'excerptTemplates' => [
                'type' => 'array',
                'default' => ['recruitingCategory','schedule','office']
            ],
            'showContent' => [
                'type' => 'boolean',
                'default' => false
            ],
            'showApplicationForm' => [
                'type' => 'boolean',
                'default' => false
            ],
            'blockId' => [
                'type' => 'string',
                'default' => ''
            ],
            'textColor' => [
                'type' => 'string'
            ],
            'linkColor' => [
                'type' => 'string'
            ],
            'backgroundColor' => [
                'type' => 'string'
            ]
        ];
        $list_attributes = apply_filters('personio_integration_gutenberg_block_list_attributes', $list_attributes);

        // register list block
        register_block_type(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN).'blocks/list/', [
            'render_callback' => 'personio_integration_get_list',
            'attributes' => $list_attributes
        ]);

        // collect attributes for filter-list block
        $list_attributes = [
            'filter' => [
                'type' => 'array',
                'default' => ['recruitingCategory','schedule','office']
            ],
             'blockId' => [
                'type' => 'string',
                'default' => ''
            ],
            'textColor' => [
                'type' => 'string'
            ],
            'linkColor' => [
                'type' => 'string'
            ],
            'backgroundColor' => [
                'type' => 'string'
            ]
        ];
        $list_attributes = apply_filters('personio_integration_gutenberg_block_filter_list_attributes', $list_attributes);

        // register filter-list block
        register_block_type(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN).'blocks/filter-list/', [
            'render_callback' => 'personio_integration_get_filter_list',
            'attributes' => $list_attributes
        ]);

        // register translations
        wp_set_script_translations('wp-personio-integration-show-editor-script', 'wp-personio-integration', trailingslashit(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN)) . 'languages/');
        wp_set_script_translations('wp-personio-integration-list-editor-script', 'wp-personio-integration', trailingslashit(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN)) . 'languages/');
        wp_set_script_translations('wp-personio-integration-filter-list-editor-script', 'wp-personio-integration', trailingslashit(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN)) . 'languages/');
    }
}
add_action( 'init', 'personio_integration_add_blocks', 10 );