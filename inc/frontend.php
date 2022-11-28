<?php

use personioIntegration\helper;
use personioIntegration\Position;
use personioIntegration\Positions;

/**
 * Initialization of available shortcodes.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_frontend_init() {
    add_shortcode('personioPosition', 'personio_integration_position_shortcode');
    add_shortcode('personioPositions', 'personio_integration_positions_shortcode');
}
add_action( 'init', 'personio_integration_frontend_init' );

/**
 * Output of single positions via shortcode and any PageBuilder.
 * Example: [personioPosition lang="de" id="96" templates="title,content,formular" excerpt="recruitingCategory,schedule,office,department,seniority,experience,occupation"]
 *
 * Parameter:
 * - personioid => PersonioId of the position (required)
 * - lang => sets the language for the output, defaults to default-language from plugin-settings
 * - templates => comma-separated list of template to use, defaults to title and excerpt
 * - excerpt => comma-separated list of terms to display, defaults to recruitingCategory, schedule, office
 * - donotlink => if position-title should be linked (0) or not (1), defaults to link (0)
 *
 * Templates:
 * - title => show position title
 * - excerpt => show excerpt configured by excerpt-parameter
 * - content => show language-specific content
 * - formular => show application-button
 *
 * @param array $attributes
 * @return string
 * @noinspection PhpMissingParamTypeInspection
 */
function personio_integration_position_shortcode( $attributes = [] ): string
{
    if( !is_array($attributes) ) {
        $attributes = [];
    }

    // define the default values for each attribute
    $attribute_defaults = [
        'personioid' => 0,
        'lang' => get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY),
        'template' => '',
        'templates' => implode(',', get_option('personioIntegrationTemplateContentDefaults', '')),
        'excerpt' => implode(",", get_option('personioIntegrationTemplateExcerptDetail', '')),
        'donotlink' => 1
    ];

    // define the settings for each attribute (array or string)
    $attribute_settings = [
        'personioid' => 'int',
        'lang' => 'string',
        'templates' => 'array',
        'excerpt' => 'array',
        'donotlink' => 'bool'
    ];
    $personio_attributes = helper::get_shortcode_attributes( $attribute_defaults, $attribute_settings, $attributes );

    // do not output anything without ID
    if( $personio_attributes['personioid'] <= 0 ) {
        if( get_option('personioIntegration_debug', 0) == 1 ) {
            return '<div><p>'.__('Detail-view called without Id of a position.', 'wp-personio-integration').'</p></div>';
        }
        return '';
    }

    // get the position
    $positions = new Positions();
    $position = $positions->getPositionByPersonioId( $personio_attributes['personioid'] );

    // do not show this position if it is not valid or could not be loaded
    if( $position && !$position->isValid() || !$position ) {
        if( get_option('personioIntegration_debug', 0) == 1 ) {
            return '<div><p>'.__('Given Id is not a valid position-Id.', 'wp-personio-integration').'</p></div>';
        }
        return "";
    }

    // set language
    $position->lang = $personio_attributes['lang'];

    // change settings for output
    $personio_attributes = apply_filters('personio_integration_get_template', $personio_attributes, $attribute_defaults);

    // collect the output
    ob_start();
    include helper::getTemplate('single-'.WP_PERSONIO_INTEGRATION_CPT.'-shortcode'.$personio_attributes['template'].'.php');
    return ob_get_clean();
}

/**
 * Output of list of positions via shortcode and any PageBuilder.
 * Example: [personioPositions filter="office,recruitingCategory,occupationCategory,department,employmenttype,seniority,schedule,experience,language" filtertype="select" lang="de" templates="title,excerpt,content" excerpt="recruitingCategory,schedule,office,department,seniority,experience,occupation" ids="96,97"]
 *
 * Parameter:
 * - lang => sets the language for the output, defaults to default-language from plugin-settings
 * - showfilter => enables the filter for this list-view, default: disabled
 * - filter => comma-separated list of filter which will be visible above the list, default: empty
 * - filtertype => sets the type of filter to use (select or linklist), default: select
 * - templates => comma-separated list of template to use, defaults to title and excerpt
 * - excerpt => comma-separated list of terms to display, defaults to recruitingCategory, schedule, office
 * - ids => comma-separated list of PositionIDs to display, default: empty
 * - sort => direction for sorting the resulting list (asc or desc), default: asc
 * - sortby => Field to be sorted by (title or date), default: title
 * - limit => limit the items in the list (-1 for unlimited, 0 for pagination-setting), default: 0
 *
 * Filter:
 * - office
 * - recruitingCategory
 * - occupationCategory
 * - department
 * - employmenttype
 * - seniority
 * - schedule
 * - experience
 *
 * Templates:
 * - title => show position title
 * - excerpt => show excerpt configured by excerpt-parameter
 * - content => show language-specific content
 * - formular => show application-button
 *
 * @return false|string
 */
function personio_integration_positions_shortcode( $attributes = [] ) {
    if( !is_array($attributes) ) {
        $attributes = [];
    }

    // define the default values for each attribute
    $attribute_defaults = [
        'lang' => get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY),
        'showfilter' => (get_option('personioIntegrationEnableFilter', 0) == 1),
        'filter' => implode(',', get_option('personioIntegrationTemplateFilter', '')),
        'filtertype' => get_option('personioIntegrationFilterType', 'select'),
        'template' => '',
        'templates' => implode(',', get_option('personioIntegrationTemplateContentList', '')),
        'excerpt' => implode(",", get_option('personioIntegrationTemplateExcerptDefaults', '')),
        'ids' => '',
        'donotlink' => (get_option('personioIntegrationEnableLinkInList', 0) == 0),
        'sort' => 'asc',
        'sortby' => 'title',
        'limit' => 0,
        'nopagination' => apply_filters('personio_integration_pagination', true),
        'groupby' => ''
    ];
    // define the settings for each attribute (array or string)
    $attribute_settings = [
        'id' => 'string',
        'lang' => 'string',
        'showfilter' => 'bool',
        'filter' => 'array',
        'templates' => 'array',
        'excerpt' => 'array',
        'ids' => 'array',
        'donotlink' => 'bool',
        'sort' => 'string',
        'sortby' => 'string',
        'limit' => 'unsignedint',
        'filtertype' => 'string',
        'nopagination' => 'bool',
        'groupby' => 'string'
    ];
    // add taxonomies which are available as filter
    foreach( WP_PERSONIO_INTEGRATION_TAXONOMIES as $taxonomy_name => $taxonomy ) {
        if( !empty($taxonomy['slug']) && $taxonomy['useInFilter'] == 1 ) {
            if( !empty($_GET['personiofilter']) && !empty($_GET['personiofilter'][$taxonomy['slug']]) ) {
                $attribute_defaults[$taxonomy['slug']] = 0;
                $attribute_settings[$taxonomy['slug']] = 'filter';
            }
        }
        if( !empty($taxonomy['slug']) && $taxonomy['useInFilter'] == 0 ) {
            unset($attribute_defaults[$taxonomy['slug']]);
            unset($attribute_settings[$taxonomy['slug']]);
        }
    }

    // get the attributes to filter
    $personio_attributes = helper::get_shortcode_attributes( $attribute_defaults, $attribute_settings, $attributes );

    // get positions-object for search
    $positionsObj = new Positions();

    // unset the id-list if it is empty
    if( empty($personio_attributes['ids'][0]) ) {
        unset($personio_attributes['ids']);
    }
    else {
        // convert id-list from PersonioId in post_id
        $resultingList = [];
        foreach( $personio_attributes['ids'] as $personioId ) {
            $position = $positionsObj->getPositionByPersonioId($personioId);
            if( $position instanceof Position ) {
                $resultingList[] = $position->ID;
            }
        }
        $personio_attributes['ids'] = $resultingList;
    }

    // set limit
    $limitByWp = $personio_attributes['limit'] ?: get_option('posts_per_page');
    $personio_attributes['limit'] = apply_filters('personio_integration_limit', $limitByWp > 10 ? 10 : $limitByWp, $personio_attributes['limit']);

    // get the positions
    $positions = $positionsObj->getPositions( $personio_attributes['limit'], $personio_attributes );
    $GLOBALS['personio_query_results'] = $positionsObj->getResult();

    // change settings for output
    $personio_attributes = apply_filters('personio_integration_get_template', $personio_attributes, $attribute_defaults);

    // collect the output
    ob_start();
    include helper::getTemplate('archive-'.WP_PERSONIO_INTEGRATION_CPT.'-shortcode'.$personio_attributes['template'].'.php');
    return ob_get_clean();
}

/**
 * Change output of post_content for the custom post type of this plugin.
 *
 * @param $content
 * @return mixed|string
 * @noinspection PhpUnused
 */
function personio_integration_content_output( $content ) {

    // change the output for our own cpt
    if( !helper::is_admin_api_request() && !is_admin()
        && is_single() && get_post_type(get_the_ID()) == WP_PERSONIO_INTEGRATION_CPT ) {

        // set attributes for single output
        $attributes = [
            'id' => get_post_meta( get_the_ID(), WP_PERSONIO_INTEGRATION_CPT_PM_PID, true )
        ];

        // return the output of shortcode-function
        return personio_integration_position_shortcode( $attributes );
    }

    // return results
    return $content;
}
add_filter( 'the_content', 'personio_integration_content_output', 5 );

/**
 * Change output of excerpt in archive-pages for the custom post type of this plugin.
 *
 * @param $excerpt
 * @return mixed
 * @noinspection PhpUnused
 */
function personio_integration_excerpt_output( $excerpt )
{
    if( !helper::is_admin_api_request()
        && !is_single()
        && get_post_type(get_the_ID()) == WP_PERSONIO_INTEGRATION_CPT
    ) {
        // set attributes for single output
        $attributes = [
            'personioId' => get_post_meta( get_the_ID(), WP_PERSONIO_INTEGRATION_CPT_PM_PID, true ),
            'templates' => 'excerpt'
        ];

        // return the output of shortcode-function
        return personio_integration_position_shortcode( $attributes );
    }
    return $excerpt;
}
add_filter('the_excerpt' , 'personio_integration_excerpt_output', 10);
add_filter('get_the_excerpt' , 'personio_integration_excerpt_output', 10);

/**
 * Get position title for list.
 *
 * @param $position
 * @param $attributes
 * @return void
 * @noinspection PhpUnused
 * @noinspection PhpUnusedParameterInspection
 * @noinspection DuplicatedCode
 */
function personio_integration_get_title( $position, $attributes ) {
    // set the header-size (h1 for single, h2 for list)
    $hSize = "2";
    if( !did_action( 'elementor/loaded' ) && is_single() ) {
        $hSize = "1";
    }
    // and h3 if list is grouped
    if( !empty($attributes['groupby']) ) {
        $hSize = "3";
    }

    if( false !== $attributes["donotlink"] ) {
        ?>
        <header class="entry-content default-max-width">
            <h<?php echo absint($hSize); ?> class="entry-title"><?php echo esc_html($position->getTitle()); ?></h<?php echo absint($hSize); ?>>
        </header>
        <?php
    }
    else {
        ?>
        <header class="entry-content default-max-width">
            <h<?php echo absint($hSize); ?> class="entry-title"><a href="<?php echo esc_url(get_permalink($position->ID)); ?>"><?php echo esc_html($position->getTitle()); ?></a></h<?php echo absint($hSize); ?>>
        </header>
        <?php
    }
}
add_action( 'personio_integration_get_title', 'personio_integration_get_title', 10, 2 );

/**
 * Get position excerpts for list.
 *
 * @param $position
 * @param $attributes
 * @return void
 */
function personio_integration_get_excerpt( $position, $attributes ) {
    $excerpt = '';
    $separator = get_option('personioIntegrationTemplateExcerptSeparator', ', ')." ";
    if( empty($attributes['excerpt']) ) {
        $attributes['excerpt'] = $attributes;
    }
    if( !empty($attributes['excerpt']) ) {
        foreach ($attributes['excerpt'] as $excerptTemplate) {
            $stringToAdd = helper::get_taxonomy_name_of_position( $excerptTemplate, $position );
            if (strlen($excerpt) > 0 && strlen($stringToAdd) > 0) $excerpt .= $separator;
            $excerpt .= $stringToAdd;
        }
    }
    if( !empty($excerpt) ) {
        ?>
        <div class="entry-content">
            <p><?php echo esc_html($excerpt); ?></p>
        </div>
        <?php
    }
}
add_action( 'personio_integration_get_excerpt', 'personio_integration_get_excerpt', 10, 2 );

/**
 * Get position content for list.
 *
 * @param $position
 * @param $attributes
 * @return void
 * @noinspection PhpUnused
 * @noinspection PhpUnusedParameterInspection
 */
function personio_integration_get_content( $position, $attributes ) {
    if( !empty($position->getContentAsArray()) ) {
        ?>
        <div class="entry-content">
            <?php
            echo wp_kses_post($position->getContent());
            ?>
        </div>
        <?php
    }
}
add_action( 'personio_integration_get_content', 'personio_integration_get_content', 10, 2 );

/**
 * Get position application-link-button for list.
 *
 * @param $position
 * @param $attributes
 * @return void
 * @noinspection PhpUnused
 * @noinspection PhpUnusedParameterInspection
 */
function personio_integration_get_formular( $position, $attributes ) {
    include helper::getTemplate('parts/properties-application-button.php');
}
add_action( 'personio_integration_get_formular', 'personio_integration_get_formular', 10, 2 );

/**
 * Update each post-object with the language-specific texts of a position.
 *
 * @param $post
 * @param $query
 * @return void
 * @noinspection PhpUnused
 * @noinspection PhpUnusedParameterInspection
 */
function personio_integration_update_post_object( $post, $query ) {
    if( $post->post_type == WP_PERSONIO_INTEGRATION_CPT ) {
        // get the position as object
        $position = new personioIntegration\Position(get_the_ID());

        // set language
        $position->lang = get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY);

        // override the post_title
        $post->post_title = $position->getTitle();

        // override the post_content
        $post->post_content = $position->getContent();

        // override the post_excerpt
        $post->post_excerpt = $position->getExcerpt();
    }
}
add_action( 'the_post', 'personio_integration_update_post_object', 10, 2 );

/**
 * Get single template.
 *
 * @param $single_template
 * @return mixed|string
 * @noinspection PhpUnused
 */
function personio_integration_get_single_template( $single_template ) {
    if( get_post_type(get_the_ID()) == WP_PERSONIO_INTEGRATION_CPT ) {
        $path = helper::getTemplate('single-personioposition.php');
        if (file_exists($path)) {
            $single_template = $path;
        }
    }
    return $single_template;
}
add_filter( 'single_template', 'personio_integration_get_single_template' );

/**
 * Get archive template.
 *
 * @param $archive_template
 * @return mixed|string
 * @noinspection PhpUnused
 */
function personio_integration_get_archive_template( $archive_template ) {
    if ( is_post_type_archive(WP_PERSONIO_INTEGRATION_CPT) ) {
        $path = helper::getTemplate('archive-personioposition.php');
        if( file_exists($path) ) {
            $archive_template = $path;
        }
    }
    return $archive_template;
}
add_filter( 'archive_template', 'personio_integration_get_archive_template' ) ;

/**
 * Show a filter in frontend restricted to positions which are visible in list.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_get_filter( $filter, $attributes ) {
    $taxonomyToUse = '';
    $term_ids = [];

    // get the terms of this taxonomy
    foreach( WP_PERSONIO_INTEGRATION_TAXONOMIES as $taxonomy_name => $taxonomy ) {
        if ($taxonomy['slug'] == $filter && $taxonomy['useInFilter'] == 1) {
            $taxonomyToUse = $taxonomy_name;
            $terms = get_terms(['taxonomy' => $taxonomy_name]);
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if( $term->count > 0 ) {
                        $term_ids[] = $term->term_id;
                    }
                }
            }
        }
    }

    // show term as filter only if its name is known
    if( strlen($taxonomyToUse) > 0 ) {
        // get the terms of this taxonomy
        $terms = get_terms(["taxonomy" => $taxonomyToUse, "include" => $term_ids]);

        if( !empty($terms) ) {

            // get the value
            $value = 0;
            // -> if filter is set by editor
            if (!empty($attributes["office"])) {
                $value = $attributes["office"];
            }
            // -> if filter is set by user in frontend
            if (!empty($_GET['personiofilter']) && !empty($_GET['personiofilter'][$filter])) {
                $value = sanitize_text_field($_GET['personiofilter'][$filter]);
            }

            // set name
            $taxonomy = get_taxonomy($taxonomyToUse);
            $filtername = $taxonomy->labels->singular_name;

            // output of filter
            include helper::getTemplate('parts/term-filter-' . $attributes['filtertype'] . '.php');
        }
    }

}
add_action( 'personio_integration_get_filter', 'personio_integration_get_filter', 10, 2 );
