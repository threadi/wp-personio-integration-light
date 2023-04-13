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

    $attribute_defaults = [
        'templates' => $templates,
        'excerpt' => $excerptTemplates,
        'donotlink' => $doNotLink,
        'personioid' => $attributes['id']
    ];

    // get the output
    return personio_integration_position_shortcode( $attribute_defaults );
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

    $attribute_defaults = [
        'templates' => $templates,
        'excerpt' => $excerptTemplates,
        'donotlink' => $doNotLink,
        'sort' => $attributes["sort"],
        'sortby' => $attributes["sortby"],
        'groupby' => $attributes["groupby"],
        'limit' => $attributes["limit"],
        'filter' => implode(",", $attributes['filter']),
        'filtertype' => $attributes['filtertype'],
        'showfilter' => $attributes['showFilter']
    ];

    // get the output
    return personio_integration_positions_shortcode( apply_filters( 'personio_integration_get_gutenberg_list_attributes', $attributes, $attribute_defaults) );
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
            ]
        ];
        $list_attributes = apply_filters('personio_integration_gutenberg_block_list_attributes', $list_attributes);

        // register list block
        register_block_type(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN).'blocks/list/', [
            'render_callback' => 'personio_integration_get_list',
            'attributes' => $list_attributes
        ]);
        wp_set_script_translations('wp-personio-integration-show-editor-script', 'wp-personio-integration', trailingslashit(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN)) . 'languages/');
        wp_set_script_translations('wp-personio-integration-list-editor-script', 'wp-personio-integration', trailingslashit(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN)) . 'languages/');
    }
}
add_action( 'init', 'personio_integration_add_blocks', 10 );