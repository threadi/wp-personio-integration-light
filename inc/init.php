<?php

use personioIntegration\helper;
use personioIntegration\Import;
use personioIntegration\Position;
use personioIntegration\Positions;
use personioIntegration\updates;

/**
 * General initialization.
 *
 * Must priority of 0 to load before widgets_init, which run with priority 1 during init-run.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_init(): void
{
    load_plugin_textdomain( 'wp-personio-integration', false, dirname( plugin_basename( WP_PERSONIO_INTEGRATION_PLUGIN ) ) . '/languages' );
}
add_action( 'init', 'personio_integration_init', -1 );

/**
 * Add position as custom posttype.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_add_position_posttype(): void
{
    $labels = [
        'name'                => __( 'Positions', 'wp-personio-integration' ),
        'singular_name'       => __( 'Position', 'wp-personio-integration'),
        'menu_name'           => __( 'Positions', 'wp-personio-integration'),
        'parent_item_colon'   => __( 'Parent Position', 'wp-personio-integration'),
        'all_items'           => __( 'All Positions', 'wp-personio-integration'),
        'view_item'           => __( 'View Position in frontend', 'wp-personio-integration'),
        'view_items'          => __( 'View Positions', 'wp-personio-integration'),
        'edit_item'           => __( 'View Position in backend', 'wp-personio-integration' ),
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
            WP_PERSONIO_INTEGRATION_TAXONOMY_SCHEDULE,
	        WP_PERSONIO_INTEGRATION_TAXONOMY_KEYWORDS
        ],
        'publicly_queryable'  => (bool)$detailSlug,
        'show_in_rest'        => true,
        'capability_type'     => 'post',
        'capabilities' => array(
            'create_posts'       => false,
            'edit_post'          => 'manage_options',
            'edit_others_posts'  => false,
            'read_post'          => 'manage_options',
            'publish_posts'      => false,
            'read_private_posts' => false
        ),
        'menu_icon'           => plugin_dir_url( WP_PERSONIO_INTEGRATION_PLUGIN ).'/gfx/personio_icon.png',
        'rewrite' => [
            'slug' => $detailSlug
        ]
    ];
    register_post_type( WP_PERSONIO_INTEGRATION_CPT, $args );

    // register personioId als postmeta to be published in rest-api,
    // which is necessary for our Blocks.
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
function personio_integration_add_taxonomies(): void
{
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

        // remove slugs for not logged in users
        if( !is_user_logged_in() ) {
            $taxonomy_array['rewrite'] = false;
        }

        // apply additional settings for taxonomy
        $taxonomy_array = apply_filters('get_' . $taxonomy_name.'_translate_taxonomy', $taxonomy_array, $taxonomy_name);

        // do not show any taxonomy in menu if Personio URL is not available
        if( !personioIntegration\helper::is_personioUrl_set() ) {
            $taxonomy_array['show_in_menu'] = false;
        }

        // register taxonomy
        register_taxonomy($taxonomy_name, [WP_PERSONIO_INTEGRATION_CPT], $taxonomy_array);

        add_filter( 'get_'.$taxonomy_name, 'personio_integration_translate_taxonomy', 10, 2);
    }
}
add_action( 'init', 'personio_integration_add_taxonomies', 0 );

/**
 * One-time function to create taxonomy-defaults.
 *
 * @return void
 */
function personio_integration_add_taxonomy_defaults(): void
{
    // Exit if the work has already been done.
    if ( get_option( 'personioTaxonomyDefaults', 0 ) == 1 ) {
        return;
    }

    // loop through our own taxonomies and configure them
    foreach( apply_filters('personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES) as $taxonomy_name => $taxonomy ) {
        // add default terms to taxonomy if they do not exist (only in admin or via CLI)
	    $taxonomy_obj = get_taxonomy($taxonomy_name);
        if( !empty($taxonomy_obj->defaults) && ( is_admin() || helper::isCli()) ) {
            $hasTerms = get_terms(['taxonomy' => $taxonomy_name]);
            if( empty($hasTerms) ) {
                personioIntegration\helper::addTerms($taxonomy_obj->defaults, $taxonomy_name);
            }
        }
    }

    // Add or update the wp_option
    update_option( 'personioTaxonomyDefaults', 1 );
}
add_action( 'init', 'personio_integration_add_taxonomy_defaults', 20 );

/**
 * Change the REST API-response for own cpt.
 *
 * @param $data
 * @param $post
 * @param $context
 * @return WP_REST_Response
 * @noinspection PhpUnused
 * @noinspection PhpUnusedParameterInspection
 */
function personio_integration_rest_changes($data, $post, $context): WP_REST_Response {
    // get positions-object
    $positions = positions::get_instance();

    // get the position as object
    $position = $positions->get_position($post->ID);

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
function personio_integration_register_widget(): void
{
    if( !wp_use_widgets_block_editor() ) {
        register_widget('personioIntegration\PositionWidget');
        register_widget('personioIntegration\PositionsWidget');
    }
}
add_action( 'widgets_init', 'personio_integration_register_widget', 10 );

/**
 * Un-Register an old-fashion Wordpress-widget only if Block-widgets are disabled.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_unregister_widget(): void
{
    if( !wp_use_widgets_block_editor() ) {
        unregister_widget('personioIntegration\PositionWidget');
        unregister_widget('personioIntegration\PositionsWidget');
        delete_option('widget_personiopositionwidget');
        delete_option('widget_personiopositionswidget');
    }
}
add_action( 'widgets_init', 'personio_integration_unregister_widget', 20 );

/**
 * Add some cron-intervals.
 *
 * @param $schedules
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_add_cron_intervals( $schedules ): array
{
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
function personio_integration_schudule_event_import_positions(): void
{
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
function personio_integration_add_custom_toolbar($admin_bar): void
{
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
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_use_cpt_template( $template ): string
{
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
        'excerpt' => esc_html__('details', 'wp-personio-integration'),
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
function personio_integration_add_styles_frontend(): void
{
    wp_enqueue_style(
        'personio-integration-styles',
            trailingslashit(plugin_dir_url(WP_PERSONIO_INTEGRATION_PLUGIN)) . 'css/styles.css',
        [],
        filemtime(trailingslashit(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN)) . 'css/styles.css')
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
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_redirection_post_types( $array ): array
{
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
 * @return string
 */
function personio_integration_yoast_description( $meta_og_description, $presentation ): string
{
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
function personio_integration_update(): void
{
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
                updates::version205();
                updates::version211();
                updates::version227();
                updates::version240();
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
function personio_integration_rest_api(): void
{
    register_rest_route( 'personio/v1', '/taxonomies/', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'personio_integration_rest_api_taxonomies',
        'permission_callback' => function () {
            return current_user_can( 'edit_posts' );
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
			if( !empty($taxonomies_labels_array[$taxonomy['slug']]) ) {
				$taxonomies[] = [
					'id'      => $count,
					'label'   => $taxonomies_labels_array[ $taxonomy['slug'] ],
					'value'   => $taxonomy['slug'],
					'entries' => $terms
				];
			}
        }
    }
    return $taxonomies;
}

/**
 * Optimize output of plugin OG.
 *
 * @source https://de.wordpress.org/plugins/og/
 * @param $array
 * @return array
 */
function personio_integration_og_optimizer( $array ): array {
    if( is_singular(WP_PERSONIO_INTEGRATION_CPT) ) {
        // get position as object
        $post_id = get_queried_object_id();
        $position = new Position($post_id);
        $position->lang = helper::get_wp_lang();

        // get description
        $description = wp_strip_all_tags($position->getContent());
        $description = preg_replace("/\s+/", " ", $description);

        // update settings
        $array['og']['title'] = $position->getTitle();
        $array['og']['description'] = $description;
        $array['twitter']['title'] = $position->getTitle();
        $array['twitter']['description'] = $description;
        $array['schema']['title'] = $position->getTitle();
        $array['schema']['description'] = $description;
    }
    return $array;
}
add_filter( 'og_array', 'personio_integration_og_optimizer');

/**
 * Translate the term-names of each plugin-own taxonomy if set.
 * Only in frontend, not in backend.
 *
 * @param $_term
 * @param $taxonomy
 * @return mixed
 * @noinspection PhpUnused
 */
function personio_integration_translate_taxonomy( $_term, $taxonomy ) {
    if( $taxonomy != WP_PERSONIO_INTEGRATION_TAXONOMY_LANGUAGES && $_term instanceof WP_Term ) {
        // read from defaults for the taxonomy.
        $array = helper::get_taxonomy_defaults($taxonomy);
        if( !empty($array[$_term->name]) ) {
            $_term->name = $array[$_term->name];
        }
    }
    return $_term;
}
