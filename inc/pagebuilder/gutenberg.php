<?php

use personioIntegration\gutenberg\templates;
use personioIntegration\helper;
use personioIntegration\Positions;

/**
 * Gutenberg-Callback to get the content for single position.
 *
 * @param $attributes
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_single( $attributes ): string
{
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

    // get block-classes
    $block_html_attributes = get_block_wrapper_attributes();

    // get styles
    $stylesArray = [];
    $styles = helper::get_attribute_value_from_html('style', $block_html_attributes);
    if( !empty($styles) ) {
        $stylesArray[] = '.entry.' . $class . ' { ' . $styles . ' }';
    }

    $attribute_defaults = [
        'templates' => personio_integration_get_gutenberg_templates($attributes),
        'excerpt' => $excerptTemplates,
        'donotlink' => $doNotLink,
        'personioid' => $attributes['id'],
        'styles' => implode(PHP_EOL, $stylesArray),
        'classes' => $class.' '.helper::get_attribute_value_from_html('class', $block_html_attributes)
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
    $templates = personio_integration_get_gutenberg_templates($attributes);

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

    // get block-classes
    $block_html_attributes = get_block_wrapper_attributes();

    // get styles
    $stylesArray = [];
    $styles = helper::get_attribute_value_from_html('style', $block_html_attributes);
    if( !empty($styles) ) {
        $stylesArray[] = '.' . $class . ' { ' . $styles . ' }';
    }
    if (!empty($attributes['style']) && !empty($attributes['style']['spacing']) && !empty($attributes['style']['spacing']['blockGap'])) {
        $value = $attributes['style']['spacing']['blockGap'];
        // convert var-setting to var-style-entity
        if(false !== strpos($attributes['style']['spacing']['blockGap'], 'var:')) {
            $value = str_replace('|', '--', $value);
            $value = str_replace('var:', '', $value);
            $value = 'var(--wp--' . $value . ')';
        }
        $stylesArray[] = 'body .' . $class . ' { margin-bottom: ' . $value . '; }';
    }

    // collect all settings for this block
    $attributes = [
        'templates' => $templates,
        'excerpt' => $excerptTemplates,
        'donotlink' => $doNotLink,
        'sort' => $attributes["sort"],
        'sortby' => $attributes["sortby"],
        'groupby' => $attributes["groupby"],
        'limit' => absint($attributes["limit"]),
        'filter' => implode(",", $attributes['filter']),
        'filtertype' => $attributes['filtertype'],
        'showfilter' => $attributes['showFilter'],
        'show_back_to_list' => '',
        'styles' => implode(PHP_EOL, $stylesArray),
        'classes' => $class.' '.helper::get_attribute_value_from_html('class', $block_html_attributes)
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

    // get block-classes
    $block_html_attributes = get_block_wrapper_attributes();

    // get styles
    $stylesArray = [];
    $styles = helper::get_attribute_value_from_html('style', $block_html_attributes);
    if( !empty($styles) ) {
        $stylesArray[] = '.' . $class . ' { ' . $styles . ' }';
    }

    $stylesArray = [];
    if( !empty($class) ) {
        if (!empty($attributes['hideResetLink'])) {
            $stylesArray[] = '.entry.' . $class . ' .personio-position-filter-reset { display: none }';
        }
        if (!empty($attributes['hideFilterTitle'])) {
            $stylesArray[] = '.entry.' . $class . ' legend { display: none }';
        }
        if (!empty($attributes['space_between'])) {
            $stylesArray[] = '.entry.' . $class . ' .personio-position-filter-linklist > div { margin-right: ' . $attributes['space_between'] . 'px }';
        }
    }

    // collect all settings for this block
    $attributes = [
        'templates' => '',
        'filter' => implode(",", $attributes['filter']),
        'filtertype' => 'linklist',
        'showfilter' => true,
        'styles' => implode(PHP_EOL, $stylesArray),
        'classes' => $class.' '.helper::get_attribute_value_from_html('class', $block_html_attributes)
    ];

    // get the output
    return personio_integration_positions_shortcode( apply_filters( 'personio_integration_get_gutenberg_filter_list_attributes', $attributes) );
}

/**
 * Gutenberg-Callback to get the filter as select-boxes.
 *
 * @param $attributes
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_filter_select( $attributes ): string
{
    // set ID as class
    $class = '';
    if( !empty($attributes['blockId']) ) {
        $class = 'personio-integration-block-' . $attributes['blockId'];
    }

    // get block-classes
    $block_html_attributes = get_block_wrapper_attributes();

    // get styles
    $stylesArray = [];
    $styles = helper::get_attribute_value_from_html('style', $block_html_attributes);
    if( !empty($styles) ) {
        $stylesArray[] = '.' . $class . ' { ' . $styles . ' }';
    }

    $stylesArray = [];
    if( !empty($class) ) {
        if (!empty($attributes['hideResetLink'])) {
            $stylesArray[] = '.entry.' . $class . ' .personio-position-filter-reset { display: none }';
        }
        if (!empty($attributes['hideSubmitButton'])) {
            $stylesArray[] = '.entry.' . $class . ' button { display: none }';
        }
        if (!empty($attributes['hideFilterTitle'])) {
            $stylesArray[] = '.entry.' . $class . ' legend { display: none }';
        }
    }

    // collect all settings for this block
    $attributes = [
        'templates' => '',
        'filter' => implode(",", $attributes['filter']),
        'filtertype' => 'select',
        'showfilter' => true,
        'styles' => implode(PHP_EOL, $stylesArray),
        'classes' => $class.' '.helper::get_attribute_value_from_html('class', $block_html_attributes)
    ];

    // get the output
    return personio_integration_positions_shortcode( apply_filters( 'personio_integration_get_gutenberg_filter_select_attributes', $attributes) );
}

/**
 * Return application-button.
 *
 * @param $attributes
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_get_application_button( $attributes ): string
{
    // get positions object
    $positions = positions::get_instance();

    // get the position as object
    // -> is no id is available choose a random one (e.g. for preview in Gutenberg)
    $post_id = get_the_ID();
    if( empty($post_id) ) {
        $position_array = $positions->getPositions(1);
        $position = $position_array[0];
    }
    else {
        $position = $positions->get_position($post_id);
    }
    if( !$position->isValid() ) {
        return '';
    }

    // set ID as class
    $class = '';
    if( !empty($attributes['blockId']) ) {
        $class = 'personio-integration-block-' . $attributes['blockId'];
    }

    // get block-classes
    $block_html_attributes = get_block_wrapper_attributes();

    // get styles
    $stylesArray = [];
    $styles = helper::get_attribute_value_from_html('style', $block_html_attributes);
    if( !empty($styles) ) {
        $stylesArray[] = '.entry.' . $class . ' { ' . $styles . ' }';
    }

    $attribute_defaults = [
        'personioid' => absint($position->getPersonioId()),
        'templates' => ['formular'],
        'styles' => implode(PHP_EOL, $stylesArray),
        'classes' => $class.' '.helper::get_attribute_value_from_html('class', $block_html_attributes)
    ];

    // get the output
    return personio_integration_position_shortcode( apply_filters( 'personio_integration_get_gutenberg_application_button_attributes', $attribute_defaults ) );
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
            'preview' => [
                'type' => 'boolean',
                'default' => false
            ],
            'showFilter' => [
                'type' => 'boolean',
                'default' => false
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
            'preview' => [
                'type' => 'boolean',
                'default' => false
            ],
            'filter' => [
                'type' => 'array',
                'default' => ['recruitingCategory','schedule','office']
            ],
             'blockId' => [
                'type' => 'string',
                'default' => ''
            ],
            'hideResetLink' => [
                'type' => 'boolean',
                'default' => false
            ],
            'hideFilterTitle' => [
                'type' => 'boolean',
                'default' => false
            ],
            'space_between' => [
                'type' => 'integer',
                'default' => 0
            ]
        ];
        $list_attributes = apply_filters('personio_integration_gutenberg_block_filter_list_attributes', $list_attributes);

        // register filter-list block
        register_block_type(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN).'blocks/filter-list/', [
            'render_callback' => 'personio_integration_get_filter_list',
            'attributes' => $list_attributes
        ]);

        // collect attributes for filter-select block
        $list_attributes = [
            'preview' => [
                'type' => 'boolean',
                'default' => false
            ],
            'filter' => [
                'type' => 'array',
                'default' => ['recruitingCategory','schedule','office']
            ],
            'blockId' => [
                'type' => 'string',
                'default' => ''
            ],
            'hideResetLink' => [
                'type' => 'boolean',
                'default' => false
            ],
            'hideSubmitButton' => [
                'type' => 'boolean',
                'default' => false
            ],
            'hideFilterTitle' => [
                'type' => 'boolean',
                'default' => false
            ]
        ];
        $list_attributes = apply_filters('personio_integration_gutenberg_block_filter_select_attributes', $list_attributes);

        // register filter-list block
        register_block_type(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN).'blocks/filter-select/', [
            'render_callback' => 'personio_integration_get_filter_select',
            'attributes' => $list_attributes
        ]);

        // collect attributes for application-button block
        $list_attributes = [
            'preview' => [
                'type' => 'boolean',
                'default' => false
            ],
            'blockId' => [
                'type' => 'string',
                'default' => ''
            ]
        ];
        $list_attributes = apply_filters('personio_integration_gutenberg_block_application_button_select_attributes', $list_attributes);

        // register application-button block
        register_block_type(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN).'blocks/application-button/', [
            'render_callback' => 'personio_integration_get_application_button',
            'attributes' => $list_attributes
        ]);

        // register translations
        wp_set_script_translations('wp-personio-integration-show-editor-script', 'wp-personio-integration', trailingslashit(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN)) . 'languages/');
        wp_set_script_translations('wp-personio-integration-list-editor-script', 'wp-personio-integration', trailingslashit(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN)) . 'languages/');
        wp_set_script_translations('wp-personio-integration-filter-list-editor-script', 'wp-personio-integration', trailingslashit(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN)) . 'languages/');
        wp_set_script_translations('wp-personio-integration-filter-select-editor-script', 'wp-personio-integration', trailingslashit(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN)) . 'languages/');
    }
}
add_action( 'init', 'personio_integration_add_blocks', 10 );

/**
 * Generate template-string from given attributes.
 *
 * @param $attributes
 * @return string
 */
function personio_integration_get_gutenberg_templates( $attributes ): string
{
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
    return $templates;
}

/**
 * Initialize the block template handling.
 *
 * @return void
 */
function personio_integration_add_templates(): void
{
    $templates_obj = templates::get_instance();
    $templates_obj->init();
}
add_action( 'init', 'personio_integration_add_templates', 10 );