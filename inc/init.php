<?php

use personioIntegration\helper;
use personioIntegration\Import;
use personioIntegration\Position;
use personioIntegration\updates;

/**
 * General initialization.
 *
 * Must priority of 0 to load before widgets_init, which run with priority 1 during init-run.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_init() {
    load_plugin_textdomain( 'wp-personio-integration', false, dirname( plugin_basename( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . '/languages' );
}
add_action( 'init', 'personio_integration_init', -1 );

/**
 * Add position as custom posttype.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_add_position_posttype() {
    $labels = [
        'name'                => __( 'Positions', 'wp-personio-integration' ),
        'singular_name'       => __( 'Position', 'wp-personio-integration'),
        'menu_name'           => __( 'Positions', 'wp-personio-integration'),
        'parent_item_colon'   => __( 'Parent Position', 'wp-personio-integration'),
        'all_items'           => __( 'All Positions', 'wp-personio-integration'),
        'view_item'           => __( 'View Position', 'wp-personio-integration'),
        'view_items'          => __( 'View Positions', 'wp-personio-integration'),
        'search_items'        => __( 'Search Position', 'wp-personio-integration'),
        'not_found'           => __( 'Not Found', 'wp-personio-integration'),
        'not_found_in_trash'  => __( 'Not found in Trash', 'wp-personio-integration')
    ];

    // get the slugs
    $archiveSlug = apply_filters('personio_integration_archive_slug', helper::getArchiveSlug());
    $detailSlug = apply_filters('personio_integration_detail_slug', helper::getDetailSlug());

    // set arguments for our own cpt
    $args = [
        'label'               => $labels['name'],
        'description'         => '',
        'labels'              => $labels,
        'supports'            => [ 'title', 'editor', 'custom-fields' ],
        'public'              => true,
        'hierarchical'        => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'has_archive'         => $archiveSlug,
        'can_export'          => false,
        'exclude_from_search' => false,
        'taxonomies' 	      => [
            WP_PERSONIO_INTEGRATION_TAXONOMY_RECRUITING_CATEGORY,
            WP_PERSONIO_INTEGRATION_TAXONOMY_OCCUPATION_CATEGORY,
            WP_PERSONIO_INTEGRATION_TAXONOMY_OFFICE,
            WP_PERSONIO_INTEGRATION_TAXONOMY_DEPARTMENT,
            WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES,
            WP_PERSONIO_INTEGRATION_TAXONOMY_EMPLOYMENT_TYPE,
            WP_PERSONIO_INTEGRATION_TAXONOMY_SENIORITY,
            WP_PERSONIO_INTEGRATION_TAXONOMY_EXPERIENCE,
            WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE
        ],
        'publicly_queryable'  => (bool)$detailSlug,
        'show_in_rest'        => true,
        'capability_type'     => 'post',
        'capabilities' => [
            'create_posts'      => false,
            'edit_post'         => false,
            'edit_others_posts' => false,
            'read_post'         => false,
            'publish_posts'     => false,
            'read_private_posts' => false
        ],
        'menu_icon'           => plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN ).'/gfx/personio_icon.png',
        'rewrite' => [
            'slug' => $detailSlug
        ]
    ];
    register_post_type( WP_PERSONIO_INTEGRATION_CPT, $args );

    // register personioId als postmeta to be published in rest-api
    // which is necessary for our Blocks
    register_post_meta( WP_PERSONIO_INTEGRATION_CPT, WP_PERSONIO_INTEGRATION_CPT_PM_PID, [
            'type' => 'integer',
            'single' => true,
            'show_in_rest' => true
        ]
    );
}
add_action( 'init', 'personio_integration_add_position_posttype', 10 );

/**
 * Add taxonomies used with the personio posttype.
 * Each will be visible in REST-API, also public.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_add_taxonomies() {
    // set default taxonomy-settings
    // -> could be overwritten by each taxonomy in taxonomies.php
    $taxonomy_array_default = [
        'hierarchical' => true,
        'labels' => '',
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false,
        'show_in_nav_menus' => false,
        'show_admin_column' => true,
        'show_tagcloud' => true,
        'show_in_quick_edit' => true,
        'show_in_rest' => true,
        'query_var' => true,
        'capabilities' => [
            'manage_terms' => 'manage_options',
            'edit_terms' => 'manage_options',
            'delete_terms' => 'god',
            'assign_terms' => 'manage_options',
        ]
    ];

    // loop through our own taxonomies and configure them
    foreach( apply_filters('personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES) as $taxonomy_name => $taxonomy ) {
        // get properties
        $taxonomy_array = array_merge( $taxonomy_array_default, $taxonomy["attr"] );
        $taxonomy_array['labels'] = helper::get_taxonomy_label($taxonomy_name);
        $taxonomy_array['defaults'] = helper::get_taxonomy_defaults($taxonomy_name);

        // apply additional settings for taxonomy
        $taxonomy_array = apply_filters('get_' . $taxonomy_name.'_translate_taxonomy', $taxonomy_array, $taxonomy_name);

        // do not show any taxonomy in menu if Personio URL is not available
        if( !personioIntegration\helper::is_personioUrl_set() ) {
            $taxonomy_array['show_in_menu'] = false;
        }

        // register taxonomy
        register_taxonomy($taxonomy_name, [WP_PERSONIO_INTEGRATION_CPT], $taxonomy_array);
        // add default terms to taxonomy if they do not exist (only in admin or via CLI)
        if( !empty($taxonomy_array['defaults']) && ( is_admin() || helper::isCli()) ) {
            $hasTerms = get_terms(['taxonomy' => $taxonomy_name]);
            if( empty($hasTerms) ) {
                personioIntegration\helper::addTerms($taxonomy_array['defaults'], $taxonomy_name);
            }
        }
    }
}
add_action( 'init', 'personio_integration_add_taxonomies', 0 );

/**
 * Change the REST API-response for own cpt.
 *
 * @param $data
 * @param $post
 * @param $context
 * @return mixed
 * @noinspection PhpUnused
 * @noinspection PhpUnusedParameterInspection
 */
function personio_integration_rest_changes($data, $post, $context) {
    // get the position as object
    $position = new Position($post->ID);

    // generate content
    $content = $position->getContent();

    // generate except
    $excerpt = $position->getExcerpt();

    // add result to response
    $data->data["excerpt"] = ["rendered" => $excerpt, "raw" => "", "protected" => false];
    $data->data["content"] = $content;

    // set response
    return $data;
}
add_filter('rest_prepare_'.WP_PERSONIO_INTEGRATION_CPT, 'personio_integration_rest_changes', 12, 3);

/**
 * Register an old-fashion Wordpress-widget only if Block-widgets are disabled.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_register_widget() {
    if( !wp_use_widgets_block_editor() ) {
        register_widget('personioIntegration\PositionWidget');
        register_widget('personioIntegration\PositionsWidget');
    }
}
add_action( 'widgets_init', 'personio_integration_register_widget' );

/**
 * Add some cron-intervals.
 *
 * @param $schedules
 * @return mixed
 * @noinspection PhpUnused
 */
function personio_integration_add_cron_intervals( $schedules ) {
    $schedules['5minutely'] = [
        'interval'  => 5*60,
        'display'   => __('every 5th Minute', 'wp-personio-integration')
    ];
    return $schedules;
}
add_filter( 'cron_schedules', 'personio_integration_add_cron_intervals' );

/**
 * Run the scheduled positions-import.
 * Only if it is enabled.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_schudule_event_import_positions() {
    if( get_option('personioIntegrationEnablePositionSchedule', 0) == 1 ) {
        new Import();
    }
}
add_action( 'personio_integration_schudule_events', 'personio_integration_schudule_event_import_positions', 10, 0 );

/**
 * Add link in toolbar to list of positions.
 * Only if Personio URL is given and list-view is not disabled.
 *
 * @param $admin_bar
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_add_custom_toolbar($admin_bar) {
    if( get_option('personioIntegrationUrl', false) && get_option('personioIntegrationDisableListSlug', 0) == 0 ) {
        $admin_bar->add_menu([
            'id' => 'personio-position-list',
            'parent' => 'site-name',
            'title' => __('Personio Positions', 'wp-personio-integration'),
            'href' => get_post_type_archive_link(WP_PERSONIO_INTEGRATION_CPT)
        ]);
    }
}
add_action('admin_bar_menu', 'personio_integration_add_custom_toolbar', 100);

/**
 * Get template for archive or single.
 *
 * @param $template
 * @return mixed|string
 * @noinspection PhpUnused
 */
function personio_integration_use_cpt_template( $template ) {
    if ( get_post_type(get_the_ID()) == WP_PERSONIO_INTEGRATION_CPT ) {
        // if the theme is a fse-theme
        if( Helper::theme_is_fse_theme() ) {
            return ABSPATH . WPINC . '/template-canvas.php';
        }

        // for classic themes
        if( is_single() ) {
            return personio_integration_get_single_template($template);
        }
        return personio_integration_get_archive_template($template);
    }
    return $template;
}
add_filter('template_include', 'personio_integration_use_cpt_template');

/**
 * Update slugs on request.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_update_slugs() {
    if( false !== get_transient('personio_integration_update_slugs') ) {
        flush_rewrite_rules();
        delete_transient('personio_integration_update_slugs');
    }
}
add_action('wp', 'personio_integration_update_slugs', 10 );

/**
 * Get language-specific labels for categories.
 *
 * @return array
 */
function personio_integration_admin_categories_labels(): array
{
    return apply_filters('personio_integration_cat_labels', [
        'recruitingCategory' => esc_html__('recruiting category', 'wp-personio-integration'),
        'schedule' => esc_html__('schedule', 'wp-personio-integration'),
        'office' => esc_html__('office', 'wp-personio-integration'),
        'department' => esc_html__('department', 'wp-personio-integration'),
        'employmenttype' => esc_html__('employment types', 'wp-personio-integration'),
        'seniority' => esc_html__('seniority', 'wp-personio-integration'),
        'experience' => esc_html__('experience', 'wp-personio-integration'),
        'occupation' => esc_html__('occupation', 'wp-personio-integration'),
    ]);
}

/**
 * Get language-specific labels for content templates.
 *
 * This also defines the order of the templates in backend and frontend.
 *
 * @return array
 */
function personio_integration_admin_template_labels(): array
{
    return apply_filters('personio_integration_admin_template_labels', [
        'title' => esc_html__('title', 'wp-personio-integration'),
        'excerpt' => esc_html__('excerpt', 'wp-personio-integration'),
        'content' => esc_html__('content', 'wp-personio-integration'),
        'formular' => esc_html__('application link', 'wp-personio-integration')
    ]);
}

/**
 * Return true for import any positions.
 *
 * @return bool
 * @noinspection PhpUnused
 */
function personio_integration_import_single_position(): bool
{
    return true;
}
add_filter( 'personio_integration_import_single_position', 'personio_integration_import_single_position', 10, 2);

/**
 * Add own CSS and JS for frontend.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_add_styles_frontend()
{
    wp_enqueue_style(
        'personio-integration-styles',
        plugin_dir_url(WP_PERSONIO_INTEGRATION_PLUGIN) . '/css/styles.css',
        [],
        filemtime(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN) . '/css/styles.css')
    );
}
add_action('wp_enqueue_scripts', 'personio_integration_add_styles_frontend', PHP_INT_MAX);

/**
 * Change all attributes zu lowercase
 *
 * @param $values
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_lowercase_attributes( $values ): array
{
    $array = [];
    foreach( $values['attributes'] as $name => $attribute ) {
        $array[strtolower($name)] = $attribute;
    }
    return [
        'defaults' => $values['defaults'],
        'settings' => $values['settings'],
        'attributes' => $array
    ];
}
add_filter('personio_integration_get_shortcode_attributes', 'personio_integration_lowercase_attributes', 5, 1);

/**
 * Remove our own cpt from post type list in Redirection-plugin.
 *
 * @param $array
 * @return mixed
 * @noinspection PhpUnused
 */
function personio_integration_redirection_post_types( $array ) {
    unset($array[WP_PERSONIO_INTEGRATION_CPT]);
    return $array;
}
add_filter( 'redirection_post_types', 'personio_integration_redirection_post_types');

/**
 * Optimize Yoast-generated og:description-text.
 * Without this Yoast uses the page content with formular or button-texts.
 *
 * @param $meta_og_description
 * @param $presentation
 * @return array|mixed|string|string[]|null
 */
function personio_integration_yoast_description( $meta_og_description, $presentation ) {
    if( $presentation->model->object_sub_type == WP_PERSONIO_INTEGRATION_CPT ) {
        $position = new Position($presentation->model->object_id);
        return preg_replace("/\s+/", " ", $position->getContent());
    }
    return $meta_og_description;
}
add_filter( 'wpseo_opengraph_desc', 'personio_integration_yoast_description', 10, 2);

/**
 * Optimize RankMath-generated meta-description and og:description.
 * Without this RankMath uses plain post_content, which is JSON and not really nice to read.
 *
 * @param $description
 * @return string
 */
function personio_integration_rankmath_description( $description ): string
{
    if( is_single() ) {
        $object = get_queried_object();
        if( $object instanceof WP_Post && $object->post_type == WP_PERSONIO_INTEGRATION_CPT ) {
            $position = new Position($object->ID);
            return preg_replace("/\s+/", " ", $position->getContent());
        }
    }
    return $description;
}
add_filter( 'rank_math/frontend/description', 'personio_integration_rankmath_description', 10, 1);

/**
 * Check on each load if plugin-version has been changed.
 * If yes, run appropriated functions for migrate to the new version.
 *
 * @return void
 */
function personio_integration_update() {
    // get installed plugin-version (version of the actual files in this plugin)
    $installedPluginVersion = WP_PERSONIO_INTEGRATION_VERSION;

    // get db-version (version which was last installed)
    $dbPluginVersion = get_option('personioIntegrationVersion', '1.0.0');

    // compare version if we are not in development-mode
    if( $installedPluginVersion != '@@VersionNumber@@' && version_compare($installedPluginVersion, $dbPluginVersion, '>') ) {
        switch( $dbPluginVersion ) {
            case '1.2.3':
                // nothing to do as 1.2.3 is the first version with this update-check
                break;
            default:
                updates::version123();
                break;
        }

        // save new plugin-version in DB
        update_option('personioIntegrationVersion', $installedPluginVersion);
    }
}
add_action( 'plugins_loaded', 'personio_integration_update' );

/**
 * Add each position to list during import.
 *
 * @return true
 */
function personio_integration_import_single_position_filter_existing(): bool
{
    return true;
}
add_filter( 'personio_integration_import_single_position_filter_existing', 'personio_integration_import_single_position_filter_existing', 10);

/**
 * Add endpoint for requests from our own Blocks.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_rest_api() {
    register_rest_route( 'personio/v1', '/taxonomies/', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'personio_integration_rest_api_taxonomies',
        'permission_callback' => function () {
            return true;//current_user_can( 'edit_posts' );
        }
    ) );
}
add_action( 'rest_api_init', 'personio_integration_rest_api');

/**
 * Return list of available taxonomies for REST-API.
 *
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_rest_api_taxonomies(): array
{
    $taxonomies_labels_array = personio_integration_admin_categories_labels();
    $taxonomies = [];
    $count = 0;
    foreach( apply_filters('personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES) as $taxonomy_name => $taxonomy ) {
        if( $taxonomy['useInFilter'] == 1 ) {
            $count++;
            $termsAsObjects = get_terms(['taxonomy' => $taxonomy_name]);
            $termCount = 0;
            $terms = [
                [
                    'id' => $termCount,
                    'label' => __('Please choose', 'wp-personio-integration'),
                    'value' => 0
                ]
            ];
            foreach( $termsAsObjects as $term ) {
                $termCount++;
                $terms[] = [
                    'id' => $termCount,
                    'label' => $term->name,
                    'value' => $term->term_id
                ];
            }
            $taxonomies[] = [
                'id' => $count,
                'label' => $taxonomies_labels_array[$taxonomy['slug']],
                'value' => $taxonomy['slug'],
                'entries' => $terms
            ];
        }
    }
    return $taxonomies;
}