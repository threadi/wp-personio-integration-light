<?php

use personioIntegration\helper;
use personioIntegration\Import;
use personioIntegration\Position;
use personioIntegration\Positions;

/**
 * Add own CSS and JS for backend.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_add_styles_and_js_admin() {
    // admin-specific styles
    wp_enqueue_style('personio_integration-admin-css',
        plugin_dir_url(WP_PERSONIO_INTEGRATION_PLUGIN) . '/admin/styles.css',
        [],
        filemtime(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN) . '/admin/styles.css'),
    );

    // admin- and backend-styles for attribute-type-output
    wp_enqueue_style(
        'personio_integration-styles',
        plugin_dir_url(WP_PERSONIO_INTEGRATION_PLUGIN) . '/css/styles.css',
        [],
        filemtime(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN) . '/css/styles.css')
    );

    // backend-JS
    wp_enqueue_script( 'personio_integration-admin-js',
        plugins_url( '/admin/js.js' , WP_PERSONIO_INTEGRATION_PLUGIN ),
        ['jquery'],
        filemtime(plugin_dir_path(WP_PERSONIO_INTEGRATION_PLUGIN) . '/admin/js.js'),
    );

    // add php-vars to our js-script
    wp_localize_script( 'personio_integration-admin-js', 'customJsVars', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'pro_url' => helper::get_pro_url(),
            'label_go_pro' => __('Get Personio Integration Pro', 'wp-personio-integration'),
            'dismiss_nonce' => wp_create_nonce( 'wp-dismiss-notice' ),
            'run_import_nonce' => wp_create_nonce( 'personio-run-import' ),
            'get_import_nonce' => wp_create_nonce( 'personio-get-import-info' ),
            'label_reset_sort' => __('Reset sorting', 'wp-personio-integration'),
            'label_run_import' => __('Run import', 'wp-personio-integration'),
            'label_import_is_running' => __('Import is running', 'wp-personio-integration'),
            'txt_please_wait' => __('Please wait', 'wp-personio-integration'),
            'txt_import_hint' => __('Performing the import could take a few minutes. If a timeout occurs, a manual import is not possible this way. Then the automatic import should be used.', 'wp-personio-integration'),
            'txt_import_has_been_run' => sprintf(
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
            ),
            'label_ok' => __('OK', 'wp-personio-integration')
        ]
    );

    // embed necessary scripts for progressbar
    if( !empty($_GET["post_type"]) && $_GET["post_type"] === WP_PERSONIO_INTEGRATION_CPT ) {
        $wp_scripts = wp_scripts();
        wp_enqueue_script('jquery-ui-progressbar');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style(
            'personio-jquery-ui-styles',
            'https://code.jquery.com/ui/' . $wp_scripts->registered['jquery-ui-core']->ver . '/themes/smoothness/jquery-ui.min.css',
            false,
            '1.0.0',
            false
        );
    }
}
add_action( 'admin_enqueue_scripts', 'personio_integration_add_styles_and_js_admin', PHP_INT_MAX );

/**
 * Add a dashboard widget to show positions.
 *
 * @noinspection PhpUnused
 */
function personio_integration_add_dashboard_widgets() {
    // only if Personio URL is available
    if( !helper::is_personioUrl_set() ) {
        return;
    }

    wp_add_dashboard_widget(
        'dashboard_personio_integration_positions',
        __('Positions imported from Personio', 'wp-personio-integration'),
        'personio_integration_dashboard_widget_function',
        null, [],
        'side',
        'high'
    );
}
add_action( 'wp_dashboard_setup', 'personio_integration_add_dashboard_widgets' );

/**
 * Output the contents of the dashboard widget
 *
 * @noinspection PhpUnusedParameterInspection
 */
function personio_integration_dashboard_widget_function( $post, $callback_args ) {
    $positionsObj = new Positions();
    $positionsList = $positionsObj->getPositions(3);
    if( count($positionsList) == 0 ) {
        echo '<p>'.__('Actually there are no positions imported from Personio.', 'wp-personio-integration').'</p>';
    }
    else {
        $link = esc_url( add_query_arg(
            [
                'post_type' => WP_PERSONIO_INTEGRATION_CPT
            ],
            get_admin_url() . 'edit.php'
        ) );

        ?><ul class="personio_positions"><?php
        foreach( $positionsList as $position ) {
            ?><li><a href="<?php echo get_permalink($position->ID); ?>"><?php echo esc_html($position->getTitle()); ?></a></li><?php
        }
        ?></ul><?php
        ?><p><a href="<?php echo esc_url($link); ?>"><?php  _e('Show all positions', 'wp-personio-integration'); ?></a></p><?php
    }
}

/**
 * Generate transient-based messages in backend.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_notices() {
    // show transients
    foreach( apply_filters('personio_integration_admin_transients', WP_PERSONIO_INTEGRATION_TRANSIENTS) as $transient => $settings ) {
        if( false !== get_transient( $transient ) ) {
            // marker to show the transient
            $show = true;

            // check if this transient is dismissed to some time
            if( !helper::is_transient_not_dismissed($transient) ) {
                continue;
            }

            // hide on specific pages
            $page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
            if( isset($settings['options']['hideOnPages']) && in_array($page, $settings['options']['hideOnPages']) ) {
                $show = false;
            }

            // hide if other transient is also visible
            if( isset($settings['options']['hideIfTransients']) ){
                foreach( $settings['options']['hideIfTransients'] as $transientToCheck ) {
                    if( false !== get_transient($transientToCheck) ) {
                        $show = false;
                    }
                }
            }

            // hide on settings-tab
            $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : '';
            if( isset($settings['options']['hideOnSettingsTabs']) && in_array($tab, $settings['options']['hideOnSettingsTabs']) ) {
                $show = false;
            }

            // get the translated content
            $settings['content'] = helper::get_admin_transient_content($transient);

            // do not show anything on empty content
            if( empty($settings['content']) ) {
                $show = false;
            }

            // show it
            if( $show ) {
                ?>
                <div class="wp-personio-integration-transient updated <?php echo esc_attr($settings['type']); ?>" data-dismissible="<?php echo esc_attr($transient); ?>-14">
                    <?php echo wp_kses_post($settings['content']); ?>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo esc_html__( 'Dismiss this notice.', 'wp-personio-integration' ); ?></span></button>
                </div>
                <?php

                // remove the transient
                delete_transient( $transient );

                // disable plugin if option is set
                if( !empty($settings['options']['disable_plugin']) ) {
                    deactivate_plugins(plugin_basename(WP_PERSONIO_INTEGRATION_PLUGIN));
                }
            }
        }
    }
}
add_action( 'admin_notices', 'personio_integration_admin_notices' );

/**
 * Add columns to position-table in backend.
 *
 * @param $columns
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_admin_position_add_column($columns): array
{
    // create new column-array
    $newColumns = [];

    // add column for PersonioId
    $newColumns['id'] = __( 'PersonioID', 'wp-personio-integration' );

    // remove checkbox-column
    unset($columns['cb']);

    // return results
    return array_merge($newColumns, $columns);
}
add_filter( 'manage_'.WP_PERSONIO_INTEGRATION_CPT.'_posts_columns', 'personio_integration_admin_position_add_column', 10 );

/**
 * Add content to the column in the position-table in backend.
 *
 * @param $column
 * @param $post_id
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_position_column_content( $column, $post_id ) {
    if ($column == 'id') {
        $position = new Position($post_id);
        echo absint($position->getPersonioId());
    }
}
add_action( 'manage_'.WP_PERSONIO_INTEGRATION_CPT.'_posts_custom_column' , 'personio_integration_admin_add_position_column_content', 10, 2 );

/**
 * Add link to plugin-settings in plugin-list.
 *
 * @param $links
 * @return mixed
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_setting_link( $links ) {
    // build and escape the URL
    $url = add_query_arg(
        [
            'page' => 'personioPositions',
            'post_type' => WP_PERSONIO_INTEGRATION_CPT
        ],
        get_admin_url() . 'edit.php'
    );

    // create the link
    $settings_link = "<a href='".esc_url($url)."'>" . __( 'Settings', 'wp-personio-integration' ) . '</a>';

    // adds the link to the end of the array
    $links[] = $settings_link;

    return $links;
}
add_filter( 'plugin_action_links_'.plugin_basename(WP_PERSONIO_INTEGRATION_PLUGIN), 'personio_integration_admin_add_setting_link' );

/**
 * Activate transient-based hint if configuration does not contain the necessary URL.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_checkConfig() {
    if( !helper::is_personioUrl_set() ) {
        set_transient('personio_integration_no_url_set', 1, 60);
    }
    elseif( get_option('personioIntegrationPositionCount', 0) > 0 ) {
        set_transient('personio_integration_limit_hint', 0);
    }
}
add_action( 'admin_init', 'personio_integration_admin_checkConfig');

/**
 * Activate transient-based hint if configuration is set but no positions are imported until now.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_checkPositionCount() {
    if( helper::is_personioUrl_set() && get_option( 'personioIntegrationPositionCount', 0 ) == 0 ) {
        set_transient('personio_integration_no_position_imported', 1, 60);
    }
}
add_action( 'admin_init', 'personio_integration_admin_checkPositionCount');

/**
 * Remove any bulk actions for our own cpt.
 *
 * @noinspection PhpUnused
 * @noinspection PhpUnusedParameterInspection
 */
function personio_integration_admin_remove_bulk_actions( $actions ): array
{
    return [];
}
add_filter( 'bulk_actions-edit-'.WP_PERSONIO_INTEGRATION_CPT, 'personio_integration_admin_remove_bulk_actions' );

/**
 * Remove all actions except "view" for our own cpt.
 *
 * @param $actions
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_admin_remove_actions( $actions ): array {
    if( get_post_type() === WP_PERSONIO_INTEGRATION_CPT ) {
        return ['view' => $actions['view']];
    }
    return $actions;
}
add_filter('post_row_actions', 'personio_integration_admin_remove_actions', 10, 1 );

/**
 * Add filter for our own cpt on lists in admin.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_filter() {
    $post_type = (isset($_GET['post_type'])) ? sanitize_text_field($_GET['post_type']) : 'post';

    if( $post_type == WP_PERSONIO_INTEGRATION_CPT ) {
        // add filter for each taxonomy
        foreach( apply_filters('personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES) as $taxonomy_name => $taxonomy ) {
            // show only taxonomies which are visible in filter
            if( $taxonomy['useInFilter'] == 1 ) {
                // get the taxonomy as object
                $taxonomy = get_taxonomy($taxonomy_name);

                // get its terms
                $terms = get_terms(['taxonomy' => $taxonomy_name, 'hide_empty' => false]);

                // list terms only if they are available
                if( !empty($terms) ) {
                    ?>
                        <select name="admin_filter_<?php echo esc_attr($taxonomy_name); ?>">
                            <option value="0"><?php echo esc_html($taxonomy->label); ?></option>
                            <?php
                            foreach( $terms as $term ) {
                                ?><option value="<?php echo esc_attr($term->term_id); ?>"<?php echo (isset($_GET['admin_filter_'.$taxonomy_name]) && absint($_GET['admin_filter_'.$taxonomy_name]) == $term->term_id ) ? ' selected="selected"' : ''; ?>><?php echo esc_html($term->name); ?></option><?php
                            }
                            ?>
                        </select>
                    <?php
                }
            }
        }
    }
}
add_action( 'restrict_manage_posts', 'personio_integration_admin_add_filter');

/**
 * Use filter in admin on edit-page for filtering the cpt-items.
 *
 * @param $query
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_use_filter($query) {
    global $pagenow;
    $post_type = (isset($_GET['post_type'])) ? sanitize_text_field($_GET['post_type']) : 'post';

    if ($post_type == WP_PERSONIO_INTEGRATION_CPT && $pagenow == 'edit.php') {
        // add filter for each taxonomy
        $tax_query = [];
        foreach( apply_filters('personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES) as $taxonomy_name => $taxonomy ) {
            if ($taxonomy['useInFilter'] == 1) {
                if( isset($_GET['admin_filter_'.$taxonomy_name]) && absint($_GET['admin_filter_'.$taxonomy_name]) > 0 ) {
                    $tax_query[] = [
                        'taxonomy' => $taxonomy_name,
                        'field' => 'term_id',
                        'terms' => absint($_GET['admin_filter_'.$taxonomy_name])
                    ];
                }
            }
        }
        if( !empty($tax_query) ) {
            if( count($tax_query) > 1 ) {
                $query->set('tax_query', [
                    'relation' => 'AND',
                    $tax_query
                ]);
            }
            else {
                $query->set('tax_query', $tax_query);
            }
        }
    }
}
add_filter( 'parse_query', 'personio_integration_admin_use_filter');

/**
 * Handles Ajax request to persist notices dismissal.
 * Uses check_ajax_referer to verify nonce.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_dismiss() {
    // get values
    $option_name        = isset( $_POST['option_name'] ) ? sanitize_text_field( wp_unslash( $_POST['option_name'] ) ) : false;
    $dismissible_length = isset( $_POST['dismissible_length'] ) ? sanitize_text_field( wp_unslash( $_POST['dismissible_length'] ) ) : 14;

    if ( 'forever' !== $dismissible_length ) {
        // If $dismissible_length is not an integer default to 14.
        $dismissible_length = ( 0 === absint( $dismissible_length ) ) ? 14 : $dismissible_length;
        $dismissible_length = strtotime( absint( $dismissible_length ) . ' days' );
    }

    // check nonce
    check_ajax_referer( 'wp-dismiss-notice', 'nonce' );

    // save value
    update_site_option( 'pi-dismissed-'.md5($option_name), $dismissible_length );

    // return nothing
    wp_die();
}

/**
 * Start Import via AJAX.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_run_import() {
    // check nonce
    check_ajax_referer( 'personio-run-import', 'nonce' );

    // run import
    new Import();

    // return nothing
    wp_die();
}

/**
 * Return state of the actual running import.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_get_import_info() {
    // check nonce
    check_ajax_referer( 'personio-get-import-info', 'nonce' );

    // return actual and max count of import steps
    echo absint(get_option(WP_PERSONIO_OPTION_COUNT, 0)).";".absint(get_option(WP_PERSONIO_OPTION_MAX)).";".absint(get_option(WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0));

    // return nothing else
    wp_die();
}

/**
 * Add AJAX-endpoints.
 */
add_action( 'admin_init', function() {
    add_action('wp_ajax_nopriv_dismiss_admin_notice', 'personio_integration_admin_dismiss');
    add_action('wp_ajax_dismiss_admin_notice', 'personio_integration_admin_dismiss');

    add_action('wp_ajax_nopriv_personio_run_import', 'personio_integration_admin_run_import');
    add_action('wp_ajax_personio_run_import', 'personio_integration_admin_run_import');

    add_action('wp_ajax_nopriv_personio_get_import_info', 'personio_integration_admin_get_import_info');
    add_action('wp_ajax_personio_get_import_info', 'personio_integration_admin_get_import_info');
});

/**
 * Show hint for our Pro-version.
 *
 * @param $hint
 * @return void
 */
function personio_integration_admin_show_pro_hint( $hint ) {
    echo '<p class="personio-pro-hint">'.sprintf(wp_kses_post($hint), '<a href="'.esc_url(helper::get_pro_url()).'" target="_blank">Personio Integration Pro</a>').'</p>';
}
add_action( 'personio_integration_admin_show_pro_hint', 'personio_integration_admin_show_pro_hint', 10, 1);

/**
 * Add marker for free version on body-element
 *
 * @param $classes
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_body_class_free( $classes ): string
{
    $classes .= ' personio-integration-free';
    if( !helper::is_personioUrl_set() ) {
        $classes .= ' personio-integration-url-missing';
    }
    return $classes;
}
add_filter( 'admin_body_class', 'personio_integration_admin_add_body_class_free', 10, 1);